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
// [ 会员中心控制器基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\user\controller;

use think\App;
use think\Container;
use think\facade\Db;
use app\common\model\UsersConfig as UsersConfigModel;
use app\common\controller\Common;
abstract class BaseController extends Common
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 当前会员配置数组
     * @var array
     */
    protected $usersConfig = [];
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? Container::pull('app') : $app;
        parent::__construct($this->app);
        // 控制器初始化
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     * @return mixed
     * @throws HttpResponseException
     */
    protected function initialize()
    {
        parent::initialize();
        $this->suberInit();
    }
    /**
     * 初始化操作
     * @access protected
     * @return mixed
     */
    protected function suberInit()
    {
        if (session('?users_id')) {
            $users_id = session('users_id');
            $users = GetUsersLatestData($users_id);
            $this->users = $users;
            $users_id = $this->users_id = $users['users_id'];
            $nickname = $this->users['nickname'];
            if (empty($nickname)) {
                $nickname = $this->users['username'];
            }
            $this->assign(compact('nickname', 'users', 'users_id'));
        } else {
            //过滤不需要登陆的行为
            $ctl_act = $this->params['controller_name'] . '@' . $this->params['action_name'];
            $ctl_all = $this->params['controller_name'] . '@*';
            $filter_login_action = config('user.filter_login_action');
            if (!in_array($ctl_act, $filter_login_action) && !in_array($ctl_all, $filter_login_action)) {
                $resource = input('param.resource/s');
                if ('Uploadify@*' == $ctl_all && 'reg' == $resource) {
                    // 注册时上传图片不验证登录行为
                } elseif (isAjax()) {
                    return $this->error('请先登录！');
                } elseif (isWeixin()) {
                    return $this->redirect('user/Users/usersSelectLogin');
                } else {
                    return $this->redirect('user/Users/login');
                }
            }
        }
        // 订单超过 get_shop_order_validity 设定的时间，则修改订单为已取消状态，无需返回数据
        S('Shop')->UpdateShopOrderData($this->users_id);
        // 会员功能是否开启
        $logut_redirect_url = '';
        $this->usersConfig = UsersConfigModel::getUsersConfigData('all');
        $web_users_switch = config('tpcache.web_users_switch');
        if (empty($web_users_switch) || isset($this->usersConfig['users_open_register']) && $this->usersConfig['users_open_register'] == 1) {
            // 前台会员中心已关闭
            $logut_redirect_url = $this->web_root . '/';
        } else {
            if (session('?users_id') && empty($this->users)) {
                // 登录的会员被后台删除，立马退出会员中心
                $logut_redirect_url = url('user/Users/centre');
            }
        }
        if (!empty($logut_redirect_url)) {
            // 清理session并回到首页
            session('users_id', null);
            session('users', null);
            return $this->redirect($logut_redirect_url);
        }
		// 默认主题颜色
        $this->usersConfig['theme_color'] = $theme_color = !empty($this->usersConfig['theme_color']) ? $this->usersConfig['theme_color'] : '#ff6565';
		$this->assign('usersConfig', $this->usersConfig);
        // 是否为手机端
        $is_mobile = isMobile() ? 1 : 2;
        // 是否为端微信
        $is_wechat = isWeixin() ? 1 : 2;
        // 是否为微信端小程序
        $is_wechat_applets = isWeixinApplets() ? 1 : 0;
        $this->assign(compact('is_wechat_applets', 'is_wechat', 'is_mobile', 'theme_color'));
    }
	
	/**
     * 登陆状态下重定向到会员中心
     * @access protected
     * @return \think\Response|void
     */
    protected function isLoginToCentre()
    {
        if ($this->users_id > 0) {
            return $this->redirect('user/Users/centre');
        }
    }
}