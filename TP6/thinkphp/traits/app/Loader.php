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
namespace think\traits\app;

trait Loader
{
    // +------------------------------------------------------
    // | 加载应用文件
    // | @param string $filesPath 文件路径
    // | @return bool
    // +------------------------------------------------------
    public function loadfiles($filesPath)
    {
        static $_importFiles = array();
        if (substr($filesPath, -1) != DIRECTORY_SEPARATOR) {
            $filesPath .= DIRECTORY_SEPARATOR;
        }
        $filesload = false;
        if (!isset($_importFiles['__loadfiles'][$filesPath]) && is_dir($filesPath)) {
            $files = glob($filesPath . '*' . '.php');
            foreach ($files as $file) {
                if (empty($_importFiles['__loadfiles'][$file])) {
                    if ($this->runningInWin() && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME)) {
                        return false;
                    } else {
                        $filesload = __include_file($file);
                    }
                }
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
        if (is_array($helpers)) {
            foreach ($helpers as $thinker) {
                $this->helper($thinker);
            }
            return false;
        }
        return __include_file($this->getHelpersPath() . "{$helpers}.helper.php");
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
        if (is_array($helpers)) {
            foreach ($helpers as $thinker) {
                $this->helperbypath($thinker);
            }
            return false;
        }
        if (substr($filesPath, -1) != DIRECTORY_SEPARATOR) {
            $filesPath .= DIRECTORY_SEPARATOR;
        }
        return __include_file($filesPath . "{$helpers}.helper.php");
    }

}
// 作用范围隔离
if (!function_exists('__include_file')) {
    /**
     * include
     * @param  string $filename 文件路径
     * @return mixed
     */
    function __include_file(string $filename)
    {
        static $_importFiles = array();
        if (!isset($_importFiles[$filename]) && is_file($filename)) {
            include $filename;
            $_importFiles[$filename] = true;
        }
        if (!isset($_importFiles[$filename])) {
            exit("Unable to load the requested file: {$filename}");
        }
        return $_importFiles[$filename];
    }
}
if (!function_exists('__require_file')) {
    /**
     * require
     * @param  string $filename 文件路径
     * @return mixed
     */
    function __require_file(string $filename)
    {
        static $_importFiles = array();
        if (!isset($_importFiles[$filename]) && is_file($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        }
        if (!isset($_importFiles[$filename])) {
            exit("Unable to load the requested file: {$filename}");
        }
        return $_importFiles[$filename];
    }
}