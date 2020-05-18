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
// +-------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\musicplayer;

use think\App;
use think\Response;
use think\helper\Arr;
use think\exception\FileException;

class PublicStatic
{
    protected $app;
    protected $basePath;
    protected $filename = '';
	
    /**
     * 构造函数
     * @param think\App $app
     * @param string $basePath
     */
    public function __construct(App $app, $basePath = NULL)
    {
        $this->app = $app;
		
        if ($basePath === NULL) {
            $basePath = __DIR__ . DIRECTORY_SEPARATOR . 'music';
        }
        $this->basePath = $basePath;
		
        $this->init();
    }
	
    /**
     * 初始化
     */
    protected function init()
    {
        $url = $this->app->request->url();
		
        $depr = $this->app->config->get('route.pathinfo_depr', '/');
        $filename = trim(strrchr($url, $depr), $depr);
		
        $basePath = str_replace($depr, DIRECTORY_SEPARATOR, ltrim(rtrim($url, $filename), $depr . 'static'));
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . $basePath;
        $this->setBasePath($basePath);
		
        $filename = trim($this->toreplace($filename, '?'));
        $this->setFilename($filename);
    }
	
    /**
     * 获取文件类型信息
     * @access protected
     * @param string $Pathname
     * @return string
     */
    protected function getMime($Pathname) : string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $Pathname);
    }
	
    /**
     * 去掉某个字符串及之后的内容
     * @param string $str 需要操作的字符串
     * @param string $find 指定的字符串
     */
    protected function toreplace($str, $find)
    {
        if (strpos($str, $find) === false) {
            return $str;
        }
        $res = explode($find, $str);
        return $res[0];
    }
	
    /**
     * 设置当前请求的资源文件
     *
     * @param string $filename
     */
    protected function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }
	
    /**
     * 返回当前请求的资源文件
     *
     * @param string $filename
     */
    protected function getFilename()
    {
        return $this->filename;
    }
	
    /**
     * 设置资源的相对路径
     *
     * @param string $path
     */
    protected function setBasePath($path)
    {
        $this->basePath = $path;
        return $this;
    }
	
    /**
     * 返回资源的相对路径
     *
     * @return string
     */
    protected function getBasePath()
    {
        return $this->basePath;
    }
	
    public function index($path = NULL)
    {
		
        $basePath = $this->getBasePath();
		
        if (substr($basePath, -1) != DIRECTORY_SEPARATOR) {
            $basePath .= DIRECTORY_SEPARATOR;
        }
		
        $filename = $basePath . $this->getFilename();
		
        if (!is_file($filename)) {
            throw new FileException(sprintf('The file "%s" does not exist', $this->getFilename()));
        }
		
        $ext = strtolower(Arr::last(explode('.', $filename)));
        $extlayer = trim(strrchr($filename, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
		
        $types = ['css' => 'text/css', 'js' => 'application/javascript'];
		
        if (array_key_exists($ext, $types)) {
            $mime = $types[$ext];
        } else {
            //$mime = mime_content_type($filename);
            $mime = $this->getMime($filename);
        }
		
        $contents = file_get_contents($filename);
		
        if ($extlayer == 'layer.css') {
            $contents .= <<<EOF
.layui-layer .layui-layer-content,.layui-layer .layui-layer-btn{text-align:center !important}
.layui-layer .layui-layer-btn a{height:36px;padding:0;line-height:36px;width:140px;background-color:#00C1DE;outline:none;border:none;color:#fff}
.layui-layer .layui-layer-btn .layui-layer-btn0{background-color:#009900;color:#fff}
EOF;
        }
		
		return Response::create($contents)->header([])->cacheControl('max-age=31536000, public')->contentType($mime);
    }
}