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
declare (strict_types=1);
namespace think\traits\app;

trait RunTrait
{
    /**
     * 执行全局容器绑定操作
     * @access protected
     * @return void
     */
    protected function globalProviderInit() : void
    {
        if (is_file($this->appPath . 'provider.php')) {
            $this->bind(include $this->appPath . 'provider.php');
        }
    }
    /**
     * 执行应用容器绑定操作
     * @access public
     * @param string $appName 应用名
     * @return void
     */
    public function appProviderInit(string $appName) : void
    {
        $appPath = $this->getBasePath() . $appName . DS;
        // 针对index.php不能访问的应用调用容器绑定操作
        if (is_file($appPath . 'provider.php') && in_array($appName, $this->config('app.deny_multi_app_list'))) {
            $this->bind(include $appPath . 'provider.php');
        }
    }
    /**
     * 加载语言包
     * @param string $langset 语言
     * @return void
     */
    public function loadLangPack($langset)
    {
        if (empty($langset)) {
            return;
        }
        // 加载系统语言包
        $files = glob($this->appPath . 'lang' . DS . $langset . '.*');
        $this->lang->load($files);
        // 加载扩展（自定义）语言包
        $list = $this->config->get('lang.extend_list', []);
        if (isset($list[$langset])) {
            $this->lang->load($list[$langset]);
        }
    }
    /**
     * 注册服务
     * @access public
     * @param Service|string $service 服务
     * @param bool           $force   强制重新注册
     * @return Service|null
     */
    public function register($service, bool $force = false)
    {
        $registered = $this->getService($service);
        if ($registered && !$force) {
            return $registered;
        }
        if (is_string($service)) {
            $service = new $service($this);
        }
        if (method_exists($service, 'register')) {
            $service->register();
        }
        if (property_exists($service, 'bind')) {
            $this->bind($service->bind);
        }
        $this->services[] = $service;
    }
    /**
     * 执行服务
     * @access public
     * @param Service $service 服务
     * @return mixed
     */
    public function bootService($service)
    {
        if (method_exists($service, 'boot')) {
            return $this->invoke([$service, 'boot']);
        }
    }
    /**
     * 获取服务
     * @param string|Service $service
     * @return Service|null
     */
    public function getService($service)
    {
        $name = is_string($service) ? $service : get_class($service);
        return array_values(array_filter($this->services, function ($value) use($name) {
            return $value instanceof $name;
        }, ARRAY_FILTER_USE_BOTH))[0] ?? null;
    }
    /**
     * 引导应用
     * @access public
     * @return void
     */
    public function boot() : void
    {
        array_walk($this->services, function ($service) {
            $this->bootService($service);
        });
    }
    /**
     * 调试模式设置
     * @access protected
     * @return void
     */
    protected function debugModeInit() : void
    {
        // 应用调试模式
        if (!$this->appDebug) {
            $this->appDebug = $this->env->get('app_debug') ? true : false;
            ini_set('display_errors', 'Off');
        }
        if (!$this->runningInConsole()) {
            //重新申请一块比较大的buffer
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }
    }
    /**
     * 注册应用事件
     * @access protected
     * @param array $event 事件数据
     * @return void
     */
    public function loadEvent(array $event) : void
    {
        if (isset($event['bind'])) {
            $this->event->bind($event['bind']);
        }
        if (isset($event['listen'])) {
            $this->event->listenEvents($event['listen']);
        }
        if (isset($event['subscribe'])) {
            $this->event->subscribe($event['subscribe']);
        }
    }
}