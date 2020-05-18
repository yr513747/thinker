<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace think;

use Closure;
use Throwable;
use think\helper\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use think\event\HttpEnd;
use think\event\HttpRun;
use think\event\RouteLoaded;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ClassNotFoundException;

/**
 * Web应用管理类
 * @package think
 */
class Http
{
    /**
     * @var App
     */
    protected $app;
	
    /**
     * 请求对象
     * @var Request
     */
    protected $request;
	
    /**
     * 当前应用类库命名空间
     * @var string
     */
    protected $namespace = 'app';
	
    /**
     * 请求调度分发
     * @var array 
     */
    protected static $dispatch;
	
    /**
     * 应用名称
     * @var string
     */
    protected $name;
	
    /**
     * 应用名称
     * @var string
     */
    protected $appName;
	
    /**
     * 控制器名
     * @var string
     */
    protected $controller;
	
    /**
     * 操作名
     * @var string
     */
    protected $actionName;
	
    /**
     * 路由变量
     * @var array
     */
    protected $param;
	
    /**
     * 应用路径
     * @var string
     */
    protected $path;
	
    /**
     * 应用路径
     * @var string
     */
    protected $basePath;
	
    /**
     * 路由目录
     * @var string
     */
    protected $routePath;
	
    /**
     * 是否绑定应用
     * @var bool
     */
    protected $isBind = false;
	
	/**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */	
    public function __construct(App $app)
    {
        $this->app       = $app;
        $this->basePath  = $this->app->getBasePath();
        $this->routePath = $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR;
        $this->namespace = $this->app->getNamespace();
    }
	
    /**
     * 设置应用名称
     * @access public
     * @param string $name 应用名称
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }
	
    /**
     * 获取应用名称
     * @access public
     * @return string
     */
    public function getName() : string
    {
        return $this->name ?: '';
    }
	
