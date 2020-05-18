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

use app\common\model\Config as ConfigModel;
use app\common\model\UsersConfig as UsersConfigModel;
use think\facade\Db;
class Index extends AuthController
{
    public function index()
    {
        // 小程序开关
        $web_minipro_switch = ConfigModel::tpCache('web.web_minipro_switch');
        $this->assign('web_minipro_switch', $web_minipro_switch);
        // 首页链接
        $home_url = $this->request->domain() . $this->root_dir;
        $this->assign('home_url', $home_url);
        $this->assign('admin_info', getAdminInfo(session('admin_id')));
        $this->assign('menu', getMenuList());
        // 检测是否存在会员中心模板
        $globalConfig = $this->params['global'];
        if ('v1.0.1' > getVersion('version_themeusers') && !empty($globalConfig['web_users_switch'])) {
            $is_syn_theme_users = 1;
        } else {
            $is_syn_theme_users = 0;
        }
        $this->assign('is_syn_theme_users', $is_syn_theme_users);
        return $this->fetch();
    }
    public function welcome()
    {
        $globalConfig = $this->params['global'];
        /*百度分享*/
        /*        $share = array(
                    'bdText'    => $globalConfig['web_title'],
                    'bdPic'     => is_http_url($globalConfig['web_logo']) ? $globalConfig['web_logo'] : $this->request->domain().$globalConfig['web_logo'],
                    'bdUrl'     => $globalConfig['web_basehost'],
                );
                $this->assign('share',$share);*/
        /*--end*/
        // 纠正上传附件的大小，始终以空间大小为准
        $file_size = $globalConfig['file_size'];
        $maxFileupload = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 0;
        $maxFileupload = intval($maxFileupload);
        if (empty($file_size) || $file_size > $maxFileupload) {
            ConfigModel::tpCache('basic', ['file_size' => $maxFileupload]);
        }
        /*未备份数据库提示*/
        $system_explanation_welcome = !empty($globalConfig['system_explanation_welcome']) ? $globalConfig['system_explanation_welcome'] : 0;
        $sqlfiles = glob(root_path('data') . 'sqldata/*');
        foreach ($sqlfiles as $file) {
            if (stristr($file, getCmsVersion())) {
                $system_explanation_welcome = 1;
            }
        }
        $this->assign('system_explanation_welcome', $system_explanation_welcome);
        /*--end*/
        /*检查密码复杂度*/
        $admin_login_pwdlevel = -1;
        $system_explanation_welcome_2 = !empty($globalConfig['system_explanation_welcome_2']) ? $globalConfig['system_explanation_welcome_2'] : 0;
        if (empty($system_explanation_welcome_2)) {
            $admin_login_pwdlevel = session('admin_login_pwdlevel');
            if (!session('?admin_login_pwdlevel') || 3 < intval($admin_login_pwdlevel)) {
                $system_explanation_welcome_2 = 1;
            }
        }
        $this->assign('admin_login_pwdlevel', $admin_login_pwdlevel);
        $this->assign('system_explanation_welcome_2', $system_explanation_welcome_2);
        /*end*/
        // 同步导航与内容统计的状态
        $this->synOpenQuickmenu();
        // 快捷导航
        $quickMenu = Db::name('quickentry')->where(['type' => 1, 'checked' => 1, 'status' => 1])->order('sort_order asc, id asc')->getArray();
        foreach ($quickMenu as $key => $val) {
            $quickMenu[$key]['vars'] = !empty($val['vars']) ? $val['vars'] : "";
        }
        $this->assign('quickMenu', $quickMenu);
        // 内容统计
        $contentTotal = $this->contentTotalList();
        $this->assign('contentTotal', $contentTotal);
        // 服务器信息
        $this->assign('sys_info', $this->getSysInfo());
        // 升级弹窗
        $this->assign('web_show_popup_upgrade', $globalConfig['web_show_popup_upgrade']);
        // 升级系统时，同时处理sql语句
        $this->synExecuteSql();
        $ajaxLogic = new \app\admin\logic\AjaxLogic();
        $ajaxLogic->updateTemplate('users');
        // 升级前台会员中心的模板文件
        $ajaxLogic->synGuestbookAttribute();
        // 只同步一次每个留言栏目的字段列表前4个显示(v1.5.0节点去掉)
        return $this->fetch();
    }
    /**
     * 升级系统时，同时处理sql语句
     * @return [type] [description]
     */
    private function synExecuteSql()
    {
        // 新增订单提醒的邮箱模板
        if (!ConfigModel::tpCache('system.system_smtp_tpl_5')) {
            $r = Db::name('smtp_tpl')->insert(['tpl_name' => '订单提醒', 'tpl_title' => '您有新的订单消息，请查收！', 'tpl_content' => '${content}', 'send_scene' => 5, 'is_open' => 1, 'add_time' => getTime()]);
            false !== $r && ConfigModel::tpCache('system', ['system_smtp_tpl_5' => 1]);
        }
    }
    /**
     * 内容统计管理
     */
    public function ajaxContentTotal()
    {
        if (isAjaxPost(false)) {
            $checkedids = input('post.checkedids/a', []);
            $ids = input('post.ids/a', []);
            $saveData = [];
            foreach ($ids as $key => $val) {
                if (in_array($val, $checkedids)) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $saveData[$key] = ['id' => $val, 'checked' => $checked, 'sort_order' => intval($key) + 1, 'update_time' => getTime()];
            }
            if (!empty($saveData)) {
                $r = model('Quickentry')->saveAll($saveData);
                if ($r) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }
        /*同步v1.3.9以及早期版本的自定义模型*/
        $this->synCustomQuickmenu(2);
        /*end*/
        $totalList = Db::name('quickentry')->where('type', 2)->where('status', 1)->order('sort_order asc, id asc')->getArray();
        $this->assign('totalList', $totalList);
        return $this->fetch();
    }
    /**
     * 内容统计 - 数量处理
     */
    private function contentTotalList()
    {
        $archivesTotalRow = null;
        $quickentryList = Db::name('quickentry')->where(['type' => 2, 'checked' => 1, 'status' => 1])->order('sort_order asc, id asc')->getArray();
        foreach ($quickentryList as $key => $val) {
            $code = $val['controller'] . '@' . $val['action'] . '@' . $val['vars'];
            $quickentryList[$key]['vars'] = !empty($val['vars']) ? $val['vars'] : "";
            if ($code == 'Guestbook@index@channel=8') {
                // 留言列表
                $map = [];
                $quickentryList[$key]['total'] = Db::name('guestbook')->where($map)->count();
            } else {
                if (1 == $val['groups']) {
                    // 模型内容统计
                    if (null === $archivesTotalRow) {
                        $archivesTotalRow = Db::name('archives')->field('channel, count(aid) as total')->where(['status' => 1, 'is_del' => 0])->group('channel')->getAllWithIndex('channel');
                    }
                    parse_str($val['vars'], $vars);
                    $total = !empty($archivesTotalRow[$vars['channel']]['total']) ? intval($archivesTotalRow[$vars['channel']]['total']) : 0;
                    $quickentryList[$key]['total'] = $total;
                } else {
                    if ($code == 'AdPosition@index@') {
                        // 广告
                        $map = ['is_del' => 0];
                        $quickentryList[$key]['total'] = Db::name('ad_position')->where($map)->count();
                    } else {
                        if ($code == 'Links@index@') {
                            // 友情链接
                            $map = [];
                            $quickentryList[$key]['total'] = Db::name('links')->where($map)->count();
                        } else {
                            if ($code == 'Tags@index@') {
                                // Tags标签
                                $map = [];
                                $quickentryList[$key]['total'] = Db::name('tagindex')->where($map)->count();
                            } else {
                                if ($code == 'Member@users_index@') {
                                    // 会员
                                    $map = ['is_del' => 0];
                                    $quickentryList[$key]['total'] = Db::name('users')->where($map)->count();
                                } else {
                                    if ($code == 'Shop@index@') {
                                        // 订单
                                        $map = [];
                                        $quickentryList[$key]['total'] = Db::name('shop_order')->where($map)->count();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $quickentryList;
    }
    /**
     * 快捷导航管理
     */
    public function ajaxQuickmenu()
    {
        if (isAjaxPost(false)) {
            $checkedids = input('post.checkedids/a', []);
            $ids = input('post.ids/a', []);
            $saveData = [];
            foreach ($ids as $key => $val) {
                if (in_array($val, $checkedids)) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $saveData[$key] = ['id' => $val, 'checked' => $checked, 'sort_order' => intval($key) + 1, 'update_time' => getTime()];
            }
            if (!empty($saveData)) {
                $r = model('Quickentry')->saveAll($saveData);
                if ($r) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }
        /*同步v1.3.9以及早期版本的自定义模型*/
        $this->synCustomQuickmenu(1);
        /*end*/
        $menuList = Db::name('quickentry')->where(['type' => 1, 'groups' => 0, 'status' => 1])->order('sort_order asc, id asc')->getArray();
        $this->assign('menuList', $menuList);
        return $this->fetch();
    }
    /**
     * 同步自定义模型的快捷导航
     */
    private function synCustomQuickmenu($type = 1)
    {
        $row = Db::name('quickentry')->where(['controller' => 'Custom', 'type' => $type])->count();
        if (empty($row)) {
            $customRow = Db::name('channeltype')->field('id,ntitle')->where(['ifsystem' => 0])->getArray();
            $saveData = [];
            foreach ($customRow as $key => $val) {
                $saveData[] = ['title' => $val['ntitle'], 'laytext' => $val['ntitle'] . '列表', 'type' => $type, 'controller' => 'Custom', 'action' => 'index', 'vars' => 'channel=' . $val['id'], 'groups' => 1, 'sort_order' => 100, 'add_time' => getTime(), 'update_time' => getTime()];
            }
            model('Quickentry')->saveAll($saveData);
        }
    }
    /**
     * 同步受开关控制的导航和内容统计
     */
    private function synOpenQuickmenu()
    {
        $tpcacheConfig = $this->params['global'];
        $usersConfig = UsersConfigModel::getUsersConfigData('all');
        /*商城中心 - 受本身开关和会员中心开关控制*/
        if (!empty($tpcacheConfig['web_users_switch']) && !empty($usersConfig['shop_open'])) {
            $shop_open = 1;
        } else {
            $shop_open = 0;
        }
        /*end*/
        $saveData = [['id' => 31, 'status' => !empty($tpcacheConfig['web_users_switch']) ? 1 : 0, 'update_time' => getTime()], ['id' => 32, 'status' => 1 == $tpcacheConfig['web_weapp_switch'] ? 1 : 0, 'update_time' => getTime()], ['id' => 33, 'status' => !empty($tpcacheConfig['web_users_switch']) ? 1 : 0, 'update_time' => getTime()], ['id' => 34, 'status' => $shop_open, 'update_time' => getTime()], ['id' => 35, 'status' => $shop_open, 'update_time' => getTime()]];
        model('Quickentry')->saveAll($saveData);
        /*处理模型导航和统计*/
        $channeltypeRow = Db::name('channeltype')->cache(true, CACHE_TIME, "channeltype")->getArray();
        foreach ($channeltypeRow as $key => $val) {
            $updateData = ['groups' => 1, 'vars' => 'channel=' . $val['id'], 'status' => $val['status'], 'update_time' => getTime()];
            Db::name('quickentry')->where(['vars' => 'channel=' . $val['id']])->update($updateData);
        }
        /*end*/
    }
    /**
     * 服务器信息
     */
    private function getSysInfo()
    {
        $sys_info['os'] = PHP_OS;
        $sys_info['zlib'] = function_exists('gzclose') ? 'YES' : '<font color="red">NO（请开启 php.ini 中的php-zlib扩展）</font>';
        //zlib
        $sys_info['safe_mode'] = (bool) ini_get('safe_mode') ? 'YES' : 'NO';
        //safe_mode = Off
        $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl'] = function_exists('curl_init') ? 'YES' : '<font color="red">NO（请开启 php.ini 中的php-curl扩展）</font>';
        $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv'] = phpversion();
        $sys_info['ip'] = $this->request->serverIP();
        $sys_info['postsize'] = @ini_get('file_uploads') ? ini_get('post_max_size') : '未知';
        $sys_info['fileupload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : '未开启';
        $sys_info['max_ex_time'] = @ini_get("max_execution_time") . 's';
        //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain'] = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit'] = ini_get('memory_limit');
        $sys_info['version'] = $this->params['version'];
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        if (function_exists("gd_info")) {
            $gd = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }
        if (extension_loaded('zip')) {
            $sys_info['zip'] = "YES";
        } else {
            $sys_info['zip'] = '<font color="red">NO（请开启 php.ini 中的php-zip扩展）</font>';
        }
        $sys_info['curent_version'] = $this->params['version'];
        //当前程序版本
        $sys_info['web_name'] = ConfigModel::tpCache('global.web_name');
        return $sys_info;
    }
    /**
     * 录入商业授权
     */
    public function authortoken()
    {
        $domain = config('service_ey');
        $domain = base64_decode($domain);
        $vaules = array('client_domain' => urldecode($this->request->host(true)));
        $url = $domain . '/index.php?m=api&c=Service&a=check_authortoken&' . http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => 3, 'method' => 'GET')));
        $response = @file_get_contents($url, false, $context);
        $params = json_decode($response, true);
        if (false === $response || is_array($params) && 1 == $params['code']) {
            $web_authortoken = $params['msg'];
            ConfigModel::tpCache('web', array('web_authortoken' => $web_authortoken));
            $source = realpath('/static/admin/images/logo_ey.png');
            $destination = realpath('/static/admin/images/logo.png');
            @copy($source, $destination);
            delFile(RUNTIME_PATH . 'html');
            // 清空缓存页面
            session('isset_author', null);
            adminLog('验证商业授权');
            $this->success('域名授权成功', request()->baseFile(), '', 1, [], '_parent');
        }
        $this->error('域名（' . $this->request->domain() . '）未授权', request()->baseFile(), '', 3, [], '_parent');
    }
    /**
     * 更换后台logo
     */
    public function editAdminlogo()
    {
        $filename = input('param.filename/s', '');
        if (!empty($filename)) {
            $source = realpath(preg_replace('#^' . $this->root_dir . '/#i', '', $filename));
            // 支持子目录
            $web_is_authortoken = ConfigModel::tpCache('web.web_is_authortoken');
            if (empty($web_is_authortoken)) {
                $destination = realpath('/static/admin/images/logo.png');
            } else {
                $destination = realpath('/static/admin/images/logo_ey.png');
            }
            if (@copy($source, $destination)) {
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }
    /**
     * 待处理事项
     */
    public function pendingMatters()
    {
        $html = '<div style="text-align: center; margin: 20px 0px; color:red;">惹妹子生气了，没啥好处理！</div>';
        echo $html;
    }
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal()
    {
        if (isAjaxPost(false)) {
            $url = null;
            $data = ['refresh' => 0];
            $param = input('param.');
            $table = input('param.table/s');
            // 表名
            $id_name = input('param.id_name/s');
            // 表主键id名
            $id_value = input('param.id_value/d');
            // 表主键id值
            $field = input('param.field/s');
            // 修改哪个字段
            $value = input('param.value/s', '', null);
            // 修改字段值
            $value = eyPreventShell($value) ? $value : strip_sql($value);
            /*插件专用*/
            if ('weapp' == $table) {
                if (1 == intval($value)) {
                    // 启用
                    action('Weapp/enable', ['id' => $id_value]);
                } else {
                    if (-1 == intval($value)) {
                        // 禁用
                        action('Weapp/disable', ['id' => $id_value]);
                    }
                }
            }
            /*end*/
            /*处理数据的安全性*/
            if (empty($id_value)) {
                $this->error('查询条件id不合法！');
            }
            foreach ($param as $key => $val) {
                if ('value' == $key) {
                    continue;
                }
                if (!preg_match('/^([A-Za-z0-9_-]*)$/i', $val)) {
                    $this->error('数据含有非法入侵字符！');
                }
            }
            /*end*/
            switch ($table) {
                // 会员等级表
                case 'users_level':
                    $return = model('UsersLevel')->isRequired($id_name, $id_value, $field, $value);
                    if (is_array($return)) {
                        $this->error($return['msg']);
                    }
                    break;
                // 会员属性表
                case 'users_parameter':
                    $return = model('UsersParameter')->isRequired($id_name, $id_value, $field, $value);
                    if (is_array($return)) {
                        $this->error($return['msg']);
                    }
                    break;
                // 会员中心菜单表
                case 'users_menu':
                    if ('is_userpage' == $field) {
                        Db::name('users_menu')->where('id', 'gt', 0)->update(['is_userpage' => 0, 'update_time' => getTime()]);
                    }
                    $data['refresh'] = 1;
                    break;
                // 会员投稿功能
                case 'archives':
                    if ('arcrank' == $field) {
                        if (0 == $value) {
                            $value = -1;
                        } else {
                            $value = 0;
                        }
                    }
                    break;
                // 会员产品类型表
                case 'users_type_manage':
                    if (empty($value)) {
                        $this->error('不可为空');
                    }
                    break;
                // 留言属性表
                case 'guestbook_attribute':
                    $return = model('GuestbookAttribute')->isValidate($id_name, $id_value, $field, $value);
                    if (is_array($return)) {
                        $time = !empty($return['time']) ? $return['time'] : 3;
                        $this->error($return['msg'], null, [], $time);
                    }
                    break;
                // 小程序页面表
                case 'minipro_page':
                    $re = Db::name('minipro_page')->where(['is_home' => 1, $id_name => ['EQ', $id_value]])->count();
                    if (!empty($re)) {
                        $this->error('禁止取消默认项', null, [], 3);
                    }
                    break;
                default:
                    # code...
                    break;
            }
            $savedata = [$field => $value, 'update_time' => getTime()];
            $r = Db::name($table)->where([$id_name => $id_value])->cache(true, null, $table)->save($savedata);
            // 根据条件保存修改的数据
            if ($r !== false) {
                // 以下代码可以考虑去掉，与行为里的清除缓存重复 AppEndBehavior.php / clearHtmlCache
                switch ($table) {
                    case 'auth_modular':
                        extra_cache('admin_auth_modular_list_logic', null);
                        extra_cache('admin_all_menu', null);
                        break;
                    case 'minipro_page':
                        if ('is_home' == $field) {
                            $data['refresh'] = 1;
                            Db::name('minipro_page')->where([$id_name => ['NEQ', $id_value]])->update(['is_home' => 0, 'update_time' => getTime()]);
                        }
                        break;
                    default:
                        // 清除logic逻辑定义的缓存
                        extra_cache('admin_' . $table . '_list_logic', null);
                        // 清除一下缓存
                        // delFile(RUNTIME_PATH.'html'); // 先清除缓存, 否则不好预览
                        \think\Cache::clear($table);
                        break;
                }
                $this->success('更新成功', $url, $data);
            }
            $this->error('更新失败', null, []);
        }
    }
    /**
     * 功能开关
     */
    public function switchMap()
    {
        if (isPost()) {
            $inc_type = input('post.inc_type/s');
            $name = input('post.name/s');
            $value = input('post.value/s');
            $data = [];
            switch ($inc_type) {
                case 'pay':
                case 'shop':
                    UsersConfigModel::getUsersConfigData($inc_type, [$name => $value]);
                    // 开启商城
                    if (1 == $value) {
                        // 同时开启会员中心
                        ConfigModel::tpCache('web', ['web_users_switch' => 1]);
                        // 同时显示发布文档时的价格文本框
                        Db::name('channelfield')->where(['name' => 'users_price', 'channel_id' => 2])->update(['ifeditable' => 1, 'update_time' => getTime()]);
                    }
                    if (in_array($name, ['shop_open'])) {
                        // $data['reload'] = 1;
                        /*检测是否存在订单中心模板*/
                        if ('v1.0.1' > getVersion('version_themeshop') && !empty($value)) {
                            $is_syn = 1;
                        } else {
                            $is_syn = 0;
                        }
                        $data['is_syn'] = $is_syn;
                        /*--end*/
                        // 同步会员中心的左侧菜单
                        if ('shop_open' == $name) {
                            Db::name('users_menu')->where(['mca' => 'user/Shop/shop_centre'])->update(['status' => 1 == $value ? 1 : 0, 'update_time' => getTime()]);
                        }
                    } else {
                        if ('pay_open' == $name) {
                            // 同步会员中心的左侧菜单
                            Db::name('users_menu')->where(['mca' => 'user/Pay/pay_consumer_details'])->update(['status' => 1 == $value ? 1 : 0, 'update_time' => getTime()]);
                        }
                    }
                    break;
                case 'users':
                    // 会员投稿
                    $r = Db::name('users_menu')->where(['mca' => 'user/UsersRelease/release_centre'])->update(['status' => 1 == $value ? 1 : 0, 'update_time' => getTime()]);
                    if ($r) {
                        UsersConfigModel::getUsersConfigData($inc_type, [$name => $value]);
                        if (1 == $value) {
                            // 同时开启会员中心
                            ConfigModel::tpCache('web', ['web_users_switch' => 1]);
                        }
                    }
                    break;
                case 'level':
                    // 会员升级
                    $r = Db::name('users_menu')->where(['mca' => 'user/Level/level_centre'])->update(['status' => 1 == $value ? 1 : 0, 'update_time' => getTime()]);
                    if ($r) {
                        UsersConfigModel::getUsersConfigData($inc_type, [$name => $value]);
                        if (1 == $value) {
                            // 同时开启会员中心
                            ConfigModel::tpCache('web', ['web_users_switch' => 1]);
                        }
                    }
                    break;
                case 'web':
                    ConfigModel::tpCache($inc_type, [$name => $value]);
                    if (in_array($name, ['web_users_switch'])) {
                        // $data['reload'] = 1;
                        // 检测是否存在会员中心模板
                        if ('v1.0.1' > getVersion('version_themeusers') && !empty($value)) {
                            $is_syn = 1;
                        } else {
                            $is_syn = 0;
                        }
                        $data['is_syn'] = $is_syn;
                    }
                    break;
            }
            $this->success('操作成功', null, $data);
        }
        $globalConfig = $this->params['global'];
        $this->assign('globalConfig', $globalConfig);
        $UsersConfigData = UsersConfigModel::getUsersConfigData('all');
        $this->assign('userConfig', $UsersConfigData);
        $is_online = 0;
        if (is_realdomain()) {
            $is_online = 1;
        }
        $this->assign('is_online', $is_online);
        // 检测是否存在会员中心模板
        if ('v1.0.1' > getVersion('version_themeusers')) {
            $is_themeusers_exist = 1;
        } else {
            $is_themeusers_exist = 0;
        }
        $this->assign('is_themeusers_exist', $is_themeusers_exist);
        // 检测是否存在商城中心模板
        if ('v1.0.1' > getVersion('version_themeshop')) {
            $is_themeshop_exist = 1;
        } else {
            $is_themeshop_exist = 0;
        }
        $this->assign('is_themeshop_exist', $is_themeshop_exist);
        $this->assign('is_thinker_authortoken', null);
        return $this->fetch();
    }
}