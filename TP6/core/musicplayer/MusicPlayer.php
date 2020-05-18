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
// | MKOnlinePlayer v2.4
// | 后台音乐数据抓取模块控制器
// | 特别感谢 @metowolf 提供的 Meting.php
// +-------------------------------------------------------------------------
declare (strict_types=1);
namespace core\musicplayer;

use think\AbstractController;
use think\Util;
class MusicPlayer extends AbstractController
{
    const VERSION = '2.4';
    /**
     * Meting实例
     * @var Meting
     */
    protected $meting;
    /**
     * 程序根目录
     * @var string
     */
    protected $rootPath;
    /**
     * 静态资源路径
     * @var string
     */
    protected $staticPath;
    /**
     * 网易云COOKIE
     * @var string
     */
    protected $netease_cookie;
    /**
     * 音乐台开关
     * @var bool
     */
    protected $music_station_switch;
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
        $this->rootPath = $this->app->getRootPath();
        $this->staticPath = __DIR__ . DIRECTORY_SEPARATOR . 'music';
        $this->netease_cookie = $this->app->config->get('music.netease_cookie', '');
        $this->music_station_switch = $this->app->config->get('music.music_station_switch', true);
        if ($this->music_station_switch === true) {
            $this->install(false);
        } else {
            $this->uninstall();
            return $this->error('音乐台已关闭');
        }
        // 如果您的网站启用了https，请将此项置为“true”，如果你的网站未启用 https，建议将此项设置为“false”
        $is_https = $this->request->isSsl();
        defined('IS_HTTPS') or define('IS_HTTPS', $this->app->config->get('route.is_https', $is_https));
        // 配置缓存参数
        $htmlcache_options = $this->app->config->get('music.htmlcache_options', []);
        $this->app->config->set($htmlcache_options, 'htmlcache');
    }
    /**
     * 安装动作
     * @access protected
     * @param  bool $overWrite
     */
    protected function install(bool $overWrite = true)
    {
        if (!is_dir($this->rootPath . 'public/static/music/js')) {
            $res = Util::copyPath($this->staticPath, $this->rootPath . 'public/static/music', null, ['override' => $overWrite]);
            if (!$res) {
                return $this->error('资源安装失败');
            }
        }
    }
    /**
     * 卸载动作
     * @access protected
     */
    protected function uninstall()
    {
        if (is_dir($this->rootPath . 'public/static/music')) {
            $res = Util::delPath($this->rootPath . 'public/static/music');
            if ($res) {
                if (Util::emptyPath($this->rootPath . 'public/static')) {
                    $res = Util::delPath($this->rootPath . 'public/static');
                }
            }
            if (!$res && $this->app->isDebug()) {
                throw new \RuntimeException('资源删除失败,请手动删除' . $this->rootPath . 'public/static/music');
            }
        }
    }
    /**
     * 默认主页
     * @access public
     * @return mixed
     */
    public function index()
    {
        // 标题
        $title = $this->app->config->get('music.title', 'MKOnlinePlayer v2.4');
        // 描述
        $description = $this->app->config->get('music.description', '');
        // 关键字
        $keywords = $this->app->config->get('music.keywords', '');
        // 文字logo
        $header_logo = $this->app->config->get('music.header_logo', '♫ MKOnlinePlayer');
        $this->assign(compact('title', 'description', 'keywords', 'header_logo'));
        $domain = $this->request->domain() . $this->root_dir;
        $apiurl = url("\\core\\musicplayer\\MusicPlayer@main");
        $version = '?version=' . ltrim($this->params['version'], 'v') . time();
        $this->assign(compact('apiurl', 'version', 'domain'));
        return $this->fetch(__DIR__ . \DIRECTORY_SEPARATOR . 'view' . \DIRECTORY_SEPARATOR . 'index.tpl');
    }
    /**
     * API接口
     * @access public
     * @return mixed
     */
    public function main()
    {
        $source = input('source', 'netease');
        $this->meting = new Meting($source);
        // 启用格式化功能
        $this->meting->format(true);
        if ($source == 'kugou' || $source == 'baidu') {
            // 酷狗和百度音乐源暂不支持 https
            define('NO_HTTPS', true);
        } elseif ($source == 'netease' && $this->netease_cookie) {
            // 解决网易云 Cookie 失效
            $this->meting->cookie($this->netease_cookie);
        }
        $types = input('types');
        switch ($types) {
            case 'url':
                // 获取歌曲链接
                $id = input('id');
                // 歌曲ID
                $data = $this->meting->url($id);
                return $this->jsonpReturn((string) $data);
                break;
            case 'pic':
                // 获取歌曲链接
                $id = input('id');
                // 歌曲ID
                $data = $this->meting->pic($id);
                return $this->jsonpReturn((string) $data);
                break;
            case 'lyric':
                // 获取歌词
                $id = input('id');
                // 歌曲ID
                if ($source == 'netease') {
                    $cache = $source . '_' . $types . '_' . $id . '.json';
                    if ($this->HtmlCache->has($cache) && date("Ymd", filemtime($this->HtmlCache->getCacheKey($cache))) == date("Ymd")) {
                        // 缓存存在，则读取缓存
                        $data = $this->HtmlCache->get($cache);
                    } else {
                        $data = $this->meting->lyric($id);
                        // 只缓存链接获取成功的歌曲
                        if (json_decode($data)->lyric !== '') {
                            $this->HtmlCache->set($cache, $data);
                        }
                    }
                } else {
                    $data = $this->meting->lyric($id);
                }
                return $this->jsonpReturn((string) $data);
                break;
            case 'download':
                // 下载歌曲(弃用)
                $fileurl = input('url');
                // 链接
                header('location:$fileurl');
                break;
            case 'userlist':
                // 获取用户歌单列表
                $uid = input('uid');
                // 用户ID
                $url = 'http://music.163.com/api/user/playlist/?offset=0&limit=1001&uid=' . $uid;
                $data = @file_get_contents($url);
                $data = $data === false ? "" : (string) $data;
                return $this->jsonpReturn($data);
                break;
            case 'playlist':
                // 获取歌单中的歌曲
                $id = input('id');
                // 歌单ID
                if ($source == 'netease') {
                    $cache = $source . '_' . $types . '_' . $id . '.json';
                    if ($this->HtmlCache->has($cache) && date("Ymd", filemtime($this->HtmlCache->getCacheKey($cache))) == date("Ymd")) {
                        // 缓存存在，则读取缓存
                        $data = $this->HtmlCache->get($cache);
                    } else {
                        $data = $this->meting->format(false)->playlist($id);
                        // 只缓存链接获取成功的歌曲
                        if (isset(json_decode($data)->playlist->tracks)) {
                            $this->HtmlCache->set($cache, $data);
                        }
                    }
                } else {
                    $data = $this->meting->format(false)->playlist($id);
                }
                return $this->jsonpReturn((string) $data);
                break;
            case 'search':
                // 搜索歌曲
                $s = input('name');
                // 歌名
                $limit = input('count', 20);
                // 每页显示数量
                $pages = input('pages', 1);
                // 页码
                $data = $this->meting->search($s, ['page' => $pages, 'limit' => $limit]);
                $this->musicList($data);
                return $this->jsonpReturn((string) $data);
                break;
            default:
                $htmlmix = '<!doctype html><html><head><meta charset="utf-8"><title>信息</title><style>* {font-family: microsoft yahei}</style></head><body> <h2>MKOnlinePlayer</h2><h3>Github: https://github.com/mengkunsoft/MKOnlineMusicPlayer</h3><br>';
                if (!defined('DEBUG_LEVEL') || DEBUG_LEVEL !== true) {
                    // 非调试模式
                    $htmlmix .= '<p>Api 调试模式已关闭</p>';
                } else {
                    $htmlmix .= '<p><font color="red">您已开启 Api 调试功能，正常使用时请在 api.php 中关闭该选项！</font></p><br>';
                    $htmlmix .= '<p>PHP 版本：' . phpversion() . ' （本程序要求 PHP 7.1+）</p><br>';
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
     * 检测服务器函数支持情况
     * @access protected
     * @param  string $f  函数名
     * @param  bool $m  是否为必须函数
     * @return string
     */
    protected function checkfunc(string $f, bool $m = false) : string
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
     * 输出一个json或jsonp格式的内容
     * @access protected
     * @param  string $data 数组内容
     * @return string
     */
    protected function jsonpReturn(string $data) : string
    {
        $callback = input('callback');
        if (defined('IS_HTTPS') && IS_HTTPS === true && !defined('NO_HTTPS')) {
            // 替换链接为 https
            $data = str_replace('http:\\/\\/', 'https:\\/\\/', $data);
            $data = str_replace('http://', 'https://', $data);
        }
        if ($callback) {
            //输出jsonp格式
            return $this->jsonp((array) json_decode($data, true));
        } else {
            return $this->json((array) json_decode($data, true));
        }
    }
    /**
     * 写入静态模板缓存用于页面展示(运营模式)
     * @access protected
     * @param  mixed $html 缓存值
     * @return bool
     */
    protected function writeHtmlCache($html = '') : bool
    {
        $routeInfo = $this->request->routeInfo();
        if (isset($routeInfo['rule'][0])) {
            $filename = "music_station_static_cache_file_{$routeInfo['rule'][0]}.html";
            return $this->HtmlCache->set($filename, $html);
        }
        return false;
    }
    /**
     * 读取静态模板缓存用于页面展示(运营模式)
     * @access protected
     * @return mixed
     */
    protected function readHtmlCache()
    {
        $routeInfo = $this->request->routeInfo();
        if (isset($routeInfo['rule'][0])) {
            $filename = "music_station_static_cache_file_{$routeInfo['rule'][0]}.html";
            if ($this->HtmlCache->has($filename)) {
                return $this->HtmlCache->send($filename, true);
            }
        }
        return false;
    }
    protected function musicList($data)
    {
        $data = json_decode($data, true);
        if (empty($data)) {
            return false;
        }
        $eol = [];
        foreach ($data as $key => $value) {
            !isset($value['pic']) && ($value['pic'] = '');
            !isset($value['url']) && ($value['url'] = '');
            $eol[$key] = $value;
        }
        try {
            $cachePath = __DIR__ . \DIRECTORY_SEPARATOR . 'cache' . \DIRECTORY_SEPARATOR;
            $base = file_get_contents($cachePath . 'musicList.json');
            if (empty($base)) {
                $base = '';
            }
            $base = json_decode($base, true);
            if (empty($base)) {
                $base = [];
            }
            $base = array_merge($eol, $base);
            if (count($base) > 200) {
                $base = $eol;
            }
            $result = json_encode($base, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $content = $result === false ? '' : $result;
            file_put_contents($cachePath . 'musicList.json', $content);
            $json = file_get_contents($cachePath . 'musicList.json');
            if (empty($json)) {
                return false;
            }
            $contents = file_get_contents($cachePath . 'musicList.js');
            $contents = str_replace('#MUSICLIST#', $json, $contents);
            file_put_contents($this->rootPath . 'public/static/music/js/musicList.js', $contents);
        } catch (\Exception $e) {
            return false;
        }
    }
    /*cookie 获取及使用方法*/
    public function doc()
    {
        $domain = url("\\core\\musicplayer\\MusicPlayer@index");
        $this->assign(compact('domain'));
        return $this->fetch(__DIR__ . \DIRECTORY_SEPARATOR . 'view' . \DIRECTORY_SEPARATOR . 'doc.tpl');
    }
}