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
// [ 广告 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 广告表
//
//字段	类型	空	默认	注释
//id	int(11)	否	 	广告id
//pid	int(11)	否	0	广告位置ID
//media_type	tinyint(1)	是	0	广告类型
//title	varchar(60)	是	 	广告名称
//links	varchar(255)	是	 	广告链接
//litpic	varchar(255)	是	 	图片地址
//start_time	int(11)	是	0	投放时间
//end_time	int(11)	是	0	结束时间
//intro	text	是	NULL	描述
//link_man	varchar(60)	是	 	添加人
//link_email	varchar(60)	是	 	添加人邮箱
//link_phone	varchar(60)	是	 	添加人联系电话
//click	int(11)	是	0	点击量
//bgcolor	varchar(30)	是	 	背景颜色
//status	tinyint(1)	是	1	1=显示，0=屏蔽
//sort_order	int(11)	是	0	排序
//target	varchar(50)	是	 	是否开启浏览器新窗口
//admin_id	int(10)	是	0	管理员ID
//is_del	tinyint(1)	是	0	伪删除，1=是，0=否
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class Ad extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'ad';
    
}