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
// [ 模型自定义字段 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use think\traits\model\ModelTrait;
use app\common\basic\BaseModel;
use think\facade\Db;

//表注释: 自定义字段表
//
//字段	类型	空	默认	注释
//id	int(10)	否	 	自增ID
//name	varchar(32)	否	 	字段名称
//channel_id	int(10)	否	0	所属文档模型id
//title	varchar(32)	否	 	字段标题
//dtype	varchar(32)	否	 	字段类型
//define	text	否	 	字段定义
//maxlength	int(10)	否	0	最大长度，文本数据必须填写，大于255为text类型
//dfvalue	varchar(1000)	否	 	默认值
//dfvalue_unit	varchar(50)	否	 	数值单位
//remark	varchar(256)	否	 	提示说明
//is_screening	tinyint(1)	否	0	是否应用于条件筛选
//is_release	tinyint(1)	否	0	是否应用于会员投稿发布
//ifeditable	tinyint(1)	否	1	是否在编辑页显示
//ifrequire	tinyint(1)	否	0	是否必填
//ifsystem	tinyint(1)	否	0	字段分类，1=系统(不可修改)，0=自定义
//ifmain	tinyint(1)	否	0	是否主表字段
//ifcontrol	tinyint(1)	否	1	状态，控制该条数据是否允许被控制，1为不允许控制，0为允许控制
//sort_order	int(5)	否	100	排序
//status	tinyint(1)	否	1	状态
//add_time	int(11)	否	0	创建时间
//update_time	int(11)	否	0	更新时间
class Channelfield extends BaseModel
{
   use ModelTrait;
    protected $pk = 'id';
    protected $name = 'channelfield';

    /**
     * 获取单条记录
     * @param int $id 
	 * @param string $field 
     */
    public static function getInfo($id, $field = '*')
    {
        $result = self::comment('app\common\model\Channelfield->getInfo')->field($field)->getOne($id);

        return $result;
    }

    /**
     * 获取单条记录
     * @param array $where 
	 * @param string $field 
     */
    public static function getInfoByWhere($where, $field = '*')
    {
        $result = self::comment('app\common\model\Channelfield->getInfoByWhere')->field($field)->where($where)->cache(true,CACHE_TIME,"channelfield")->getOne();

        return $result;
    }

    /**
     * 默认模型字段
     * @param array $map 
	 * @param string $field 
	 * @param string $index_key 
     */
    public static function getListByWhere($map = array(), $field = '*', $index_key = '')
    {
        $result = self::comment('app\common\model\Channelfield->getListByWhere')->field($field)
            ->where($map)
            ->order('sort_order asc, channel_id desc, id desc')
            ->getArray();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }
        
        return $result;
    }
}