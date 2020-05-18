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
// [ 系统插件核心控制器基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace think;

use think\template\exception\TemplateNotFoundException;
abstract class AbstractWeappController extends AbstractCommon
{
    use \think\traits\controller\WeappTrait;
    /**
     * 当前模板路径
     * @var string
     */
    private $view_path;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        // 配置模板引擎参数
        $this->view_path = $this->weapp_path . 'template' . \DIRECTORY_SEPARATOR;
        config(['view_path' => $this->view_path], 'template');
        $this->view = $this->app->view;
        if (!is_file($this->config_file)) {
            throw new \Exception("Plug in configuration file missing");
        }
        // 验证插件的配置完整性
        $this->checkConfig();
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
        if ('Weapp' == $this->params['controller_name'] && 'execute' == $this->params['action_name']) {
            /*插件的前置操作*/
            $m = $this->weapp_app_name;
            $c = $this->weapp_controller_name;
            $a = $this->weapp_action_name;
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
    /**
     * 加载模板输出
     * @access protected
     * @param  string $template
     * @param  array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     * @throws TemplateNotFoundException
     * @throws \think\Exception
     */
    protected function fetch($template = '', $vars = [])
    {
        if (empty($template)) {
            $template = $this->weapp_action_name;
        }
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            $template = str_replace('\\', '/', $template);
            $arr = explode('/', $template);
            if (1 == count($arr)) {
                $template = $this->view_path . $arr[0];
            } elseif (2 == count($arr)) {
                $template = $this->view_path . $arr[0] . \DIRECTORY_SEPARATOR . $arr[1];
            } elseif (3 == count($arr)) {
                $this->view_path = str_replace('/' . $this->weapp_app_name . '/template/', '/' . $arr[0] . '/template/', $this->view_path);
                $template = $this->view_path . $arr[1] . \DIRECTORY_SEPARATOR . $arr[2];
            } else {
                $template = $this->view_path . $arr[count($arr) - 1];
            }
            $template = $template . '.' . $this->view_suffix;
        }
        if (!$this->exists($template)) {
            throw new TemplateNotFoundException(lang('template not exists') . ':' . $template, $template);
        }
        // 使用视图输出过滤
        return $this->sendData($this->view->filter(function ($content) {
            return str_replace("__WEAPP_TEMPLATE__", $this->web_root . '/weapp/' . $this->weapp_app_name . '/template', $content);
        })->fetch($template, $vars));
    }
    /**
     * 渲染内容输出
     * @access protected
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @return mixed
     */
    protected function display($content = '', $vars = [])
    {
        return $this->sendData($this->view->filter(function ($content) {
            return str_replace("__WEAPP_TEMPLATE__", $this->web_root . '/weapp/' . $this->weapp_app_name . '/template', $content);
        })->display($content, $vars));
    }
}