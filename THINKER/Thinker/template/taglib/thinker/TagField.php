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
// [ 获取字段值 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagField extends Base
{
    public $aid = '';
    //初始化
    protected function init()
    {
        $this->aid = input('param.aid/d', 0);
    }
    /**
     * 获取字段值
     */
    public function getField($addfields = '', $aid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid)) {
            echo '标签field报错：缺少属性 aid 值，或文档ID不存在。';
            return false;
        }
        if (empty($addfields)) {
            echo '标签field报错：缺少属性 addfields 值。';
            return false;
        }
        $addfields = str_replace('，', ',', $addfields);
        $addfields = trim($addfields, ',');
        $addfields = explode(',', $addfields);
        $parseStr = '';
        $archivesRow = Db::name('archives')->field('typeid,channel')->where(['aid' => $aid])->getOne();
        if (empty($archivesRow)) {
            return $parseStr;
        }
        $channel = $archivesRow['channel'];
        // 获取栏目绑定的自定义字段ID列表
        $field_ids = Db::name('channelfield_bind')->where('typeid', 'IN', implode(',', [0, $archivesRow['typeid']]))->column('field_id');
        if (empty($field_ids)) {
            $fieldname = current($addfields);
        } else {
            // 获取栏目对应的频道下指定的自定义字段
            $row = Db::name('channelfield')->where([['id', 'IN', $field_ids], ['name', 'IN', $addfields], 'channel_id' => $channel])->field('name')->getAllWithIndex('name');
            foreach ($addfields as $key => $val) {
                if (!empty($row[$val])) {
                    $fieldname = $val;
                    break;
                }
            }
        }
        // 附加表
        if (!empty($fieldname)) {
            // 自定义字段的类型
            $dtype = Db::name('channelfield')->where([['name', '=', $fieldname], 'channel_id' => $channel])->getField('dtype');
            $channelInfo = M('Channeltype')->getInfo($channel);
            $tableContent = $channelInfo['table'] . '_content';
            $parseStr = Db::name($tableContent)->where('aid', $aid)->getField($fieldname);
            if ('htmltext' == $dtype) {
                $parseStr = htmlspecialchars_decode($parseStr);
            }
        }
        return $parseStr;
    }
}