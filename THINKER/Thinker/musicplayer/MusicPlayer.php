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
// | cookie 获取及使用方法见
// | https://github.com/mengkunsoft/MKOnlineMusicPlayer/wiki/%E7%BD%91%E6%98%93%E4%BA%91%E9%9F%B3%E4%B9%90%E9%97%AE%E9%A2%98
// +-------------------------------------------------------------------------
// | 更多相关问题可以查阅项目 wiki
// | https://github.com/mengkunsoft/MKOnlineMusicPlayer/wiki
// +-------------------------------------------------------------------------
// | 如果还有问题，可以提交 issues
// | https://github.com/mengkunsoft/MKOnlineMusicPlayer/issues
// +-------------------------------------------------------------------------
// | MKOnlinePlayer v2.4
// | 后台音乐数据抓取模块控制器
// | 特别感谢 @metowolf 提供的 Meting.php
// +-------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\musicplayer;

use app\common\controller\Common;
class MusicPlayer extends Common
{
    const VERSION = '2.4';
    /**
     * Meting实例
     * @var \think\Meting
     */
    protected $meting = null;
    /**
     * 网易云COOKIE
     * @var 
     * 如果网易云音乐歌曲获取失效，请将你的 COOKIE 放到extra/music.php配置文件中
     */
    protected $netease_cookie = '';
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct()
    {
        parent::__construct();
        $this->netease_cookie = config('music.netease_cookie', '');
        // 如果您的网站启用了https，请将此项置为“true”，如果你的网站未启用 https，建议将此项设置为“false”
        $is_https = config('route.is_https', false);
        defined('IS_HTTPS') or define('IS_HTTPS', $is_https);
        // 文件缓存目录,请确保该目录存在且有读写权限。如无需缓存，可将此行注释掉
        defined('MUSIC_CACHE_PATH') or define('MUSIC_CACHE_PATH', runtime_path('music'));
        // 控制器初始化
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
        // 没有缓存文件夹则创建
        if (defined('MUSIC_CACHE_PATH') && !is_dir(MUSIC_CACHE_PATH)) {
            $this->createFolders(MUSIC_CACHE_PATH);
        }
    }
    /**
     * 默认主页
     * @access public
     * @return mixed
     */
    public function index()
    {
        $host = $this->params['web_root'];
        $apiurl = url("\\Thinker\\musicplayer\\MusicPlayer@main");
        $update_time = $this->V();
        $content = '<!doctype html>';
        $content .= '<html>';
        $content .= '<head>';
        $content .= '<meta charset="' . $this->params['cfg_soft_lang'] . '">';
        $content .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $content .= '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">';
        $content .= '<meta name="renderer" content="webkit">';
        $content .= '<meta name="author" content="mengkun">';
        $content .= '<meta name="generator" content="KodCloud">';
        $content .= '<meta http-equiv="Cache-Control" content="no-siteapp">';
        $content .= '<!-- 强制移动设备以app模式打开页面(即在移动设备下全屏，仅支持部分浏览器) -->';
        $content .= '<meta name="apple-mobile-web-app-capable" content="yes">';
        $content .= '<meta name="apple-touch-fullscreen" content="yes">';
        $content .= '<meta name="apple-mobile-web-app-status-bar-style" content="black">';
        $content .= '<meta name="full-screen" content="yes">';
        $content .= '<!--UC强制全屏-->';
        $content .= '<meta name="browsermode" content="application">';
        $content .= '<!--UC应用模式-->';
        $content .= '<meta name="x5-fullscreen" content="true">';
        $content .= '<!--QQ强制全屏-->';
        $content .= '<meta name="x5-page-mode" content="app">';
        $content .= '<!--QQ应用模式-->';
        $content .= '<title>MKOnlinePlayer v2.4</title>';
        $content .= '<meta name="description" content="一款开源的基于网易云音乐api的在线音乐播放器。具有音乐搜索、播放、下载、歌词同步显示、个人音乐播放列表同步等功能。"/>';
        $content .= '<meta name="keywords" content="孟坤播放器,在线音乐播放器,MKOnlinePlayer,网易云音乐,音乐api,音乐播放器源代码"/>';
        $content .= '<!-- 不支持IE8及以下版本浏览器 -->';
        $content .= '<!--[if lte IE 8]>';
        $content .= '<script>window.location.href="' . $host . '/"</script>';
        $content .= '<![endif]-->';
        $content .= '<script>';
        $content .= 'var YROnlinePlayer = "' . $apiurl . '";';
        $content .= '</script>';
        $content .= '<link rel="shortcut icon" href="' . $host . '/static/music/images/favicon.ico' . $update_time . '">';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/jquery.min.js' . $update_time . '"></script>';
        $content .= '<link rel="stylesheet" type="text/css" href="' . $host . '/static/music/css/player.css' . $update_time . '" />';
        $content .= '<link rel="stylesheet" type="text/css" href="' . $host . '/static/music/css/small.css' . $update_time . '" />';
        $content .= '<link rel="stylesheet" type="text/css" href="' . $host . '/static/music/css/jquery.mCustomScrollbar.min.css' . $update_time . '" />';
        $content .= '</head>';
        $content .= '<body>';
        $content .= '<div id="blur-img"></div>';
        $content .= '<div class="header">';
        $content .= '<div class="logo" title="Version 2.4; Based on Meting; Powered by Mengkun"><a class="btn" href="' . $host . '/">♫ MKOnlinePlayer</a></div></div>';
        $content .= '<div class="center">';
        $content .= '<div class="container">';
        $content .= '<div class="btn-bar">';
        $content .= '<div class="btn-box" id="btn-area"> <span class="btn" data-action="player" hidden>播放器</span> <span class="btn" data-action="playing" title="正在播放列表">正在播放</span> <span class="btn" data-action="sheet" title="音乐播放列表">播放列表</span> <span class="btn" data-action="search" title="点击搜索音乐">歌曲搜索</span> </div>';
        $content .= ' </div>';
        $content .= ' <div class="data-area">';
        $content .= '<!--歌曲歌单-->';
        $content .= '<div id="sheet" class="data-box" hidden></div><div id="main-list" class="music-list data-box"></div></div>';
        $content .= '<div class="player" id="player"> ';
        $content .= '<div class="cover"> <img src="' . $host . '/static/music/images/player_cover.png' . $update_time . '" class="music-cover" id="music-cover"> </div>';
        $content .= '<div class="lyric"><ul id="lyric"></ul></div><div id="music-info" title="点击查看歌曲信息"></div></div></div></div>';
        $content .= '<div class="footer">';
        $content .= '<div class="container">';
        $content .= '<div class="con-btn"> <a href="javascript:;" class="player-btn btn-prev" title="上一首"></a> <a href="javascript:;" class="player-btn btn-play" title="暂停/继续"></a> <a href="javascript:;" class="player-btn btn-next" title="下一首"></a> <a href="javascript:;" class="player-btn btn-order" title="循环控制"></a> </div>';
        $content .= '<div class="vol"><div class="quiet"> <a href="javascript:;" class="player-btn btn-quiet" title="静音"></a> </div>';
        $content .= '<div class="volume"><div class="volume-box"><div id="volume-progress" class="mkpgb-area"></div></div></div></div>';
        $content .= '<div class="progress"><div class="progress-box"><div id="music-progress" class="mkpgb-area"></div></div></div></div></div>';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/layer/layer.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/ajax.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/lyric.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/musicList.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/functions.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/player.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/jquery.mCustomScrollbar.concat.min.js' . $update_time . '"></script> ';
        $content .= '<script type="text/javascript" src="' . $host . '/static/music/js/background-blur.min.js' . $update_time . '"></script>';
        $content .= '</body>';
        $content .= '</html>';
        return $this->display($content);
    }
    /**
     * 获取请求对象
	 * @access protected
     * @param $source 歌曲源
     */
    protected function getMeting($source)
    {
        if (is_null($this->meting)) {
            $this->meting = new Meting($source);
        }
        return $this->meting;
    }
    /**
     * API接口
     * @access public
     * @return mixed
     */
    public function main()
    {
        $source = $this->getParam('source', 'netease');
        $API = $this->getMeting($source);
        $API->format(true);
        // 启用格式化功能
        if ($source == 'kugou' || $source == 'baidu') {
            define('NO_HTTPS', true);
            // 酷狗和百度音乐源暂不支持 https
        } elseif ($source == 'netease' && $this->netease_cookie) {
            $API->cookie($this->netease_cookie);
            // 解决网易云 Cookie 失效
        }
        $types = $this->getParam('types');
        switch ($types) {
            case 'url':
                // 获取歌曲链接
                $id = $this->getParam('id');
                // 歌曲ID
                $data = $API->url($id);
                return $this->echojson($data);
                break;
            case 'pic':
                // 获取歌曲链接
                $id = $this->getParam('id');
                // 歌曲ID
                $data = $API->pic($id);
                return $this->echojson($data);
                break;
            case 'lyric':
                // 获取歌词
                $id = $this->getParam('id');
                // 歌曲ID
                if ($source == 'netease' && defined('MUSIC_CACHE_PATH')) {
                    $cache = MUSIC_CACHE_PATH . $source . '_' . $types . '_' . $id . '.json';
                    if (is_file($cache)) {
                        // 缓存存在，则读取缓存
                        $data = @file_get_contents($cache);
                    } else {
                        $data = $API->lyric($id);
                        // 只缓存链接获取成功的歌曲
                        if (json_decode($data)->lyric !== '') {
                            @file_put_contents($cache, $data);
                        }
                    }
                } else {
                    $data = $API->lyric($id);
                }
                return $this->echojson($data);
                break;
            case 'download':
                // 下载歌曲(弃用)
                $fileurl = $this->getParam('url');
                // 链接
                header('location:$fileurl');
                exit;
                break;
            case 'userlist':
                // 获取用户歌单列表
                $uid = $this->getParam('uid');
                // 用户ID
                $url = 'http://music.163.com/api/user/playlist/?offset=0&limit=1001&uid=' . $uid;
                $data = @file_get_contents($url);
                return $this->echojson($data);
                break;
            case 'playlist':
                // 获取歌单中的歌曲
                $id = $this->getParam('id');
                // 歌单ID
                if ($source == 'netease' && defined('MUSIC_CACHE_PATH')) {
                    $cache = MUSIC_CACHE_PATH . $source . '_' . $types . '_' . $id . '.json';
                    if (is_file($cache) && date("Ymd", filemtime($cache)) == date("Ymd")) {
                        // 缓存存在，则读取缓存
                        $data = @file_get_contents($cache);
                    } else {
                        $data = $API->format(false)->playlist($id);
                        // 只缓存链接获取成功的歌曲
                        if (isset(json_decode($data)->playlist->tracks)) {
                            @file_put_contents($cache, $data);
                        }
                    }
                } else {
                    $data = $API->format(false)->playlist($id);
                }
                return $this->echojson($data);
                break;
            case 'search':
                // 搜索歌曲
                $s = $this->getParam('name');
                // 歌名
                $limit = $this->getParam('count', 20);
                // 每页显示数量
                $pages = $this->getParam('pages', 1);
                // 页码
                $data = $API->search($s, ['page' => $pages, 'limit' => $limit]);
                return $this->echojson($data);
                break;
            default:
                $htmlmix = '<!doctype html><html><head><meta charset="utf-8"><title>信息</title><style>* {font-family: microsoft yahei}</style></head><body> <h2>MKOnlinePlayer</h2><h3>Github: https://github.com/mengkunsoft/MKOnlineMusicPlayer</h3><br>';
                if (!defined('DEBUG_LEVEL') || DEBUG_LEVEL !== true) {
                    // 非调试模式
                   $htmlmix .= '<p>Api 调试模式已关闭</p>';
                } else {
                    $htmlmix .= '<p><font color="red">您已开启 Api 调试功能，正常使用时请在 api.php 中关闭该选项！</font></p><br>';
                    $htmlmix .= '<p>PHP 版本：' . phpversion() . ' （本程序要求 PHP 5.4+）</p><br>';
                    $htmlmix .= '<p>服务器函数检查</p>';
                    $htmlmix .= '<p>curl_exec: ' . $this->checkfunc('curl_exec', true) . ' （用于获取音乐数据）</p>';
                    $htmlmix .= '<p>file_get_contents: ' . $this->checkfunc('file_get_contents', true) . ' （用于获取音乐数据）</p>';
                    $htmlmix .= '<p>json_decode: ' . $this->checkfunc('json_decode', true) . ' （用于后台数据格式化）</p>';
                    $htmlmix .= '<p>hex2bin: ' . $this->checkfunc('hex2bin', true) . ' （用于数据解析）</p>';
                    $htmlmix .= '<p>openssl_encrypt: ' . $this->checkfunc('openssl_encrypt', true) . ' （用于数据解析）</p>';
                }
                $htmlmix .= '</body></html>';
				return $this->display($htmlmix);
        }
    }
    /**
     * 创建多层文件夹 
	 * @access protected
     * @param $dir 路径
     */
    protected function createFolders($dir)
    {
        return is_dir($dir) or $this->createFolders(dirname($dir)) and mkdir($dir, 0755);
    }
    /**
     * 检测服务器函数支持情况
	 * @access protected
     * @param $f 函数名
     * @param $m 是否为必须函数
     * @return 
     */
    protected function checkfunc($f, $m = false)
    {
        if (function_exists($f)) {
            return '<font color="green">可用</font>';
        } else {
            if ($m == false) {
                return '<font color="black">不支持</font>';
            } else {
                return '<font color="red">不支持</font>';
            }
        }
    }
    /**
     * 获取GET或POST过来的参数
	 * @access protected
     * @param $key 键值
     * @param $default 默认值
     * @return 获取到的内容（没有则为默认值）
     */
    protected function getParam($key, $default = '')
    {
        return input($key, $default);
    }
    /**
     * 输出一个json或jsonp格式的内容
	 * @access protected
     * @param $data 数组内容
     */
    protected function echojson($data)
    {
        $callback = $this->getParam('callback');
        if (defined('IS_HTTPS') && IS_HTTPS === true && !defined('NO_HTTPS')) {
            // 替换链接为 https
            $data = str_replace('http:\\/\\/', 'https:\\/\\/', $data);
            $data = str_replace('http://', 'https://', $data);
        }
        if ($callback) {
            //输出jsonp格式
            return $this->jsonp(json_decode($data, true));
        } else {
            return $this->json(json_decode($data, true));
        }
    }
    /**
     *  静态文件版本号
	 * @access protected
     * @return string
     */
    protected function V()
    {
        $format = '?cdnversion=';
        $n = date("n");
        $j = date("j");
        $d = strtotime("tomorrow");
        return $format . date("Ymd", $d) . $n . $j;
    }
}