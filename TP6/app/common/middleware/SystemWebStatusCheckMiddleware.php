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
// [ 关闭网站 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\middleware;

use Closure;
use think\App;
use think\Response;
class SystemWebStatusCheckMiddleware
{
	use \think\traits\app\ErrorPage;
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
     * 中间件执行入口
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next) 
    {
        $response = $next($request);
        $globalConfig = config('global', []);
        if (isset($globalConfig['web_status']) && $globalConfig['web_status'] === true && !in_array($request->app(), config('app.deny_multi_app_list', []))) {
            $options = array();
            $options['error_message'] = isset($globalConfig['error_message']) ? $globalConfig['error_message'] : '网站暂时关闭，维护中……';
            $options['bar'] = '';
            $options['tips'] = array();
            $response = Response::create($this->setErrorPage($options));
        }
        return $response;
    }
}