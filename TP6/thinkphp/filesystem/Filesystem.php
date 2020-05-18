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
namespace think\filesystem;

use think\App;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem as FilesystemComponents;
use think\exception\FileSystemException;
class Filesystem
{
    const DEFAULT_ERROR_MSG = '操作失败,请稍候再试!';
    /**
     * 错误信息
     * @var string
     */
    private $errorMsg;
    private static $lastError;
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * Finder组件实例
     * @var Finder
     */
    protected $Finder;
    /**
     * Filesystem组件实例
     * @var Filesystem
     */
    protected $Filesystem;
    /**
     * 文件hash规则
     * @var array
     */
    protected $hash = [];
    /**
     * \ZipArchive实例
     * @var \ZipArchive
     */
    protected $zip;
    /**
     * 压缩包生成的root地址
     * @var string
     */
    protected $root;
    /**
     * 压缩文件时要忽略的文件列表
     * @var array
     */
    protected $ignored_names;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @param  \ZipArchive $zip  压缩文件操作对象
     * @param  Finder $finder  Finder组件对象
     * @param  FilesystemComponents $filesystem  Filesystem组件对象
     */
    public function __construct(App $app, \ZipArchive $zip, Finder $finder, FilesystemComponents $filesystem)
    {
        $this->app = $app;
        $this->zip = $zip;
        $this->Finder = $finder;
        $this->Filesystem = $filesystem;
    }
    /**
     * 设置错误信息
     *
     * @param  string $errorMsg
     * @param  string $method
     * @param  int    $line
     * @return bool
     *
     * @throws FileSystemException
     */
    protected function setErrorInfo(string $errorMsg = self::DEFAULT_ERROR_MSG, string $method = null, int $line = null, string $path = null)
    {
        $errorMsg = sprintf('Method——>(%s)%sLine——>(%s)%sErrorInfo——>(%s)', $method = $method ?? __METHOD__, PHP_EOL, $line = $line ?? __LINE__, PHP_EOL, $errorMsg);
        $this->errorMsg = $errorMsg;
        if ($this->app->isDebug()) {
            throw new FileSystemException($errorMsg, 0, null, $path);
        }
        return false;
    }
    /**
     * 获取错误信息
     *
     * @param  string $defaultMsg
     * @return string
     */
    public function getErrorInfo(string $defaultMsg = self::DEFAULT_ERROR_MSG)
    {
        return !empty($this->errorMsg) ? $this->errorMsg : $defaultMsg;
    }
    /**
     * 解压zip文件到指定文件夹
     *
     * @param  string $zipfile   压缩文件路径
     * @param  string $path      压缩包解压到的目标路径
     * @param  bool   $unzipfile 解压成功后是否删除压缩文件
     * @return bool 
     *
     * @throws FileSystemException
     */
    public function unzip(string $zipfile, string $path = null, bool $unzipfile = false)
    {
        if ($this->zip->open($zipfile) === true) {
            if (!\is_dir((string) $path)) {
                // 无法创建目录，解压到当前文件夹
                if (@\mkdir((string) $path, 0755, true) === false) {
                    $path = \dirname($zipfile) . \DIRECTORY_SEPARATOR . \pathinfo($zipfile, \PATHINFO_FILENAME);
                }
            }
            $result = $this->zip->extractTo($path);
            $this->zip->close();
            if ($unzipfile === false) {
                return $result;
            }
            if ($result) {
                $res = $this->unlink($zipfile);
                if ($res) {
                    if ($this->emptyPath(\dirname($zipfile))) {
                        $res = $this->delPath(\dirname($zipfile));
                    }
                }
                if (!$res) {
                    return $this->setErrorInfo(sprintf('Resource deletion failed, please delete it manually:%s', $zipfile), __METHOD__, __LINE__, $zipfile);
                }
            }
            return $result;
        }
        return false;
    }
    /**
     * 创建压缩文件
     * 
     * @param  string $zipfile  将要生成的压缩文件路径
     * @param  strng  $folder   将要被压缩的文件夹路径
     * @param  array  $ignored  要忽略的文件列表
     * @param  bool   $unfolder 压缩成功后是否删除源文件
     * @return bool 
     *
     * @throws FileSystemException
     */
    public function zip(string $zipfile, string $folder, array $ignored = [], bool $unfolder = false)
    {
        if (!$this->exists($folder)) {
            return $this->setErrorInfo(sprintf('could not open file:%s', $folder), __METHOD__, __LINE__, $folder);
        }
        $this->ensureDirectoryExists(\dirname($zipfile), 0755, true);
        $this->ignored_names = $ignored;
        if ($this->zip->open($zipfile, \ZipArchive::CREATE) !== true) {
            return $this->setErrorInfo(sprintf('could not create file:%s', $zipfile), __METHOD__, __LINE__, $zipfile);
        }
        $folder = substr($folder, -1) == \DIRECTORY_SEPARATOR ? substr($folder, 0, strlen($folder) - 1) : $folder;
        if (strstr($folder, \DIRECTORY_SEPARATOR)) {
            $this->root = substr($folder, 0, strrpos($folder, \DIRECTORY_SEPARATOR) + 1);
            $folder = substr($folder, strrpos($folder, \DIRECTORY_SEPARATOR) + 1);
        }
        $this->createZip($folder);
        if ($unfolder === false) {
            return $this->zip->close();
        }
        $result = $this->zip->close();
        if ($result) {
            $res = $this->delPath($folder);
            if ($res) {
                if ($this->emptyPath(\dirname($folder))) {
                    $res = $this->delPath(\dirname($folder));
                }
            }
            if (!$res) {
                return $this->setErrorInfo(sprintf('Resource deletion failed, please delete it manually:%s', $folder), __METHOD__, __LINE__, $folder);
            }
        }
        return $result;
    }
    /**
     * 递归添加文件到压缩包
     * 
     * @param  string  $folder 添加到压缩包的文件夹路径
     * @param  string  $parent 添加到压缩包的文件夹上级路径
     * @return void
     */
    private function createZip(string $folder, string $parent = null)
    {
        $full_path = $this->root . $parent . $folder;
        $zip_path = $parent . $folder;
        $this->zip->addEmptyDir($zip_path);
        $dir = new \DirectoryIterator($full_path);
        foreach ($dir as $file) {
            if (!$file->isDot()) {
                $filename = $file->getFilename();
                if (!in_array($filename, $this->ignored_names)) {
                    if ($file->isDir()) {
                        $this->createZip($filename, $zip_path . \DIRECTORY_SEPARATOR);
                    } else {
                        $this->zip->addFile($full_path . \DIRECTORY_SEPARATOR . $filename, $zip_path . \DIRECTORY_SEPARATOR . $filename);
                    }
                }
            }
        }
    }
    /**
     * 读取压缩包文件与目录列表
     * 
     * @param  string $zipfile 压缩包文件
     * @return array  
     */
    public function readZip(string $zipfile)
    {
        $pathsMap = [];
        $filesMap = [];
        if ($this->zip->open($zipfile) == true) {
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $numfiles = $this->zip->getNameIndex($i);
                if (\preg_match('/\\/$/i', $numfiles)) {
                    $pathsMap[] = $this->replaceSlash($numfiles);
                } else {
                    $filesMap[] = $this->replaceSlash($numfiles);
                }
            }
            $this->zip->close();
        }
        return array('filesMap' => $filesMap, 'pathsMap' => $pathsMap);
    }
    /**
     * 检查是否存在一个或多个文件或目录
     *
     * @param  string|iterable $paths 要检查的文件名、文件数组或可遍历实例
     * @return bool 
     */
    public function exists($paths)
    {
        $maxPathLength = PHP_MAXPATHLEN - 2;
        if (is_string($paths)) {
            if (\strlen($paths) > $maxPathLength) {
                return $this->setErrorInfo(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength), __METHOD__, __LINE__, $paths);
            }
            return file_exists($paths);
        }
        foreach ($this->toIterable($paths) as $file) {
			$file = (string)$file;
            if (\strlen($file) > $maxPathLength) {
                return $this->setErrorInfo(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength), __METHOD__, __LINE__, $file);
            }
            if (!file_exists($file)) {
                return false;
            }
        }
        return true;
    }
    /**
     * 检查是否缺少文件或目录
     *
     * @param  string|iterable $paths 要检查的文件名、文件数组或可遍历实例
     * @return bool
     */
    public function missing($paths)
    {
        return !$this->exists($paths);
    }
    /**
     * 判断目录是否为空
     *
     * @param  @param string|array $dirs 目录路径或目录数组(字符串传入多个目录时用逗号分隔)
     * @return bool
     *
     * @throws FileSystemException
     */
    public function emptyPath($dirs)
    {
        is_string($dirs) && ($dirs = explode(',', $dirs));
        $resolvedDirs = [];
        foreach ((array) $dirs as $dir) {
            if (is_dir($dir)) {
                $resolvedDirs[] = $this->normalizeDir($dir);
            } elseif ($glob = glob($dir, (\defined('GLOB_BRACE') ? GLOB_BRACE : 0) | GLOB_ONLYDIR | GLOB_NOSORT)) {
                sort($glob);
                $resolvedDirs = array_merge($resolvedDirs, array_map([$this, 'normalizeDir'], $glob));
            } else {
                return $this->setErrorInfo(sprintf('The "%s" directory does not exist.', $this->filterSpaces($dir)), __METHOD__, __LINE__, $this->filterSpaces($dir));
            }
        }
        if (0 === \count($resolvedDirs)) {
            return true;
        }
        if (1 === \count($resolvedDirs)) {
            $iterator = new \FilesystemIterator($resolvedDirs[0]);
            return !$iterator->valid();
        }
        $riterator = [];
        foreach ($resolvedDirs as $dir) {
            $riterator[] = new \FilesystemIterator($dir);
        }
        $error = 0;
        foreach ($riterator as $result) {
            if ($result->valid()) {
                $error++;
            }
        }
        return $error === 0;
    }
    public function filterSpaces($str)
    {
        return str_replace(" ", "%20", html_entity_decode((string) $str));
    }
    /**
     * 读取文件的内容
     *
     * @param  string  $path 文件路径
     * @param  bool  $lock 是否加锁
     * @return string|bool
     *
     * @throws FileSystemException
     */
    public function get(string $path, $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }
        return $this->setErrorInfo(sprintf('File does not exist at path %s', $path), __METHOD__, __LINE__, $path);
    }
    /**
     * 读取文件的内容（加锁）
     *
     * @param  string  $path 文件路径
     * @return string
     */
    public function sharedGet(string $path)
    {
        $contents = '';
        if (!$this->isFile($path)) {
            return $contents;
        }
        $handle = fopen($path, 'rb');
        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);
                    $contents = fread($handle, $this->size($path) ?: 1);
                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }
        return $contents;
    }
    /**
     * 读取文件内容(兼容函数)
     *
     * @param  string  $path 文件路径
     * @return string
     */
    public function getFile(string $path)
    {
        return $this->sharedGet($path);
    }
    /**
     * 引入文件（require）
     *
     * @param  string  $path 文件路径
     * @return mixed
     *
     * @throws FileSystemException
     */
    public function getRequire(string $path)
    {
        if ($this->isFile($path)) {
            return require $path;
        }
        return $this->setErrorInfo(sprintf('File does not exist at path %s', $path), __METHOD__, __LINE__, $path);
    }
    /**
     * 引入文件（require_once）
     *
     * @param  string  $file 文件路径
     * @return mixed
     */
    public function requireOnce(string $file)
    {
        require_once $file;
    }
    /**
     * 获取文件的哈希散列值
     * 
     * @param  string  $path
     * @param  string  $type
     * @return string
     */
    public function hash(string $path, string $type = 'sha1')
    {
        if (!isset($this->hash[$type])) {
            $this->hash[$type] = \hash_file($type, $path);
        }
        return $this->hash[$type];
    }
    /**
     * 获取文件的MD5值
     * 
     * @param  string  $path
     * @return string
     */
    public function md5(string $path)
    {
        return $this->hash($path, 'md5');
    }
    /**
     * 获取文件的SHA1值
     * 
     * @param  string  $path
     * @return string
     */
    public function sha1(string $path)
    {
        return $this->hash($path, 'sha1');
    }
    /**
     * 将给定的内容保存到文件中
     *
     * @param  string  $path  目标文件路径
     * @param  string|resource  $contents  要写入文件的数据
     * @param  bool  $lock  是否加锁
     * @return bool
     *
     * @throws FileSystemException 
     */
    public function put(string $path, $contents, $lock = false)
    {
        if (\is_array($contents)) {
            return $this->setErrorInfo(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.'), __METHOD__, __LINE__);
        }
        $dir = \dirname($path);
        $this->ensureDirectoryExists($dir);
        if (!is_writable($dir)) {
            return $this->setErrorInfo(sprintf('Unable to write to the "%s" directory.', $dir), __METHOD__, __LINE__, $dir);
        }
        if (false === ($result = @file_put_contents($path, $contents, $lock ? LOCK_EX : 0))) {
            return $this->setErrorInfo(sprintf('Failed to write file "%s".', $filename), __METHOD__, __LINE__, $filename);
        }
        return $result === 0 ? true : (bool) $result;
    }
    /**
     * 将给定的内容保存到文件中（加锁）
     *
     * @param  string $filename  文件路径
     * @param  mixed  $data      要写入的数据 
     * @param  bool   $cover     是否覆盖原文件
     * @return bool
     */
    public function putFile(string $filename, $data = null, bool $cover = true)
    {
        $this->ensureDirectoryExists($this->dirname($filename));
        $mode = 'wb';
        if (is_file($filename) && $cover == false) {
            $mode = 'ab';
        } elseif (is_file($filename) && $cover == true) {
            $mode = 'wb';
        }
        if (is_array($data)) {
            $data = $this->arrayToString($data);
        }
        $result = false;
        $handle = fopen($filename, $mode);
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
     * 将给定的内容保存到文件中
     *  -它以原子方式执行此操作：首先写入一个临时文件，然后在完成后将其移动到新文件位置
     *  -这意味着用户将始终看到完整的旧文件或完整的新文件（但看不到部分写入的文件）
     *
     * @param  string $filename 目标文件路径
     * @param  string|resource $content 要写入文件的数据
     * @return bool
     *
     * @throws FileSystemException 
     */
    public function dumpFile(string $filename, $content)
    {
        if (\is_array($content)) {
            return $this->setErrorInfo(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.'), __METHOD__, __LINE__);
        }
        $dir = \dirname($filename);
        $this->ensureDirectoryExists($dir);
        if (!is_writable($dir)) {
            return $this->setErrorInfo(sprintf('Unable to write to the "%s" directory.', $dir), __METHOD__, __LINE__, $dir);
        }
        // 创建具有0600访问权限的临时文件
        // 当文件系统支持chmod时。
        $tmpFile = $this->tempnam($dir, basename($filename));
        if (false === ($result = @file_put_contents($tmpFile, $content))) {
            return $this->setErrorInfo(sprintf('Failed to write file "%s".', $filename), __METHOD__, __LINE__, $filename);
        }
        // 修复tempnam（）创建权限
        @chmod($tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());
        $this->rename($tmpFile, $filename, true);
        return $result === 0 ? true : (bool) $result;
    }
    /**
     * 写一个文件的内容，如果它已经存在，就自动替换它
     *
     * @param  string  $path  目标文件路径
     * @param  string|resource  $contents  要写入文件的数据
     * @return bool
     *
     * @throws FileSystemException 
     */
    public function replace(string $path, $content)
    {
        return $this->dumpFile($path, $content);
    }
    /**
     * 在文件前面写入数据
     *
     * @param  string  $path  目标文件路径
     * @param  string|resource  $data  要写入文件的数据
     * @return bool
     *
     * @throws FileSystemException
     */
    public function prependToFile(string $path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }
        return $this->put($path, $data);
    }
    /**
     * 在指定文件的末尾添加新内容，如果文件或其包含目录不存在，则此方法将在附加内容之前创建一个新文件
     *
     * @param  string $filename 目标文件路径
     * @param  string|resource $content 要附加的内容
     * @return bool
     *
     * @throws FileSystemException
     */
    public function appendToFile(string $filename, $content)
    {
        if (\is_array($content)) {
            return $this->setErrorInfo(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.'), __METHOD__, __LINE__);
        }
        $dir = \dirname($filename);
        $this->ensureDirectoryExists($dir);
        if (!is_writable($dir)) {
            return $this->setErrorInfo(sprintf('Unable to write to the "%s" directory.', $dir), __METHOD__, __LINE__, $dir);
        }
        if (false === ($result = @file_put_contents($filename, $content, FILE_APPEND))) {
            return $this->setErrorInfo(sprintf('Failed to write file "%s".', $filename), __METHOD__, __LINE__, $filename);
        }
        return $result === 0 ? true : (bool) $result;
    }
    /**
     * 更改文件的模式或权限
     *
     * @param  string|iterable $files     要更改模式的文件名、文件数组或可遍历实例
     * @param  int             $mode      新模式（八进制）
     * @param  int             $umask     模式掩码（八进制）
     * @param  bool            $recursive 是否递归更改
     * @return bool
     * @throws FileSystemException When the change fails
     */
    public function chmod($files, int $mode, int $umask = 00, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if (true !== @chmod($file, $mode & ~$umask)) {
                return $this->setErrorInfo(sprintf('Failed to chmod file "%s".', $file), __METHOD__, __LINE__, $file);
            }
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chmod(new \FilesystemIterator($file), $mode, $umask, true);
            }
        }
        return true;
    }
    /**
     * 设置文件的访问和修改时间
     *
     * @param  string|iterable $files 要设置的文件名、文件数组或可遍历实例
     * @param  int|null        $time  访问时间作为Unix时间戳，如果未提供，则使用当前系统时间
     * @param  int|null        $atime 访问时间作为Unix时间戳，如果未提供，则使用当前系统时间
     * @return bool 
     * @throws FileSystemException When touch fails
     */
    public function touch($files, int $time = null, int $atime = null)
    {
        foreach ($this->toIterable($files) as $file) {
            $touch = $time ? @touch($file, $time, $atime) : @touch($file);
            if (true !== $touch) {
                return $this->setErrorInfo(sprintf('Failed to touch "%s".', $file), __METHOD__, __LINE__, $file);
            }
        }
        return true;
    }
    /**
     * 更改文件的所有者
     *
     * @param  string|iterable $files     要更改所有者的文件名、文件数组或可遍历实例
     * @param  string|int      $user      用户名或号码
     * @param  bool            $recursive 是否递归更改
     * @return bool
     * @throws FileSystemException When the change fails
     */
    public function chown($files, $user, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chown(new \FilesystemIterator($file), $user, true);
            }
            if (is_link($file) && \function_exists('lchown')) {
                if (true !== @lchown($file, $user)) {
                    return $this->setErrorInfo(sprintf('Failed to chown file "%s".', $file), __METHOD__, __LINE__, $file);
                }
            } else {
                if (true !== @chown($file, $user)) {
                    return $this->setErrorInfo(sprintf('Failed to chown file "%s".', $file), __METHOD__, __LINE__, $file);
                }
            }
        }
        return true;
    }
    /**
     * 更改文件或目录数组的组
     *
     * @param  string|iterable $files     要更改组的文件名、文件数组或可遍历实例
     * @param  string|int      $group     组名或号码
     * @param  bool            $recursive 是否递归更改
     * @return bool
     * @throws FileSystemException When the change fails
     */
    public function chgrp($files, $group, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chgrp(new \FilesystemIterator($file), $group, true);
            }
            if (is_link($file) && \function_exists('lchgrp')) {
                if (true !== @lchgrp($file, $group)) {
                    return $this->setErrorInfo(sprintf('Failed to chgrp file "%s".', $file), __METHOD__, __LINE__, $file);
                }
            } else {
                if (true !== @chgrp($file, $group)) {
                    return $this->setErrorInfo(sprintf('Failed to chgrp file "%s".', $file), __METHOD__, __LINE__, $file);
                }
            }
        }
        return true;
    }
    /**
     * 创建到文件的硬链接或多个硬链接
     *
     * @param  string $originFile 源文件
     * @param  string|string[] $targetFiles 目标文件（支持多个）
     * @return bool
     * @throws FileNotFoundException When original file is missing or not a file
     * @throws FileSystemException           When link fails, including if link already exists
     */
    public function hardlink(string $originFile, $targetFiles)
    {
        if (!$this->exists($originFile)) {
            return $this->setErrorInfo(sprintf('File "%s" could not be found.', $originFile), __METHOD__, __LINE__, $originFile);
        }
        if (!is_file($originFile)) {
            return $this->setErrorInfo(sprintf('Origin file "%s" is not a file.', $originFile), __METHOD__, __LINE__, $originFile);
        }
        foreach ($this->toIterable($targetFiles) as $targetFile) {
            if (is_file($targetFile)) {
                if (fileinode($originFile) === fileinode($targetFile)) {
                    continue;
                }
                $this->delPath($targetFile);
            }
            if (!self::box('link', $originFile, $targetFile)) {
                return $this->linkException($originFile, $targetFile, 'hard');
            }
        }
        return true;
    }
    /**
     * @param string $linkType 链接类型的名称，通常为 'symbolic' or 'hard'
     */
    private function linkException(string $origin, string $target, string $linkType)
    {
        if (self::$lastError) {
            if ('\\' === \DIRECTORY_SEPARATOR && false !== strpos(self::$lastError, 'error code(1314)')) {
                return $this->setErrorInfo(sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType), __METHOD__, __LINE__, $target);
            }
        }
        return $this->setErrorInfo(sprintf('Failed to create "%s" link from "%s" to "%s".', $linkType, $origin, $target), __METHOD__, __LINE__, $target);
    }
    /**
     * 读取链接目标
     *
     * @param  string $path 
     * @param  bool $canonicalize 
     * 什么时候$canonicalize是false
     *      - 如果$path不存在或不是链接，则返回null。
     *      - 如果$path是链接，它将返回链接的下一个直接目标，而不考虑目标的存在。
     *
     * 什么时候$canonicalize是true
     *      - 如果$path不存在，则返回null。
     *      - 如果$path存在，则返回其完全解析的绝对最终版本。
     *
     * @return string|null
     */
    public function readlink(string $path, bool $canonicalize = false)
    {
        if (!$canonicalize && !is_link($path)) {
            return null;
        }
        if ($canonicalize) {
            if (!$this->exists($path)) {
                return null;
            }
            if ('\\' === \DIRECTORY_SEPARATOR) {
                $path = readlink($path);
            }
            return realpath($path);
        }
        if ('\\' === \DIRECTORY_SEPARATOR) {
            return realpath($path);
        }
        return readlink($path);
    }
    /**
     * 采取两条绝对路径并返回从第二条路径到第一条路径的相对路径
     *
     * @param  string $endPath
     * @param  string $startPath
     * @return string 
     */
    public function makePathRelative(string $endPath, string $startPath)
    {
        if (!$this->isAbsolutePath($startPath)) {
            throw new \InvalidArgumentException(sprintf('The start path "%s" is not absolute.', $startPath));
        }
        if (!$this->isAbsolutePath($endPath)) {
            throw new \InvalidArgumentException(sprintf('The end path "%s" is not absolute.', $endPath));
        }
        // Normalize separators on Windows
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $endPath = str_replace('\\', '/', $endPath);
            $startPath = str_replace('\\', '/', $startPath);
        }
        $stripDriveLetter = function ($path) {
            if (\strlen($path) > 2 && ':' === $path[1] && '/' === $path[2] && ctype_alpha($path[0])) {
                return substr($path, 2);
            }
            return $path;
        };
        $endPath = $stripDriveLetter($endPath);
        $startPath = $stripDriveLetter($startPath);
        // Split the paths into arrays
        $startPathArr = explode('/', trim($startPath, '/'));
        $endPathArr = explode('/', trim($endPath, '/'));
        $normalizePathArray = function ($pathSegments) {
            $result = [];
            foreach ($pathSegments as $segment) {
                if ('..' === $segment) {
                    array_pop($result);
                } elseif ('.' !== $segment) {
                    $result[] = $segment;
                }
            }
            return $result;
        };
        $startPathArr = $normalizePathArray($startPathArr);
        $endPathArr = $normalizePathArray($endPathArr);
        // Find for which directory the common path stops
        $index = 0;
        while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
            ++$index;
        }
        // Determine how deep the start path is relative to the common path (ie, "web/bundles" = 2 levels)
        if (1 === \count($startPathArr) && '' === $startPathArr[0]) {
            $depth = 0;
        } else {
            $depth = \count($startPathArr) - $index;
        }
        // Repeated "../" for each level need to reach the common path
        $traverser = str_repeat('../', $depth);
        $endPathRemainder = implode('/', \array_slice($endPathArr, $index));
        // Construct $endPath from traversing to the common path, then to the remaining $endPath
        $relativePath = $traverser . ('' !== $endPathRemainder ? $endPathRemainder . '/' : '');
        return '' === $relativePath ? './' : $relativePath;
    }
    /**
     * 返回文件路径是否为绝对路径
     *
     * @param  string $file  文件或目录
     * @return bool
     */
    public function isAbsolutePath(string $file)
    {
        return strspn($file, '/\\', 0, 1) || \strlen($file) > 3 && ctype_alpha($file[0]) && ':' === $file[1] && strspn($file, '/\\', 2, 1) || null !== parse_url($file, PHP_URL_SCHEME);
    }
    /**
     * 创建具有唯一文件名的临时文件，并返回其路径
     *
     * @param  string $dir 
     * @param  string $prefix 生成的临时文件名的前缀，注意：Windows只使用前缀的前三个字符
     * @return string 
     */
    public function tempnam(string $dir, string $prefix)
    {
        list($scheme, $hierarchy) = $this->getSchemeAndHierarchy($dir);
        // 如果没有scheme或scheme是“file”或“gs”（Google Cloud），则在本地文件系统中创建临时文件
        if (null === $scheme || 'file' === $scheme || 'gs' === $scheme) {
            $tmpFile = @tempnam($hierarchy, $prefix);
            // 如果tempnam失败或没有方案返回文件名，则在方案前面加上
            if (false !== $tmpFile) {
                if (null !== $scheme && 'gs' !== $scheme) {
                    return $scheme . '://' . $tmpFile;
                }
                return $tmpFile;
            }
            return $this->setErrorInfo('A temporary file could not be created.', __METHOD__, __LINE__);
        }
        // 循环，直到创建有效的临时文件或达到10次尝试
        for ($i = 0; $i < 10; ++$i) {
            // 创建唯一的文件名
            $tmpFile = $dir . '/' . $prefix . uniqid(mt_rand(), true);
            // 使用fopen而不是file_存在，因为某些流不支持stat
            // 使用模式“x+”原子检查存在性并创建以避免TOCTOU漏洞
            $handle = @fopen($tmpFile, 'x+');
            // 如果不成功，则重新启动循环
            if (false === $handle) {
                continue;
            }
            // 如果文件已成功打开，关闭它并返回结果
            @fclose($handle);
            return $tmpFile;
        }
        return $this->setErrorInfo('A temporary file could not be created.', __METHOD__, __LINE__);
    }
    /**
     * 删除指定路径处的文件
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths)
    {
        if (is_string($paths)) {
            return $this->unlink($paths);
        }
        $success = true;
        foreach ($paths as $path) {
            try {
                if (!$this->unlink($path)) {
                    $success = false;
                }
            } catch (\Throwable $e) {
                $success = false;
            }
        }
        return $success;
    }
    /**
     * 复制文件，如果目标已经存在，则仅当源修改日期晚于目标时才复制文件
     *
     * @param  string $originFile  源文件
     * @param  string $targetFile  目标文件
     * @param  bool   $overwriteNewerFiles  是否覆盖目标文件
     * @return bool 
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws FileSystemException           When copy fails
     */
    public function copyFile(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
    {
        $originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
        if ($originIsLocal && !is_file($originFile)) {
            return $this->setErrorInfo(sprintf('Failed to copy "%s" because file does not exist.', $originFile), __METHOD__, __LINE__, $originFile);
        }
        $this->ensureDirectoryExists(\dirname($targetFile));
        $doCopy = true;
        if (!$overwriteNewerFiles && null === parse_url($originFile, PHP_URL_HOST) && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        }
        if ($doCopy) {
            // https://bugs.php.net/64634
            if (false === ($source = @fopen($originFile, 'r'))) {
                return $this->setErrorInfo(sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading.', $originFile, $targetFile), __METHOD__, __LINE__, $originFile);
            }
            // 创建流上下文以允许在使用FTP流包装器时覆盖文件-默认情况下禁用
            if (false === ($target = @fopen($targetFile, 'w', null, stream_context_create(['ftp' => ['overwrite' => true]])))) {
                return $this->setErrorInfo(sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing.', $originFile, $targetFile), __METHOD__, __LINE__, $originFile);
            }
            $bytesCopied = stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);
            if (!is_file($targetFile)) {
                return $this->setErrorInfo(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), __METHOD__, __LINE__, $originFile);
            }
            if ($originIsLocal) {
                // 像“cp”一样，保留可执行权
                @chmod($targetFile, fileperms($targetFile) | fileperms($originFile) & 0111);
                if ($bytesCopied !== ($bytesOrigin = filesize($originFile))) {
                    return $this->setErrorInfo(sprintf('Failed to copy the whole content of "%s" to "%s" (%g of %g bytes copied).', $originFile, $targetFile, $bytesCopied, $bytesOrigin), __METHOD__, __LINE__, $originFile);
                }
            }
            return true;
        }
        return false;
    }
    /**
     * 创建指向目标文件或目录的符号链接。在Windows上，如果目标是文件，则创建硬链接。
     *
     * @param  string  $target
     * @param  string  $link
     * @return void
     */
    public function link(string $target, string $link)
    {
        if (!$this->runningInWin()) {
            return \symlink($target, $link);
        }
        $mode = $this->isDir($target) ? 'J' : 'H';
        \exec("mklink /{$mode} " . \escapeshellarg($link) . ' ' . \escapeshellarg($target));
    }
    /**
     * 创建符号链接或复制目录
     *
     * @throws FileSystemException When symlink fails
     */
    public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false)
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $originDir = strtr($originDir, '/', '\\');
            $targetDir = strtr($targetDir, '/', '\\');
            if ($copyOnWindows) {
                $this->copyPath($originDir, $targetDir);
                return;
            }
        }
        $this->ensureDirectoryExists(\dirname($targetDir));
        if (is_link($targetDir)) {
            if (readlink($targetDir) === $originDir) {
                return;
            }
            $this->delPath($targetDir);
        }
        if (!self::box('symlink', $originDir, $targetDir)) {
            return $this->linkException($originDir, $targetDir, 'symbolic');
        }
    }
	public function maxPathLength($path)
    {
		$path = (string) $path;
        $maxPathLength = PHP_MAXPATHLEN - 2;
        if (\strlen($path) > $maxPathLength) {
            return $this->setErrorInfo(sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength), __METHOD__, __LINE__, $path);
        }
        return $path;
    }
    /**
     * 返回路径中的文件名部分（包含后缀）
     *
     * @param  string  $path
     * @return string
     */
    public function name($path)
    {
        $path = $this->maxPathLength($path);
        return \pathinfo($path, \PATHINFO_FILENAME);
    }
    /**
     * 返回路径中的文件名部分（不含后缀）
     *
     * @param  string  $path
     * @return string
     */
    public function basename($path)
    {
        $path = $this->maxPathLength($path);
        return \pathinfo($path, \PATHINFO_BASENAME);
    }
    /**
     * 返回路径的父目录
     *
     * @param  string  $path
     * @return string
     */
    public function dirname($path)
    {
        $path = $this->maxPathLength($path);
        return \pathinfo($path, \PATHINFO_DIRNAME);
    }
    /**
     * 返回文件扩展名
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path)
    {
        $path = $this->maxPathLength($path);
        return \pathinfo($path, \PATHINFO_EXTENSION);
    }
    /**
     * 返回文件类型
     *
     * @param  string  $path
     * @return string
     */
    public function type($path)
    {
        $path = $this->maxPathLength($path);
        return \filetype($path);
    }
    /**
     * 返回文件的mime类型
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType($path)
    {
        $path = $this->maxPathLength($path);
        return \finfo_file(\finfo_open(\FILEINFO_MIME_TYPE), $path);
    }
    /**
     * 获取给定文件的文件大小
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        $path = $this->maxPathLength($path);
        return \filesize($path);
    }
    /**
     * 获取内容大小
     *
     * @param  string  $contents
     * @return int 
     */
    public function contentSize($contents)
    {
        return \defined('MB_OVERLOAD_STRING') ? \mb_strlen((string) $contents, '8bit') : \strlen((string) $contents);
    }
    /**
     * 获取文件的最后修改时间
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        $path = $this->maxPathLength($path);
        return \filemtime($path);
    }
    /**
     * 确定给定路径是否为目录
     *
     * @param  string  $path
     * @return bool
     */
    public function isDir($path)
    {
        $path = $this->maxPathLength($path);
        return \is_dir($path);
    }
    /**
     * 确定给定路径是否可读
     *
     * @param  string  $path
     * @return bool
     */
    public function isReadable($path)
    {
        $path = $this->maxPathLength($path);
        return \is_readable($path);
    }
    /**
     * 确定给定路径是否可写
     *
     * @param  string  $path
     * @return bool
     */
    public function isWritable($path)
    {
        $path = $this->maxPathLength($path);
        return \is_writable($path);
    }
    /**
     * 确定给定路径是否为文件
     *
     * @param  string  $path
     * @return bool
     */
    public function isFile($path)
    {
        $path = $this->maxPathLength($path);
        return \is_file($path);
    }
    /**
     * 寻找与模式匹配的文件路径
     *
     * @param  string  $pattern
     * @param  int  $flags
     * @return array
     */
    public function glob(string $pattern, int $flags = 0)
    {
        return \glob($pattern, $flags);
    }
    /**
     * 获取目录中所有文件的数组
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function gbfiles(string $directory, bool $hidden = false)
    {
        return iterator_to_array($this->Finder->files()->ignoreDotFiles(!$hidden)->in($directory)->depth(0)->sortByName(), false);
    }
    /**
     * 从给定目录中获取所有文件（递归）
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function allFiles(string $directory, bool $hidden = false)
    {
        return iterator_to_array($this->Finder->files()->ignoreDotFiles(!$hidden)->in($directory)->sortByName(), false);
    }
    /**
     * 获取给定目录中的所有目录
     *
     * @param  string  $directory
     * @return array
     */
    public function directories(string $directory)
    {
        $directories = [];
        foreach ($this->Finder->in($directory)->directories()->depth(0)->sortByName() as $dir) {
            $directories[] = $dir->getPathname();
        }
        return $directories;
    }
    /**
     * 递归文件和文件夹
     * @param string $path 目录路径 
     * @param string $ext 指定文件后缀（*表示不指定）
     * @return array
     */
    public function findFilesAndPath($path, string $ext = '*')
    {
        $filesMap = [];
        $pathsMap = [];
        $otherMap = [];
        if (is_string($path)) {
            if (is_file($path)) {
                $path = [new \SplFileInfo($path)];
            } elseif (is_dir($path)) {
                $objects = $this->Finder->ignoreDotFiles(false)->in($path);
                $path = [];
                /** @var SplFileInfo $object */
                foreach ($objects as $object) {
                    $path[] = $object;
                }
            } else {
                return ['filesMap' => $filesMap, 'pathsMap' => $pathsMap, 'otherMap' => $otherMap];
            }
        }
        if (strpos($ext, '.') !== false) {
            $ext = str_replace('.', '', $ext);
        }
        if ($ext == '*' || $ext == '') {
            /** @var SplFileInfo $file */
            foreach ($path as $file) {
                if ($file->isFile()) {
                    $filesMap["[type:{$file->getType()}]"] = $file->getPathname();
                } elseif ($file->isDir() && !$file->isLink()) {
                    $pathsMap["[type:{$file->getType()}]"] = $file->getPathname();
                } else {
                    $otherMap["[type:{$file->getType()}]"] = $file->getPathname();
                }
            }
        } else {
            /** @var SplFileInfo $file */
            foreach ($path as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getPathname();
                    if (pathinfo($filePath, PATHINFO_EXTENSION) != $ext) {
                        continue;
                    }
                    $filesMap["[type:{$file->getType()}]"] = $filePath;
                } elseif ($file->isDir() && !$file->isLink()) {
                    $pathsMap["[type:{$file->getType()}]"] = $file->getPathname();
                } else {
                    $otherMap["[type:{$file->getType()}]"] = $file->getPathname();
                }
            }
        }
        return ['filesMap' => $filesMap, 'pathsMap' => $pathsMap, 'otherMap' => $otherMap];
    }
    /**
     * 目录不存在则创建
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public function ensureDirectoryExists(string $path, int $mode = 0755, bool $recursive = true)
    {
        if (!$this->isDir($path)) {
            $this->mkPath($path, $mode, $recursive);
        }
    }
    /**
     * 创建目录
     *
     * @param  string|iterable $dirs 目录路径，可以将数组或任何Traversable对象作为第一个参数传递
     * @param  int  $mode
     * @param  bool  $recursive  允许递归创建由 dirs 所指定的多级嵌套目录
     * @return bool
     * @throws FileSystemException On any directory creation failure
     */
    public function mkPath($dirs, int $mode = 0755, bool $recursive = true)
    {
        foreach ($this->toIterable($dirs) as $dir) {
            if (\is_dir($dir)) {
                continue;
            }
            if (!self::box('mkdir', $dir, $mode, $recursive)) {
                if (!\is_dir($dir)) {
                    if (self::$lastError) {
                        return $this->setErrorInfo(sprintf('Failed to create "%s": ' . self::$lastError, $dir), __METHOD__, __LINE__, $dir);
                    }
                    return $this->setErrorInfo(sprintf('Failed to create "%s".', $dir), __METHOD__, __LINE__, $dir);
                }
            }
        }
        return true;
    }
    /**
     * 移动文件或文件夹（更改单个文件或目录的名称）
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     *
     * @throws FileSystemException
     */
    public function move($path, $target)
    {	
        return $this->rename((string) $path, (string) $target, true);
    }
    /**
     * 移动文件或文件夹（更改单个文件或目录的名称）
     *
     * @param  bool            $overwrite 是否覆盖已经存在的文件
     * @return bool
     * @throws FileSystemException When target file or directory already exists
     * @throws FileSystemException When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = false)
    {
        // 目标存在
        if (!$overwrite && $this->isReadable($target)) {
            return $this->setErrorInfo(sprintf('Cannot rename because the target "%s" already exists.', $target), __METHOD__, __LINE__, $target);
        }
        if (true !== @rename($origin, $target)) {
            if ($this->isDir($origin)) {
                // See https://bugs.php.net/54097 & https://php.net/rename#113943
                $this->copyPath($origin, $target, null, ['override' => $overwrite, 'delete' => $overwrite]);
                $this->delPath($origin);
                return true;
            }
            return $this->setErrorInfo(sprintf('Cannot rename "%s" to "%s".', $origin, $target), __METHOD__, __LINE__, $target);
        }
        return false;
    }
    /**
     * 复制文件夹
     *
     * 默认情况下：
     *
     *  - 目标目录中的现有文件将被覆盖，除非它们较新（请参见“覆盖”选项）
     *  - 不会删除目标目录中源目录中不存在的文件（请参见“删除”选项）
     *
     * @param  string $originDir 源目录
     * @param  string $targetDir 目标目录
     * @param  \Traversable|null $iterator 迭代器，过滤要复制的文件和目录，如果为空，则创建递归迭代器
     * @param  array             $options  布尔选项数组
     *                                    有效选项包括：
     *                                    - $options['override']如果为true，则覆盖比原始文件新的目标文件（请参见copy（），默认为false）
     *                                    - $options['copy_on_windows'] 是否在Windows上复制文件而不是链接（请参见FilesystemComponents::symlink（），默认为false）
     *                                    - $options['delete'] 是否删除不在源目录中的文件（默认为false）
     * @return bool
     * @throws FileSystemException When file type is unknown
     */
    public function copyPath(string $originDir, string $targetDir, \Traversable $iterator = null, array $options = [])
    {
        if ($this->runningInWin() && !isset($options['copy_on_windows'])) {
            $options['copy_on_windows'] = true;
        }
        $targetDir = rtrim($targetDir, '/\\');
        $originDir = rtrim($originDir, '/\\');
        $originDirLen = \strlen($originDir);
        if (!$this->exists($originDir)) {
            return $this->setErrorInfo(sprintf('The origin directory specified "%s" was not found.', $originDir), __METHOD__, __LINE__, $originDir);
        }
        // 在目标文件夹中迭代以删除过时的条目
        if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
            $deleteIterator = $iterator;
            if (null === $deleteIterator) {
                $flags = \FilesystemIterator::SKIP_DOTS;
                $deleteIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir, $flags), \RecursiveIteratorIterator::CHILD_FIRST);
            }
            $targetDirLen = \strlen($targetDir);
            foreach ($deleteIterator as $file) {
                $origin = $originDir . substr($file->getPathname(), $targetDirLen);
                if (!$this->exists($origin)) {
                    $this->delPath($file);
                }
            }
        }
        $copyOnWindows = $options['copy_on_windows'] ?? false;
        if (null === $iterator) {
            $flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
        }
        $this->ensureDirectoryExists($targetDir);
        $filesCreatedWhileMirroring = [];
        foreach ($iterator as $file) {
            if ($file->getPathname() === $targetDir || $file->getRealPath() === $targetDir || isset($filesCreatedWhileMirroring[$file->getRealPath()])) {
                continue;
            }
            $target = $targetDir . substr($file->getPathname(), $originDirLen);
            $filesCreatedWhileMirroring[$target] = true;
            if (!$copyOnWindows && is_link($file)) {
                $this->symlink($file->getLinkTarget(), $target);
            } elseif (is_dir($file)) {
                $this->ensureDirectoryExists($target);
            } elseif (is_file($file)) {
                $this->copyFile($file, $target, isset($options['override']) ? $options['override'] : false);
            } else {
                return $this->setErrorInfo(sprintf('Unable to guess "%s" file type.', $file), __METHOD__, __LINE__, $file);
            }
        }
        return true;
    }
    /**
     * 复制文件夹
     *
     * @param  string  $directory
     * @param  string  $destination
     * @param  int  $options
     * @return bool
     */
    public function copyDirectory(string $directory, string $destination, int $options = \FilesystemIterator::SKIP_DOTS)
    {
        if (!$this->isDir($directory)) {
            return false;
        }
        // 如果目标目录不存在，将继续
        // 递归地创建它，它只是让目的地准备好复制
        // 文件结束了。一旦做了目录，将继续复制。
        $this->ensureDirectoryExists($destination, 0777, true);
        $items = new \FilesystemIterator($directory, $options);
        foreach ($items as $item) {
            // 检查当前文件是否实际
            // 目录或文件。当它实际上是一个需要调用的目录时
            // 递归地返回到该函数中，以继续复制这些嵌套文件夹。
            $target = $destination . '/' . $item->getBasename();
            if ($item->isDir()) {
                $path = $item->getPathname();
                if (!$this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            } else {
                if (!$this->copyFile($item->getPathname(), $target, true)) {
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 删除文件，目录和符号链接
     *
     * @param  string|iterable $files 要删除的文件名、文件数组或可遍历实例
     * @return bool
     * @throws FileSystemException When removal fails
     */
    public function delPath($files)
    {
        if ($files instanceof \Traversable) {
            $files = iterator_to_array($files, false);
        } elseif (!\is_array($files)) {
            $files = [$files];
        }
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (is_link($file)) {
                // See https://bugs.php.net/52176
                if (!(self::box('unlink', $file) || '\\' !== \DIRECTORY_SEPARATOR || self::box('rmdir', $file)) && file_exists($file)) {
                    return $this->setErrorInfo(sprintf('Failed to remove symlink "%s": ' . self::$lastError, $file), __METHOD__, __LINE__);
                }
            } elseif (is_dir($file)) {
                $this->delPath(new \FilesystemIterator($file, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));
                if (!self::box('rmdir', $file) && file_exists($file)) {
                    return $this->setErrorInfo(sprintf('Failed to remove directory "%s": ' . self::$lastError, $file), __METHOD__, __LINE__);
                }
            } elseif (!self::box('unlink', $file) && file_exists($file)) {
                return $this->setErrorInfo(sprintf('Failed to remove file "%s": ' . self::$lastError, $file), __METHOD__, __LINE__);
            }
        }
        return true;
    }
    /**
     * 删除文件（指定文件）
     *
     * @access public
     * @param  string $file
     * @return bool
     */
    public function unlink($file)
    {
		if (!$this->isFile($file)) {
            return false;
        }
		if (!self::box('unlink', (string) $file) && file_exists((string) $file)) {
            return $this->setErrorInfo(sprintf('Failed to remove file "%s": ' . self::$lastError, (string) $file), __METHOD__, __LINE__, (string) $file);
        }
        return true;
    }
    /**
     * 递归删除目录
     *
     * @param  string  $directory
     * @param  bool  $preserve  是否保留目录本身
     * @return bool
     */
    public function deleteDirectory(string $directory, bool $preserve = false)
    {
        if (!$this->isDir($directory)) {
            return false;
        }
        $items = new \FilesystemIterator($directory);
        foreach ($items as $item) {
            // 如果项是一个目录，可以递归到函数中
            // 删除那个子目录，否则只删除文件
            // 继续遍历每个文件，直到目录被清除。
            if ($item->isDir() && !$item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            } else {
                $this->delete($item->getPathname());
            }
        }
        //$dir = new \DirectoryIterator($directory);
        //foreach ($dir as $fileinfo) {
        //    if ($fileinfo->isFile() || $fileinfo->isLink()) {
        //        $this->unlink($fileinfo->getPathName());
        //    } elseif (!$fileinfo->isDot() && $fileinfo->isDir()) {
        //        $this->deleteDirectory($fileinfo->getPathName());
        //    }
        //}
        if (!$preserve) {
            @rmdir($directory);
        }
        return true;
    }
    /**
     * 删除给定目录中的所有目录
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectories(string $directory)
    {
        $allDirectories = $this->directories($directory);
        if (!empty($allDirectories)) {
            foreach ($allDirectories as $directoryName) {
                $this->deleteDirectory($directoryName);
            }
            return true;
        }
        return false;
    }
    /**
     * 清空所有文件和文件夹的指定目录
     *
     * @param  string  $directory
     * @return bool
     */
    public function cleanDirectory(string $directory)
    {
        return $this->deleteDirectory($directory, true);
    }
    /**
     * 递归删除文件夹和文件
     *
     * @param  string  $path 目录路径 
     * @param  string  $ext 指定文件后缀（*表示不指定）
     * @param  array   $deleteFiles 要删除的文件(包含路径，留空表示删除全部)
     * @param  array   $excludeFiles 要排除的文件(包含路径)
     * @param  string  $rmdir 是否删除空目录(默认删除)
     * @return bool
     */
    public function rmFilesAndPath($path, string $ext = '*', array $deleteFiles = [], array $excludeFiles = [], bool $rmdir = true)
    {
        if ($ext === '*' && empty($deleteFiles) && empty($excludeFiles) && $rmdir === true) {
            return $this->deleteDirectory($path, true);
        }
        $findFilesAndPath = $this->findFilesAndPath($path, $ext);
        $filesMap = $findFilesAndPath['filesMap'];
        $pathsMap = $findFilesAndPath['pathsMap'];
        $error = 0;
        // 先删除文件
        if (!empty($filesMap)) {
            if (empty($deleteFiles) && empty($excludeFiles)) {
                $type = 1;
            } elseif (!empty($deleteFiles) && !empty($excludeFiles)) {
                $type = 2;
            } elseif (!empty($deleteFiles) && empty($excludeFiles)) {
                $type = 3;
            } elseif (empty($deleteFiles) && !empty($excludeFiles)) {
                $type = 4;
            } else {
                $type = 5;
            }
            switch ($type) {
                case 1:
                    foreach ($filesMap as $file) {
                        if (is_file($file)) {
                            if (($result = \unlink($file)) === false) {
                                $error++;
                            }
                        }
                    }
                    break;
                case 2:
                    foreach ($filesMap as $file) {
                        if (\in_array($file, $deleteFiles) && !\in_array($file, $excludeFiles) && is_file($file)) {
                            if (($result = \unlink($file)) === false) {
                                $error++;
                            }
                        }
                    }
                    break;
                case 3:
                    foreach ($filesMap as $file) {
                        if (\in_array($file, $deleteFiles) && is_file($file)) {
                            if (($result = \unlink($file)) === false) {
                                $error++;
                            }
                        }
                    }
                    break;
                case 4:
                    foreach ($filesMap as $file) {
                        if (!\in_array($file, $excludeFiles) && is_file($file)) {
                            if (($result = \unlink($file)) === false) {
                                $error++;
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        // 根据条件删除目录
        if ($rmdir && !empty($pathsMap)) {
            if ($result = $this->rmEmptydir($path) === false) {
                $error++;
            }
        }
        return $error === 0;
    }
    /** 
     * 递归删除所有空目录 
     *
     * @param string $path 目录路径 
     * @return bool
     */
    public function rmEmptydir(string $path)
    {
        $error = 0;
        $path = $this->normalizeSuffix($path);
        //if (is_dir($path) && ($handle = @opendir($path)) !== false) {
        //    while (($file = @readdir($handle)) !== false) {
        //        if ($file != '.' && $file != '..') {
        //            // 当前目录
        //            $curfile = $path . $file;
        //            // 目录
        //            if (is_dir($curfile)) {
        //                // 如果是目录则继续遍历
        //                $this->rmEmptydir($curfile);
        //                //目录为空,=2是因为.和..存在
        //                if (@count(@scandir($curfile)) == 2) {
        //                    // 删除空目录
        //                    if (@rmdir($curfile) === false) {
        //                        $error++;
        //                    }
        //                }
        //            }
        //        }
        //    }
        //    @closedir($handle);
        //}
        if ($this->isDir($path)) {
            $handle = new \DirectoryIterator($path);
            foreach ($handle as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $curfile = $fileinfo->getPathname();
                    $this->rmEmptydir($curfile);
                    if (!$fileinfo->valid()) {
                        // 删除空目录
                        if (@rmdir($curfile) === false) {
                            $error++;
                        }
                    }
                }
            }
            @rmdir($path);
        }
        is_dir(dirname($path)) && @rmdir(dirname($path));
        return $error === 0;
    }
    /**
     * 判断给定的路径是否正确
     *
     * @param  string $path 目录路径
     * @return string
     */
    public function has(string $path)
    {
        $path = $this->normalizePath($path);
        if (\strlen($path) === 0) {
			return $this->setErrorInfo(sprintf('Path is outside of the defined root, path: %s', $path), __METHOD__, __LINE__);
        }
        return $path;
    }
    /**
     * 格式化路径(该函数返回的路径不包含最后的斜杠)
     *
     * @param  string|null|bool $path 目录路径
     * @return string
     */
    public function normalizePath($path)
    {
        if (empty($path)) {
            return;
        }
        $parts = array();
        $path = str_replace(" ", "%20", html_entity_decode($path));
        $path = strtr($path, '\\', '/');
        $prefix = '';
        $absolute = false;
        if (preg_match('{^([0-9a-z]+:(?://(?:[a-z]:)?)?)}i', $path, $match)) {
            $prefix = $match[1];
            $path = substr($path, strlen($prefix));
        }
        if (substr($path, 0, 1) === '/') {
            $absolute = true;
            $path = substr($path, 1);
        }
        $up = false;
        foreach (explode('/', $path) as $chunk) {
            if ('..' === $chunk && ($absolute || $up)) {
                array_pop($parts);
                $up = !(empty($parts) || '..' === end($parts));
            } elseif ('.' !== $chunk && '' !== $chunk) {
                $parts[] = $chunk;
                $up = '..' !== $chunk;
            }
        }
        $path = $this->replaceSlash($prefix . ($absolute ? '/' : '') . implode('/', $parts));
        return $path === "\\" || $path === "/" ? \DIRECTORY_SEPARATOR : rtrim($path, \DIRECTORY_SEPARATOR);
    }
    /**
     * 格式化路径(该函数返回的路径包含最后的斜杠)
     *
     * @param  string $path 目录路径
     * @return string
     */
    public function normalizeSuffix($path)
    {
        $path = $this->has($path);
        return $this->normalizePrefix($path, \DIRECTORY_SEPARATOR);
    }
    /**
     * 替换斜杠
     *
     * @param  string $path 目录路径
     * @return string
     */
    public function replaceSlash(string $path)
    {
        return str_replace(array("\\", "/"), \DIRECTORY_SEPARATOR, $path);
    }
    /**
     * 返回文件路径的信息
     *
     * @param  string $path
     * @return array pathinfo
     */
    public function pathinfo(string $path)
    {
        $pathinfo = compact('path');
        // 规范路径
        $pathinfo['path'] = $this->normalizePath($pathinfo['path']);
        if ('' !== ($dirname = $this->dirname($path))) {
            $pathinfo['dirname'] = $this->normalizeDirname($dirname);
        }
        $pathinfo['basename'] = $this->pbasename($path);
        $pathinfo += pathinfo($pathinfo['basename']);
        return $pathinfo + ['dirname' => ''];
    }
    /**
     * 规范化dirname返回值
     *
     * @param  string $dirname
     * @return string 
     */
    public function normalizeDirname(string $dirname)
    {
        return $dirname === '.' ? '' : $dirname;
    }
    /**
     * 返回路径中的目录部分
     *
     * @param  string $path
     * @return string 
     */
    public function pdirname(string $path)
    {
        return $this->normalizeDirname($this->dirname($path));
    }
    /**
     * 映射结果数组
     *
     * @param  array $object
     * @param  array $map
     * @return array 
     */
    public function map(array $object, array $map)
    {
        $result = [];
        foreach ($map as $from => $to) {
            if (!isset($object[$from])) {
                continue;
            }
            $result[$to] = $object[$from];
        }
        return $result;
    }
    /**
     * 规范化前/后缀
     *
     * @param  string $prefix
     * @param  string $separator
     * @return string 
     */
    public function normalizePrefix(string $prefix, string $separator)
    {
        return rtrim(rtrim($prefix), $separator) . $separator;
    }
    /**
     * 通过删除后面的斜杠来规范化给定的目录名
     *
     * 不包括：（s）ftp://or ssh2。（s）ftp://wrapper
     *
     * @param  string $dir
     * @return string 
     */
    public function normalizeDir(string $dir)
    {
        if ('/' === $dir) {
            return $dir;
        }
        $dir = rtrim($dir, '/' . \DIRECTORY_SEPARATOR);
        if (preg_match('#^(ssh2\\.)?s?ftp://#', $dir)) {
            $dir .= '/';
        }
        return $dir;
    }
    /**
     * 返回路径中的文件名部分
     *
     * @param  string $path
     * @return string
     */
    private function pbasename(string $path)
    {
        $separators = \DIRECTORY_SEPARATOR === '/' ? '/' : '\\/';
        $path = rtrim($path, $separators);
        $basename = preg_replace('#.*?([^' . preg_quote($separators, '#') . ']+$)#', '$1', $path);
        if (\DIRECTORY_SEPARATOR === '/') {
            return $basename;
        }
        // @codeCoverageIgnoreSta
        while (preg_match('#^[a-zA-Z]{1}:[^\\\\/]#', $basename)) {
            $basename = substr($basename, 2);
        }
        if (preg_match('#^[a-zA-Z]{1}:$#', $basename)) {
            $basename = rtrim($basename, ':');
        }
        // @codeCoverageIgnoreEnd
        return $basename;
    }
    /**
     * 是否运行在命令行下
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }
    /**
     * 是否运行在win下
     *
     * @return bool
     */
    public function runningInWin()
    {
        return strpos(PHP_OS, 'WIN') !== false || PHP_OS_FAMILY === 'Windows';
    }
    /**
     * 是否为64位操作系统
     * 
     * @return bool
     */
    public function runningInX64()
    {
        return PHP_INT_SIZE === 8;
    }
    /**
     * 倒回文件指针的位置
     *
     * @param  resource $resource 
     * @return void
     */
    public function rewindStream($resource)
    {
        if (ftell($resource) !== 0 && $this->isSeekableStream($resource)) {
            rewind($resource);
        }
    }
    public function isSeekableStream($resource)
    {
        $metadata = stream_get_meta_data($resource);
        return $metadata['seekable'];
    }
    /**
     * 写入文件（可安全用于二进制文件）
     *
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
    /**
     * 通过已打开的文件指针取得文件大小
     *
     * @param  resource $resource
     * @return int|null 
     */
    public function getStreamSize($resource)
    {
        $stat = fstat($resource);
        if (!is_array($stat) || !isset($stat['size'])) {
            return null;
        }
        return $stat['size'];
    }
    /** 
     * 多维数组转字符串 
     *
     * @param  mixed $arr 
     * @return srting 
     */
    public function arrayToString($arr)
    {
        if (is_array($arr)) {
            return implode('', array_map(array($this, 'arrayToString'), $arr));
        }
        return $arr;
    }
    /** 
     * 多维数组变成一维数组 
     *
     * @param  array $array 
     * @return array 
     */
    public function multiToarray(array $array)
    {
        static $result_array = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->multiToarray($value);
            } else {
                $result_array[$key] = $value;
            }
        }
        return $result_array;
    }
    /** 
     * 返回数组的维度 
     *
     * @param  array $arr  
     * @return mixed     
     */
    public function arrayLevel(array $arr)
    {
        $al = array(0);
        function aL($arr, &$al, $level = 0)
        {
            if (is_array($arr)) {
                $level++;
                $al[] = $level;
                foreach ($arr as $v) {
                    aL($v, $al, $level);
                }
            }
        }
        aL($arr, $al);
        return max($al);
    }
    public function __call($method, array $params)
    {
        if (\is_callable([$this->Finder, $method])) {
            $instance = $this->Finder;
        } elseif (\is_callable([$this->Filesystem, $method])) {
            $instance = $this->Filesystem;
        } else {
            // 操作不存在
            throw new \BadMethodCallException(\sprintf('Method %s::%s does not exist.', static::class, $method));
        }
        return \call_user_func_array([$instance, $method], $params);
    }
    private function toIterable($files) : iterable
    {
        return \is_array($files) || $files instanceof \Traversable ? $files : [$files];
    }
    /**
     * 获取scheme的2元组（可能为空）和文件名的层次结构部分（例如file:///tmp->[file，tmp]）。
     */
    private function getSchemeAndHierarchy(string $filename) : array
    {
        $components = explode('://', $filename, 2);
        return 2 === \count($components) ? [$components[0], $components[1]] : [null, $components[0]];
    }
    /**
     * @return mixed
     */
    private static function box(callable $func)
    {
        self::$lastError = null;
        set_error_handler(__CLASS__ . '::handleError');
        try {
            $result = $func(...\array_slice(\func_get_args(), 1));
            restore_error_handler();
            return $result;
        } catch (\Throwable $e) {
        }
        restore_error_handler();
        throw $e;
    }
    /**
     * @internal
     */
    public static function handleError($type, $msg)
    {
        self::$lastError = $msg;
    }
}