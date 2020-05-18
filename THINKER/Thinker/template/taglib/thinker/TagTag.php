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
// [ TAG标签 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagTag extends Base
{
    public $aid = 0;
    //初始化
    protected function init()
    {
        $this->aid = input('param.aid/d', 0);
    }
    public function getTag($getall = 0, $typeid = '', $aid = 0, $row = 30, $sort = 'new', $type = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        $getall = intval($getall);
        !empty($typeid) && ($getall = 1);
        $result = false;
        $condition = array();
        if ($getall == 0 && $aid > 0) {
            $condition[] = array('aid', '=', $aid);
            $result = Db::name('taglist')->field('*, tid AS tagid')->where($condition)->limit((int) $row)->getArray();
        } else {
            if (!empty($typeid)) {
                $typeid = $this->getTypeids($typeid, $type);
                $condition[] = array('typeid', 'in', $typeid);
            }
            if ($sort == 'rand') {
                $orderby = 'rand() ';
            } elseif ($sort == 'week') {
                $orderby = ' weekcc DESC ';
            } elseif ($sort == 'month') {
                $orderby = ' monthcc DESC ';
            } elseif ($sort == 'hot') {
                $orderby = ' count DESC ';
            } elseif ($sort == 'total') {
                $orderby = ' total DESC ';
            } else {
                $orderby = 'add_time DESC  ';
            }
            $result = Db::name('tagindex')->field('*, id AS tagid')->where($condition)->orderRaw($orderby)->limit((int) $row)->getArray();
        }
        foreach ($result as $key => $val) {
            $val['link'] = url('home/Tags/lists', array('tagid' => $val['tagid']));
            $result[$key] = $val;
        }
        return $result;
    }
    private function getTypeids($typeid, $type = '')
    {
        $typeidArr = $typeid;
        if (!is_array($typeidArr)) {
            $typeidArr = explode(',', $typeid);
        }
        $typeids = [];
        foreach ($typeidArr as $key => $tid) {
            $result = [];
            switch ($type) {
                case 'son':
                    // 下级栏目
                    $result = M('Arctype')->getSon($tid, false);
                    break;
                case 'self':
                    // 同级栏目
                    $result = M('Arctype')->getSelf($tid);
                    break;
                case 'top':
                    // 顶级栏目
                    $result = M('Arctype')->getTop();
                    break;
                case 'sonself':
                    // 下级、同级栏目
                    $result = M('Arctype')->getSon($tid, true);
                    break;
                case 'first':
                    // 第一级栏目
                    $result = M('Arctype')->getFirst($tid);
                    break;
                default:
                    $result = [['id' => $tid]];
                    break;
            }
            if (!empty($result)) {
                $typeids = array_merge($typeids, get_arr_column($result, 'id'));
            }
        }
        return $typeids;
    }
}