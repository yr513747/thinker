<?php
// +----------------------------------------------------------------------
// | 音乐台设置
// +----------------------------------------------------------------------
return [
    // 开关
    'music_station_switch' => true,
	// 标题
	'title' => 'MKOnlinePlayer v2.4',
	// 描述
	'description' => '一款开源的基于网易云音乐api的在线音乐播放器。具有音乐搜索、播放、下载、歌词同步显示、个人音乐播放列表同步等功能。',
	// 关键字
	'keywords' => '孟坤播放器,在线音乐播放器,MKOnlinePlayer,网易云音乐,音乐api,音乐播放器源代码',
	// 文字logo
	'header_logo' => '♫ MKOnlinePlayer',
    // 如果网易云音乐歌曲获取失效，请将你的 COOKIE 放到这儿 ↓↓↓↓↓
    'netease_cookie' => '',
    // 模板缓存参数
    'htmlcache_options' => [
        // 缓存开关
        'switch' => true,
        // 缓存保存目录
        'path' => runtime_path('html') . 'music' . DIRECTORY_SEPARATOR,
        // 是否使用二级目录
        'cache_subdir' => true,
        // 命名方式
        'hash_type' => 'md5',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        // 是否启用字符压缩
        'data_compress' => true,
        // 序列化机制 例如 ['serialize', 'unserialize']
        'serialize' => [],
    ],
];