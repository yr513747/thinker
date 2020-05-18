<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace Thinker\app;

use think\Request;

use Thinker\Route as BaseRoute;

class Route extends BaseRoute
{
	

    /**
     * 路由调度，多应用解析
     * @param Request $request
     * @param Closure $withRoute
     * @return Response
     */
    public function dispatchForMultiApp(Request $request, $withRoute = null)
    {
        $this->request = $request;
        $this->host    = $this->request->host(true);
        $this->init();

        if ($withRoute) {
            //加载路由
            $withRoute();
            $dispatch = $this->check();
        } else {
            $dispatch = $this->url($this->path());
        }

        return $dispatch->getDispatch();
    }

   
}
