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

final class TplTool
{
    public function render(object $context, string $tpl)
    {
        if (!is_file($tpl)) {
            return null;
        }
        $closure = function ($tpl) {
            ob_start();
            include $tpl;
            return ob_end_flush();
        };
        $closure = $closure->bindTo($context, $context);
        $closure($tpl);
    }
}