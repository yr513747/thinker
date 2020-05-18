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
// [ 文章列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use app\home\logic\FieldLogic;
use think\facade\Db;
class TagArclist extends Base
{
    protected $tid = '';
    protected $fieldLogic;
    //初始化
    protected function init()
    {
        $this->fieldLogic = new FieldLogic();
        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');
        // 应用于文档列表
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
        }
        // tid为目录名称的情况下
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    /**
     * arclist解析函数
     * @access    public
     * @param     array  $param  查询数据条件集合
     * @param     int  $row  调用行数
     * @param     string  $orderby  排列顺序
     * @param     string  $addfields  附加表字段，以逗号隔开
     * @param     string  $orderway  排序方式
     * @param     string  $tagid  标签id
     * @param     string  $tag  标签属性集合
     * @param     string  $pagesize  分页显示条数
     * @param     string  $thumb  是否开启缩略图
     * @param     string  $arcrank  是否显示会员权限
     * @return    array
     */
    public function getArclist($param = array(), $row = 15, $orderby = '', $addfields = '', $orderway = '', $tagid = '', $tag = '', $pagesize = 0, $thumb = '', $arcrank = '')
    {
        $result = false;
        $channeltype = "" != $param['channel'] && is_numeric($param['channel']) ? intval($param['channel']) : '';
        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;
        empty($orderway) && ($orderway = 'desc');
        $pagesize = empty($pagesize) ? intval($row) : intval($pagesize);
        $limit = $row;
        if (!empty($param['typeid'])) {
            if (!preg_match('/^\\d+([\\d\\,]*)$/i', $param['typeid'])) {
                echo '标签arclist报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            // 过滤typeid中含有空值的栏目ID
            $typeidArr_tmp = explode(',', $param['typeid']);
            $typeidArr_tmp = array_unique($typeidArr_tmp);
            foreach ($typeidArr_tmp as $k => $v) {
                if (empty($v)) {
                    unset($typeidArr_tmp[$k]);
                }
            }
            $param['typeid'] = implode(',', $typeidArr_tmp);
            // end
        }
        $typeid = $param['typeid'];
        $allow_release_channel = config('global.allow_release_channel');
        // 不指定模型ID、栏目ID，默认显示所有可以发布文档的模型ID下的文档
        if ("" === $channeltype && empty($typeid) || 0 === $channeltype) {
            $channeltype = $param['channel'] = implode(',', $allow_release_channel);
        }
        if (!empty($param['joinaid'])) {
            $joinaid = intval($param['joinaid']);
            unset($param['typeid']);
            unset($param['channel']);
        } else {
            if (!empty($channeltype)) {
                // 如果指定了频道ID，则频道下的所有文档都展示
                unset($param['typeid']);
            } else {
                // unset($param['channel']);
                if (!empty($typeid)) {
                    $typeidArr = explode(',', $typeid);
                    if (count($typeidArr) == 1) {
                        $typeid = intval($typeid);
                        $channel_info = Db::name('Arctype')->field('id,current_channel')->where('id', '=', $typeid)->getOne();
                        if (empty($channel_info)) {
                            echo '标签arclist报错：指定属性 typeid 的栏目ID不存在。';
                            return false;
                        }
                        // 当前栏目ID所属模型ID
                        $channeltype = !empty($channel_info) ? $channel_info["current_channel"] : '';
                        // 当前模型ID不属于含有列表模型，直接返回无数据
                        if (false === array_search($channeltype, $allow_release_channel)) {
                            return false;
                        }
                        // 获取当前栏目下的所有同模型的子孙栏目
                        $arctype_list = M("Arctype")->getHasChildren($channel_info['id']);
                        foreach ($arctype_list as $key => $val) {
                            if ($channeltype != $val['current_channel']) {
                                unset($arctype_list[$key]);
                            }
                        }
                        $typeids = get_arr_column($arctype_list, "id");
                        !in_array($typeid, $typeids) && ($typeids[] = $typeid);
                        $typeid = implode(",", $typeids);
                    } elseif (count($typeidArr) > 1) {
                        //$firstTypeid = $typeidArr[0];
                        //$firstTypeid = Db::name('Arctype')->where('id|dirname', '=', $firstTypeid)->getField('id');
                        //$channeltype = Db::name('Arctype')->where('id', '=', $firstTypeid)->getField('current_channel');
                    }
                    $param['channel'] = $channeltype;
                }
            }
        }
        // 查询条件
        $condition = array();
        foreach (array('keywords', 'typeid', 'notypeid', 'flag', 'noflag', 'channel', 'joinaid') as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    array_push($condition, "a.title LIKE '%{$param[$key]}%'");
                } elseif ($key == 'channel') {
                    array_push($condition, "a.channel IN ({$channeltype})");
                } elseif ($key == 'typeid') {
                    array_push($condition, "a.typeid IN ({$typeid})");
                } elseif ($key == 'notypeid') {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    array_push($condition, "a.typeid NOT IN (" . $param[$key] . ")");
                } elseif ($key == 'flag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $k2 => $v2) {
                        if ($v2 == "c") {
                            array_push($where_or_flag, "a.is_recom = 1");
                        } elseif ($v2 == "h") {
                            array_push($where_or_flag, "a.is_head = 1");
                        } elseif ($v2 == "a") {
                            array_push($where_or_flag, "a.is_special = 1");
                        } elseif ($v2 == "j") {
                            array_push($where_or_flag, "a.is_jump = 1");
                        } elseif ($v2 == "p") {
                            array_push($where_or_flag, "a.is_litpic = 1");
                        } elseif ($v2 == "b") {
                            array_push($where_or_flag, "a.is_b = 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (" . implode(" OR ", $where_or_flag) . ") ";
                        array_push($condition, $where_flag_str);
                    }
                } elseif ($key == 'noflag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $nk2 => $nv2) {
                        if ($nv2 == "c") {
                            array_push($where_or_flag, "a.is_recom <> 1");
                        } elseif ($nv2 == "h") {
                            array_push($where_or_flag, "a.is_head <> 1");
                        } elseif ($nv2 == "a") {
                            array_push($where_or_flag, "a.is_special <> 1");
                        } elseif ($nv2 == "j") {
                            array_push($where_or_flag, "a.is_jump <> 1");
                        } elseif ($nv2 == "p") {
                            array_push($where_or_flag, "a.is_litpic <> 1");
                        } elseif ($nv2 == "b") {
                            array_push($where_or_flag, "a.is_b <> 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (" . implode(" OR ", $where_or_flag) . ") ";
                        array_push($condition, $where_flag_str);
                    }
                } else {
                    array_push($condition, "a.{$key} = '" . $param[$key] . "'");
                }
            }
        }
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        // 回收站功能
        array_push($condition, "a.is_del = 0");
        // 定时文档显示插件
        if (is_dir(root_path('weapp') . 'TimingTask')) {
            $TimingTaskRow = M('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                // 只显当天或之前的文档
                array_push($condition, "a.add_time <= " . getTime());
            }
        }
        $where_str = "";
        if (0 < count($condition)) {
            $where_str = implode(" AND ", $condition);
        }
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby, $orderway, true);
        // 获取查询的控制器名
        $channeltype_info = M('Channeltype')->getInfo($channeltype);
        $controller_name = $channeltype_info['ctl_name'];
        $channeltype_table = $channeltype_info['table'];
        // 用于arclist标签的分页
        if (0 < $pagesize) {
            $tag['typeid'] = $typeid;
            isset($tag['channelid']) && ($tag['channelid'] = $channeltype);
            // 进行tagid的默认处理
            $tagidmd5 = $this->attDef($tag);
        }
        // 是否显示会员权限
        $users_level_list = [];
        if ('on' == $arcrank) {
            $users_level_list = Db::name('users_level')->field('level_name,level_value')->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
        }
        // 查询数据处理
        $aidArr = array();
        // 附加字段的数据表名
        $addtableName = '';
        switch ($channeltype) {
            case '-1':
                break;
            default:
                $field = "b.*, a.*";
                $result = Db::name('archives')->field($field)->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where($where_str)->orderRaw($orderby)->limit($limit)->getArray();
                // 用于arclist标签的分页
                $querysql = Db::name('archives')->getLastSql();
                foreach ($result as $key => $val) {
                    // 收集文档ID
                    array_push($aidArr, $val['aid']);
                    // 栏目链接
                    if ($val['is_part'] == 1) {
                        $val['typeurl'] = $val['typelink'];
                    } else {
                        $val['typeurl'] = typeurl('home/' . $controller_name . "/lists", $val);
                    }
                    // 文档链接
                    if ($val['is_jump'] == 1) {
                        $val['arcurl'] = $val['jumplinks'];
                    } else {
                        $val['arcurl'] = arcurl('home/' . $controller_name . '/view', $val);
                    }
                    // 封面图
                    if (empty($val['litpic'])) {
                        $val['is_litpic'] = 0;
                        // 无封面图
                    } else {
                        $val['is_litpic'] = 1;
                        // 有封面图
                    }
                    // 默认封面图
                    $val['litpic'] = get_default_pic($val['litpic']);
                    // 属性控制是否使用缩略图
                    if ('on' == $thumb) {
                        $val['litpic'] = thumb_img($val['litpic']);
                    }
                    // 是否显示会员权限
                    !isset($val['level_name']) && ($val['level_name'] = $val['arcrank']);
                    !isset($val['level_value']) && ($val['level_value'] = 0);
                    if ('on' == $arcrank) {
                        if (!empty($users_level_list[$val['arcrank']])) {
                            $val['level_name'] = $users_level_list[$val['arcrank']]['level_name'];
                            $val['level_value'] = $users_level_list[$val['arcrank']]['level_value'];
                        } else {
                            if (empty($val['arcrank'])) {
                                $firstUserLevel = current($users_level_list);
                                $val['level_name'] = $firstUserLevel['level_name'];
                                $val['level_value'] = $firstUserLevel['level_value'];
                            }
                        }
                    }
                    $result[$key] = $val;
                }
                // 附加表
                if (!empty($addfields) && !empty($aidArr)) {
                    // 替换中文逗号
                    $addfields = str_replace('，', ',', $addfields);
                    $addfields = trim($addfields, ',');
                    $addtableName = $channeltype_table . '_content';
                    $resultExt = Db::name($addtableName)->field("aid,{$addfields}")->where('aid', 'in', implode(',', $aidArr))->getAllWithIndex('aid');
                    // 自定义字段的数据格式处理
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channeltype, true);
                    foreach ($result as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $result[$key] = $val;
                    }
                }
                break;
        }
        //分页特殊处理
        if (false !== $tagidmd5 && 0 < $pagesize) {
            $arcmultiRow = Db::name('arcmulti')->field('tagid')->where(['tagid' => $tagidmd5])->getOne();
            //记录属性,以便分页样式统一调用
            $attstr = addslashes(serialize($tag));
            if (empty($arcmultiRow)) {
                Db::name('arcmulti')->insert([
                    //
                    'tagid' => $tagidmd5,
                    'tagname' => 'arclist',
                    'innertext' => '',
                    'pagesize' => $pagesize,
                    'querysql' => $querysql,
                    'ordersql' => $orderby,
                    'addfieldsSql' => $addfields,
                    'addtableName' => $addtableName,
                    'attstr' => $attstr,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ]);
            } else {
                Db::name('arcmulti')->where([
                    //
                    'tagid' => $tagidmd5,
                    'tagname' => 'arclist',
                ])->update([
                    //
                    'innertext' => '',
                    'pagesize' => $pagesize,
                    'querysql' => $querysql,
                    'ordersql' => $orderby,
                    'addfieldsSql' => $addfields,
                    'addtableName' => $addtableName,
                    'attstr' => $attstr,
                    'update_time' => getTime(),
                ]);
            }
        }
        $data = ['tag' => $tag, 'list' => $result];
        return $data;
    }
    /**
     * 生成hash唯一串
     * @param     array  $tag 标签属性
     * @return    string
     */
    private function attDef($tag)
    {
        $tagmd5 = md5(serialize($tag));
        if (!empty($tag['tagid'])) {
            $tagidmd5 = $tag['tagid'] . '_' . $tagmd5;
        } else {
            $tagidmd5 = false;
            // $tagidmd5 = 'arclist_'.$tagmd5;
        }
        return $tagidmd5;
    }
}