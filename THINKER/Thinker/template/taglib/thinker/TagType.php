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
// [ 栏目基本信息 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use app\home\logic\FieldLogic;
use think\facade\Db;
class TagType extends Base
{
    protected $tid = '';
    protected $fieldLogic;
    //初始化
    protected function init()
    {
        $this->fieldLogic = new FieldLogic();
        $this->tid = input("param.tid/s", '');
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
        }
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    /**
     * 获取栏目基本信息
     */
    public function getType($typeid = '', $type = 'self', $addfields = '')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        if (empty($typeid)) {
            echo '标签type报错：缺少属性 typeid 值，或栏目ID不存在。';
            return false;
        }
        $result = array();
        switch ($type) {
            case 'top':
                $result = $this->getTop($typeid);
                break;
            default:
                $result = $this->getSelf($typeid);
                break;
        }
        $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
        // 当前单页栏目的内容信息
        if (!empty($addfields) && isset($result['nid']) && $result['nid'] == 'single') {
			// 兼容之前的版本
            $addfields = str_replace('single_content', 'content', $addfields);
            // 替换中文逗号
            $addfields = str_replace('，', ',', $addfields);         
            $addfields = trim($addfields, ',');
            $row = Db::name('single_content')->field($addfields)->where('typeid', $result['id'])->getOne();
            $row = $this->fieldLogic->getChannelFieldList($row, $result['current_channel']);
            $result = array_merge($row, $result);
        }
        return $result;
    }
    /**
     * 获取当前栏目基本信息
     */
    public function getSelf($typeid)
    {
        // 当前栏目信息
        $result = M("Arctype")->getInfo($typeid);
        return $result;
    }
    /**
     * 获取当前栏目的第一级栏目基本信息
     */
    public function getTop($typeid)
    {
        // 获取当前栏目的所有父级栏目
        $parent_list = M('Arctype')->getAllPid($typeid);
        // 第一级栏目
        $result = current($parent_list);
        return $result;
    }
}