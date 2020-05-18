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

if (!function_exists('getTime')) 
{
    /**
     * 获取当前请求的时间
     * @access public
     * @param  bool $float 是否使用浮点类型
     * @return integer|float
     */
    function getTime(bool $float = false)
    {
        return $float ? $_SERVER[strtoupper('REQUEST_TIME_FLOAT')] : $_SERVER[strtoupper('REQUEST_TIME')];
    }
}
if (!function_exists('MyDate')) 
{
    /**
     *  时间转化日期格式
     *
     * @param     string  $format     日期格式
     * @param     intval  $t     时间戳
     * @return    string
     */
    function MyDate($format = 'Y-m-d', $t = '')
    {
        if (!empty($t)) {
            $t = date($format, $t);
        }
        return $t;
    }
}