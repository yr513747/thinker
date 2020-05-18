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

namespace think\traits\think;

use think\App;
use think\Http;
use think\Container;

trait RequestTrait
{
	/**
     * @var App
     */
    protected $app;
	
	/**
     * @var Http
     */
    protected $http;
	
	/**
     * 当前应用名
     * @var string
     */
    protected $appName;
	
	/**
     * 全局过滤规则
     * @var array
     */
	protected $filter = ['trim', 'addslashes', 'strip_tags', 'htmlspecialchars'];
	
	/**
     * @var array 当前路由信息
     */
    protected $routeInfo = [];

    /**
     * @var array 当前调度信息
     */
    protected $dispatch = [];
	
	/**
     * 初始化
     * @access public
     * @return \think\Request
     */
    public static function instance() : \think\Request
    {
        return Container::pull('app')->request;
    }
	/**
     * 获取当前请求的时间
     * @access public
     * @param  bool $float 是否使用浮点类型
     * @return integer|float
     */
    public function getTime(bool $float = false)
    {
        return $float ? $this->server('REQUEST_TIME_FLOAT') : $this->server('REQUEST_TIME');
    }
	
	/**
     * 获取当前请求的路由信息
     * @access public
     * @param array $route 路由名称
     * @return array
     */
    public function routeInfo($route = [])
    {
        if (!empty($route)) {
            $this->routeInfo = $route;
        } else {
            return $this->routeInfo;
        }
    }

    /**
     * 设置或者获取当前请求的调度信息
     * @access public
     * @param array $dispatch 调度信息
     * @return array
     */
    public function dispatch($dispatch = null)
    {
        if (!is_null($dispatch)) {
            $this->dispatch = $dispatch;
        }
        return $this->dispatch;
    }
	
