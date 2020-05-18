<?php

use think\facade\Env;

return [
    'default' => Env::get('filesystem.driver', 'public'),
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => runtime_path('storage'),
        ],
        'public' => [
            'type'       => 'local',
            'root'       => root_path('public'). 'uploads',
            'url'        => '/uploads',
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
    ],
];