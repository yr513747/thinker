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
// [ 公用请求变量接口 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\basic\contract;

interface CommonFuncInterface
{
    /**
     * 当前请求参数变量名称
     */
    const DATANAME = 'data';
    /**
     * 请求变量赋值
     * @access public
     * @param string $name  变量名
     * @param mixed  $value 变量值
     */
    public function __set($name, $value);
    /**
     * 取得请求变量的值
     * @access protected
     * @param string $name 请求变量
     * @return mixed
     */
    public function __get($name);
    /**
     * 检测请求变量是否存在
     * @access public
     * @param string $name 请求变量名
     * @return bool
     */
    public function __isset($name);
    public function __unset($name);
}