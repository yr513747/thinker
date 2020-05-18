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
// [ 图集图片 Model ]
// --------------------------------------------------------------------------
namespace app\home\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 图集图片表
//
//字段	类型	空	默认	注释
//img_id	mediumint(8)	否	 	自增ID
//aid	mediumint(8)	否	0	图集ID
//title	varchar(200)	是	 	产品标题
//image_url	varchar(255)	是	 	文件存储路径
//intro	varchar(2000)	是	 	图集描述
//width	int(11)	是	0	图片宽度
//height	int(11)	是	0	图片高度
//filesize	mediumint(8)	是	0	文件大小
//mime	varchar(50)	是	 	图片类型
//sort_order	smallint(5)	是	0	排序
//add_time	int(10)	是	0	上传时间
//update_time	int(11)	是	0	更新时间
class ImagesUpload extends BaseModel
{
    use ModelTrait;
    protected $pk = 'img_id';
    protected $name = 'images_upload';
    /**
     * 获取指定图集的所有图片
     */
    public static function getImgUpload($aids = [], $field = '*')
    {
        $where = [];
        !empty($aids) && ($where[] = ['aid', 'IN', implode(',', $aids)]);
        $result = new self();
        $result = $result->field($field);
        !empty($where) && ($result = $result->where($where));
        $result = $result->order('sort_order asc');
        $result = $result->getArray();
        !empty($result) && ($result = group_same_key($result, 'aid'));
        return $result;
    }
}