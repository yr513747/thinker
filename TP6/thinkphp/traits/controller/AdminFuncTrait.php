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
namespace think\traits\controller;

trait AdminFuncTrait
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;
    /**
     * 把当前模型或数据集对象转换为数组
     * @access protected
     * @param  mixed $data 要转换的数据集或对象
     * @return array  
     */
    protected final function toArray($data = '') : array
    {
        if (empty($data)) {
            $data = array();
        }
        if (is_object($data) && is_callable([$data, 'toArray'])) {
            $data = $data->toArray();
        }
        if (!is_array($data)) {
            $data = array();
        }
        return $data;
    }
    /**
     * 转换SQL关键字
     * @access protected
     * @param  string|array $string
     * @return string
     */
    protected final function stripSql($string) : string
    {
        $pattern_arr = array(
            "/\\bunion\\b/i",
            "/\\bselect\\b/i",
            "/\\bupdate\\b/i",
            "/\\bdelete\\b/i",
            "/\\boutfile\\b/i",
            // "/\bor\b/i",
            "/\\bchar\\b/i",
            "/\\bconcat\\b/i",
            "/\\btruncate\\b/i",
            "/\\bdrop\\b/i",
            "/\\binsert\\b/i",
            "/\\brevoke\\b/i",
            "/\\bgrant\\b/i",
            "/\\breplace\\b/i",
            // "/\balert\b/i",
            "/\\brename\\b/i",
            // "/\bmaster\b/i",
            "/\\bdeclare\\b/i",
            // "/\bsource\b/i",
            // "/\bload\b/i",
            // "/\bcall\b/i",
            "/\\bexec\\b/i",
            "/\\bdelimiter\\b/i",
            "/\\bphar\\b\\:/i",
            "/\\bphar\\b/i",
            "/\\@(\\s*)\\beval\\b/i",
            "/\\beval\\b/i",
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
        return is_array($string) ? array_map(array($this, 'stripSql'), $string) : preg_replace($pattern_arr, $replace_arr, $string);
    }
    /**
     * 对字符串进行SQL注入过滤.
     * @access protected
     * @param  string|array  $string 处理的字符串或数组
     * @return array|string 
     */
    protected final function sqlinsert($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->sqlinsert($val);
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
    /**
     * 字段权限控制代码加密后（加密后可用URL传递）.
     * @access protected
     * @param  string  $string    需要加密或解密的字符串
     * @param  string  $operation ENCODE:加密，DECODE:解密
     * @param  string  $key       密钥
     * @param  int     $expiry    加密有效时间
     * @return string 
     */
    protected final function authcode(string $string, string $operation = 'DECODE', string $key = '', int $expiry = 0) : string
    {
        $ckey_length = 4;
        $key = md5($key ? $key : $this->request->secureKey());
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
    /**
     * 16进制ASCII码转换成字符
     * @access protected
     * @param  array $data 数据
     * @return mixed
     */
    protected final function asciiToString($data)
    {
        $result = "";
        for ($i = 0; isset($data[$i]); $i++) {
            $result .= chr($data[$i]);
        }
        return $result;
    }
    /**
     * 字符串转化为16进制ASCII码
     * @access protected
     * @param  string $data 数据
     * @return mixed
     */
    protected final function stringToAscii($data)
    {
        $result = array();
        $box = str_split($data, 1);
        foreach ($box as $key => $value) {
            $result[$key] = "0x" . bin2hex($value);
        }
        return $result;
    }
    /**
     * 生成UUID  UUID的标准格式为“xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxx”
     * @access protected
     * @return string 
     */
    protected final function uuid() : string
    {
        $chars = md5(uniqid((string) mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-' . substr($chars, 8, 4) . '-' . substr($chars, 12, 4) . '-' . substr($chars, 16, 4) . '-' . substr($chars, 20, 12);
        return $uuid;
    }
    /**
     * 屏蔽电话号码中间四位
     * @access protected
     * @param  string $phone
     * @return string 
     */
    protected final function hidtel(string $phone) : string
    {
        //固定电话
        $IsWhat = preg_match('/(0[0-9]{2,3}[\\-]?[2-9][0-9]{6,7}[\\-]?[0-9]?)/i', $phone);
        if ($IsWhat == 1) {
            return preg_replace('/(0[0-9]{2,3}[\\-]?[2-9])[0-9]{3,4}([0-9]{3}[\\-]?[0-9]?)/i', '$1****$2', $phone);
        } else {
            return preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
        }
    }
}