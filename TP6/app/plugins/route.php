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
// [ Plug in routing rules ]
// --------------------------------------------------------------------------
declare (strict_types=1);
use think\facade\Db;
$Plug_in_routing_rules = [];
try {
    $Plug_in_data_list = Db::name('weapp')->comment('引入全部插件的路由配置')->field('code')->where('status', 1)->cache(true, null, "weapp")->getArray();
    foreach ($Plug_in_data_list as $key => $value) {
        $Sub_plug_in_routing_file = root_path('weapp') . $value['code'] . DIRECTORY_SEPARATOR . 'route.php';
        if (is_file($Sub_plug_in_routing_file)) {
            $Sub_plug_in_routing_rules = (include_once $Sub_plug_in_routing_file);
            if (!empty($Sub_plug_in_routing_rules) && is_array($Sub_plug_in_routing_rules)) {
                $Plug_in_routing_rules = array_merge($Sub_plug_in_routing_rules, $Plug_in_routing_rules);
            }
        }
    }
} catch (\PDOException $e) {
    $Plug_in_routing_rules = [];
} catch (\Exception $e) {
    throw $e;
}
$Plug_in_routing_global_parameters = ['__pattern__' => [], '__alias__' => [], '__domain__' => []];
$Plug_in_routing_collection = array_merge($Plug_in_routing_global_parameters, $Plug_in_routing_rules);
return $Plug_in_routing_collection;