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
if (!function_exists('dede_htmlspecialchars')) {
    function dede_htmlspecialchars($str)
    {
		return htmlspecialchars($str);    
    }
}

if (!function_exists('e')) {
    /**
     * 在字符串中编码HTML特殊字符。
     *
     * @param  string  $value
     * @param  bool  $doubleEncode
     * @return string
     */
    function e($value, $doubleEncode = true)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
}
if (!function_exists('is_url')) {
    /**
     * 字符串验证：URL
     * @param  string  $url		要验证的URL
     * @return boolean $flag	合法的URL返回true，否则返回false
     */
    function is_url($url)
    {
        $flag = true;
        $patten = '/http(s):\\/\\/[\\w.]+[\\w\\/]*[\\w.]*\\??[\\w=&\\+\\%]*/is';
        if (preg_match($patten, $url) == 0) {
            $flag = false;
        }
        return $flag;
    }
}

if (!function_exists('is_http_url')) 
{
    /**
     * 判断url是否完整的链接
     * @param  string $url 网址
     * @return boolean
     */
    function is_http_url($url)
    {
        // preg_match("/^(http:|https:|ftp:|svn:)?(\/\/).*$/", $url, $match);
        preg_match("/^((\w)*:)?(\/\/).*$/", $url, $match);
        if (empty($match)) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('text_msubstr')) 
{
    /**
     * 字符串截取，支持中文和其他编码 用于兼容旧版
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function text_msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        
        return msubstr($str, $start, $length, $suffix, $charset);
    }
}

if (!function_exists('msubstr')) 
{
    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }

        $str_len = strlen($str); // 原字符串长度
        $slice_len = strlen($slice); // 截取字符串的长度
        if ($slice_len < $str_len) {
            $slice = $suffix ? $slice.'...' : $slice;
        }

        return $slice;
    }
}

if (!function_exists('html_msubstr')) 
{
    /**
     * 截取内容清除html之后的字符串长度，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function html_msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        $str = check_htmlspecialchars_decode($str);
        $str = checkStrHtml($str);
        return msubstr($str, $start, $length, $suffix, $charset);
    }
}



if (!function_exists('check_htmlspecialchars_decode')) 
{
    /**
     * 自定义只针对htmlspecialchars编码过的字符串进行解码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function check_htmlspecialchars_decode($str='') {
        if (is_string($str) && stripos($str, '&lt;') !== false && stripos($str, '&gt;') !== false) {
            $str = htmlspecialchars_decode($str);
        }
        return $str;
    }
}

if (!function_exists('checkStrHtml')) 
{
    /**
     * 过滤Html标签
     *
     * @param     string  $string  内容
     * @return    string
     */
    function checkStrHtml($string){
        $string = trim_space($string);

        if(is_numeric($string)) return $string;
        if(!isset($string) or empty($string)) return '';

        $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string);
        $string  = ($string);

        $string = strip_tags($string,""); //清除HTML如<br />等代码
        // $string = str_replace("\n", "", str_replace(" ", "", $string));//去掉空格和换行
        $string = str_replace("\n", "", $string);//去掉空格和换行
        $string = str_replace("\t","",$string); //去掉制表符号
        $string = str_replace(PHP_EOL,"",$string); //去掉回车换行符号
        $string = str_replace("\r","",$string); //去掉回车
        $string = str_replace("'","‘",$string); //替换单引号
        $string = str_replace("&amp;","&",$string);
        $string = str_replace("=★","",$string);
        $string = str_replace("★=","",$string);
        $string = str_replace("★","",$string);
        $string = str_replace("☆","",$string);
        $string = str_replace("√","",$string);
        $string = str_replace("±","",$string);
        $string = str_replace("‖","",$string);
        $string = str_replace("×","",$string);
        $string = str_replace("∏","",$string);
        $string = str_replace("∷","",$string);
        $string = str_replace("⊥","",$string);
        $string = str_replace("∠","",$string);
        $string = str_replace("⊙","",$string);
        $string = str_replace("≈","",$string);
        $string = str_replace("≤","",$string);
        $string = str_replace("≥","",$string);
        $string = str_replace("∞","",$string);
        $string = str_replace("∵","",$string);
        $string = str_replace("♂","",$string);
        $string = str_replace("♀","",$string);
        $string = str_replace("°","",$string);
        $string = str_replace("¤","",$string);
        $string = str_replace("◎","",$string);
        $string = str_replace("◇","",$string);
        $string = str_replace("◆","",$string);
        $string = str_replace("→","",$string);
        $string = str_replace("←","",$string);
        $string = str_replace("↑","",$string);
        $string = str_replace("↓","",$string);
        $string = str_replace("▲","",$string);
        $string = str_replace("▼","",$string);

        // --过滤微信表情
        $string = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';}, $string);

        return $string;
    }
}

if (!function_exists('trim_space')) 
{
    /**
     * 过滤前后空格等多种字符
     *
     * @param string $str 字符串
     * @param array $arr 特殊字符的数组集合
     * @return string
     */
    function trim_space($str, $arr = array())
    {
        if (empty($arr)) {
            $arr = array(' ', '　');
        }
        foreach ($arr as $key => $val) {
            $str = preg_replace('/(^'.$val.')|('.$val.'$)/', '', $str);
        }

        return $str;
    }
}
if (!function_exists('filterEmoji')) {

	/**
     * 过滤掉emoji表情
     *
     * @param string $str 字符串
     * @return string
     */
    function filterEmoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
}

if (!function_exists('sensitive_words_filter')) {

    /**
     * 敏感词过滤
     *
     * @param string
     * @return string
     */
    function sensitive_words_filter($str)
    {
        if (!$str) return '';
        $file = root_path('data') . 'censorwords/CensorWords';
        $words = file($file);
        foreach ($words as $word) {
            $word = str_replace(array("\r\n", "\r", "\n", "/", "<", ">", "=", " "), '', $word);
            if (!$word) continue;

            $ret = preg_match("/$word/", $str, $match);
            if ($ret) {
                return $match[0];
            }
        }
        return '';
    }
}
