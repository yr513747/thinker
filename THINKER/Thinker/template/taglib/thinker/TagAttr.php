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
class TagAttr extends Base
{
    public $aid = '';
    //初始化
    protected function init()
    {
        // 应用于文档列表
        $this->aid = input('param.aid/d', 0);
    }
    /**
     * 获取每篇文章的属性值
     */
    public function getAttr($aid = '', $name = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid)) {
            echo '标签attr报错：缺少属性 aid 值。';
            return false;
        }
        if (empty($name)) {
            echo '标签attr报错：缺少属性 name 值。';
            return false;
        }
        $parseStr = false;
        // 当前文档的属性值
        $attr_id = intval($name);
		$where = ['a.aid' => $aid, 'a.attr_id' => $attr_id, 'b.is_del' => 0];
        $row = Db::name('product_attr')->alias('a')->field('a.attr_value')->join('product_attribute b', 'a.attr_id = b.attr_id', 'LEFT')->where($where)->getOne();
        if (!empty($row)) {
            $parseStr = $row['attr_value'];
        } 
        return $parseStr;
    }
}