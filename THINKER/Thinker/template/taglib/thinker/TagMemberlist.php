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
// [ 会员列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagMemberlist extends Base
{
    /**
     * 获取会员列表
     */
    public function getMemberlist($limit = '', $orderby = '', $orderway = '', $js = '', $attarray = '')
    {
        /*加载js*/
        if (empty($js)) {
            return $this->getMemberlistJs($attarray);
        }
        /*end*/
        $condition = ['admin_id' => 0];
        switch ($orderby) {
            case 'logintime':
            // 兼容织梦的写法
            case 'last_login':
                $orderby = "last_login {$orderway}";
                break;
            case 'users_id':
                $orderby = "users_id {$orderway}";
                break;
            case 'regtime':
            case 'reg_time':
                $orderby = "reg_time {$orderway}";
                break;
            default:
                $fieldList = Db::name('users')->getTableFields();
                if (in_array($orderby, $fieldList)) {
                    $orderby = "{$orderby} {$orderway}";
                } else {
                    $orderby = "users_id desc";
                }
                break;
        }
        $list = Db::name("users")->field('password,paypwd', true)->where($condition)->order($orderby)->limit((int)$limit)->getArray();
        if (empty($list)) {
            return false;
        }
        foreach ($list as $key => $val) {
            $val['head_pic'] = get_head_pic($val['head_pic']);
            $list[$key] = $val;
        }
        return $list;
    }
    /**
     * 获取会员列表的JS
     */
    private function getMemberlistJs($attarray = '')
    {
        $result = [];
        $t_uniqid = md5(getTime() . uniqid(mt_rand(), TRUE));
        $txtid = "thinker_" . md5("memberlist_txt_{$t_uniqid}");
        $result['txtid'] = $txtid;
        $result['root_dir'] = $this->root_dir;
        $result['attarray'] = $attarray;
        $result_json = json_encode($result);
        $version = $this->params['version'];
        $hidden = <<<EOF
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_memberlist.js?v={$version}"></script>
<script type="text/javascript">
    var tag_memberlist_result_json = {$result_json};
    tag_memberlist(tag_memberlist_result_json);
</script>
EOF;
        $data = ['txtid' => $txtid, 'hidden' => $hidden];
        return $data;
    }
}