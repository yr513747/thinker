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
// [ Ajax异步类 ]
// --------------------------------------------------------------------------
namespace app\api\controller;

use think\facade\Db;
use think\Container;
class Ajax extends BaseController
{
    /**
     * 内容页浏览量的自增接口
     * @access public
     * @return mixed
     */
    public function arcclick()
    {
        if (isAjax()) {
            $aid = input('aid/d', 0);
            $type = input('type/s', '');
            if ($aid > 0) {
                if ('view' == $type) {
                    Db::name('archives')->where('aid', $aid)->inc('click', 1)->update();
                }
                $click = Db::name('archives')->where('aid', $aid)->getField('click');
            } else {
                $click = 0;
            }
            return (int) $click;
        }
    }
    /**
     * 文档下载次数
     * @access public
     * @return mixed
     */
    public function downcount()
    {
        if (isAjax()) {
            $aid = input('aid/d', 0);
            if ($aid > 0) {
                $downcount = Db::name('archives')->where('aid', $aid)->getField('downcount');
            } else {
                $downcount = 0;
            }
            return (int) $downcount;
        }
    }
    /**
     * arclist列表分页arcpagelist标签接口
     * @access public
     * @return mixed
     */
    public function arcpagelist()
    {
        $pnum = input('page/d', 0);
        $pagesize = input('pagesize/d', 0);
        $tagid = input('tagid/s', '');
        $tagidmd5 = input('tagidmd5/s', '');
        !empty($tagid) && ($tagid = preg_replace("/[^a-zA-Z0-9-_]/", '', $tagid));
        !empty($tagidmd5) && ($tagidmd5 = preg_replace("/[^a-zA-Z0-9_]/", '', $tagidmd5));
        if (empty($tagid) || empty($pnum) || empty($tagidmd5)) {
            return $this->error('参数有误');
        }
        $data = ['code' => 1, 'msg' => '', 'lastpage' => 0];
        $arcmultiRow = Db::name('arcmulti')->where(['tagid' => $tagidmd5])->getOne();
        if (!empty($arcmultiRow) && !empty($arcmultiRow['querysql'])) {
            // arcpagelist标签属性pagesize优先级高于arclist标签属性pagesize
            if (0 < intval($pagesize)) {
                $arcmultiRow['pagesize'] = $pagesize;
            }
            // 取出属性并解析为变量
            $attarray = unserialize(stripslashes($arcmultiRow['attstr']));
            // extract($attarray, EXTR_SKIP); // 把数组中的键名直接注册为了变量
            // 通过页面及总数解析当前页面数据范围
            $pnum < 2 && ($pnum = 2);
            $strnum = intval($attarray['row']) + ($pnum - 2) * $arcmultiRow['pagesize'];
            // 拼接完整的SQL
            $querysql = preg_replace('#LIMIT(\\s+)(\\d+)(,\\d+)?#i', '', $arcmultiRow['querysql']);
            $querysql = preg_replace('#SELECT(\\s+)(.*)(\\s+)FROM#i', 'SELECT COUNT(*) AS totalNum FROM', $querysql);
            $queryRow = Db::query($querysql);
            if (!empty($queryRow)) {
                $tpl_content = '';
                $filename = root_path('view') . $this->theme_style . 'system' . DS . 'arclist_' . $tagid . '.' . $this->view_suffix;
                if (!file_exists($filename)) {
                    $data['code'] = -1;
                    $data['msg'] = "模板追加文件 arclist_{$tagid}.htm 不存在！";
                    return $this->error("标签模板不存在", null, $data);
                } else {
                    $tpl_content = @file_get_contents($filename);
                }
                if (empty($tpl_content)) {
                    $data['code'] = -1;
                    $data['msg'] = "模板追加文件 arclist_{$tagid}.htm 没有HTML代码！";
                    return $this->error("标签模板不存在", null, $data);
                }
                /*拼接完整的arclist标签语法*/
                $offset = intval($strnum);
                $row = intval($offset) + intval($arcmultiRow['pagesize']);
                $innertext = "{arclist";
                foreach ($attarray as $key => $val) {
                    if (in_array($key, ['tagid', 'offset', 'row'])) {
                        continue;
                    }
                    $innertext .= " {$key}='{$val}'";
                }
                $innertext .= " limit='{$offset},{$row}'}";
                $innertext .= $tpl_content;
                $innertext .= "{/arclist}";
                /*--end*/
                $data['msg'] = $this->display($innertext);
                // 是否到了最终页
                if (!empty($queryRow[0]['totalNum']) && $queryRow[0]['totalNum'] <= $row) {
                    $data['lastpage'] = 1;
                }
            } else {
                $data['lastpage'] = 1;
            }
        }
        return $this->success('请求成功', null, $data);
    }
    /**
     * 检验会员登录
     * @access public
     * @return mixed
     */
    public function checkUser()
    {
        if (isAjax()) {
            $type = input('param.type/s', 'default');
            $img = input('param.img/s');
            $users_id = session('users_id');
            if ('login' == $type) {
                if (!empty($users_id)) {
                    $currentstyle = input('param.currentstyle/s');
                    $users = Db::name('users')->field('username,nickname,head_pic')->where(['users_id' => $users_id])->getOne();
                    if (!empty($users)) {
                        $nickname = $users['nickname'];
                        if (empty($nickname)) {
                            $nickname = $users['username'];
                        }
                        $head_pic = get_head_pic($users['head_pic']);
                        if ('on' == $img) {
                            $users['html'] = "<img class='{$currentstyle}' alt='{$nickname}' src='{$head_pic}' />";
                        } else {
                            $users['html'] = $nickname;
                        }
                        $users['is_login'] = 1;
                        return $this->success('请求成功', null, $users);
                    }
                }
                return $this->success('请先登录', null, ['is_login' => 0]);
            } elseif ('reg' == $type || 'register' == $type) {
                if (!empty($users_id)) {
                    $users['is_login'] = 1;
                } else {
                    $users['is_login'] = 0;
                }
                return $this->success('请求成功', null, $users);
            } elseif ('logout' == $type) {
                if (!empty($users_id)) {
                    $users['is_login'] = 1;
                } else {
                    $users['is_login'] = 0;
                }
                return $this->success('请求成功', null, $users);
            } elseif ('cart' == $type) {
                if (!empty($users_id)) {
                    $users['is_login'] = 1;
                    $users['cart_num_time'] = Db::name('shop_cart')->where(['users_id' => $users_id])->sum('product_num');
                } else {
                    $users['is_login'] = 0;
                    $users['cart_num_time'] = 0;
                }
                return $this->success('请求成功', null, $users);
            }
        }
        return $this->error('访问错误');
    }
    /**
     * 获取用户信息
     * @access public
     * @return mixed
     */
    public function getTagUserInfo()
    {
        $t_uniqid = input('param.t_uniqid/s', '');
        if (isAjax() && !empty($t_uniqid)) {
            $users_id = session('users_id');
            if (!empty($users_id)) {
                $users = Db::name('users')
				->field('b.*, a.*')
				->alias('a')
				->join('users_level b', 'a.level = b.level_id', 'LEFT')
				->where(['a.users_id' => $users_id])
				->getOne();
                if (!empty($users)) {
                    $users['reg_time'] = MyDate('Y-m-d H:i:s', $users['reg_time']);
                    $users['update_time'] = MyDate('Y-m-d H:i:s', $users['update_time']);
                } else {
                    $users = [];
                    $tableFields1 = Db::name('users')->getTableFields();
                    $tableFields2 = Db::name('users_level')->getTableFields();
                    $tableFields = array_merge($tableFields1, $tableFields2);
                    foreach ($tableFields as $key => $val) {
                        $users[$val] = '';
                    }
                }
                $users['url'] = url('user/Users/centre');
                unset($users['password']);
                unset($users['paypwd']);
                $dtypes = [];
                foreach ($users as $key => $val) {
                    $html_key = md5($key . '-' . $t_uniqid);
                    $users[$html_key] = $val;
                    $dtype = 'txt';
                    if (in_array($key, ['head_pic'])) {
                        $dtype = 'img';
                    } elseif (in_array($key, ['url'])) {
                        $dtype = 'href';
                    }
                    $dtypes[$html_key] = $dtype;
                    unset($users[$key]);
                }
                $data = ['is_login' => 1, 'users' => $users, 'dtypes' => $dtypes];
                return $this->success('请求成功', null, $data);
            }
            return $this->success('请先登录', null, ['is_login' => 0]);
        }
        return $this->error('访问错误');
    }
    /**
     * 验证码获取
     * @access public
     * @return mixed
     */
    public function captcha()
    {
        $type = input('param.type/s', 'default');
        $configList = config('captcha');
        $captchaArr = array_keys($configList);
        if (in_array($type, $captchaArr)) {
            // 验证码插件开关
            $admin_login_captcha = config('captcha.' . $type);
            $config = !empty($admin_login_captcha['is_on']) && !empty($admin_login_captcha['config']) ? $type . '.config' : 'default';
            return captcha($config);
        }
        return captcha();
    }
    /**
     * 邮箱发送
     * @access public
     * @return mixed
     */
    public function sendEmail()
    {
        // 超时后，断掉邮件发送
        function_exists('set_time_limit') && set_time_limit(10);
        $type = input('param.type/s');
        // 留言发送邮件
        if (isAjaxPost(false) && 'gbook_submit' == $type) {
            $tid = input('param.tid/d');
            $aid = input('param.aid/d');
            $send_email_scene = config('email.send_email_scene');
            $scene = $send_email_scene[1]['scene'];
            $web_name = $this->params['global']['web_name'];
            // 判断标题拼接
            $arctype = Db::name('arctype')->field('typename')->getOne($tid);
            $web_name = $arctype['typename'] . '-' . $web_name;
            // 拼装发送的字符串内容
            $row = Db::name('guestbook_attribute')
			->field('a.attr_name, b.attr_value')
			->alias('a')
			->join('guestbook_attr b', 'a.attr_id = b.attr_id AND a.typeid = ' . $tid, 'LEFT')
			->where(['b.aid' => $aid])
			->order('a.attr_id sac')
			->getArray();
            $content = '';
            foreach ($row as $key => $val) {
                if (preg_match('/(\\.(jpg|gif|png|bmp|jpeg|ico|webp))$/i', $val['attr_value'])) {
                    if (!stristr($val['attr_value'], '|')) {
                        $val['attr_value'] = $this->request->domain() . handle_subdir($val['attr_value']);
                        $val['attr_value'] = "<a href='" . $val['attr_value'] . "' target='_blank'><img src='" . $val['attr_value'] . "' width='150' height='150' /></a>";
                    }
                } else {
                    $val['attr_value'] = str_replace(PHP_EOL, ' | ', $val['attr_value']);
                }
                $content .= $val['attr_name'] . '：' . $val['attr_value'] . '<br/>';
            }
            $html = "<p style='text-align: left;'>{$web_name}</p><p style='text-align: left;'>{$content}</p>";
            if (isMobile()) {
                $html .= "<p style='text-align: left;'>——来源：移动端</p>";
            } else {
                $html .= "<p style='text-align: left;'>——来源：电脑端</p>";
            }
            // 发送邮件
            $res = send_email(null, null, $html, $scene);
            if (intval($res['code']) == 1) {
                return $this->success($res['msg']);
            } else {
                return $this->error($res['msg']);
            }
        }
    }
    /**
     * 判断文章内容阅读权限
     * @access public
     * @return mixed
     */
    public function getArcrank()
    {
        $aid = input('param.aid/d');
        if (!empty($aid)) {
            // 是否允许
            $arcrank = 0;
            // 用户ID
            $users_id = session('users_id');
            // 文章查看所需等级值
            $Arcrank = Db::name('archives')
			->alias('a')
			->field('a.users_id, a.arcrank, b.level_value, b.level_name')
			->join('users_level b', 'a.arcrank = b.level_value', 'LEFT')
			->where(['a.aid' => $aid])
			->getOne();
            if (!empty($users_id)) {
                // 会员级别等级值
                $UsersData = Db::name('users')
				->alias('a')
				->field('a.users_id,b.level_value,b.level_name')
				->join('users_level b', 'a.level = b.level_id', 'LEFT')
				->where(['a.users_id' => $users_id])
				->getOne();
                if (0 == $Arcrank['arcrank']) {
                    $arcrank = 1;
                    $msg = '允许查阅！';
                } elseif (-1 == $Arcrank['arcrank']) {
                    if ($users_id == $Arcrank['users_id']) {
                        $arcrank = 1;
                        $msg = '允许查阅！';
                    } else {
                        $arcrank = 0;
                        $msg = '待审核稿件，你没有权限阅读！';
                    }
                } elseif ($UsersData['level_value'] < $Arcrank['level_value']) {
                    $arcrank = 0;
                    $msg = '内容需要【' . $Arcrank['level_name'] . '】才可以查看，您为【' . $UsersData['level_name'] . '】，请先升级！';
                } else {
                    $arcrank = 1;
                    $msg = '允许查阅！';
                }
            } else {
                if (0 == $Arcrank['arcrank']) {
                    $arcrank = 1;
                    $msg = '允许查阅！';
                } elseif (-1 == $Arcrank['arcrank']) {
                    $arcrank = 0;
                    $msg = '待审核稿件，你没有权限阅读！';
                } elseif (!empty($Arcrank['level_name'])) {
                    $arcrank = 0;
                    $msg = '文章需要【' . $Arcrank['level_name'] . '】才可以查看，游客不可查看，请登录！';
                } else {
                    $arcrank = 0;
                    $msg = '游客不可查看，请登录！';
                }
            }
            if ($arcrank === 1) {
                if (isAjax()) {
                    return $this->success($msg);
                }
                return null;
            } else {
                if (isAjax()) {
                    return $this->error($msg);
                }
                return $msg;
            }
        }
    }
    /**
     * 获取会员列表
     * @access public
     * @return mixed
     */
    public function getTagMemberlist()
    {
        if (isAjaxPost(false)) {
            $data = array();
            $htmlcode = input('post.htmlcode/s');
            $htmlcode = htmlspecialchars_decode($htmlcode);
            $htmlcode = preg_replace('/<\\?(\\s*)php(\\s+)/i', '', $htmlcode);
            $attarray = input('post.attarray/s');
            $attarray = htmlspecialchars_decode($attarray);
            $attarray = json_decode(base64_decode($attarray));
            /*拼接完整的memberlist标签语法*/
            $thinker = Container::factory('\\Thinker\\template\\taglib\\Thinker');
            $tagsList = $thinker->getTags();
            $tagsAttr = $tagsList['memberlist'];
            $innertext = "{memberlist";
            foreach ($attarray as $key => $val) {
                if (!in_array($key, $tagsAttr) || in_array($key, ['js'])) {
                    continue;
                }
                $innertext .= " {$key}='{$val}'";
            }
            $innertext .= " js='on'}";
            $innertext .= $htmlcode;
            $innertext .= "{/memberlist}";
            /*--end*/
            $data['msg'] = $this->display($innertext);
            return $this->success('加载成功！', null, $data);
        }
        return $this->error('加载失败！');
    }
}