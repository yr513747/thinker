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
namespace think;

use think\facade\File;
class Util extends File
{
    public static function buildContent(array $arrays)
    {
        array_walk_recursive($arrays, [static::class, 'buildClosure']);
        return $arrays;
    }
    public static function buildClosure(&$value)
    {
        if ($value instanceof \Closure) {
            $reflection = new \ReflectionFunction($value);
            $startLine = $reflection->getStartLine();
            $endLine = $reflection->getEndLine();
            $file = $reflection->getFileName();
            $item = file($file);
            $content = '';
            for ($i = $startLine - 1; $i <= $endLine - 1; $i++) {
                $content .= $item[$i];
            }
            $start = strpos($content, 'function');
            $end = strrpos($content, '}');
            $value = '[__start__' . substr($content, $start, $end - $start + 1) . '__end__]';
        }
    }
    /**
     * 建立文件夹
     *
     * @param  string  $folder
     * @param  integer $mode
     * @param  bool    $recursive  允许递归创建由 folder 所指定的多级嵌套目录
     * @return bool
     */
    public static function createFolder() 
    {
		
        
    }
    /**
     * 建立文件
     *
     * @param  string $file 
     * @param  bool   $overwrite 该参数控制是否覆盖原文件
     * @return bool
     */
    public static function createFile(string $file, bool $overwrite = false) : bool
    {
        
    }
    /**
     * 移动文件夹
     *
     * @param  string $oldfolder
     * @param  string $newfolder
     * @param  bool   $overwrite 该参数控制是否覆盖原文件
     * @return bool
     */
    public static function moveFolder(string $oldfolder, string $newfolder, bool $overwrite = false) : bool
    {
        
    }
    /**
     * 移动文件
     *
     * @param  string $oldfile
     * @param  string $newfile
     * @param  bool   $overwrite 该参数控制是否覆盖原文件
     * @return bool
     */
    public static function moveFile(string $oldfile, string $newfile, bool $overwrite = false) : bool
    {
        
    }
    /**
     * 删除文件夹
     *
     * @param  string $folder
     * @return bool
     */
    public static function unlinkFolder(string $folder) : bool
    {
       
    }
    /**
     * 删除文件
     *
     * @param  string $file
     * @return bool
     */
    public static function unlinkFile(string $file) : bool
    {
        try {
            return is_file($file) && unlink($file);
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * 复制文件夹
     *
     * @param  string $oldfolder
     * @param  string $newfolder
     * @param  bool   $overwrite 该参数控制是否覆盖原文件
     * @return bool
     */
    public static function copyFolder(string $oldfolder, string $newfolder, bool $overwrite = false) : bool
    {
        
    }
    /**
     * 复制文件
     *
     * @param  string $oldfile
     * @param  string $newfile
     * @param  bool   $overwrite 该参数控制是否覆盖原文件
     * @return bool
     */
    public static function copyFile(string $oldfile, string $newfile, bool $overwrite = false) : bool
    {
        if (!is_file($oldfile)) {
            return false;
        }
        if (is_file($newfile) && $overwrite == false) {
            return false;
        } elseif (is_file($newfile) && $overwrite == true) {
            static::unlinkFile($newfile);
        }
        static::createFolder(dirname($newfile));
        return copy($oldfile, $newfile);
    }
    /**
     * 判断目录是否为空
     *
     * @param  string $folder
     * @return bool
     */
    public static function emptyFolder(string $folder) : bool
    {
        if (!is_dir($folder)) {
            return true;
        }
        $res = array_diff(scandir($folder), array('..', '.'));
        return empty($res);
    }
    //列出文件和目录
    public static function getFileAndFolderList(string $folder) : array
    {
        $files = array();
        try {
            $items = new \DirectoryIterator($folder);
        } catch (\Exception $e) {
            throw new \Exception($folder . ' is not readable');
        }
        foreach ($items as $item) {
            if ($item->isDot()) {
                continue;
            }
            $files[] = $item->getFileName();
        }
        return $files;
    }
    //仅仅列出目录
    public static function getFolderList(string $folder) : array
    {
        $files = array();
        try {
            $items = new \DirectoryIterator($folder);
        } catch (\Exception $e) {
            throw new \Exception($folder . ' is not readable');
        }
        foreach ($items as $item) {
            if ($item->isLink() || $item->isFile()) {
                $files[] = $item->getFileName();
            }
        }
        return $files;
    }
    //仅仅列出文件
    public static function getFileList(string $folder) : array
    {
        $files = array();
        try {
            $items = new \DirectoryIterator($folder);
        } catch (\Exception $e) {
            throw new \Exception($folder . ' is not readable');
        }
        foreach ($items as $item) {
            if (!$item->isDot() && $item->isDir()) {
                $files[] = $item->getFileName();
            }
        }
        return $files;
    }
    public static function readFile() : string
    {
    }
    public static function writeFile() : bool
    {
    }
}