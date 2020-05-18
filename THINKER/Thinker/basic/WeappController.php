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
namespace Thinker\basic;

use think\App;
use think\Container;
use Thinker\template\exception\TemplateNotFoundException;
use Thinker\exceptions\AuthException;
use think\facade\Db;
/**
 * 插件控制器基础类
 */
abstract class WeappController extends Common
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
	
	/**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];
    /**
     * 当前子插件根目录
     * @var string 
     */
    protected $weapp_path = '';
    /**
     * 当前子插件配置文件路径
     * @var string 
     */
    protected $config_file = '';
    /**
     * 当前子插件应用分组
     * @var string 
     */
    protected $weapp_app_name = '';
    /**
     * 当前子插件控制器
     * @var string 
     */
    protected $weapp_controller_name = '';
    /**
     * 当前子插件操作名
     * @var string 
     */
    protected $weapp_action_name = '';
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? Container::pull('app') : $app;
        parent::__construct($this->app);
		// 返回对象的类名
        $class = get_class($this);
        $wmcArr = explode('\\', $class);
        // 当前插件应用名称
        $this->params['weapp_app_name'] = $this->weapp_app_name = isset($this->data[$this->weapp_app]) ? $this->data[$this->weapp_app] : $wmcArr[1];
        // 当前插件控制器名称
        $this->params['weapp_controller_name'] = $this->weapp_controller_name = isset($this->data[$this->weapp_controller]) ? $this->data[$this->weapp_controller] : $wmcArr[3];
        // 当前插件操作名称
        $this->params['weapp_action_name'] = $this->weapp_action_name = isset($this->data[$this->weapp_action]) ? $this->data[$this->weapp_action] : 'index';
        // 设置当前模板路径
        $view_path = root_path('weapp') . $this->weapp_app_name . DS . 'template' . DS;
        config(['view_path' => $view_path], 'template');
        // 设置当前模板解析的引擎
        $template = config('template', []);
        $this->engine($template);
		
        $this->weapp_path = root_path('weapp') . $this->weapp_app_name . DS;
        if (!is_file($this->weapp_path . 'config.php')) {
            throw new AuthException("Plug in configuration file missing");
        }
        $this->config_file = $this->weapp_path . 'config.php';
        // 验证插件的配置完整性
        $this->checkConfig();
		// 控制器初始化
        $this->initialize();
		// 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                $this->beforeAction($options) :
                $this->beforeAction($method, $options);
            }
        }
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
    }
	/**
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = [])
    {
        if ('Weapp' == $this->params['controller_name'] && 'execute' == $this->params['action_name']) {
            /*插件的前置操作*/
			$m = isset($this->data[$this->weapp_app_name]) ? $this->data[$this->weapp_app_name] : null;
            $c = isset($this->data[$this->weapp_controller_name]) ? $this->data[$this->weapp_controller_name] : null;
            $a = isset($this->data[$this->weapp_action_name]) ? $this->data[$this->weapp_action_name] : null;
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
     * @access public
     * @param string $template
     * @param array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     * @throws \think\Exception
     */
    public function fetch($template = '', $vars = [])
    {
        $view_path = config('template.view_path');
        if (empty($template)) {
            $template = $this->weapp_action_name;
        }
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            $template = str_replace('\\', '/', $template);
            $arr = explode('/', $template);
            if (1 == count($arr)) {
                $template = $view_path . $arr[0];
            } else {
                if (2 == count($arr)) {
                    $template = $view_path . $arr[0] . DS . $arr[1];
                } else {
                    if (3 == count($arr)) {
                        $view_path = str_replace('/' . $this->weapp_app_name . '/template/', '/' . $arr[0] . '/template/', $view_path);
                        $template = $view_path . $arr[1] . DS . $arr[2];
                    } else {
                        $template = $view_path . $arr[count($arr) - 1];
                    }
                }
            }
            $template = $template . '.' . config('template.view_suffix');
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
     * 验证插件的配置完整性
     * @return string
     * @throws Exception
     */
    public final function checkConfig()
    {
        $config_check_keys = array('code', 'name', 'description', 'scene', 'author', 'version', 'min_version');
        $config = (include $this->config_file);
        foreach ($config_check_keys as $value) {
            if (!array_key_exists($value, $config)) {
                throw new AuthException("The plug-in configuration file config.php does not conform to the official specification, and the {$value} array element is missing");
            }
        }
        return true;
    }
    /**
     * 获取插件信息
     */
    public final function getWeappInfo($code = '')
    {
        static $_weapp = array();
        if (empty($code)) {
            $config = $this->getConfig();
            $code = !empty($config['code']) ? $config['code'] : $this->weapp_app_name;
        }
        if (isset($_weapp[$code])) {
            return $_weapp[$code];
        }
        $values = array();
        $config = Db::name('weapp')->where('code', $code)->getField('config');
        if (!empty($config)) {
            $values = json_decode($config, true);
        }
        $_weapp[$code] = $values;
        return $values;
    }
    /**
     * 获取插件的配置
     */
    public final function getConfig()
    {
        static $_config = array();
        if (!empty($_config)) {
            return $_config;
        }
        $config = (include $this->config_file);
        $_config = $config;
        return $config;
    }
    /**
     * 插件使用说明
     */
    public function doc()
    {
        return $this->success("该插件开发者未完善使用指南！", null, '', 3);
    }
}