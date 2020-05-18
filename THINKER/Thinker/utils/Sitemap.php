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
// [ Sitemap输出和生成类 ]
// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\utils;

use think\Response;
// --------------------------------------------------------------------------
// <xml version='1.0' encoding='UTF-8'>
// <urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>
//   <url>
//     <loc>http://domain/</loc>
//     <priority></priority>
//     <lastmod>2020-02-22 13:24:33</lastmod>
//     <changefreq>always</changefreq>
//   </url>
// </urlset>
// --------------------------------------------------------------------------
class Sitemap
{
    private $config = array(
        // config
        'encoding' => 'UTF-8',
        'ver' => '1.0',
    );
    private $content = null;
    // 储存节点数据
    private $items = array();
    public function __get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }
    public function content()
    {
        if (is_null($this->content)) {
            $this->content = $this->Build();
        }
        return $this->content;
    }
    /**
     * 架构函数
     * @access public
     * @param array $config 配置参数
     */
    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }
    /**
     * 添加一个节点，支持多次调用
     * @access public
     * @param  $loc  文档链接
     * @param  $priority 优先级别
     * @param  $changefreq  更新频率 | always 经常,hourly 每小时,daily 每天,weekly 每周,monthly 每月,yearly 每年,never 从不
     * @param  $lastmod_time 最后更新时间（可空）
     */
    public function AddItem($loc, $priority, $changefreq = 'Always', $lastmod_time = '')
    {
        if (empty($lastmod_time)) {
            $lastmod_time = date('Y-m-d H:i:s', getTime());
        }
        $arr = array(
            // arr
            '1.0' => '1.0',
            '0.9' => '0.9',
            '0.8' => '0.8',
            '0.7' => '0.7',
            '0.6' => '0.6',
            '0.5' => '0.5',
        );
        $this->items[] = array(
            // items
            'loc' => $loc,
            'priority' => $arr[$priority],
            'lastmod' => $lastmod_time,
            'changefreq' => $changefreq,
        );
    }
    /**
     * 自动生成sitemap xml文件内容
     * @access public
     */
    public function Build()
    {
        $s = "<?xml version='{$this->ver}' encoding='{$this->encoding}'?>\r\n";
        $s .= "\t<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\r\n";
        // items
        for ($i = 0; $i < count($this->items); $i++) {
            $s .= "\t\t<url>\n";
            $s .= "\t\t\t<loc>{$this->items[$i]['loc']}</loc>\r\n";
            $s .= "\t\t\t<priority>{$this->items[$i]['priority']}</priority>\r\n";
            $s .= "\t\t\t<lastmod>{$this->items[$i]['lastmod']}</lastmod>\r\n";
            $s .= "\t\t\t<changefreq>{$this->items[$i]['changefreq']}</changefreq>\r\n";
            $s .= "\t\t</url>\n";
        }
        // close
        $s .= "\t</urlset>";
        return $this->content = $s;
    }
    /**
     * 将产生的sitemap内容直接打印输出
     * @access public
     */
    public function Show()
    {
        //header("Content-Type: text/xml; charset=utf-8");
        $content = $this->content();
        return Response::create($content)->contentType("text/xml");
    }
    /**
     * 将产生的sitemap 内容保存到文件
     * @access public
     * @param  $fname 要保存的文件名
     */
    public function SaveToFile($fname)
    {
        $content = $this->content();
        $handle = fopen($fname, 'w+');
        if ($handle === false) {
            return false;
        }
        fwrite($handle, $content);
        fclose($handle);
        return true;
    }
    /**
     * 从文件中获取输出
     * @access public
     * @param  $fname 文件名
     */
    public function getFile($fname)
    {
        $handle = fopen($fname, 'r');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            echo fgets($handle);
        }
        fclose($handle);
    }
}