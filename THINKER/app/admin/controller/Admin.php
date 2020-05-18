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

namespace app\admin\controller;

use Thinker\Page;
use think\facade\Db;
use app\common\model\Config as ConfigModel;
use app\admin\model\AuthRole;
use app\admin\logic\AjaxLogic;

class Admin extends AuthController {

    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition[] = ['a.user_name|a.true_name','LIKE', "%{$keywords}%"];
        }

        // 权限控制 
        $admin_info = session('admin_info');
        if (0 < intval($admin_info['role_id'])) {
            $condition[] = ['a.admin_id|a.parent_id','=', $admin_info['admin_id']];
        } else {
            if (!empty($admin_info['parent_id'])) {
                $condition[] = ['a.admin_id|a.parent_id','=', $admin_info['admin_id']];
            }
        }
       

        // 查询满足要求的总记录数
      
        $count = Db::name('admin')->alias('a')->where($condition)->count();
		// 实例化分页类 传入总记录数和每页显示的记录数
        $Page = new Page($count, config('paginate.list_rows'));
		
        $list = Db::name('admin')->field('a.*, b.name AS role_name')
            ->alias('a')
            ->join('auth_role b', 'a.role_id = b.id', 'LEFT')
            ->where($condition)
            ->order('a.admin_id asc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select()->toArray();

        foreach ($list as $key => $val) {
            if (0 >= intval($val['role_id'])) {
                $val['role_name'] = !empty($val['parent_id']) ? '超级管理员' : '创始人';
            }
            $list[$key] = $val;
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('pager',$Page);

        // 第一次同步CMS用户的栏目ID到权限组里
        $this->synBuiltAuthRole();
       

        return $this->fetch();
    }

    /*
     * 管理员登陆
     */
    public function login()
    {
        if (session('?admin_id') && session('admin_id') > 0) {
            $web_adminbasefile = ConfigModel::tpCache('web.web_adminbasefile');
            $web_adminbasefile = !empty($web_adminbasefile) ? $web_adminbasefile : '/admin.php';
            return $this->success("您已登录", $web_adminbasefile);
        }
      
        
        $is_vertify = 1; 
        $admin_login_captcha = config('captcha.admin_login');
        if (!function_exists('imagettftext') || empty($admin_login_captcha['is_on'])) {
			// 函数不存在，不符合开启的条件
            $is_vertify = 0; 
        }
        $this->assign('is_vertify', $is_vertify);

        if (isPost()) {

            $post = input('post.');

           
            
            
            if (1 == $is_vertify) {
             
                if (!captcha_check($post['vertify'])) {
                    return $this->error('验证码错误');
                }
            }

            $is_clicap = 0; // 默认关闭文字验证码
            if (is_dir(root_path('weapp').'Clicap')) {
                $ClicapRow = model('Weapp')->getWeappList('Clicap');
                if (!empty($ClicapRow['status']) && 1 == $ClicapRow['status']) {
                    if (!empty($ClicapRow['data']) && $ClicapRow['data']['captcha']['admin_login']['is_on'] == 1) {
                        $clicaptcha_info = input('post.clicaptcha-submit-info');
                        $clicaptcha = new \weapp\Clicap\vendor\Clicaptcha;
                        if (empty($clicaptcha_info) || !$clicaptcha->check($clicaptcha_info, false)) {
                            return $this->error('文字点击验证错误！');
                        }
                    }
                }
            }

            $user_name = input('post.user_name/s');
            $password = input('post.password/s');
			

            // 登录错误次数的限制
            /*$ststem_login_errnum_key = 'system_'.md5('login_errnum_'.$user_name);
            $ststem_login_errtime_key = 'system_'.md5('login_errtime_'.$user_name);
            $loginErrtotal = config('login_errtotal'); // 限定最大的登录错误次数
            $loginErrexpire = config('login_errexpire'); // 限定登录错误锁定有效时间
            $loginErrnum = ConfigModel::tpCache('system.'.$ststem_login_errnum_key); // 登录错误次数
            $loginErrtime = ConfigModel::tpCache('system.'.$ststem_login_errtime_key); // 最后一次登录错误时间
            if (intval($loginErrnum) >= intval($loginErrtotal)) {
                if (getTime() < $loginErrtime + $loginErrexpire) {
                    return $this->error("登录错误次数超限，用户名被锁定15分钟！");
                } else {
                    // 重置登录错误次数
                    $loginErrnum = 0;
                    $loginErrtime = 0;
                    ConfigModel::tpCache('system', [$ststem_login_errnum_key => $loginErrnum]);
                    ConfigModel::tpCache('system', [$ststem_login_errtime_key => $loginErrtime]);
                }
            }*/
            

            $condition['user_name'] = $user_name;
            $condition['password'] = $password;
            if (!empty($condition['user_name']) && !empty($condition['password'])) {
                $condition['password'] = func_encrypt($condition['password']);
                $admin_info =  Db::name('admin')->where($condition)->find();
                if (empty($admin_info)) {
                    /*记录登录错误次数*/
                    /*$login_num = intval($loginErrtotal) - intval($loginErrnum);
                    $ststem_login_errnum = $loginErrnum + 1;
                    ConfigModel::tpCache('system', [$ststem_login_errnum_key=>$ststem_login_errnum]);
                    ConfigModel::tpCache('system', [$ststem_login_errtime_key=>getTime()]);
                    return $this->error("用户名或密码错误，您还可以尝试[{$login_num}]次！");*/
                    return $this->error("用户名或密码错误！");
                    /*end*/
                } else {
                    if ($admin_info['status'] == 0) {
                        return $this->error('用户名被禁用！');
                    }

                    $role_id = !empty($admin_info['role_id']) ? $admin_info['role_id'] : -1;
                    $auth_role_info = array();
                    if (!empty($admin_info['parent_id'])) {
                        $role_name = '超级管理员';
                        $isFounder = 0;
                    } else {
                        $role_name = '创始人';
                        $isFounder = 1;
                    }
                    if (0 < intval($role_id)) {
                        $auth_role_info = Db::name('auth_role')
                            ->field("a.*, a.name AS role_name")
                            ->alias('a')
                            ->where('a.id','=', $role_id)
                            ->find();
                        if (!empty($auth_role_info)) {
                           
                            $auth_role_info['cud'] = unserialize($auth_role_info['cud']);
                            $auth_role_info['permission'] = unserialize($auth_role_info['permission']);
                            $role_name = $auth_role_info['name'];
                        }
                    }
                    $admin_info['auth_role_info'] = $auth_role_info;
                    $admin_info['role_name'] = $role_name;

                    $last_login_time = getTime();
                    $last_login_ip = $this->request->clientIP();
                    $login_cnt = $admin_info['login_cnt'] + 1;
                    Db::name('admin')->where("admin_id = ".$admin_info['admin_id'])->save(array('last_login'=>$last_login_time, 'last_ip'=>$last_login_ip, 'login_cnt'=>$login_cnt, 'session_id'=>$this->var_session_id));
                    $admin_info['last_login'] = $last_login_time;
                    $admin_info['last_ip'] = $last_login_ip;

                    // 头像
                    empty($admin_info['head_pic']) && $admin_info['head_pic'] = get_head_pic($admin_info['head_pic'], true);

                    $admin_info_new = $admin_info;
                    // 过滤存储在session文件的敏感信息
                    foreach (['user_name','true_name','password'] as $key => $val) {
                        unset($admin_info_new[$val]);
                    }
                   
                    session('admin_id',$admin_info['admin_id']);
                    session('admin_info', $admin_info_new);
                    session('admin_login_expire', getTime()); // 登录有效期

                    // 检查密码复杂度
                    $admin_login_pwdlevel = checkPasswordLevel($password);
                    session('admin_login_pwdlevel', $admin_login_pwdlevel);
                 

                    // 重置登录错误次数
                    /*ConfigModel::tpCache('system', [$ststem_login_errnum_key=>0]);
                    ConfigModel::tpCache('system', [$ststem_login_errtime_key=>0]);*/

                    adminLog('后台登录');
                    $url = session('from_url') ? session('from_url') : $this->request->baseFile();
                    session('isset_author', null); // 内置勿动

                    // 同步追加一个后台管理员到会员用户表
                    $this->synUsersLogin($admin_info, $isFounder);
                   

                    return $this->success('登录成功', $url);
                }
            } else {
                return $this->error('请填写用户名/密码');
            }
        }

        $ajaxLogic = new AjaxLogic;
        $ajaxLogic->loginHandle();
        
        session('admin_info', null);

        return $this->fetch();
    }

    /**
     * 验证码获取
     */
    public function vertify()
    {
        /*验证码插件开关*/
        $admin_login_captcha = config('captcha.admin_login');
        $config = (!empty($admin_login_captcha['is_on']) && !empty($admin_login_captcha['config'])) ? 'admin_login.config' : 'default';
        /*--end*/
       // ob_clean(); 
        return captcha($config);
    }
    
    /**
     * 修改管理员密码
     * @return \think\mixed
     */
    public function adminPwd()
    {
        $admin_id = input('admin_id/d',0);
        $oldPwd = input('old_pw/s');
        $newPwd = input('new_pw/s');
        $new2Pwd = input('new_pw2/s');
       
        if(!$admin_id){
            $admin_id = session('admin_id');
        }
        $info = Db::name('admin')->where("admin_id", $admin_id)->find();
        $info['password'] =  "";
        $this->assign('info',$info);
        
        if(isPost()){
            //修改密码
            $enOldPwd = func_encrypt($oldPwd);
            $enNewPwd = func_encrypt($newPwd);
            $admin = Db::name('admin')->where('admin_id' , $admin_id)->find();
            if(!$admin || $admin['password'] != $enOldPwd){
                exit(json_encode(array('status'=>-1,'msg'=>'旧密码不正确')));
            }else if($newPwd != $new2Pwd){
                exit(json_encode(array('status'=>-1,'msg'=>'两次密码不一致')));
            }else{
                $data = array(
                    'update_time'   => getTime(),
                    'password'      => $enNewPwd,
                );
                $row = Db::name('admin')->where('admin_id' , $admin_id)->save($data);
                if($row){
                    /*检查密码复杂度*/
                    $admin_login_pwdlevel = checkPasswordLevel($newPwd);
                    session('admin_login_pwdlevel', $admin_login_pwdlevel);
                    /*end*/
                    adminLog('修改管理员密码');
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                }else{
                    exit(json_encode(array('status'=>-1,'msg'=>'操作失败')));
                }
            }
        }

        if ($this->isAjax()) {
            return $this->fetch('admin/admin_pwd_ajax');
        } else {
            return $this->fetch('admin/admin_pwd');
        }
    }
    
    /**
     * 退出登陆
     */
    public function logout()
    {
        adminLog('安全退出');
      
       
        session(null);
		// 清除并恢复栏目列表的展开方式
        cookie('admin-treeClicked', null); 
        return $this->success("安全退出", $this->request->baseFile());
    }

    /**
     * 新增管理员时，检测用户名是否与前台用户名相同
     */
    public function ajaxAddUserName()
    {
        if (isAjaxPost(false)) {
            $user_name = input('post.user_name/s');
            if (Db::name('admin')->where("user_name", $user_name)->count()) {
                return $this->error("此用户名已被注册，请更换！");
            }
            $row = Db::name('users')->field('users_id')->where([
                    'username'  => $user_name,
                   
                ])->find();
            if (!empty($row)) {
                return $this->error('已有相同会员名，将其转为系统账号？');
            } else {
                return $this->success('会员名不存在，无需提示！');
            }
        }
    }

    /**
     * 新增管理员
     */
    public function adminAdd()
    {
        

        if (isPost()) {
            $data = input('post.');

            if (0 < intval(session('admin_info.role_id'))) {
                return $this->error("超级管理员才能操作！");
            }

            if (empty($data['password']) || empty($data['password2'])) {
               return  $this->error("密码不能为空！");
            }else if ($data['password'] != $data['password2']) {
                return $this->error("两次密码输入不一致！");
            }

            $data['user_name'] = trim($data['user_name']);
            $data['password'] = func_encrypt($data['password']);
            $data['password2'] = func_encrypt($data['password2']);
            $data['role_id'] = intval($data['role_id']);
            $data['parent_id'] = session('admin_info.admin_id');
            $data['add_time'] = getTime();
            if (empty($data['pen_name'])) {
                $data['pen_name'] = $data['user_name'];
            }
            if (Db::name('admin')->where("user_name", $data['user_name'])->count()) {
               return  $this->error("此用户名已被注册，请更换",url('Admin/admin_add'));
            } else {
                $admin_id = Db::name('admin')->insertGetId($data);
                if ($admin_id) {
                    adminLog('新增管理员：'.$data['user_name']);

                    /*同步追加一个后台管理员到会员用户表*/
                    try {
                        $usersInfo = Db::name('users')->field('users_id')->where([
                                'username'  => $data['user_name'],
                                
                            ])->find();
                        if (!empty($usersInfo)) {
                            $r = Db::name('users')->where(['users_id'=>$usersInfo['users_id']])->update([
                                    'nickname'      => $data['user_name'],
                                    'admin_id'      => $admin_id,
                                    'is_activation' => 1,
                                    'is_lock'       => 0,
                                    'is_del'        => 0,
                                    'update_time'   => getTime(),
                                ]);
                            !empty($r) && $users_id = $usersInfo['users_id'];
                        } else {
                            // 获取要添加的用户名
                            $username = $this->GetUserName($data['user_name']);
                            $AddData = [
                                'username' => $username,
                                'nickname' => $username,
                                'password' => func_encrypt(getTime()),
                                'level'    => 1,
                                
                                'reg_time' => getTime(),
                                'add_time' => getTime(),
                                'head_pic' => get_head_pic(),
                                'register_place' => 1,
                                'admin_id' => $admin_id,
                            ];
                            $users_id = Db::name('users')->insertGetId($AddData);
                        }
                        if (!empty($users_id)) {
                            Db::name('admin')->where(['admin_id'=>$admin_id])->update([
                                    'syn_users_id'  => $users_id,
                                    'update_time'   => getTime(),
                                ]);
                        }
                    } catch (\Exception $e) {}
                    /* END */

                   return  $this->success("操作成功", url('Admin/index'));
                } else {
                   return  $this->error("操作失败");
                }
            }
        }

        // 权限组
        $admin_role_list = model('AuthRole')->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);

        // 模块组
        $modules = getAllMenu();
        $this->assign('modules', $modules);

        // 权限集
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = Db::name('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = model('Weapp')->getList(['status'=>1]);
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    /**
     * 编辑管理员
     */
    public function adminEdit()
    {
        if (isPost()) {
            $data = input('post.');
            $id = $data['admin_id'];

            if ($id == session('admin_info.admin_id')) {
                unset($data['role_id']); // 不能修改自己的权限组
            } else if (0 < intval(session('admin_info.role_id')) && session('admin_info.admin_id') != $id) {
                return $this->error('禁止更改别人的信息！');
            }

            $password = $data['password'];
            if (!empty($password) || !empty($data['password2'])) {
                if ($password != $data['password2']) {
                  return   $this->error("两次密码输入不一致！");
                }
            }

            $user_name = $data['user_name'];
            if(empty($password)){
                unset($data['password']);
            }else{
                $data['password'] = func_encrypt($password);
            }
            unset($data['user_name']);
            
            if (empty($data['pen_name'])) {
                $data['pen_name'] = $user_name;
            }

            /*不允许修改自己的权限组*/
            if (isset($data['role_id'])) {
                if (0 < intval(session('admin_info.role_id')) && intval($data['role_id']) != session('admin_info.role_id')) {
                    $data['role_id'] = session('admin_info.role_id');
                }
            }
            /*--end*/
            $data['update_time'] = getTime();
            $r = Db::name('admin')->where('admin_id', $id)->save($data);
            if ($r) {
                /*检查密码复杂度*/
                if ($id == session('admin_info.admin_id')) {
                    $admin_login_pwdlevel = checkPasswordLevel($password);
                    session('admin_login_pwdlevel', $admin_login_pwdlevel);
                }
                /*end*/

                /*过滤存储在session文件的敏感信息*/
                if ($id == session('admin_info.admin_id')) {
                    $admin_info = session('admin_info');
                    $admin_info = array_merge($admin_info, $data);
                    foreach (['user_name','true_name','password','password2'] as $key => $val) {
                        unset($admin_info[$val]);
                    }
                    session('admin_info', $admin_info);
                }
                /*--end*/
                adminLog('编辑管理员：'.$user_name);
               return  $this->success("操作成功",url('Admin/index'));
            } else {
              return   $this->error("操作失败");
            }
        }

        $id = input('get.id/d', 0);
        $info = Db::name('admin')->field('a.*')
            ->alias('a')
            ->where("a.admin_id", $id)->find();
        $info['password'] =  "";
        $this->assign('info',$info);

        // 当前角色信息
        $admin_role_model = model('AuthRole');
        $role_info = $admin_role_model->getRole(array('id' => $info['role_id']));
        $this->assign('role_info', $role_info);

        // 权限组
        $admin_role_list = $admin_role_model->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);

        // 模块组
        $modules = config('menu');
        $this->assign('modules', $modules);

        // 权限集
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = Db::name('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = model('Weapp')->getList(['status'=>1]);
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    /**
     * 删除管理员
     */
    public function adminDel()
    {
       

        if (isPost()) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (in_array(session('admin_id'), $id_arr)) {
              return   $this->error('禁止删除自己');
            }
            if (!empty($id_arr)) {
                if (0 < intval(session('admin_info.role_id')) || !empty($parent_id) ) {
                    $count = Db::name('admin')->where("admin_id IN (".implode(',', $id_arr).") AND role_id = -1")
                        ->count();
                    if (!empty($count)) {
                       return  $this->error('禁止删除超级管理员');
                    }
                }

                $result = Db::name('admin')->field('user_name')->where("admin_id",'IN',$id_arr)->select();
                $user_names = get_arr_column($result, 'user_name');

                $r = Db::name('admin')->where("admin_id",'IN',$id_arr)->delete();
                if($r){
                    adminLog('删除管理员：'.implode(',', $user_names));

                    // 同步删除管理员关联的前台会员
                    Db::name('users')->where('admin_id','IN', $id_arr)->delete();
                    

                   return  $this->success('删除成功');
                }else{
                  return   $this->error('删除失败');
                }
            }else{
               return  $this->error('参数有误');
            }
        }
        return $this->error('非法操作');
    }

    /*
     * 第一次同步CMS用户的栏目ID到权限组里
     * 默认赋予内置权限所有的内容栏目权限
     */
    private function synBuiltAuthRole()
    {
        $authRole = new AuthRole;
        $roleRow = $authRole->getRoleAll(['built_in'=>1,'update_time'=>['elt',0]]);
        if (!empty($roleRow)) {
            $saveData = [];
            foreach ($roleRow as $key => $val) {
                $permission = $val['permission'];
                $arctype = Db::name('arctype')->where('status',1)->column('id');
                if (!empty($arctype)) {
                    $permission['arctype'] = $arctype;
                } else {
                    unset($permission['arctype']);
                }
                $saveData[] = array(
                    'id'    => $val['id'],
                    'permission'    => $permission,
                    'update_time'   => getTime(),
                );
            }
            $authRole->saveAll($saveData);
        }
    }

    /*
     * 设置admin表数据
     */
    public function ajaxSetfield()
    {
        if (isPost()) {
            $admin_id = session('admin_id');
            $field  = input('field'); // 修改哪个字段
            $value  = input('value', '', null); // 修改字段值  
            if (!empty($admin_id)) {
                $r = Db::name('admin')->where('admin_id',intval($admin_id))->save([
                        $field=>$value,
                        'update_time'=>getTime(),
                    ]); // 根据条件保存修改的数据
                if ($r) {
                    /*更新存储在session里的信息*/
                    $admin_info = session('admin_info');
                    $admin_info[$field] = $value;
                    session('admin_info', $admin_info);
                    /*--end*/
                  return   $this->success('操作成功');
                }
            }
        }
      return   $this->error('操作失败');
    }

    /*
     * 检测密码的复杂程度
     */
    public function ajaxCheckPasswordLevel()
    {
        $password = input('post.password/s');
        if (isAjaxPost(false) && !empty($password)) {
            $pwdLevel = checkPasswordLevel($password);
            if (3 >= $pwdLevel) {
               return  $this->success("<font color='red'>当前密码复杂度为 {$pwdLevel} ，建议复杂度在 4~7 范围内，避免容易被暴力破解！</font>", null, ['pwdLevel'=>$pwdLevel]);
            } else {
               return  $this->success("<font color='green'>当前密码复杂度为 {$pwdLevel} ，在系统设定 4~7 安全范围内！</font>", null, ['pwdLevel'=>$pwdLevel]);
            }
        }
        return $this->error('操作失败');
    }

    // 确保用户名唯一
    private function GetUserName($username = null)
    {
        $count = Db::name('users')->where('username',$username)->count();
        if (!empty($count)) {
            $username_new = $username.rand(1000,9999);
            $username = $this->GetUserName($username_new);
        }

        return $username;
    }

    /**
     * 同步追加一个后台管理员到会员用户表，并同步前台登录
     */
    private function synUsersLogin($admin_info = [], $isFounder = 0)
    {
        $where_new = [
            'admin_id'  => $admin_info['admin_id'],
            
        ];
        $users_id = Db::name('users')->where($where_new)->getField('users_id');
        try {
            if (empty($users_id) && empty($admin_info['syn_users_id'])) {
                $usersInfo = [];
                if (1 == $isFounder) {
                    // 如果是创始人，强制将与会员名相同的改为管理员前台用户名
                    $usersInfo = Db::name('users')->field('users_id')->where([
                            'username'  => $admin_info['user_name'],
                            
                        ])->find();
                }
                if (!empty($usersInfo)) {
                    $r = Db::name('users')->where(['users_id'=>$usersInfo['users_id']])->update([
                            'nickname'      => $admin_info['user_name'],
                            'admin_id'      => $admin_info['admin_id'],
                            'is_activation' => 1,
                            'is_lock'       => 0,
                            'is_del'        => 0,
                            'update_time'   => getTime(),
                            'last_login'    => getTime(),
                        ]);
                    !empty($r) && $users_id = $usersInfo['users_id'];
                } else {
                    // 获取要添加的用户名
                    $username = $this->GetUserName($admin_info['user_name']);
                    $AddData = [
                        'username' => $username,
                        'nickname' => $username,
                        'password' => func_encrypt(getTime()),
                        'level'    => 1,
                        
                        'reg_time' => getTime(),
                        'head_pic' => get_head_pic(),
                        'add_time' => getTime(),
                        'last_login' => getTime(),
                        'register_place' => 1,
                        'admin_id' => $admin_info['admin_id'],
                    ];
                    $users_id = Db::name('users')->insertGetId($AddData);
                }
                if (!empty($users_id)) {
                    Db::name('admin')->where(['admin_id'=>$admin_info['admin_id']])->update([
                            'syn_users_id'  => $users_id,
                            'update_time'   => getTime(),
                        ]);
                    $admin_info['syn_users_id'] = $users_id;
                    session('admin_info', $admin_info);
                }
            } else if (!empty($users_id) && empty($admin_info['syn_users_id'])) {
                Db::name('admin')->where(['admin_id'=>$admin_info['admin_id']])->update([
                        'syn_users_id'  => $users_id,
                        'update_time'   => getTime(),
                    ]);
                $admin_info['syn_users_id'] = $users_id;
                session('admin_info', $admin_info);
            }
        } catch (\Exception $e) {}
        
        // 加载前台session
        if (!empty($users_id)) {
            $users = Db::name('users')->field('a.*,b.level_name,b.level_value,b.discount as level_discount')
                ->alias('a')
                ->join('users_level b', 'a.level = b.level_id', 'LEFT')
                ->where([
                    'a.users_id'        => $users_id,
                   
                    'a.is_activation'   => 1,
                ])->find();
            if (!empty($users)) {
                session('users',$users);
                session('users_id',$users_id);
                Db::name('users')->where(['users_id'=>$users_id])->update([
                        'update_time'   => getTime(),
                        'last_login'    => getTime(),
                    ]);
            }
        }
    }
}