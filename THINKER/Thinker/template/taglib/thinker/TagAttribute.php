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
// [ 栏目属性值 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagAttribute extends Base
{
    public $aid = '';
    //初始化
    protected function init()
    {
        // 应用于文档列表
        $this->aid = input('param.aid/d', 0);
    }
    /**
     * 获取每篇文章的属性
     */
    public function getAttribute($aid = '', $type = '', $attrid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid)) {
            echo '标签attribute报错：缺少属性 aid 值。';
            return false;
        }
        $result = false;
        if ('newattr' == $type) {
            // 新版参数
            $where = [
                //
                'a.list_id' => $attrid,
                'a.status' => 1,
                'b.aid' => $aid,
            ];
            $result = Db::name('shop_product_attribute')->alias('a')->field('a.attr_name as name, b.attr_value as value')->join('shop_product_attr b', 'a.attr_id = b.attr_id', 'LEFT')->where($where)->order('sort_order asc')->getArray();
        } else {
            // 旧版参数
            $where = [
                //
                'a.aid' => $aid,
                'b.is_del' => 0,
            ];
            // 当前栏目下的属性
            $row = Db::name('product_attr')->alias('a')->field('a.attr_value,b.attr_id,b.attr_name')->join('product_attribute b', 'a.attr_id = b.attr_id', 'LEFT')->where($where)->order('b.sort_order asc, a.attr_id asc')->getArray();
            if (empty($row)) {
                return $result;
            } else {
                if ('default' == $type) {
                    $newAttribute = array();
                    foreach ($row as $key => $val) {
                        $attr_id = $val['attr_id'];
                        // 字段名称
                        $name = 'value_' . $attr_id;
                        $newAttribute[$name] = $val['attr_value'];
                        // 表单提示文字
                        $itemname = 'name_' . $attr_id;
                        $newAttribute[$itemname] = $val['attr_name'];
                    }
                    $result[0] = $newAttribute;
                } else {
                    if ('auto' == $type) {
                        foreach ($row as $key => $val) {
                            $row[$key] = [
                                //
                                'name' => $val['attr_name'],
                                'value' => $val['attr_value'],
                            ];
                        }
                        $result = $row;
                    }
                }
            }
        }
        return $result;
    }
}