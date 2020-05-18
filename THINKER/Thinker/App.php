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
namespace Thinker;

use think\App as BaseApp;
use think\Cache;
use think\Config;
use think\Console;
use think\Cookie;
use think\Db;
use think\Env;
use think\Event;
use think\Http;
use think\Lang;
use think\Log;
use think\Middleware;
use think\Request;
use think\Response;
use think\Route;
use think\Session;
use think\Validate;
use think\View;
use think\Filesystem;
use think\event\AppInit;
use think\helper\Str;
use think\initializer\BootService;
use think\initializer\Error;
use think\initializer\RegisterService;
use think\exception\FileException;
use think\exception\HttpResponseException;

/**
 * App 基础类，由于在think\App的基础上进行了些许修改，需要清空think\App的所有属性和方法
 * @property Route      $route
 * @property Config     $config
 * @property Cache      $cache
 * @property Request    $request
 * @property Http       $http
 * @property Console    $console
 * @property Env        $env
 * @property Event      $event
 * @property Middleware $middleware
 * @property Log        $log
 * @property Lang       $lang
 * @property Db         $db
 * @property Cookie     $cookie
 * @property Session    $session
 * @property Validate   $validate
 * @property Filesystem $filesystem
 */
class App extends BaseApp
{
	use traits\app\SettingTrait;
	use traits\app\Loader;
    use traits\app\RunTrait;
	use traits\app\AppTrait;
    use traits\app\WeAppTrait;
	use traits\app\ErrorPage;
		
	const VERSION = '6.0.2';
	
    /**
     * 应用调试模式
     * @var bool
     */
    protected $appDebug = false;

    /**
     * 应用开始时间
     * @var float
     */
    protected $beginTime;

    /**
     * 应用内存初始占用
     * @var integer
     */
    protected $beginMem;

    /**
     * 当前应用类库命名空间
     * @var string
     */
    protected $namespace = 'app';

    /**
     * 应用根目录
     * @var string
     */
    protected $rootPath = '';

    /**
     * 框架目录
     * @var string
     */
    protected $thinkPath = '';

    /**
     * 应用目录
     * @var string
     */
    protected $appPath = '';

    /**
     * Runtime目录
     * @var string
     */
    protected $runtimePath = '';

    /**
     * 路由定义目录
     * @var string
     */
    protected $routePath = '';

    /**
     * 配置后缀
     * @var string
     */
    protected $configExt = '.php';

    /**
     * 应用初始化器
     * @var array
     */
    protected $initializers = [
        Error::class,
        RegisterService::class,
        BootService::class,
    ];

    /**
     * 注册的系统服务
     * @var array
     */
    protected $services = [];

    /**
     * 初始化
     * @var bool
     */
    protected $initialized = false;

    /**
     * 容器绑定标识
     * @var array
     */
    protected $bind = [
        'app'                     => BaseApp::class,
        'cache'                   => Cache::class,
        'config'                  => Config::class,
        'console'                 => Console::class,
        'cookie'                  => Cookie::class,
        'db'                      => Db::class,
        'env'                     => Env::class,
        'event'                   => Event::class,
        'http'                    => Http::class,
        'lang'                    => Lang::class,
        'log'                     => Log::class,
        'middleware'              => Middleware::class,
        'request'                 => Request::class,
        'response'                => Response::class,
        'route'                   => Route::class,
        'session'                 => Session::class,
        'validate'                => Validate::class,
        'view'                    => View::class,
        'filesystem'              => Filesystem::class,
        'think\DbManager'         => Db::class,
        'think\LogManager'        => Log::class,
        'think\CacheManager'      => Cache::class,

        // 接口依赖注入
        'Psr\Log\LoggerInterface' => Log::class,
    ];

	/**
     * 引导期间要加载的环境文件。
     *
     * @var string
     */
    protected $environmentFile = '.env';
	
	 /**
     * @var array 额外加载文件
     */
    protected $file = [];
	
	/**
     * 插件的命名空间
     *
     * @var string
     */
    protected $weappnamespace = 'weapp';
	
    /**
     * 架构方法
     * @access public
     * @param string $rootPath 应用根目录
     */
    public function __construct(string $rootPath = null)
    {
        $this->thinkPath = realpath(dirname(dirname(__FILE__))) . DS . 'vendor' . DS . 'topthink' . DS . 'framework' . DS . 'src' . DS;
        $this->rootPath = $rootPath ? rtrim($rootPath, DS) . DS : $this->getDefaultRootPath();
        $this->appPath = $this->rootPath . 'app' . DS;
        $this->runtimePath = $this->rootPath . 'runtime' . DS;
        $this->routePath = $this->rootPath . 'route' . DS;
        $this->globalProviderInit();
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance('think\\Container', $this);
    }
	
