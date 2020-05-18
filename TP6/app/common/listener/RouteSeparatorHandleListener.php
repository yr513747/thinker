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
        $this->resolveRouteConfiguration();
    }
    /**
     * 解析路由配置
     * @access protected
     * @return void
     * @throws HttpException
     */
    protected function resolveRouteConfiguration()
    {
        // 全局配置
        $globalTpCache = $this->app->config->get('tpcache', []);
        // URL模式
        $seo_pseudo = !empty($globalTpCache['seo_pseudo']) ? intval($globalTpCache['seo_pseudo']) : $this->app->config->get('thinker.seo_pseudo', 1);
        $this->app->config->set(['seo_pseudo' => $seo_pseudo], 'thinker');
        // 是否https链接
        $is_https = !empty($globalTpCache['web_is_https']) ? true : $this->app->config->get('route.is_https', false);
        $this->app->config->set(['is_https' => $is_https], 'route');
        // 关闭网站
        $web_status = !empty($globalTpCache['web_status']) ? true : $this->app->config->get('global.web_status', false);
        $this->app->config->set(['web_status' => $web_status], 'global');
        // 是否启用绝对网址
        $cfg_multi_site = !empty($globalTpCache['cfg_multi_site']) ? true : $this->app->config->get('route.cfg_multi_site', false);
        $this->app->config->set(['cfg_multi_site' => $cfg_multi_site], 'route');
        // 是否隐藏入口文件
        $seo_inlet = !empty($globalTpCache['seo_inlet']) ? $globalTpCache['seo_inlet'] : $this->app->config->get('thinker.seo_inlet', 0);
        $this->app->config->set(['seo_inlet' => $seo_inlet], 'thinker');
        // 路由规则处理
        $result = $this->routingRuleProcessing(['seo_pseudo' => $seo_pseudo]);
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
    protected function routingRuleProcessing(array $params = []) : array
    {
        if (isset($params['seo_pseudo'])) {
            $route_Path = $this->getRoutePath();
            $route_config = $route_Path . 'route.config';
            $route_file = $route_Path . 'route.php';
            // 文件目录
            if (!is_writable($route_Path)) {
                return $this->returnMessage("路由目录没有操作权限[{$route_Path}]", -11);
            }
            // 路配置文件权限
            if (is_file($route_file) && !is_writable($route_file)) {
                return $this->returnMessage("路由配置文件没有操作权限[{$route_file}]", -11);
            }
            // pathinfo+短地址模式
            if ($params['seo_pseudo'] == 2) {
                if (!is_file($route_file)) {
                    if (!is_file($route_config)) {
                        return $this->returnMessage("路由规则文件不存在[{$route_config}]", -14);
                    }
                    // 开始生成规则文件
                    if (file_put_contents($route_file, file_get_contents($route_config)) === false) {
                        return $this->returnMessage('路由规则文件生成失败', -10);
                    }
                }
				return $this->returnMessage('无需处理', 0);
            } else {
				// 兼容模式+pathinfo模式
                if (is_file($route_file) && @unlink($route_file) === false) {
                    return $this->returnMessage('路由规则处理失败', -10);
                }
                return $this->returnMessage('无需处理', 0);
            }
        }
        return $this->returnMessage('无需处理', 0);
    }
    /**
     * 获取路由目录
     * @access protected
     * @return string
     */
    protected function getRoutePath() : string
    {
        return $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR;
    }
    /**
     * 返回消息
     * @access protected
     * @param  string $msg
     * @param  int $code
     * @return array
     */
    protected function returnMessage(string $msg, int $code) : array
    {
        return ['msg' => $msg, 'code' => $code];
    }
}