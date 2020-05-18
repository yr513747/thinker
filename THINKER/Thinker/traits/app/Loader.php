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
declare (strict_types=1);
namespace Thinker\traits\app;

trait Loader
{
    // +------------------------------------------------------
    // | 加载应用文件
    // | @param string $filesPath 文件路径
    // | @return bool
    // +------------------------------------------------------
    public function loadfiles($filesPath)
    {
        global $_M;
        if (isset($_M['__loadfiles'][$filesPath])) {
            return TRUE;
        }
        $files = array();
        if (substr($filesPath, -1) != DS) {
            $filesPath .= DS;
        }
        if (is_dir($filesPath)) {
            $files = array_merge($files, glob($filesPath . '*' . '.php'));
            $_M['__loadfiles'][$filesPath] = TRUE;
        }
        if (!isset($_M['__loadfiles'][$filesPath])) {
            exit("Unable to load the requested path: {$filesPath}");
        }
        $filesload = FALSE;
        foreach ($files as $file) {
            if (empty($_M['__loadfiles'][$file])) {
                if ($this->runningInWin() && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME)) {
                    return FALSE;
                }
                include_once $file;
                !empty($_M['__loadfiles']) && ($filesload = $_M['__loadfiles'][$file] = TRUE);
            }
        }
        return $filesload;
    }
    // +------------------------------------------------------
    // |  载入小助手,系统默认载入小助手
    // |  在/include/inc/helper.inc.php中进行默认小助手初始化的设置
    // |  使用示例:
    // |      在开发中,首先需要创建一个小助手函数,目录在\include\helpers中
    // |  例如,我们创建一个示例为test.helper.php,文件基本内容如下:
    // |  <code>
    // |  if ( ! function_exists('HelloTdd'))
    // |  {
    // |      function HelloTdd()
    // |      {
    // |          echo "Hello! Tdd...";
    // |      }
    // |  }
    // |  </code>
    // |  则我们在开发中使用这个小助手的时候直接使用函数Loader::helper('test');初始化它
    // |  然后在文件中就可以直接使用:HelloTdd();来进行调用.
    // |
    // | @access    public
    // | @param     mix   $helpers  小助手名称,可以是数组,可以是单个字符串
    // | @return    bool
    // +------------------------------------------------------
    public function helper($helpers)
    {
        global $_M;
        //如果是数组,则进行递归操作
        if (is_array($helpers)) {
            foreach ($helpers as $thinker) {
                $this->helper($thinker);
            }
            return FALSE;
        }
        if (isset($_M['__helpers'][$helpers])) {
            return TRUE;
        }
        $filesload = FALSE;
        if (is_file($this->getHelpersPath() . "{$helpers}.helper.php")) {
            include_once $this->getHelpersPath() . "{$helpers}.helper.php";
            $filesload = $_M['__helpers'][$helpers] = TRUE;
        }
        // 无法载入小助手
        if (!isset($_M['__helpers'][$helpers])) {
            exit("Unable to load the requested file: helpers/{$helpers}.helper.php");
        }
        return $filesload;
    }
	// +------------------------------------------------------
    // |  指定目录载入小助手
    // |  
    // |  使用示例:
    // |      在开发中,首先需要创建一个小助手函数,指定目录中
    // |  例如,我们创建一个示例为test.helper.php,文件基本内容如下:
    // |  <code>
    // |  if ( ! function_exists('HelloTdd'))
    // |  {
    // |      function HelloTdd()
    // |      {
    // |          echo "Hello! Tdd...";
    // |      }
    // |  }
    // |  </code>
    // |  则我们在开发中使用这个小助手的时候直接使用函数Loader::helperbypath('test',指定目录);初始化它
    // |  然后在文件中就可以直接使用:HelloTdd();来进行调用.
    // |
    // | @access    public
    // | @param     mix   $helpers  小助手名称,可以是数组,可以是单个字符串
	// | @param     string $filesPath 文件路径
    // | @return    bool
    // +------------------------------------------------------
    public function helperbypath($helpers, $filesPath)
    {
        global $_M;
        //如果是数组,则进行递归操作
        if (is_array($helpers)) {
            foreach ($helpers as $thinker) {
                $this->helperbypath($thinker);
            }
            return FALSE;
        }
        if (isset($_M['__helperbypath'][$helpers])) {
            return TRUE;
        }
        $filesload = FALSE;
		if (substr($filesPath, -1) != DS) {
            $filesPath .= DS;
        }
        if (is_file($filesPath . "{$helpers}.helper.php")) {
            include_once $filesPath . "{$helpers}.helper.php";
            $filesload = $_M['__helperbypath'][$helpers] = TRUE;
        }
        // 无法载入小助手
        if (!isset($_M['__helperbypath'][$helpers])) {
            exit("Unable to load the requested file: {$filesPath}{$helpers}.helper.php");
        }
        return $filesload;
    }
}
// +-------------------------------------------------------------------------
// | 作用范围隔离
// +-------------------------------------------------------------------------
if (!function_exists('__include_file')) {
    /**
     * include
     * @param  string $file 文件路径
     * @return mixed
     */
    function __include_file($file)
    {
        global $_M;
        if (isset($_M['__include_file'][$file])) {
            return FALSE;
        }
        if (is_file($file)) {
            include $file;
            $_M['__include_file'][$file] = TRUE;
            return TRUE;
        }
        if (!isset($_M['__include_file'][$file])) {
            exit("Unable to load the requested file: {$file}");
        }
        return FALSE;
    }
}
if (!function_exists('__include_once_file')) {
    /**
     * include
     * @param  string $file 文件路径
     * @return mixed
     */
    function __include_once_file($file)
    {
        global $_M;
        if (isset($_M['__include_once_file'][$file])) {
            return FALSE;
        }
        if (is_file($file)) {
            include_once $file;
            $_M['__include_once_file'][$file] = TRUE;
            return TRUE;
        }
        if (!isset($_M['__include_once_file'][$file])) {
            exit("Unable to load the requested file: {$file}");
        }
        return FALSE;
    }
}
if (!function_exists('__require_file')) {
    /**
     * require
     * @param  string $file 文件路径
     * @return mixed
     */
    function __require_file($file)
    {
        global $_M;
        if (isset($_M['__require_file'][$file])) {
            return FALSE;
        }
        if (is_file($file)) {
            require $file;
            $_M['__require_file'][$file] = TRUE;
            return TRUE;
        }
        if (!isset($_M['__require_file'][$file])) {
            exit("Unable to load the requested file: {$file}");
        }
        return FALSE;
    }
}
if (!function_exists('__require_once_file')) {
    /**
     * require
     * @param  string $file 文件路径
     * @return mixed
     */
    function __require_once_file($file)
    {
        global $_M;
        if (isset($_M['__require_once_file'][$file])) {
            return FALSE;
        }
        if (is_file($file)) {
            require_once $file;
            $_M['__require_once_file'][$file] = TRUE;
            return TRUE;
        }
        if (!isset($_M['__require_once_file'][$file])) {
            exit("Unable to load the requested file: {$file}");
        }
        return FALSE;
    }
}