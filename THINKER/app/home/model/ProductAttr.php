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
// [ 产品参数 Model ]
// --------------------------------------------------------------------------
namespace app\home\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 产品表单属性值
//
//字段	类型	空	默认	注释
//product_attr_id	int(10)	否	 	产品属性id自增
//aid	mediumint(8)	否	0	产品id
//attr_id	int(11)	否	0	属性id
//attr_value	text	是	NULL	属性值
//attr_price	varchar(255)	是	 	属性价格
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
//表注释: 产品表单属性表 product_attribute
//
//字段	类型	空	默认	注释
//attr_id	int(11)	否	 	属性id
//attr_name	varchar(60)	是	 	属性名称
//typeid	int(11)	是	0	栏目id
//attr_index	tinyint(1)	是	0	0不需要检索 1关键字检索 2范围检索
//attr_input_type	tinyint(1)	是	0	0=文本框，1=下拉框，2=多行文本框
//attr_values	text	是	NULL	可选值列表
//sort_order	int(11)	是	0	属性排序
//is_del	tinyint(1)	是	0	是否已删除，0=否，1=是
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class ProductAttr extends BaseModel
{
    use ModelTrait;
    protected $pk = 'product_attr_id';
    protected $name = 'product_attr';
    /**
     * 获取指定产品的所有参数
     */
    public static function getProAttr($aids = [], $field = 'b.*, a.*')
    {
        $where = [];
        !empty($aids) && ($where[] = ['b.aid', 'IN', implode(',', $aids)]);
        $where[] = ['a.is_del', '=', 0];
        $result = Db::name('product_attribute')
		->field($field)
		->alias('a')
		->join('product_attr b', 'b.attr_id = a.attr_id', 'LEFT')
		->where($where)
		->order('a.sort_order asc, a.attr_id asc')
		->getArray();
        !empty($result) && ($result = group_same_key($result, 'aid'));
        return $result;
    }
}