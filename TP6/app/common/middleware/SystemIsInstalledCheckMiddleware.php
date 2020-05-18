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
// [ 检测系统是否安装 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\middleware;

use Closure;
use think\App;
use think\Request;
class SystemIsInstalledCheckMiddleware
{
    /** @var App */
    protected $app;
    public function __construct(App $app)
    {
        $this->app = $app;
    }
    /**
     * 中间件执行入口
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next) 
    {
        $response = $next($request);
        if (!$this->checkSystemIsInstalled($request)) {
            $response = redirect('install/Index/index');
        }
        return $response;
    }
    /**
     * 检测系统是否安装
     * @access protected
     * @param Request $request
     * @return bool
     */
    protected function checkSystemIsInstalled(Request $request) : bool
    {
        if ($request->app() != 'install') {
            $install_path = base_path('install');
            $install_data_path = $install_path . 'data' . DS;
            if (is_dir($install_path) && !is_file($install_data_path . "install.lock")) {
                return false;
            }
        }
        return true;
    }
}