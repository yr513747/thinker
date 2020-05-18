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
// [ 文章分页列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use app\home\logic\FieldLogic;
use think\facade\Db;
class TagList extends Base
{
    public $tid = '';
    public $fieldLogic;
    public $url_screen_var;
    //初始化
    protected function init()
    {
        $this->fieldLogic = new FieldLogic();
        $this->tid = input('param.tid/s', '');
        // 应用于文档列表
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
        }
        // typeid|tid为目录名称的情况下
        $this->tid = $this->getTrueTypeid($this->tid);
        // 定义筛选标识
        $this->url_screen_var = config('global.url_screen_var');
    }
    /**
     * 获取分页列表
     */
    public function getList($param = array(), $pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '', $arcrank = '')
    {
        $module_name_tmp = strtolower($this->params['app_name']);
        $ctl_name_tmp = strtolower($this->params['controller_name']);
        $action_name_tmp = strtolower($this->params['action_name']);
        empty($orderway) && ($orderway = 'desc');
        // 自定义字段筛选
        $url_screen_var = input('param.' . $this->url_screen_var . '/d');
        if (1 == $url_screen_var) {
            return $this->GetFieldScreeningList($param, $pagesize, $orderby, $addfields, $orderway, $thumb);
        }
        // 搜索、标签搜索
        if (in_array($ctl_name_tmp, array('search', 'tags'))) {
            return $this->getSearchList($pagesize, $orderby, $addfields, $orderway, $thumb);
        }
        $result = false;
        $channeltype = "" != $param['channel'] && is_numeric($param['channel']) ? intval($param['channel']) : '';
        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;
        if (!empty($param['typeid'])) {
            if (!preg_match('/^\\d+([\\d\\,]*)$/i', $param['typeid'])) {
                echo '标签list报错：typeid属性值语法错误，请正确填写栏目ID。';
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
        // 不指定模型ID、栏目ID，默认显示所有可以发布文档的模型ID下的文档
        if ("" === $channeltype && empty($typeid) || 0 === $channeltype) {
            $allow_release_channel = config('global.allow_release_channel');
            $channeltype = $param['channel'] = implode(',', $allow_release_channel);
        }
        // 如果指定了频道ID，则频道下的所有文档都展示
        if (!empty($channeltype)) {
            // 优先展示模型下的文章
            unset($param['typeid']);
        } elseif (!empty($typeid)) {
            // 其次展示栏目下的文章
            // unset($param['channel']);
            $typeidArr = explode(',', $typeid);
            if (count($typeidArr) == 1) {
                $typeid = intval($typeid);
                $channel_info = Db::name('Arctype')->field('id,current_channel')->where('id', '=', $typeid)->getOne();
                if (empty($channel_info)) {
                    echo '标签list报错：指定属性 typeid 的栏目ID不存在。';
                    return false;
                }
                $channeltype = !empty($channel_info) ? $channel_info["current_channel"] : '';
                // 获取当前栏目下的同模型所有子孙栏目
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
        } else {
            // 再次展示控制器对应的模型文章
            $controller_name = $this->params['controller_name'];
            $channeltype_info = M('Channeltype')->getInfoByWhere(array('ctl_name' => $controller_name), 'id');
            if (!empty($channeltype_info)) {
                $channeltype = $channeltype_info['id'];
                $param['channel'] = $channeltype;
            }
        }
        if (empty($typeid) && empty($channeltype)) {
            echo '标签list报错：至少指定属性 typeid | channelid 任何一个。';
            return $result;
        }
        // 查询条件
        $condition = array();
        foreach (array('keywords', 'typeid', 'notypeid', 'flag', 'noflag', 'channel') as $key) {
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
        array_push($condition, "a.is_del = 0");
        // 定时文档显示插件
        if (is_dir(root_path('weapp') . 'TimingTask')) {
            $TimingTaskRow = M('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= " . getTime());
            }
        }
        $where_str = "";
        if (0 < count($condition)) {
            $where_str = implode(" AND ", $condition);
        }
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby, $orderway);
        // 是否显示会员权限
        $users_level_list = [];
        if ('on' == $arcrank) {
            $users_level_list = Db::name('users_level')->field('level_name,level_value')->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
        }
        // 获取查询的表名
        $channeltype_info = M('Channeltype')->getInfo($channeltype);
        $controller_name = $channeltype_info['ctl_name'];
        $channeltype_table = $channeltype_info['table'];
        switch ($channeltype) {
            case '-1':
                break;
            default:
                $list = array();
                $query_get = array();
                // 列表分页URL问号的查询部分
                $get_arr = input('get.');
                foreach ($get_arr as $key => $val) {
                    if (empty($val) || stristr($key, '/')) {
                        unset($get_arr[$key]);
                    }
                }
                $param_arr = input('param.');
                foreach ($param_arr as $key => $val) {
                    if (empty($val) || stristr($key, '/')) {
                        unset($param_arr[$key]);
                    }
                }
                $paginate = array(
                    //
                    'type' => config('paginate.type'),
                    'var_page' => config('paginate.var_page'),
                    'query' => $query_get,
                );
                $field = "b.*, a.*";
                $pages = Db::name('archives')->field($field)->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where($where_str)->orderRaw($orderby)->paginate($pagesize, false, $paginate);
                $aidArr = array();
                foreach ($pages->items() as $key => $val) {
                    // 获取指定路由模式下的URL
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
                    $list[$key] = $val;
                    // 文档ID数组
                    array_push($aidArr, $val['aid']);
                }
                // 附加表
                if (!empty($addfields) && !empty($aidArr)) {
                    // 替换中文逗号
                    $addfields = str_replace('，', ',', $addfields);
                    $addfields = trim($addfields, ',');
                    $tableContent = $channeltype_table . '_content';
                    $resultExt = Db::name($tableContent)->field("aid,{$addfields}")->where('aid', 'in', implode(',', $aidArr))->getAllWithIndex('aid');
                    // 自定义字段的数据格式处理
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channeltype, true);
                    foreach ($list as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $list[$key] = $val;
                    }
                }
                // 针对下载列表
                if (!empty($aidArr) && strtolower($controller_name) == 'download') {
                    $downloadRow = Db::name('download_file')->where('aid', 'IN', implode(',', $aidArr))->order('aid asc, sort_order asc')->getArray();
                    $downloadFileArr = array();
                    if (!empty($downloadRow)) {
                        // 获取指定文档ID的下载文件列表
                        foreach ($downloadRow as $key => $val) {
                            if (!isset($downloadFileArr[$val['aid']]) || empty($downloadFileArr[$val['aid']])) {
                                $downloadFileArr[$val['aid']] = array();
                            }
                            $val['downurl'] = url("home/View/downfile", array('id' => $val['file_id'], 'uhash' => $val['uhash']));
                            $downloadFileArr[$val['aid']][] = $val;
                        }
                    }
                    // 将组装好的文件列表与文档相关联
                    foreach ($list as $key => $val) {
                        $list[$key]['file_list'] = !empty($downloadFileArr[$val['aid']]) ? $downloadFileArr[$val['aid']] : array();
                    }
                }
                $result['pages'] = $pages;
                $result['list'] = $list;
                break;
        }
        return $result;
    }
    /**
     * 获取搜索分页列表
     */
    public function getSearchList($pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '')
    {
        $result = false;
        empty($orderway) && ($orderway = 'desc');
        $condition = array();
        // 获取到所有URL参数
        $param = input('param.');
        if (strtolower($this->params['controller_name']) == 'tags') {
            $tag = input('param.tag/s', '');
            $tagid = input('param.tagid/d', 0);
            if (!empty($tag)) {
                $tagidArr = Db::name('tagindex')->where('tag', 'LIKE', "%{$tag}%")->column('id', 'id');
                $aidArr = Db::name('taglist')->field('aid')->where('tid', 'in', implode(",", $tagidArr))->column('aid', 'aid');
                array_push($condition, "aid IN (" . implode(",", $aidArr) . ")");
            } elseif ($tagid > 0) {
                $aidArr = Db::name('taglist')->field('aid')->where('tid', '=', $tagid)->column('aid', 'aid');
                array_push($condition, "aid IN (" . implode(",", $aidArr) . ")");
            }
        }
        // 应用搜索条件
        foreach (['keywords', 'typeid', 'notypeid', 'channelid', 'flag', 'noflag'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ('keywords' == $key) {
                    $keywords = trim($param[$key]);
                    array_push($condition, "a.title LIKE '%{$keywords}%'");
                } else {
                    if ('typeid' == $key) {
                        $param[$key] = str_replace('，', ',', $param[$key]);
                        $param[$key] = preg_replace('/([^0-9,])/i', '', $param[$key]);
                        if (stristr($param[$key], ',')) {
                            // 指定多个栏目ID
                            $typeids = explode(',', $param[$key]);
                        } else {
                            $search_type = input('param.type/s', 'default');
                            if ('default' == $search_type) {
                                // 默认只检索指定的栏目ID，不涉及下级栏目
                                $typeids = [$param[$key]];
                            } else {
                                if ('sonself' == $search_type) {
                                    // 当前栏目以及下级栏目
                                    $arctype_info = Db::name('arctype')->field('id,current_channel')->where('id', '=', $param[$key])->where('is_del', 0)->getOne();
                                    $childrenRow = M('Arctype')->getHasChildren($param[$key]);
                                    foreach ($childrenRow as $k2 => $v2) {
                                        if ($arctype_info['current_channel'] != $v2['current_channel']) {
                                            unset($childrenRow[$k2]);
                                            // 排除不是同一模型的栏目
                                        }
                                    }
                                    $typeids = get_arr_column($childrenRow, 'id');
                                }
                            }
                        }
                        array_push($condition, "a.typeid IN ({$typeid})");
                    } elseif ($key == 'channelid') {
                        array_push($condition, "a.channel IN ({$param[$key]})");
                    } elseif ($key == 'notypeid') {
                        $param[$key] = str_replace('，', ',', $param[$key]);
                        $param[$key] = preg_replace('/([^0-9,])/i', '', $param[$key]);
                        $notypeids = explode(',', $param[$key]);
                        array_push($condition, "a.typeid NOT IN (" . $notypeids . ")");
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
        }
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0");
        // 定时文档显示插件
        if (is_dir(root_path('weapp') . 'TimingTask')) {
            $TimingTaskRow = M('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= " . getTime());
            }
        }
        $where_str = "";
        if (0 < count($condition)) {
            $where_str = implode(" AND ", $condition);
        }
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby, $orderway);
        /**
         * 数据查询，搜索出主键ID的值
         */
        $list = array();
        $query_get = input('get.');
        $paginate_type = config('paginate.type');
        if (isMobile()) {
            $paginate_type = 'mobile';
        }
        $paginate = array('type' => $paginate_type, 'var_page' => config('paginate.var_page'), 'query' => $query_get);
        $pages = Db::name('archives')->field("a.aid")->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where('a.channel', 'NOT IN', [6])->where($where_str)->order($orderby)->paginate($pagesize, false, $paginate);
        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($pages->total() > 0) {
            $list = $pages->items();
            $aids = get_arr_column($list, 'aid');
            $fields = "b.*, a.*";
            $row = Db::name('archives')->field($fields)->alias('a')->join('arctype b', 'a.typeid = b.id', 'LEFT')->where('a.aid', 'in', $aids)->getAllWithIndex('aid');
            // 获取模型对应的控制器名称
            $channel_list = M('Channeltype')->getAll('id, ctl_name', array(), 'id');
            foreach ($list as $key => $val) {
                $arcval = $row[$val['aid']];
                $controller_name = $channel_list[$arcval['channel']]['ctl_name'];
                // 获取指定路由模式下的URL
                if ($arcval['is_part'] == 1) {
                    $arcval['typeurl'] = $arcval['typelink'];
                } else {
                    $arcval['typeurl'] = typeurl('home/' . $controller_name . "/lists", $arcval);
                }
                // 文档链接
                if ($arcval['is_jump'] == 1) {
                    $arcval['arcurl'] = $arcval['jumplinks'];
                } else {
                    $arcval['arcurl'] = arcurl('home/' . $controller_name . "/view", $arcval);
                }
                // 封面图
                if (empty($arcval['litpic'])) {
                    $arcval['is_litpic'] = 0;
                    // 无封面图
                } else {
                    $arcval['is_litpic'] = 1;
                    // 有封面图
                }
                $arcval['litpic'] = get_default_pic($arcval['litpic']);
                if ('on' == $thumb) {
                    $arcval['litpic'] = thumb_img($arcval['litpic']);
                }
                $list[$key] = $arcval;
            }
            // 附加表
            if (!empty($addfields) && !empty($list)) {
                $channeltypeRow = M('Channeltype')->getAll('id,table', [], 'id');
                $channelGroupRow = group_same_key($list, 'current_channel');
                foreach ($channelGroupRow as $channelid => $tmp_list) {
                    $addtableName = '';
                    $tmp_aid_arr = get_arr_column($tmp_list, 'aid');
                    $channeltype_table = $channeltypeRow[$channelid]['table'];
                    $addfields = str_replace('，', ',', $addfields);
                    $addfields = trim($addfields, ',');
                    $addtableName = $channeltype_table . '_content';
                    $resultExt = Db::name($addtableName)->field("aid,{$addfields}")->where('aid', 'in', implode(',', $tmp_aid_arr))->getAllWithIndex('aid');
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channelid, true);
                    foreach ($list as $key2 => $val2) {
                        $valExt = !empty($resultExt[$val2['aid']]) ? $resultExt[$val2['aid']] : array();
                        $val2 = array_merge($valExt, $val2);
                        $list[$key2] = $val2;
                    }
                }
            }
        }
        $result['pages'] = $pages;
        $result['list'] = $list;
        return $result;
    }
    /**
     * 获取搜索分页列表
     */
    public function GetFieldScreeningList($param = array(), $pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '')
    {
        $result = false;
        empty($orderway) && ($orderway = 'desc');
        $condition = array();
        // 获取到所有URL参数
        $param_new = input('param.');
        $param_new['tid'] = $this->getTrueTypeid($param_new['tid']);
        if (strtolower($this->params['controller_name']) == 'tags') {
            $tag = input('param.tag/s', '');
            $tagid = input('param.tagid/d', 0);
            if (!empty($tag)) {
                $tagidArr = Db::name('tagindex')->where('tag', 'LIKE', "%{$tag}%")->column('id', 'id');
                $aidArr = Db::name('taglist')->field('aid')->where('tid', 'in', implode(",", $tagidArr))->column('aid', 'aid');
                array_push($condition, "aid IN (" . implode(",", $aidArr) . ")");
            } elseif ($tagid > 0) {
                $aidArr = Db::name('taglist')->field('aid')->where('tid', '=', $tagid)->column('aid', 'aid');
                array_push($condition, "aid IN (" . implode(",", $aidArr) . ")");
            }
        } else {
            // 自定义字段筛选
            $where = [
                // 根据需求新增条件
                'is_screening' => 1,
            ];
            // 所有应用于搜索的自定义字段
            $channelfield = Db::name('channelfield')->where($where)->field('channel_id,id,name,dtype')->getArray();
            // 查询当前栏目所属模型
            $channel_id = Db::name('arctype')->where('id', $param_new['tid'])->getField('current_channel');
            // 所有模型类别
            $channeltype_list = config('global.channeltype_list');
            $channel_table = array_search($channel_id, $channeltype_list);
            // 查询获取aid初始sql语句
            $wheres = [];
            $where_multiple = "";
            foreach ($channelfield as $key => $value) {
                // 值不为空则执行
                $fieldname = $value['name'];
                if (!empty($fieldname) && !empty($param_new[$fieldname])) {
                    // 分割参数，判断多选或单选，拼装sql语句
                    $val_arr = explode('|', trim($param_new[$fieldname], '|'));
                    if (!empty($val_arr)) {
                        if ('' == $val_arr[0]) {
                            // 选择全部时拼装sql语句
                            // $wheres[] = [$fieldname,'<>', null];
                        } else {
                            if (1 == count($val_arr)) {
                                // 多选字段类型
                                if ('checkbox' == $value['dtype']) {
                                    $val_arr[0] = addslashes($val_arr[0]);
                                    $where_multiple = Db::raw("FIND_IN_SET('" . $val_arr[0] . "',{$fieldname})");
                                } else {
                                    $wheres[] = [$fieldname, '=', $val_arr[0]];
                                    //$wheres[$fieldname] = $val_arr[0];
                                }
                            } else {
                                // 多选
                                $where_or_arr = array();
                                foreach ($val_arr as $k2 => $v2) {
                                    // $v2 = func_preg_replace(['"','\'',';'], '', $v2);
                                    $v2 = addslashes($v2);
                                    array_push($where_or_arr, "'{$v2}' IN ({$fieldname})");
                                }
                                if (!empty($where_or_arr)) {
                                    $where_multiple = implode(" OR ", $where_or_arr);
                                }
                            }
                        }
                    }
                }
            }
            $aid_result = Db::name($channel_table . '_content');
            $aid_result = $aid_result->field('aid');
            !empty($wheres) && ($aid_result = $aid_result->where($wheres));
            !empty($where_multiple) && ($aid_result = $aid_result->where($where_multiple));
            $aid_result = $aid_result->getArray();
            if (!empty($aid_result)) {
                array_push($condition, "a.aid IN (" . implode(',', get_arr_column($aid_result, "aid")) . ")");
            } else {
                $pages = Db::name('archives')->field("aid")->where("aid=0")->paginate($pagesize);
                $result['pages'] = $pages;
                $result['list'] = [];
                return $result;
            }
            /*结束*/
        }
        // 应用搜索条件
        foreach (['keywords', 'typeid', 'notypeid', 'channel', 'flag', 'noflag'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ('keywords' == $key) {
                    $keywords = trim($param[$key]);
                    array_push($condition, "a.title LIKE '%{$keywords}%'");
                } else {
                    if (in_array($key, array('typeid', 'channel'))) {
                        $param[$key] = str_replace('，', ',', $param[$key]);
                        array_push($condition, "a.{$key} IN ({$param[$key]})");
                    } elseif ($key == 'notypeid') {
                        $param[$key] = str_replace('，', ',', $param[$key]);
                        array_push($condition, "a.typeid NOT IN ({$param[$key]})");
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
        }
        // 查询条件拼装
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0");
        //定时文档显示插件
        if (is_dir(root_path('weapp') . 'TimingTask')) {
            $TimingTaskRow = M('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= " . getTime());
            }
        }
        //end
        // 最后一级栏目则查询当前栏目数据
        $TypeIdWhere = "a.typeid = " . $param_new['tid'];
        // 查询栏目是否存在下一级栏目
        // $arctype_ids = Db::name('arctype')->where('parent_id',$param_new['tid'])->field('id')->getArray();
        $arctype_ids = M('Arctype')->getHasChildren($param_new['tid']);
        if (!empty($arctype_ids)) {
            $arctype_ids = get_arr_column($arctype_ids, "id");
            $field_id = [];
            // 处理得到绑定栏目的ID
            foreach ($param_new as $key => $value) {
                foreach ($channelfield as $kk => $vv) {
                    if ($vv['name'] == $key) {
                        $field_id[] = $vv['id'];
                    }
                }
            }
            // 处理栏目ID
            if (!empty($field_id)) {
                $typeid_arr = Db::name('channelfield_bind')->where('field_id', 'IN', $field_id)->field('typeid')->getArray();
                $typeid_arr = array_unique(get_arr_column($typeid_arr, "typeid"));
                $array_new = array_intersect($typeid_arr, $arctype_ids);
                if (!empty($array_new)) {
                    $typeid_ids = '(' . implode(',', $array_new) . ')';
                } else {
                    $typeid_ids = '(' . $param_new['tid'] . ')';
                }
            } else {
                $typeid_ids = '(' . implode(',', $arctype_ids) . ')';
            }
            $TypeIdWhere = "a.typeid IN " . $typeid_ids;
        }
        array_push($condition, $TypeIdWhere);
        // 拼装查询所有条件成sql
        $condition_str = "";
        if (0 < count($condition)) {
            $condition_str = implode(" AND ", $condition);
        }
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby, $orderway);
        /**
         * 数据查询，搜索出主键ID的值
         */
        $list = array();
        $query_get = input('get.');
        $paginate = array(
            //
            'type' => config('paginate.type'),
            'var_page' => config('paginate.var_page'),
            'query' => $query_get,
        );
        $pages = Db::name('archives')->field("a.aid")->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where('a.channel', 'NOT IN', [6])->where($condition_str)->order($orderby)->paginate($pagesize, false, $paginate);
        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($pages->total() > 0) {
            $list = $pages->items();
            $aids = get_arr_column($list, 'aid');
            $fields = "b.*, a.*";
            $row = Db::name('archives')->field($fields)->alias('a')->join('arctype b', 'a.typeid = b.id', 'LEFT')->where('a.aid', 'in', $aids)->getAllWithIndex('aid');
            // 获取模型对应的控制器名称
            $channel_list = M('Channeltype')->getAll('id, ctl_name', array(), 'id');
            foreach ($list as $key => $val) {
                $arcval = $row[$val['aid']];
                $controller_name = $channel_list[$arcval['channel']]['ctl_name'];
                // 获取指定路由模式下的URL
                if ($arcval['is_part'] == 1) {
                    $arcval['typeurl'] = $arcval['typelink'];
                } else {
                    $arcval['typeurl'] = typeurl('home/' . $controller_name . "/lists", $arcval);
                }
                // 文档链接
                if ($arcval['is_jump'] == 1) {
                    $arcval['arcurl'] = $arcval['jumplinks'];
                } else {
                    $arcval['arcurl'] = arcurl('home/' . $controller_name . "/view", $arcval);
                }
                // 封面图
                if (empty($arcval['litpic'])) {
                    $arcval['is_litpic'] = 0;
                    // 无封面图
                } else {
                    $arcval['is_litpic'] = 1;
                    // 有封面图
                }
                // 默认封面图
                $arcval['litpic'] = thumb_img(get_default_pic($arcval['litpic']));
                $list[$key] = $arcval;
            }
            // 附加表
            if (!empty($addfields) && !empty($list)) {
                // 模型对应数据表
                $channeltypeRow = M('Channeltype')->getAll('id,table', [], 'id');
                // 模型下的文档集合
                $channelGroupRow = group_same_key($list, 'current_channel');
                foreach ($channelGroupRow as $channelid => $tmp_list) {
                    // 附加字段的数据表名
                    $addtableName = '';
                    $tmp_aid_arr = get_arr_column($tmp_list, 'aid');
                    // 每个模型对应的数据表
                    $channeltype_table = $channeltypeRow[$channelid]['table'];
                    // 替换中文逗号
                    $addfields = str_replace('，', ',', $addfields);
                    $addfields = trim($addfields, ',');
                    $addtableName = $channeltype_table . '_content';
                    $resultExt = Db::name($addtableName)->field("aid,{$addfields}")->where('aid', 'in', implode(',', $tmp_aid_arr))->getAllWithIndex('aid');
                    // 自定义字段的数据格式处理
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channelid, true);
                    foreach ($list as $key2 => $val2) {
                        $valExt = !empty($resultExt[$val2['aid']]) ? $resultExt[$val2['aid']] : array();
                        $val2 = array_merge($valExt, $val2);
                        $list[$key2] = $val2;
                    }
                }
            }
        }
        // 分页显示输出
        $result['pages'] = $pages;
        // 赋值数据集
        $result['list'] = $list;
        return $result;
    }
}