    /**
     * 设置应用目录
     * @access public
     * @param string $path 应用目录
     * @return $this
     */
    public function path(string $path)
    {
        if (substr($path, -1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $this->path = $path;
        return $this;
    }
	
    /**
     * 获取应用路径
     * @access public
     * @return string
     */
    public function getPath() : string
    {
        return $this->path ?: '';
    }
	
    /**
     * 获取路由目录
     * @access public
     * @return string
     */
    public function getRoutePath() : string
    {
        return $this->routePath;
    }
	
    /**
     * 设置路由目录
     * @access public
     * @param string $path 路由定义目录
     * @return string
     */
    public function setRoutePath(string $path) : void
    {
        $this->routePath = $path;
    }
	
    /**
     * 设置应用绑定
     * @access public
     * @param bool $bind 是否绑定
     * @return $this
     */
    public function setBind(bool $bind = true)
    {
        $this->isBind = $bind;
        return $this;
    }
	
    /**
     * 是否绑定应用
     * @access public
     * @return bool
     */
    public function isBind() : bool
    {
        return $this->isBind;
    }
	
    /**
     * 执行应用程序
     * @access public
     * @param Request|null $request
     * @return Response
     */
    public function run(Request $request = null): Response
    {
        //自动创建request对象
        $request = $request ?? $this->app->make('request', [], true);
		$this->request = $request;
        $this->app->instance('request', $request);
		
		$request->withApp($this->app);
		$request->withHttp($this);
		
		if (!isset($GLOBALS['_M']['root_dir'])) {
            $GLOBALS['_M']['root_dir'] = $request->rootUrl();
        }
		
        try {
            $response = $this->runWithRequest($request);
        } catch (Throwable $e) {
            $this->reportException($e);

            $response = $this->renderException($request, $e);
        }

        return $response;
    }
	
    /**
     * 初始化
     */
    protected function initialize()
    {
        if (!$this->app->initialized()) {
            $this->app->initialize();
        }
    }
	
    /**
     * 执行应用程序
     * @param Request $request
     * @return mixed
     */
    protected function runWithRequest(Request $request)
    {
        $this->initialize();
		
        // 加载全局中间件
        $this->loadMiddleware();
		
        // 设置开启事件机制
        $this->app->event->withEvent($this->config('app.with_event', true));
		
        // 监听HttpRun
        $this->app->event->trigger(HttpRun::class);
		
        return $this->app->middleware->pipeline()
		    ->send($request)
			->then(function ($request) {
                return $this->dispatchToResponse($request);
            });
    }
	
    protected function dispatchToResponse($request)
    {
        try {
			
            if ($this->name) {
                // 模块/控制器绑定
                $this->app->route->bind($this->name);
            } elseif ($this->config('app.auto_bind_app') === true) {
                // 入口自动绑定
                $name = pathinfo($request->baseFile(), PATHINFO_FILENAME);
                if ($name && 'index' != $name && is_dir($this->basePath . $name)) {
                    $this->app->route->bind($name);
                }
            }
			
            if (empty(static::$dispatch)) {
                // 路由检测
                static::$dispatch = $this->routeCheck($request);
            }
			
            // 执行路由后置操作
            $this->doRouteAfter($request);
			$request->dispatch(static::$dispatch);
			
            // 记录路由和请求信息
			if ($this->app->isDebug()) {
                $this->log('[ DISPATCH ] ' . var_export(static::$dispatch, true));
				$this->log('[ ROUTE ] ' . var_export($request->routeInfo(), true));
				$this->log('[ HEADER ] ' . var_export($request->header(), true));
                $this->log('[ PARAM ] ' . var_export($request->param(), true));
            }         
			
            $data = $this->exec($request, static::$dispatch);
        } catch (HttpResponseException $exception) {
            $data = $exception->getResponse();
        }
		
        return $this->autoResponse($data);
    }
	
    protected function autoResponse($data) : Response
    {
        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            // 默认自动识别响应输出类型
            $type = $this->request->isAjax() || $this->request->isJson() ? 'json' : 'html';
            $response = Response::create($data, $type);
        } else {
            $data = ob_get_clean();
            $content = false === $data ? '' : $data;
            $status = '' === $content && $this->request->isAjax() || $this->request->isJson() ? 204 : 200;
            $response = Response::create($content, 'html', $status);
        }
		
        return $response;
    }
	
    /**
     * 加载全局中间件
     */
    protected function loadMiddleware() : void
    {
        if (is_file($this->basePath . 'middleware.php')) {
            $this->app->middleware->import(include $this->basePath . 'middleware.php');
        }
    }
	
    /**
     * 加载路由
     * @access protected
     * @return void
     */
    protected function loadRoutes() : void
    {
        // 加载路由定义
        $routePath = $this->getRoutePath();
		
        if (is_dir($routePath)) {
            $files = glob($routePath . '*.php');
			
            // 导入路由配置
            foreach ($files as $file) {
                if (is_file($file)) {
                    $rules = (include $file);
                    is_array($rules) && $this->app->route->import($rules);
                }
            }
        }
		
        $this->app->event->trigger(RouteLoaded::class);
    }
	
    /**
     * Report the exception to the exception handler.
     *
     * @param Throwable $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        $this->app->make(Handle::class)->report($e);
    }
	
    /**
     * Render the exception to a response.
     *
     * @param Request   $request
     * @param Throwable $e
     * @return Response
     */
    protected function renderException($request, Throwable $e)
    {
        return $this->app->make(Handle::class)->render($request, $e);
    }
	
    /**
     * HttpEnd
     * @param Response $response
     * @return void
     */
    public function end(Response $response) : void
    {
        $this->app->event->trigger(HttpEnd::class, $response);
		
        //执行中间件
        $this->app->middleware->end($response);
		
        // 写入日志
        $this->app->log->save();
    }
	
    /***************************扩展方法************************************/
    /**
     * 设置当前请求的调度信息
     * @access public
     * @param array|string  $dispatch 调度信息
     * @param string        $type     调度类型
     * @return void
     */
    public function dispatch($dispatch, $type = 'module')
    {
        self::$dispatch = ['type' => $type, $type => $dispatch];
    }
	
    /**
     * 记录调试信息
     * @access public
     * @param  mixed  $msg  调试信息
     * @param  string $type 信息类型
     * @return void
     */
    public function log($msg, $type = 'info')
    {
        $this->app->isDebug() && $this->app->log->record($msg, $type);
    }
	
    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string    $name 配置参数名（支持二级配置 .号分割）
     * @param mixed       $default 默认值
     * @return mixed
     */
    public function config(string $name = null, $default = null)
    {
        return $this->app->config->get($name, $default);
    }
	
    /**
     * URL路由检测（根据PATH_INFO)
     * @access public
     * @param  \think\Request $request 请求实例
     * @return array
     * @throws \think\Exception
     */
    public function routeCheck(Request $request)
    {
        $path = $this->urlPath($request);
        $depr = $this->config('route.pathinfo_depr');
        $result = false;
		
        // 路由检测
        if ($this->config('app.with_route', true) === true) {
			
            // 开启路由
            if (is_file($this->app->getRuntimePath() . 'route.php')) {
                // 读取路由缓存
                $rules = (include $this->app->getRuntimePath() . 'route.php');
                is_array($rules) && $this->app->route->rules($rules);
            } else {
                // 导入路由配置
                $this->loadRoutes();
            }
			
            // 路由检测（根据路由定义返回不同的URL调度）
            $result = $this->app->route->check($request, $path, $depr, $this->config('route.url_domain_deploy', false));
			
            if ($this->config('route.url_route_must', false) && false === $result) {
                // 路由无效
                throw new RouteNotFoundException();
            }
        }
		
        // 路由无效 解析模块/控制器/操作/参数... 支持控制器自动搜索
        if (false === $result) {
            $result = $this->app->route->parseUrl($path, $depr, $this->config('route.controller_auto_search', false));
        }
		
        return $this->app->middleware->pipeline('route')
		    ->send($request)
			->then(function () use($result) {
                return $result;
            });
    }
	
    /**
     * 获取当前请求URL的pathinfo信息(不含URL后缀)
     * @access protected
     * @param  \think\Request $request 请求实例
     * @return string
     */
    protected function urlPath(Request $request) : string
    {
        $suffix = $this->config('route.url_html_suffix');
        $pathinfo = $request->pathinfo();
		
        if (false === $suffix) {
            // 禁止伪静态访问
            $path = $pathinfo;
        } elseif ($suffix) {
            // 去除正常的URL后缀
            $path = preg_replace('/\\.(' . ltrim($suffix, '.') . ')$/i', '', $pathinfo);
        } else {
            // 允许任何后缀访问
            $path = preg_replace('/\\.' . $request->ext() . '$/i', '', $pathinfo);
        }
		
        return $path;
    }
	
    /**
     * 检查路由后置操作
     * @access protected
     * @param  \think\Request $request 请求实例
     * @return void
     */
    protected function doRouteAfter(Request $request) : void
    {
        $option = $this->app->route->getOption();
        $this->param = $this->app->route->getVars();
		
        // 添加中间件
        if (!empty($option['middleware'])) {
            $this->app->middleware->import($option['middleware'], 'route');
        }
		
        if (!empty($option['append'])) {
            $this->param = array_merge($this->param, $option['append']);
        }
		
        // 绑定模型数据
        if (!empty($option['model'])) {
            $this->createBindModel($option['model'], $this->param);
        }
		
        // 记录当前请求的路由规则
        //$request->setRule($this->rule);
		
        // 记录路由变量
        $request->setRoute($this->param);
		
        // 数据自动验证
        if (isset($option['validate'])) {
            $this->autoValidate($option['validate'], $request);
        }
    }
	
    /**
     * 路由绑定模型实例
     * @access protected
     * @param array $bindModel 绑定模型
     * @param array $matches   路由变量
     * @return void
     */
    protected function createBindModel(array $bindModel, array $matches) : void
    {
        foreach ($bindModel as $key => $val) {
            if ($val instanceof Closure) {
                $result = $this->app->invokeFunction($val, $matches);
            } else {
                $fields = explode('&', $key);
				
                if (is_array($val)) {
                    [$model, $exception] = $val;
                } else {
                    $model = $val;
                    $exception = true;
                }
				
                $where = [];
                $match = true;
                foreach ($fields as $field) {
                    if (!isset($matches[$field])) {
                        $match = false;
                        break;
                    } else {
                        $where[] = [$field, '=', $matches[$field]];
                    }
                }
				
                if ($match) {
                    $result = $model::where($where)->failException($exception)->find();
                }
            }
			
            if (!empty($result)) {
                // 注入容器
                $this->app->instance(get_class($result), $result);
            }
        }
    }
	
    /**
     * 验证数据
     * @access protected
     * @param array $option
     * @param  \think\Request $request 请求实例
     * @return void
     * @throws \think\exception\ValidateException
     */
    protected function autoValidate(array $option, Request $request) : void
    {
        [$validate, $scene, $message, $batch] = $option;
		
        if (is_array($validate)) {
            // 指定验证规则
            $v = new Validate();
            $v->rule($validate);
        } else {
            // 调用验证器
            $class = false !== strpos($validate, '\\') ? $validate : $this->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
		
        /** @var Validate $v */
        $v->message($message)
		    ->batch($batch)
			->failException(true)
			->check($request->param());
    }
	
    public function getDispatch()
    {
        return $this->dispatch;
    }
	
    public function getParam()
    {
        return $this->param;
    }
	
    /**
     * 执行调用分发
     * @access protected
     * @param  \think\Request $request 请求实例
     * @param array $dispatch 调用信息
     * @return Response|mixed
     * @throws \InvalidArgumentException
     */
    protected function exec(Request $request, $dispatch)
    {
        switch ($dispatch['type']) {
            case 'redirect':
                // 重定向跳转
                $data = Response::create($dispatch['url'], 'redirect')->code($dispatch['status']);
                break;
            case 'module':
                // 模块/控制器/操作
                $data = $this->module($request, $dispatch['module'], isset($dispatch['convert']) ? $dispatch['convert'] : null);
                break;
            case 'controller':
                // 执行控制器操作
                $vars = array_merge($request->param(), $dispatch['var']);
                $data = $this->action($dispatch['controller'], $vars, $config['url_controller_layer'], $config['controller_suffix']);
                break;
            case 'method':
                // 回调方法
                $vars = array_merge($request->param(), $dispatch['var']);
                $data = $this->app->invokeMethod($dispatch['method'], $vars);
                break;
            case 'function':
                // 闭包
                $data = $this->app->invokeFunction($dispatch['function']);
                break;
            case 'response':
                // Response 实例
                $data = $dispatch['response'];
                break;
            default:
                throw new \InvalidArgumentException('dispatch type not support');
        }
		
        return $data;
    }
	
    /**
     * 执行模块
     * @access public
     * @param  \think\Request $request 请求实例
     * @param array $result  模块/控制器/操作
     * @param bool  $convert 是否自动转换控制器和操作名
     * @return mixed
     * @throws HttpException
     */
    public function module(Request $request, $result, $convert = null)
    {
        if (!$this->parseMultiApp($request, $result)) {
            return $this->middlewarePipeline($request, $result, $convert);
        }
		
        return $this->app->middleware->pipeline('app')
		    ->send($request)
			->then(function () use($request, $result, $convert) {
                return $this->middlewarePipeline($request, $result, $convert);
            });
    }
	
    public function middlewarePipeline(Request $request, $result, $convert = null)
    {	
        if (is_string($result)) {
            $result = explode('/', $result);
        }
		
        // 设置默认过滤机制
        $request->filter($this->config('app.default_filter'), null);
		
        // 获取控制器名
        $controller = strip_tags($result[1] ?: $this->app->route->config('default_controller'));
		
        if (!preg_match('/^[A-Za-z](\\w|\\.)*$/', $controller)) {
            throw new HttpException(404, 'controller not exists:' . $controller);
        }
		
        if (strpos($controller, '.')) {
            $pos = strrpos($controller, '.');
            $this->controller = substr($controller, 0, $pos) . '.' . Str::studly(substr($controller, $pos + 1));
        } else {
            $this->controller = Str::studly($controller);
        }
		
        // 是否自动转换控制器和操作名
        $convert = is_bool($convert) ? $convert : $this->app->route->config('url_convert');
		
        $this->controller = $convert ? strtolower($this->controller) : $this->controller;
		
        // 获取操作名
        $actionName = strip_tags($result[2] ?: $this->app->route->config('default_action'));
		
        if (!empty($this->app->route->config('action_convert'))) {
            $this->actionName = Str::studly($actionName);
        } else {
            $this->actionName = $convert ? strtolower($actionName) : $actionName;
        }
		
        // 设置当前请求的控制器、操作
        $request->setController($this->controller)->setAction($this->actionName);
		
        try {
            // 实例化控制器
            $suffix = $this->app->route->config('controller_suffix') ? 'Controller' : '';
            $controllerLayer = $this->app->route->config('controller_layer') ?: 'controller';
            $emptyController = $this->app->route->config('empty_controller') ?: 'Error';
			
            $instance = $this->controller($this->controller, $controllerLayer, $suffix, $emptyController);
            // $instance = $this->controller($this->controller);
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }
		
        // 注册控制器中间件
        $this->registerControllerMiddleware($instance);
		
        return $this->app->middleware->pipeline('controller')
		    ->send($this->request)
			->then(function () use($instance) {
                // 获取当前操作名
                $suffix = $this->app->route->config('action_suffix');
                $action = $this->actionName . $suffix;
				
                $vars = [];
                if (is_callable([$instance, $action])) {
					
                    $vars = $this->request->param();
                    // 执行操作方法
                    $call = [$instance, $action];
					
                    try {
                        $reflect = new ReflectionMethod($instance, $action);
                        // 严格获取当前操作方法名
                        $actionName = $reflect->getName();
						
                        if ($suffix) {
                            $actionName = substr($actionName, 0, -strlen($suffix));
                        }
						
                        $this->request->setAction($actionName);
                    } catch (ReflectionException $e) {
						
                        $call = [$instance, '__call'];
						
                        $reflect = new ReflectionMethod($instance, '__call');
						
                        $vars = [$action, $vars];
						
                        $this->request->setAction($action);
                    }
                } else {
                    // 操作不存在
                    throw new HttpException(404, 'method not exists:' . get_class($instance) . '->' . $action . '()');
                }
				
                // 监听ActionBegin
                $this->app->event->trigger('ActionBegin', $call);
				
                return $this->app->invokeReflectMethod($instance, $reflect, $vars);
            });
    }
	
    /**
     * 使用反射机制注册控制器中间件
     * @access public
     * @param object $controller 控制器实例
     * @return void
     */
    protected function registerControllerMiddleware($controller) : void
    {
        $class = new ReflectionClass($controller);
		
        if ($class->hasProperty('middleware')) {
            $reflectionProperty = $class->getProperty('middleware');
            $reflectionProperty->setAccessible(true);
			
            $middlewares = $reflectionProperty->getValue($controller);
			
            foreach ($middlewares as $key => $val) {
                if (!is_int($key)) {
                    if (isset($val['only']) && !in_array($this->request->action(true), array_map(function ($item) {
                        return strtolower($item);
                    }, is_string($val['only']) ? explode(",", $val['only']) : $val['only']))) {
                        continue;
                    } elseif (isset($val['except']) && in_array($this->request->action(true), array_map(function ($item) {
                        return strtolower($item);
                    }, is_string($val['except']) ? explode(',', $val['except']) : $val['except']))) {
                        continue;
                    } else {
                        $val = $key;
                    }
                }
				
                if (is_string($val) && strpos($val, ':')) {
                    $val = explode(':', $val, 2);
                }
				
                $this->app->middleware->controller($val);
            }
        }
    }
	
    /**
     * 实例化访问控制器
     * @access public
     * @param string $name 资源地址
     * @return object
     * @throws ClassNotFoundException
     */
    public function controllerbak(string $name)
    {
        $suffix = $this->app->route->config('controller_suffix') ? 'Controller' : '';
        $controllerLayer = $this->app->route->config('controller_layer') ?: 'controller';
        $emptyController = $this->app->route->config('empty_controller') ?: 'Error';
		
        $class = $this->app->parseClass($controllerLayer, $name . $suffix);
		
        if (class_exists($class)) {
            return $this->app->make($class, [], true);
        } elseif ($emptyController && class_exists($emptyClass = $this->app->parseClass($controllerLayer, $emptyController . $suffix))) {
            return $this->app->make($emptyClass, [], true);
        }
		
        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }
	
    /**
     * 解析模块和类名
     * @access protected
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @return array
     */
    protected function parseModuleAndClass($name, $layer, $appendSuffix)
    {
        if (false !== strpos($name, '\\')) {
            $class = $name;
            $module = $this->name;
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = $this->name;
            }
			
            $class = $this->parseClass($module, $layer, $name, $appendSuffix);
        }
		
        return [$module, $class];
    }
	
    /**
     * 实例化应用类库
     * @access public
     * @param  string $name         类名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    public function create($name, $layer, $appendSuffix = false, $common = 'common')
    {
        $guid = $name . $layer;
		
        if ($this->app->bound($guid)) {
            return $this->app->make($guid, [], true);
        }
		
        list($module, $class) = $this->parseModuleAndClass($name, $layer, $appendSuffix);
		
        if (class_exists($class)) {
            $object = $this->app->make($class, [], true);
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
			
            if (class_exists($class)) {
                $object = $this->app->make($class, [], true);
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }
		
        $this->app->bind($guid, $class);
		
        return $object;
    }
	
    /**
     * 实例化（分层）模型
     * @access public
     * @param  string $name         Model名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return Model
     * @throws ClassNotFoundException
     */
    public function model($name = '', $layer = 'model', $appendSuffix = false, $common = 'common')
    {
        return $this->create($name, $layer, $appendSuffix, $common);
    }
	
    /**
     * 实例化（分层）控制器 格式：[模块名/]控制器名
     * @access public
     * @param  string $name              资源地址
     * @param  string $layer             控制层名称
     * @param  bool   $appendSuffix      是否添加类名后缀
     * @param  string $empty             空控制器名称
     * @return object
     * @throws ClassNotFoundException
     */
    public function controller($name, $layer = 'controller', $appendSuffix = false, $empty = '')
    {
        list($module, $class) = $this->parseModuleAndClass($name, $layer, $appendSuffix);
		
        if (class_exists($class)) {
            return $this->app->make($class, [], true);
        } elseif ($empty && class_exists($emptyClass = $this->parseClass($module, $layer, $empty, $appendSuffix))) {
            return $this->app->make($emptyClass, [], true);
        }
		
        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }
	
    /**
     * 实例化验证类 格式：[模块名/]验证器名
     * @access public
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return Validate
     * @throws ClassNotFoundException
     */
    public function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: $this->config('app.default_validate');
		
        if (empty($name)) {
            return new Validate();
        }
		
        return $this->create($name, $layer, $appendSuffix, $common);
    }
	
