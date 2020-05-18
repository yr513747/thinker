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
use think\facade\Request;
// --------------------------------------------------------------------------
if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例
     * @return Request
     */
    function request(): \think\Request
    {
        return app('request');
    }
}
if (!function_exists('isWeixin')) {
    /**
     * 是否微信端访问
     * @return boolean
     */
    function isWeixin()
    {
        return Request::isWeixin();
    }
}
if (!function_exists('isWeixinApplets')) {
    /**
     * 是否微信端小程序访问
     * @return boolean
     */
    function isWeixinApplets()
    {
        return Request::isWeixinApplets();
    }
}
if (!function_exists('isQq')) {
    /**
     * 是否QQ端访问
     * @return boolean
     */
    function isQq()
    {
        return Request::isQq();
    }
}
if (!function_exists('isAlipay')) {
    /**
     * 是否支付端访问
     * @return boolean
     */
    function isAlipay()
    {
        return Request::isAlipay();
    }
}
if (!function_exists('getResponseType')) {
    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    function getResponseType()
    {
        return Request::isJson() || Request::isAjax() ? 'json' : 'html';
    }
}
if (!function_exists('isAjax')) {
    /**
     * 当前是否Ajax请求
     * @param  bool $ajax true 获取原始ajax请求
     * @return bool
     */
    function isAjax($ajax = false)
    {
        return Request::isJson() || Request::isAjax($ajax) ? true : false;
    }
}
if (!function_exists('isAjaxPost')) {
    /**
     * 当前是否AjaxPost请求
     * @param  bool $ajax true 获取原始ajax请求
     * @return bool
     */
    function isAjaxPost($ajax = false)
    {
        return Request::isJson() || Request::isAjax($ajax) && Request::isPost() ? true : false;
    }
}
if (!function_exists('isGet')) {
    /**
     * 是否为GET请求
     * @return bool
     */
    function isGet()
    {
        return Request::isGet();
    }
}
if (!function_exists('isPost')) {
    /**
     * 是否为POST请求
     * @return bool
     */
    function isPost()
    {
        return Request::isPost();
    }
}
if (!function_exists('isPut')) {
    /**
     * 是否为PUT请求
     * @return bool
     */
    function isPut()
    {
        return Request::isPut();
    }
}
if (!function_exists('isDelete')) {
    /**
     * 是否为DELTE请求
     * @return bool
     */
    function isDelete()
    {
        return Request::isDelete();
    }
}
if (!function_exists('isHead')) {
    /**
     * 是否为HEAD请求
     * @return bool
     */
    function isHead()
    {
        return Request::isHead();
    }
}
if (!function_exists('isPatch')) {
    /**
     * 是否为PATCH请求
     * @return bool
     */
    function isPatch()
    {
        return Request::isPatch();
    }
}
if (!function_exists('isOptions')) {
    /**
     * 是否为OPTIONS请求
     * @return bool
     */
    function isOptions()
    {
        return Request::isOptions();
    }
}
if (!function_exists('isCli')) {
    /**
     * 是否为cli
     * @return bool
     */
    function isCli()
    {
        return Request::isCli();
    }
}
if (!function_exists('isCgi')) {
    /**
     * 是否为cgi
     * @return bool
     */
    function isCgi()
    {
        return Request::isCgi();
    }
}
if (!function_exists('isSsl')) {
    /**
     * 当前是否ssl
     * @return bool
     */
    function isSsl()
    {
        return Request::isSsl();
    }
}
if (!function_exists('isPjax')) {
    /**
     * 当前是否Pjax请求
     * @param  bool $pjax true 获取原始pjax请求
     * @return bool
     */
    function isPjax($pjax = false)
    {
        return Request::isPjax($pjax);
    }
}
if (!function_exists('PreventShell')) {
    /**
     * 验证是否shell注入
     * @param mixed        $data 任意数值
     * @return mixed
     */
    function PreventShell($data = '')
    {
        $data = true;
        if (is_string($data) && (preg_match('/^phar:\/\//i', $data) || stristr($data, 'phar://'))) {
            $data = false;
        } else if (is_numeric($data)) {
            $data = intval($data);
        }

        return $data;
    }
}

