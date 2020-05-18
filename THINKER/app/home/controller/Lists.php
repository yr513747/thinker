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
// [ 列表 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use think\facade\Db;
class Lists extends BaseController
{
    /**
     * 模型标识
     * @var string
     */
    protected $nid = '';
    /**
     * 模型ID
     * @var string
     */
    protected $channel = '';
    /**
     * 栏目列表
     */
    public function index($tid = '')
    {
        $param = input('param.');
        /*获取当前栏目ID以及模型ID*/
        $page_tmp = input('param.page/s', 0);
		
        if (empty($tid) || !is_numeric($page_tmp)) {
            return abort(404, '页面不存在');
        }
        $map = [];
        if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
            $map[] = array('a.dirname', '=', $tid);
        } else {
            $map[] = array('a.id', '=', $tid);
        }
        // 回收站功能
        $map[] = array('a.is_del', '=', 0);
        $row = Db::name('arctype')->field('a.id, a.current_channel, b.nid')->alias('a')->join('channeltype b', 'a.current_channel = b.id', 'LEFT')->where($map)->getOne();
        if (empty($row)) {
            return abort(404, '页面不存在');
        }
        $tid = $row['id'];
        $this->nid = $row['nid'];
        $this->channel = intval($row['current_channel']);
        /*--end*/
        // 模型对应逻辑
        $result = $this->logic($tid);
        $thinker = array('field' => $result);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
       
        // 模板文件
        $viewfile = $filename = !empty($result['templist']) ? str_replace('.' . $this->view_suffix, '', $result['templist']) : 'lists_' . $this->nid;
      
