<?php

declare(strict_types=1);

namespace Thinker\weapp\middleware;

use think\App;

class Weapp
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app  = $app;
    }

    /**
     * 插件中间件
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        hook('weapp_middleware', $request);

        return $next($request);
    }
}