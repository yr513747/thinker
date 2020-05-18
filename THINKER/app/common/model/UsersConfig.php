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
// [ 会员功能配置 Model ]
// --------------------------------------------------------------------------
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 会员功能配置表
//
//字段	类型	空	默认	注释
//id	int(11)	否	 	会员功能配置表ID
//name	varchar(50)	是	 	配置的key键名
//value	text	是	NULL	配置的value值
//desc	varchar(100)	是	 	键名说明
//inc_type	varchar(64)	是	 	配置分组
//update_time	int(11)	是	0	更新时间
class UsersConfig extends BaseModel
{
    use ModelTrait;
    protected $pk = 'id';
    protected $name = 'users_config';
    /**
     * 专用于获取users_config，会员配置表数据处理。
     * @param string $config_key 传入值不同，获取数据不同
     * 例：获取配置所有数据，传入：all，
     * 获取分组所有数据，传入：分组标识，如：member，
     * 获取分组中的单个数据，传入：分组标识.名称标识，如：users.users_open_register
     * @param array $data 为空则查询，否则为添加或修改。
     * @param array $options 缓存配置
     * @return array or string or bool
     */
    public static function getUsersConfigData($config_key, $data = array(), $options = null)
    {
        $param = explode('.', $config_key);
        $cache_inc_type = 'users_config' . $param[0];
        if (empty($data)) {
            $config = cache($cache_inc_type, '', $options);
            //直接获取缓存文件
            if (empty($config)) {
                //缓存文件不存在就读取数据库
                if ($param[0] == 'all') {
                    $param[0] = 'all';
                    $res = Db::name('users_config')->select()->toArray();
                } else {
                    $res = Db::name('users_config')->where('inc_type', $param[0])->select()->toArray();
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
            $result = Db::name('users_config')->where('inc_type', $param[0])->select()->toArray();
            if ($result) {
                foreach ($result as $val) {
                    $temp[$val['name']] = $val['value'];
                }
                $add_data = array();
                foreach ($data as $k => $v) {
                    $newK = strtolower($k);
                    $newArr = array('name' => $newK, 'value' => trim($v), 'inc_type' => $param[0], 'update_time' => getTime());
                    if (!isset($temp[$newK])) {
                        array_push($add_data, $newArr);
                        //新key数据插入数据库
                    } else {
                        if ($v != $temp[$newK]) {
                            Db::name('users_config')->where('name', $newK)->save($newArr);
                            //缓存key存在且值有变更新此项
                        }
                    }
                }
                if (!empty($add_data)) {
                    Db::name('users_config')->insertAll($add_data);
                }
                //更新后的数据库记录
                $newRes = Db::name('users_config')->where('inc_type', $param[0])->select()->toArray();
                foreach ($newRes as $rs) {
                    $newData[$rs['name']] = $rs['value'];
                }
            } else {
                if ($param[0] != 'all') {
                    foreach ($data as $k => $v) {
                        $newK = strtolower($k);
                        $newArr[] = array('name' => $newK, 'value' => trim($v), 'inc_type' => $param[0], 'update_time' => getTime());
                    }
                    !empty($newArr) && Db::name('users_config')->insertAll($newArr);
                }
                $newData = $data;
            }
            $result = false;
            $res = Db::name('users_config')->select()->toArray();
            if ($res) {
                $global = array();
                foreach ($res as $k => $val) {
                    $global[$val['name']] = $val['value'];
                }
                $result = cache('users_config' . 'all', $global, $options);
            }
            if ($param[0] != 'all') {
                $result = cache($cache_inc_type, $newData, $options);
            }
            return $result;
        }
    }
}