    /**
     * 数据库初始化
     * @access public
     * @param  bool|string   $name 连接标识 true 强制重新连接
     * @return \think\db\Query
     */
    public function db($name = false)
    {
        return Db::connect($name);
    }
	
    /**
     * 远程调用模块的操作方法 参数格式 [模块/控制器/]操作
     * @access public
     * @param  string       $url          调用地址
     * @param  string|array $vars         调用参数 支持字符串和数组
     * @param  string       $layer        要调用的控制层名称
     * @param  bool         $appendSuffix 是否添加类名后缀
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        $info = pathinfo($url);
		
        $action = $info['basename'];
		
        $module = '.' != $info['dirname'] ? $info['dirname'] : $this->app->request->controller();
		
        $class = $this->controller($module, $layer, $appendSuffix);
		
        if (is_scalar($vars)) {
            if (strpos($vars, '=')) {
                parse_str($vars, $vars);
            } else {
                $vars = [$vars];
            }
        }
		
        return $this->app->invokeMethod([$class, $action . $this->config('route.action_suffix')], $vars);
    }
	
    /**
     * 解析应用类的类名
     * @access public
     * @param  string $module 模块名
     * @param  string $layer  层名 controller model ...
     * @param  string $name   类名
     * @param  bool   $appendSuffix
     * @return string
     */
    public function parseClass($module, $layer, $name, $appendSuffix = false)
    {
        $name = str_replace(['/', '.'], '\\', $name);
		
        $array = explode('\\', $name);
        $class = Str::studly(array_pop($array)) . ($appendSuffix ? ucfirst($layer) : '');
        $path = $array ? implode('\\', $array) . '\\' : '';
		
        return $this->namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
    }
	
