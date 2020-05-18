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
// [ 插件路由配置 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
use think\facade\Db;
use think\facade\Route;
$plugins_route = [];
try {
    $weappRow = Db::name('weapp')->comment('引入全部插件的路由配置')->field('code')->where('status', 1)->cache(true, null, "weapp")->getArray();
    foreach ($weappRow as $key => $val) {
        $file = root_path('weapp') . $val['code'] . DIRECTORY_SEPARATOR . 'route.php';
        if (is_file($file)) {
            $route_value = (include_once $file);
            if (!empty($route_value) && is_array($route_value)) {
                $plugins_route = array_merge($route_value, $plugins_route);
            }
        }
    }
} catch (\PDOException $e) {
    return $plugins_route;
} catch (\Exception $e) {
    throw $e;
}
return $plugins_route;