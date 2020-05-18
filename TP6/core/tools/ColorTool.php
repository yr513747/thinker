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
declare (strict_types=1);
namespace core\tools;

class ColorTool
{
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
}