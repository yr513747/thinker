<?php
// 异常错误报错级别,
//error_reporting(E_ERROR | E_PARSE );
// 应用公共文件
function recurse_copy($src, $des)
{
	if (!is_dir($src)) {
        return false;
    }
	$src = rtrim($src, '/' . \DIRECTORY_SEPARATOR);
	$des = rtrim($des, '/' . \DIRECTORY_SEPARATOR);
    $hander = opendir($src);
    @mkdir($des);
    while (false !== ($file = readdir($hander))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . \DIRECTORY_SEPARATOR . $file)) {
                recurse_copy($src . \DIRECTORY_SEPARATOR . $file, $des . \DIRECTORY_SEPARATOR . $file);
            } else {
                if (!is_dir(dirname($des . \DIRECTORY_SEPARATOR . $file))) {
                    mkdir(dirname($des . \DIRECTORY_SEPARATOR . $file), 0755, true);
                }
                copy($src . \DIRECTORY_SEPARATOR . $file, $des . \DIRECTORY_SEPARATOR . $file);
            }
        }
    }
    closedir($hander);
}