	/**
     * 定义常量
     * @access protected
     * @global $_M 特殊全局变量
     * @return void
     */
    protected function toDefined() : void
    {
        global $_M;
		// PHP版本号
		define('PHP_VERSION', $this->getPHPVersion());
		// 操作系统
		define('PHP_SYSTEM', $this->getSystem());
		// 服务器版本
        define('PHP_SERVER', $this->getWebServer());
		// 是否为64位操作系统
        define('IS_X64', $this->runningInX64());
		// 安全key
		defined('UC_KEY') or define('UC_KEY', $_M['uc_key'] = $this->request->secureKey());
		// 请求协议
		defined('HTTP_SCHEME') or define('HTTP_SCHEME', $this->request->isSsl() ? 'https://' : 'http://');
		// 当前访问的主机名
        defined('HTTP_HOST') or define('HTTP_HOST', $this->request->host(true));
        // 来源页面
        defined('REQUEST_URI') or define('REQUEST_URI', $this->getRequestUri());
        // 子目录
        defined('ROOT_DIR') or define('ROOT_DIR', $_M['root_dir'] = $this->request->rootUrl());
		// Session保存路径
        $sessSavePath = $this->getRuntimePath() . "sessions_{$_M['session_key']}" . DS;
        defined('SESSION_PATH') or define('SESSION_PATH', $sessSavePath);
        $this->config->set(['path' => $sessSavePath], 'session');
		// 支付宝 - 存放日志，AOP缓存数据目录
        defined('AOP_SDK_WORK_DIR') or define('AOP_SDK_WORK_DIR', $this->getRuntimePath());
		
    }
	
	/**
     * 获取请求来源URL.
     * @access public
     * @return string 返回URL
     */
    public function getRequestUri() : string
    {
        if ($this->request->server('HTTP_X_ORIGINAL_URL')) {
            $url = $this->request->server('HTTP_X_ORIGINAL_URL');
        } elseif ($this->request->server('HTTP_X_REWRITE_URL')) {
            $url = $this->request->server('HTTP_X_REWRITE_URL');
        } elseif ($this->request->server('REQUEST_URI')) {
            $url = $this->request->server('REQUEST_URI');
        } elseif ($this->request->server('REDIRECT_URL')) {
            $url = $this->request->server('REDIRECT_URL');
            if ($this->request->server('REDIRECT_QUERY_STRIN')) {
                $url .= '?' . $this->request->server('REDIRECT_QUERY_STRIN');
            }
        } else {
            $url = htmlentities($this->request->server('PHP_SELF')) . ($this->request->server('QUERY_STRING') ? '?' . htmlentities($this->request->server('QUERY_STRING')) : '');
        }

        return $url;
    }
	
	/**
     * 获取PHP Version.
     * @access public
     * @return string
     */
    public function getPHPVersion() : string
    {
        $p = phpversion();
        if (strpos($p, '-') !== false) {
            $p = substr($p, 0, strpos($p, '-'));
        }

        return $p;
    }
	
	/**
     * 获取服务器.
     * @access public
     * @return string
     */
    public function getWebServer() : string
    {
        $webServer = strtolower($this->request->server('SERVER_SOFTWARE'));
        if (strpos($webServer, 'apache') !== false) {
            return 'APACHE';
        } elseif (strpos($webServer, 'microsoft-iis') !== false) {
            return 'IIS';
        } elseif (strpos($webServer, 'nginx') !== false) {
            return 'NGINX';
        } elseif (strpos($webServer, 'lighttpd') !== false) {
            return 'LIGHTTPD';
        } elseif (strpos($webServer, 'kangle') !== false) {
            return 'KANGLE';
        } elseif (strpos($webServer, 'caddy') !== false) {
            return 'CADDY';
        } elseif (strpos($webServer, 'development server') !== false) {
            return 'BUILTIN';
        } else {
            return 'UNKNOWN';
        }
    }

