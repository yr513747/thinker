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
// [ 后台所有控制器继承的类基类 ]
// --------------------------------------------------------------------------
namespace app\admin\controller;
use app\common\model\Config as ConfigModel;
use think\facade\Db;
abstract class AuthController extends SystemBasic
{
    /**
     * 当前登陆管理员信息
     * @var
     */
    protected $adminInfo;

    /**
     * 当前登陆管理员ID
     * @var
     */
    protected $adminId;

    /**
     * 当前的 session_id
     * @var string
     */
    protected $var_session_id;

    protected $skipLogController = ['index','common'];
	
	/**
     * 初始化操作
     */
    protected function initialize()
    {
        parent::initialize();
		
        $this->var_session_id = var_session_id();
        // 将当前的session_id保存为常量，供其它方法调用
        !defined('SESSION_ID') && define('SESSION_ID', $this->var_session_id);
		// 过滤不需要登陆的行为
        $ctl_act = $this->params['controller_name'].'@'.$this->params['action_name'];
        $ctl_all = $this->params['controller_name'].'@*';
        $filter_login_action = config('config.filter_login_action');
		
        if (!in_array($ctl_act, $filter_login_action) || !in_array($ctl_all, $filter_login_action)) {
          
       
            $web_login_expiretime = ConfigModel::tpCache('web.web_login_expiretime');
            empty($web_login_expiretime) && $web_login_expiretime = config('config.login_expire');
			// 登录有效期
            $admin_login_expire = session('admin_login_expire'); 
            if (session('?admin_id') && getTime() - intval($admin_login_expire) < $web_login_expiretime) {
                session('admin_login_expire', getTime()); 
                $this->checkAuth();
            }else{
                // 自动退出
                adminLog('自动退出');
                
                session(null);
				// 清除并恢复栏目列表的展开方式
                cookie('admin-treeClicked', null); 
				$Url = url('admin/Admin/login');
				
                return $this->redirect($Url);
            }
        }

        // 增、改的跳转提示页，只限制于发布文档的模型和自定义模型 
        $channeltype_list = config('global.channeltype_list');
        $controller_name = $this->params['controller_name'];
        if (isset($channeltype_list[strtolower($controller_name)]) || 'Custom' == $controller_name) {
            if (in_array($this->params['action_name'], ['add','edit'])) {
				
                config(['jump_success_tmpl' => 'public/dispatch_jump'], 'app');
              
                $id = $this->input('param.id/d', $this->input('param.aid/d'));
                (isGet()) && cookie('ENV_IS_UPHTML', 0);
            } else if (in_array($this->params['action_name'], ['index'])) {
                cookie('ENV_GOBACK_URL', $this->request->url());
                cookie('ENV_LIST_URL', url("admin/{$controller_name}/index"));
            }
        }
        if ('Archives' == $controller_name && in_array($this->params['action_name'], ['indexArchives'])) {
            cookie('ENV_GOBACK_URL', $this->request->url());
            cookie('ENV_LIST_URL', url("admin/Archives/indexArchives"));
        }
       

        // 会员投稿设置
        $IsOpenRelease = Db::name('users_menu')->where([
            'mca'  => 'user/UsersRelease/release_centre',
           
        ])->getField('status');
        $this->assign('IsOpenRelease',$IsOpenRelease);
       
    }
	
	/**
     * 检查管理员菜单操作权限
     * @access protected
     * @return mixed
     */
    protected function checkAuth()
    {
       $ctl = $this->params['controller_name'];
        $act = $this->params['action_name'];
        $ctl_act = $ctl.'@'.$act;
        $ctl_all = $ctl.'@*';
        // 无需验证的操作
        $uneed_check_action = config('config.uneed_check_action');
        if (0 >= intval(session('admin_info.role_id'))) {
            // 超级管理员无需验证
            return true;
        } else {
            $bool = false;

            // 检测是否有该权限
            if (is_check_access($ctl_act)) {
                $bool = true;
            }
           

            // 在列表中的操作不需要验证权限
            if (isAjax || strpos($act,'ajax') !== false || in_array($ctl_act, $uneed_check_action) || in_array($ctl_all, $uneed_check_action)) {
                $bool = true;
            }
           

            // 检查是否拥有此操作权限
            if (!$bool) {
                return $this->error('您没有操作权限，请联系超级管理员分配权限');
            }
        }
    }

	/**
     * 写入全局内置参数
	 * @access protected 
	 * @param array $options 缓存配置
     * @return void
     */
    protected function writeGlobalParams($options = null)
    {
        $webConfigParams = Db::name('config')->where(['inc_type' => 'web', 'is_del' => 0])->getAllWithIndex('name');
        // 网站根网址
        $web_basehost = !empty($webConfigParams['web_basehost']) ? $webConfigParams['web_basehost']['value'] : '';
        // CMS安装目录
        $web_cmspath = !empty($webConfigParams['web_cmspath']) ? $webConfigParams['web_cmspath']['value'] : '';
        // 启用绝对网址，开启此项后附件、栏目连接、arclist内容等都使用http路径
        $cfg_multi_site = !empty($webConfigParams['cfg_multi_site']) ? $webConfigParams['cfg_multi_site']['value'] : '';
        if ($cfg_multi_site == 1) {
            $web_mainsite = $web_basehost . $web_cmspath;
        } else {
            $web_mainsite = '';
        }
        // CMS安装目录的网址
        $param['web_cmsurl'] = $web_mainsite;
        // 将内置的全局变量(页面上没有入口更改的全局变量)存储到web版块里
        $inc_type = 'web';
        foreach ($param as $key => $val) {
            if (preg_match("/^" . $inc_type . "_(.)+/i", $key) !== 1) {
                $nowKey = strtolower($inc_type . '_' . $key);
                $param[$nowKey] = $val;
            }
        }
        ConfigModel::tpCache($inc_type, $param, $options);
    }
}