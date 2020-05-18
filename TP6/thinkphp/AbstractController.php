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
// [ 系统核心控制器基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace think;

abstract class AbstractController extends AbstractCommon
{
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        // 配置模板引擎参数
        if (in_array($this->params['app_name'], ['home', 'user'])) {
            $view_path = root_path('view') . $this->theme_style;
            config(['view_path' => $view_path], 'template');
        } else {
            if (in_array($this->params['app_name'], array('admin'))) {
                if ('weapp' == strtolower($this->params['controller_name']) && 'execute' == strtolower($this->params['action_name'])) {
                    $view_path = root_path('weapp') . $this->request->param($this->params['weapp_app']) . \DIRECTORY_SEPARATOR . 'template' . \DIRECTORY_SEPARATOR;
                    config(['view_path' => $view_path], 'template');
                }
            }
        }
        $this->view = $this->app->view;
        // 初始化操作
        $this->initialize();
        // 初始化后再实列化模板缓存对象便于在初始化时设置缓存参数
        $this->HtmlCache = new HtmlCache();
        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ? $this->beforeAction($options) : $this->beforeAction($method, $options);
            }
        }
        // 尝试从缓存中读取
        if ($this->request->isGet()) {
            if (false === $this->app->isDebug()) {
                $this->readHtmlCache();
            } else {
                $this->deleteHtmlCache();
            }
        }
    }
    /**
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = []) : void
    {
        if (in_array($this->params['app_name'], array('admin')) && 'Weapp' == $this->params['controller_name'] && 'execute' == $this->params['action_name']) {
            /*插件的前置操作*/
            $m = $this->request->param($this->weapp_app);
            $c = $this->request->param($this->weapp_controller);
            $a = $this->request->param($this->weapp_action);
            if (isset($options['only'])) {
                if (is_string($options['only'])) {
                    $options['only'] = explode(',', $options['only']);
                }
                if (!in_array($a, $options['only'])) {
                    return;
                }
            } elseif (isset($options['except'])) {
                if (is_string($options['except'])) {
                    $options['except'] = explode(',', $options['except']);
                }
                if (in_array($a, $options['except'])) {
                    return;
                }
            }
            call_user_func([$this, $method], $m, $c, $a);
            /*--end*/
        } else {
            if (isset($options['only'])) {
                if (is_string($options['only'])) {
                    $options['only'] = explode(',', $options['only']);
                }
                if (!in_array($this->params['action_name'], $options['only'])) {
                    return;
                }
            } elseif (isset($options['except'])) {
                if (is_string($options['except'])) {
                    $options['except'] = explode(',', $options['except']);
                }
                if (in_array($this->params['action_name'], $options['except'])) {
                    return;
                }
            }
            call_user_func([$this, $method]);
        }
    }
}