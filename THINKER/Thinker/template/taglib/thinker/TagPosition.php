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
// [ 获取面包屑位置 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
use app\common\model\Config as ConfigModel;
class TagPosition extends Base
{
    public $tid = '';
    //初始化
    protected function init()
    {
        $this->tid = input("param.tid/s", '');
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = Db::name('archives')->where('aid', $aid)->getField('typeid');
        }
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    public function getPosition($typeid = '', $symbol = '', $style = 'crumb')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        $basicConfig = ConfigModel::tpCache('basic');
        $basic_indexname = !empty($basicConfig['basic_indexname']) ? $basicConfig['basic_indexname'] : '首页';
        $symbol = !empty($symbol) ? $symbol : $basicConfig['list_symbol'];
        $homeURL = url('home/Index/index');
        $str = "<a href='{$homeURL}' class='{$style}'>{$basic_indexname}</a>";
        $result = M('Arctype')->getAllPid($typeid);
        $i = 1;
        foreach ($result as $key => $val) {
            if ($i < count($result)) {
                $str .= " {$symbol} <a href='{$val['typeurl']}' class='{$style}'>{$val['typename']}</a>";
            } else {
                $str .= " {$symbol} <a href='{$val['typeurl']}'>{$val['typename']}</a>";
            }
            ++$i;
        }
        return $str;
    }
}