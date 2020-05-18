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
// [ 系统公用一级基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\basic;

use think\App;
use think\Container;
use think\Validate;
use think\Response;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use BadMethodCallException;
use Thinker\exceptions\AuthException;
use Thinker\basic\contract\TemplateHandlerInterface;
use Thinker\View;
use app\common\model\Config as ConfigModel;
use app\common\model\UsersConfig as UsersConfigModel;
use Thinker\route\Url as UrlBuild;
abstract class Common implements TemplateHandlerInterface
{
    use traits\Jump;
    use traits\AdminFuncTrait;
    use traits\CommonFuncTrait;
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 当前应用实例
     * @var \think\Http
     */
    protected $http;
    /**
     * 请求对象
     * @var \think\Request
     */
    protected $request;
    /**
     * 视图实例
     * @var \Thinker\View
     */
    protected $view;
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    /**
     * 当前的 session_id
     * @var string
     */
    protected $var_session_id;
    /**
     * 当前模板变量数组
     * @var array
     */
    protected $thinker = [];
    /**
     * 当前会员登陆id
     * @var mixed
     */
    protected $users_id = 0;
    /**
     * 当前会员信息数组
     * @var array
     */
    protected $users = [];
    /**
     * json/jsonp输出参数
     * @var array
     */
    protected $options = [
        //
        'var_jsonp_handler' => 'callback',
        'default_jsonp_handler' => 'jsonpReturn',
        //JSON_INVALID_IGNORE(integer)和JSON_INVALTD_UTF8_SUBSTITUTE(integer)的支持，取代之前的utf-8编码的无效类型
        //JSON_UNESCAPED_UNICODE (integer) 以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX） 　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];
    
    
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        // history.back返回后输入框值丢失问题
        header("Cache-control: private");
        $this->var_session_id = var_session_id();
        // 将当前的session_id保存为常量，供其它方法调用
        !defined('SESSION_ID') && define('SESSION_ID', $this->var_session_id);
        $this->app = is_null($app) ? Container::pull('app') : $app;
        $this->request = $this->app->request;
        $this->http = $this->app->http;     
		// 获取系统全局变量
        $this->loadParams();  
		 // 获取请求变量
        $this->loadForm();
        // 初始化视图驱动
        $template = config('template', []);
        $this->view = View::instance($template);
        // 初始化操作
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        try {
            $this->loadGlobal();
            $this->loadConfig();
            $this->loadSystem();
			$this->loadUsers();
            // 系统变量赋值，可以在模板中使用$params获取系统变量支持无限级使用'.'号分割
            $params = $this->params;
            $version = $params['version'];
            $global = $params['global'];
            // 当前模板全局变量
            $this->thinker['global'] = $params['global'];
            // 判断是否开启注册入口
			$users_open_register = isset($params['users']['users_open_register']) ? $params['users']['users_open_register'] : '';
            unset($params['global'], $params['get_defined_functions'], $params['get_defined_constants']);
            $this->assign(compact('params', 'version', 'global', 'users_open_register'));
        } catch (\PDOException $e) {
            // 系统变量赋值
            $params = $this->params;
            $version = $params['version'];
            unset($params['get_defined_functions'], $params['get_defined_constants']);
            $this->assign(compact('params', 'version'));
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template)
    {
        return $this->view->exists($template);
    }
    /**
     * 视图过滤
     * @access public
     * @param Callable $filter 过滤方法或闭包
     * @return $this
     */
    public function filter(callable $filter = null)
    {
        $this->view->filter($filter);
        return $this;
    }
    /**
     * 加载模板输出
     * @access public
     * @param string $template
     * @param array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     * @throws \think\Exception
     */
    public function fetch($template = '', $vars = [])
    {
        $html = $this->view->fetch($template, $vars);
        //尝试写入静态缓存
        if (false === $this->app->isDebug()) {
            $this->writeHtmlCache($html);
        }
        return $html;
    }
    /**
     * 渲染内容输出
     * @access public
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @return mixed
     */
    public function display($content = '', $vars = [])
    {
        return $this->view->display($content, $vars);
    }
    /**
     * 模板变量赋值
     * @access public
     * @param string|array $name  模板变量
     * @param mixed        $value 变量值
     * @return $this
     */
    public function assign($name, $value = null)
    {
        $this->view->assign($name, $value);
        return $this;
    }
    /**
     * 初始化模板引擎
     * @access public
     * @param  array $engine 引擎参数
     * @return $this
     */
    public function engine($engine)
    {
        $this->view->engine($engine);
        return $this;
    }
    /**
     * 写入静态模板缓存用于页面展示(运营模式)
     * @access public
     * @param mixed $html 缓存值
     * @return bool
     */
    public function writeHtmlCache($html = '')
    {
        if (isGet() && !in_array($this->params['app_name'], config('app.deny_multi_app_list', []))) {
			
            $html_cache_type = config('html.cache_type', []);
            $param = input('param.');
            $m_c_a_str = $this->params['app_name'] . '_' . $this->params['controller_name'] . '_' . $this->params['action_name'];
            // 应用_控制器_方法
            $m_c_a_str = strtolower($m_c_a_str);
            foreach ($html_cache_type as $key => $val) {
                $key = strtolower($key);
                if ($key != $m_c_a_str) {
                    //不是当前 应用 控制器 方法 直接跳过
                    continue;
                }
                if (empty($val['filename'])) {
                    continue;
                }
                $filename = '';
                // 组合参数
                if (isset($val['p'])) {
                    foreach ($val['p'] as $k => $v) {
                        if (isset($param[$v])) {
                            if (preg_match('/\\/$/i', $filename)) {
                                $filename .= $param[$v];
                            } else {
                                if (!empty($filename)) {
                                    $filename .= '_';
                                }
                                $filename .= $param[$v];
                            }
                        }
                    }
                }
                empty($filename) && ($filename = 'index');
                $path = runtime_path('html') . $val['filename'] . DS;
                if (isMobile()) {
                    $path .= "_mobile";
                } else {
                    $path .= "_pc";
                }
                $filename = $path . '_html' . DS . "{$filename}.html";
                $this->checkDirBuild(dirname($filename));
                !empty($html) && @file_put_contents($filename, $html);
                return TRUE;
            }
        }
    }
    /**
     * 读取静态模板缓存用于页面展示(运营模式)
     * @access public
     * @return mixed
     */
    public function readHtmlCache()
    {
        if (isGet() && !in_array($this->params['app_name'], config('app.deny_multi_app_list', []))) {
            $html_cache_type = config('html.cache_type', []);
            $param = input('param.');
            $m_c_a_str = $this->params['app_name'] . '_' . $this->params['controller_name'] . '_' . $this->params['action_name'];
            // 应用_控制器_方法
            $m_c_a_str = strtolower($m_c_a_str);
            foreach ($html_cache_type as $key => $val) {
                $key = strtolower($key);
                if ($key != $m_c_a_str) {
                    //不是当前 应用 控制器 方法 直接跳过
                    continue;
                }
                if (empty($val['filename'])) {
                    continue;
                }
                $filename = '';
                // 组合参数
                if (isset($val['p'])) {
                    foreach ($val['p'] as $k => $v) {
                        if (isset($param[$v])) {
                            if (preg_match('/\\/$/i', $filename)) {
                                $filename .= $param[$v];
                            } else {
                                if (!empty($filename)) {
                                    $filename .= '_';
                                }
                                $filename .= $param[$v];
                            }
                        }
                    }
                }
                empty($filename) && ($filename = 'index');
                $path = runtime_path('html') . $val['filename'] . DS;
                if (isMobile()) {
                    $path .= "_mobile";
                } else {
                    $path .= "_pc";
                }
                $filename = $path . '_html' . DS . "{$filename}.html";
                if (is_file($filename)) {
                    $html = @file_get_contents($filename);
                    if (!empty($html)) {
                        return $this->html($html);
                    }
                }
            }
        }
    }
    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->beparseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        $v->message($message);
        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        return $v->failException(true)->check($data);
    }
    
    /**
     * 空操作
     * @access public
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __callb($method, $args)
    {
        if ($method instanceof Response) {
            throw new HttpResponseException($method);
        } else {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }
    }
}