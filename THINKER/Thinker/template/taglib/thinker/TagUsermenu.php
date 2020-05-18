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
// [ 获取会员菜单 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagUsermenu extends Base
{
    public function getUsermenu($currentstyle = '', $limit = '')
    {
        $map = array();
        $map[] = ['status', '=', 1];
        $menuRow = Db::name("users_menu")->where($map)->order('sort_order asc')->limit((int) $limit)->getArray();
        $result = [];
        foreach ($menuRow as $key => $val) {
            $val['url'] = url($val['mca']);
            // 标记被选中效果
            if (preg_match('/^' . $this->params['app_name'] . '\\/' . $this->params['controller_name'] . '\\//i', $val['mca'])) {
                $val['currentstyle'] = $currentstyle;
            } else {
                $val['currentstyle'] = '';
            }
            $result[] = $val;
        }
        return $result;
    }
}