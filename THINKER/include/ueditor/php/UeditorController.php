<?php
// +-------------------------------------------------------------------------
// | THINKER [ Internet Ecological traffic aggregation and sharing platform ]
// +-------------------------------------------------------------------------
// | Copyright (c) 2019~2099 https://thinker.com All rights reserved.
// +-------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-------------------------------------------------------------------------
// | Author: yangrong <3223577520@qq.com>
// +-------------------------------------------------------------------------
declare (strict_types=1);
namespace inc\ueditor\php;

use think\App;
use Thinker\Controllers\Controller;
use think\exception\FileException;
class UeditorController extends Controller
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    protected $ueditorBasePath;
    protected $ueditorConfig;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app, $basePath = NULL)
    {
        //header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        //header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        $this->app = $app;
        if ($basePath === NULL) {
            $basePath = __DIR__ . DIRECTORY_SEPARATOR;
        }
        $this->ueditorBasePath = $basePath;
        parent::__construct($this->app);
        // 初始化操作
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
        $this->getUeditorConfig();
    }
    protected function getUeditorConfig()
    {
        $filename = $this->ueditorBasePath . "config.json";
        if (!is_file($filename)) {
            throw new FileException(sprintf('The file "%s" does not exist', $this->getFilename()));
        }
        $this->ueditorConfig = json_decode(preg_replace("/\\/\\*[\\s\\S]+?\\*\\//", "", file_get_contents($filename)), true);
        return $this;
    }
    public function index()
    {
        $action = !empty($this->data['action']) ? $this->data['action'] : '';
        switch ($action) {
            case 'config':
                $result = $this->ueditorConfig;
                break;
            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = $this->ueditorUpload($action, $this->ueditorConfig);
                break;
            /* 列出图片 */
            case 'listimage':
                $result = $this->ueditorListImage($this->ueditorConfig);
                break;
            /* 列出文件 */
            case 'listfile':
                $result = $this->ueditorListFile($this->ueditorConfig);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->ueditorCatchImage($this->ueditorConfig);
                break;
            default:
                $result = array('state' => '请求地址出错');
                break;
        }
        /* 输出结果 */
        if (isset($this->data["callback"])) {
            if (preg_match("/^[\\w_]+\$/", $this->data["callback"])) {
                return htmlspecialchars($this->data["callback"]) . '(' . $result . ')';
            } else {
                return $this->json(array('state' => 'callback参数不合法'));
            }
        } else {
            return $this->json($result);;
        }
    }
    /**
     * 上传文件
     * @access protected
     */
    protected function ueditorUpload($action, $config)
    {
        /* 上传配置 */
        $base64 = "upload";
        switch (htmlspecialchars($action)) {
            case 'uploadimage':
                $Newconfig = array("pathFormat" => $config['imagePathFormat'], "maxSize" => $config['imageMaxSize'], "allowFiles" => $config['imageAllowFiles']);
                $fieldName = $config['imageFieldName'];
                break;
            case 'uploadscrawl':
                $Newconfig = array("pathFormat" => $config['scrawlPathFormat'], "maxSize" => $config['scrawlMaxSize'], "allowFiles" => $config['scrawlAllowFiles'], "oriName" => "scrawl.png");
                $fieldName = $config['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $Newconfig = array("pathFormat" => $config['videoPathFormat'], "maxSize" => $config['videoMaxSize'], "allowFiles" => $config['videoAllowFiles']);
                $fieldName = $config['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $Newconfig = array("pathFormat" => $config['filePathFormat'], "maxSize" => $config['fileMaxSize'], "allowFiles" => $config['fileAllowFiles']);
                $fieldName = $config['fileFieldName'];
                break;
        }
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $Newconfig, $base64);
        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */
        /* 返回数据 */
        return $res = $up->getFileInfo();
    }
    /**
     * 列出图片
     * @access protected
     */
    protected function ueditorListImage($config)
    {
        $allowFiles = $config['imageManagerAllowFiles'];
        $listSize = $config['imageManagerListSize'];
        $path = $config['imageManagerListPath'];
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        /* 获取参数 */
        $size = isset($this->data['size']) ? htmlspecialchars($this->data['size']) : $listSize;
        $start = isset($this->data['start']) ? htmlspecialchars($this->data['start']) : 0;
        $end = $start + $size;
        /* 获取文件列表 */
        $path = $this->input('server.DOCUMENT_ROOT') . (substr($path, 0, 1) == "/" ? "" : "/") . $path;
        $files = getfiles($path, $allowFiles);
        if (!count($files)) {
			$result = array("state" => "no match file", "list" => array(), "start" => $start, "total" => count($files));
            return $result;
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}
        /* 返回数据 */
        $result = array("state" => "SUCCESS", "list" => $list, "start" => $start, "total" => count($files));
        return $result;
    }
    /**
     * 列出文件
     * @access protected
     */
    protected function ueditorListFile($config)
    {
        $allowFiles = $config['fileManagerAllowFiles'];
        $listSize = $config['fileManagerListSize'];
        $path = $config['fileManagerListPath'];
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        /* 获取参数 */
        $size = isset($this->data['size']) ? htmlspecialchars($this->data['size']) : $listSize;
        $start = isset($this->data['start']) ? htmlspecialchars($this->data['start']) : 0;
        $end = $start + $size;
        /* 获取文件列表 */
        $path = $this->input('server.DOCUMENT_ROOT') . (substr($path, 0, 1) == "/" ? "" : "/") . $path;
        $files = getfiles($path, $allowFiles);
        if (!count($files)) {
            $result = array("state" => "no match file", "list" => array(), "start" => $start, "total" => count($files));
			return $result;
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}
        /* 返回数据 */
        $result = array("state" => "SUCCESS", "list" => $list, "start" => $start, "total" => count($files));
        return $result;
    }
    /**
     * 抓取远程文件
     * @access protected
     */
    protected function ueditorCatchImage($config)
    {
        set_time_limit(0);
        /* 上传配置 */
        $Newconfig = array("pathFormat" => $config['catcherPathFormat'], "maxSize" => $config['catcherMaxSize'], "allowFiles" => $config['catcherAllowFiles'], "oriName" => "remote.png");
        $fieldName = $config['catcherFieldName'];
        /* 抓取远程图片 */
        $list = array();
        $source = isset($this->data[$fieldName]) ? $this->data[$fieldName] : [];
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $Newconfig, "remote");
            $info = $item->getFileInfo();
            array_push($list, array("state" => $info["state"], "url" => $info["url"], "size" => $info["size"], "title" => htmlspecialchars($info["title"]), "original" => htmlspecialchars($info["original"]), "source" => htmlspecialchars($imgUrl)));
        }
        /* 返回抓取数据 */
        $result = array('state' => count($list) ? 'SUCCESS' : 'ERROR', 'list' => $list);
		return $result;
    }
}
/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) {
        return null;
    }
    if (substr($path, strlen($path) - 1) != '/') {
        $path .= '/';
    }
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\\.(" . $allowFiles . ")\$/i", $file)) {
                    $files[] = array('url' => substr($path2, strlen(input('server.DOCUMENT_ROOT'))), 'mtime' => filemtime($path2));
                }
            }
        }
    }
    return $files;
}