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
// [ 网站应用插件列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagWeapplist extends Base
{
    /**
     * 页面上展示网站应用插件列表
     * @access public
     * @param  string  $type
     * @param  string  $currentstyle
     * @return mixed
     */
    public function getWeapplist($type = 'usersmenu', $currentstyle = '')
    {
        $row = false;
        $map = [];
        $map[] = ['tag_weapp', '=', '1'];
        $map[] = ['status', '=', '1'];
        $result = Db::name('weapp')->comment('页面上展示网站应用插件列表')->field('name,code,config')->where($map)->where('position', $type)->cache(true, CACHE_TIME, 'hookslist')->getArray();
        foreach ($result as $key => $val) {
            $config = json_decode($val['config'], true);
            if (isMobile() && !in_array($config['scene'], [0, 1])) {
                continue;
            } else {
                if (!isMobile() && !in_array($config['scene'], [0, 2])) {
                    continue;
                }
            }
            $code = $val['code'];
            $link = !empty($config['link']) ? $config['link'] : 'user/Users/centre';
            $href = url($link);
            $menutitle = !empty($config['menutitle']) ? $config['menutitle'] : $val['name'];
            // 标记被选中效果
            if (stristr($link, $this->params['app_name'] . '/' . $this->params['controller_name'] . '/')) {
                $tmp_currentstyle = $currentstyle;
            } else {
                $tmp_currentstyle = '';
            }
            $row[] = [
                //
                'code' => $code,
                'href' => $href,
                'title' => $menutitle,
                'currentstyle' => $tmp_currentstyle,
            ];
        }
        return $row;
    }
}