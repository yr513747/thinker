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

class Iterator
{
	/**
     * 设置与SplFileInfo：：openFile（）|SplFileInfo：：getFileInfo（）和SplFileInfo：：getPathInfo（）一起使用的类
     * @access public
     * @param object $object
     * @return self::setClass($object
     */	
    public static function setClass(object $object)
    {
		// 使用此方法设置在调用SplFileInfo：：openFile（）时将使用的自定义类。传递给此方法的类名称必须是SplFileObject或从SplFileObject派生的类。
        $object->setFileClass('\\think\\FileObject');
		// 使用此方法设置在调用SplFileInfo：：getFileInfo（）和SplFileInfo：：getPathInfo（）时将使用的自定义类。传递给此方法的类名必须是SplFileInfo或从SplFileInfo派生的类。
        $object->setInfoClass('\\think\\FileInfo');
        return $object;
    }
    /**
     * 这个迭代器能陆续遍历几个迭代器
     * @access public
     * @return self::setClass(object
     */
    public static function AppendIterator()
    {
        return self::setClass(new \AppendIterator());
    }
    /**
     * 这个迭代器允许在遍历数组和对象时删除和更新值与键
     * @access public
     * @param mixed $array  
     * @param int $flags   
     * @return self::setClass(object
     */
    public static function ArrayIterator($array = array(), int $flags = 0)
    {
        return self::setClass(new \ArrayIterator($array, $flags));
    }
    /**
     * 此对象支持在另一个迭代器上缓存迭代
     * @access public
     * @param \Iterator $iterator  
     * @param int $flags   
     * @return self::setClass(object
     */
    public static function CachingIterator(\Iterator $iterator, int $flags = \CachingIterator::CALL_TOSTRING)
    {
        return self::setClass(new \CachingIterator($iterator, $flags));
    }
    /**
     * 从另一个迭代器创建筛选迭代器
     * @access public
     * @param \Iterator $iterator  
     * @param callable $callback 
     * @return self::setClass(object
     */
    public static function CallbackFilterIterator(\Iterator $iterator, callable $callback)
    {
        return self::setClass(new \CallbackFilterIterator($iterator, $callback));
    }
    /**
     * 从路径构造新的目录迭代器
     * @access public
     * @param string $path  
     * @return self::setClass(object
     */
    public static function DirectoryIterator(string $path)
    {
        return self::setClass(new \DirectoryIterator($path));
    }
    /**
     * 空迭代器的EmptyIterator类
     * @access public
     * @param \Iterator $iterator  
     * @return self::setClass(object
     */
    public static function EmptyIterator()
    {
        return self::setClass(new \EmptyIterator());
    }
    /**
     * 构造一个新的文件系统迭代器
     * @access public
     * @param string $path  
     * @param int $flags
     * @return self::setClass(object
     */
    public static function FilesystemIterator(string $path, int $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS)
    {
        return self::setClass(new \FilesystemIterator($path, $flags));
    }
    /**
     * 这个抽象类的遍历并过滤出不想要的值.这个类应该被实现了迭代过滤器的类继承 FilterIterator::accept()方法必须被子类实现. 这里只是示例
     * @access public
     * @param \Iterator $iterator  
     * @return self::setClass(object
     */
    public static function FilterIterator(\Iterator $iterator)
    {
        return self::setClass(new FilterIterator($iterator));
    }
    /**
     * 遍历一个文件系统行为类似于 glob()
     * @access public
     * @param string $pattern 检索模式
     * @param int $flags 规定特殊的设定 
     * @return self::setClass(object
     */
    public static function GlobIterator(string $pattern, int $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO)
    {
        return self::setClass(new \GlobIterator($pattern, $flags));
    }
    /**
     * 无限运算符允许用户在迭代器上进行完全迭代，而不必在到达迭代器的末尾时手动遍历迭代器
     * @access public
     * @param \Iterator $iterator  
     * @return self::setClass(object
     */
    public static function InfiniteIterator(\Iterator $iterator)
    {
        return self::setClass(new \InfiniteIterator($iterator));
    }
    /**
     * 从任何可遍历的对象创建迭代器
     * @access public
     * @param \Traversable $iterator  
     * @return self::setClass(object
     */
    public static function IteratorIterator(\Traversable $iterator)
    {
        return self::setClass(new \IteratorIterator($iterator));
    }
    /**
     * LimitIterator类允许遍历一个 Iterator 的限定子集的元素
     * @access public
     * @param \Iterator $iterator  
     * @param int $offset
     * @param int $count
     * @return self::setClass(object
     */
    public static function LimitIterator(\Iterator $iterator, int $offset = 0, int $count = -1)
    {
        return self::setClass(new \LimitIterator($iterator, $offset, $count));
    }
    /**
     * 在所有附加迭代器上顺序迭代的迭代器
     * @access public
     * @param int $flags 
     * @return self::setClass(object
     */
    public static function MultipleIterator(int $flags = \MultipleIterator::MIT_NEED_ALL | \MultipleIterator::MIT_KEYS_NUMERIC)
    {
        return self::setClass(new \MultipleIterator($flags));
    }
    /**
     * 此迭代器忽略倒带操作。这允许在多个部分foreach循环中处理迭代器
     * @access public
     * @param \Iterator $iterator  
     * @return self::setClass(object
     */
    public static function NoRewindIterator(\Iterator $iterator)
    {
        return self::setClass(new \NoRewindIterator($iterator));
    }
    /**
     * 这个扩展的FilterIterator允许使用RecursiveIterator进行递归迭代，它只显示那些有子元素的元素
     * @access public
     * @param \RecursiveIterator $iterator  
     * @return self::setClass(object
     */
    public static function ParentIterator(\RecursiveIterator $iterator)
    {
        return self::setClass(new \ParentIterator($iterator));
    }
    /**
     * 此迭代器允许在数组和对象上以与ArrayIterator相同的方式迭代时取消设置和修改值和键。此外，还可以迭代当前迭代器条目
     * @access public
     * @param mixed $array  
     * @param int $flags
     * @return self::setClass(object
     */
    public static function RecursiveArrayIterator($array = array(), int $flags = 0)
    {
        return self::setClass(new \RecursiveArrayIterator($array, $flags));
    }
    /**
     * RecursiveCachingIterator::__construct — Construct
     * @access public
     * @param \Iterator $iterator  
     * @param int $flags
     * @return self::setClass(object
     */
    public static function RecursiveCachingIterator(\Iterator $iterator, int $flags = \RecursiveCachingIterator::CALL_TOSTRING)
    {
        return self::setClass(new \RecursiveCachingIterator($iterator, $flags));
    }
    /**
     * 从\RecursiveIterator创建\RecursiveCallbackFilterIterator
     * @access public
     * @param \RecursiveIterator $iterator  
     * @param string $callback
     * @return self::setClass(object
     */
    public static function RecursiveCallbackFilterIterator(\RecursiveIterator $iterator, string $callback)
    {
        return self::setClass(new \RecursiveCallbackFilterIterator($iterator, $callback));
    }
    /**
     * \RecursiveDirectoryIterator提供了一个在文件系统目录上递归迭代的接口
     * @access public
     * @param string $path  
     * @param int $flags
     * @return self::setClass(object
     */
    public static function RecursiveDirectoryIterator(string $path, int $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO)
    {
        return self::setClass(new \RecursiveDirectoryIterator($path, $flags));
    }
    /**
     * 这个抽象迭代器为递归迭代器筛选出不需要的值。应该扩展这个类以实现自定义筛选器。必须在子类中实现recursiveFilteriter：：accept，这里只是示例
     * @access public
     * @param \RecursiveIterator $iterator 
     * @return self::setClass(object
     */
    public static function RecursiveFilterIterator(\RecursiveIterator $iterator)
    {
        return self::setClass(new RecursiveFilterIterator($iterator));
    }
    /**
     * 可用于迭代递归迭代器
     * @access public
     * @param \Traversable $iterator  
     * @param int $mode
     * @param int $flags
     * @return self::setClass(object
     */
    public static function RecursiveIteratorIterator(\Traversable $iterator, int $mode = \RecursiveIteratorIterator::SELF_FIRST, int $flags = \RecursiveIteratorIterator::CATCH_GET_CHILD)
    {
        return self::setClass(new \RecursiveIteratorIterator($iterator, $mode, $flags));
    }
    /**
     * 这个递归迭代器可以通过正则表达式过滤另一个递归迭代器
     * @access public
     * @param \Iterator $iterator  
     * @return self::setClass(object
     */
    public static function RecursiveRegexIterator(\RecursiveIterator $iterator, string $regex, int $mode = \RecursiveRegexIterator::MATCH, int $flags = 0, int $preg_flags = 0)
    {
        return self::setClass(new \RecursiveRegexIterator($iterator, $regex, $mode, $flags, $preg_flags));
    }
    /**
     * 允许在递归迭代器上迭代以生成ASCII图形树
     * @access public
     * @param \RecursiveIterator|\IteratorAggregate $it  
     * @param int $flags
     * @param int $cit_flags
     * @param int $mode
     * @return self::setClass(object
     */
    public static function RecursiveTreeIterator(\SplFileInfo $it, int $flags = \RecursiveTreeIterator::BYPASS_KEY, int $cit_flags = \CachingIterator::CATCH_GET_CHILD, int $mode = \RecursiveIteratorIterator::SELF_FIRST)
    {
        return self::setClass(new \RecursiveTreeIterator($it, $flags, $cit_flags, $mode));
    }
    /**
     * 此迭代器可用于根据正则表达式筛选另一个迭代器
     * @access public
     * @param \Iterator $iterator  
     * @param string $regex
     * @param int $mode
     * @param int $flags
     * @param int $preg_flags
     * @return self::setClass(object
     */
    public static function RegexIterator(\Iterator $iterator, string $regex, int $mode = \RegexIterator::MATCH, int $flags = 0, int $preg_flags = 0)
    {
        return self::setClass(new \RegexIterator($iterator, $regex, $mode, $flags, $preg_flags));
    }
}
class FilterIterator extends \FilterIterator
{
    public function accept()
    {
        // Only accept strings with a length of 10 and greater
        return strlen(parent::current()) >= 10;
    }
}
class RecursiveFilterIterator extends \RecursiveFilterIterator
{
    public function accept()
    {
        // Accept the current item if we can recurse into it
        // or it is a value starting with "test"
        return $this->hasChildren() || strpos($this->current(), "test") !== FALSE;
    }
}