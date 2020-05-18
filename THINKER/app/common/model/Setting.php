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
// [ 系统配置 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 系统非全局配置表
//
//字段	类型	空	默认	注释
//id	int(11)	否
//name	varchar(50)	是	 	配置的key键名
//value	text	是	NULL
//inc_type	varchar(64)	是	 	配置分组
//update_time	int(11)	是	0	更新时间
class Setting extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'setting';
    /**
     * 获取缓存或者更新缓存，只适用于setting表
     * @param string $config_key 缓存文件名称
     * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
     * @param array $options 缓存配置
     * @return array or string or bool
     */
    public static function tpSetting($config_key, $data = array(), $options = null)
    {
        $param = explode('.', $config_key);
        $cache_inc_type = 'setting' . $param[0];
        if (empty($data)) {
            //直接获取缓存文件
            $config = cache($cache_inc_type, '', $options);
            if (empty($config)) {
                //缓存文件不存在就读取数据库
                if ($param[0] == 'global') {
                    $param[0] = 'global';
                    $res = self::getArray();
                } else {
                    $res = self::where('inc_type', $param[0])->getArray();
                }
                if ($res) {
                    foreach ($res as $k => $val) {
                        $config[$val['name']] = $val['value'];
                    }
                    cache($cache_inc_type, $config, $options);
                }
            }
            if (!empty($param) && count($param) > 1) {
                $newKey = strtolower($param[1]);
                return isset($config[$newKey]) ? $config[$newKey] : '';
            } else {
                return $config;
            }
        } else {
            //更新缓存
            $result = self::where('inc_type', $param[0])->getArray();
            if ($result) {
                foreach ($result as $val) {
                    $temp[$val['name']] = $val['value'];
                }
                $add_data = array();
                foreach ($data as $k => $v) {
                    $newK = strtolower($k);
                    $newArr = array('name' => $newK, 'value' => trim($v), 'inc_type' => $param[0], 'update_time' => getTime());
                    if (!isset($temp[$newK])) {
						//新key数据插入数据库
                        array_push($add_data, $newArr);                       
                    } else {
                        if ($v != $temp[$newK]) {
							//缓存key存在且值有变更新此项
                            self::where('name', $newK)->save($newArr);
                        }
                    }
                }
                if (!empty($add_data)) {
                    self::insertAll($add_data);
                }
                //更新后的数据库记录
                $newRes = self::where('inc_type', $param[0])->getArray();
                foreach ($newRes as $rs) {
                    $newData[$rs['name']] = $rs['value'];
                }
            } else {
                if ($param[0] != 'global') {
                    foreach ($data as $k => $v) {
                        $newK = strtolower($k);
                        $newArr[] = array('name' => $newK, 'value' => trim($v), 'inc_type' => $param[0], 'update_time' => time());
                    }
                    self::insertAll($newArr);
                }
                $newData = $data;
            }
            $result = false;
            $res = self::getArray();
            if ($res) {
                $global = array();
                foreach ($res as $k => $val) {
                    $global[$val['name']] = $val['value'];
                }
                $result = cache('settingglobal', $global, $options);
            }
            if ($param[0] != 'global') {
                $result = cache($cache_inc_type, $newData, $options);
            }
            return $result;
        }
    }
}