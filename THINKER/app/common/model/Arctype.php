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
// [ 栏目 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
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
class Arctype extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'arctype';

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    public static  function getInfo($id, $field = '', $get_parent = false)
    {
        if (empty($field)) {
            $field = 'c.*, a.*';
        }
        $field .= ', a.id as typeid';

        /*当前栏目信息*/
        $result = self::field($field)
            ->alias('a')
            ->where('a.id', $id)
            ->join('channeltype c', 'c.id = a.current_channel', 'LEFT')
            ->cache(true,CACHE_TIME,"arctype")
            ->getOne();
        /*--end*/
        if (!empty($result)) {
            if ($get_parent) {
                $result['typeurl'] = self::getTypeUrl($result); // 当前栏目的URL
                /*获取当前栏目父级栏目信息*/
                if ($result['parent_id'] > 0) {
                    $parent_row = self::field($field)
                        ->alias('a')
                        ->where('a.id', $result['parent_id'])
                        ->join('channeltype c', 'c.id = a.current_channel', 'LEFT')
                        ->cache(true,CACHE_TIME,"arctype")
                        ->getOne();
                    $ptypeurl = self::getTypeUrl($parent_row);
                    $parent_row['typeurl'] = $ptypeurl;
                } else {
                    $parent_row = $result;
                }
                /*--end*/
                
                /*给每个父类字段开头加上p*/
                foreach ($parent_row as $key => $val) {
                    $newK = 'p'.$key;
                    $parent_row[$newK] = $val;
                }
                /*--end*/
                $result = array_merge($result, $parent_row);
            } else {
                $result = self::parentAndTopInfo($id, $result);
            }
        }

        return $result;
    }

    /**
     * 获取指定栏目的父级和顶级栏目信息（用于前台与静态生成）
     * @author wengxianhu by 2017-7-26
     */
    public static  function parentAndTopInfo($id, $result = [])
    {
        $result['typeurl'] = self::getTypeUrl($result); // 当前栏目的URL
        if (!empty($result['parent_id'])) {
            // 当前栏目的父级栏目信息
            $parent_row = self::where('id', $result['parent_id'])
                ->cache(true,CACHE_TIME,"arctype")
                ->getOne();
            $ptypeid = $parent_row['id'];
            $ptypeurl = self::getTypeUrl($parent_row);
            $ptypename = $parent_row['typename'];
            $pdirname = $parent_row['dirname'];
            // 当前栏目的顶级栏目信息
            if (!isset($result['toptypeurl'])) {
                $allPid = self::getAllPid($id);
                $toptypeinfo = current($allPid);
                $toptypeid = $toptypeinfo['id'];
                $toptypeurl = self::getTypeUrl($toptypeinfo);
                $toptypename = $toptypeinfo['typename'];
                $topdirname = $toptypeinfo['dirname'];
            }
            // end
        } else {
            // 当前栏目的父级栏目信息 或 顶级栏目的信息
            $toptypeid = $ptypeid = $result['id'];
            $toptypeurl = $ptypeurl = $result['typeurl'];
            $toptypename = $ptypename = $result['typename'];
            $topdirname = $pdirname = $result['dirname'];
        }
        // 当前栏目的父级栏目信息
        $result['ptypeid'] = $ptypeid;
        $result['ptypeurl'] = $ptypeurl;
        $result['ptypename'] = $ptypename;
        $result['pdirname'] = $pdirname;
        // 当前栏目的顶级栏目信息
        !isset($result['toptypeid']) && $result['toptypeid'] = $toptypeid;
        !isset($result['toptypeurl']) && $result['toptypeurl'] = $toptypeurl;
        !isset($result['toptypename']) && $result['toptypename'] = $toptypename;
        !isset($result['topdirname']) && $result['topdirname'] = $topdirname;
        // end

        return $result;
    }

    /**
     * 根据目录名称获取单条记录
     * @author wengxianhu by 2018-4-20
     */
    public static  function getInfoByDirname($dirname)
    {
        $field = 'c.*, a.*, a.id as typeid';

        $result = self::field($field)
            ->alias('a')
            ->where('a.dirname', $dirname)
            ->join('channeltype c', 'c.id = a.current_channel', 'LEFT')
            ->cache(true,CACHE_TIME,"arctype")
            ->getOne();
        if (!empty($result)) {
            $result['typeurl'] = self::getTypeUrl($result);

            $result_tmp = self::where('id', $result['parent_id'])->getOne();
            $result['ptypeurl'] = self::getTypeUrl($result_tmp);
        }

        return $result;
    }

    /**
     * 检测是否有子栏目
     * @author wengxianhu by 2017-7-26
     */
    public static  function hasChildren($id)
    {
        if (is_array($id)) {
            $ids = array_unique($id);
            $row = self::field('parent_id, count(id) AS total')->where('parent_id','IN', $ids)->where('is_del',0)->group('parent_id')->getAllWithIndex('parent_id');
            return $row;
        } else {
            $count = self::where([
                    'parent_id' => $id,
                    'is_del'    => 0,
                ])->count('id');
            return ($count > 0 ? 1 : 0);
        }
    }

    /**
     * 获取栏目的URL
     */
    public static  function getTypeUrl($res)
    {
        if ($res['is_part'] == 1) {
            $typeurl = $res['typelink'];
        } else {
            $ctl_name = get_controller_byct($res['current_channel']);
            $typeurl = typeurl("home/{$ctl_name}/lists", $res);
        }

        return $typeurl;
    }


    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2018-4-26
     */
    public static  function getChannelList($id = '', $type = 'son')
    {
        $result = array();
        switch ($type) {
            case 'son':
                $result = self::getSon($id, false);
                break;

            case 'self':
                $result = self::getSelf($id);
                break;

            case 'top':
                $result = self::getTop();
                break;

            case 'sonself':
                $result = self::getSon($id, true);
                break;
        }

        return $result;
    }

    /**
     * 获取下一级栏目
     * @param string $self true表示没有子栏目时，获取同级栏目
     * @author wengxianhu by 2017-7-26
     */
    public static  function getSon($id, $self = false)
    {
        $result = array();
        if (empty($id)) {
            return $result;
        }

        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = $arctypeLogic->arctype_list($id, 0, false, $arctype_max_level, $map);

        if (!empty($res)) {
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $arctype_max_level; $i++) {
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            if (isset($arr[$id])) {
                $result = $arr[$id];
            }
        }

        if (empty($result) && $self == true) {
            $result = self::getSelf($id);
        }

        return $result;
    }

    /**
     * 获取同级栏目
     * @author wengxianhu by 2017-7-26
     */
    public static  function getSelf($id)
    {
        $result = array();
        if (empty($id)) {
            return $result;
        }

        $map = array(
            'id'   => $id,
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = self::field('parent_id')->where($map)->getOne();

        if ($res) {
            $newId = $res['parent_id'];
            $arctypeLogic = new \app\common\logic\ArctypeLogic();
            $arctype_max_level = intval(config('global.arctype_max_level'));
            $map = array(
                'is_hidden'   => 0,
                'status'  => 1,
            );
            $res = $arctypeLogic->arctype_list($newId, 0, false, $arctype_max_level, $map);

            if (!empty($res)) {
                $arr = group_same_key($res, 'parent_id');
                for ($i=0; $i < $arctype_max_level; $i++) { 
                    foreach ($arr as $key => $val) {
                        foreach ($arr[$key] as $key2 => $val2) {
                            if (!isset($arr[$val2['id']])) continue;
                            $val2['children'] = $arr[$val2['id']];
                            $arr[$key][$key2] = $val2;
                        }
                    }
                }
                $result = $arr[$newId];
            }
        }
        return $result;
    }

    /**
     * 获取顶级栏目
     * @author wengxianhu by 2017-7-26
     */
    public static  function getTop()
    {
        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = $arctypeLogic->arctype_list(0, 0, false, $arctype_max_level, $map);

        $result = array();
        if (!empty($res)) {
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $arctype_max_level; $i++) { 
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            reset($arr);
            $firstResult = current($arr);
            $result = $firstResult;
        }

        return $result;
    }

    /**
     * 获取当前栏目及所有子栏目
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2017-7-26
     */
    public static  function getHasChildren($id, $self = true)
    {
       
        $cacheKey = "common_model_Arctype_getHasChildren_{$id}_{$self}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $where = array(
                'c.status'  => 1,
               
                'c.is_del'  => 0,
            );
            $fields = "c.*, count(s.id) as has_children";
            $res = self::field($fields)
                ->alias('c')
                ->join('arctype s','s.parent_id = c.id','LEFT')
                ->where($where)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->getArray();

            $result = arctype_options($id, $res, 'id', 'parent_id');

            if (!$self) {
                array_shift($result);
            }

            cache($cacheKey, $result, null, "arctype");
        }

        return $result;
    }

    /**
     * 获取所有栏目
     * @param   int     $id     栏目的ID
     * @param   int     $selected   当前选中栏目的ID
     * @param   int     $channeltype      查询条件
     * @author wengxianhu by 2017-7-26
     */
    public static  function getList($id = 0, $select = 0, $re_type = true, $map = array())
    {
        $id = $id ? intval($id) : 0;
        $select = $select ? intval($select) : 0;

        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $options = $arctypeLogic->arctype_list($id, $select, $re_type, $arctype_max_level, $map);

        return $options;
    }


    /**
     * 默认获取全部
     * @author 小虎哥 by 2018-4-16
     */
    public static  function getAll($field = '*', $map = array(), $index_key = '')
    {
        
        $result = self::field($field)
            ->where($map)
           
            ->order('sort_order asc')
            ->cache(true,CACHE_TIME,"arctype")
            ->getArray();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }

        return $result;
    }

    /**
     * 获取当前栏目的所有父级
     * @author wengxianhu by 2018-4-26
     */
    public static  function getAllPid($id)
    {
		if (isMobile() && is_dir(root_path('view') . 'mobile')) {
            $theme_style = 'mobile';
        } else {
            $theme_style = 'pc';
        } 
        $cacheKey = array(
            'common',
            'model',
            'Arctype',
            'getAllPid',
            $theme_style,
            $id,
        );
        $cacheKey = json_encode($cacheKey);
        $data = cache($cacheKey);
        if (empty($data)) {
            $data = array();
            $typeid = $id;
            $arctype_list = self::field('*, id as typeid')
                ->where([
                    'status'    => 1,
                    'is_del'    => 0,
                ])
                ->getAllWithIndex('id');
            if (isset($arctype_list[$typeid])) {
                // 第一个先装起来
                $arctype_list[$typeid]['typeurl'] = self::getTypeUrl($arctype_list[$typeid]);
                $data[$typeid] = $arctype_list[$typeid];
            } else {
                return $data;
            }

            while (true)
            {
                $typeid = $arctype_list[$typeid]['parent_id'];
                if($typeid > 0){
                    if (isset($arctype_list[$typeid])) {
                        $arctype_list[$typeid]['typeurl'] = self::getTypeUrl($arctype_list[$typeid]);
                        $data[$typeid] = $arctype_list[$typeid];
                    }
                } else {
                    break;
                }
            }
            $data = array_reverse($data, true);

            cache($cacheKey, $data, null, "arctype");
        }

        return $data;
    }

    /**
     * 伪删除指定栏目（包括子栏目、所有相关文档）
     */
    public static  function pseudo_del($typeid)
    {
        $childrenList = self::getHasChildren($typeid); // 获取当前栏目以及所有子栏目
        $typeidArr = get_arr_column($childrenList, 'id'); // 获取栏目数组里的所有栏目ID作为新的数组
        $typeidArr2 = $typeidArr;

        

        /*标记当前栏目以及子栏目为被动伪删除*/
        $sta1 = self::where([
                'id'    => ['IN', $typeidArr],
                'is_del'    => 0,
                'del_method' => 0,
            ])
            ->cache(true,null,"arctype")
            ->update([
                'is_del'    => 1,
                'del_method'    => 2, // 1为主动删除，2为跟随上级栏目被动删除
                'update_time'   => getTime(),
            ]); // 伪删除栏目
        /*--end*/

        /*标记当前栏目为主动伪删除*/
        
        $sta2 = self::where('id','IN', $typeidArr2)
            ->cache(true,null,"arctype")
            ->update([
                'is_del'    => 1,
                'del_method'    => 1, // 1为主动删除，2为跟随上级栏目被动删除
                'update_time'   => getTime(),
            ]); // 伪删除栏目
        /*--end*/

        if ($sta1 && $sta2) {
            model('Archives')->pseudo_del($typeidArr); // 删除文档
           

            /*清除页面缓存*/
            // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
            // $htmlCacheLogic->clear_arctype();
            /*--end*/

            return true;
        }

        return false;
    }

    /**
     * 删除指定栏目（包括子栏目、所有相关文档）
     */
    public static  function del($typeid)
    {
        $childrenList = self::getHasChildren($typeid); // 获取当前栏目以及所有子栏目
        $typeidArr = get_arr_column($childrenList, 'id'); // 获取栏目数组里的所有栏目ID作为新的数组
        
        $sta = self::where('id','IN', $typeidArr)
            ->cache(true,null,"arctype")
            ->delete(); // 删除栏目
        if ($sta) {
            model('Archives')->del($typeidArr); // 删除文档
           
            /*清除页面缓存*/
            // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
            // $htmlCacheLogic->clear_arctype();
            /*--end*/

            return true;
        }

        return false;
    }

    /**
     * 每个栏目的顶级栏目的目录名称
     */
    public static  function getEveryTopDirnameList()
    {
        $result = extra_cache('common_getEveryTopDirnameList_model');
        if ($result === false)
        {
            
            $fields = "c.id, c.parent_id, c.dirname, c.grade, count(s.id) as has_children";
            $row = self::field($fields)
                ->alias('c')
                ->join('arctype s','s.parent_id = c.id','LEFT')
                
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->cache(true,CACHE_TIME,"arctype")
                ->getArray();
            $row = arctype_options(0, $row, 'id', 'parent_id');

            $result = array();
            foreach ($row as $key => $val) {
                if (empty($val['parent_id'])) {
                    $val['tdirname'] = $val['dirname'];
                } else {
                    $val['tdirname'] = isset($row[$val['parent_id']]['tdirname']) ? $row[$val['parent_id']]['tdirname'] : $val['dirname'];
                }
                $row[$key] = $val;
                $result[md5($val['dirname'])] = $val;
            }

            extra_cache('common_getEveryTopDirnameList_model', $result);
        }

        return $result;
    }

    /**
     * 新增栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public static  function addData($data = [])
    {
        $insertId = false;
        if (!empty($data)) {
            $insertId = self::insertGetId($data);
            if($insertId){
                // --存储单页模型
                if ($data['current_channel'] == 6) {
                    $archivesData = array(
                        'title' => $data['typename'],
                        'typeid'=> $insertId,
                        'channel'   => $data['current_channel'],
                        'sort_order'    => 100,
                        
                        'add_time'  => getTime(),
                    );
                    // $archivesData = array_merge($archivesData, $data);
                    $aid = M('archives')->insertGetId($archivesData);
                    if ($aid) {
                        // ---------后置操作
                        if (!isset($post['addonFieldExt'])) {
                            $post['addonFieldExt'] = array(
                                'typeid'    => $archivesData['typeid'],
                            );
                        } else {
                            $post['addonFieldExt']['typeid'] = $archivesData['typeid'];
                        }
                        $post['addonFieldExt']['content'] = !empty($post['addonFieldExt']['content']) ? $post['addonFieldExt']['content'] : '';
                        $addData = array(
                            'addonFieldExt' => $post['addonFieldExt'],
                        );
                        $addData = array_merge($addData, $archivesData);
                        model('Single')->afterSave($aid, $addData, 'add');
                        // ---------end
                    }
                }

                /*同步栏目ID到权限组，默认是赋予该栏目的权限*/
                model('AuthRole')->syn_auth_role($insertId);
                /*--end*/

                /*清除页面缓存*/
                // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
                // $htmlCacheLogic->clear_arctype();
                /*--end*/

                // \think\Cache::clear("arctype");
                // extra_cache('admin_all_menu', NULL);
                // \think\Cache::clear('admin_archives_release');
            }
        }
        return $insertId;
    }

    /**
     * 批量增加顶级栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public static  function batchAddTopData($addData = [], $post = [])
    {
        $arctypeLogic = new \app\common\logic\ArctypeLogic;

        $result = [];
        if (!empty($addData)) {
            $rdata = self::getSelfModel()->saveAll($addData);
            if ($rdata) {
                // --存储单页模型的主表
                $archivesData = [];
                foreach ($rdata as $k1 => $v1) {
                    $info = $v1->getData();
                    if ($info['current_channel'] == 6) {
                        $archivesData[] = [
                            'title' => $info['typename'],
                            'typeid'=> $info['id'],
                            'channel'   => $info['current_channel'],
                            'sort_order'    => 100,
                            
                            'add_time'  => getTime(),
                        ];
                    } else {
                        break;
                    }
                }
                // --存储单页模型的附表
                if (!empty($archivesData)) {
                    $arcdata = model('Archives')->saveAll($archivesData);
                    if ($arcdata) {
                        $singleData = [];
                        foreach ($arcdata as $k1 => $v1) {
                            $info = $v1->getData();
                            $singleData[] = [
                                'aid' => $info['aid'],
                                'typeid'=> $info['typeid'],
                                'content'   => '',
                                'add_time'  => getTime(),
                                'update_time'  => getTime(),
                            ];
                        }
                        !empty($singleData) && Db::name('single_content')->insertAll($singleData);
                    }
                }

                foreach ($rdata as $k1 => $v1) {
                    $info = $v1->getData();
                    $result[] = $info;

                    /*同步栏目ID到权限组，默认是赋予该栏目的权限*/
                    model('AuthRole')->syn_auth_role($info['id']);
                    /*--end*/

                   
                }

                /*新增顶级栏目的下级栏目*/
                $saveData = [];
                $dirnameArr = [];
                foreach ($result as $key => $val) {
                    if (!empty($post['sontype'][$key])) {
                        $sontype = $post['sontype'][$key];
                        foreach ($sontype as $son_k => $son_v) {
                            $typename = trim($son_v);
                            if (empty($typename)) continue;

                            // 目录名称
                            $dirname = $arctypeLogic->get_dirname($typename, '', 0, $dirnameArr);
                            array_push($dirnameArr, $dirname);

                            $dirpath = $val['dirpath'].'/'.$dirname;

                            $data = [
                                'typename'  => $typename,
                                'channeltype'   => $val['channeltype'],
                                'current_channel'   => $val['current_channel'],
                                'parent_id' => intval($val['id']),
                                'dirname'   => $dirname,
                                'dirpath'   => $dirpath,
                                'grade' => intval($val['grade']) + 1,
                                'templist'  => !empty($val['templist']) ? $val['templist'] : '',
                                'tempview'  => !empty($val['tempview']) ? $val['tempview'] : '',
                                'is_hidden'  => $val['is_hidden'],
                                'admin_id'  => $val['admin_id'],
                               
                                'sort_order'    => $val['sort_order'],
                                'add_time'  => $val['add_time'],
                                'update_time'  => $val['update_time'],
                            ];

                            $saveData[] = $data;
                        }
                    }
                }
                if (!empty($saveData)) {
                    $result2 = self::batchAddSubData($saveData);
                    $result = array_merge($result, $result2);
                }
                /*end*/
            }
        }

        return $result;
    }

    /**
     * 批量增加下级栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public static  function batchAddSubData($addData = [])
    {
        $result = [];
        if (!empty($addData)) {
            $rdata = self::getSelfModel()->saveAll($addData);
            if ($rdata) {
                // --存储单页模型的主表
                $archivesData = [];
                foreach ($rdata as $k1 => $v1) {
                    $info = $v1->getData();
                    if ($info['current_channel'] == 6) {
                        $archivesData[] = [
                            'title' => $info['typename'],
                            'typeid'=> $info['id'],
                            'channel'   => $info['current_channel'],
                            'sort_order'    => 100,
                           
                            'add_time'  => getTime(),
                        ];
                    } else {
                        break;
                    }
                }
                // --存储单页模型的附表
                if (!empty($archivesData)) {
                    $arcdata = model('Archives')->saveAll($archivesData);
                    if ($arcdata) {
                        $singleData = [];
                        foreach ($arcdata as $k1 => $v1) {
                            $info = $v1->getData();
                            $singleData[] = [
                                'aid' => $info['aid'],
                                'typeid'=> $info['typeid'],
                                'content'   => '',
                                'add_time'  => getTime(),
                                'update_time'  => getTime(),
                            ];
                        }
                        !empty($singleData) && Db::name('single_content')->insertAll($singleData);
                    }
                }

                foreach ($rdata as $k1 => $v1) {
                    $info = $v1->getData();
                    $result[] = $info;

                    /*同步栏目ID到权限组，默认是赋予该栏目的权限*/
                    model('AuthRole')->syn_auth_role($info['id']);
                    /*--end*/

                   
                }
            }
        }

        return $result;
    }

    /**
     * 编辑栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public static  function arctypeUpdateData($data = [])
    {
        $bool = false;
        if (!empty($data)) {
           
            $bool = M('arctype')->where([
                    'id'    => $data['id'],
                    
                ])
                ->cache(true,null,"arctype")
                ->update($data);
            if($bool){
                /*批量更新所有子孙栏目的最顶级模型ID*/
                $allSonTypeidArr = self::getHasChildren($data['id'], false); // 获取当前栏目的所有子孙栏目（不包含当前栏目）
                if (!empty($allSonTypeidArr)) {
                    $i = 1;
                    $minuendGrade = 0;
                    foreach ($allSonTypeidArr as $key => $val) {
                        if ($i == 1) {
                            $firstGrade = intval($post['oldgrade']);
                            $minuendGrade = intval($grade) - $firstGrade;
                        }
                        $update_data = array(
                            'channeltype'        => $data['channeltype'],
                            'update_time'        => getTime(),
                            'grade'   =>  Db::raw('grade+'.$minuendGrade),
                        );
                        M('arctype')->where([
                                'id'    => $val['id'],
                                
                            ])
                            ->cache(true,null,"arctype")
                            ->update($update_data);
                        ++$i;
                    }
                }
                /*--end*/

                // --存储单页模型
                if ($data['current_channel'] == 6) {
                    $archivesData = array(
                        'title' => $data['typename'],
                        'typeid'=> $data['id'],
                        'channel'   => $data['current_channel'],
                        'sort_order'    => 100,
                        'update_time'     => getTime(),
                    );
                    // $archivesData = array_merge($archivesData, $data);
                    $aid = M('single_content')->where(array('typeid'=>$data['id']))->getField('aid');
                    if (empty($aid)) {
                        $opt = 'add';
                        
                        $archivesData['add_time'] = getTime();
                        $up = $aid = M('archives')->insertGetId($archivesData);
                    } else {
                        $opt = 'edit';
                        $up = M('archives')->where([
                                'aid'   => $aid,
                               
                            ])->update($archivesData);
                    }
                    if ($up) {
                        // ---------后置操作
                        if (!isset($post['addonFieldExt'])) {
                            $post['addonFieldExt'] = array(
                                'typeid'    => $data['id'],
                            );
                        } else {
                            $post['addonFieldExt']['typeid'] = $data['id'];
                        }
                        $updateData = array(
                            'addonFieldExt' => $post['addonFieldExt'],
                        );
                        $updateData = array_merge($updateData, $archivesData);
                        model('Single')->afterSave($aid, $updateData, $opt);
                        // ---------end
                    }
                }

                

                /*清除页面缓存*/
                // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
                // $htmlCacheLogic->clear_arctype();
                /*--end*/

                // \think\Cache::clear("arctype");
                // extra_cache('admin_all_menu', NULL);
                // \think\Cache::clear('admin_archives_release');
            }
        }
        return $bool;
    }
}