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
// [ 公共函数文件 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
if (!function_exists('set_arcseotitle')) {
    /**
     * 设置内容标题
     */
    function set_arcseotitle($title = '', $seo_title = '', $typename = '')
    {
        // 针对没有自定义SEO标题的文档
        if (empty($seo_title)) {
            static $web_name = null;
            null === $web_name && ($web_name = config('tpcache.web_name'));
            static $seo_viewtitle_format = null;
            null === $seo_viewtitle_format && ($seo_viewtitle_format = config('tpcache.seo_viewtitle_format'));
            switch ($seo_viewtitle_format) {
                case '1':
                    $seo_title = $title;
                    break;
                case '3':
                    $seo_title = $title . '_' . $typename . '_' . $web_name;
                    break;
                case '2':
                default:
                    $seo_title = $title . '_' . $web_name;
                    break;
            }
        }
        return $seo_title;
    }
}
if (!function_exists('set_typeseotitle')) {
    /**
     * 设置栏目标题
     */
    function set_typeseotitle($typename = '', $seo_title = '')
    {
        // 针对没有自定义SEO标题的列表
        if (empty($seo_title)) {
            $web_name = config('tpcache.web_name');
            $seo_liststitle_format = config('tpcache.seo_liststitle_format');
            switch ($seo_liststitle_format) {
                case '1':
                    $seo_title = $typename . '_' . $web_name;
                    break;
                case '2':
                default:
                    $page = input('param.page/d', 1);
                    if ($page > 1) {
                        $typename .= "_第{$page}页";
                    }
                    $seo_title = $typename . '_' . $web_name;
                    break;
            }
        }
        return $seo_title;
    }
}