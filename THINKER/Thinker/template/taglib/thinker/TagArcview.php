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
// [ 获取栏目基本信息 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
use app\home\logic\FieldLogic;
class TagArcview extends Base
{
    public $aid = '';
    public $fieldLogic;
    //初始化
    protected function init()
    {
        $this->fieldLogic = new FieldLogic();
        // 应用于文档列表
        $this->aid = input('param.aid/d', 0);
    }
    public function getArcview($aid = '', $addfields = '', $joinaid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        $joinaid !== '' && ($aid = $joinaid);
        if (empty($aid)) {
            return false;
        }
        // 文档信息
        $result = Db::name("archives")->field('b.*, a.*')->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->getOne($aid);
        if (empty($result)) {
            echo '标签arcview报错：该文档ID(' . $aid . ')不存在。';
            return false;
        }
		// 默认封面图
        $result['litpic'] = get_default_pic($result['litpic']);
        // 获取查询的控制器名
        $channelInfo = M('Channeltype')->getInfo($result['channel']);
        $controller_name = $channelInfo['ctl_name'];
        $channeltype_table = $channelInfo['table'];
        // 栏目链接
        if ($result['is_part'] == 1) {
            $result['typeurl'] = $result['typelink'];
        } else {
            $result['typeurl'] = typeurl('home/' . $controller_name . "/lists", $result);
        }
        // 文档链接
        if ($result['is_jump'] == 1) {
            $result['arcurl'] = $result['jumplinks'];
        } else {
            $result['arcurl'] = arcurl('home/' . $controller_name . '/view', $result);
        }
        // 附加表
        if (!empty($addfields)) {
            // 替换中文逗号
            $addfields = str_replace('，', ',', $addfields);
            $addfields = trim($addfields, ',');
        } else {
            $addfields = '*';
        }
        $tableContent = $channeltype_table . '_content';
        $row = Db::name($tableContent)->field($addfields)->where('aid', $aid)->getOne();
        if (is_array($row)) {
            $result = array_merge($result, $row);
        } else {
            $saveData = [
                //
                'aid' => $aid,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ];
            Db::name($tableContent)->save($saveData);
        }
        // 自定义字段的数据格式处理
        $result = $this->fieldLogic->getChannelFieldList($result, $result['channel']);
        $result = view_logic($aid, $result['channel'], $result, true);
        return $result;
    }
}