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
// [ 路由规则处理 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\listener;

use think\App;
use think\exception\HttpException;
class RouteSeparatorHandleListener
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
     * 事件执行入口
     * @access public
     * @return void
     */
    public function handle()
    {
        $this->RouteSetup();
    }
    /**
     * 解析路由配置
     * @access protected
     * @return void
     * @throws HttpException
     */
    protected function RouteSetup()
    {
        // 全局配置
        $globalTpCache = config('tpcache');
        // URL模式
        $seo_pseudo = !empty($globalTpCache['seo_pseudo']) ? intval($globalTpCache['seo_pseudo']) : config('thinker.seo_pseudo', 1);
        config(['seo_pseudo' => $seo_pseudo], 'thinker');
        // 是否https链接
        $is_https = !empty($globalTpCache['web_is_https']) ? true : config('route.is_https', false);
        config(['is_https' => $is_https], 'route');
        // 关闭网站
        $web_status = !empty($globalTpCache['web_status']) ? true : config('global.web_status', false);
        config(['web_status' => $web_status], 'global');
        // 是否启用绝对网址
        $cfg_multi_site = !empty($globalTpCache['cfg_multi_site']) ? true : config('route.cfg_multi_site', false);
        config(['cfg_multi_site' => $cfg_multi_site], 'route');
        // 是否隐藏入口文件
        $seo_inlet = !empty($globalTpCache['seo_inlet']) ? $globalTpCache['seo_inlet'] : config('thinker.seo_inlet', 0);
        config(['seo_inlet' => $seo_inlet], 'thinker');
        // 路由规则处理
        $result = $this->RouteSeparatorHandle(['seo_pseudo' => $seo_pseudo]);
        if ($result['code'] !== 0) {
            throw new HttpException(404, $result['msg']);
        }
    }
    /**
     * 路由规则处理
     * @access protected
     * @param array $params 
     * @return array 
     */
    protected function RouteSeparatorHandle(array $params = []) : array
    {
        if (isset($params['seo_pseudo'])) {
            $route_file = $this->app->getRoutePath() . 'route.config';
            $route_file_php = $this->app->getRoutePath() . 'route.php';
            // 文件目录
            if (!@is_writable($this->app->getRoutePath())) {
                return $result = ['msg' => '路由目录没有操作权限' . '[./route]', 'code' => -11];
            }
            // 路配置文件权限
            if (@is_file($route_file_php) && !@is_writable($route_file_php)) {
                return $result = ['msg' => '路由配置文件没有操作权限' . '[./route/route.php]', 'code' => -11];
            }
            // pathinfo+短地址模式
            if ($params['seo_pseudo'] == 2) {
                if (!@is_file($route_file)) {
                    return $result = ['msg' => '路由规则文件不存在' . '[./route/route.config]', 'code' => -14];
                }
                if (@is_file($route_file_php)) {
                    return $result = ['msg' => '无需处理', 'code' => 0];
                }
                // 开始生成规则文件
                if (@file_put_contents($route_file_php, @file_get_contents($route_file)) === false) {
                    return $result = ['msg' => '路由规则文件生成失败', 'code' => -10];
                }
                // 兼容模式+pathinfo模式
            } else {
                if (!@is_file($route_file_php)) {
                    return $result = ['msg' => '无需处理', 'code' => 0];
                }
                if (@is_file($route_file_php) && @unlink($route_file_php) === false) {
                    return $result = ['msg' => '路由规则处理失败', 'code' => -10];
                }
            }
            return $result = ['msg' => '处理成功', 'code' => 0];
        }
        return $result = ['msg' => '无需处理', 'code' => 0];
    }
}