if (!function_exists('daddslashes')) {
    /**
     * 对字符串进行反斜杠处理，如果服务器开启MAGIC_QUOTES_GPC。则不处理。
     *
     * @param string/array $string 处理的字符串或数组
     * @param bool         $force  是否强制反斜杠处理
     *
     * @return array 返回处理好的字符串或数组
     */
    function daddslashes($string, $force = 0)
    {
        !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
        if (!MAGIC_QUOTES_GPC || $force) {
            if (is_array($string)) {
                foreach ($string as $key => $val) {
                    $string[$key] = daddslashes($val, $force);
                }
            } else {
                if (!defined('IN_ADMIN')) {
                    $string = trim(addslashes(sqlinsert($string)));
                } else {
                    $string = trim(addslashes(strip_sql($string)));
                }
            }
        }
        return $string;
    }
}

if (!function_exists('input')) {
    /**
     * 获取输入数据 支持默认值和过滤
     * @param string $key     获取的变量名
     * @param mixed  $default 默认值
     * @param string $filter  过滤方法
     * @return mixed
     */
    function input($key = '', $default = null, $filter = '')
    {
		if (null !== Request::request('GLOBALS')) {
            throw new \Thinker\exceptions\AuthException('Access Error');
        }	
        if (0 === strpos($key, '?')) {
            $key = substr($key, 1);
            $has = true;
        }

        if ($pos = strpos($key, '.')) {
            // 指定参数来源
            $method = substr($key, 0, $pos);
            if (in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'route', 'param', 'request', 'session', 'cookie', 'server', 'env', 'path', 'file'])) {
                $key = substr($key, $pos + 1);
                if ('server' == $method && is_null($default)) {
                    $default = '';
                }
            } else {
                $method = 'param';
            }
        } else {
            // 默认为自动判断
            $method = 'param';
        }
				
		if (isset($has)) {
            $data = Request::has($key, $method);
        } else {
            $data = Request::$method($key, $default, $filter);
        }
		// IIS编码修正
	
        $passedArgs = $data;     
        if (strpos(Request::server('SERVER_SOFTWARE'), 'Microsoft-IIS') !== false) {
            foreach ($passedArgs as &$args) {
                $args = mb_convert_encoding($args, 'UTF-8', 'GBK');
            }
        }
        $data = isset($args) ? $args : $passedArgs;
		
        // 防止shell注入处理
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = PreventShell($val) ? $val : '';
            }
        } else if (is_string($data) && stristr($data, ',')) {
            $arr = explode(',', $data);
            foreach ($arr as $key => $val) {
                $arr[$key] = PreventShell($val) ? $val : '';
            }
            $data = implode(',', $arr);
        } else {
            $data = PreventShell($data) ? $data : '';
        }
				
        return daddslashes($data);
    }
}

if (!function_exists('I')) {
    /**
     * 兼容以前3.2的单字母单数 I
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @return mixed
     */
    function I($name, $default='', $filter='') {
     
        $value = input($name,'',$filter);        
        if($value !== null && $value !== ''){
            return daddslashes($value);
        }
        if(strstr($name, '.'))  
        {
            $name = explode('.', $name);
            $value = input(end($name),'',$filter);           
            if($value !== null && $value !== '')
                return daddslashes($value);            
        }
        if (!PreventShell($default)) {
            $default = '';
        }
        return daddslashes($default);
    } 
}

if (!function_exists('isMobile')) {
    /**
     * 检测是否使用手机访问
     * @return bool
     */
    function isMobile()
    {
        return Request::isMobile();
    }
}
if (!function_exists('checkToken')) {
    /**
     * 检查请求令牌
     * @access public
     * @param  string $token 令牌名称
     * @param  array  $data  表单数据
     * @return bool
     */
    function checkToken($token = '__token__', $data = array())
    {
        return Request::checkToken($token, $data);
    }
}

if (!function_exists('token')) {
    /**
     * 获取Token令牌
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token(string $name = '__token__', string $type = 'sha1'): string
    {
        return Request::buildToken($name, $type);
    }
}

if (!function_exists('token_field')) {
    /**
     * 生成令牌隐藏表单
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token_field(string $name = '__token__', string $type = 'sha1'): string
    {
        $token = Request::buildToken($name, $type);

        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('token_meta')) {
    /**
     * 生成令牌meta
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token_meta(string $name = '__token__', string $type = 'sha1') : string
    {
        $token = Request::buildToken($name, $type);
        $result = "<meta name=\"{$name}\" content=\"{$token}\">\r\n";
        $result .= <<<EOHTML
<script type="text/javascript">
    \$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': \$('meta[name="{$name}"]').attr('content')
    }
});
</script>
EOHTML;
        return $result;
    }
}

