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

if (!function_exists('arrayto_string')) {
    /**
     * 数组转换为字符串（多维情况）
     * @param  array   $arr  		要转换的数组
     * @param  string  $delimiter1  一维数组分割符
     * @param  string  $delimiter2  二维数组分割符
     * @param  string  $delimiter3  三维数组分割符
     * @return string  $str		  	返回由数组转换后的字符串，输入的数组不正确（数组为混合数组）返回false
     */
    function arrayto_string($arr, $decollator1 = ',', $decollator2 = '|', $decollator3 = '&')
    {
        if (array_level($arr) == 1) {
            $str = implode($decollator1, $arr);
        } else {
            if (array_level($arr) == 2) {
                $i = 0;
                foreach ($arr as $val) {
                    if (!is_array($val)) {
                        return false;
                    }
                    if ($i == 0) {
                        $str = implode($decollator1, $val);
                    } else {
                        $str .= $decollator2 . implode($decollator1, $val);
                    }
                    $i++;
                }
            } else {
                if (array_level($arr) == 3) {
                    $i = 0;
                    foreach ($arr as $val) {
                        if (!is_array($val)) {
                            return false;
                        }
                        if ($i == 0) {
                        } else {
                            $str = $str . $decollator3;
                        }
                        foreach ($val as $value) {
                            if (!is_array($value)) {
                                return false;
                            }
                            if ($i == 0) {
                                $str = implode($decollator1, $value);
                            } else {
                                $str .= $decollator2 . implode($decollator1, $value);
                            }
                            $i++;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        return $str;
    }
}
if (!function_exists('stringto_array')) {
    function stringto_array($str, $decollator1 = '', $decollator2 = '', $decollator3 = '')
    {
        $stop = '<inc>';
        if (!$decollator1) {
            $decollator1 = $stop;
        }
        if (!$decollator2) {
            $decollator2 = $stop;
        }
        if (!$decollator3) {
            $decollator3 = $stop;
        }
        if (is_string($str)) {
            $str1 = $decollator3 == $stop ? $str : trim($str, $decollator3);
            $arr1 = explode($decollator3, $str1);
            foreach ($arr1 as $key => $val) {
                $str2 = $decollator2 == $stop ? $val : trim($val, $decollator2);
                $arr2 = explode($decollator2, $str2);
                foreach ($arr2 as $value) {
                    $str3 = $decollator1 == $stop ? $value : trim($value, $decollator1);
                    if ($decollator3 == $stop && $decollator2 == $stop) {
                        $arr = explode($decollator1, $str3);
                    } else {
                        if ($decollator3 == $stop && $decollator2 != $stop) {
                            $arr[] = explode($decollator1, $str3);
                        } else {
                            $arr[$key][] = explode($decollator1, $str3);
                        }
                    }
                }
            }
        } else {
            return false;
        }
        return $arr;
    }
}
if (!function_exists('array_level')) {
    /**
     * 判断数组的维数
     * @param  array    $arr    要判断的数组
     * @param  array    $arr1   层数数组
     * @param  array    $level  当前层数
     * @return int              返回数组的维数
     */
    function array_level($arr, &$arr1 = array(), $level = 0)
    {
        if (is_array($arr)) {
            $level++;
            $arr1[] = $level;
            foreach ($arr as $val) {
                array_level($val, $arr1, $level);
            }
        } else {
            $arr1[] = 0;
        }
        return max($arr1);
    }
}
if (!function_exists('arr_sort')) {
    /**
     * 一维数组/二维数组排序
     * @param  array		$arr		要排序的数组
     * @param  string(int)	$sort_key	如果数组是二维数组则代表要排序的键，如果为一维数组 0代表按值排序 1代表按键排序
     * @param  string		$sort		SORT_ASC - 按照上升顺序排序    SORT_DESC - 按照下降顺序排序（默认升序）
     * @return array		$arr		返回排序后的数组，输入的数组不正确时返回false
     */
    function arr_sort($arr, $sort_key = 0, $sort = SORT_ASC)
    {
        if (array_level($arr) == 2) {
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $key_arr[] = $val[$sort_key];
                } else {
                    return false;
                }
            }
            array_multisort($key_arr, $sort, $arr);
            return $arr;
        } else {
            if (array_level($arr) == 1) {
                if ($sort_key == 0) {
                    if ($sort == SORT_ASC) {
                        asort($arr);
                    } else {
                        arsort($arr);
                    }
                } else {
                    if ($sort_key == 1) {
                        if ($sort == SORT_ASC) {
                            ksort($arr);
                        } else {
                            krsort($arr);
                        }
                    }
                }
                return $arr;
            } else {
                return false;
            }
        }
    }
}
if (!function_exists('jsonencode')) {
    /**
     * 数组转换成json
     * @param  array	$arr	要转换的数组
     * @return json				返回转换成的json
     */
    function jsonencode($arr)
    {
        $parts = array();
        $is_type = false;
        //false 关联数组         true 索引数组
        $keys = array_keys($arr);
        $length = count($arr) - 1;
        if ($keys[0] === 0 && $keys[$length] == $length) {
            //判断是索引数组还是关联数组
            $is_type = true;
            for ($i = 0; $i < count($keys); $i++) {
                if ($i != $keys[$i]) {
                    $is_type = false;
                    break;
                }
            }
        }
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                if ($is_type) {
                    $parts[] = jsonencode($val);
                } else {
                    $parts[] = '"' . $key . '":' . jsonencode($val);
                }
            } else {
                $str = '';
                if (!$is_type) {
                    $str = '"' . $key . '":';
                }
                if ($val === false) {
                    $str .= 'false';
                } else {
                    if ($val === true) {
                        $str .= 'true';
                    } else {
                        $str .= '"' . str_replace(array('\\', '/', '"'), array('\\\\', '\\/', '\\"'), $val) . '"';
                    }
                }
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        $json = str_replace(array("\r", "\n", "\t"), '', $json);
        if ($is_type) {
            return '[' . $json . ']';
        }
        return '{' . $json . '}';
    }
}
if (!function_exists('jsondecode')) {
    /**
     * json转换成数组
     * @param  json		$json	要转换的json（只能是json格式）
     * @return array	$arr	返回转换成的数组
     */
    function jsondecode($json)
    {
        if ($json) {
            $convert = false;
            $str = '$arr=';
            for ($i = 0; $i < strlen($json); $i++) {
                if (!$convert) {
                    if ($json[$i] == '{' || $json[$i] == '[') {
                        $str .= ' array(';
                    } else {
                        if ($json[$i] == '}' || $json[$i] == ']') {
                            $str .= ')';
                        } else {
                            if ($json[$i] == ':') {
                                $str .= '=>';
                            } else {
                                $str .= $json[$i];
                            }
                        }
                    }
                } else {
                    $str .= $json[$i];
                }
                if ($json[$i] == '"' && $json[$i - 1] != "\\") {
                    $convert = !$convert;
                }
            }
            $str = str_replace(array('\\\\', '\\/'), array('\\', '/'), $str);
            @eval($str . ';');
        } else {
            $arr = array();
        }
        return $arr;
    }
}
if (!function_exists('jsoncallback')) {
    /*
     * 把数组转成JSON，用于ajax返回，可以用于普通json请求返回，也可以用于跨域的ajax的jsonp格式的数据请求返回。
     * @param array  $back      输出字符串或数组
     * @param string $callback  ajax的回调函数的名称
     */
    function jsoncallback($back, $callback = 'callback')
    {
        $callback = input("param.{$callback}/s");
        if ($callback) {
			return jsonp(json_decode(jsonencode($back)));
        } else {
			return json(json_decode(jsonencode($back)));
        }
    }
}

if (!function_exists('group_same_key')) 
{ 
    /**
     * 将二维数组以元素的某个值作为键，并归类数组
     *
     * array( array('name'=>'aa','type'=>'pay'), array('name'=>'cc','type'=>'pay') )
     * array('pay'=>array( array('name'=>'aa','type'=>'pay') , array('name'=>'cc','type'=>'pay') ))
     * @param $arr 数组
     * @param $key 分组值的key
     * @return array
     */
    function group_same_key($arr,$key){
        $new_arr = array();
        foreach($arr as $k=>$v ){
            $new_arr[$v[$key]][] = $v;
        }
        return $new_arr;
    }
}

if (!function_exists('convert_arr_key')) 
{
    /**
     * 将数据库中查出的列表以指定的 id 作为数组的键名 
     *
     * @param array $arr 数组
     * @param string $key_name 数组键名
     * @return array
     */
    function convert_arr_key($arr, $key_name)
    {
		is_object($arr) && $arr = $arr->toArray();
        if (function_exists('array_column')) {
            return array_column($arr, null, $key_name);
        }

        $arr2 = array();
        foreach($arr as $key => $val){
            $arr2[$val[$key_name]] = $val;        
        }
        return $arr2;
    }
}

if (!function_exists('get_arr_column')) 
{         
    /**
     * 获取数组中的某一列
     *
     * @param array $arr 数组
     * @param string $key_name  列名
     * @return array  返回那一列的数组
     */
    function get_arr_column($arr, $key_name)
    {
		is_object($arr) && $arr = $arr->toArray();
        if (function_exists('array_column')) {
            return array_column($arr, $key_name);
        }

        $arr2 = array();
        foreach($arr as $key => $val){
            $arr2[] = $val[$key_name];        
        }
        return $arr2;
    }
}

if (!function_exists('sort_list_tier')) {
    /**
     * 分级排序
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param string $html
     * @param int $level
     * @param bool $clear
     * @return array
     */
    function sort_list_tier($data, $pid = 0, $field = 'pid', $pk = 'id', $html = '|-----', $level = 1, $clear = true)
    {
        static $list = [];
        if ($clear) $list = [];
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                $res['html'] = str_repeat($html, $level);
                $list[] = $res;
                unset($data[$k]);
                sort_list_tier($data, $res[$pk], $field, $pk, $html, $level + 1, false);
            }
        }
        return $list;
    }
}