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
// [ Channeltype Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use think\traits\model\ModelTrait;
use app\common\basic\BaseModel;
use think\facade\Db;
//表注释: 文档栏目表
//
//字段	类型	空	默认	注释
//id	int(10)	否	 	栏目ID
//channeltype	int(10)	是	0	栏目顶级模型ID
//current_channel	int(10)	是	0	栏目当前模型ID
//parent_id	int(10)	是	0	栏目上级ID
//typename	varchar(200)	是	 	栏目名称
//dirname	varchar(200)	是	 	目录英文名
//dirpath	varchar(200)	是	 	目录存放HTML路径
//englist_name	varchar(200)	是	 	栏目英文名
//grade	tinyint(1)	是	0	栏目等级
//typelink	varchar(200)	是	 	栏目链接
//litpic	varchar(250)	是	 	栏目图片
//templist	varchar(200)	是	 	列表模板文件名
//tempview	varchar(200)	是	 	文档模板文件名
//seo_title	varchar(200)	是	 	SEO标题
//seo_keywords	varchar(200)	是	 	seo关键字
//seo_description	text	是	NULL	seo描述
//sort_order	int(10)	是	0	排序号
//is_hidden	tinyint(1)	是	0	是否隐藏栏目：0=显示，1=隐藏
//is_part	tinyint(1)	是	0	栏目属性：0=内容栏目，1=外部链接
//admin_id	int(10)	是	0	管理员ID
//is_del	tinyint(1)	是	0	伪删除，1=是，0=否
//del_method	tinyint(1)	是	0	伪删除状态，1为主动删除，2为跟随上级栏目被动删除
//status	tinyint(1)	是	1	启用 (1=正常，0=屏蔽)
//is_release	tinyint(1)	是	0	栏目是否应用于会员投稿发布，1是，0否
//weapp_code	varchar(50)	是	 	插件栏目唯一标识
//add_time	int(11)	是	0	新增时间
//update_time	int(11)	是	0	更新时间
class Channeltype extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'channeltype';

    /**
     * 获取单条记录
     * @param int $id
	 * @return array or null
     */
    public static function getInfo($id)
    {
        $result = self::field('*')->cache(true,CACHE_TIME,"channeltype")->find($id);

        return $result->toArray();
    }

    /**
     * 获取单条记录
     * @param array $where 查询条件
	 * @param string $field 查询字段
	 * @return array or null
     */
    public static function getInfoByWhere($where, $field = '*')
    {
        $result = self::field($field)->where($where)->find();

        return $result;
    }

    /**
     * 获取多条记录
     * @return array
     */
    public static function getListByIds($ids, $field = '*')
    {
        
		is_array($ids) &&  $ids = implode(",", $ids);
	
		$model = new self();
        $model = $model->field($field);
		$model = $model->whereIn('id',$ids);
		$model = $model->order('sort_order asc');
		$model = $model->select();
		

        return $model->toArray();
    }

    /**
     * 默认获取全部
     * @return array
     */
    public static function getAll($field = '*', $map = array(), $index_key = '')
    {
        $cacheKey = array(
            'common',
            'model',
            'Channeltype',
            'getAll',
            $field,
            $map,
            $index_key
        );
        $cacheKey = json_encode($cacheKey);
        $result = cache($cacheKey);
        if (empty($result)) {
            $result = Db::name('channeltype')->field($field)
                ->where($map)
                ->order('sort_order asc, id asc')
                ->select();

            if (!empty($index_key)) {
                $result = convert_arr_key($result, $index_key);
            }

            cache($cacheKey, $result, null, 'channeltype');
        }

        return $result;
    }

    /**
     * 获取有栏目的模型列表
     * @param string $type yes表示存在栏目的模型列表，no表示不存在栏目的模型列表
     * @return array
     */
    public static function getArctypeChannel($type = 'yes')
    {
        if ($type == 'yes') {
            $map = array(
                'b.status'    => 1,
            );
            $result = Db::name('Channeltype')->field('b.*, a.*, b.id as typeid')
                ->alias('a')
                ->join('arctype b', 'b.current_channel = a.id', 'LEFT')
                ->where($map)
                ->group('a.id')
                ->cache(true,CACHE_TIME,"arctype")
                ->getAllWithIndex('nid');

        } else {
            $result = Db::name('Channeltype')->field('b.*, a.*, b.id as typeid')
                ->alias('a')
                ->join('arctype b', 'b.current_channel = a.id', 'LEFT')
                ->group('a.id')
                ->cache(true,CACHE_TIME,"arctype")
                ->getAllWithIndex('nid');

            if ($result) {
                foreach ($result as $key => $val) {
                    if (intval($val['channeltype']) > 0) {
                        unset($result[$key]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 根据文档ID获取模型信息
     * @return array or null
     */
    public static function getInfoByAid($aid)
    {
        $result = array();
        $res1 = Db::name('archives')->where(array('aid'=>$aid))->find();
        $res2 = Db::name('Channeltype')->where(array('id'=>$res1['channel']))->find();

        if (is_array($res1) && is_array($res2)) {
            $result = array_merge($res1, $res2);
        }

        return $result;
    }

    /**
     * 根据前端模板自动开启系统模型
     */
    public static function setChanneltypeStatus()
    {
        $planPath = 'template/pc';
        $planPath = realpath($planPath);
        if (!file_exists($planPath)) {
            return true;
        }
        $ctl_name_arr = array();
        $dirRes   = opendir($planPath);
        $view_suffix = config('template.view_suffix');
        while($filename = readdir($dirRes))
        {
            if(preg_match('/^(lists|view)?_/i', $filename) == 1)
            {
                $tplname = preg_replace('/([^_]+)?_([^\.]+)\.'.$view_suffix.'$/i', '${2}', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            } elseif (preg_match('/\.'.$view_suffix.'$/i', $filename) == 1) {
                $tplname = preg_replace('/\.'.$view_suffix.'$/i', '', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            }
        }
        $ctl_name_arr = array_unique($ctl_name_arr);

        if (!empty($ctl_name_arr)) {
            \think\Db::name('Channeltype')->where('id > 0')->cache(true,null,"channeltype")->update(array('status'=>0, 'update_time'=>getTime()));
            $map = array(
                'ctl_name'  => array('IN', $ctl_name_arr),
            );
            \think\Db::name('Channeltype')->where($map)->cache(true,null,"channeltype")->update(array('status'=>1, 'update_time'=>getTime()));
        } 
    }
}