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
// [ 模型基类 ]
// --------------------------------------------------------------------------
namespace app\common\basic;

use think\facade\Db;
use think\facade\Request;
use think\Model;
class BaseModel extends Model 
{
	/**
     * 当前站点子目录路径
     * @var string
     */
    protected static $root_dir;
    private static $errorMsg;
    private static $transaction = 0;
    private static $DbInstance = [];
    const DEFAULT_ERROR_MSG = '操作失败,请稍候再试!';
	/**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
		global $_M;
		static::$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : Request::rootUrl();
		unset($_M);
    }
    /**
     * 设置错误信息
     * @param string $errorMsg
     * @return bool
     */
    protected static function setErrorInfo($errorMsg = self::DEFAULT_ERROR_MSG, $rollback = false)
    {
        if ($rollback) {
            self::rollbackTrans();
        }
        self::$errorMsg = $errorMsg;
        return false;
    }
    /**
     * 获取错误信息
     * @param string $defaultMsg
     * @return string
     */
    public static function getErrorInfo($defaultMsg = self::DEFAULT_ERROR_MSG)
    {
        return !empty(self::$errorMsg) ? self::$errorMsg : $defaultMsg;
    }
    /**
     * 开启事务
     */
    public static function beginTrans()
    {
        Db::startTrans();
    }
    /**
     * 提交事务
     */
    public static function commitTrans()
    {
        Db::commit();
    }
    /**
     * 关闭事务
     */
    public static function rollbackTrans()
    {
        Db::rollback();
    }
    /**
     * 根据结果提交滚回事务
     * @param $res
     */
    public static function checkTrans($res)
    {
        if ($res) {
            self::commitTrans();
        } else {
            self::rollbackTrans();
        }
    }
}