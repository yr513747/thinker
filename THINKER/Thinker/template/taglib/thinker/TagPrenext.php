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
// [ 获取内容页上下篇 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagPrenext extends Base
{
    public function getPrenext($get = 'pre')
    {
        $aid = input("param.aid/d", 0);
        if (empty($aid)) {
            echo '标签prenext报错：只能用在内容页。';
            return false;
        }
        $channelRes = M('Channeltype')->getInfoByAid($aid);
        $channel = $channelRes['channel'];
        $typeid = $channelRes['typeid'];
        $where = array();
        $where[] = array('a.typeid', '=', $typeid);
        $where[] = array('a.channel', '=', $channel);
        $where[] = array('a.status', '=', 1);
        $where[] = array('a.is_del', '=', 0);
        $where[] = array('a.arcrank', '>=', 0);
        if ($get == 'next') {
            // 下一篇
            $where[] = array('a.aid', '>', $aid);
            $result = Db::name('archives')->field('b.*, a.*')->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where($where)->order('a.aid asc')->getOne();
            if (!empty($result)) {
                $result['arcurl'] = arcurl('home/View/index', $result);
                if (empty($result['litpic'])) {
                    $result['is_litpic'] = 0;
                } else {
                    $result['is_litpic'] = 1;
                }
                $result['litpic'] = get_default_pic($result['litpic']);
            }
        } else {
            // 上一篇
            $where[] = array('a.aid', '<', $aid);
            $result = Db::name('archives')->field('b.*, a.*')->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where($where)->order('a.aid desc')->getOne();
            if (!empty($result)) {
                $result['arcurl'] = arcurl('home/View/index', $result);
                if (empty($result['litpic'])) {
                    $result['is_litpic'] = 0;
                } else {
                    $result['is_litpic'] = 1;
                }
                $result['litpic'] = get_default_pic($result['litpic']);
            }
        }
        return $result;
    }
}