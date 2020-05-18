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
// [ 单页 Model ]
// --------------------------------------------------------------------------
namespace app\home\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 单页附加表
//
//字段	类型	空	默认	注释
//id	int(10)	否
//aid	int(10)	否	0	文档ID
//typeid	int(10)	是	0	栏目ID
//content	longtext	是	NULL	内容详情
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class SingleContent extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'single_content';
    /**
     * 获取单条记录
     */
    public static function getInfoByTypeid($typeid)
    {
        $cacheKey = "home_model_Single_getInfoByTypeid_{$typeid}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $field = 'c.*, b.*, a.*, b.aid, a.id as typeid';
            $result = Db::name('arctype')
			->field($field)
			->alias('a')
			->join('archives b', 'b.typeid = a.id', 'LEFT')
			->join('single_content c', 'c.aid = b.aid', 'LEFT')
			->where('b.channel', 6)
			->getOne($typeid);
            cache($cacheKey, $result, null, "arctype");
        }
        return $result;
    }
}