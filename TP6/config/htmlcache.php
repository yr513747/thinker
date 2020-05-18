<?php
// +----------------------------------------------------------------------
// | 静态文件缓存设置
// +----------------------------------------------------------------------
return [
    // 缓存开关
    'switch' => true,
    // 缓存保存目录
    'path' => runtime_path('html'),
    // 是否使用二级目录
    'cache_subdir' => true,
    // 命名方式
    'hash_type' => 'md5',
    // 缓存前缀
    'prefix' => '',
    // 缓存有效期 0表示永久缓存 支持单独在缓存规则中设置缓存时间 单位s
    'expire' => 0,
    // 是否启用字符压缩
    'data_compress' => true,
    // 序列化机制 例如 ['serialize', 'unserialize']
    'serialize' => [],
	// +-------------------------------------------------------------------------
    // | 模板缓存规则
    // | 假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html
    // | 就保存名字为 index_goods_goodsinfo_1.html
    // | 配置成这样, 指定 模块 控制器 方法名 参数名
    // +-------------------------------------------------------------------------
    'cache_type' => [
        // 首页
        'home_Index_index' => ['filename' => 'index', 'cache' => 7200],
        // [普通伪静态]文章
        'home_Article_index' => ['filename' => 'channel', 'cache' => 7200],
        'home_Article_lists' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        'home_Article_view' => ['filename' => 'view', 'p' => ['dirname', 'aid'], 'cache' => 7200],
        // [普通伪静态]产品
        'home_Product_index' => ['filename' => 'channel', 'cache' => 7200],
        'home_Product_lists' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        'home_Product_view' => ['filename' => 'view', 'p' => ['dirname', 'aid'], 'cache' => 7200],
        // [普通伪静态]图集
        'home_Images_index' => ['filename' => 'channel', 'cache' => 7200],
        'home_Images_lists' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        'home_Images_view' => ['filename' => 'view', 'p' => ['dirname', 'aid'], 'cache' => 7200],
        // [普通伪静态]下载
        'home_Download_index' => ['filename' => 'channel', 'cache' => 7200],
        'home_Download_lists' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        'home_Download_view' => ['filename' => 'view', 'p' => ['dirname', 'aid'], 'cache' => 7200],
        // [普通伪静态]单页
        'home_Single_index' => ['filename' => 'channel', 'cache' => 7200],
        'home_Single_lists' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        // [超短伪静态]列表页
        'home_Lists_index' => ['filename' => 'lists', 'p' => ['tid', 'page'], 'cache' => 7200],
        // [超短伪静态]内容页
        'home_View_index' => ['filename' => 'view', 'p' => ['dirname', 'aid'], 'cache' => 7200],
    ],
];