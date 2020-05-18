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
namespace Thinker\app;

use Closure;
use think\App;
use think\exception\HttpException;
use think\Request;
use think\Response;
use think\exception\HttpResponseException;

/**
 * 多应用模式支持
 */
class MultiApp
{
	//use \Thinker\basic\traits\Jump;
    /** @var App */
    protected $app;
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
     * 应用路径
     * @var string
     */
    protected $path;
	
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->name = $this->app->http->getName();
        $this->path = $this->app->http->getPath();
    }
	
    /**
     * 多应用解析
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (!$this->parseMultiApp($request)) {
            return $next($request);
        }
        return $this->app->middleware->pipeline('app')
		    ->send($request)
			->then(function ($request) use($next) {
            return $next($request);
        });
    }
	
    /**
     * 解析多应用
     * @return bool
     */
    protected function parseMultiApp($request) : bool
    {
        $multi_app = $this->app->config->get('app.multi_app', true);
		
        if ($multi_app === false) {
            return false;
        }
		
        // 多应用部署
        $result = $this->dispatchToRoute($request);
		
		// 这行是避免miss路由下路由未定义的情况下count函数报错
		if ($result instanceof Closure) {
			return false;               
        }
		is_object($result) && $result = get_object_vars($result);
		
        if (is_string($result)) {
            $result = explode('/', $result);
        }
		
		if (count($result) !== 3) {
            return false;
        }
		
        $scriptName = $this->getScriptName();
		
        $defaultApp = $this->app->config->get('app.default_app') ?: 'index';
		
        $this->appName = strip_tags(strtolower($result[0] ?: $defaultApp));
		
        $bind = $this->app->route->getBind();
		
        $available = false;
		
        if ($this->name || $scriptName && !in_array($scriptName, ['index'])) {
            $this->appName = $this->name ?: $scriptName;
            $this->app->http->setBind();
            $available = true;
        } elseif ($bind && preg_match('/^[a-z]/is', $bind)) {
            // 绑定应用
            list($bindApp) = explode('/', $bind);
            if (empty($result[0])) {
                $this->appName = $bindApp;
            }
            $available = true;
        } elseif (!in_array($this->appName, $this->app->config->get('app.deny_app_list', [])) && is_dir($this->app->getAppPath() . $this->appName)) {
            $available = true;
        } else {
            // 自动多应用识别
            $this->app->http->setBind(false);
            $this->appName = null;
            $bind = null;
            $bind = $this->app->config->get('app.domain_bind', []);
			
            if (!empty($bind)) {
                // 获取当前子域名
                $subDomain = $request->subDomain();
                $domain = $request->host(true);
                if (isset($bind[$domain])) {
                    $this->appName = $bind[$domain];
                    $this->app->http->setBind();
                    $available = true;
                } elseif (isset($bind[$subDomain])) {
                    $this->appName = $bind[$subDomain];
                    $this->app->http->setBind();
                    $available = true;
                } elseif (isset($bind['*'])) {
                    $this->appName = $bind['*'];
                    $this->app->http->setBind();
                    $available = true;
                }
            }
			
            if (!$this->app->http->isBind()) {
                $path = $request->pathinfo();
                $map = $this->app->config->get('app.app_map', []);
                $deny = $this->app->config->get('app.deny_app_list', []);
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
                    $appPath = $this->path ?: $this->app->getBasePath() . $this->appName . DS;
                    if (!is_dir($appPath)) {
                        $express = $this->app->config->get('app.app_express', false);
                        if ($express) {
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
			//$this->checkAndSetupIsInstalled($this->appName);
            // 初始化应用
            $request->setApp($this->appName);
            $this->app->multiAppInit($this->appName);
            return true;
        } else {
            throw new HttpException(404, 'app not exists:' . $this->appName);
        }
    }
	
    protected function dispatchToRoute($request)
    {
        $withRoute = $this->app->config->get('app.with_route', true) ? function () {
            $this->app->http->loadRoutes();
        } : null;
        return $this->app->route->dispatchForMultiApp($request, $withRoute);
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
     * 检测系统是否安装
     * @access private
	 * @param string $appName
     * @return mixed
     */
    private function checkAndSetupIsInstalled($appName)
    {
        if ($appName != 'install') {
            $install_path = base_path('install');
            $install_extra_path = $install_path . 'extra' . DS;
            if (is_dir($install_path) && !is_file($install_extra_path . "install.lock")) {
                return $this->redirect('install/Index/index');
            }
        }
    }
}