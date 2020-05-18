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

use think\exception\HttpResponseException;
/**
 * 静态文件缓存类
 */
class HtmlCache
{
    /**
     * 缓存参数配置
     * @var array
     */
    protected $options = [
        // 缓存开关
        'switch' => true,
        // 缓存保存目录
        'path' => '',
        // 是否使用二级目录
        'cache_subdir' => true,
        // 命名方式
        'hash_type' => 'md5',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        // 是否启用字符压缩
        'data_compress' => true,
        // 序列化机制 例如 ['serialize', 'unserialize']
        'serialize' => [],
    ];
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        $options = config('htmlcache', array());
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (empty($this->options['path'])) {
            $this->options['path'] = runtime_path('html');
        }
        if (substr($this->options['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->options['path'] .= DIRECTORY_SEPARATOR;
        }
		if (!$this->options['switch']) {
            $this->clear();
        }
    }
    /**
     * 获取有效期
     * @access protected
     * @param integer|\DateTimeInterface|\DateInterval $expire 有效期
     * @return int
     */
    protected function getExpireTime($expire) : int
    {
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        } elseif ($expire instanceof \DateInterval) {
            $expire = \DateTime::createFromFormat('U', (string) time())->add($expire)->format('U') - time();
        }
        return (int) $expire;
    }
    /**
     * 取得变量的存储文件名
     * @access public
     * @param string $name 缓存变量名
     * @return string
     */
    public function getCacheKey(string $name) : string
    {
        $name = hash($this->options['hash_type'], $name);
        if ($this->options['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }
        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DIRECTORY_SEPARATOR . $name;
        }
        return $this->options['path'] . $name . '.php';
    }
    /**
     * 获取缓存数据
     * @param string $name 缓存标识名
     * @return array|null
     */
    protected function getRaw(string $name)
    {
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) {
            return;
        }
        $content = Util::getFile($filename);
        if ('' !== $content) {
            $expire = (int) substr($content, 8, 12);
            if (0 != $expire && time() - $expire > filemtime($filename)) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return;
            }
            $content = substr($content, 32);
            if ($this->options['data_compress']) {
                //启用数据压缩
                $content = $this->decompression($content);
            }
            return ['content' => $content, 'expire' => $expire];
        }
    }
    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name) : bool
    {
        return $this->getRaw($name) !== null;
    }
    /**
     * 读取缓存
     * @access public
     * @param string $name    缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $raw = $this->getRaw($name);
        return is_null($raw) ? $default : $this->unserialize($raw['content']);
    }
    /**
     * 写入缓存
     * @access public
     * @param string        $name   缓存变量名
     * @param mixed         $value  存储数据
     * @param int|\DateTime $expire 有效时间 0为永久
     * @return bool
     */
    public function set($name, $value, $expire = null) : bool
    {
        if (!$this->options['switch']) {
            return false;
        }
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $expire = $this->getExpireTime($expire);
        $filename = $this->getCacheKey($name);
        
        $data = $this->serialize($value);
        if ($this->options['data_compress']) {
            //数据压缩
            $data = $this->compress($data, 3);
        }
        $data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
                
        return Util::putFile($filename, $data);
    }
    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function delete($name) : bool
    {
        return $this->unlink($this->getCacheKey($name));
    }
    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public function clear() : bool
    {
        $dirname = $this->options['path'] . $this->options['prefix'];
        $this->rmdir($dirname);
        return true;
    }
    /**
     * 序列化数据
     * @access protected
     * @param mixed $data 缓存数据
     * @return string|null
     */
    protected function serialize($data)
    {
        if (is_bool($data)) {
            return;
        }
        if (is_numeric($data)) {
            return (string) $data;
        }
        $serialize = $this->options['serialize'][0] ?? "serialize";
        return $serialize($data);
    }
    /**
     * 反序列化数据
     * @access protected
     * @param string $data 缓存数据
     * @return mixed
     */
    protected function unserialize($data)
    {
        if (is_bool($data)) {
            return;
        }
        if (is_numeric($data)) {
            return $data;
        }
        $unserialize = $this->options['serialize'][1] ?? "unserialize";
        return $unserialize($data);
    }
    /**
     * 压缩数据
     * @access protected
     * @param mixed $data 数据
     * @return string
     */
    protected function compress(string $data, int $level = 3)
    {
        try {
            if ($this->options['data_compress']) {
                if (function_exists('gzdeflate')) {
                    $data = gzdeflate($data, $level);
                } elseif (function_exists('gzcompress')) {
                    $data = gzcompress($data, $level);
                } elseif (function_exists('gzencode')) {
                    $data = gzencode($data, $level);
                }
            }
        } catch (\Exception $e) {
            // 数据错误
            return false;
        }
        return $data;
    }
    /**
     * 解压压缩数据
     * @access protected
     * @param string $data 压缩数据
     * @return string
     */
    protected function decompression(string $data)
    {
        try {
            if ($this->options['data_compress']) {
                if (function_exists('gzdeflate')) {
                    $data = gzinflate($data);
                } elseif (function_exists('gzcompress')) {
                    $data = gzuncompress($data);
                } elseif (function_exists('gzencode')) {
                    $data = gzdecode($data);
                }
            }
        } catch (\Exception $e) {
            // 数据错误
            return false;
        }
        return $data;
    }
    /**
     * 判断文件是否存在后，删除
     * @access private
     * @param string $path
     * @return bool
     */
    private function unlink(string $path) : bool
    {
        try {
            return is_file($path) && unlink($path);
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * 删除文件夹
     * @param $dirname
     * @return bool
     */
    private function rmdir($dirname)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $items = new \FilesystemIterator($dirname);
        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->rmdir($item->getPathname());
            } else {
                $this->unlink($item->getPathname());
            }
        }
        @rmdir($dirname);
		is_dir(dirname($dirname)) && @rmdir(dirname($dirname));
        return true;
    }
    /**
     * 发送数据到客户端 
     * @access public
     * @param string  $name 缓存变量名
     * @param bool    $exit 是否中断输出
     * @param integer $code    状态码
     * @param array   $header 头部
     * @param array   $options 参数
     * @return mixed
     * @throws HttpResponseException
     */
    public function send($name, bool $exit = true, int $code = 200, array $header = [], array $options = [])
    {
        $data = $this->get($name);
        if (is_null($data)) {
            return false;
        } elseif (is_string($data)) {
            $response = Response::create($data, 'html', $code)->header($header)->options($options);
        } elseif (is_array($data)) {
            $response = Response::create($data, 'json', $code)->header($header)->options($options);
        } else {
            $response = Response::create()->code(304);
        }
        if ($exit === false) {
            return $response->send();
        }
        throw new HttpResponseException($response);
    }
}