    /**
     * 解析多应用
     * @access protected
     * @param  \think\Request $request 请求实例
     * @param array $result  模块/控制器/操作
     * @return bool
     */
    protected function parseMultiApp(Request $request, $result) : bool
    {
        $multi_app = $this->config('app.multi_app', true);
        if ($multi_app === false) {
            return false;
        }
		
        if (count($result) !== 3) {
            return false;
        }
		
        $scriptName = $this->getScriptName();
        $defaultApp = $this->config('app.default_app') ?: 'index';
        $this->appName = strip_tags(strtolower($result[0] ?: $defaultApp));
        $bind = $this->app->route->getBind('module');
        $available = false;
		
        if ($this->name || $scriptName && !in_array($scriptName, ['index'])) {
            $this->appName = $this->name ?: $scriptName;
            $this->setBind();
            $available = true;
        } elseif ($bind && preg_match('/^[a-z]/is', $bind)) {
            // 绑定应用
            list($bindApp) = explode('/', $bind);
			
            if (empty($result[0])) {
                $this->appName = $bindApp;
            }
			
            $available = true;
        } elseif (!in_array($this->appName, $this->config('app.deny_app_list', [])) && is_dir($this->app->getAppPath() . $this->appName)) {
            $available = true;
        } else {
            // 自动多应用识别
            $this->setBind(false);
            $this->name = null;
            $this->appName = null;
            $bind = null;
            $bind = $this->config('app.domain_bind', []);
			
            if (!empty($bind)) {
                // 获取当前子域名
                $subDomain = $request->subDomain();
                $domain = $request->host(true);
                if (isset($bind[$domain])) {
                    $this->appName = $bind[$domain];
                    $this->setBind();
                    $available = true;
                } elseif (isset($bind[$subDomain])) {
                    $this->appName = $bind[$subDomain];
                    $this->setBind();
                    $available = true;
                } elseif (isset($bind['*'])) {
                    $this->appName = $bind['*'];
                    $this->setBind();
                    $available = true;
                }
            }
			
            if (!$this->isBind()) {
                $path = $request->pathinfo();
                $map = $this->config('app.app_map', []);
                $deny = $this->config('app.deny_app_list', []);
                $name = current(explode('/', $path));
                if (strpos($name, '.')) {
                    $name = strstr($name, '.', true);
                }
				
                if (isset($map[$name])) {
                    if ($map[$name] instanceof Closure) {
                        $result = call_user_func_array($map[$name], [$this->app]);
                        $this->appName = $result ?: $name;
                        $available = true;
                    } else {
                        $this->appName = $map[$name];
                        $available = true;
                    }
                } elseif ($name && (false !== array_search($name, $map) || in_array($name, $deny))) {
                    throw new HttpException(404, 'app not exists:' . $name);
                } elseif ($name && isset($map['*'])) {
                    $this->appName = $map['*'];
                    $available = true;
                } else {
                    $this->appName = $name ?: $defaultApp;
                    $appPath = $this->path ?: $this->basePath . $this->appName . DIRECTORY_SEPARATOR;
					
                    if (!is_dir($appPath)) {
                        $express = $this->config('app.app_express', false);
                        if ($express) {
                            $this->appName = $defaultApp;
                            $available = true;
                        } else {
                            $available = false;
                        }
                    }
                }
				
                if ($name) {
                    $request->setRoot('/' . $name);
                    $request->setPathinfo(strpos($path, '/') ? ltrim(strstr($path, '/'), '/') : '');
                }
            }
        }
		
        // 应用初始化
        if ($this->appName && $available) {
            // 初始化应用
            $this->name = $this->appName;
			
            if (method_exists($request, 'setApp')) {
                $request->setApp($this->appName);
            } else {
                $request->app = $this->appName;
            }
			
            $this->setApp($this->appName);
            return true;
        } else {
            $this->app->config->set(['multi_app' => false], 'app');
            return false;
            //throw new HttpException(404, 'app not exists:' . $this->appName);
        }
    }
	
