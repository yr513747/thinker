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

use SplTempFileObject;
use think\traits\think\SplTrait;
/**
 * @Methods
 * @method TempFileObject make (int $max_memory) : 构造新的临时文件对象
 */
class TempFileObject extends SplTempFileObject
{
    use SplTrait;
    /**
     * 文件操作对象
     * @var null|static
     */
    protected static $file = null;
    protected function __construct(int $max_memory = 2048000)
    {
        parent::__construct($max_memory);
    }
    /**
     * 创建普通TempFileObject对象
     * @param int $max_memory 
     * @FQA:要使用的临时文件的最大内存量（字节，默认为2 MB）。如果临时文件超过此大小，它将被移动到系统临时目录中的文件中。
     * @FQA:如果最大内存为负数，则只使用内存。如果最大内存为零，则不使用内存
     * @return TempFileObject
     */
    public static function make(int $max_memory = 2048000) : TempFileObject
    {
        if (is_null(self::$file)) {
            self::$file = self::setClass(new self($max_memory));
        }
        return self::$file;
    }
    /**
     * 获取输出数据
     * @access public
     * @return string
     */
    public function getContent() : string
    {
		if (null == $this->content) {
            $this->content = parent::__toString();
        }
        return $this->content;
    }
	/**
     * 写入文件
     * @access public
     * @param  string   $filename  文件路径
	 * @param  mixed    $data    要写入文件的数据，可以是字符串、数组或数据流
     * @param  int      $flags   如何打开/写入文件
     * @param  resource $context 文件句柄环境
     * @return int
     */
    public function file_put_contents(string $filename, $data = '', int $flags = 0, $context = null) : int
    {
		$mixed = $data ? $data : $this->getContent();
        $result = file_put_contents($filename, $mixed, $flags, $context);
        return $result === false ? 0 : $result;
    }
}