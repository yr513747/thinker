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
// +-------------------------------------------------------------------------
// | 模板缓存规则
// | 假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html
// | 就保存名字为 index_goods_goodsinfo_1.html
// | 配置成这样, 指定 模块 控制器 方法名 参数名
// +-------------------------------------------------------------------------
// --------------------------------------------------------------------------
return [
    // 缓存的页面，规则：模块 控制器 方法名 参数名
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