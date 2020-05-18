<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\exception\HttpException;
if (!function_exists('weapp_url')) {
    /**
     * 插件显示内容里生成访问插件的url
     * @param string        $url 路由地址
     * @param string|array  $vars 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function weapp_url($url = '', $vars = '', $suffix = true, $domain = false, $toString = true)
    {
        if (stristr($url, '://')) {
            $urlinfo        =  parse_url($url);   // Curd://Curd/index ====> Array ( [scheme] => Curd [host] => Curd [path] => /index )
            $sm     =  $urlinfo['scheme'];
            $sc     =  $urlinfo['host'];
            $sa     =  isset($urlinfo['path']) ? substr($urlinfo['path'], 1) : "index";
        } else {
            $urlinfo = explode('/', $url); // Curd/Curd/index
            if (1 >= count($urlinfo)) {
                throw new HttpException(0, '插件weapp_url函数的参数不符合规范！');
            } else if (2 == count($urlinfo)) {
                $sm     =  $urlinfo[0];
                $sc     =  $urlinfo[0];
                $sa     =  $urlinfo[1];
            } else if (3 == count($urlinfo)) {
                $sm     =  $urlinfo[0];
                $sc     =  $urlinfo[1];
                $sa     =  $urlinfo[2];
            }
            $sa     =  !empty($sa) ? $sa : "index";
        }

        /* 基础参数 */
		$weapp_app = config('route.weapp_app', 'm');
        $weapp_controller = config('route.weapp_controller', 'c');
        $weapp_action = config('route.weapp_action', 'a');
        $params_array = array(
            $weapp_app        => $sm,
            $weapp_controller => $sc,
            $weapp_action     => $sa,
        );

        if (is_string($vars)) {
            $vars = rtrim($vars, '&');
            $vars .= '&'.http_build_query($params_array);
        } else if (is_array($vars)) {
            $vars = array_merge($vars, $params_array); //添加额外参数
        }

        $url = url('Weapp/execute', $vars, $suffix, $domain, $toString);
        return $url;
    }
}