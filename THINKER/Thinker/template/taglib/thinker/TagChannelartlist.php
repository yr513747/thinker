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
// [ 获取当前频道的下级栏目的内容列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagChannelartlist extends Base
{
    public $tid = '';
    //初始化
    protected function init()
    {
        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');
        // 应用于文档列表
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $cacheKey = 'tagChannelartlist_' . strtolower('home_' . $this->params['controller_name'] . '_' . $this->params['action_name']);
            $cacheKey .= "_{$aid}";
            $this->tid = cache($cacheKey);
            if ($this->tid == false) {
                $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
                cache($cacheKey, $this->tid);
            }
        }
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    /**
     * 获取当前频道的下级栏目的内容列表标签
     * @param string type son表示下一级栏目,self表示当前栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     */
    public function getChannelartlist($typeid = '', $type = 'self')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        if (empty($typeid)) {
            $type = 'top';
            // 默认顶级栏目
        }
        $result = $this->getSwitchType($typeid, $type);
        return $result;
    }
    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     */
    public function getSwitchType($typeid = '', $type = 'son')
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
                $result = $this->getTop();
                break;
            case 'sonself':
                // 下级、同级栏目
                $result = $this->getSon($typeid, true);
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
        $map = array();
        if ($self) {
            $map[] = array('id|parent_id', 'IN', $typeid);
        } else {
            $map[] = array('parent_id', 'IN', $typeid);
        }
        $map[] = array('is_hidden', '=', 0);
        $map[] = array('status', '=', 1);
        $map[] = array('is_del', '=', 0);
        $result = Db::name('arctype')->field('*, id as typeid')->where($map)->order('sort_order asc')->getArray();
        if ($result) {
            $ctl_name_list = M('Channeltype')->getAll('id,ctl_name', array(), 'id');
            foreach ($result as $key => $val) {
                // 获取指定路由模式下的URL
                if ($val['is_part'] == 1) {
                    $typeurl = $val['typelink'];
                } else {
                    $ctl_name = $ctl_name_list[$val['current_channel']]['ctl_name'];
                    $typeurl = typeurl('home/' . $ctl_name . "/lists", $val);
                }
                $val['typeurl'] = $typeurl;
                // 封面图
                $val['litpic'] = handle_subdir($val['litpic']);
                $result[$key] = $val;
            }
        }
        return $result;
    }
    /**
     * 获取当前栏目
     */
    public function getSelf($typeid)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        $map = array();
        $map[] = array('id', 'IN', $typeid);
        $map[] = array('is_hidden', '=', 0);
        $map[] = array('status', '=', 1);
        $map[] = array('is_del', '=', 0);
        $result = Db::name('arctype')->field('*, id as typeid')->where($map)->order('sort_order asc')->getArray();
        if ($result) {
            $ctl_name_list = M('Channeltype')->getAll('id,ctl_name', array(), 'id');
            foreach ($result as $key => $val) {
                // 获取指定路由模式下的URL
                if ($val['is_part'] == 1) {
                    $typeurl = $val['typelink'];
                } else {
                    $ctl_name = $ctl_name_list[$val['current_channel']]['ctl_name'];
                    $typeurl = typeurl('home/' . $ctl_name . "/lists", $val);
                }
                $val['typeurl'] = $typeurl;
                // 封面图
                $val['litpic'] = handle_subdir($val['litpic']);
                $result[$key] = $val;
            }
        }
        return $result;
    }
    /**
     * 获取顶级栏目
     */
    public function getTop()
    {
        $map = array();
        $map[] = array('parent_id', '=', 0);
        $map[] = array('is_hidden', '=', 0);
        $map[] = array('status', '=', 1);
        $map[] = array('is_del', '=', 0);
        $result = Db::name('arctype')->field('*, id as typeid')->where($map)->order('sort_order asc')->getArray();
        if ($result) {
            $ctl_name_list = M('Channeltype')->getAll('id,ctl_name', array(), 'id');
            foreach ($result as $key => $val) {
                // 获取指定路由模式下的URL
                if ($val['is_part'] == 1) {
                    $typeurl = $val['typelink'];
                } else {
                    $ctl_name = $ctl_name_list[$val['current_channel']]['ctl_name'];
                    $typeurl = typeurl('home/' . $ctl_name . "/lists", $val);
                }
                $val['typeurl'] = $typeurl;
                // 封面图
                $val['litpic'] = handle_subdir($val['litpic']);
                $result[$key] = $val;
            }
        }
        return $result;
    }
}