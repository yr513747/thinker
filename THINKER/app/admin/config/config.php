<?php


$admin_config = array(
    
    //分页配置
    'paginate'      => array(
        'list_rows' => 15,
    ),
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'htmlspecialchars', // htmlspecialchars
    // 登录有效期
    'login_expire' => 3600,
    // 登录错误最大次数
    'login_errtotal'   => 8,
    // 登录错误超过次数之后，锁定用户名有效时间 15 分钟
    'login_errexpire'   => 900,
    
   
    'HTML_CACHE_STATUS' => false,
    
    // 控制器与操作名之间的连接符
    'POWER_OPERATOR' => '@',

    // 数据管理
    'DATA_BACKUP_PATH' => '/data/sqldata', //数据库备份根路径
    'DATA_BACKUP_PART_SIZE' => 52428800, //数据库备份卷大小 50M
    'DATA_BACKUP_COMPRESS' => 0, //数据库备份文件是否启用压缩
    'DATA_BACKUP_COMPRESS_LEVEL' => 9, //数据库备份文件压缩级别

    // 过滤不需要登录的操作
    'filter_login_action' => array(
        'Admin@login', // 登录
        'Admin@logout', // 退出
        'Admin@vertify', // 验证码
		'Admin@*'
    ),
    
    // 无需验证权限的操作
    'uneed_check_action' => array(
        'Base@*', // 基类
        'Index@*', // 后台首页
        'Ajax@*', // 所有ajax操作
        'Ueditor@*', // 编辑器上传
        'Uploadify@*', // 图片上传
    ),
);

return $admin_config;
