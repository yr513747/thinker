<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\template\driver;

use think\Exception;

class File
{
    protected $cacheFile;

    /**
     * 写入编译缓存
     * @access public
     * @param  string $cacheFile 缓存的文件名
     * @param  string $content 缓存的内容
     * @return void
     */
    public function write(string $cacheFile, string $content): void
    {
        // 检测模板目录
        $dir = dirname($cacheFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 生成模板缓存文件
        if (false === $this->putFile($cacheFile, $content)) {
            throw new Exception('cache write error:' . $cacheFile, 11602);
        }
    }

    /**
     * 读取编译编译
     * @access public
     * @param  string  $cacheFile 缓存的文件名
     * @param  array   $vars 变量数组
     * @return void
     */
    public function read(string $cacheFile, array $vars = []): void
    {
        $this->cacheFile = $cacheFile;

        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成为独立变量
            extract($vars, EXTR_OVERWRITE);
        }

        //载入模版缓存文件
        include $this->cacheFile;
    }

    /**
     * 检查编译缓存是否有效
     * @access public
     * @param  string  $cacheFile 缓存的文件名
     * @param  int     $cacheTime 缓存时间
     * @return bool
     */
    public function check(string $cacheFile, int $cacheTime): bool
    {
        // 缓存文件不存在, 直接返回false
        if (!file_exists($cacheFile)) {
            return false;
        }

        if (0 != $cacheTime && getTime() > filemtime($cacheFile) + $cacheTime) {
			// 自动清除过期缓存文件by仰融
			try {
				unlink($cacheFile);
			} catch (\Exception $e){
				//删除失败
			}
            // 缓存是否在有效期
            return false;
        }

        return true;
    }
	
	/**
     * 读取文件的内容（加锁）
     * @access public
     * @param  string  $path 文件路径
     * @return string
     */
    public function getFile(string $path)
    {
        $contents = '';
		
        if (is_file($path)) {
            $handle = fopen($path, 'rb');
            if ($handle) {
                try {
                    if (flock($handle, LOCK_SH)) {
                        clearstatcache(true, $path);
                        $contents = fread($handle, filesize($path) ?: 1);
                        flock($handle, LOCK_UN);
                    }
                } finally {
                    fclose($handle);
                }
            }
        }
       
        return $contents;
    }
	
	/**
     * 将给定的内容保存到文件中（加锁）
     * @access public
     * @param  string $filename  文件路径
     * @param  string $data      要写入的数据 
     * @return bool
     */
    public function putFile(string $filename, string $data = null)
    {
        $result = false;
        $handle = fopen($filename, 'wb');
        if ($handle) {
            try {
                if (flock($handle, LOCK_EX)) {
                    clearstatcache(true, $filename);
                    $result = $this->fwriteStream($handle, $data);
                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }
        return $result === 0 ? true : (bool) $result;
    }
	
	/**
     * 写入文件（可安全用于二进制文件）
     * @access public
     * @param  resource $resource
     * @param  string   $data  
     * @return int
     */
    public function fwriteStream($resource, string $data = null)
    {
        for ($written = 0; $written < strlen($data); $written += $fwrite) {
            $fwrite = fwrite($resource, substr($data, $written));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }
}