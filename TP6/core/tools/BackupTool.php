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
// [ 数据备份还原类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace core\tools;

use think\App;
use think\traits\think\InstanceTrait;
use think\facade\Db;
use think\exception\AuthException;
use FilesystemIterator;

class BackupTool
{
	use InstanceTrait;
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 数据库实例
     * @var \think\db\Query 
     */
    protected $db;
    /**
     * 文件指针
     * @var resource
     */
    protected $fp;
    /**
     * 备份文件信息 part - 卷号，name - 文件名
     * @var array
     */
    protected $file;
    /**
     * 当前打开文件大小
     * @var integer
     */
    protected $size = 0;
    /**
     * 数据库配置
     * @var array
     */
    protected $dbconfig = array();
    /**
     * 备份配置
     * @var array
     */
    protected $config = array(
        // 数据库备份路径
        'path' => './data/',
        // 数据库备份卷大小
        'part' => 20971520,
        // 数据库备份文件是否启用压缩 0不压缩 1 压缩
        'compress' => 0,
        // 压缩级别
        'level' => 9,
    );
    private $errorMsg;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @param  array $config  配置参数
     */
    public function __construct(App $app, array $config = [])
    {
        $this->app = $app;
        $this->config = array_merge($this->config, $config);
        //初始化文件名
        $this->setFile();
        // 初始化数据库操作对象
        $db = $this->connect();
        //初始化数据库连接参数
        $baseconfig = $db->getConfig();
        $dbconfig = isset($baseconfig['default']) ? $baseconfig['connections'][$baseconfig['default']] : $baseconfig;
        $this->setDbConfig($dbconfig);
        //检查文件是否可写
        if (!$this->checkDirBuild($this->config['path'])) {
            throw new AuthException("The current directory is not writable");
        }
    }
    /**
     * 设置脚本运行超时时间, 0表示不限制，支持连贯操作
     * @access public
     * @param  integer  $time
     * @return $this
     */
    public function setTimeout($time = null)
    {
        if (!is_null($time)) {
            set_time_limit($time) || ini_set("max_execution_time", $time);
        }
        return $this;
    }
    /**
     * 设置数据库连接必备参数
     * @access public
     * @param array  $dbconfig   数据库连接配置信息
     * @return $this 
     */
    public function setDbConfig($dbconfig = [])
    {
        $this->dbconfig = array_merge($this->dbconfig, $dbconfig);
        return $this;
    }
    /**
     * 设置备份文件名
     * @access public
     * @param array  $file  文件配置
     * @return $this  
     */
    public function setFile($file = null)
    {
        if (is_null($file)) {
            $this->file = ['name' => date('Ymd-His'), 'part' => 1, 'version' => config('base.base.cfg_soft_version', 'v1.0.0')];
        } else {
            if (!array_key_exists("name", $file) && !array_key_exists("part", $file)) {
                $this->file = $file['1'];
            } else {
                $this->file = $file;
            }
        }
        return $this;
    }
    /**
     * 创建数据连接
     * @access public
     * @return think\db\Query  
     */
    public function connect()
    {
		if (is_null($this->db)) {
            $this->db = Db::connect();
        }    
        return $this->db;
    }
    /**
     * 读取数据库表列表
     * @access public
     * @param string  $table  表名
     * @param boolean  $type  类型
     * @return array  
     */
    public function tableList($table = null, $type = true)
    {
        $db = $this->connect();
        if (is_null($table)) {
            $list = $db->query("SHOW TABLE STATUS");
        } else {
            if ($type) {
                $list = $db->query("SHOW FULL COLUMNS FROM {$table}");
            } else {
                $list = $db->query("show columns from {$table}");
            }
        }
        return array_map('array_change_key_case', $list);
    }
    /**
     * 读取数据库备份文件列表
     * @access public
     * @return array  
     */
    public function fileList()
    {
        if (!$this->checkDirBuild($this->config['path'])) {
            $this->setErrorInfo("The current directory is not writable");
            return false;
        }
        $path = realpath($this->config['path']);
        $flag = FilesystemIterator::KEY_AS_FILENAME;
        $glob = new FilesystemIterator($path, $flag);
        $list = array();
        foreach ($glob as $name => $file) {
            if (preg_match('/^\\d{8,8}-\\d{6,6}-\\d+\\.sql(?:\\.gz)?$/', $name)) {
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];
                if (isset($list["{$date} {$time}"])) {
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info['compress'] = $extension === 'SQL' ? '-' : $extension;
                $info['time'] = strtotime("{$date} {$time}");
                $list["{$date} {$time}"] = $info;
            }
        }
        return $list;
    }
    /**
     * 读取数据库备份文件列表
     * @access public
     * @param string  $type  
	 * @param integer $time
     * @return mixed  
     */
    public function getFile($type = '', $time = 0)
    {
		if (!is_numeric($time)) {
			$this->setErrorInfo("{$time} Illegal data type");
            return false;
        }
        switch ($type) {
            case 'time':
			    $name = date('Ymd-His', $time) . '-*.sql*';
                $path = realpath($this->config['path']) . DIRECTORY_SEPARATOR . $name;
                return glob($path);
                break;
            case 'timeverif':
			    $name = date('Ymd-His', $time) . '-*.sql*';
                $path = realpath($this->config['path']) . DIRECTORY_SEPARATOR . $name;
                $files = glob($path);
                $list = array();
                foreach ($files as $name) {
                    $basename = basename($name);
                    $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                    $gz = preg_match('/^\\d{8,8}-\\d{6,6}-\\d+\\.sql.gz$/', $basename);
                    $list[$match[6]] = array($match[6], $name, $gz);
                }
                $last = end($list);
                if (count($list) === $last[0]) {
                    return $list;
                } else {
                    $this->setErrorInfo("File {$files['0']} may be damaged, please check again");
                    return false;
                }
                break;
            case 'pathname':
                return "{$this->config['path']}{$this->file['name']}-{$this->file['part']}.sql";
                break;
            case 'filename':
                return "{$this->file['name']}-{$this->file['part']}.sql";
                break;
            case 'filepath':
                return $this->config['path'];
                break;
            default:
                $arr = array('pathname' => "{$this->config['path']}{$this->file['name']}-{$this->file['part']}.sql", 'filename' => "{$this->file['name']}-{$this->file['part']}.sql", 'filepath' => $this->config['path'], 'file' => $this->file);
                return $arr;
        }
    }
    /**
     * 删除备份文件
     * @access public
     * @param integer $time
     * @return boolean  
     */
    public function delFile($time)
    {
		$file = $this->getFile('time', $time);
        if ($file !== false && $time) {
            array_map("unlink", $this->getFile('time', $time));
            if (count($this->getFile('time', $time))) {
                $this->setErrorInfo("File {$path} deleted failed");
                return false;
            } else {
                return true;
            }
        } else {
            $this->setErrorInfo("{$time} Time parameter is incorrect");
            return false;
        }
    }
    /**
     * 下载备份
     * @access public
     * @param integer $time
     * @param integer $part
     * @return mixed
     */
    public function downloadFile($time, int $part = 0)
    {
        $file = $this->getFile('time', $time);
        if ($file !== false && isset($file[$part]) && file_exists($fileName = $file[$part])) {
			return download($fileName, basename($fileName));
        } else {
            $this->setErrorInfo("{$time} File is abnormal");
            return null;
        }
    }
    /**
     * 还原数据
     * @access public
     * @param  integer $start 起始行数
     * @return mixed
     */
    public function import(int $start)
    {
        $db = $this->connect();
        if ($this->config['compress']) {
            $gz = gzopen($this->file[1], 'r');
            $size = 0;
        } else {
            $size = filesize($this->file[1]);
            $gz = fopen($this->file[1], 'r');
        }
        $sql = '';
        if ($start) {
            $this->config['compress'] ? gzseek($gz, $start) : fseek($gz, $start);
        }
        for ($i = 0; $i < 1000; $i++) {
            $sql .= $this->config['compress'] ? gzgets($gz) : fgets($gz);
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== $db->execute($sql)) {
                    $start += strlen($sql);
                } else {
                    return false;
                }
                $sql = '';
            } elseif ($this->config['compress'] ? gzeof($gz) : feof($gz)) {
                return 0;
            }
        }
        return array($start, $size);
    }
    /**
     * 写入初始数据
     * @access public
     * @return boolean 
     */
    public function backupInit()
    {
        $mysqlinfo = $this->connect()->query("SELECT VERSION() as version");
        $mysql_version  = $mysqlinfo[0]['version'];

        $sql  = "-- ----------------------------------------\n";
        $sql .= "-- Think MySQL Data Transfer \n";
        $sql .= "-- \n";
        $sql .= "-- Server         : " . $this->dbconfig['hostname'].'_'.$this->dbconfig['hostport'] . "\n";
        $sql .= "-- Server Version : " . $mysql_version . "\n";
        $sql .= "-- Host           : " . $this->dbconfig['hostname'].':'.$this->dbconfig['hostport'] . "\n";
        $sql .= "-- Database       : " . $this->dbconfig['database'] . "\n";
        $sql .= "-- \n";
        $sql .= "-- Part : #{$this->file['part']}\n";
        $sql .= "-- Version : #{$this->file['version']}\n";
        $sql .= "-- Date : " . date("Y-m-d H:i:s") . "\n";
        $sql .= "-- -----------------------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
		
        return $this->write($sql);
    }
    /**
     * 备份表结构
     * @access public
     * @param  string  $table 表名
     * @param  integer $start 起始行数
     * @return boolean   
     */
    public function backup(string $table, int $start)
    {
        $db = $this->connect();
        // 备份表结构
        if (0 == $start) {
            $result = $db->query("SHOW CREATE TABLE `{$table}`");
            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";        
            if(isset($result[0]['create table'])){
                $sql .= trim($result[0]['create table']) . ";\n\n";
            }
			if(isset($result[0]['Create Table'])){
                $sql .= trim($result[0]['Create Table']) . ";\n\n";
            }
			if(false === $this->write($sql)){
                return false;
            }
        }
        //数据总数
        $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
        $count = $result['0']['count'];
        //备份表数据
        if ($count) {
            //写入数据注释
            if (0 == $start) {
                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n";
                $this->write($sql);
            }
            //备份数据记录
            $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
            foreach ($result as $row) {
                $row = array_map('addslashes', $row);
                $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\\r', '\\n'), implode("', '", $row)) . "');\n";
                if (false === $this->write($sql)) {
                    return false;
                }
            }
            //还有更多数据
            if ($count > $start + 1000) {
                return $this->backup($table, $start + 1000);
            }
        }
        //备份下一表
        return 0;
    }
    /**
     * 优化表
     * @access public
     * @param  string|array $tables 表名
     * @return boolean  
     */
    public function optimize($tables = null)
    {
        if ($tables) {
            $db = $this->connect();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = $db->query("OPTIMIZE TABLE `{$tables}`");
            } else {
                $list = $db->query("OPTIMIZE TABLE `{$tables}`");
            }
            if ($list) {
                return true;
            } else {
                $this->setErrorInfo("data sheet'{$tables}'Repair mistakes please try again!");
                return false;
            }
        } else {
            $this->setErrorInfo("Please specify the table to be repaired!");
            return false;
        }
    }
    /**
     * 修复表
     * @access public
     * @param  string|array $tables 表名
     * @return boolean 
     */
    public function repair($tables = null)
    {
        if ($tables) {
            $db = $this->connect();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = $db->query("REPAIR TABLE `{$tables}`");
            } else {
                $list = $db->query("REPAIR TABLE `{$tables}`");
            }
            if ($list) {
                return true;
            } else {
                $this->setErrorInfo("data sheet'{$tables}'Repair mistakes please try again!");
                return false;
            }
        } else {
            $this->setErrorInfo("Please specify the table to be repaired!");
            return false;
        }
    }
    /**
     * 写入SQL语句
     * @access protected
     * @param  string $sql 要写入的SQL语句
     * @return boolean    
     */
    protected function write(string $sql)
    {
        $size = strlen($sql);
        //由于压缩原因，无法计算出压缩后的长度，这里假设压缩率为50%，
        //一般情况压缩率都会高于50%；
        $size = $this->config['compress'] ? $size / 2 : $size;
        $this->open($size);
        return $this->config['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }
    /**
     * 打开一个卷，用于写入数据
     * @access protected
     * @param  integer $size 写入数据的大小
     * @return void
     */
    protected function open($size)
    {
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config['part']) {
                $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->fp = null;
                $this->file['part']++;
                $this->setAttr('backup_file', $this->file);
                $this->backupInit();
            }
        } else {
            $filename = "{$this->config['path']}{$this->file['name']}-{$this->file['part']}.sql";
            if ($this->config['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = @gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = @fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }
    /**
     * 创建目录
     * @access protected
     * @param  string $dirname 目录名称
     * @return boolean
     */
    protected function checkDirBuild(string $dirname)
    {
        if (is_dir($dirname)) {
            return true;
        }
        if (mkdir($dirname, 0755, true)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 属性修改器 设置数据对象值
     * @access public
     * @param string|array $name  属性名
     * @param mixed  $value 属性值
     * @return $this
     */
    public function setAttr($name, $value = '')
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->{$key} = $value;
            }
        } else {
            $this->{$name} = $value;
        }
        return $this;
    }
	/**
     * 属性获取器
     * @access public
     * @param string $name  属性名
     * @return mixed
     */
    public function getAttr(string $name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        } else {
            return null;
        }     
    }
    /**
     * 设置错误信息
     * @access public
     * @param string $errorMsg
     * @return $this
     */
    public function setErrorInfo($errorMsg = '操作失败,请稍候再试!')
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }
    /**
     * 获取错误信息
     * @access public
     * @param string $defaultMsg
     * @return string
     */
    public function getErrorInfo($defaultMsg = '操作失败,请稍候再试!')
    {
        return !empty($this->errorMsg) ? $this->errorMsg : $defaultMsg;
    }
    /**
     * 析构方法，用于关闭文件资源
     * @access public
     */
    public function __destruct()
    {
        if ($this->fp) {
            $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
        }
    }
}