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
use think\App;
use think\Container;
use think\facade\Cookie;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Route;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Event;
use think\facade\Db;
use think\Model;
use think\Validate;
use think\Collection;
use think\model\Collection as ModelCollection;
// --------------------------------------------------------------------------
if (!function_exists('app')) {
    /**
     * 快速获取容器中的实例 支持依赖注入
     * @param string $name        类名或标识 默认获取当前应用实例
     * @param array  $args        参数
     * @param bool   $newInstance 是否每次创建新的实例
     * @return object|App
     */
    function app(string $name = '', array $args = [], bool $newInstance = false)
    {
        return Container::getInstance()->make($name ?: App::class, $args, $newInstance);
    }
}

if (!function_exists('bind')) {
    /**
     * 绑定一个类到容器
     * @param string|array $abstract 类标识、接口（支持批量绑定）
     * @param mixed        $concrete 要绑定的类、闭包或者实例
     * @return Container
     */
    function bind($abstract, $concrete = null)
    {
        return Container::getInstance()->bind($abstract, $concrete);
    }
}

if (!function_exists('invoke')) {
    /**
     * 调用反射实例化对象或者执行方法 支持依赖注入
     * @param mixed $call 类名或者callable
     * @param array $args 参数
     * @return mixed
     */
    function invoke($call, array $args = [])
    {
        if (is_callable($call)) {
            return Container::getInstance()->invoke($call, $args);
        }

        return Container::getInstance()->invokeClass($call, $args);
    }
}

