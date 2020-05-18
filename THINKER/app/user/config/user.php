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
// [ 会员中心基础配置文件 ]
// --------------------------------------------------------------------------
return [
    // 过滤不需要登录的操作
    'filter_login_action' => [
        // 登录
        'Users@login',
        // 退出
        'Users@logout',
        // 注册
        'Users@register',
        // 验证码
        'Users@captcha',
        // 忘记密码
        'Users@retrievePassword',
        // 重置密码
        'Users@resetPassword',
        // 微信登陆
        'Users@getWechatInfo',
        // 选择登陆方式
        'Users@usersSelectLogin',
        // 授权微信登陆
        'Users@ajaxWechatLogin',
        // PC端微信扫码登陆
        'Users@pcWechatLogin',
        // 支付宝异步通知
        'Pay@alipayReturn',
        // 邮箱发送
        'Smtpmail@*',
        // 第三方登录
        'LoginApi@*',
    ],
];