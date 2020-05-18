<?php

declare(strict_types=1);

namespace Thinker\weapp;

use think\helper\Str;
use think\facade\Event;
use think\facade\Config;
use think\exception\HttpException;

class Route
{
    /**
     * 插件路由请求
     * @param null $weapp
     * @param null $controller
     * @param null $action
     * @return mixed
     */
    public static function execute($weapp = null, $controller = null, $action = null)
    {
        $app = app();
        $request = $app->request;

        Event::trigger('weapp_begin', $request);

        if (empty($weapp) || empty($controller) || empty($action)) {
            throw new HttpException(500, lang('weapp can not be empty'));
        }

        $request->weapp = $weapp;
        // 设置当前请求的控制器、操作
        $request->setController($controller)->setAction($action);

        // 获取插件基础信息
        $info = get_weapp_info($weapp);
        if (!$info) {
            throw new HttpException(404, lang('weapp %s not found', [$weapp]));
        }
        if (!$info['status']) {
            throw new HttpException(500, lang('weapp %s is disabled', [$weapp]));
        }

        // 监听weapp_module_init
        Event::trigger('weapp_module_init', $request);
        $class = get_weapp_class($weapp, 'controller', $controller);
        if (!$class) {
            throw new HttpException(404, lang('weapp controller %s not found', [Str::studly($controller)]));
        }

        // 重写视图基础路径
        $config = Config::get('view');
        $config['view_path'] = $app->weapp->getWeappPath() . $weapp . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        Config::set($config, 'view');

        // 生成控制器对象
        $instance = new $class($app);
        $vars = [];
        if (is_callable([$instance, $action])) {
            // 执行操作方法
            $call = [$instance, $action];
        } elseif (is_callable([$instance, '_empty'])) {
            // 空操作
            $call = [$instance, '_empty'];
            $vars = [$action];
        } else {
            // 操作不存在
            throw new HttpException(404, lang('weapp action %s not found', [get_class($instance).'->'.$action.'()']));
        }
        Event::trigger('weapp_action_begin', $call);

        return call_user_func_array($call, $vars);
    }
}