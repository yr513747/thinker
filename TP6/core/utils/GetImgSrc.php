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
// [ 提取HTML文章中的图片地址 ]
// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
declare (strict_types=1);

namespace core\utils;

use think\App;
use think\Image;
use think\facade\Session;

class GetImgSrc
{
    protected $app;
    protected static $init = false;
	protected static $request = null;
    protected static $web_basehost;
    protected static $basehost;
    protected static $dirname;
    protected static $root_dir = '';
    protected static $users_id;
	protected static $web_root;
	
	/**
     * 构造函数
     * @param think\App $app
     * @param string $basePath
     */
    public function __construct(App $app)
    {
        $this->app = $app;
		
        is_null(self::$request) && self::$request = $app->request;
		
        static::init();
    }
	
    /**
     * 初始化操作
     * @access public
     * @return array
     */
    public static function init($domain = false)
    {
        if (empty(self::$init)) {
			self::$basehost = self::$request->domain();
			//self::$root_dir = self::$request->rootUrl();
            self::$root_dir = ROOT_DIR;
			//根目录
			if ('Y' == config('cfg_multi_site')) {
                $domain = true;
            }
            if ($domain) {
                self::$web_root = self::$basehost . self::$root_dir;
            } else {
                self::$web_root = self::$root_dir;
            }
            //远程图片本地化相关配置
            $web_basehost = tpCache('web.web_basehost');
            $parse_arr = parse_url($web_basehost);
            self::$web_basehost = $parse_arr['scheme'] . '://' . $parse_arr['host'];
            self::$dirname = './' . UPLOAD_PATH . 'ueditor/' . date('Ymd/');
			$users_id = 0;
            if (Session::has('users_id')) {
                $users_id = Session::get('users_id');
            } else if (Session::has('admin_id')) {
                $users_id = Session::get('admin_id');
            }
			self::$users_id = $users_id;
            self::$init = true;
        }
    }
	 /**
     * 缩略图 从原始图来处理出来
     * @param type $original_img  图片路径
     * @param type $width     生成缩略图的宽度
     * @param type $height    生成缩略图的高度
     * @param type $thumb_mode    生成方式
     */
    public static function thumb_img($original_img = '', $width = '', $height = '', $thumb_mode = '')
    {
        // 缩略图配置
        static $thumbConfig = null;
        null === $thumbConfig && ($thumbConfig = tpCache('thumb'));
        $thumbextra = config('global.thumb');
        if (!empty($width) || !empty($height) || !empty($thumb_mode)) {
            // 单独在模板里调用，不受缩略图全局开关影响
        } else {
            // 非单独模板调用，比如内置的arclist\list标签里
            if (empty($thumbConfig['thumb_open'])) {
                return $original_img;
            }
        }
        // 缩略图优先级别高于七牛云，自动把七牛云的图片路径转为本地图片路径，并且进行缩略图
        $original_img = self::is_local_file($original_img);
        // 未开启缩略图，或远程图片
        if (self::is_http_url($original_img) || stristr($original_img, self::$web_root . '/public/static/common/images/not_adv.jpg')) {
            return $original_img;
        } else {
            if (empty($original_img)) {
                return self::$web_root . '/public/static/common/images/not_adv.jpg';
            }
        }
        // 图片文件名
        $filename = '';
        $imgArr = explode('/', $original_img);
        $imgArr = end($imgArr);
        $filename = preg_replace("/\\.([^\\.]+)\$/i", "", $imgArr);
        $file_ext = preg_replace("/^(.*)\\.([^\\.]+)\$/i", "\$2", $imgArr);
        // 如果图片参数是缩略图，则直接获取到原图，并进行缩略处理
        if (preg_match('/\\/uploads\\/thumb\\/\\d{1,}_\\d{1,}\\//i', $original_img)) {
            $pattern = UPLOAD_PATH . 'allimg/*/' . $filename;
            if (in_array(strtolower($file_ext), ['jpg', 'jpeg'])) {
                $pattern .= '.jp*g';
            } else {
                $pattern .= '.' . $file_ext;
            }
            $original_img_tmp = glob($pattern);
            if (!empty($original_img_tmp)) {
                $original_img = '/' . current($original_img_tmp);
            }
        } else {
            if ('bmp' == $file_ext && version_compare(PHP_VERSION, '7.2.0', '<')) {
                return self::handle_subdir($original_img);
            }
        }
        // --end
        $original_img1 = preg_replace('#^' . self::$root_dir . '#i', '', self::handle_subdir($original_img));
        $original_img1 = '.' . $original_img1;
        // 相对路径
        //获取图像信息
        $info = @getimagesize($original_img1);
        //检测图像合法性
        if (false === $info || IMAGETYPE_GIF === $info[2] && empty($info['bits'])) {
            return self::handle_subdir($original_img);
        } else {
            if (!empty($info['mime']) && stristr($info['mime'], 'bmp') && version_compare(PHP_VERSION, '7.2.0', '<')) {
                return self::handle_subdir($original_img);
            }
        }
        // 缩略图宽高度
        empty($width) && ($width = !empty($thumbConfig['thumb_width']) ? $thumbConfig['thumb_width'] : $thumbextra['width']);
        empty($height) && ($height = !empty($thumbConfig['thumb_height']) ? $thumbConfig['thumb_height'] : $thumbextra['height']);
        $width = intval($width);
        $height = intval($height);
        //判断缩略图是否存在
        $path = UPLOAD_PATH . "thumb/{$width}_{$height}/";
        $img_thumb_name = "{$filename}";
        // 已经生成过这个比例的图片就直接返回了
        if (is_file($path . $img_thumb_name . '.jpg')) {
            return self::$web_root . '/' . $path . $img_thumb_name . '.jpg';
        }
        if (is_file($path . $img_thumb_name . '.jpeg')) {
            return self::$web_root . '/' . $path . $img_thumb_name . '.jpeg';
        }
        if (is_file($path . $img_thumb_name . '.gif')) {
            return self::$web_root . '/' . $path . $img_thumb_name . '.gif';
        }
        if (is_file($path . $img_thumb_name . '.png')) {
            return self::$web_root . '/' . $path . $img_thumb_name . '.png';
        }
        if (is_file($path . $img_thumb_name . '.bmp')) {
            return self::$web_root . '/' . $path . $img_thumb_name . '.bmp';
        }
        if (!is_file($original_img1)) {
            return self::$web_root . '/public/static/common/images/not_adv.jpg';
        }
        try {
            vendor('topthink.think-image.src.Image');
            vendor('topthink.think-image.src.image.Exception');
            if (stristr($original_img1, '.gif')) {
                vendor('topthink.think-image.src.image.gif.Encoder');
                vendor('topthink.think-image.src.image.gif.Decoder');
                vendor('topthink.think-image.src.image.gif.Gif');
            }
            $image = Image::open($original_img1);
            $img_thumb_name = $img_thumb_name . '.' . $image->type();
            // 生成缩略图
            !is_dir($path) && mkdir($path, 0777, true);
            // 填充颜色
            $thumb_color = !empty($thumbConfig['thumb_color']) ? $thumbConfig['thumb_color'] : $thumbextra['color'];
            // 生成方式参考 vendor/topthink/think-image/src/Image.php
            if (!empty($thumb_mode)) {
                $thumb_mode = intval($thumb_mode);
            } else {
                $thumb_mode = !empty($thumbConfig['thumb_mode']) ? $thumbConfig['thumb_mode'] : $thumbextra['mode'];
            }
            1 == $thumb_mode && ($thumb_mode = 6);
            // 按照固定比例拉伸
            2 == $thumb_mode && ($thumb_mode = 2);
            // 填充空白
            if (3 == $thumb_mode) {
                $img_width = $image->width();
                $img_height = $image->height();
                if ($width < $img_width && $height < $img_height) {
                    // 先进行缩略图等比例缩放类型，取出宽高中最小的属性值
                    $min_width = $img_width < $img_height ? $img_width : 0;
                    $min_height = $img_width > $img_height ? $img_height : 0;
                    if ($min_width > $width || $min_height > $height) {
                        if (0 < intval($min_width)) {
                            $scale = $min_width / min($width, $height);
                        } else {
                            if (0 < intval($min_height)) {
                                $scale = $min_height / $height;
                            } else {
                                $scale = $min_width / $width;
                            }
                        }
                        $s_width = $img_width / $scale;
                        $s_height = $img_height / $scale;
                        $image->thumb($s_width, $s_height, 1, $thumb_color)->save($path . $img_thumb_name, NULL, 100);
                        //按照原图的比例生成一个最大为$width*$height的缩略图并保存
                    }
                }
                $thumb_mode = 3;
                // 截减
            }
            // 参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
            $image->thumb($width, $height, $thumb_mode, $thumb_color)->save($path . $img_thumb_name, NULL, 100);
            //按照原图的比例生成一个最大为$width*$height的缩略图并保存
            //图片水印处理
            $water = tpCache('water');
            if ($water['is_mark'] == 1 && $water['is_thumb_mark'] == 1 && $image->width() > $water['mark_width'] && $image->height() > $water['mark_height']) {
                $imgresource = '.' . self::$root_dir . '/' . $path . $img_thumb_name;
                if ($water['mark_type'] == 'text') {
                    //$image->text($water['mark_txt'],ROOT_PATH.'public/static/common/font/hgzb.ttf',20,'#000000',9)->save($imgresource);
                    $ttf = INC_PATH . 'data/font/hgzb.ttf';
                    if (file_exists($ttf)) {
                        $size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
                        $color = $water['mark_txt_color'] ?: '#000000';
                        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                            $color = '#000000';
                        }
                        $transparency = intval((100 - $water['mark_degree']) * (127 / 100));
                        $color .= dechex($transparency);
                        $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['mark_sel'])->save($imgresource);
                        $return_data['mark_txt'] = $water['mark_txt'];
                    }
                } else {
                    /*支持子目录*/
                    $water['mark_img'] = preg_replace('#^(/[/\\w]+)?(/public/upload/|/uploads/)#i', '$2', $water['mark_img']);
                    // 支持子目录
                    /*--end*/
                    //$image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
                    $waterPath = "." . $water['mark_img'];
                    if (self::PreventShell($waterPath) && file_exists($waterPath)) {
                        $quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
                        $waterTempPath = dirname($waterPath) . '/temp_' . basename($waterPath);
                        $image->open($waterPath)->save($waterTempPath, null, $quality);
                        $image->open($imgresource)->water($waterTempPath, $water['mark_sel'], $water['mark_degree'])->save($imgresource);
                        @unlink($waterTempPath);
                    }
                }
            }
            $img_url = self::$web_root . '/' . $path . $img_thumb_name;
            return self::handle_subdir($img_url);
        } catch (\Exception $e) {
            return self::handle_subdir($original_img);
        }
    }	
	/**
     * 处理子目录与根目录的图片平缓切换
     * @param string $str 图片路径或html代码
     */
    public static function handle_subdir($str = '', $type = 'img')
    {
		switch ($type) {
            case 'img':
                if (!self::is_http_url($str) && !empty($str)) {
                    $str = preg_replace('#^(/[/\\w]+)?(/public/upload/|/uploads/|/public/static/)#i', self::$root_dir . '$2', $str);
                    //支持绝对路径				
                    $str = cfg_multi_site($str);
                } else {
                    if (self::is_http_url($str) && !empty($str)) {
                        // 图片路径处理
                        $str = preg_replace('#^(/[/\\w]+)?(/public/upload/|/uploads/|/public/static/)#i', self::$root_dir . '$2', $str);
                        $StrData = parse_url($str);
                        $strlen = strlen(self::$root_dir);
                        if (empty($StrData['scheme'])) {
                            if ('/uploads/' == substr($StrData['path'], $strlen, 9) || '/public/upload/' == substr($StrData['path'], $strlen, 15)) {
                                // 七牛云配置处理
                                static $Qiniuyun = null;
                                if (null == $Qiniuyun) {
                                    // 需要填写你的 Access Key 和 Secret Key
                                    $data = M('weapp')->where('code', 'Qiniuyun')->field('data,status')->find();
                                    $Qiniuyun = json_decode($data['data'], true);
                                    $Qiniuyun['status'] = $data['status'];
                                }
                                // 是否开启图片加速
                                if ('1' == $Qiniuyun['status']) {
                                    // 开启
                                    if ($Qiniuyun['domain'] == $StrData['host']) {
                                        $tcp = !empty($Qiniuyun['tcp']) ? $Qiniuyun['tcp'] : '';
                                        switch ($tcp) {
                                            case '2':
                                                $tcp = 'https://';
                                                break;
                                            case '3':
                                                $tcp = '//';
                                                break;
                                            case '1':
                                            default:
                                                $tcp = 'http://';
                                                break;
                                        }
                                        $str = $tcp . $Qiniuyun['domain'] . $StrData['path'];
                                    } else {
                                        // 若切换了存储空间或访问域名，与数据库中存储的图片路径域名不一致时，访问本地路径，保证图片正常
                                        $str = $StrData['path'];
                                    }
                                } else {
                                    // 关闭
                                    $str = $StrData['path'];
                                }
                            }
                        }
                    }
                }
                break;
            case 'html':
                $str = preg_replace('#(.*)(\\#39;|&quot;|"|\')(/[/\\w]+)?(/public/upload/|/public/plugins/|/uploads/)(.*)#iU', '$1$2' . self::$root_dir . '$4$5', $str);
                break;
            case 'soft':
                if (!self::is_http_url($str) && !empty($str)) {
                    $str = preg_replace('#^(/[/\\w]+)?(/public/upload/soft/|/uploads/soft/)#i', self::$root_dir . '$2', $str);
                    //支持绝对路径
                    $str = cfg_multi_site($str);
                }
                break;
            default:
                # code...
                break;
        }
        return $str;
    }
	/**
     * 默认头像
     */
    public static function get_head_pic($pic_url = '', $is_admin = false)
    {
		if ($is_admin) {
            $default_pic = self::$web_root . '/public/static/admin/images/admin_default_pic.png';
        } else {
            $default_pic = self::$web_root . '/public/static/common/images/member_default_pic.png';
        }
        return empty($pic_url) ? $default_pic : $pic_url;
    }
	/**
     * 判断远程链接是否属于本地文件，并返回本地文件路径
     *
     * @param string $file 文件地址
     * @param boolean $returnbool 返回类型，false 返回图片路径，true 返回布尔值
	 * @param string|boolean $domain 如果是本地文件是否返回完整路径的域名
     */
    public static function is_local_file($file = '', $domain = false, $returnbool = false)
    {
		empty(self::$init) && self::init($domain);
        $filePath = parse_url($file, PHP_URL_PATH);
        if (!empty($filePath) && file_exists('.' . $filePath)) {
            $filePath = preg_replace('#^' . self::$root_dir . '/#i', '/', $filePath);
            $file = self::$web_root . $filePath;
            if (true == $returnbool) {
                return true;
            }
            return $file;
        }
        if (true == $returnbool) {
            return false;
        }
        return $file;
    }
	
	/**
     * 图片不存在，显示默认无图封面
     * @param string $pic_url 图片路径
     * @param string|boolean $domain 完整路径的域名
     */
    public static function get_default_pic($pic_url = '', $domain = false)
    {
        $default_pic = '/public/static/common/images/not_adv.jpg';
        if (!self::is_http_url($pic_url)) {
            $boolean = self::is_local_file($pic_url, false, true);
            if ($boolean) {
                $pic_url = self::is_local_file($pic_url, $domain, false);
            } else {
                $pic_url = self::is_local_file($default_pic, $domain, false);
            }
        }
        return $pic_url;
    }
	/**
     * 判断url是否完整的链接
     *
     * @param  string $url 网址
     * @return boolean
     */
    public static function is_http_url($url)
    {
        preg_match("/^((\w)*:)?(\/\/).*$/", $url, $match);
        if (empty($match)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * 提取HTML文章中的图片地址
     * @param string $data HTML或者文章
     * @param int $num 第 $num 个图片的src，默认为第一张
     * @param string $order 顺取倒取； 默认为 asc ，从正方向计数。 desc 从反方向计数
     * @param string|array $blacklist 图片地址黑名单，排除图片地址中包含该数据的地址；例如 传入 baidu.com  会排除 src="http://www.baidu.com/img/a.png"
     * @param string $model 默认为字符串模式;可取值 string  preg；string模式处理效率高，PHP版本越高速度越快，可比正则快几倍
     * @return false | null | src  当data为空时返回 false ， src不存在时返回 null ，反之返回src
     */
    public static function src($data, $num = 1, $order = 'asc', $blacklist = false, $model = 'string')
    {
        if (isset($data)) {
            if ($model === 'preg') {
                $imgSrc = self::pregModel($data, $num - 1, $order);
            } else {
                $imgSrc = self::strModel($data, $num, $order);
            }
            if ($blacklist === false) {
                return $imgSrc;
            } else {
                if (is_array($blacklist)) {
                    foreach ($blacklist as $value) {
                        if (strpos($imgSrc, $value) !== false) {
                            return self::src($data, $num + 1, $order, $blacklist, $model);
                        }
                    }
                    return $imgSrc;
                } else {
                    if (strpos($imgSrc, $blacklist) === false) {
                        return $imgSrc;
                    } else {
                        return self::src($data, $num + 1, $order, $blacklist, $model);
                    }
                }
            }
        } else {
            return "";
        }
    }
    /**
     * 提取HTML文章中的图片地址
     * @param string $data HTML或者文章
     * @param int $startNum 默认为1，从第一张图片开始抽取
     * @param int $length 从 $startNum 开始抽取，共抽取 $length 张；默认为0，为0则抽取到最后
     * @param string $order 顺取倒取； 默认为 asc ，从正方向计数。 desc 从反方向计数
     * @param string|array $blacklist 图片地址黑名单，排除图片地址中包含该数据的地址；例如 传入 img.baidu.com  会排除 src="img.baidu.com/a.png"
     * @param string $model 抽取集合时，默认为正则模式；可选模式：preg  string，当 $length > 3 或者 $length = 0时，强制使用正则模式，因为取的数量大时，正则速度更快。
     * @return 图片地址的集合数组，若无则返回空数组[]
     */
    public static function srcList($data, $startNum = 1, $length = 0, $order = 'asc', $blacklist = false, $model = 'preg')
    {
        if ($model === 'preg' || $length > 3 || $length === 0) {
            $imgSrcArr = self::pregModel($data, [$startNum - 1, $length, $blacklist], $order);
        } else {
            $imgSrcArr = [];
            for ($i = $startNum; $i < $startNum + $length; $i++) {
                $imgSrc = self::strModel($data, $i, $order);
                if (is_array($blacklist)) {
                    $blackBool = true;
                    foreach ($blacklist as $k => $v) {
                        if (strpos($imgSrc, $blacklist) !== false) {
                            $blackBool = false;
                        }
                    }
                    if ($blackBool) {
                        $imgSrcArr[] = $imgSrc;
                    } else {
                        $length++;
                    }
                } else {
                    if (strpos($imgSrc, $blacklist) === false) {
                        $imgSrcArr[] = $imgSrc;
                    } else {
                        $length++;
                    }
                }
            }
        }
        return $imgSrcArr;
    }
    /**
     * @param $str
     * @param $num
     * @param $order
     * @return bool|string|null
     */
    public static function strModel($str, $num, $order)
    {
        $topStr = null;
        if ($order != 'asc') {
            $funcStr = 'strrpos';
        } else {
            $funcStr = 'strpos';
        }
        for ($i = 1; $i <= $num; $i++) {
            $firstNum = $funcStr($str, '<img');
            if ($firstNum !== false) {
                if ($order != 'asc') {
                    $topStr = $str;
                    $str = substr($str, 0, $firstNum);
                } else {
                    $str = substr($str, $firstNum + 4);
                }
            } else {
                return "";
            }
        }
        $str = $order == 'asc' ? $str : $topStr;
        $firstNum1 = $funcStr($str, 'src=');
        $type = substr($str, $firstNum1 + 4, 1);
        $str2 = substr($str, $firstNum1 + 5);
        if ($type == '\'') {
            $position = strpos($str2, "'");
        } else {
            $position = strpos($str2, '"');
        }
        $imgPath = substr($str2, 0, $position);
        return $imgPath;
    }
    /**
     * @param $str
     * @param $num
     * @param $order
     * @return string|array|null
     */
    public static function pregModel($str, $num, $order)
    {
        preg_match_all("/<img.*>/isU", $str, $ereg);
        $img = $ereg[0];
        if ($order != 'asc') {
            $img = array_reverse($img);
        }
        if (is_array($num)) {
            $startNum = $num[0];
            $length = $num[1];
            $blacklist = $num[2];
            $imgSrcArr = [];
            foreach ($img as $key => $value) {
                $imgSrc = $value;
                $pregModel = "/src=('|\")(.*)('|\")/isU";
                preg_match_all($pregModel, $imgSrc, $img1);
                if (is_array($blacklist)) {
                    $blacklistBool = true;
                    foreach ($blacklist as $v) {
                        if (strpos($img1[2][0], $v) !== false) {
                            $blacklistBool = false;
                        }
                    }
                    if ($blacklistBool) {
                        $imgSrcArr[] = $img1[2][0];
                    }
                } else {
                    if (strpos($img1[2][0], $blacklist) === false) {
                        $imgSrcArr[] = $img1[2][0];
                    }
                }
            }
            if ($length > 0) {
                return array_slice($imgSrcArr, $startNum, $length);
            } else {
                return array_slice($imgSrcArr, $startNum);
            }
        } else {
            if (!empty($img[$num])) {
                $imgStr = $img[$num];
                $pregModel = "/src=('|\")(.*)('|\")/isU";
                preg_match_all($pregModel, $imgStr, $img1);
                return $img1[2][0];
            } else {
                return "";
            }
        }
    }
    /**
     *  远程图片本地化
     *
     * @access    public
     * @param     string  $body  内容
     * @return    string
     */
    public static function remote_to_local($body = '')
    {
        $img_array = self::srcList($body);
        foreach ($img_array as $key => $val) {
            if (preg_match("/^http(s?):\\/\\/mmbiz.qpic.cn\\/(.*)\\?wx_fmt=(\\w+)&/", $val) == 1) {
                unset($img_array[$key]);
            }
        }
        //创建目录失败
        if (!file_exists(self::$dirname) && !mkdir(self::$dirname, 0777, true)) {
            return $body;
        } else {
            if (!is_writeable(self::$dirname)) {
                return $body;
            }
        }
        foreach ($img_array as $key => $value) {
            $imgUrl = trim($value);
            // 本站图片
            if (preg_match("#" . self::$basehost . "#i", $imgUrl)) {
                continue;
            }
            // 根网址图片
            if (self::$web_basehost != self::$basehost && preg_match("#" . self::$web_basehost . "#i", $imgUrl)) {
                continue;
            }
            // 不是合法链接
            if (!preg_match("#^http(s?):\\/\\/#i", $imgUrl)) {
                continue;
            }
            $heads = @get_headers($imgUrl, 1);
            // 获取请求头并检测死链
            if (empty($heads)) {
                continue;
            } else {
                if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                    continue;
                }
            }
            // 图片扩展名
            $fileType = substr($heads['Content-Type'], -4, 4);
            if (!preg_match("#\\.(jpg|jpeg|gif|png|ico|bmp)#i", $fileType)) {
                if ($fileType == 'image/gif') {
                    $fileType = ".gif";
                } else {
                    if ($fileType == 'image/png') {
                        $fileType = ".png";
                    } else {
                        if ($fileType == 'image/x-icon') {
                            $fileType = ".ico";
                        } else {
                            if ($fileType == 'image/bmp') {
                                $fileType = ".bmp";
                            } else {
                                $fileType = '.jpg';
                            }
                        }
                    }
                }
            }
            $fileType = strtolower($fileType);
            //格式验证(扩展名验证和Content-Type验证)，链接contentType是否正确
            $is_weixin_img = false;
            if (preg_match("/^http(s?):\\/\\/mmbiz.qpic.cn\\/(.*)/", $imgUrl) != 1) {
                $allowFiles = [".png", ".jpg", ".jpeg", ".gif", ".bmp", ".ico", ".webp"];
                if (!in_array($fileType, $allowFiles) || isset($heads['Content-Type']) && !stristr($heads['Content-Type'], "image/")) {
                    continue;
                }
            } else {
                $is_weixin_img = true;
            }
            //打开输出缓冲区并获取远程图片
            ob_start();
            $context = stream_context_create(array('http' => array('follow_location' => false)));
            readfile($imgUrl, false, $context);
            $img = ob_get_contents();
            ob_end_clean();
            preg_match("/[\\/]([^\\/]*)[\\.]?[^\\.\\/]*\$/", $imgUrl, $m);
            $file = [];
            $file['oriName'] = $m ? $m[1] : "";
            $file['filesize'] = strlen($img);
            $file['ext'] = $fileType;
            $file['name'] = self::$users_id . '-' . dd2char(date("ymdHis") . mt_rand(100, 999)) . $file['ext'];
            $file['fullName'] = self::$dirname . $file['name'];
            $fullName = $file['fullName'];
            //检查文件大小是否超出限制
            if ($file['filesize'] >= 20480000) {
                continue;
            }
            //移动文件
            if (!(file_put_contents($fullName, $img) && file_exists($fullName))) {
                //移动失败
                continue;
            }
            $fileurl = self::$root_dir . substr($file['fullName'], 1);
            if ($is_weixin_img == true) {
                $fileurl .= "?";
            }
            $body = str_replace($imgUrl, $fileurl, $body);
            // 添加水印
            self::print_water($fileurl);
        }
        return $body;
    }
    /**
     *  给图片增加水印
     *
     * @access    public
     * @param     string  $imgpath  不带子目录的图片路径
     * @return    string
     */
    public static function print_water($imgpath = '')
    {
        try {
            static $water = null;
            null === $water && ($water = tpCache('water'));
            if (empty($imgpath) || $water['is_mark'] != 1) {
                return $imgpath;
            }
            //$imgpath = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $imgpath); // 支持子目录
            $imgpath = parse_url($imgpath, PHP_URL_PATH);
            $imgresource = "." . $imgpath;
            $image = Image::open($imgresource);
            if ($image->width() > $water['mark_width'] && $image->height() > $water['mark_height']) {
                if ($water['mark_type'] == 'text') {
                    $ttf = INC_PATH . 'data/font/hgzb.ttf';
                    if (file_exists($ttf)) {
                        $size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
                        $color = $water['mark_txt_color'] ?: '#000000';
                        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                            $color = '#000000';
                        }
                        $transparency = intval((100 - $water['mark_degree']) * (127 / 100));
                        $color .= dechex($transparency);
                        $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['mark_sel'])->save($imgresource);
                    }
                } else {
                    //$water['mark_img'] = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $water['mark_img']); // 支持子目录
                    $water['mark_img'] = parse_url($water['mark_img'], PHP_URL_PATH);
                    $waterPath = "." . $water['mark_img'];
                    if (self::PreventShell($waterPath) && file_exists($waterPath)) {
                        $quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
                        $waterTempPath = dirname($waterPath) . '/temp_' . basename($waterPath);
                        $image->open($waterPath)->save($waterTempPath, null, $quality);
                        $image->open($imgresource)->water($waterTempPath, $water['mark_sel'], $water['mark_degree'])->save($imgresource);
                        @unlink($waterTempPath);
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
    /**
     * 内容图片地址替换成带有http地址
     *
     * @param string $content 内容
     * @param string $imgurl 远程图片url
     * @return string
     */
    public static function img_replace_url($content = '', $imgurl = '')
    {
        $pregRule = "/<img(.*?)src(\\s*)=(\\s*)[\\'|\"](.*?(?:[\\.jpg|\\.jpeg|\\.png|\\.gif|\\.bmp|\\.ico]))[\\'|\"](.*?)[\\/]?(\\s*)>/i";
        $content = preg_replace($pregRule, '<img ${1} src="' . $imgurl . '" ${5} />', $content);
        return $content;
    }
    /**
     * 验证是否shell注入
     * @param mixed        $data 任意数值
     * @return mixed
     */
    public static function PreventShell($data = '')
    {
        $data = true;
        if (is_string($data) && (preg_match('/^phar:\\/\\//i', $data) || stristr($data, 'phar://'))) {
            $data = false;
        } else {
            if (is_numeric($data)) {
                $data = intval($data);
            }
        }
        return $data;
    }
}