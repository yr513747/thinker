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
// +-------------------------------------------------------------------------
// | 模板缓存规则
// | 假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html
// | 就保存名字为 index_goods_goodsinfo_1.html
// | 配置成这样, 指定 模块 控制器 方法名 参数名
// +-------------------------------------------------------------------------
// --------------------------------------------------------------------------
use think\facade\Db;
use app\common\model\Config as ConfigModel;
// 模板缓存规则
$cache_type = [];
// 全局变量数组
$global = config('tpcache');
empty($global) && ($global = ConfigModel::tpCache('global'));
// 系统模式
$web_cmsmode = isset($global['web_cmsmode']) ? $global['web_cmsmode'] : 2;
// 页面缓存有效期
$web_htmlcache_expires_in = -1;
// 运营模式
if (1 == $web_cmsmode) {
    $web_htmlcache_expires_in = isset($global['web_htmlcache_expires_in']) ? $global['web_htmlcache_expires_in'] : 7200;
}
// 引入全部插件的页面缓存规则
$weappRow = Db::name('weapp')->field('code')->where('status', 1)->cache(true, null, "weapp")->getArray();
foreach ($weappRow as $key => $val) {
    $file = root_path('weapp') . $val['code'] . DS . 'html.php';
    if (file_exists($file)) {
        $html_value = (include_once $file);
        if (empty($html_value)) {
            continue;
        }
        foreach ($html_value as $k => $v) {
            if (!empty($v) && is_array($v)) {
                $v = array_merge($v, ['cache' => $web_htmlcache_expires_in]);
                $html_value[$k] = $v;
            }
        }
        $cache_type = array_merge($html_value, $cache_type);
    }
}
return ['cache_type' => $cache_type];