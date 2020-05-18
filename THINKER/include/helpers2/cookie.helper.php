<?php  if(!defined('DEDEINC')) exit('thinker');
/**
 * Cookie处理小助手
 *
 * @version        $Id: file.helper.php 1 13:58 2010年7月5日Z tianya $
 * @package        DedeCMS.Helpers
 * @copyright      Copyright (c) 2007 - 2019, DesDev, Inc.
 * @license        http://help.thinker.com/usersguide/license.html
 * @link           http://www.thinker.com
 */

/**
 *  设置Cookie记录
 *
 * @param     string  $key    键
 * @param     string  $value  值
 * @param     string  $kptime  保持时间
 * @param     string  $pa     保存路径
 * @return    void
 */
if ( ! function_exists('PutCookie'))
{
    function PutCookie($key, $value, $kptime=0, $pa="/")
    {
        global $cfg_cookie_encode,$cfg_domain_cookie;
        setcookie($key, $value, getTime()+$kptime, $pa,$cfg_domain_cookie);
        setcookie($key.'__ckMd5', substr(md5($cfg_cookie_encode.$value),0,16), getTime()+$kptime, $pa,$cfg_domain_cookie);
    }
}


/**
 *  清除Cookie记录
 *
 * @param     $key   键名
 * @return    void
 */
if ( ! function_exists('DropCookie'))
{
    function DropCookie($key)
    {
        global $cfg_domain_cookie;
        setcookie($key, '', getTime()-360000, "/",$cfg_domain_cookie);
        setcookie($key.'__ckMd5', '', getTime()-360000, "/",$cfg_domain_cookie);
    }
}

/**
 *  获取Cookie记录
 *
 * @param     $key   键名
 * @return    string
 */
if ( ! function_exists('GetCookie'))
{
    function GetCookie($key)
    {
        global $cfg_cookie_encode;
        if( !isset($_COOKIE[$key]) || !isset($_COOKIE[$key.'__ckMd5']) )
        {
            return '';
        }
        else
        {
            if($_COOKIE[$key.'__ckMd5']!=substr(md5($cfg_cookie_encode.$_COOKIE[$key]),0,16))
            {
                return '';
            }
            else
            {
                return $_COOKIE[$key];
            }
        }
    }
}


