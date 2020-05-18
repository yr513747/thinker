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
return [
    // 默认纯动态URL模式，兼容不支持pathinfo环境
    'seo_pseudo' => 1,
    // 1=兼容模式的URL，2=伪动态
    'seo_dynamic_format' => 1,
    // 1=精简伪静态，2=层次栏目伪静态
    'seo_rewrite_format' => 1,
    // 0=保留入口文件，1=隐藏入口文件
    'seo_inlet' => 0,
    // 手机域名配置
    'web_mobile_domain' => 'm',
    // 0 = 响应式模板，1 = 分离式模板
    'response_type' => 0,
    // 0 = 响应式手机端，1 = 分离式手机端
    'separate_mobile' => 0,
];