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
// [ 获取友情链接 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagFlink extends Base
{
    public function getFlink($type = 'text', $limit = '')
    {
        if ($type == 'text') {
            $typeid = 1;
        } elseif ($type == 'image') {
            $typeid = 2;
        }
        $map = array();
        if (!empty($typeid)) {
            $map[] = array('typeid', '=', $typeid);
        }
        $result = Db::name("links");
        !empty($map) && ($result = $result->where($map));
        $result = $result->order('sort_order asc');
        $result = $result->limit((int) $limit);
        $result = $result->cache(true, CACHE_TIME, "links");
        $result = $result->getArray();
        foreach ($result as $key => $val) {
            $val['logo'] = get_default_pic($val['logo']);
            $val['target'] = $val['target'] == 1 ? 'target="_blank"' : 'target="_self"';
            $result[$key] = $val;
        }
        return $result;
    }
}