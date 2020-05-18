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
// [ 系统核心函数存放文件 ]
// --------------------------------------------------------------------------
defined('DEBUG_LEVEL') || exit;
// --------------------------------------------------------------------------
if (!function_exists('get_weapp_class')) 
{
    /**
     * 获取插件类的类名
     *
     * @param strng $name 插件名
     * @param strng $controller 控制器
     * @return class
     */
    function get_weapp_class($name, $controller = ''){
        $controller = !empty($controller) ? $controller : $name;
        $class = "\\weapp\\{$name}\\controller\\{$controller}";
        return $class;
    }
}