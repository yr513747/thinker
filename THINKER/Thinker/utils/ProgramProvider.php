<?php

namespace Thinker\utils;

use EasyWeChat\MiniProgram\AccessToken;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 注册订阅消息
 * Class ProgramProvider
 * @package Thinker\utils
 */
class ProgramProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['mini_program.access_token'] = function ($pimple) {
            return new AccessToken(
                $pimple['config']['mini_program']['app_id'],
                $pimple['config']['mini_program']['secret'],
                $pimple['cache']
            );
        };

        $pimple['mini_program.now_notice'] = function ($pimple) {
            return new ProgramSubscribe($pimple['mini_program.access_token']);
        };
    }
}
