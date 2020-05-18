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
// [ 工具 ]
// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
declare (strict_types=1);

namespace Thinker\utils;

class Tools
{
    private static $_render;
    private static $_preview;
	
    public static function color($color, $rgb = false)
    {
        if ($rgb) {
            $color = str_replace('#', '', $color);
            if (strlen($color) > 3) {
                $rgb = array('r' => hexdec(substr($color, 0, 2)), 'g' => hexdec(substr($color, 2, 2)), 'b' => hexdec(substr($color, 4, 2)));
            } else {
                $r = substr($color, 0, 1) . substr($color, 0, 1);
                $g = substr($color, 1, 1) . substr($color, 1, 1);
                $b = substr($color, 2, 1) . substr($color, 2, 1);
                $rgb = array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
            }
            return $rgb;
        } else {
            if (strlen($color) && substr($color, 0, 1) != '#') {
                $color = '#' . $color;
            }
            return $color;
        }
    }
	
    public static function is_spider()
    {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spiders = array(
            'Googlebot',
            // Google
            'Baiduspider',
            // 百度
            '360Spider',
            // 360
            'bingbot',
            // Bing
            'Sogou web spider',
        );
        foreach ($spiders as $spider) {
            $spider = strtolower($spider);
            //查找有没有出现过
            if (strpos($userAgent, $spider) !== false) {
                return $spider;
            }
        }
    }
}