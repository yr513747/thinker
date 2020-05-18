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
// [ 文档 Model ]
// --------------------------------------------------------------------------
namespace app\home\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 文档主表
//
//字段	类型	空	默认	注释
//aid	int(10)	否
//typeid	int(10)	否	0	当前栏目
//channel	int(10)	否	0	模型ID
//is_b	tinyint(1)	是	0	加粗
//title	varchar(200)	是	 	标题
//litpic	varchar(250)	是	 	缩略图
//is_head	tinyint(1)	是	0	头条（0=否，1=是）
//is_special	tinyint(1)	是	0	特荐（0=否，1=是）
//is_top	tinyint(1)	是	0	置顶（0=否，1=是）
//is_recom	tinyint(1)	是	0	推荐（0=否，1=是）
//is_jump	tinyint(1)	是	0	跳转链接（0=否，1=是）
//is_litpic	tinyint(1)	是	0	图片（0=否，1=是）
//author	varchar(200)	是	 	作者
//click	int(10)	是	0	浏览量
//arcrank	int(10)	是	0	阅读权限：0=开放浏览，-1=待审核稿件
//jumplinks	varchar(200)	是	 	外链跳转
//ismake	tinyint(1)	是	0	是否静态页面（0=动态，1=静态）
//seo_title	varchar(200)	是	 	SEO标题
//seo_keywords	varchar(200)	是	 	SEO关键词
//seo_description	text	是	NULL	SEO描述
//users_price	decimal(10,2)	否	0.00	会员价
//old_price	decimal(10,2)	否	0.00	产品旧价
//stock_count	int(10)	否	0	商品库存量
//stock_show	tinyint(1)	否	1	商品库存在产品详情页是否显示，1为显示，0为不显示
//prom_type	tinyint(1)	是	0	产品类型：0普通产品，1虚拟产品
//tempview	varchar(200)	是	 	文档模板文件名
//status	tinyint(1)	是	1	状态(0=屏蔽，1=正常)
//sort_order	int(10)	是	0	排序号
//admin_id	int(10)	是	0	管理员ID
//users_id	int(10)	是	0	会员ID
//arc_level_id	int(10)	是	0	文档会员权限ID
//is_del	tinyint(1)	是	0	伪删除，1=是，0=否
//del_method	tinyint(1)	是	0	伪删除状态，1为主动删除，2为跟随上级栏目被动删除
//joinaid	int(10)	是	0	关联文档ID
//downcount	int(10)	是	0	下载次数
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class Archives extends BaseModel
{
    use ModelTrait;
    protected $pk = 'aid';
    protected $name = 'archives';
    /**
     * 获取单条文档记录
     */
    public static function getViewInfo($aid, $litpic_remote = false, $where = [])
    {
        $result = array();
        $row = new self();
        $row = $row->field('*');
        !empty($where) && ($row = $row->where($where));
        $row = $row->getOne($aid);
        if (!empty($row)) {
            if (empty($row['litpic'])) {
                // 无封面图
                $row['is_litpic'] = 0;
            } else {
                // 有封面图
                $row['is_litpic'] = 1;
            }
            // 默认封面图
            $row['litpic'] = get_default_pic($row['litpic'], $litpic_remote);
            /*文档基本信息*/
            if (1 == $row['channel']) {
                // 文章
                $rowExt = S('Article')->getInfo($aid);
            } else {
                if (2 == $row['channel']) {
                    // 产品
                    $rowExt = S('Product')->getInfo($aid);
                    // 产品参数
                    $attr_list = ProductAttr::getProAttr($aid);
                    $row['attr_list'] = !empty($attr_list[$aid]) ? $attr_list[$aid] : [];
                    // 产品相册
                    $image_list = [];
                    $image_list_tmp = ProductImg::getProImg($aid);
                    if (!empty($image_list_tmp[$aid])) {
                        foreach ($image_list_tmp[$aid] as $key => $val) {
                            $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                            $image_list[$key] = $val;
                        }
                    }
                    $row['image_list'] = $image_list;
                } else {
                    if (3 == $row['channel']) {
                        // 图集
                        $rowExt = S('Images')->getInfo($aid);
                        // 图集相册
                        $image_list = [];
                        $image_list_tmp = ImagesUpload::getImgUpload($aid);
                        if (!empty($image_list_tmp[$aid])) {
                            foreach ($image_list_tmp[$aid] as $key => $val) {
                                $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                                $image_list[$key] = $val;
                            }
                        }
                        $row['image_list'] = $image_list;
                    } else {
                        if (4 == $row['channel']) {
                            // 下载
                            $rowExt = S('Download')->getInfo($aid);
                        }
                    }
                }
            }
            // 自定义字段的数据格式处理
            $rowExt = L('FieldLogic')->getChannelFieldList($rowExt, $row['channel']);
            /*--end*/
            $result = array_merge($rowExt, $row);
        }
        return $result;
    }
    /**
     * 获取单页栏目记录
     */
    public static function getSingleInfo($typeid, $litpic_remote = false)
    {
        $result = array();
        // 文档基本信息
        $row = self::readContentFirst($typeid);
        if (!empty($row)) {
            if (empty($row['litpic'])) {
                $row['is_litpic'] = 0;
            } else {
                $row['is_litpic'] = 1;
            }
            $row['litpic'] = get_default_pic($row['litpic'], $litpic_remote);
            // 自定义字段的数据格式处理
            $row = L('FieldLogic')->getTableFieldList($row, config('global.arctype_channel_id'));
            $row = L('FieldLogic')->getChannelFieldList($row, $row['channel']);
            $result = $row;
        }
        return $result;
    }
    /**
     * 读取指定栏目ID下有内容的栏目信息，只读取每一级的第一个栏目
     * @param intval $typeid 栏目ID
     * @return array
     */
    public static function readContentFirst($typeid)
    {
        $result = false;
        while (true) {
            $result = SingleContent::getInfoByTypeid($typeid);
            if (empty($result['content']) && preg_match('/^lists_single(_(.*))?\\.htm$/i', $result['templist'])) {
                $map = array(
                    //
                    'parent_id' => $result['typeid'],
                    'current_channel' => 6,
                    'is_hidden' => 0,
                    'status' => 1,
                );
                // 查找下一级的单页模型栏目
                $row = Db::name('arctype')->where($map)->field('*')->order('sort_order asc')->getOne();
                if (empty($row)) {
                    // 不存在并返回当前栏目信息
                    break;
                } elseif (6 == $row['current_channel']) {
                    // 存在且是单页模型，则进行继续往下查找，直到有内容为止
                    $typeid = $row['id'];
                }
            } else {
                break;
            }
        }
        return $result;
    }
}