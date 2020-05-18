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
// [ 图集 SERVICE ]
// --------------------------------------------------------------------------
namespace app\home\service;

use think\facade\Db;
class Images
{
    /**
     * 获取单条记录
     */
    public function getInfo($aid, $field = '', $isshowbody = true)
    {
        $data = array();
        if (!empty($field)) {
            $field_arr = explode(',', $field);
            foreach ($field_arr as $key => $val) {
                $val = trim($val);
                if (preg_match('/^([a-z]+)\\./i', $val) == 0) {
                    array_push($data, 'a.' . $val);
                } else {
                    array_push($data, $val);
                }
            }
            $field = implode(',', $data);
        }
        $result = array();
        if ($isshowbody) {
            $field = !empty($field) ? $field : 'b.*, a.*';
            $result = Db::name('archives')->field($field)->alias('a')->join('images_content b', 'b.aid = a.aid', 'LEFT')->getOne($aid);
        } else {
            $field = !empty($field) ? $field : 'a.*';
            $result = Db::name('archives')->field($field)->alias('a')->getOne($aid);
        }
        // 文章TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = M('Taglist')->getListByAid($aid, $typeid);
            $result['tags'] = $tags;
        }
        return $result;
    }
}