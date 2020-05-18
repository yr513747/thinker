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

use SplFileObject;
use Throwable;
use think\traits\think\SplTrait;
use think\exception\SystemException;
/**
 * @Constants 
 * @const integer DROP_NEW_LINE = 1 ;
 * @const integer READ_AHEAD = 2 ;
 * @const integer SKIP_EMPTY = 4 ;
 * @const integer READ_CSV = 8 ;
 * @Methods
 * @method FileObject make ($filename, string $open_mode = "r", bool $use_include_path = FALSE, resource $context) : 构造新的文件对象
 * @method string|array current () : 返回文件当前行内容
 * @method bool eof () : 检测文件是否到末尾，如果到末尾返回true，否则返回false
 * @method bool fflush () : 将缓冲内容输出到文件,成功时返回 TRUE， 或者在失败时返回 FALSE
 * @method string fgetc () : 按字符读取文件
 * @method array fgetcsv (string $delimiter = ",", string $enclosure = "\"", string $escape = "\\") : 读取csv文件
 * @method string fgets () : 按行读取文件
 * @method string fgetss (string $allowable_tags) : 按行读取文件，并去掉html标记
 * @method bool flock ( int $operation, int &$wouldblock) : 文件锁定或解锁，返回true或false
 * @method int fpassthru () : 输出文件指针之后的所有数据和字符数
 * @method int fputcsv ( array $fields, string $delimiter = ",", string $enclosure = '"', string $escape = "\\") : 将一维数组作为一行输入csv文件中，返回写入的字符串长度或false
 * @method string fread ( int $length ) : 从文件中读取指定的字节数，返回读取的字符串或false
 * @method mixed fscanf ( string $format, mixed &$...) : 从文件中读取一行并按照指定模式解析
 * @method int fseek ( int $offset, int $whence = SEEK_SET) : 按字节移动文件指针位置
 * @method array fstat () : 获取文件指针位置的信息
 * @method int ftell () : 返回当前文件位置，文件指针位置
 * @method bool ftruncate ( int $size ) : 将文件截断到指定的长度，若长度大于文件长度用空补齐（文件打开方法对其有影响）
 * @method int fwrite ( string $str, int $length) : 将$str字符串写入文件，只写$length长度。放回写入字节数或null
 * @method void getChildren () : No purpose
 * @method array getCsvControl () : 获取用于分析CSV字段的分隔符和封闭符
 * @method int getFlags () : 获取将SplFileObject的实例设置为整数的标志
 * @method int getMaxLineLen () : 返回一行读取的最大字节数（在已设置的前提下），若未设置，默认为0
 * @method bool hasChildren () : SplFileObject是否存在子级
 * @method int key () : 获取当前行号
 * @method void next () : 移动到下一行
 * @method void rewind () : 返回到第一行
 * @method void seek ( int $line_pos ) : 定位到文件指定行
 * @method void setCsvControl (string $delimiter = ",", string $enclosure = "\"", string $escape = "\\") : 设置CSV的分隔符、封闭符和转义符
 * @method void setFlags ( int $flags ) : 设置SplFileObject的标志
 * @method void setMaxLineLen ( int $max_len ) : 设置文件读取一行的最大字节数，若文件每行有10个字符，但设置最大读取为
 * @method bool valid () : 检查是否到达文件底部，未到达底部返回 TRUE ，抵达返回false
 * @method string __toString  () : @SplFileObject::fgets()的别名
 */
class FileObject extends SplFileObject
{
    use SplTrait;
    /**
     * 文件操作对象
     * @var null|static
     */
    protected static $file = null;
    /**
     * 文件路径
     * @var string
     */
    protected $path;
    protected function __construct(string $filename, string $open_mode = "rb", bool $use_include_path = false, $context = null)
    {
        if (!is_dir(\dirname($filename)) && strpos($filename, '://') === false) {
            Util::mkPath(\dirname($filename));
        }
        try {
            parent::__construct($filename, $open_mode, $use_include_path, $context);
        } catch (Throwable $e) {
            throw new SystemException($e);
        }
        $this->path = $this->getPathname();
    }
    /**
     * 创建普通FileObject对象，大文件操作对象（同php/fopen函数）
     * @param string   $filename         文件路径
     * @param string   $open_mode        访问类型
     * @param bool     $use_include_path 如果也需要在 include_path 中检索文件的话，可以将该参数设为 true
     * @param resource $context          文件句柄的环境，context是可以修改流的行为的一套选项
     * @FQA 文件可能通过下列模式来打开：
     * @FQA r : 只读。指针定位在文件的开头，如果文件不存在会报错。
     * @FQA r+: 读/写。指针定位在文件的开头,如果文件不存在会报错。
     * @FQA w : 只写。打开并清空文件的内容，如果文件不存在，则创建新文件。
     * @FQA w+: 读/写。打开并清空文件的内容，如果文件不存在，则创建新文件。
     * @FQA a : 追加。打开并将指针定位在文件尾，如果文件不存在，则创建新文件。
     * @FQA a+ : 读/追加。打开并将指针定位在文件尾，如果文件不存在，则创建新文件。
     * @FQA x : 只写。创建新文件。如果文件存在，则返回 FALSE。
     * @FQA x+ : 读/写。创建新文件。如果文件已存在，则返回 FALSE 和一个错误。
     * @return FileObject
     * @throws SystemException
     */
    public static function make(string $filename, string $open_mode = "rb", bool $use_include_path = false, $context = null) : FileObject
    {
        if (is_null(self::$file)) {
            self::$file = self::setClass(new self($filename, $open_mode, $use_include_path, $context));
        }
        return self::$file;
    }
    /**
     * 返回文件路径
     * @return string 文件路径
     */
    public function path()
    {
        return $this->path;
    }
    /**
     * 返回文件总行数
     * @return int 文件总行数
     */
    public function line() : int
    {
        $length = 0;
        $file = $this->openFile('rb');
        foreach ($file as $k => $line) {
            $length = $k + 1;
        }
        return $length;
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
            if ($this->has()) {
                $content = $this->get();
            }
            $this->content = $content;
        }
        $ext = $this->getExtension();
        return $ext !== 'php' ? $this->content : htmlspecialchars($this->content, ENT_QUOTES, 'UTF-8');
    }
    /**
     * 写文件
     * @param $content
     * @return bool
     */
    protected function writeFile($content) : bool
    {
        return (bool) file_put_contents($this->path, $content);
    }
    /**
     * 读取文件内容
     * @return string
     */
    protected function readFile() : string
    {
        $content = parent::__toString();
        if ($this->isReadable()) {
            $file = $this->openFile('rb');
            $data = [];
            foreach ($file as $k => $line) {
                $data[] = $line;
            }
            $content = implode('', $data);
        }
        return $content;
    }
    /**
     * 获取数据
     * @return array|null
     */
    protected function getRaw()
    {
        $content = $this->readFile();
        if ('' !== $content) {
            return ['content' => $content];
        }
    }
    /**
     * 判断数据是否存在
     * @access public
     * @return bool
     */
    public function has() : bool
    {
        return $this->getRaw() !== null;
    }
    /**
     * 读取
     * @access public
     * @return mixed
     */
    public function get()
    {
        $raw = $this->getRaw();
        return is_null($raw) ? "" : $raw['content'];
    }
    /**
     * 写入
     * @access public
     * @param mixed   $value  存储数据
     * @return bool
     */
    public function set($content) : bool
    {
        return $this->writeFile($content);
    }
}