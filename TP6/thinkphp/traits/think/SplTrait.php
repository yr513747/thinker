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
namespace think\traits\think;

use think\Container;
use think\Util;
use think\Iterator;
use think\FileObject;
use think\FileInfo;
use ReflectionMethod;
use ReflectionException;
use BadMethodCallException;
use RuntimeException;
trait SplTrait
{
    /*各项都包含，例如递归文件夹就会连同子文件夹名称也作为其中项输出，顺序是先父后子*/
    public static $SELF_FIRST = \RecursiveIteratorIterator::SELF_FIRST;
    /*各项都包含，例如递归文件夹就会连同子文件夹名称也作为其中项输出，顺序是先子后父*/
    public static $CHILD_FIRST = \RecursiveIteratorIterator::CHILD_FIRST;
    /*去枝留叶，跳过空节点，只递归取实值*/
    public static $LEAVES_ONLY = \RecursiveIteratorIterator::LEAVES_ONLY;
    /*忽略调用RecursiveIteratorIterator：：getChildren（）时引发的异常*/
    public static $CATCH_GET_CHILD = \RecursiveIteratorIterator::CATCH_GET_CHILD;
    /**
     * 输出内容
     * @var string
     */
    protected $content = null;
    /**
     * 获取输出数据
     * @access public
     * @return string
     */
    public abstract function getContent();
    public function __toString()
    {
        return $this->getContent();
    }
    /**
     * 静态调用
     * @access public
     * @param string $method 调用方法
     * @param array  $params 调用参数
     * @return mixed
     * @throws BadMethodCallException
     */
    public static function __callStatic($method, array $params)
    {
        if (is_callable([Util::class, $method])) {
           
            $reflect = new ReflectionMethod(Util::class, $method);
            
			// 函数调用
            $instance = new Util();
        } elseif (is_callable([Iterator::class, $method])) {
            
            $reflect = new ReflectionMethod(Iterator::class, $method);
           
			// 迭代器调用
            $instance = new Iterator();
        } else {
            // 操作不存在
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }
        return Container::getInstance()->invokeReflectMethod($instance, $reflect, $params);
    }
    /**
     * 扫描文件或目录并返回文件操作对象
     * @param string $file 文件路径，如果是目录以数组包含的形式返回
     * @return FileInfo|array|ERRER
     */
    public static function scanFiles(string $file)
    {
        if (is_file($file)) {
            $map = FileInfo::make($file);
        } elseif (is_dir($file)) {
            $objects = self::RecursiveIteratorIterator(self::RecursiveDirectoryIterator($file), self::$SELF_FIRST);
            /** @var FileInfo $object */
            foreach ($objects as $name => $object) {
                if ($object->isFile()) {
                    $map[$name] = $object;
                }
            }
        } else {
			return 'ERROR:Could not scan for classes inside "[path]" which does not appear to be a file nor a folder';
        }
        return $map;
    }  
	
    /**
     * 遍历一个文件系统行为类似于 glob()
     * @access public
     * @param string $pattern 检索模式
     * @param int $flags 规定特殊的设定 
     * @return \GlobIterator
     */
    public static function glob(string $pattern, int $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO)
    {
        return self::GlobIterator($pattern, $flags);
    }
}
