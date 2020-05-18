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
$cache_type = array();
// 全局变量数组
$global = config('tpcache');
empty($global) && ($global = tpCache('global'));
// 系统模式
$web_cmsmode = isset($global['web_cmsmode']) ? $global['web_cmsmode'] : 2;
if (1 == $web_cmsmode) {
    // 运营模式
    $cache_type = config('html.cache_type');
}
return array('cache_type' => $cache_type);