	/**
     * 获取操作系统
     * @access public
     * @return string
     */
    public function getSystem() : string
    {
        if (in_array(strtoupper(PHP_OS), array('WINNT', 'WIN32', 'WINDOWS'))) {
            return 'WINDOWS';
        } elseif ((strtoupper(PHP_OS) === 'UNIX')) {
            return 'UNIX';
        } elseif (strtoupper(PHP_OS) === 'LINUX') {
            return 'LINUX';
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            return 'DARWIN';
        } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'CYGWIN') {
            return 'CYGWIN';
        } elseif (in_array(strtoupper(PHP_OS), array('NETBSD', 'OPENBSD', 'FREEBSD'))) {
            return 'BSD';
        } else {
            return 'UNKNOWN';
        }
    }
	
    /**
     * 是否初始化过
	 * @access public
     * @return bool
     */
    public function initialized() : bool
    {
        return $this->initialized;
    }
	
	/**
     * 加载环境变量
	 * @access public
     * @return void
     */
    public function environmentFileinit() : void
    {
        if (!is_file($this->rootPath . $this->environmentFile)) {
            $example = $this->rootPath . '.example' . $this->environmentFile;
            if (!is_file($example)) {
                throw new FileException(sprintf('The file "%s" does not exist', $example));
            }
            set_error_handler(function ($type, $msg) use(&$error) {
                $error = $msg;
            });
            $renamed = copy($example, $this->rootPath . $this->environmentFile);
            restore_error_handler();
            if (!$renamed) {
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $example, $this->rootPath . $this->environmentFile, strip_tags($error)));
            }
            @chmod((string) $this->rootPath . $this->environmentFile, 0755 & ~umask());
        }
    }
	
    /**
     * 初始化应用
     * @access public
     * @global $_M 特殊全局变量
     * @return $this
     */
    public function initialize()
    {
        global $_M;
		
        $this->initialized = true;
        $this->beginTime = microtime(true);
        $this->beginMem = memory_get_usage();
					
        // 加载环境变量
		
        if (is_file($this->rootPath . $this->environmentFile)) {
            $this->env->load($this->rootPath . $this->environmentFile);
        }
		
        $this->configExt = $this->env->get('config_ext', '.php');
		
		// 调试模式设置
        $this->debugModeInit();
		
        // 加载全局初始化文件
        $this->loadAppInit();
		
        // 注册应用命名空间
        $this->namespace = $this->env->get('app_namespace', $this->config('app.app_namespace') ?: $this->namespace);
        $this->setNamespace($this->namespace);
		
        // 加载框架默认语言包
        $langSet = $this->lang->defaultLangSet();
        $this->lang->load($this->thinkPath . 'lang' . DS . $langSet . '.php');
		
        // 加载应用默认语言包
        $this->loadLangPack($langSet);
		
        // 自动设置应用模式
        $multi_app = $this->config('app.multi_app', true);
        is_dir($this->getBasePath() . 'controller') && ($multi_app = false);
        $this->config->set(['multi_app' => $multi_app], 'app');
		
        // 设置session前缀
        $key  = $_M['session_key'] = substr(md5(substr($this->request->secureKey(), 0, 5)), 0, 10);
        $this->config->set(['prefix' => $key], 'session');
		
		// 定义常量
        $this->toDefined();
		
        // 监听AppInit
        $this->event->trigger(AppInit::class);
		
        date_default_timezone_set($this->config->get('app.default_timezone', 'Asia/Shanghai'));
		
        // 初始化
        foreach ($this->initializers as $initializer) {
            $this->make($initializer)->init($this);
        }
		
        return $this;
    }
	
    /**
     * 加载全局应用文件和配置
     * @access protected
     * @global $_M 特殊全局变量
     * @return void
     */
    protected function loadAppInit() : void
    {
        global $_M;
        $incPath = $this->getIncPath();
        $incFunctionPath = $incPath . 'function' . DS;
        $incThinkPath = $incPath . 'think' . DS;
        // 载入小助手配置,并对其进行默认初始化
        if (empty($_M['__helper']['HelperIsLoaded']) and file_exists($incPath . 'inc' . DS . 'helper.inc.php')) {
            if (empty($_M['__require_once_file']['helper.inc.php'])) {
                require_once $incPath . 'inc' . DS . 'helper.inc.php';
                $_M['__require_once_file']['helper.inc.php'] = true;
            }
            // 若没有载入配置,则初始化一个默认小助手配置
            if (!isset($cfg_helper_autoload)) {
                $cfg_helper_autoload = array('admin', 'class', 'common', 'debug', 'filter', 'request', 'response', 'time');
            }
            // 初始化小助手
            $_M['__helper']['HelperIsLoaded'] = $this->helper($cfg_helper_autoload);
        }
        if (empty($_M['__loadfiles']['incFunctionPath']) and is_dir($incFunctionPath)) {
            $_M['__loadfiles']['incFunctionPath'] = $this->loadfiles($incFunctionPath);
        }
        if (empty($_M['__loadfiles']['incThinkPath']) and is_dir($incThinkPath)) {
            $_M['__loadfiles']['incThinkPath'] = $this->loadfiles($incThinkPath);
        }
        $this->loadAppInitFiles();
    }
	
    /**
     * 加载全局应用文件和配置
     * @access protected
     * @global $_M 特殊全局变量
     * @return void
     */
    protected function loadAppInitFiles() : void
    {
        global $_M;
        $BasePath = $this->getBasePath();
        // 加载全局公用文件
        if (empty($_M['__include_once_file'][$BasePath . 'common.php']) and is_file($BasePath . 'common.php')) {
            include_once $BasePath . 'common.php';
            $_M['__include_once_file'][$BasePath . 'common.php'] = true;
        }
        // 如果助手文件载入失败就载入框架默认助手文件
        if (empty($_M['__helper']['HelperIsLoaded'])) {
            $ThinkHelper = $this->thinkPath . 'helper.php';
            if (!is_file($ThinkHelper)) {
                throw new FileException(sprintf('The file "%s" does not exist', $ThinkHelper));
            }
            include_once $ThinkHelper;
            $_M['__helper']['HelperIsLoaded'] = true;
        }
        // 加载全局配置
        $configPath = $this->getConfigPath();
        $files = [];
        if (is_dir($configPath)) {
            $files = glob($configPath . '*' . $this->configExt);
        }
        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
        // 读取全局扩展配置文件
        $this->loadExtrapath($this->rootPath . 'extra' . DS);
        // 注册全局应用事件
        if (is_file($BasePath . 'event.php')) {
            $this->loadEvent(include $BasePath . 'event.php');
        }
        // 加载全局中间件 全局中间件改为在Web应用管理类中执行加载操作
        /*if (is_file($BasePath . 'middleware.php')) {
              $this->middleware->import(include $BasePath . 'middleware.php');
          }*/
        // 注册全局系统服务
        if (is_file($BasePath . 'service.php')) {
            $services = (include $BasePath . 'service.php');
            foreach ($services as $service) {
                $this->register($service);
            }
        }
        // 加载额外文件
        $extra_file_list = $this->config->get('app.extra_file_list', []);
        if (!empty($extra_file_list)) {
            foreach ($extra_file_list as $file) {
                $file = strpos($file, '.') ? $file : $BasePath . $file . '.php';
                if (is_file($file) && !isset($this->file[$file])) {
                    include $file;
                    $this->file[$file] = true;
                }
            }
        }
    }
	
	/**
     * 读取扩展配置文件
     * @access public
     * @param string $extrapath
     * @return void
     */
    public function loadExtrapath(string $extrapath) : void
    {
		/*try {
            if (is_dir($extrapath)) {
                $files = [];
                $files = array_merge($files, glob($extrapath . '*' . $this->configExt));
                foreach ($files as $file) {
                    $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
                }
            }
        } catch (\PDOException $e) {
			// TDO CODE
        } catch (\Exception $e) {
            throw $e;
        }*/
    }
	
    /**
     * 设置应用（多应用模式）
     * @access public
     * @param string $appName
     * @return void
     */
    public function multiAppInit(string $appName) : void
    {
        // 应用容器绑定
        $this->appProviderInit($appName);
        $appPath = $this->http->getPath() ?: $this->getBasePath() . $appName . DS;
        $this->http->setName($appName);
        $this->setAppPath($appPath);
        if (is_dir($appPath)) {
            $this->setRuntimePath($this->getRuntimePath());
            $this->setRoutePath($this->getRoutePath());
            // 加载应用
            $this->loadMultiAppInitFiles($appName, $appPath);
        }
    }
	
    /**
     * 加载应用文件和配置（多应用模式）
     * @access protected
     * @global $_M 特殊全局变量
     * @param string $appName 应用名
     * @param string $appPath 应用路径
     * @return void
     */
    protected function loadMultiAppInitFiles(string $appName, string $appPath) : void
    {
        global $_M;
        // 加载应用公用文件
        if (empty($_M['__include_once_file'][$appPath . 'common.php']) and is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
            $_M['__include_once_file'][$appPath . 'common.php'] = true;
        }
        // 加载应用配置
        $files = [];
        $files = array_merge($files, glob($appPath . 'config' . DS . '*' . $this->configExt));
        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
        // 读取应用扩展配置文件
        $this->loadExtrapath($this->rootPath . 'extra' . DS . $appName . DS);
        // 注册应用事件
        if (is_file($appPath . 'event.php')) {
            $this->loadEvent(include $appPath . 'event.php');
        }
        // 加载应用中间件
        if (is_file($appPath . 'middleware.php')) {
            $this->middleware->import(include $appPath . 'middleware.php', 'app');
        }
		
		// 注册应用系统服务
        if (is_file($appPath . 'service.php')) {
            $services = (include $appPath . 'service.php');
            foreach ($services as $service) {
                $this->register($service);
            }
        }
		
        // 加载应用默认语言包
        $this->loadLangPack($this->lang->defaultLangSet());
    }
	
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param  string  $name 字符串
     * @param  int $type 转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public function parseName(string $name = null, int $type = 0, bool $ucfirst = true) : string
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