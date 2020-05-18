<?php
declare (strict_types=1);
namespace Thinker;

class Tpl
{
    public function render($context, $tpl)
    {
        $closure = function ($tpl) {
            ob_start();
            include $tpl;
            return ob_end_flush();
        };
        $closure = $closure->bindTo($context, $context);
        $closure($tpl);
    }
}