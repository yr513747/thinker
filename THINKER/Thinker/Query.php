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
// [ PDO数据查询类 ]
// --------------------------------------------------------------------------
declare (strict_types = 1);

namespace Thinker;

use think\db\Query as BaseQuery;
use PDOStatement;
use think\helper\Str;
use think\Config;
class Query extends BaseQuery
{
	/**
     * 返回前一条记录或仅返回一条记录
     * @access public
     * @param mixed $data 查询数据
     * @return array
     * @throws Exception
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function getOne($data = null): array
    {
		$result = $this->find($data);

        // 数据处理
		is_object($result) && $result = $result->toArray();
        is_null($result) && $result = array();
      
        return $result;
    }
	/**
     * 返回当前的一条记录并把游标移向下一记录
     * @access public
     * @param mixed $data 数据
     * @return array
     * @throws Exception
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function getArray($data = null): array
    {
        $result = $this->select($data);
		// 数据处理
		is_object($result) && $result = $result->toArray();
		
        return $result;
    }
	/**
     * @param mixed $data 数据
     * @return array
     * @throws Collection
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function getObject($data = null): \think\Collection
    {
        return $this->select($data);
    }
	/**
     * 得到当前或者指定名称的数据表
     * @access public
     * @param string $name 不含前缀的数据表名字
     * @return mixed
     */
    public function getTable(string $name = '')
    {
        $name = $this->parseSqlTable($name);
        return parent::getTable($name);
    }
	/**
     * 将SQL语句中的__TABLE_NAME__字符串替换成带前缀的表名（小写），兼容旧版
     * @access public
     * @param string $sql sql语句
     * @return string
     */
    public function parseSqlTable($sql)
    {
        if (false !== strpos($sql, '__')) {
            $sql    = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function ($match){
                return strtolower($match[1]);
            }, $sql);
        }
        return $sql;
    }
	/**
     * 指定查询数量,用于兼容某些错误的数据类型
     * @access public
     * @param int $offset 起始位置
     * @param int $length 查询数量
     * @return $this
     */
    public function limit($offset, $length = null)
    {
		$offset = intval($offset);
        if (!empty($length)) {
            $length = intval($length);
        }   
        return parent::limit($offset, $length);
    }
 
	/**
     * 指定group查询
     * @access public
     * @param string|array $group GROUP
     * @return $this
     */
    public function group($group)
    {
		/*设置sql_mode为宽松模式，避免分组查询遇到问题 ONLY_FULL_GROUP_BY*/
        $system_sql_mode = config('database.system_sql_mode', null);
        if (stristr($system_sql_mode, 'ONLY_FULL_GROUP_BY')) {
            $system_sql_mode = str_replace('ONLY_FULL_GROUP_BY', '', $system_sql_mode);
            $system_sql_mode = str_replace(',,', ',', trim($system_sql_mode, ','));
            $this->execute("SET sql_mode ='{$system_sql_mode}'");
        }
		
        return parent::group($group);
    }
	
	/**
     * 获取数据表信息
     * @access public
     * @param mixed  $tableName 数据表名 留空自动获取
     * @param string $fetch     获取信息类型 包括 fields type bind pk
     * @return mixed
     */
    public function getTableInfo($tableName, $fetch = '')
    {
		if (!$tableName) {
            $tableName = $this->getTable();
        }
		if (is_array($tableName)) {
            $tableName = key($tableName) ?: current($tableName);
        }

        if (strpos($tableName, ',') || strpos($tableName, ')')) {
            // 多表不获取字段信息
            return [];
        }
		return $this->connection->getTableInfo($tableName, $fetch);
    }
	
	/**
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     * @access public
     * @param string|array $field 字段名
     * @param mixed        $value 字段值
     * @return integer
     */
    public function setField($field, $value = '')
    {
        if (is_array($field)) {
            $data = $field;
        } else {
            $data[$field] = $value;
        }
        return $this->update($data);
    }
	
	/**
     * 插入记录
     * @access public
     * @param array   $data         数据
     * @param boolean $getLastInsID 返回自增主键
     * @return integer|string
     */
    public function insert(array $data = [], bool $getLastInsID = false)
    {
		// 设置允许写入的字段 
		$data = $this->allowField($data);
        
        return parent::insert($data, $getLastInsID);
    }    

    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @return integer|string
     * @throws Exception
     * @throws PDOException
     */
    public function update(array $data = []): int
    {
		// 设置允许写入的字段 
        $data = $this->allowField($data); 
		
		return parent::update($data);

    }
    
    /**
     * 兼容TP3.2里面的getField
     * 获取一条记录的某个字段值
     * @access public
     * @param string $field  字段名
     * @param string $spea  字段数据间隔符号 NULL返回数组
     * @return mixed
     */
    public function getField($field,$sepa=null) {
        if($sepa == true)
        {                        
            return $this->column($field);
        }elseif(strstr($field,','))
        {
            $field2 = explode(',', $field);
            $f = array_shift($field2);             
            return $this->column($field,$f); 
        }else
        {
            return $this->value($field);
        }
    }
    
    /**
     * 兼容TP3.2里面的add 方法
     * @access public
     * @param array     $data 数据          
     * @return $this->insert($data);
     */
    public function add($data = [])
    { 
       $this->insert($data);      
       return $this->getLastInsID();
    } 
    
    /**
     * 兼容TP3.2里面的allowField 方法  
     * 设置允许写入的字段
     * @access public   
     * @return $data
     */
    public function allowField($data)
    {        
        $field = $this->getTableInfo('', 'fields');
        // 检测字段
        if (!empty($field)) {
            foreach ($data as $key => $val) {
                if (!in_array($key, $field)) {
                    unset($data[$key]);
                }
            }
        }                                   
        return $data;
    }

    /**
     * 以指定字段值为键名形式返回结果集
     * @access public
     * @param string $index_key
     * @param array|string|Query|\Closure $data
     * @return Collection|false|\PDOStatement|string
     */
    public function getAllWithIndex($index_key = '', $data = null)
    {
        $result = $this->select($data)->toArray();
        if (false === $data) {
            return $result;
        }

        if (0 < count($result) && !empty($index_key)) {
            $rtn = array();
            foreach ($result as $_k => $_v) {
                $rtn[$_v[$index_key]] = $_v;
            }
            $result = $rtn;
        }

        return $result;
    }

    /**
     * 执行查询，以指定字段值为键名返回数据集
     * @access public
     * @param string      $sql    sql指令
     * @param string      $index_key 指定键名的字段
     * @param array       $bind   参数绑定
     * @return mixed
     */
    public function getSqlWithIndex(string $sql, $index_key = '', array $bind = [])
    {
        $result = $this->query($sql, $bind);

        if (0 < count($result) && !empty($index_key)) {
            $rtn = array();
            foreach ($result as $_k => $_v) {
                $rtn[$_v[$index_key]] = $_v;
            }
            $result = $rtn;
        }

        return $result;
    }

    /**
     * 拼接更新字段
     * @param unknown $data 批量更新的数组
     * @param string $index_key 主键值的字段名
     * @return string
     */
    public function _concatFields($data, $index_key)
    {
        if (empty($data)) {
            return '';
        }
        $array_tmp = array();
        $index_key_array = array();
        if (!is_array(current($data))) {
            $data = array($data);
        }
        foreach (current($data) as $_v => $_k) {
            if ($_v != $index_key) {
                if (!isset(${$_v . '_temp'})) ${$_v . '_temp'} = "";
                ${$_v . '_temp'} .= " {$_v} = CASE {$index_key} ";
            }
        }
        reset($data);
        foreach ($data as $_k => $_v) {
            foreach ($_v as $_f => $_fv) {
                if (!isset(${$_f . '_temp'})) ${$_f . '_temp'} = "";
                ${$_f . '_temp'} .= "WHEN '{$_v[$index_key]}' THEN '" . addslashes($_fv) . "' ";
                array_push($index_key_array, $_v[$index_key]);
            }
        }
        reset($data);
        foreach (current($data) as $_v => $_k) {
            if ($_v != $index_key) {
                if (!isset(${$_v . '_temp'})) ${$_v . '_temp'} = "";
                ${$_v . '_temp'} .= " END ";
                $array_tmp[$_v . '_temp'] = ${$_v . '_temp'};
            }
        }

        $array_tmp[$index_key] = db_create_in($index_key_array, $index_key);
        return $array_tmp;
    }

    /**
     * 获取更新的数据SQL
     * @param unknown $data 批量更新的数组
     * @param string $index_key 主键值的字段名
     * @return multitype:
     */
    public function _getUpdateInfo($data, $index_key)
    {
        reset($data);
        $fields = array();
        $conditions = array();
        $fields_info = $this->_concatFields($data, $index_key);
        $conditions = $fields_info[$index_key];
        unset($fields_info[$index_key]);
        $fields = implode(',', $fields_info);
        return compact('fields', 'conditions');
    }

}
if (!function_exists('db_create_in')) 
{
    /**
     * 创建像这样的查询: "IN('a','b')";
     *
     * @param    mixed      $item_list      列表数组或字符串,如果为字符串时,字符串只接受数字串
     * @param    string   $field_name     字段名称
     * @return   string
     */
    function db_create_in($item_list, $field_name = '')
    {
        if (empty($item_list))
        {
            return $field_name . " IN ('') ";
        }
        else
        {
            if (!is_array($item_list))
            {
                $item_list = explode(',', $item_list);
                foreach ($item_list as $k=>$v)
                {
                    $item_list[$k] = intval($v);
                }
            }

            $item_list = array_unique($item_list);
            $item_list_tmp = '';
            foreach ($item_list AS $item)
            {
                if ($item !== '')
                {
                    $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
                }
            }
            if (empty($item_list_tmp))
            {
                return $field_name . " IN ('') ";
            }
            else
            {
                return $field_name . ' IN (' . $item_list_tmp . ') ';
            }
        }
    }
}
