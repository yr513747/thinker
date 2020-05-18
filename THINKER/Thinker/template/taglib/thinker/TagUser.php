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
// [ 会员中心 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;

class TagUser extends Base
{
    /**
     * 会员ID
     * @var int
     */
    public $users_id = 0;
    //初始化
    protected function init()
    {
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }
    /**
     * 会员信息调用
     * @access public
     * @param  string  $type
     * @param  string  $img
     * @param  string  $currentstyle
     * @param  string  $txt
     * @param  string  $txtid
     * @return mixed
     */
    public function getUser($type = 'default', $img = '', $currentstyle = '', $txt = '', $txtid = '')
    {
        $result = false;
        $web_users_switch = isset($this->params['global']['web_users_switch']) ? $this->params['global']['web_users_switch'] : '';
        $users_open_register = isset($this->params['users']['users_open_register']) ? $this->params['users']['users_open_register'] : '';
        if ('open' == $type) {
            if (empty($web_users_switch) || 1 == $users_open_register) {
                return false;
            }
        }
		// 兼容
		if ($type == 'reg') {
            $type = 'register';
        }
        if (1 == intval($web_users_switch)) {
            if (empty($users_open_register)) {
                $url = '';
                $t_uniqid = '';
                switch ($type) {
                    case 'login':
                    case 'centre':
                    case 'reg':
					case 'register':
                    case 'logout':
                    case 'cart':
                        if ('cart' == $type) {
                            $shop_open = isset($this->params['users']['shop_open']) ? $this->params['users']['shop_open'] : '';
                            if (empty($shop_open)) {
                                return false;
                            }
                            // 关闭商城中心，同时隐藏购物车入口
                            $url = url('user/Shop/shopCartList');
                        } else {
                            $url = url('user/Users/' . $type);
                        }
                        $t_uniqid = md5(getTime() . uniqid(mt_rand(), TRUE));
                        // A标签ID
                        $result['id'] = md5("{$type}_{$this->users_id}_{$t_uniqid}");
                        // A标签里的文案ID
                        $result['txtid'] = !empty($txtid) ? md5($txtid) : md5("{$type}_txt_{$this->users_id}_{$t_uniqid}");
                        // 文字文案
                        $result['txt'] = $txt;
                        // 购物车的数量ID
                        $result['cartid'] = md5("{$type}_cartid_{$this->users_id}_{$t_uniqid}");
                        // IMG标签里的ID
                        // $result['imgid'] = md5("{$type}_img_{$this->users_id}_{$t_uniqid}");
                        // 图片文案
                        $result['img'] = $img;
                        // 链接
                        $result['url'] = $url;
                        // 标签类型
                        $result['type'] = $type;
                        // 图片样式类
                        $result['currentstyle'] = $currentstyle;
                        break;
                    case 'info':
                        $t_uniqid = md5(getTime() . uniqid(mt_rand(), TRUE));
                        $result = $this->getUserInfo();
                        foreach ($result as $key => $val) {
                            $html_key = md5($key . '-' . $t_uniqid);
                            $result[$key] = $html_key;
                        }
                        $result['t_uniqid'] = $t_uniqid;
                        $result['id'] = $t_uniqid;
                        break;
                    case 'open':
                        break;
                    default:
						if (empty($type) && is_string($type)) {
                            $type = explode('/', $type);
                        }
		
	                	if (count($type) !== 3) {
                            return false;
                        }
						$type = implode('/',$type);
						$t_uniqid = md5(getTime() . uniqid(mt_rand(), TRUE));
                        // A标签ID
                        $result['id'] = md5("{$type}_{$this->users_id}_{$t_uniqid}");
						$result['url'] = url($type);
                        break;
                }
                if ('login' == $type) {
                    if (isMobile() && isWeixin()) {
                        // 微信端和小程序则使用这个url
                        $result['url'] = url('user/Users/usersSelectLogin');
                    }
                }
                // 子目录
                $result['root_dir'] = $this->web_root;
				$result['info_ajax_url'] = url('api/Ajax/getTagUserInfo');
				$result['user_ajax_url'] = url('api/Ajax/checkUser');
                $result_json = json_encode($result);
                $version = $this->params['version'];
                $hidden = '';
                switch ($type) {
                    case 'login':
                    case 'reg':
					case 'register':
                    case 'logout':
                    case 'cart':
                        $hidden = <<<EOF
<script type="text/javascript" src="{$this->web_root}/static/common/js/tag_user.js?v={$version}"></script>
<script type="text/javascript">
    var tag_user_result_json = {$result_json};
    tag_user(tag_user_result_json);
</script>
EOF;
                        break;
                    case 'info':
                        $hidden = <<<EOF
<script type="text/javascript" src="{$this->web_root}/static/common/js/tag_user.js?v={$version}"></script>
<script type="text/javascript">
    var tag_user_result_json = {$result_json};
    tag_user_info(tag_user_result_json);
</script>
EOF;
                        break;
                }
                $result['hidden'] = $hidden;
            }
        }
        return $result;
    }
    /**
     * 获取用户信息
     * @access private
     * @return array
     */
    private function getUserInfo()
    {
        $users = [];
        $tableFields1 = Db::name('users')->getTableFields();
        $tableFields2 = Db::name('users_level')->getTableFields();
        $tableFields = array_merge($tableFields1, $tableFields2);
        foreach ($tableFields as $key => $val) {
            $users[$val] = '';
        }
        $users['url'] = '';
        unset($users['password']);
        unset($users['paypwd']);
        return $users;
    }
}