if (!function_exists('parse_name')) {
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name    字符串
     * @param int    $type    转换类型
     * @param bool   $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    function parse_name($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback("/_([a-zA-Z])/", function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

if (!function_exists('widget')) {  
	/**
     * 远程调用渲染输出Widget 参数格式 [模块/控制器/]操作
     * @access public
     * @param  string       $url          调用地址
     * @param  string|array $vars         调用参数 支持字符串和数组
     * @param  string       $layer        要调用的控制层名称
     * @param  bool         $appendSuffix 是否添加类名后缀
     * @return mixed
     */
    function widget($url, $vars = [], $layer = 'widget', $appendSuffix = false)
    {
        return app()->action($url, $vars, $layer, $appendSuffix = false);
    }
}

if (!function_exists('controller')) {
    /**
     * 实例化（分层）控制器 格式：[模块名/]控制器名
     * @access public
     * @param  string $name         资源地址
     * @param  string $layer        控制层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $empty        空控制器名称
     * @return object
     * @throws ClassNotFoundException
     */
    function controller($name, $layer = 'controller', $appendSuffix = false, $empty = '')
    {
        return app()->controller($name, $layer, $appendSuffix, $empty);
    }
}

if (!function_exists('action')) {
    /**
     * 远程调用模块的操作方法 参数格式 [模块/控制器/]操作
     * @access public
     * @param  string       $url          调用地址
     * @param  string|array $vars         调用参数 支持字符串和数组
     * @param  string       $layer        要调用的控制层名称
     * @param  bool         $appendSuffix 是否添加类名后缀
     * @return mixed
     */
    function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        return app()->action($url, $vars, $layer, $appendSuffix);
    }
}

if (!function_exists('M')) {
    /**
     * 实例化（分层）模型
     * @access public
     * @param  string $name         Model名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function M($name = '', $common = 'common', $layer = 'model', $appendSuffix = false)
    {
        return app()->create($name, $layer, $appendSuffix, $common);
    }
}
if (!function_exists('model')) {
    /**
     * 实例化（分层）模型兼容旧版
     */
    function model($name = '', $common = 'common', $layer = 'model', $appendSuffix = false)
    {
        return app()->create($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('L')) {
    /**
     * 实例化（分层）逻辑
     * @access public
     * @param  string $name         logic名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function L($name = '', $common = 'common', $layer = 'logic', $appendSuffix = false)
    {
        return app()->create($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('S')) {
    /**
     * 实例化（分层）服务
     * @access public
     * @param  string $name         服务名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function S($name = '', $common = 'common', $layer = 'service', $appendSuffix = false)
    {
        return app()->create($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('weappM')) {
    /**
     * 实例化（分层）模型
     * @access public
     * @param  string $name         Model名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function weappM($name = '', $common = 'common', $layer = 'model', $appendSuffix = false)
    {
        return app()->weappCreate($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('weappL')) {
    /**
     * 实例化（分层）逻辑
     * @access public
     * @param  string $name         logic名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function weappL($name = '', $common = 'common', $layer = 'logic', $appendSuffix = false)
    {
        return app()->weappCreate($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('weappS')) {
    /**
     * 实例化（分层）服务
     * @access public
     * @param  string $name         服务名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    function weappS($name = '', $common = 'common', $layer = 'service', $appendSuffix = false)
    {
        return app()->weappCreate($name, $layer, $appendSuffix, $common);
    }
}

if (!function_exists('app_path')) {
    /**
     * 获取当前应用目录
     *
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->getAppPath() . ($path ? $path . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('base_path')) {
    /**
     * 获取应用基础目录
     *
     * @param string $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->getBasePath() . ($path ? $path . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * 获取应用配置目录
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->getConfigPath() . ($path ? $path . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * 获取web根目录
     *
     * @param string $path
     * @return string
     */
    function public_path($path = '')
    {
        return app()->getRootPath() . ($path ? ltrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('runtime_path')) {
    /**
     * 获取应用运行时目录
     *
     * @param string $path
     * @return string
     */
    function runtime_path($path = '')
    {
        return app()->getRuntimePath() . ($path ? $path . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('root_path')) {
    /**
     * 获取项目根目录
     *
     * @param string $path
     * @return string
     */
    function root_path($path = '')
    {
        return app()->getRootPath() . ($path ? $path . DIRECTORY_SEPARATOR : $path);
    }
}

if (!function_exists('db')) {
    /**
     * 实例化数据库类
     * @param string        $name 操作的数据表名称（不含前缀）
     * @param array|string  $config 数据库配置参数
     * @param bool          $force 是否强制重新连接
     * @return \think\db\Query
     */
    function db(string $name, $config = [], $force = false)
    {
        return Db::connect($config, $force)->name($name);
    }
}

if (!function_exists('D')) {
    /**
     * 兼容旧版
     * @param string        $name 操作的数据表名称（不含前缀）     
     * @return \think\db\Query
     */
    function D(string $name)
    {
        return Db::name($name);                
    }
}

if (!function_exists('show')) {
    /**
     * 数据返回通用方法  /在extra扩展配置目录下的 status.php 文件中配置业务状态码
     * @param int $status 业务状态码
     * @param sting $message 提示消息
     * @param mixed $data    返回的数据
     * @param int $httpStatus  返回的状态码
     * @return mixed
     */
    function show($status = 0, $message = "", $data = [], $httpStatus = 200)
    {
        // 返回JSON数据格式到客户端 包含状态信息 [当url_common_param为false时是无法获取到$_GET的数据的，故使用Request来获取<xiaobo.sun@qq.com>]
        $var_jsonp_handler = input('callback', '');
        $handler = !empty($var_jsonp_handler) ? $var_jsonp_handler : input('jsonpReturn', '');
		
        //如果消息提示为空，并且业务状态码定义了，那么就显示默认定义的消息提示
        if (empty($message) && !empty(config("status." . $status))) {
            $message = config("status." . $status);
        }
		
        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];
		
        if (request()->isJson() || request()->isAjax()) {
            if ($handler) {
                return jsonp($result, $httpStatus);
            } else {
                return json($result, $httpStatus);
            }
        }
		
        return $message;
    }
}

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name  参数名
     * @param mixed        $value 参数值
     * @return mixed
     */
    function config($name = '', $value = null)
    {
        if (is_array($name)) {
            return Config::set($name, $value);
        }

        return 0 === strpos($name, '?') ? Config::has(substr($name, 1)) : Config::get($name, $value);
    }
}

if (!function_exists('env')) {
    /**
     * 获取和设置环境变量值
     * @access public
     * @param string|array $name    环境变量名（支持二级 .号分割）
     * @param string $default 默认值/参数值
     * @param bool $isSet 设置参数
     * @return mixed
     */
    function env($name = null, $default = null, $isSet = false)
    {
        if ($isSet === false && !is_array($name)) {
            return 0 === strpos($name, '?') ? Env::has(substr($name, 1)) : Env::get($name, $default);
        } else {
            return Env::set($name, $default);
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理
     * @param string $name   cookie名称
     * @param mixed  $value  cookie值
     * @param mixed  $option 参数
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_null($value)) {
            // 删除
            Cookie::delete($name);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1)) : Cookie::get($name);
        } else {
            // 设置
            return Cookie::set($name, $value, $option);
        }
    }
}

if (!function_exists('cache')) {
    /**
     * 缓存管理
     * @param string $name    缓存名称
     * @param mixed  $value   缓存值
     * @param mixed  $options 缓存参数
     * @param string $tag     缓存标签
     * @return mixed
     */
    function cache($name = null, $value = '', $options = null, $tag = null)
    {
        if (is_null($name)) {
            return app('cache');
        }

        if ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? Cache::has(substr($name, 1)) : Cache::get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return Cache::delete($name);
        }

        // 缓存数据
        if (is_array($options)) {
			 /*可以自定义配置 by 仰融*/
            if (!empty($options)) {
                $cache_conf = Config::get('cache', array());
                $options = array_merge($cache_conf, $options);
            }
            /*--end*/
            $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
        } else {
            $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
        }

        if (is_null($tag)) {
            return Cache::set($name, $value, $expire);
        } else {
            return Cache::tag($tag)->set($name, $value, $expire);
        }
    }
}

if (!function_exists('session')) {
    /**
     * Session管理
     * @param string $name  session名称
     * @param mixed  $value session值
     * @return mixed
     */
    function session($name = '', $value = '')
    {
        if (is_null($name)) {
            // 清除
            Session::clear();
        } elseif ('' === $name) {
            return Session::all();
        } elseif (is_null($value)) {
            // 删除
            Session::delete($name);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1)) : Session::get($name);
        } else {
            // 设置
            Session::set($name, $value);
        }
    }
}

if (!function_exists('var_session_id')) {
    /**
     * 获取当前的 session_id
     * @param @param bool $destroy  重新生成session id
     * @return mixed
     */
    function var_session_id($destroy = false)
    {
        if ($destroy) {
            Session::regenerate($destroy);
        }
        return Session::getId();
    }
}

if (!function_exists('load_relation')) {
    /**
     * 延迟预载入关联查询
     * @param mixed $resultSet 数据集
     * @param mixed $relation 关联
     * @return array
     */
    function load_relation($resultSet, $relation)
    {
        $item = current($resultSet);
        if ($item instanceof Model) {
            $item->eagerlyResultSet($resultSet, $relation);
        }
        return $resultSet;
    }
}

if (!function_exists('collection')) {
    /**
     * 数组转换为数据集对象
     * @param array $resultSet 数据集数组
     * @return \think\model\Collection|\think\Collection
     */
    function collection($resultSet)
    {
        $item = current($resultSet);
        if ($item instanceof Model) {
            return ModelCollection::make($resultSet);
        } else {
            return Collection::make($resultSet);
        }
    }
}

if (!function_exists('url')) {
    /**
     * URL生成 支持路由反射
     * @param string            $url 路由地址
     * @param string|array      $vars 参数（支持数组和字符串）a=val&b=val2... ['a'=>'val1', 'b'=>'val2']
     * @param string|bool       $suffix 伪静态后缀，默认为true表示获取配置值
     * @param boolean|string    $domain 是否显示域名 或者直接传入域名
	 * @param bool              $toString 是否转换为字符串
     * @return string|UrlBuild
     */
    function url($url = '', $vars = array(), $suffix = true, $domain = false, $toString = true)
    {
		is_object($vars) && $vars = $vars->toArray();
		// aaa=1&bbb=2 转换成数组
		is_string($vars) && parse_str($vars, $vars);
		$url = Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
		$toString === true && $url = $url->build();
        return $url;
    }
}

if (!function_exists('U')) {
    /**
     * 兼容旧版
     */
    function  U($url = '', $vars = array(), $suffix = true, $domain = false, $toString = true) 
    {
		$url = url($url, $vars, $suffix, $domain, $toString);
        return $url;
    }
}

if (!function_exists('validate')) {
    /**
     * 生成验证对象
     * @param string|array $validate      验证器类名或者验证规则数组
     * @param array        $message       错误提示信息
     * @param bool         $batch         是否批量验证
     * @param bool         $failException 是否抛出异常
     * @return Validate
     */
    function validate($validate = '', array $message = [], bool $batch = false, bool $failException = true): Validate
    {
        if (is_array($validate) || '' === $validate) {
            $v = new Validate();
            if (is_array($validate)) {
                $v->rule($validate);
            }
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }

            $class = false !== strpos($validate, '\\') ? $validate : app()->parseClass('validate', $validate);

            $v = new $class();

            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        return $v->message($message)->batch($batch)->failException($failException);
    }
}

if (!function_exists('lang')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function lang(string $name, array $vars = [], string $lang = '')
    {
        return Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('event')) {
    /**
     * 触发事件
     * @param mixed $event 事件名（或者类名）
     * @param mixed $args  参数
     * @return mixed
     */
    function event($event, $args = null)
    {
        return Event::trigger($event, $args);
    }
}

if (!function_exists('hookexec')) {
    /**
     * 执行插件某个行为
     * @access public
     * @param  mixed  $class  要执行的行为(插件标识/控制器/操作方法)
     * @param  mixed  $params 传入的参数
     * @return mixed
     */
    function hookexec($class, $params = null)
    {
        $TagWeapp = new \Thinker\template\taglib\thinker\TagWeapp();
		return $TagWeapp->hookexec($class, $params);
    }
}

if (!function_exists('helper')) {
    /**
     * 载入小助手,系统默认载入小助手
     * @param     mix   $helpers  小助手名称,可以是数组,可以是单个字符串
     * @return    bool
     */
    function helper($helpers)
    {
        return \think\facade\App::helper($helpers);
    }
}
if (!function_exists('helperbypath')) {
    /**
     * 指定目录载入小助手
     * @param     mix   $helpers  小助手名称,可以是数组,可以是单个字符串
	 * @param     string $filesPath 文件路径
     * @return    bool
     */
    function helperbypath($helpers, $filesPath)
    {
        return \think\facade\App::helperbypath($helpers, $filesPath);
    }
}
if (!function_exists('loadfiles')) {
    /**
     * 加载应用文件
     * @param string $filesPath 文件路径
     * @return bool
     */
    function loadfiles($filesPath)
    {
        return \think\facade\App::loadfiles($filesPath);
    }
}
