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
// [ 友情链接 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 友情链接表
//
//字段	类型	空	默认	注释
//id	int(11)	否	 	 
//typeid	tinyint(1)	是	1	类型：1=文字链接，2=图片链接
//title	varchar(50)	是	 	网站标题
//url	varchar(100)	是	 	网站地址
//logo	varchar(255)	是	 	网站LOGO
//sort_order	int(11)	是	0	排序号
//target	tinyint(1)	是	0	是否开启浏览器新窗口
//email	varchar(50)	是	NULL	 
//intro	text	是	NULL	网站简况
//status	tinyint(1)	是	1	状态(1=显示，0=屏蔽)
//delete_time	int(11)	是	0	软删除时间
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class Links extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'links';
    
}