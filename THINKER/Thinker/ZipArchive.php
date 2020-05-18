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
// [ Zip 文件包工具 ]
// --------------------------------------------------------------------------
namespace Thinker;

use ReflectionClass;
if (class_exists('\\ZipArchive') and class_exists('\\DirectoryIterator')) {
    class ZipArchive extends \ZipArchive
    {
		protected $app;
        protected $root;
        protected $ignored_names;
        public function iterateVisible()
        {
            $r = new ReflectionClass($this);
            return [$r->getConstants(), $r->getProperties(), $r->getMethods()];
        }
        /**
         * 构造方法
         * @access public
         * @param  App  $app  应用对象
         */
        public function __construct($app = null)
        {
            $this->app = $app;
        }
        /**
         * 解压zip文件到指定文件夹
         * @access public
         * @param string $zipfile 压缩文件路径
         * @param string $path  压缩包解压到的目标路径
         * @return booleam 解压成功返回 true 否则返回 false
         */
        public function unzip($zipfile, $path)
        {
            if ($this->open($zipfile) === true) {
                $file_tmp = @fopen($zipfile, "rb");
                $bin = fread($file_tmp, 15);
                //只读15字节 各个不同文件类型，头信息不一样。
                fclose($file_tmp);
                /* 只针对zip的压缩包进行处理 */
                //if (true === $this->getTypeList($bin)) {
                    $result = $this->extractTo($path);
                    $this->close();
                    return $result;
                //} else {
                //    return false;
                //}
            }
            return false;
        }
        /**
         * 创建压缩文件
         * @access public
         * @param string $zipfile 将要生成的压缩文件路径
         * @param strng $folder 将要被压缩的文件夹路径
         * @param array $ignored 要忽略的文件列表
         * @return booleam 压缩包生成成功返回true 否则返回 false
         */
        public function zip($zipfile, $folder, $ignored = null)
        {
            $this->ignored_names = is_array($ignored) ? $ignored : $ignored ? array($ignored) : array();
            if ($this->open($zipfile, ZIPARCHIVE::CREATE) !== true) {
                throw new \Exception("cannot open <{$zipfile}>\n");
            }
            $folder = substr($folder, -1) == '/' ? substr($folder, 0, strlen($folder) - 1) : $folder;
            if (strstr($folder, '/')) {
                $this->root = substr($folder, 0, strrpos($folder, '/') + 1);
                $folder = substr($folder, strrpos($folder, '/') + 1);
            }
            $this->createZip($folder);
            return $this->close();
        }
        /**
         * 递归添加文件到压缩包
         * @access private
         * @param string $folder 添加到压缩包的文件夹路径
         * @param string $parent 添加到压缩包的文件夹上级路径
         * @return void
         */
        private function createZip($folder, $parent = null)
        {
            $full_path = $this->root . $parent . $folder;
            $zip_path = $parent . $folder;
            $this->addEmptyDir($zip_path);
            $dir = new \DirectoryIterator($full_path);
            foreach ($dir as $file) {
                if (!$file->isDot()) {
                    $filename = $file->getFilename();
                    if (!in_array($filename, $this->ignored_names)) {
                        if ($file->isDir()) {
                            $this->createZip($filename, $zip_path . '/');
                        } else {
                            $this->addFile($full_path . '/' . $filename, $zip_path . '/' . $filename);
                        }
                    }
                }
            }
        }
        /**
         * 读取压缩包文件与目录列表
         * @access public
         * @param string $zipfile 压缩包文件
         * @return array 文件与目录列表
         */
        public function fileList($zipfile)
        {
            $file_dir_list = array();
            $file_list = array();
            if ($this->open($zipfile) == true) {
                for ($i = 0; $i < $this->numFiles; $i++) {
                    $numfiles = $this->getNameIndex($i);
                    if (preg_match('/\\/$/i', $numfiles)) {
                        $file_dir_list[] = $numfiles;
                    } else {
                        $file_list[] = $numfiles;
                    }
                }
            }
            return array('files' => $file_list, 'dirs' => $file_dir_list);
        }
        /**
         * 得到文件头与文件类型映射表
         * @param $bin string 文件的二进制前一段字符
         * @return boolean
         */
        private function getTypeList($bin)
        {
            $array = array(array("504B0304", "zip"));
            foreach ($array as $v) {
                $blen = strlen(pack("H*", $v[0]));
                //得到文件头标记字节数
                $tbin = substr($bin, 0, intval($blen));
                ///需要比较文件头长度
                if (strtolower($v[0]) == strtolower(array_shift(unpack("H*", $tbin)))) {
                    return true;
                }
            }
            return false;
        }
    }
} else {
    echo "Please install zip extension for PHP\n";
}