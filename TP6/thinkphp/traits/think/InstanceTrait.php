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

namespace think\traits\think;

use think\Container;
use Closure;
use BadMethodCallException;

trait InstanceTrait
{
    /**
     * @var null|static|object|Closure 实例对象
     */
    protected static $instance = null;
	
    /**
     * 带参数实例化当前类
	 * @access public
     * @param array $options 实例配置
     * @return static
     */
    public static function instance(...$options)
    {
		$class_name = get_called_class();
		
        if (is_null(static::$instance)) {
            static::$instance = Container::getInstance()->invokeClass($class_name, $options);
        }
		
		if (static::$instance instanceof Closure) {
            return (static::$instance)($options);
        }
		
        return static::$instance;
    }
	
	/**
     * 设置当前类实例
     * @access public
     * @param object|Closure $instance
     * @return void
     */
    public static function setInstance($instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 静态调用
	 * @access public
     * @param string $method 调用方法
     * @param array  $params 调用参数
     * @return mixed
     * @throws BadMethodCallException
     */
    public static function __callStatic($method, array $params)
    {
        if (is_null(static::$instance)) {
            static::$instance = static::instance(func_get_args());
        }
        $call = substr($method, 1);
        if (0 !== strpos($method, '_') || !is_callable([static::$instance, $call])) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }
        return call_user_func_array([static::$instance, $call], $params);
    }
}