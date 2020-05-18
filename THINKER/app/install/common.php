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
// [ 公共函数文件 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
if (!function_exists('testwrite')) {
    function testwrite($d)
    {
        $tfile = "_test.txt";
        $fp = @fopen($d . "/" . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($d . "/" . $tfile);
        if ($rs) {
            return true;
        }
        return false;
    }
}
if (!function_exists('sql_split')) {
    function sql_split($sql, $tablepre)
    {
        /*从安装目录的数据库文件，提取数据库文件里的表前缀*/
        $prefix = 'thinker_';
        preg_match_all('/CREATE\\s*TABLE\\s*`([^`]+)\\s*/', $sql, $matches2);
        $datatableList = !empty($matches2[1]) ? $matches2[1] : [];
        // 数据库所有表名
        if (!empty($datatableList)) {
            foreach ($datatableList as $key => $val) {
                if (preg_match('/_admin$/i', $val)) {
                    $prefix = preg_replace('/_admin$/i', '', $val) . '_';
                    break;
                }
            }
        }
        /*--end*/
        if ($tablepre != $prefix) {
            $sql = str_replace('`' . $prefix, '`' . $tablepre, $sql);
        }
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-') {
                    $ret[$num] .= $query;
                }
            }
            $num++;
        }
        return $ret;
    }
}
if (!function_exists('dir_create')) {
    function dir_create($path, $mode = 0777)
    {
        if (is_dir($path)) {
            return TRUE;
        }
        $ftp_enable = 0;
        $path = dir_path($path);
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for ($i = 0; $i < $max; $i++) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir)) {
                continue;
            }
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
}
if (!function_exists('dir_path')) {
    function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }
        return $path;
    }
}

if (!function_exists('delFile')) {
    // 递归删除文件夹
    function delFile($dir, $file_type = '')
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            //打开目录 //列出目录中的所有文件并去掉 . 和 ..
            foreach ($files as $filename) {
                if ($filename != '.' && $filename != '..') {
                    if (!is_dir($dir . '/' . $filename)) {
                        if (empty($file_type)) {
                            unlink($dir . '/' . $filename);
                        } else {
                            if (is_array($file_type)) {
                                //正则匹配指定文件
                                if (preg_match($file_type[0], $filename)) {
                                    unlink($dir . '/' . $filename);
                                }
                            } else {
                                //指定包含某些字符串的文件
                                if (false != stristr($filename, $file_type)) {
                                    unlink($dir . '/' . $filename);
                                }
                            }
                        }
                    } else {
                        delFile($dir . '/' . $filename);
                        rmdir($dir . '/' . $filename);
                    }
                }
            }
        } else {
            if (is_dir($dir)) {
                unlink($dir);
            }
        }
    }
}
if (!function_exists('arrayRecursive')) {
    /**
     *
     *  使用特定function对数组中所有元素做处理
     *  @param  string  &$array     要处理的字符串
     *  @param  string  $function   要执行的函数
     *  @return boolean $apply_to_keys_also     是否也应用到key上
     *  @access public
     *
     */
    function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
}
if (!function_exists('JSONstring')) {
    /**
     *
     *  将数组转换为JSON字符串（兼容中文）
     *  @param  array   $array      要转换的数组
     *  @return string      转换得到的json字符串
     *  @access public
     *
     */
    function JSONstring($array)
    {
        arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }
}