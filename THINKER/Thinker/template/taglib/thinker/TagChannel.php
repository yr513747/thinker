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
// [ 栏目列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagChannel extends Base
{
    protected $tid = '';
    protected $currentstyle = '';
    //初始化
    protected function init()
    {
        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');
        // 应用于文档列表
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $cacheKey = 'tagChannel_' . strtolower('home_' . $this->params['controller_name'] . '_' . $this->params['action_name']);
            $cacheKey .= "_{$aid}";
            $this->tid = cache($cacheKey);
            if ($this->tid == false) {
                $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
                cache($cacheKey, $this->tid);
            }
        }
        // tid为目录名称的情况下
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身    
     */
    public function getChannel($typeid = '', $type = 'top', $currentstyle = '', $notypeid = '')
    {
        $this->currentstyle = $currentstyle;
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        if (empty($typeid)) {
            // 应用于没有指定tid的列表，默认获取该控制器下的第一级栏目ID
            $controller_name = $this->params['controller_name'];
            $channeltype_info = M('Channeltype')->getInfoByWhere(array('ctl_name' => $controller_name), 'id');
            $channeltype = $channeltype_info['id'];
            $map = array(
                //
                'channeltype' => $channeltype,
                'parent_id' => 0,
                'is_hidden' => 0,
                'status' => 1,
            );
            $typeid = Db::name('arctype')->where($map)->order('sort_order asc')->limit(1)->getField('id');
        }
        $result = $this->getSwitchType($typeid, $type, $notypeid);
        return $result;
    }
    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     */
    public function getSwitchType($typeid = '', $type = 'top', $notypeid = '')
    {
        $result = array();
        switch ($type) {
            case 'son':
                // 下级栏目
                $result = $this->getSon($typeid, false);
                break;
            case 'self':
                // 同级栏目
                $result = $this->getSelf($typeid);
                break;
            case 'top':
                // 顶级栏目
                $result = $this->getTop($notypeid);
                break;
            case 'sonself':
                // 下级、同级栏目
                $result = $this->getSon($typeid, true);
                break;
            case 'first':
                // 第一级栏目
                $result = $this->getFirst($typeid);
                break;
        }
        // 处理自定义表字段的值
        if (!empty($result)) {
            // 获取自定义表字段信息
            $map = array('channel_id' => config('global.arctype_channel_id'));
            $fieldInfo = M('Channelfield')->getListByWhere($map, '*', 'name');
            foreach ($result as $key => $val) {
                if (!empty($val)) {
                    $val = L('FieldLogic', 'home')->handleAddonFieldList($val, $fieldInfo);
                    $result[$key] = $val;
                }
            }
        }
        return $result;
    }
    /**
     * 获取下一级栏目
     * @param string $self true表示没有子栏目时，获取同级栏目
     */
    public function getSon($typeid, $self = false)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        // 栏目最多级别
        $arctype_max_level = intval(config('global.arctype_max_level'));
        // 获取所有显示且有效的栏目列表
        $map = array(
            'c.is_hidden' => 0,
            'c.status' => 1,
            // 回收站功能
            'c.is_del' => 0,
        );
        $fields = "c.*, c.id as typeid, count(s.id) as has_children, '' as children";
        $res = Db::name('arctype')->field($fields)->alias('c')->join('arctype s', 's.parent_id = c.id', 'LEFT')->where($map)->group('c.id')->order('c.parent_id asc, c.sort_order asc, c.id')->cache(true, CACHE_TIME, "arctype")->getArray();
        if ($res) {
            $ctl_name_list = M('Channeltype')->getAll('id,ctl_name', array(), 'id');
            foreach ($res as $key => $val) {
                // 获取指定路由模式下的URL
                if ($val['is_part'] == 1) {
                    $val['typeurl'] = $val['typelink'];
                    if (!is_http_url($val['typeurl'])) {
                        $typeurl = '//' . $this->request->host();
                        if (!preg_match('#^' . $this->root_dir . '(.*)$#i', $val['typeurl'])) {
                            $typeurl .= $this->root_dir;
                        }
                        $typeurl .= '/' . trim($val['typeurl'], '/');
                        $val['typeurl'] = $typeurl;
                    }
                } else {
                    $ctl_name = $ctl_name_list[$val['current_channel']]['ctl_name'];
                    $val['typeurl'] = typeurl('home/' . $ctl_name . "/lists", $val);
                }
                // 标记栏目被选中效果
                if ($val['id'] == $typeid || $val['id'] == $this->tid) {
                    $val['currentstyle'] = $this->currentstyle;
                } else {
                    $val['currentstyle'] = '';
                }
                // 封面图
                $val['litpic'] = handle_subdir($val['litpic']);
                $res[$key] = $val;
            }
        }
        // 栏目层级归类成阶梯式
        $arr = group_same_key($res, 'parent_id');
        for ($i = 0; $i < $arctype_max_level; $i++) {
            foreach ($arr as $key => $val) {
                foreach ($arr[$key] as $key2 => $val2) {
                    if (!isset($arr[$val2['id']])) {
                        continue;
                    }
                    $val2['children'] = $arr[$val2['id']];
                    $arr[$key][$key2] = $val2;
                }
            }
        }
        // 取得指定栏目ID对应的阶梯式所有子孙等栏目
        $result = array();
        $typeidArr = explode(',', $typeid);
        foreach ($typeidArr as $key => $val) {
            if (!isset($arr[$val])) {
                continue;
            }
            if (is_array($arr[$val])) {
                foreach ($arr[$val] as $key2 => $val2) {
                    array_push($result, $val2);
                }
            } else {
                array_push($result, $arr[$val]);
            }
        }
        // 没有子栏目时，获取同级栏目
        if (empty($result) && $self == true) {
            $result = $this->getSelf($typeid);
        }
        return $result;
    }
    /**
     * 获取当前栏目的第一级栏目下的子栏目
     */
    public function getFirst($typeid)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        // 当前栏目往上一级级父栏目
        $row = M('Arctype')->getAllPid($typeid);
        if (!empty($row)) {
            reset($row);
            // 顶级栏目下的第一级父栏目
            $firstResult = current($row);
            $typeid = isset($firstResult['id']) ? $firstResult['id'] : '';
            // 获取第一级栏目下的子孙栏目，为空时不获得同级栏目
            $sonRow = $this->getSon($typeid, false);
            $result = $sonRow;
        }
        return $result;
    }
    /**
     * 获取同级栏目
     */
    public function getSelf($typeid)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        // 获取指定栏目ID的上一级栏目ID列表
        $map = array(
            'is_hidden' => 0,
            'status' => 1,
            // 回收站功能
            'is_del' => 0,
        );
        $res = Db::name('arctype')->field('parent_id')->where('id', 'in', $typeid)->where($map)->group('parent_id')->getArray();
        // 获取上一级栏目ID对应下的子孙栏目
        if ($res) {
            $typeidArr = get_arr_column($res, 'parent_id');
            $typeid = implode(',', $typeidArr);
            if ($typeid == 0) {
                $result = $this->getTop();
            } else {
                $result = $this->getSon($typeid, false);
            }
        }
        return $result;
    }
    /**
     * 获取顶级栏目
     */
    public function getTop($notypeid = '')
    {
        $result = array();
        // 获取所有栏目
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $map = [];
        $map[] = ['is_hidden', '=', 0];
        // 回收站功能
        $map[] = ['is_del', '=', 0];
        $map[] = ['status', '=', 1];
        // 排除指定栏目ID
        !empty($notypeid) && ($map[] = ['id', 'NOTIN', $notypeid]);
        $res = L('ArctypeLogic', 'common')->arctypeList(0, 0, false, $arctype_max_level, $map);
        if (count($res) > 0) {
            $topTypeid = $this->getTopTypeid($this->tid);
            $ctl_name_list = M('Channeltype')->getAll('id,ctl_name', array(), 'id');
            $currentstyleArr = ['tid' => 0, 'currentstyle' => '', 'grade' => 100, 'is_part' => 0];
            // 标记选择栏目的数组
            foreach ($res as $key => $val) {
                // 获取指定路由模式下的URL
                if ($val['is_part'] == 1) {
                    $val['typeurl'] = $val['typelink'];
                    if (!is_http_url($val['typeurl'])) {
                        $typeurl = '//' . $this->request->host();
                        if (!preg_match('#^' . $this->root_dir . '(.*)$#i', $val['typeurl'])) {
                            $typeurl .= $this->root_dir;
                        }
                        $typeurl .= '/' . trim($val['typeurl'], '/');
                        $val['typeurl'] = $typeurl;
                    }
                } else {
                    $ctl_name = $ctl_name_list[$val['current_channel']]['ctl_name'];
                    $val['typeurl'] = typeurl('home/' . $ctl_name . "/lists", $val);
                }
                // 标记栏目被选中效果
                $val['currentstyle'] = '';
                $pageurl = $this->request->url(true);
                $typelink = htmlspecialchars_decode($val['typelink']);
                if ($val['id'] == $topTypeid || !empty($typelink) && stristr($pageurl, $typelink)) {
                    $is_currentstyle = false;
                    // 当前栏目不是顶级栏目，按外部链接优先
                    if ($topTypeid != $this->tid && 0 == $currentstyleArr['is_part'] && $val['grade'] <= $currentstyleArr['grade']) {
                        $is_currentstyle = true;
                    } else {
                        if ($topTypeid == $this->tid && $val['grade'] < $currentstyleArr['grade']) {
                            // 当前栏目是顶级栏目，按顺序优先
                            $is_currentstyle = true;
                        }
                    }
                    if ($is_currentstyle) {
                        $currentstyleArr = ['tid' => $val['id'], 'currentstyle' => $this->currentstyle, 'grade' => $val['grade'], 'is_part' => $val['is_part']];
                    }
                }
                // 封面图
                $val['litpic'] = handle_subdir($val['litpic']);
                $res[$key] = $val;
            }
            // 循环处理选中栏目的标识
            foreach ($res as $key => $val) {
                if (!empty($currentstyleArr) && $val['id'] == $currentstyleArr['tid']) {
                    $val['currentstyle'] = $currentstyleArr['currentstyle'];
                }
                $res[$key] = $val;
            }
            // 栏目层级归类成阶梯式
            $arr = group_same_key($res, 'parent_id');
            for ($i = 0; $i < $arctype_max_level; $i++) {
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) {
                            continue;
                        }
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            reset($arr);
            // 获取第一个数组
            $firstResult = current($arr);
            $result = $firstResult;
        }
        return $result;
    }
    /**
     * 获取最顶级父栏目ID
     */
    public function getTopTypeid($typeid)
    {
        $topTypeId = 0;
        if ($typeid > 0) {
            // 当前栏目往上一级级父栏目
            $result = M('Arctype')->getAllPid($typeid);
            reset($result);
            // 获取最顶级父栏目ID
            $firstVal = current($result);
            $topTypeId = $firstVal['id'];
        }
        return intval($topTypeId);
    }
}