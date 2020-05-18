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

use SplFileInfo;
use think\traits\think\SplTrait;
use think\exception\SystemException;
/**
 * @Methods
 * @method FileInfo make ($file) : 为指定的文件名创建新的FileInfo对象，文件不需要存在或可读
 * @method int getATime () : 获取文件的最后访问时间
 * @method string getBasename (string $suffix = null) : 获取文件的没有路径信息的基本名称，参数可以为文件后缀，若有参数则返回没有该后缀的文件基本名称
 * @method int getCTime () : 返回文件最后一次变更的时间戳
 * @method string getExtension () : 获取文件扩展名
 * @method FileInfo getFileInfo (string $class_name = null) : 以对象的形式返回文件路径和名称
 * @method string getFilename () : 获取文件名称，不带路径
 * @method int getGroup () : 获取文件所在组，返回组id
 * @method int getInode () : 获取文件索引节点
 * @method string getLinkTarget () : 获取文件链接目标
 * @method int getMTime () : 获取最后修改时间
 * @method int getOwner () : 获取文件的所有者
 * @method string getPath () : 获取文件路径，不带文件名和最后的斜杠
 * @method FileInfo getPathInfo (string $class_name = null) : 返回路径对象
 * @method string getPathname () : 获取文件路径
 * @method int getPerms () : 获取文件权限
 * @method string getRealPath () : 获取文件绝对路径，若文件不存在，返回false
 * @method int getSize () : 返回文件大小，单位字节
 * @method string getType () : 返回文件类型，可能是 file, link, dir
 * @method bool isDir () : 判断是否是目录，是放回true否则返回false
 * @method bool isExecutable () : 判断文件是否可执行，返回true，否则返回false
 * @method bool isFile () : 如果文件存在且是一个普通文件（不是链接），返回true，否则返回false
 * @method bool isLink () : 判断文件是否是连接，不是返回false
 * @method bool isReadable () : 判断文件是否可读，可读返回true
 * @method bool isWritable () : 判断文件是否可写，可写返回true
 * @method FileObject openFile (string $open_mode = "r" , bool $use_include_path = FALSE , resource $context = NULL) : 获取文件对象信息
 * @method void setFileClass (string $class_name = "SplFileObject") : 
 * @method void setInfoClass (string $class_name = "SplFileInfo") : 
 * @method string __toString () : 以字符串的形式返回文件路径及名称
 */
class FileInfo extends SplFileInfo
{
    use SplTrait;
    /**
     * 文件操作对象
     * @var null|static
     */
    protected static $file = null;
    /**
     * 文件hash规则
     * @var array
     */
    protected $hash = [];
    protected $hashName;
    protected function __construct(string $file = '', bool $checkFile = false)
    {
        if ($checkFile && !is_file($file)) {
            throw new SystemException(sprintf('The file "%s" does not exist', $file));
        }
        parent::__construct($file);
    }
    /**
     * 创建普通FileInfo对象，文件不需要存在或可读
     * @param string $file
     * @param bool $checkFile
     * @return FileInfo 
     */
    public static function make(string $file = '', bool $checkFile = false) : FileInfo
    {
        if (is_null(self::$file)) {
            self::$file = self::setClass(new self($file, $checkFile));
        }
        return self::$file;
    }
    /**
     * 获取文件的哈希散列值
     * @access public
     * @param string $type
     * @return string
     */
    public function hash(string $type = 'sha1') : string
    {
        if (!isset($this->hash[$type])) {
            $this->hash[$type] = hash_file($type, $this->getPathname());
        }
        return $this->hash[$type];
    }
    /**
     * 获取文件的MD5值
     * @access public
     * @return string
     */
    public function md5() : string
    {
        return $this->hash('md5');
    }
    /**
     * 获取文件的SHA1值
     * @access public
     * @return string
     */
    public function sha1() : string
    {
        return $this->hash('sha1');
    }
    /**
     * 获取文件类型信息
     * @access public
     * @return string
     */
    public function getMime() : string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $this->getPathname());
    }
    /**
     * 移动文件
     * @access public
     * @param string      $directory 保存路径
     * @param string|null $name      保存的文件名
     * @return FileInfo
     */
    public function move(string $directory, string $name = null) : FileInfo
    {
        $target = $this->getTargetFile($directory, $name);
        set_error_handler(function ($type, $msg) use(&$error) {
            $error = $msg;
        });
        $renamed = rename($this->getPathname(), (string) $target);
        restore_error_handler();
        if (!$renamed) {
            throw new SystemException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error)));
        }
        @chmod((string) $target, 0666 & ~umask());
        return $target;
    }
    /**
     * 实例化一个新文件
     * @param string      $directory
     * @param null|string $name
     * @return FileInfo
     */
    protected function getTargetFile(string $directory, string $name = null) : FileInfo
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new SystemException(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new SystemException(sprintf('Unable to write in the "%s" directory', $directory));
        }
        $target = rtrim($directory, '/\\') . \DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));
        return self::$file = self::setClass(new self($target, false));
    }
    /**
     * 获取文件名
     * @param string $name
     * @return string
     */
    protected function getName(string $name) : string
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);
        return $originalName;
    }
    /**
     * 文件扩展名
     * @return string
     */
    public function extension() : string
    {
        return $this->getExtension();
    }
    /**
     * 自动生成文件名
     * @access protected
     * @param string|\Closure $rule
     * @return string
     */
    public function hashName($rule = 'date') : string
    {
        if (!$this->hashName) {
            if ($rule instanceof \Closure) {
                $this->hashName = call_user_func_array($rule, [$this]);
            } else {
                switch (true) {
                    case in_array($rule, hash_algos()):
                        $hash = $this->hash($rule);
                        $this->hashName = substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2);
                        break;
                    case is_callable($rule):
                        $this->hashName = call_user_func($rule);
                        break;
                    default:
                        $this->hashName = date('Ymd') . DIRECTORY_SEPARATOR . md5((string) microtime(true));
                        break;
                }
            }
        }
        return $this->hashName . '.' . $this->extension();
    }
    /**
     * 获取输出数据
     * @access public
     * @return string
     */
    public function getContent() : string
    {
        if (null == $this->content) {
            $content = parent::__toString();
            if ($this->isFile() && $this->isReadable()) {
                $content = $this->openFile('rb')->getContent();
            }
            $this->content = $content;
        }
        return $this->content;
    }
}