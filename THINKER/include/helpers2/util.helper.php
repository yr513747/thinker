<?php  if(!defined('DEDEINC')) exit('thinker');
/**
 * 核心小助手
 *
 * @version        $Id: util.helper.php 4 19:20 2010年7月6日Z tianya $
 * @package        DedeCMS.Helpers
 * @copyright      Copyright (c) 2007 - 2019, DesDev, Inc.
 * @license        http://help.thinker.com/usersguide/license.html
 * @link           http://www.thinker.com
 */
 
define('T_NEW_LINE', -1);

if (!function_exists('token_get_all_nl')) 
{
    function token_get_all_nl($source)
    {
        $new_tokens = array();

        // Get the tokens
        $tokens = token_get_all($source);

        // Split newlines into their own tokens
        foreach ($tokens as $token)
        {
            $token_name = is_array($token) ? $token[0] : null;
            $token_data = is_array($token) ? $token[1] : $token;

            // Do not split encapsed strings or multiline comments
            if ($token_name == T_CONSTANT_ENCAPSED_STRING || substr($token_data, 0, 2) == '/*')
            {
                $new_tokens[] = array($token_name, $token_data);
                continue;
            }

            // Split the data up by newlines
            $split_data = preg_split('#(\r\n|\n)#', $token_data, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            foreach ($split_data as $data)
            {
                if ($data == "\r\n" || $data == "\n")
                {
                    // This is a new line token
                    $new_tokens[] = array(T_NEW_LINE, $data);
                }
                else
                {
                    // Add the token under the original token name
                    $new_tokens[] = is_array($token) ? array($token_name, $data) : $data;
                }
            }
        }

        return $new_tokens;
    }
}
    
if (!function_exists('token_name_nl')) 
{
    function token_name_nl($token)
    {
        if ($token === T_NEW_LINE)
        {
            return 'T_NEW_LINE';
        }

        return token_name($token);
    }
}

/**
 *  获得当前的脚本网址
 *
 * @return    string
 */
if ( ! function_exists('GetCurUrl'))
{
    function GetCurUrl()
    {
        if(!empty($_SERVER["REQUEST_URI"]))
        {
            $scriptName = $_SERVER["REQUEST_URI"];
            $nowurl = $scriptName;
        }
        else
        {
            $scriptName = $_SERVER["PHP_SELF"];
            if(empty($_SERVER["QUERY_STRING"]))
            {
                $nowurl = $scriptName;
            }
            else
            {
                $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
            }
        }
        return $nowurl;
    }
}

/**
 *  获取用户真实地址
 *
 * @return    string  返回用户ip
 */
if ( ! function_exists('GetIP'))
{
    function GetIP()
    {
        static $realip = NULL;
        if ($realip !== NULL)
        {
            return $realip;
        }
        if (isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第x个非unknown的有效IP字符? */
                foreach ($arr as $ip)
                {
                    $ip = trim($ip);
                    if ($ip != 'unknown')
                    {
                        $realip = $ip;
                        break;
                    }
                }
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                if (isset($_SERVER['REMOTE_ADDR']))
                {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
                else
                {
                    $realip = '0.0.0.0';
                }
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $realip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }
}

/**
 *  获取编辑器
 *
 * @param     string  $fname  表单名称
 * @param     string  $fvalue 如果表单中有默认值,则填入默认值
 * @param     string  $nheight 高度
 * @param     string  $etype   编辑器类型
 * @param     string  $gtype   获取类型
 * @param     string  $isfullpage   是否全屏
 * @return    string
 */
if ( ! function_exists('GetEditor'))
{
    function GetEditor($fname, $fvalue, $nheight="350", $etype="Basic", $gtype="print", $isfullpage="FALSE",$bbcode=false)
    {
        if(!function_exists('SpGetEditor'))
        {
            require_once(DEDEINC."/inc/inc_fun_funAdmin.php");
        }
        return SpGetEditor($fname, $fvalue, $nheight, $etype, $gtype, $isfullpage, $bbcode);
    }
}

/**
 *  获取模板
 *
 * @param     string  $filename 文件名称
 * @return    string
 */
if ( ! function_exists('GetTemplets'))
{
    function GetTemplets($filename)
    {
        if(file_exists($filename))
        {
            $fp = fopen($filename,"r");
            $rstr = fread($fp,filesize($filename));
            fclose($fp);
            return $rstr;
        }
        else
        {
            return '';
        }
    }
}

/**
 *  获取系统模板
 *
 * @param     $filename  模板文件
 * @return    string
 */
if ( ! function_exists('GetSysTemplets'))
{
    function GetSysTemplets($filename)
    {
        return GetTemplets($GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'].'/system/'.$filename);
    }
}

/**
 *  获取新闻提示
 *
 * @return    void
 */
if ( ! function_exists('GetNewInfo'))
{
    function GetNewInfo()
    {
        if(!function_exists('SpGetNewInfo'))
        {
            require_once(DEDEINC."/inc/inc_fun_funAdmin.php");
        }
        return SpGetNewInfo();
    }
}

/**
 *  生成一个随机字符
 *
 * @access    public
 * @param     string  $ddnum
 * @return    string
 */
if ( ! function_exists('dd2char'))
{
    function dd2char($ddnum)
    {
        $ddnum = strval($ddnum);
        $slen = strlen($ddnum);
        $okdd = '';
        $nn = '';
        for($i=0;$i<$slen;$i++)
        {
            if(isset($ddnum[$i+1]))
            {
                $n = $ddnum[$i].$ddnum[$i+1];
                if( ($n>96 && $n<123) || ($n>64 && $n<91) )
                {
                    $okdd .= chr($n);
                    $i++;
                }
                else
                {
                    $okdd .= $ddnum[$i];
                }
            }
            else
            {
                $okdd .= $ddnum[$i];
            }
        }
        return $okdd;
    }
}
