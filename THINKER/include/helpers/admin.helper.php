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
if (!function_exists('GetEditor')) {
    /**
     * 获取编辑器
     * @param  string  $fname  表单名称
     * @param  string  $fvalue 如果表单中有默认值,则填入默认值
     * @param  string  $nheight 高度
     * @param  string  $etype   编辑器类型
     * @param  string  $gtype   获取类型
     * @param  string  $isfullpage   是否全屏
     * @return string
     */
    function GetEditor($fname, $fvalue, $nheight = "350", $etype = "Basic", $gtype = "print", $isfullpage = "FALSE", $bbcode = false)
    {
        if (!function_exists('SpGetEditor')) {
            require_once INC_PATH . "inc" . DIRECTORY_SEPARATOR . "inc_fun_funAdmin.php";
        }
        return SpGetEditor($fname, $fvalue, $nheight, $etype, $gtype, $isfullpage, $bbcode);
    }
}
if (!function_exists('strip_sql')) {
    /**
     * 转换SQL关键字
     * @param string/array $string 处理的字符串或数组
     * @return unknown
     */
    function strip_sql($string) {
        $pattern_arr = array(
                "/\bunion\b/i",
                "/\bselect\b/i",
                "/\bupdate\b/i",
                "/\bdelete\b/i",
                "/\boutfile\b/i",
                // "/\bor\b/i",
                "/\bchar\b/i",
                "/\bconcat\b/i",
                "/\btruncate\b/i",
                "/\bdrop\b/i",            
                "/\binsert\b/i", 
                "/\brevoke\b/i", 
                "/\bgrant\b/i",      
                "/\breplace\b/i", 
                // "/\balert\b/i", 
                "/\brename\b/i",            
                // "/\bmaster\b/i",
                "/\bdeclare\b/i",
                // "/\bsource\b/i",
                // "/\bload\b/i",
                // "/\bcall\b/i", 
                "/\bexec\b/i",         
                "/\bdelimiter\b/i",
                "/\bphar\b\:/i",
                "/\bphar\b/i",
                "/\@(\s*)\beval\b/i",
                "/\beval\b/i",
        );
        $replace_arr = array(
                'ｕｎｉｏｎ',
                'ｓｅｌｅｃｔ',
                'ｕｐｄａｔｅ',
                'ｄｅｌｅｔｅ',
                'ｏｕｔｆｉｌｅ',
                // 'ｏｒ',
                'ｃｈａｒ',
                'ｃｏｎｃａｔ',
                'ｔｒｕｎｃａｔｅ',
                'ｄｒｏｐ',            
                'ｉｎｓｅｒｔ',
                'ｒｅｖｏｋｅ',
                'ｇｒａｎｔ',
                'ｒｅｐｌａｃｅ',
                // 'ａｌｅｒｔ',
                'ｒｅｎａｍｅ',
                // 'ｍａｓｔｅｒ',
                'ｄｅｃｌａｒｅ',
                // 'ｓｏｕｒｃｅ',
                // 'ｌｏａｄ',
                // 'ｃａｌｌ',                 
                'ｅｘｅｃ',         
                'ｄｅｌｉｍｉｔｅｒ',
                'ｐｈａｒ',
                'ｐｈａｒ',
                '＠ｅｖａｌ',
                'ｅｖａｌ',
        );
     
        return is_array($string) ? array_map('strip_sql', $string) : preg_replace($pattern_arr, $replace_arr, $string);
    }
}

if (!function_exists('sqlinsert')) {
    /**
     * 对字符串进行SQL注入过滤.
     * @param string/array $string 处理的字符串或数组
     * @return array 返回处理好的字符串或数组
     */
    function sqlinsert($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = sqlinsert($val);
            }
        } else {
            $string_old = $string;
            $string = str_ireplace('\\', '/', $string);
            $string = str_ireplace('"', '/', $string);
            $string = str_ireplace("'", '/', $string);
            $string = str_ireplace('*', '/', $string);
            $string = str_ireplace('%5C', '/', $string);
            $string = str_ireplace('%22', '/', $string);
            $string = str_ireplace('%27', '/', $string);
            $string = str_ireplace('%2A', '/', $string);
            $string = str_ireplace('~', '/', $string);
            $string = str_ireplace('select', "\\sel\33ct", $string);
            $string = str_ireplace('insert', "\\ins\33rt", $string);
            $string = str_ireplace('update', "\\up\\date", $string);
            $string = str_ireplace('delete', "\\de\\lete", $string);
            $string = str_ireplace('union', "\\un\\ion", $string);
            $string = str_ireplace('into', "\\in\to", $string);
            $string = str_ireplace('load_file', "\\load\\_\file", $string);
            $string = str_ireplace('outfile', "\\out\file", $string);
            $string = str_ireplace('sleep', "\\sle\33p", $string);
            $string = strip_tags($string);
            if ($string_old != $string) {
                $string = '';
            }
            $string = trim($string);
        }
        return $string;
    }
}

