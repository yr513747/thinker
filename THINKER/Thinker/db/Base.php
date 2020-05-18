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
// [ 数据操作基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\db;

use think\facade\Db;

abstract class Base
{
    /**
     * @var string 数据表
     */
    protected $table = '';
	
    /**
     * @var array 表结构信息
     */
    protected $datainfo = array();
	
    /**
     * @var array 数据
     */
    protected $data = array();
	
    /**
     * @var Database__Interface db
     */
    protected $db = null;
	
    /**
     * @var string 类名
     */
    protected $classname = '';

    /**
	 * 架构函数 
	 * @access public
     * @param string $tablename  数据表不含前缀
     */
    public function __construct(string $tablename)
    {
		is_null($this->db) && $this->db = Db::name($tablename);

        $this->table = $this->db->getTable();
		
        $this->datainfo = $this->db->getFieldsType();

        if (function_exists('get_called_class')) {
            $this->classname = get_called_class();
        } else {
            $this->classname = get_class($this);
        }
		
    }

    /**
 	 * @access public
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
	 * @access public
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
	 * @access public
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
	 * @access public
     * @param $name
     */
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * 获取数据库数据(不设$key就返回整个data数组)
     * @access public
     * @return array
     */
    public function getData($key = null)
    {
        if (null == $key) {
            return $this->data;
        } else {
            return $this->data[$key];
        }
    }

    /**
     * 设置数据库数据
     * @access public
     * @param key 如果是array，就忽略$value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 获取数据表
     * @access public
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * 获取表结构
     * @access public
     * @return array
     */
    public function getDataInfo()
    {
        return $this->datainfo;
    }
	
	/**
     * 获取当前类名
     * @access public
     * @return array
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * 保存数据
     * @access public
     * @return mixed
     */
    public function save()
    {
		if (empty($this->data)) {
            return false;
        }
		
        return $this->db->insertGetId($this->data);
    }

    /**
     * 将数据用JSON格式输出
     * @access public
     * @return string
     */
    public function __toString()
    {
        return (string) json_encode($this->data);
    }
}