    /**
     * 获取当前运行入口名称
     * @access protected
     * @codeCoverageIgnore
     * @return string
     */
    protected function getScriptName() : string
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $file = $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($_SERVER['argv'][0])) {
            $file = realpath($_SERVER['argv'][0]);
        }
		
        return isset($file) ? pathinfo($file, PATHINFO_FILENAME) : '';
    }
	
    /**
     * 设置应用
     * @access protected
     * @param string $appName
     * @return void
     */
    protected function setApp(string $appName) : void
    {
        $appPath = $this->path ?: $this->basePath . $appName . DIRECTORY_SEPARATOR;
        $this->app->setAppPath($appPath);
        // 设置应用命名空间
        $this->app->setNamespace($this->config('app.app_namespace') ?: 'app\\' . $appName);
		
        if (is_dir($appPath)) {
            //$this->app->setRuntimePath($this->app->getRuntimePath() . $appName . DIRECTORY_SEPARATOR);
            //$this->setRoutePath($this->routePath . $appName . DIRECTORY_SEPARATOR);
            //加载应用
            $this->loadApp($appName, $appPath);
        }
    }
	
    /**
     * 加载应用文件
     * @access protected
     * @param string $appName 应用名
     * @param string $appPath 应用路径
     * @return void
     */
    protected function loadApp(string $appName, string $appPath) : void
    {
        if (is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
        }
		
        $files = [];
		
        $files = array_merge($files, glob($appPath . 'config' . DIRECTORY_SEPARATOR . '*' . $this->app->getConfigExt()));
		
        foreach ($files as $file) {
            $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
		
        if (is_file($appPath . 'event.php')) {
            $this->app->loadEvent(include $appPath . 'event.php');
        }
		
        if (is_file($appPath . 'middleware.php')) {
            $this->app->middleware->import(include $appPath . 'middleware.php', 'app');
        }
		
        if (is_file($appPath . 'provider.php')) {
            $this->app->bind(include $appPath . 'provider.php');
        }
		
        // 加载应用默认语言包
        $this->app->loadLangPack($this->app->lang->defaultLangSet());
    }
}