        /*每个栏目内置模板文件名*/
        //$viewfilepath = root_path('view') . $this->theme_style . DS . $filename . "_{$result['id']}." . $this->view_suffix;
//        if (file_exists($viewfilepath)) {
//            $viewfile = $filename . "_{$result['id']}";
//        }
        /*--end*/
        return $this->fetch(":{$viewfile}");
    }
    /**
     * 模型对应逻辑
     * @param intval $tid 栏目ID
     * @return array
     */
    private function logic($tid = '')
    {
        $result = array();
        if (empty($tid)) {
            return $result;
        }
        switch ($this->channel) {
            case '6':
                // 单页模型
                $arctype_info = M('Arctype')->getInfo($tid);
                if ($arctype_info) {
                    // 读取当前栏目的内容，否则读取每一级第一个子栏目的内容，直到有内容或者最后一级栏目为止。
                    $result_new = M('Archives')->readContentFirst($tid);
                    // 阅读权限
                    if ($result_new['arcrank'] == -1) {
                        return $this->success('待审核稿件，你没有权限阅读！');
                    }
                    // 外部链接跳转
                    if ($result_new['is_part'] == 1) {
                        $result_new['typelink'] = htmlspecialchars_decode($result_new['typelink']);
                        if (!is_http_url($result_new['typelink'])) {
                            $typeurl = '//' . $this->request->host();
                            if (!preg_match('#^' . $this->root_dir . '(.*)$#i', $result_new['typelink'])) {
                                $typeurl .= $this->root_dir;
                            }
                            $typeurl .= '/' . trim($result_new['typelink'], '/');
                            $result_new['typelink'] = $typeurl;
                        }
                        return $this->redirect($result_new['typelink']);
                    }
                    // 自定义字段的数据格式处理
                    $result_new = $this->fieldLogic->getChannelFieldList($result_new, $this->channel);
                    $result = array_merge($arctype_info, $result_new);
                    $result['templist'] = !empty($arctype_info['templist']) ? $arctype_info['templist'] : 'lists_' . $arctype_info['nid'];
                    $result['dirpath'] = $arctype_info['dirpath'];
                    $result['typeid'] = $arctype_info['typeid'];
                }
                break;
            default:
                $result = M('Arctype')->getInfo($tid);
                // 外部链接跳转
                if ($result['is_part'] == 1) {
                    $result['typelink'] = htmlspecialchars_decode($result['typelink']);
                    if (!is_http_url($result['typelink'])) {
                        $result['typelink'] = '//' . $this->request->host() . $this->root_dir . '/' . trim($result['typelink'], '/');
                    }
                    return $this->redirect($result['typelink']);
                }
                break;
        }
        if (!empty($result)) {
            // 自定义字段的数据格式处理
            $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
        }
        // 是否有子栏目，用于标记【全部】选中状态
        $result['has_children'] = M('Arctype')->hasChildren($tid);
        // seo
        $result['seo_title'] = set_typeseotitle($result['typename'], $result['seo_title']);
        // 获取当前页面URL
        $result['pageurl'] = typeurl('home/Lists/index',$result);
        // 给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值
        foreach ($result as $key => $val) {
            if (!preg_match('/^type/i', $key)) {
                $key_new = 'type' . $key;
                !array_key_exists($key_new, $result) && ($result[$key_new] = $val);
            }
        }
        return $result;
    }
    /**
     * 留言提交
     */
    public function gbookSubmit()
    {
        $typeid = input('post.typeid/d');
        if (isPost() && !empty($typeid)) {
            $post = input('post.');
            $token = input('__token__');
            
            $ip = $this->request->clientIP();
            // 留言间隔限制
            $channel_guestbook_interval = tpSetting('channel_guestbook.channel_guestbook_interval');
            $channel_guestbook_interval = is_numeric($channel_guestbook_interval) ? intval($channel_guestbook_interval) : 60;
            if (0 < $channel_guestbook_interval) {
                $map = array('ip' => $ip, 'typeid' => $typeid);
                $count = Db::name('guestbook')->where($map)->where('add_time', '>', getTime() - $channel_guestbook_interval)->count('aid');
                if ($count > 0) {
                    return $this->error('同一个IP在' . $channel_guestbook_interval . '秒之内不能重复提交！');
                }
            }
            //判断必填项
            foreach ($post as $key => $value) {
                if (stripos($key, "attr_") !== false) {
                    //处理得到自定义属性id
                    $attr_id = substr($key, 5);
                    $attr_id = intval($attr_id);
                    $ga_data = Db::name('guestbook_attribute')->where(['attr_id' => $attr_id])->getOne();
                    if ($ga_data['required'] == 1 && empty($value)) {
                        return $this->error($ga_data['attr_name'] . '不能为空！');
                    }
                    if ($ga_data['validate_type'] == 1) {
                        $pattern = "/^1\\d{10}\$/";
                        if (!preg_match($pattern, $value)) {
                            return $this->error($ga_data['attr_name'] . '格式不正确！');
                        }
                    } elseif ($ga_data['validate_type'] == 2) {
                        $pattern = "/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,})\$/i";
                        if (preg_match($pattern, $value) == false) {
                            return $this->error($ga_data['attr_name'] . '格式不正确！');
                        }
                    }
                }
            }
            // 处理判断验证码
            $is_vertify = 1;
            // 默认开启验证码
            $guestbook_captcha = config('captcha.guestbook');
            if (!function_exists('imagettftext') || empty($guestbook_captcha['is_on'])) {
                $is_vertify = 0;
                // 函数不存在，不符合开启的条件
            }
            if (1 == $is_vertify) {
                if (empty($post['vertify'])) {
                    return $this->error('图片验证码不能为空！');
                }
                if (!captcha_check($post['vertify'])) {
                    return $this->error('图片验证码不正确！');
                }
            }
            $this->channel = Db::name('arctype')->where(['id' => $typeid])->getField('current_channel');
            $newData = array(
                //
                'typeid' => $typeid,
                'channel' => $this->channel,
                'ip' => $ip,
                'add_time' => getTime(),
                'update_time' => getTime(),
            );
            $data = array_merge($post, $newData);
            // 数据验证
            $rule = ['typeid' => 'require|token:' . $token];
		 /*当前栏目下的表单属性*/
            $row = Db::name('guestbook_attribute')->where('typeid', $typeid)->where('is_del', 0)->order('sort_order asc, attr_id asc')->getArray();
        /*--end*/
		    $newAttribute = array();
            foreach ($row as $key => $val) {             
                $newAttribute['itemname_' . $val['attr_id']] = $val['attr_name'];
            }
            $message = ['typeid.require' => '表单缺少标签属性{$field.hidden}'];
            $validate = new \think\Validate($rule, $message);
            if (!$validate->batch()->check($data)) {
                $error = $validate->getError();
                $error_msg = array_values($error);
                return $this->error($error_msg[0]);
            } else {
                $guestbookRow = [];
                /*处理是否重复表单数据的提交*/
                $formdata = $data;
                foreach ($formdata as $key => $val) {
                    if (in_array($key, ['typeid']) || preg_match('/^attr_(\\d+)$/i', $key)) {
                        continue;
                    }
                    unset($formdata[$key]);
                }
                $md5data = md5(serialize($formdata));
                $data['md5data'] = $md5data;
                $guestbookRow = Db::name('guestbook')->field('aid')->where(['md5data' => $md5data])->getOne();
                /*--end*/
                $dataStr = '';
                if (empty($guestbookRow)) {
                    // 非重复表单的才能写入数据库
                    $aid = Db::name('guestbook')->insertGetId($data);
                    if ($aid > 0) {
                        $this->saveGuestbookAttr($aid, $typeid);
                    }
                    /*插件 - 邮箱发送*/
                    $data = ['gbook_submit', $typeid, $aid];
                    $dataStr = implode('|', $data);
                    /*--end*/
                } else {
                    // 存在重复数据的表单，将在后台显示在最前面
                    Db::name('guestbook')->where('aid', $guestbookRow['aid'])->update(['add_time' => getTime(), 'update_time' => getTime()]);
                }
                return $this->success('操作成功！', null, $dataStr, 5);
            }
        }
		
    }
    /**
     *  给指定留言添加表单值到 guestbook_attr
     * @param int $aid 留言id
     * @param int $typeid 留言栏目id
     */
    private function saveGuestbookAttr($aid, $typeid)
    {
        // post 提交的属性  以 attr_id _ 和值的 组合为键名
        $post = input("post.");
        /*上传图片或附件*/
        foreach ($_FILES as $fileElementId => $file) {
            try {
                if (!empty($file['name']) && !is_array($file['name'])) {
                    $uplaod_data = func_common($fileElementId, 'allimg');
                    if (0 == $uplaod_data['errcode']) {
                        $post[$fileElementId] = $uplaod_data['img_url'];
                    } else {
                        $post[$fileElementId] = '';
                    }
                }
            } catch (\Exception $e) {
            }
        }
        /*end*/
        foreach ($post as $k => $v) {
            if (!strstr($k, 'attr_')) {
                continue;
            }
            $attr_id = str_replace('attr_', '', $k);
            is_array($v) && ($v = implode(PHP_EOL, $v));
            //$v = str_replace('_', '', $v); // 替换特殊字符
            //$v = str_replace('@', '', $v); // 替换特殊字符
            $v = trim($v);
            $adddata = array(
                //
                'aid' => $aid,
                'attr_id' => $attr_id,
                'attr_value' => $v,
                'add_time' => getTime(),
                'update_time' => getTime(),
            );
            Db::name('guestbook_attr')->add($adddata);
        }
    }
}