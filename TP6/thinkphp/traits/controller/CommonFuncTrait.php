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
namespace think\traits\controller;

use think\Request;
use app\common\model\Config as ConfigModel;
use app\common\model\UsersConfig as UsersConfigModel;
trait CommonFuncTrait
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 请求对象
     * @var \think\Request
     */
    protected $request;
    /**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];
    /**
     * 当前的 session_id
     * @var string
     */
    protected $var_session_id;
    /**
     * 当前模板变量数组
     * @var array
     */
    protected $thinker = [];
    /**
     * 当前会员登陆id
     * @var mixed
     */
    protected $users_id;
    /**
     * 当前会员信息数组
     * @var array
     */
    protected $users = [];
    /**
     * json/jsonp输出参数
     * @var array
     */
    protected $json_encode_param = [
        // 以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX）
        JSON_UNESCAPED_UNICODE,
        // 不转义斜杠 　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　
        JSON_UNESCAPED_SLASHES,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
    ];
    /**
     * 当前请求控制器、应用、方法等
     * @var array
     */
    protected $params = [];
    /**
     * 根目录路径包含子目录
     * @var string
     */
    protected $web_root;
    /**
     * 当前站点子目录路径
     * @var string
     */
    protected $root_dir;
    /**
     * 是否启用绝对网址
     * @var bool
     */
    protected $cfg_multi_site;
    /**
     * 默认插件应用名获取变量
     * @var string
     */
    protected $weapp_app;
    /**
     * 默认插件控制器获取变量
     * @var string
     */
    protected $weapp_controller;
    /**
     * 默认插件操作名获取变量
     * @var string
     */
    protected $weapp_action;
    /**
     * 当前子插件应用分组
     * @var string 
     */
    protected $weapp_app_name;
    /**
     * 当前子插件控制器
     * @var string 
     */
    protected $weapp_controller_name;
    /**
     * 当前子插件操作名
     * @var string 
     */
    protected $weapp_action_name;
    /**
     * 当前子插件根目录
     * @var string 
     */
    protected $weapp_path;
    /**
     * 当前子插件配置文件路径
     * @var string 
     */
    protected $config_file;
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
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = []) : void
    {
        if (isset($options['only'])) {
            if (is_string($options['only'])) {
                $options['only'] = explode(',', $options['only']);
            }
            if (!in_array($this->request->action(), $options['only'])) {
                return;
            }
        } elseif (isset($options['except'])) {
            if (is_string($options['except'])) {
                $options['except'] = explode(',', $options['except']);
            }
            if (in_array($this->request->action(), $options['except'])) {
                return;
            }
        }
        call_user_func([$this, $method]);
    }
    private function sessionInit()
    {
        function multiToarray(array $array)
        {
            static $result_array = array();
            foreach ($array as $value) {
                if (is_array($value)) {
                    multiToarray($value);
                } else {
                    $result_array[] = $value;
                }
            }
            return $result_array;
        }
        if (false === array_search('\\think\\middleware\\SessionInit', multiToarray($this->app->middleware->all('global')))) {
            throw new \RuntimeException('Please open the session manually');
        }
    }
    private function setMiddleware()
    {
        $middlewarefile = $this->app->getBasePath() . 'middleware.php';
        if (is_file($middlewarefile)) {
            //分析php源码
            $arr = file($middlewarefile);
            for ($i = 0, $j = count($arr); $i < $j; $i++) {
                if (is_string($arr[$i]) && strpos($arr[$i], '\\think\\middleware\\SessionInit') !== false) {
                    $arr[$i] = str_replace('//', '', $arr[$i]);
                    break;
                }
            }
            $content = implode('', $arr);
            if (strpos($content, '\\think\\middleware\\SessionInit') !== false) {
                file_put_contents($middlewarefile, $content);
            }
        }
    }
    /**
     * 获取系统全局变量，存放在$this->params
     * @access protected
     * @return void
     */
    protected final function loadParams() : void
    {
        // 启用SESSION
        $this->sessionInit();
        $this->var_session_id = $this->app->session->getId();
        // 将当前的session_id保存为常量，供其它方法调用
        !defined('SESSION_ID') && define('SESSION_ID', $this->var_session_id);
        $this->cfg_multi_site = config('route.cfg_multi_site', false);
        $this->params['version'] = config('base.base.cfg_soft_version', 'v1.0.0');
        $this->params['cfg_soft_lang'] = config('base.base.cfg_soft_lang', '');
        $this->params['cfg_soft_public'] = config('base.base.cfg_soft_public', '');
        $this->params['cfg_soft_name'] = config('base.base.cfg_soft_name', '');
        $this->params['cfg_soft_enname'] = config('base.base.cfg_soft_enname', '');
        $this->params['cfg_soft_devteam'] = config('base.base.cfg_soft_devteam', '');
        // 当前请求的安全key
        $this->params['uc_key'] = $this->request->secureKey();
        // 子目录
        $this->params['root_dir'] = $this->root_dir = isset($GLOBALS['_M']['root_dir']) ? $GLOBALS['_M']['root_dir'] : $this->request->rootUrl();
        // 根目录
        if ($this->cfg_multi_site === true) {
            $this->params['web_root'] = $this->web_root = $this->request->domain() . $this->root_dir;
        } else {
            $this->params['web_root'] = $this->web_root = $this->root_dir;
        }
        // 系统变量名称设置
        $this->params['var_depr'] = config('route.pathinfo_depr', '/');
        $this->params['weapp_app'] = $this->weapp_app = config('route.weapp_app', 'm');
        $this->params['weapp_controller'] = $this->weapp_controller = config('route.weapp_controller', 'c');
        $this->params['weapp_action'] = $this->weapp_action = config('route.weapp_action', 'a');
        // 当前应用名称
        if (method_exists($this->request, 'setApp')) {
            $this->params['app_name'] = $this->request->app();
        } else {
            $this->params['app_name'] = $this->request->app;
        }
        // 当前控制器名称
        $this->params['controller_name'] = $this->request->controller();
        // 当前操作名称
        $this->params['action_name'] = $this->request->action();
        // 数据库表前缀
        $this->params['prefix'] = config('database.connections.mysql.prefix', '');
        // 返回对象的类名
        $weappmvcarray = explode('\\', get_class($this));
        $weappmvcarray[3] = isset($weappmvcarray[3]) ? $weappmvcarray[3] : '';
        // 当前插件应用名称
        $this->params['weapp_app_name'] = $this->weapp_app_name = $this->request->param($this->weapp_app, $weappmvcarray[1]);
        // 当前插件控制器名称
        $this->params['weapp_controller_name'] = $this->weapp_controller_name = $this->request->param($this->weapp_controller, $weappmvcarray[3]);
        // 当前插件操作名称
        $this->params['weapp_action_name'] = $this->weapp_action_name = $this->request->param($this->weapp_action, 'index');
        // 当前子插件根目录
        $this->weapp_path = root_path('weapp') . $this->weapp_app_name . \DIRECTORY_SEPARATOR;
        // 当前子插件配置文件路径
        $this->config_file = $this->weapp_path . 'config.php';
        // 是否访问手机版
        $returnData = $this->pcToMobile($this->request);
        $this->is_mobile = $returnData['is_mobile'];
        // 当前模板风格
        if ($this->is_mobile === true && is_dir(root_path('view') . 'mobile')) {
            $this->theme_style = 'mobile';
        } elseif (is_dir(root_path('view') . 'pc')) {
            $this->theme_style = 'pc';
        }
        $this->theme_style = $this->theme_style ? $this->theme_style . \DIRECTORY_SEPARATOR : '';
        // 当前模板后缀
        $this->view_suffix = config('template.view_suffix', 'htm');
    }
    /**
     * 获取系统全局变量，存放在$this->params['global']
     * @access protected
     * @return void
     */
    protected final function loadGlobal() : void
    {
        if (class_exists(ConfigModel::class)) {
            $this->params['global'] = ConfigModel::tpCache('global');
        } else {
            $this->params['global'] = array();
        }
    }
    /**
     * 获取系统全局配置变量，存放在$this->params['config']
     * @access protected
     * @return void
     */
    protected final function loadConfig() : void
    {
        $this->params['config'] = array();
    }
    /**
     * 获取系统全局变量，存放在$this->params['system']
     * @access protected
     * @return void
     */
    protected final function loadSystem() : void
    {
        $this->params['system'] = array();
    }
    /**
     * 获取会员配置，存放在$this->params['users']
     * @access protected
     * @return void
     */
    protected final function loadUsers() : void
    {
        if (class_exists(UsersConfigModel::class)) {
            $this->params['users'] = UsersConfigModel::getUsersConfigData('all');
        } else {
            $this->params['users'] = array();
        }
    }
    /**
     * 创建目录
     * @access protected
     * @param  string $dirname 目录名称
     * @return void
     */
    protected final function checkDirBuild(string $dirname) : void
    {
        if (!is_dir($dirname)) {
            @mkdir($dirname, 0755, true);
        }
    }
    /**
     * 批量修改文件后缀名
     * @access protected
     * @param  string $dir 文件夹路径
     * @param  string $srcExtension 原文件后缀名 ($srcExtension=all说明整个目录的所有文件)
     * @param  string $desExtension 目的文件后缀名
     * @return bool 
     */
    protected final function batchModifyFileSuffix(string $dir, string $srcExtension, string $desExtension) : bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $items = new \FilesystemIterator($dir);
        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->batchModifyFileSuffix($item->getPathname(), $srcExtension, $desExtension);
            } else {
                $path = pathinfo($item->getPathname());
                if ($path['extension'] == $srcExtension || $srcExtension == "all") {
                    $newname = $path['dirname'] . \DIRECTORY_SEPARATOR . $path['filename'] . "." . $desExtension;
                    rename($item->getPathname(), $newname);
                }
            }
        }
        return true;
    }
    /**
     * 手机端访问自动跳到手机独立域名
     * @access private
     * @param  Request $request
     * @return mixed
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
        if (empty($web_mobile_domain_open) || in_array($this->params['app_name'], ['admin']) || $request->isAjax() || $request->isJson()) {
            $data['is_mobile'] = $request->isMobile() ? true : false;
        } elseif (!$request->isMobile() && !empty($subDomain) && $subDomain == $web_mobile_domain) {
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