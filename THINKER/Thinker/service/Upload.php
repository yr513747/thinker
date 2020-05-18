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
// [ 上传类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\service;

use Thinker\db\Base;

/**
 * 上传类.
 *
 * @property string Name
 * @property string FullFile
 * @property string Size
 * @property string Dir
 * @property int PostTime
 * @property int|string AuthorID
 * @property string SourceName
 * @property string MimeType
 * @property Member Author
 */
class Upload extends Base
{
    public function __construct()
    {
        parent::__construct('upload');

        $this->PostTime = getTime();
    }

    /**
     * @param string $extList
     *
     * @return bool
     */
    public function checkExtName($extList = '')
    {
        global $zbp;
        $e = $this->getFileExt($this->Name);
        $extList = strtolower($extList);
        // 无论如何，禁止.php、.php5之类的文件的上传
        if (preg_match('/php/i', $e)) {
            return false;
        }
        if (trim($extList) == '') {
            $extList = $zbp->option['ZC_UPLOAD_FILETYPE'];
        }

        return $this->hasNameInString($extList, $e);
    }

    /**
     * @return bool
     */
    public function checkSize()
    {
        global $zbp;
        $n = 1024 * 1024 * (int) $zbp->option['ZC_UPLOAD_FILESIZE'];

        return $n >= $this->Size;
    }

    /**
     * @return bool
     */
    public function delFile()
    {
        if (file_exists($this->FullFile)) {
            return @unlink($this->FullFile);
        }

        return false;
    }

    /**
     * @param $tmp
     *
     * @return bool
     */
    public function saveFile($tmp)
    {
        if (!file_exists(root_path('public') . $this->Dir)) {
            @mkdir(root_path('public') . $this->Dir, 0755, true);
        }
        if (PHP_SYSTEM === "WINDOWS") {
            $fn = iconv("UTF-8", "GBK//IGNORE", $this->Name);
        } else {
            $fn = $this->Name;
        }
        return @move_uploaded_file($tmp, root_path('public') . $this->Dir . $fn);
    }

    /**
     * @param $str64
     *
     * @return bool
     */
    public function saveBase64File($str64)
    {
        if (!file_exists(root_path('public') . $this->Dir)) {
            @mkdir(root_path('public') . $this->Dir, 0755, true);
        }
        $s = base64_decode($str64);
        $this->Size = strlen($s);
        if (PHP_SYSTEM === "WINDOWS") {
            $fn = iconv("UTF-8", "GBK//IGNORE", $this->Name);
        } else {
            $fn = $this->Name;
        }
        return @file_put_contents(root_path('public') . $this->Dir . $fn, $s);
    }

    /**
     * @param string $s
     *
     * @return bool|string
     */
    public function time($s = 'Y-m-d H:i:s')
    {
        return date($s, $this->PostTime);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (!in_array($name, array('Url', 'Dir', 'FullFile', 'Author'))) {
            parent::__set($name, $value);
        }
    }

    /**
     * @param $name
     *
     * @return Member|mixed|string
     */
    public function __get($name)
    {
        global $zbp;
        if ($name == 'Url') {
            return $zbp->host . '/' . $this->Dir . rawurlencode($this->Name);
        }
        if ($name == 'Dir') {
            return 'upload/' . date('Y', $this->PostTime) . '/' . date('m', $this->PostTime) . '/';
        }
        if ($name == 'FullFile') {
            return root_path('public') . $this->Dir . $this->Name;
        }
        if ($name == 'Author') {
            return $zbp->GetMemberByID($this->AuthorID);
        }

        return parent::__get($name);
    }
	
	/**
     * 获取文件后缀名.
     * @param string $f 文件名
     * @return string 返回小写的后缀名
     */
    protected function getFileExt($f)
    {
        if (strpos($f, '.') === false) {
            return '';
        }

        $a = explode('.', $f);

        return strtolower(end($a));
    }
	
	/**
     * 在字符串参数值查找参数.
     * @param string $s    字符串型的参数表，以|符号分隔
     * @param string $name 参数名
     * @return bool
     */
    protected function hasNameInString($s, $name)
    {
        $pl = $s;
        $name = (string) $name;
        $apl = explode('|', $pl);

        return in_array($name, $apl);
    }
}