if (!function_exists('json_encode')) {
    /**
     * json_encode兼容函数
     * @param  string  $data
     * @return string
     */
    function format_json_value(&$value)
    {
        if(is_bool($value)) {
            $value = $value?'TRUE':'FALSE';
        } else if (is_int($value)) {
            $value = intval($value);
        } else if (is_float($value)) {
            $value = floatval($value);
        } else if (defined($value) && $value === NULL) {
            $value = strval(constant($value));
        } else if (is_string($value)) {
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }
    function json_encode($data)
    {
        if(is_object($data)) {
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  FALSE;
        }else {
            $assoc  =  TRUE;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_NULL($val)) {
                if($assoc) {
                    $json .= "\"$key\":".json_encode($val).",";
                }else {
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}
if (!function_exists('json_decode')) {
    /**
     * json_decode兼容函数
     * @param  string  $json  json数据
     * @param  string  $assoc  当该参数为 TRUE 时，将返回 array 而非 object
     * @return string
     */
    function json_decode($json, $assoc = FALSE)
    {
        // 目前不支持二维数组或对象
        $begin  =  substr($json,0,1) ;
        if(!in_array($begin,array('{','[')))
            // 不是对象或者数组直接返回
            return $json;
        $parse = substr($json,1,-1);
        $data  = explode(',',$parse);
        if($flag = $begin =='{' ) {
            // 转换成PHP对象
            $result   = new stdClass();
            foreach($data as $val) {
                $item    = explode(':',$val);
                $key =  substr($item[0],1,-1);
                $result->$key = json_decode($item[1],$assoc);
            }
            if($assoc)
                $result   = get_object_vars($result);
        }else {
            // 转换成PHP数组
            $result   = array();
            foreach($data as $val)
                $result[]  =  json_decode($val,$assoc);
        }
        return $result;
    }
}

if (!function_exists('scandir')) {
    /**
     * scandir 兼容函数
     * @param  string  $dir  文件目录
     * @param  string  $type 返回数据
     * @return array
     */
    function scandir($dir, $type = 'all')
    {
        $files = [];
        if (!is_dir($dir)) {
            return $files;
        } else {
            $mydir = dir($dir);
            while ($file = $mydir->read()) {
                $files[] = "{$file}";
            }
            $mydir->close();
        }
        $arr_file = [];
        foreach ($files as $key => $val) {
            if ($val != "." and $val != "..") {
                if ('all' == $type) {
                    $arr_file[] = "{$val}";
                } elseif ('file' == $type && is_file($val)) {
                    $arr_file[] = "{$val}";
                } elseif ('dir' == $type && is_dir($val)) {
                    $arr_file[] = "{$val}";
                }
            }
        }
        return $arr_file;
    }
}

if (!function_exists('thinker_scandir')) {
    /**
     * 自定义scandir函数
     * @param  string  $dir  文件目录
     * @param  string  $type 返回数据
     * @return array
     */
    function thinker_scandir($dir, $type = 'all')
    {
        if (!is_dir($dir)) {
            return [];
        }
        if (function_exists('scandir')) {
            $files = scandir($dir);
        } else {
            $files = [];
            $mydir = dir($dir);
            while ($file = $mydir->read()) {
                $files[] = "{$file}";
            }
            $mydir->close();
        }
        $arr_file = [];
        foreach ($files as $key => $val) {
            if ($val != "." and $val != "..") {
                if ('all' == $type) {
                    $arr_file[] = "{$val}";
                } elseif ('file' == $type && is_file($val)) {
                    $arr_file[] = "{$val}";
                } elseif ('dir' == $type && is_dir($val)) {
                    $arr_file[] = "{$val}";
                }
            }
        }
        return $arr_file;
    }
}

if (!function_exists('authcode')) {
    /**
     * 字段权限控制代码加密后（加密后可用URL传递）.
     * @param string $string    需要加密或解密的字符串
     * @param string $operation ENCODE:加密，DECODE:解密
     * @param string $key       密钥
     * @param int    $expiry    加密有效时间
     * @return string 加密或解密后的字符串
     */
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key ? $key : UC_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? $operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + getTime() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; ++$i) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; ++$i) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; ++$i) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 256]);
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - getTime() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}