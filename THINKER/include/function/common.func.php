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
// [ 系统核心函数存放文件 ]
// --------------------------------------------------------------------------
defined('DEBUG_LEVEL') || exit;
// --------------------------------------------------------------------------

if (!function_exists('make_path')) {

    /**
     * 上传路径转化,默认路径
     * @param $path
     * @param int $type
     * @param bool $force
     * @return string
     */
    function make_path($path, int $type = 2, bool $force = false)
    {
        $path = DIRECTORY_SEPARATOR . ltrim(rtrim($path));
        switch ($type) {
            case 1:
                $path .= DIRECTORY_SEPARATOR . date('Y');
                break;
            case 2:
                $path .= DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
                break;
            case 3:
                $path .= DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d');
                break;
        }
        try {
            if (is_dir(root_path('public') . 'uploads' . $path) == true || mkdir(root_path('public') . 'uploads' . $path, 0777, true) == true) {
                return trim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '.');
            } else return '';
        } catch (\Exception $e) {
            if ($force)
                throw new \Exception($e->getMessage());
            return '无法创建文件夹，请检查您的目录权限：' . root_path('public') . 'uploads' . $path;
        }

    }
}
if (!function_exists('copykey')) {
    /**
     * 提取一个数组中部分键值返回.
     *
     * @param array    $roc      提取的数组
     * @param keyarray $keyarray 需要提取的键值数组
     *
     * @return array 返回提取的键值数组
     */
    function copykey($roc, $keyarray)
    {
        $des = array();
        if (is_array($keyarray)) {
            foreach ($keyarray as $key => $val) {
                $des[$val] = $roc[$val];
            }
        }
        return $des;
    }
}
if (!function_exists('getbrowser')) {
    /**
     * 获取浏览器版本.
     *
     * @return string 浏览器
     */
    function getbrowser()
    {
        $agent = \think\facade\Request::server('HTTP_USER_AGENT');
        $browser = '';
        $browser_ver = '';
        if (preg_match('/OmniWeb\\/(v*)([^\\s|;]+)/i', $agent, $return)) {
            $browser = 'OmniWeb';
            $browser_ver = $return[2];
        }
        if (preg_match('/Netscape([\\d]*)\\/([^\\s]+)/i', $agent, $return)) {
            $browser = 'Netscape';
            $browser_ver = $return[2];
        }
        if (preg_match('/safari\\/([^\\s]+)/i', $agent, $return)) {
            $browser = 'Safari';
            $browser_ver = $return[1];
        }
        if (preg_match('/Chrome\\/([^\\s]+)/i', $agent, $return)) {
            $browser = 'Chrome';
            $browser_ver = $return[1];
        }
        if (preg_match('/MSIE\\s([^\\s|;]+)/i', $agent, $return)) {
            $browser = 'Internet Explorer';
            $browser_ver = $return[1];
        }
        if (preg_match('/Opera[\\s|\\/]([^\\s]+)/i', $agent, $return)) {
            $browser = 'Opera';
            $browser_ver = $return[1];
        }
        if (preg_match('/NetCaptor\\s([^\\s|;]+)/i', $agent, $return)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
            $browser_ver = $return[1];
        }
        if (preg_match('/Maxthon/i', $agent, $return)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
            $browser_ver = '';
        }
        if (preg_match('/360SE/i', $agent, $return)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') 360SE';
            $browser_ver = '';
        }
        if (preg_match('/SE 2.x/i', $agent, $return)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') sougou';
            $browser_ver = '';
        }
        if (preg_match('/FireFox\\/([^\\s]+)/i', $agent, $return)) {
            $browser = 'FireFox';
            $browser_ver = $return[1];
        }
        if (preg_match('/Lynx\\/([^\\s]+)/i', $agent, $return)) {
            $browser = 'Lynx';
            $browser_ver = $return[1];
        }
        if ($browser != '') {
            return $browser . ' ' . $browser_ver;
        } else {
            return false;
        }
    }
}
if (!function_exists('srcToLazyload')) {
    // 内容中图片路径lazyload预处理
    function srcToLazyload($str)
    {
        $str = preg_replace_callback('/(<img[^>]*)src(=[^>]*>)/', function ($match) {
            return $match['1'] . 'data-original' . $match['2'];
        }, $str);
        return $str;
    }
}
if (!function_exists('arraystrReplace')) {
    /**
     * strReplace 多维数组或字符串值字符替换.
     *
     * @param string $find    查找的字符
     * @param string $replace 替换的字符
     * @param string $array   数组或者字符串
     *
     * @return array/String $array 数组或者字符串
     */
    function arraystrReplace($find, $replace, $array)
    {
        if (is_array($array)) {
            $array = str_replace($find, $replace, $array);
            foreach ($array as $key => $val) {
                if (is_array($val)) {
                    $array[$key] = arraystrReplace($find, $replace, $array[$key]);
                }
            }
        } else {
            $array = str_replace($find, $replace, $array);
        }
        return $array;
    }
}
if (!function_exists('strReplace')) {
    /**
     * @param string $string 需要替换的字符串
     * @param int $start 开始的保留几位
     * @param int $end 最后保留几位
     * @return string
     */
    function strReplace($string, $start, $end)
    {
        $strlen = mb_strlen($string, 'UTF-8');//获取字符串长度
        $firstStr = mb_substr($string, 0, $start, 'UTF-8');//获取第一位
        $lastStr = mb_substr($string, -1, $end, 'UTF-8');//获取最后一位
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($string, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;

    }
}
if (!function_exists('get_sql')) {
    function get_sql($data)
    {
        $sql = '';
        foreach ($data as $key => $value) {
            if (strstr($value, "'")) {
                $value = str_replace("'", "\\'", $value);
            }
            $sql .= " {$key} = '{$value}',";
        }
        return trim($sql, ',');
    }
}

if (!function_exists('func_encrypt')) 
{
    /**
     * md5加密 
     *
     * @param string $str 字符串
     * @return array
     */
    function func_encrypt($str){
        $auth_code = \app\common\model\Config::tpCache('system.system_auth_code');
        if (empty($auth_code)) {
            $auth_code = \think\facade\Request::secureKey();
           
            \app\common\model\Config::tpCache('system', ['system_auth_code'=>$auth_code]);
            
        }
        return md5($auth_code.$str);
    }
}
if (!function_exists('get_head_pic')) 
{
    /**
     * 默认头像
     */
    function get_head_pic($pic_url = '', $is_admin = false)
    {
		global $_M;
		$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : \think\facade\Request::rootUrl();
        if ($is_admin) {
            $default_pic = $root_dir . '/static/admin/images/admint.png';
        } else {
            $default_pic = $root_dir . '/static/common/images/dfboy.png';
        }
        return empty($pic_url) ? $default_pic : $pic_url;
    }
}
if (!function_exists('getVersion')) 
{
    /**
     * 获取当前各种版本号
     *
     * @return string
     */
    function getVersion($filename='version', $ver='v1.0.0')
    {
        $version_txt_path = root_path('data').'config'.DIRECTORY_SEPARATOR.$filename.'.txt';
        if(file_exists($version_txt_path)) {
            $fp = fopen($version_txt_path, 'r');
            $content = fread($fp, filesize($version_txt_path));
            fclose($fp);
            $ver = $content ? $content : $ver;
        } else {
            $r = tp_mkdir(dirname($version_txt_path));
            if ($r) {
                $fp = fopen($version_txt_path, "w+") or die("请设置".$version_txt_path."的权限为777");
                if (fwrite($fp, $ver)) {
                    fclose($fp);
                }
            }
        }
        return $ver;
    }
}
if (!function_exists('tp_mkdir')) 
{
    /**
     * 递归创建目录 
     *
     * @param string $path 目录路径，不带反斜杠
     * @param intval $purview 目录权限码
     * @return boolean
     */  
    function tp_mkdir($path, $purview = 0777)
    {
        if (!is_dir($path)) {
            tp_mkdir(dirname($path), $purview);
            if (!mkdir($path, $purview)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('get_default_pic')) 
{
    /**
     * 图片不存在，显示默认无图封面
     * @param string $pic_url 图片路径
     * @param string|boolean $domain 完整路径的域名
     */
    function get_default_pic($pic_url = '', $domain = false)
    {
		global $_M;
		$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : \think\facade\Request::rootUrl();
        if (!is_http_url($pic_url)) {
            if (true === $domain) {
                $domain = \think\facade\Request::domain();
            } else if (false === $domain) {
                $domain = '';
            }
            
            $pic_url = preg_replace('#^(/[/\w]+)?(/upload/|/static/|/uploads/)#i', '$2', $pic_url); // 支持子目录
            $realpath = realpath(trim($pic_url, '/'));
            if ( is_file($realpath) && file_exists($realpath) ) {
                $pic_url = $domain . $root_dir . $pic_url;
            } else {
                $pic_url = $domain . $root_dir . '/static/common/images/not_adv.jpg';
            }
        }

        return $pic_url;
    }
}

if (!function_exists('handle_subdir')) 
{
    /**
     * 处理子目录与根目录的图片平缓切换
     * @param string $str 图片路径或html代码
     */
    function handle_subdir($str = '', $type = 'img', $domain = false)
    {
		global $_M;
		$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : \think\facade\Request::rootUrl();
      
        switch ($type) {
            case 'img':
                if (!is_http_url($str) && !empty($str)) {
                   
                        $str = preg_replace('#^(/[/\w]+)?(/upload/|/static/|/uploads/)#i', $root_dir.'$2', $str);
                    
                }else if (is_http_url($str) && !empty($str)) {
                    // 图片路径处理
                    $str = preg_replace('#^(/[/\w]+)?(/upload/|/uploads/|/static/)#i', $root_dir.'$2', $str);
                    $StrData = parse_url($str);
                    $strlen  = strlen($root_dir);
                    if (empty($StrData['scheme'])) {
                        if ('/uploads/'==substr($StrData['path'],$strlen,9) || '/upload/'==substr($StrData['path'],$strlen,8)) {
                            // 七牛云配置处理
                            static $Qiniuyun = null;
                            if (null == $Qiniuyun) {
                                // 需要填写你的 Access Key 和 Secret Key
                                $data     = \think\facade\Db::name('weapp')->where('code','Qiniuyun')->field('data,status')->find();
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
                                    $str = $tcp.$Qiniuyun['domain'].$StrData['path'];
                                }else{
                                    // 若切换了存储空间或访问域名，与数据库中存储的图片路径域名不一致时，访问本地路径，保证图片正常
                                    $str = $StrData['path'];
                                }
                            }else{
                                // 关闭
                                $str = $StrData['path'];
                            }
                        }
                    }
                }
                break;

            case 'html':
               
                    $str = preg_replace('#(.*)(\#39;|&quot;|"|\')(/[/\w]+)?(/upload/|/plugins/|/uploads/)(.*)#iU', '$1$2'.$root_dir.'$4$5', $str);
               
                break;

            case 'soft':
                if (!is_http_url($str) && !empty($str)) {
                    $str = preg_replace('#^(/[/\w]+)?(/upload/soft/|/uploads/soft/)#i', $root_dir.'$2', $str);
                }
                break;
            
            default:
                # code...
                break;
        }

        if (!empty($str) && !is_http_url($str) && true === $domain) {           
            $domain = NULL;
            $domain = \think\facade\Request::domain();
          
            $str = $domain.$str;
        }

        return $str;
    }
}
if (!function_exists('typeurl')) {
    /**
     * 栏目Url生成
     * @param string        $url 路由地址
     * @param string|array  $param 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function typeurl($url = '', $param = '', $suffix = true, $domain = false)
    {
        $thinkerUrl = '';
		is_object($param) && $param = $param->toArray();
        if (is_array($param)) {
            $vars = array('tid' => $param['dirname']);
        } else {
            $vars = $param;
        }
        $thinkerUrl .= url('home/Lists/index', $vars, $suffix, $domain);
        if (!strstr($thinkerUrl, '.htm')) {
            $thinkerUrl .= '/';
        }
        return $thinkerUrl;
    }
}
if (!function_exists('arcurl')) {
    /**
     * 文档Url生成
     * @param string        $url 路由地址
     * @param string|array  $param 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function arcurl($url = '', $param = '', $suffix = true, $domain = false)
    {
        $thinkerUrl = '';
		is_object($param) && $param = $param->toArray();
        if (is_array($param)) {
            $vars = array('aid' => $param['aid'], 'dirname' => $param['dirname']);
        } else {
            $vars = $param;
        }
        $thinkerUrl .= url('home/View/index', $vars, $suffix, $domain);
        return $thinkerUrl;
    }
}