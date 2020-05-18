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
// [ 系统环境检查 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\middleware;

use Closure;
use think\Response;
use think\Request;
class SystemEnvCheckMiddleware
{
    /**
     * 中间件执行入口
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // 环境检查
        $result = $this->EnvironmentCheck($request);
        if ($result['code'] !== 0) {
            $var_jsonp_handler = input('callback', '');
            $handler = !empty($var_jsonp_handler) ? $var_jsonp_handler : input('jsonpReturn', '');
            if ($handler) {
                $response = Response::create($result, 'jsonp');
            } else {
                $response = Response::create($result, 'json');
            }
        }
        return $response;
    }
    /**
     * 环境校验
     * @access public
     * @param Request $request
     * @return array
     */
    public function EnvironmentCheck(Request $request) : array
    {
        if ($request->isAjax() || $request->isJson()) {
            // 请求参数数量校验是否超出php.ini限制
            $max_input_vars = intval(ini_get('max_input_vars')) - 5;
            $params_counbt = count(input('post.'));
            if ($params_counbt >= $max_input_vars) {
                return $result = ['msg' => '请求参数数量已超出php.ini限制[max_input_vars]', 'code' => -1000];
            }
        }
        return $result = ['msg' => '校验通过', 'code' => 0];
    }
}