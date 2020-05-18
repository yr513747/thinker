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
// [ 获取广告 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagAdv extends Base
{
    public function getAdv($pid = '', $where = '', $orderby = '')
    {
        if (empty($pid)) {
            echo '标签adv报错：缺少属性 pid 。';
            return false;
        }
        $times = getTime();
        if (empty($where)) {
            // 新逻辑
            // 排序
            switch ($orderby) {
                case 'hot':
                case 'click':
                    $orderby = 'a.click desc';
                    break;
                case 'now':
                case 'new':
                    // 兼容织梦的写法
                    $orderby = 'a.add_time desc';
                    break;
                case 'id':
                    $orderby = 'a.id desc';
                    break;
                case 'sort_order':
                    $orderby = 'a.sort_order asc';
                    break;
                case 'rand':
                    $orderby = 'rand()';
                    break;
                default:
                    if (empty($orderby)) {
                        $orderby = 'a.sort_order asc, a.id desc';
                    }
                    break;
            }
            $where = "b.status = 1 AND a.pid={$pid} and a.start_time < {$times} and (a.end_time > {$times} OR a.end_time = 0) and a.status = 1";
            $result = Db::name("ad")->alias('a')->field("a.*")->join('ad_position b', 'b.id = a.pid', 'LEFT')->where($where)->orderRaw($orderby)->cache(true, CACHE_TIME, "ad")->getArray();
        } else {
            // 排序
            switch ($orderby) {
                case 'hot':
                case 'click':
                    $orderby = 'click desc';
                    break;
                case 'now':
                case 'new':
                    // 兼容织梦的写法
                    $orderby = 'add_time desc';
                    break;
                case 'id':
                    $orderby = 'id desc';
                    break;
                case 'sort_order':
                    $orderby = 'sort_order asc';
                    break;
                case 'rand':
                    $orderby = 'rand()';
                    break;
                default:
                    if (empty($orderby)) {
                        $orderby = 'sort_order asc, id desc';
                    }
                    break;
            }
            $result = Db::name("ad")->field("*")->where($where)->orderRaw($orderby)->cache(true, CACHE_TIME, "ad")->getArray();
            $adpRow = Db::name("ad_position")->where(['id' => $pid, 'status' => 1])->count();
            if (empty($adpRow)) {
                return false;
            }
        }
        foreach ($result as $key => $val) {
            $val['litpic'] = handle_subdir(get_default_pic($val['litpic']));
            $val['target'] = $val['target'] == 1 ? 'target="_blank"' : 'target="_self"';
            $val['intro'] = htmlspecialchars_decode($val['intro']);
            $val['intro'] = handle_subdir($val['intro'], 'html');
            $result[$key] = $val;
        }
        return $result;
    }
}