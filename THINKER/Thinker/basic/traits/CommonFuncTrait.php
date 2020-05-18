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
namespace Thinker\basic\traits;

use app\common\model\Config as ConfigModel;
use app\common\model\UsersConfig as UsersConfigModel;
// --------------------------------------------------------------------------
trait CommonFuncTrait
{
    /**
     * 当前请求控制器、应用、方法、参数等
     * @var array
     */
    protected $params = [];
    /**
     * 当前请求参数
     * @var array
     */
    protected $data = [];
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
     * 获取系统全局变量，存放在$this->params
     * @access protected
     * @return $this
     */
    protected final function loadParams()
    {
        // 特殊全局变量
        global $get_defined_functions, $get_defined_constants;
        global $_M, $cfg_soft_version, $cfg_soft_lang, $cfg_soft_public, $cfg_soft_name, $cfg_soft_enname, $cfg_soft_devteam;
        $this->cfg_multi_site = config('route.cfg_multi_site', false);
        $this->params['get_defined_functions'] = $get_defined_functions;
        $this->params['get_defined_constants'] = $get_defined_constants;
        $this->params['version'] = $cfg_soft_version;
        $this->params['cfg_soft_lang'] = $cfg_soft_lang;
        $this->params['cfg_soft_public'] = $cfg_soft_public;
        $this->params['cfg_soft_name'] = $cfg_soft_name;
        $this->params['cfg_soft_enname'] = $cfg_soft_enname;
        $this->params['cfg_soft_devteam'] = $cfg_soft_devteam;
        // 当前请求的安全key
        $this->params['uc_key'] = isset($_M['uc_key']) ? $_M['uc_key'] : $this->request->secureKey();
        // 子目录
        $this->params['root_dir'] = $this->root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : $this->request->rootUrl();
        // 根目录
        if ($this->cfg_multi_site === true) {
            $this->params['web_root'] = $this->web_root = $this->request->domain() . $this->root_dir;
        } else {
            $this->params['web_root'] = $this->web_root = $this->root_dir;
        }
        // $this->params['version'] = $this->app->version();
        // 系统变量名称设置
        $this->params['var_depr'] = config('route.pathinfo_depr', '/');
        $this->params['weapp_app'] = $this->weapp_app = config('route.weapp_app', 'm');
        $this->params['weapp_controller'] = $this->weapp_controller = config('route.weapp_controller', 'c');
        $this->params['weapp_action'] = $this->weapp_action = config('route.weapp_action', 'a');
        // 当前应用名称
        if (method_exists($this->request, 'app')) {
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
        unset($get_defined_functions, $get_defined_constants, $_M);
        unset($cfg_soft_version, $cfg_soft_lang, $cfg_soft_public, $cfg_soft_name, $cfg_soft_enname, $cfg_soft_devteam);
        return $this;
    }
    /**
     * 获取系统全局变量，存放在$this->params['global']
     * @access protected
     * @return $this
     */
    protected final function loadGlobal()
    {
        $this->params['global'] = ConfigModel::tpCache('global');
        return $this;
    }
    /**
     * 获取系统全局配置变量，存放在$this->params['config']
     * @access protected
     * @return $this
     */
    protected final function loadConfig()
    {
        $this->params['config'] = array();
        return $this;
    }
    /**
     * 获取系统请求变量，存放在$this->params['form']，系统表单变量数组.使用$this->data直接获取请求变量
     * @access protected
     * @return $this
     * @throws AuthException
     */
    protected final function loadForm()
    {
        $this->params['form'] = array();
        // 获取请求变量
        foreach (input('param.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        foreach (input('cookie.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        foreach (input('request.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        foreach (input('post.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        foreach (input('get.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        foreach (input('route.') as $_key => $_value) {
            $_key[0] != '_' && ($this->params['form'][$_key] = daddslashes($_value));
        }
        $this->data = array_merge($this->data, $this->params['form']);
        unset($this->params['form']);
        return $this;
    }
    /**
     * 获取系统全局变量，存放在$this->params['system']
     * @access protected
     * @return $this
     */
    protected final function loadSystem()
    {
        $this->params['system'] = array();
        return $this;
    }
    /**
     * 获取会员配置，存放在$this->params['users']
     * @access protected
     * @return $this
     */
    protected final function loadUsers()
    {
        $this->params['users'] = UsersConfigModel::getUsersConfigData('all');
        return $this;
    }
    /**
     * 请求变量赋值
     * @access public
     * @param string $name  变量名
     * @param mixed  $value 变量值
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    /**
     * 取得请求变量的值
     * @access protected
     * @param string $name 请求变量
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }
    /**
     * 检测请求变量是否存在
     * @access public
     * @param string $name 请求变量名
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
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
     * @param $dir 文件夹路径
     * @param $srcExtension 原文件后缀名 ($srcExtension=all说明整个目录的所有文件)
     * @param $desExtension 目的文件后缀名
     * @return bool 
     */
    protected final function batchModifyFileSuffix($dir, $srcExtension, $desExtension)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $handler = opendir($dir);
        //列出$dir目录中的所有文件
        while (($fileName = readdir($handler)) != false) {
            if ($fileName != '.' && $fileName != '..') {
                //'.' 和 '..'是分别指向当前目录和上级目录
                $curDir = $dir . '/' . $fileName;
                if (is_dir($curDir)) {
                    //如果是目录，则递归下去
                    $this->batchModifyFileSuffix($curDir, $srcExtension, $desExtension);
                } else {
                    //获取文件路径的信息
                    $path = pathinfo($curDir);
                    //print_r($path);
                    if ($path['extension'] == $srcExtension || $srcExtension == "all") {
                        $newname = $path['dirname'] . '/' . $path['filename'] . "." . $desExtension;
                        rename($curDir, $newname);
                    }
                }
            }
        }
        return true;
    }
}