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
namespace Thinker\basic;

use think\App;
use think\Request;
use think\Container;
/**
 * 控制器基础类
 */
abstract class BaseController extends Common
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
     * 当前模板风格
     * @var string
     */
    protected $theme_style;
    /**
     * 当前模板后缀
     * @var string
     */
    protected $view_suffix;
	/**
     * 是否访问手机版
	 * @var bool
     */
    protected $is_mobile = false;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? Container::pull('app') : $app;
        parent::__construct($this->app);
		$returnData = $this->pcToMobile($this->request);
        $this->is_mobile = $returnData['is_mobile'];
        if ($this->is_mobile === true && is_dir(root_path('view') . 'mobile')) {
            $this->theme_style = 'mobile';
        } elseif (is_dir(root_path('view') . 'pc')) {
            $this->theme_style = 'pc';
        }
        $this->theme_style = $this->theme_style ? $this->theme_style . DS : '';
        if (in_array($this->params['app_name'], ['home', 'user'])) {
            $view_path = root_path('view') . $this->theme_style;
            config(['view_path' => $view_path], 'template');
        } else {
            if (in_array($this->params['app_name'], array('admin'))) {
                if ('weapp' == strtolower($this->params['controller_name']) && 'execute' == strtolower($this->params['action_name'])) {
                    $view_path = root_path('weapp') . $this->request->param($this->params['weapp_app']) . DS . 'template' . DS;
                    config(['view_path' => $view_path], 'template');
                }
            }
        }
        // 设置当前模板解析的引擎
        $template = config('template', []);
        $this->view_suffix = isset($template['view_suffix']) ? $template['view_suffix'] : 'htm';
        $this->engine($template);
		// 尝试从缓存中读取
        if (false === $this->app->isDebug()) {
            $this->readHtmlCache();
        }
		
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
        if (in_array($this->params['app_name'], array('admin')) && 'Weapp' == $this->params['controller_name'] && 'execute' == $this->params['action_name']) {
            /*插件的前置操作*/
            $m = isset($this->data[$this->weapp_app]) ? $this->data[$this->weapp_app] : null;
            $c = isset($this->data[$this->weapp_controller]) ? $this->data[$this->weapp_controller] : null;
            $a = isset($this->data[$this->weapp_action]) ? $this->data[$this->weapp_action] : null;
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
     * 手机端访问自动跳到手机独立域名
     * @access public
     */
    private function pcToMobile(Request $request)
    {
        $data = [];
        $data['is_mobile'] = false;
		// 是否开启手机域名访问
        $web_mobile_domain_open = config('tpcache.web_mobile_domain_open');
		// 配置手机域名
        $mobileurl = '';
        $subDomain = $request->subDomain();
        $web_mobile_domain = config('tpcache.web_mobile_domain');		
        
        if (empty($web_mobile_domain_open) || in_array($this->params['app_name'], ['admin']) || isAjax()) {
            $data['is_mobile'] = isMobile() ? true : false;
        } elseif (!isMobile() && !empty($subDomain) && $subDomain == $web_mobile_domain) {
            // 子域名和手机域名相同,就表示访问手机端模板
            $data['is_mobile'] = true;
        } else {
            // 辨识IP访问，还是域名访问，如果是IP访问，将会与PC端的URL一致
            if (preg_match('/^\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}$/i', $request->host(true)) || 'localhost' == $request->host(true)) {
                $data['is_mobile'] = true;
            } else {
      
                // 获取当前配置下，手机端带协议的域名URL，要么是与主域名一致，要么是独立二级域名
                $mobileDomainURL = $request->domain();
                if (!empty($web_mobile_domain) && ($subDomain != $web_mobile_domain || empty($subDomain))) {
                    $mobileDomainURL = preg_replace('/^(.*)(\\/\\/)([^\\/]*)(\\.?)(' . $request->rootDomain() . ')(.*)$/i', '${1}${2}' . $web_mobile_domain . '.${5}${6}', $mobileDomainURL);
                    $mobileurl = $mobileDomainURL . $request->url();
                } else {
                    $data['is_mobile'] = true;
                }
            }
            if (!empty($mobileurl) && is_string($mobileurl)) {
                return $this->redirect($mobileurl);
            }
        }
        return $data;
    }
}