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
// [ 系统服务基础类 ]
// --------------------------------------------------------------------------
declare (strict_types = 1);
namespace Thinker\serviceinit;

use Closure;
use think\App;
use think\Console;
use think\event\RouteLoaded;
/**
 * @method void register()
 * @method void boot()
 */
abstract class BaseService 
{
	/**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
	
	/**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 加载路由
     * @access protected
     * @param string $path 路由路径
     */
    protected function loadRoutesFrom($path)
    {
        $this->registerRoutes(function () use ($path) {
            include $path;
        });
    }

    /**
     * 注册路由
     * @param Closure $closure
     */
    protected function registerRoutes(Closure $closure)
    {
        $this->app->event->listen(RouteLoaded::class, $closure);
    }

    /**
     * 添加指令
     * @access protected
     * @param array|string $commands 指令
     */
    protected function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Console::starting(function (Console $console) use ($commands) {
            $console->addCommands($commands);
        });
    }
}