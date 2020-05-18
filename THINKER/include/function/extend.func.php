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
// [ 用于兼容旧版本 ]
// --------------------------------------------------------------------------
defined('DEBUG_LEVEL') || exit;
// --------------------------------------------------------------------------

if (!function_exists('tpCache')) 
{
    function tpCache($config_key, $data = array(), $options = null)
    {
        return \app\common\model\Config::tpCache($config_key, $data, $options);
    }
}
if (!function_exists('getAllMenu')) 
{
    
    function getAllMenu()
    {
        return $modules = config('menu');
    }
}
if (!function_exists('getUsersConfigData')) 
{
    
    function getUsersConfigData($config_key, $data = array(), $options = null)
    {
        return \app\common\model\UsersConfig::getUsersConfigData($config_key, $data, $options);
    }
}
if (!function_exists('tpSetting')) 
{
	function tpSetting($config_key, $data = array(), $options = null)
    {
        return \app\common\model\Setting::tpSetting($config_key, $data, $options);
    }   
}