	/**
     * 当前请求的host包含子目录
     * @access public
     * @param bool $strict  true 仅仅获取HOST
     * @return string
     */
    public function host(bool $strict = false): string
    {
        if ($this->host) {
            $host = $this->host;
        } else {
            $host = strval($this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST'));
        }
		if (true === $strict) {
            $host = strpos($host, ':') ? strstr($host, ':', true) : $host;
        }
	     
        //return true === $strict && strpos($host, ':') ? strstr($host, ':', true) : $host;
		return $host . $this->rootUrl();
    }
	
	/**
     * 设置当前请求参数
     * @access public
     * @param  string|array  $name 参数
     * @param  mixed         $value 值
     * @return void
     */
    public function withParam($name, $value = null) : void
    {
        if (is_array($name)) {
            $this->param = array_merge($this->param, $name);
        } else {
            $this->param[$name] = $value;
        }
    }
	
	/**
     * 设置应用对象
     * @access public
     * @param App $http 对象
     * @return $this
     */
    public function withApp(App $app)
    {
        $this->app = $app;
        return $this;
    }
	
	/**
     * 设置应用对象
     * @access public
     * @param Http $http 对象
     * @return $this
     */
    public function withHttp(Http $http)
    {
        $this->http = $http;
        return $this;
    }
	
	/**
     * 检索作为布尔值的输入。
     *
     * 当值为“1”、“true”、“on”和“yes”时返回true。否则，返回false。
     *
     * @param  string|null  $key
     * @param  bool  $default
     * @return bool
     */
    public function boolean($key = null, $default = false) : bool
    {
        return filter_var($this->param($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
	
	/**
     * 检索蜘蛛
     * @access public
     * @return string|null
     */	
	public function isSpider()
    {
        $userAgent = strtolower($this->server('HTTP_USER_AGENT'));
        $spiders = array(
            'Googlebot',
            // Google
            'Baiduspider',
            // 百度
            '360Spider',
            // 360
            'bingbot',
            // Bing
            'Sogou web spider',
        );
        foreach ($spiders as $spider) {
            $spider = strtolower($spider);
            //查找有没有出现过
            if (strpos($userAgent, $spider) !== false) {
                return $spider;
            }
        }
    }
	
	/**
     * 设置当前的应用名
     * @access public
     * @param  string $appName 应用名
     * @return $this
     */
  
    public function setApp($appName)
    {
		$this->appName = $appName;
		
		if (!is_null($this->http)) {
            $this->http->name($appName);
        }
		      		
        return $this;
    }
	
	/**
     * 获取当前的应用名
     * @access public
     * @return string
     */
    public function app() : string
    {
        $name = $this->appName ?: '';
		
		if (!is_null($this->http)) {
           $name = $this->http->getName();
        }
		
        return $name;
    }
	
	/**
     * 设置当前请求的安全Key
     * @access public
	 * @param  string $secureKey key值
     * @return $this
     */
    public function setSecureKey($secureKey)
    {
        $this->secureKey = md5(substr(sha1($secureKey), 0, 16));

        return $this;
    }
	
	/**
     * 获取当前请求的安全Key
     * @access public
     * @return string
     */
    public function secureKey(): string
    {
		$default_key = 'a!takA:dlmcldEv,e';
        if (is_null($this->secureKey)) {
            $this->secureKey = md5(substr(sha1($default_key), 0, 16));
        }

        return $this->secureKey;
    }
	
	/**
     * CURL发送请求
     * @param  string $url     请求URL地址
     * @param  string $method  请求方法GET/POST
     * @param  array  $params  请求参数
     * @param  array  $header  请求header信息
     * @param  int    $timeout 允许执行的最长秒数
     * @param  string $cookie  请求cookie信息
     * @param  bool   $multi   是否传输文件
     * @return mixed
     */
    public function httpRequest(string $url, string $method = 'GET', array $params = [], array $header = [], int $timeout = 30, string $cookie = '', bool $multi = false)
    {
        /* Curl settings */
        $opts = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0",
            // 在发起连接前等待的时间，如果设置为0，则无限等待
            CURLOPT_CONNECTTIMEOUT => 60,
            // 设置cURL允许执行的最长秒数
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => 1,
            // 不从证书中检查SSL加密算法是否存在
            CURLOPT_SSL_VERIFYPEER => false,
            // https请求 不验证证书和hosts
            CURLOPT_SSL_VERIFYHOST => false,
            // 启用时会将头文件的信息作为数据流输出
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $header,
            CURLINFO_HEADER_OUT => true,
        ];
        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {
            $opts[CURLOPT_FOLLOWLOCATION] = 1;
            // 指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的
            $opts[CURLOPT_MAXREDIRS] = 2;
        }
        // COOKIE带过去
        if (!empty($cookie)) {
            $opts[CURLOPT_COOKIE] = $cookie;
        }
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception('请求发生错误：' . $error);
        }
        return $data;
    }
	
	/**
     * 创建一个URL请求
     * @access public
     * @param  string    $uri URL地址
     * @param  string    $method 请求类型
     * @param  array     $params 请求参数
     * @param  array     $cookie
     * @param  array     $files
     * @param  array     $server
     * @param  string    $content
     * @return \think\Request
     */
    public function create($uri, $method = 'GET', $params = [], $cookie = [], $files = [], $server = [], $content = null) : \think\Request
    {
        $server['PATH_INFO']      = '';
        $server['REQUEST_METHOD'] = strtoupper($method);
        $info                     = parse_url($uri);

        if (isset($info['host'])) {
            $server['SERVER_NAME'] = $info['host'];
            $server['HTTP_HOST']   = $info['host'];
        }

        if (isset($info['scheme'])) {
            if ('https' === $info['scheme']) {
                $server['HTTPS']       = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }

        if (isset($info['port'])) {
            $server['SERVER_PORT'] = $info['port'];
            $server['HTTP_HOST']   = $server['HTTP_HOST'] . ':' . $info['port'];
        }

        if (isset($info['user'])) {
            $server['PHP_AUTH_USER'] = $info['user'];
        }

        if (isset($info['pass'])) {
            $server['PHP_AUTH_PW'] = $info['pass'];
        }

        if (!isset($info['path'])) {
            $info['path'] = '/';
        }

        $options     = [];
        $queryString = '';

        $options[strtolower($method)] = $params;

        if (isset($info['query'])) {
            parse_str(html_entity_decode($info['query']), $query);
            if (!empty($params)) {
                $params      = array_replace($query, $params);
                $queryString = http_build_query($params, '', '&');
            } else {
                $params      = $query;
                $queryString = $info['query'];
            }
        } elseif (!empty($params)) {
            $queryString = http_build_query($params, '', '&');
        }

        if ($queryString) {
            parse_str($queryString, $get);
            $options['get'] = isset($options['get']) ? array_merge($get, $options['get']) : $get;
        }

        $server['REQUEST_URI']  = $info['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $server['QUERY_STRING'] = $queryString;
        $options['cookie']      = $cookie;
        $options['param']       = $params;
        $options['file']        = $files;
        $options['server']      = $server;
        $options['url']         = $server['REQUEST_URI'];
        $options['baseUrl']     = $info['path'];
        $options['pathinfo']    = '/' == $info['path'] ? '/' : ltrim($info['path'], '/');
        $options['method']      = $server['REQUEST_METHOD'];
        $options['domain']      = isset($info['scheme']) ? $info['scheme'] . '://' . $server['HTTP_HOST'] : '';
        $options['content']     = $content;

        $request = static::instance();
        foreach ($options as $name => $item) {
            if (property_exists($request, $name)) {
                $request->$name = $item;
            }
        }

        return $request;
    }
	
	/**
     * 获取URL访问根目录
     * @access public
     * @return string
     */
    public function rootUrl(): string
    {
        $base = $this->root();
        $root = strpos($base, '.') ? ltrim(dirname($base), DIRECTORY_SEPARATOR) : $base;

        if ('' != $root) {
            $root = '/' . ltrim($root, '/');
        }

        return $root;
    }
	
	/**
     * 获取当前请求URL的pathinfo信息（含URL后缀）
     * @access public
     * @return string
     */
    public function pathinfo(): string
    {
        if (is_null($this->pathinfo)) {
            if (isset($_GET[$this->varPathinfo])) {
                // 判断URL里面是否有兼容模式参数
                $pathinfo = $_GET[$this->varPathinfo];
                unset($_GET[$this->varPathinfo]);
                unset($this->get[$this->varPathinfo]);
            } elseif ($this->server('PATH_INFO')) {
                $pathinfo = $this->server('PATH_INFO');
            } elseif (false !== strpos(PHP_SAPI, 'cli')) {
                $pathinfo = strpos($this->server('REQUEST_URI'), '?') ? strstr($this->server('REQUEST_URI'), '?', true) : $this->server('REQUEST_URI');
            }

            // 分析PATHINFO信息
            if (!isset($pathinfo)) {
                foreach ($this->pathinfoFetch as $type) {
                    if ($this->server($type)) {
                        $pathinfo = (0 === strpos($this->server($type), $this->server('SCRIPT_NAME'))) ?
                        substr($this->server($type), strlen($this->server('SCRIPT_NAME'))) : $this->server($type);
                        break;
                    }
                }
            }

            if (!empty($pathinfo)) {
                unset($this->get[$pathinfo], $this->request[$pathinfo]);
            }

            $this->pathinfo = empty($pathinfo) || '/' == $pathinfo ? '/' : ltrim($pathinfo, '/');
			//$this->pathinfo = empty($pathinfo) ||  '/' == $pathinfo ? '' : ltrim($pathinfo, '/');
			//$this->pathinfo = empty($pathinfo) ? '/' : ltrim($pathinfo, '/');
        }

        return $this->pathinfo;
    }
	
	/**
     * 获取当前请求URL的pathinfo信息(不含URL后缀)
     * @access public
     * @return string
     */
    public function path(): string
    {
        if (is_null($this->path)) {
            $suffix   = Config::get('route.url_html_suffix');
            $pathinfo = $this->pathinfo();
            if (false === $suffix) {
                // 禁止伪静态访问
                $this->path = $pathinfo;
            } elseif ($suffix) {
                // 去除正常的URL后缀
                $this->path = preg_replace('/\.(' . ltrim($suffix, '.') . ')$/i', '', $pathinfo);
            } else {
                // 允许任何后缀访问
                $this->path = preg_replace('/\.' . $this->ext() . '$/i', '', $pathinfo);
            }
        }
        return $this->path;
    }
	
	/**
     * 生成请求令牌
     * @access public
     * @param  string $name 令牌名称
     * @param  mixed  $type 令牌生成方法
     * @return string
     */
    public function buildToken(string $name = '__token__', $type = 'sha1'): string
    {
		$type  = is_callable($type) ? $type : 'sha1';
        $token = call_user_func($type, $this->server('REQUEST_TIME_FLOAT'));
		//加密显示数据
		$result = $this->secureKey() . '_' . $token;
        $this->session->set($name, $token);

        return $result;
    }
	
	/**
     * 检查请求令牌
     * @access public
     * @param  string $token 令牌名称
     * @param  array  $data  表单数据
     * @return bool
     */
    public function checkToken(string $token = '__token__', array $data = []): bool
    {
        if (in_array($this->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        if (!$this->session->has($token)) {
            // 令牌数据无效
            return false;
        }

        // Header验证
        if ($this->header('X-CSRF-TOKEN') && $this->secureKey() . '_' . $this->session->get($token) === $this->header('X-CSRF-TOKEN')) {
            return true;
        }

        if (empty($data)) {
            $data = $this->post();
        }

        // 令牌验证
        if (isset($data[$token]) && $this->secureKey() . '_' . $this->session->get($token) === $data[$token]) {         
            return true;
        }
		
        return false;
    }
	
	/**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile() : bool
    {
        if ($this->server('HTTP_VIA') && stristr($this->server('HTTP_VIA'), "wap")) {
            return true;
        } elseif ($this->server('HTTP_ACCEPT') && strpos(strtoupper($this->server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif ($this->server('HTTP_X_WAP_PROFILE') || $this->server('HTTP_PROFILE')) {
            return true;
        } elseif ($this->server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|Android|ucweb|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->server('HTTP_USER_AGENT'))) {
            return true;
        }
        $user_agent = $this->server('HTTP_USER_AGENT');
        $mobile_agents = array('240x320', 'acer', 'acoon', 'acs-', 'abacho', 'ahong', 'airness', 'alcatel', 'amoi', 'android', 'anywherthinkergo.com', 'applewebkit/525', 'applewebkit/532', 'asus', 'audio', 'au-mic', 'avantogo', 'becker', 'benq', 'bilbo', 'bird', 'blackberry', 'blazer', 'bleu', 'cdm-', 'compal', 'coolpad', 'danger', 'dbtel', 'dopod', 'elaine', 'eric', 'etouch', 'fly ', 'fly_', 'fly-', 'go.web', 'goodaccess', 'gradiente', 'grundig', 'haier', 'hedy', 'hitachi', 'htc', 'huawei', 'hutchison', 'inno', 'ipaq', 'ipod', 'jbrowser', 'kddi', 'kgt', 'kwc', 'lenovo', 'lg ', 'lg2', 'lg3', 'lg4', 'lg5', 'lg7', 'lg8', 'lg9', 'lg-', 'lge-', 'lge9', 'longcos', 'maemo', 'mercator', 'meridian', 'micromax', 'midp', 'mini', 'mitsu', 'mmm', 'mmp', 'mot-', 'moto', 'nec-', 'netfront', 'newgen', 'nexian', 'nf-browser', 'nintendo', 'nitro', 'nokia', 'nook', 'novarra', 'obigo', 'palm', 'panasonic', 'pantech', 'philips', 'phone', 'pg-', 'playstation', 'pocket', 'pt-', 'qc-', 'qtek', 'rover', 'sagem', 'sama', 'samu', 'sanyo', 'samsung', 'sch-', 'scooter', 'sec-', 'sendo', 'sgh-', 'sharp', 'siemens', 'sie-', 'softbank', 'sony', 'spice', 'sprint', 'spv', 'symbian', 'tablet', 'talkabout', 'tcl-', 'teleca', 'telit', 'tianyu', 'tim-', 'toshiba', 'tsm', 'up.browser', 'utec', 'utstar', 'verykool', 'virgin', 'vk-', 'voda', 'voxtel', 'vx', 'wap', 'wellco', 'wig browser', 'wii', 'windows ce', 'wireless', 'xda', 'xde', 'zte');
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }
	
	/**
     * 获取当前用户客户端IP
     * @access public
	 * @param  boolean  $long 是否转换成整数
     * @return string
     */
    public function clientIP($long = false): string
    {
        $ip = $this->ip();
		$realIP = '';
        if (preg_match('/^((?:(?:25[0-5]|2[0-4]\\d|((1\\d{2})|([1-9]?\\d)))\\.){3}(?:25[0-5]|2[0-4]\\d|((1\\d{2})|([1 -9]?\\d))))$/', $ip)) {
            $realIP = $ip;
        } 
		if($long) {
			$realIP = sprintf("%u", ip2long($realIP));
		}
		return $realIP;
    }
	
	/**
     * 获取当前服务器端IP
     * @access public
     * @return string
     */
    public function serverIP(): string
    {
        return gethostbyname($this->server("SERVER_NAME"));
    }
	/**
     * 是否微信端访问
     * @access public
     * @return boolean
     */
    public function isWeixin() : bool
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
    /**
     * 是否微信端小程序访问
     * @access public
     * @return boolean
     */
    public function isWeixinApplets() : bool
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'miniProgram') !== false) {
            return true;
        }
        return false;
    }
    /**
     * 是否QQ端访问
     * @access public
     * @return boolean
     */
    public function isQq() : bool
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'QQ') !== false) {
            return true;
        }
        return false;
    }
    /**
     * 是否支付宝客户端
     * @access public
     * @return boolean
     */
    public function isAlipay() : boolean
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'AlipayClient') !== false) {
            return true;
        }
        return false;
    }
	/**
     * 是否百度小程序
     * @access public
     * @return boolean
     */
    public function isBaidu() : boolean
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'swan-baiduboxapp') !== false) {
            return true;
        }
        return false;
    }
	/**
     * 是否头条小程序
     * @access public
     * @return boolean
     */
    public function isToutiao() : boolean
    {
        if (strpos($this->server("HTTP_USER_AGENT"), 'ToutiaoMicroApp') !== false) {
            return true;
        }
        return false;
    }
	
	/**
     * 判断当前是否小程序环境中
     * @access public
     * @return string
     */
    public function miniAppEnv() : string
    {
        if($this->server("HTTP_USER_AGENT"))
        {
            // 微信小程序 miniProgram
            // QQ小程序 miniProgram
            if(stripos($this->server("HTTP_USER_AGENT"), 'miniProgram') !== false)
            {
                // 是否QQ小程序
                if(stripos($this->server("HTTP_USER_AGENT"), 'QQ') !== false)
                {
                    return 'qq';
                }
                return 'weixin';
            }

            // 支付宝客户端 AlipayClient
            if(stripos($this->server("HTTP_USER_AGENT"), 'AlipayClient') !== false)
            {
                return 'alipay';
            }

            // 百度小程序 swan-baiduboxapp
            if(stripos($this->server("HTTP_USER_AGENT"), 'swan-baiduboxapp') !== false)
            {
                return 'baidu';
            }

            // 头条小程序 ToutiaoMicroApp
            if(stripos($this->server("HTTP_USER_AGENT"), 'ToutiaoMicroApp') !== false)
            {
                return 'toutiao';
            }
        }
        return null;
    }
}