<?php
namespace app\install;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;
/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];
    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception) : void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }
    /**
     * 异常处理
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e) : Response
    {
        $this->isJson = $this->getResponseType($request);
        // 返回JSON数据格式到客户端 包含状态信息 [当url_common_param为false时是无法获取到$_GET的数据的，故使用Request来获取<xiaobo.sun@qq.com>]
        $var_jsonp_handler = input('callback', '');
        $handler = !empty($var_jsonp_handler) ? $var_jsonp_handler : input('jsonpReturn', '');
        // ajax请求处理
        if ($this->isJson) {
            // 参数验证错误
            if ($e instanceof ValidateException) {
                $msg = $e->getError();
                $code = 422;
            }
            // 请求异常
            if ($e instanceof HttpException) {
                $msg = $e->getMessage();
                $code = $e->getStatusCode();
            }
            // 针对数据库异常
            if ($e instanceof \PDOException) {
                $error = $e->errorInfo;
                $code = $e->getCode();
                $code1 = isset($error[1]) ? $error[1] : 0;
                $code2 = isset($error[2]) ? $error[2] : '';
                // 提高错误提示的友好性
                $errcode = "{$code}:{$code1}";
                $mysqlcode = array(
                    '1045:0' => "请仔细核对数据库账号和密码。",
                    '1049:0' => "数据库不存在，请仔细检查核对。",
                    '2002:0' => "连接数据库失败",
                    '22001:1406' => "插入字段长度超过设定的长度，请联系技术处理。",
                    '42000:1055' => "数据库sql_mode模式对GROUP BY聚合操作",
                    '42S02:1146' => "数据表或视图不存在，请联系技术处理。",
                    'HY000:1017' => "数据表或视图不存在，请联系技术处理。",
                    'HY000:1030' => "磁盘临时空间不够导致，请联系空间服务商，进行清空/tmp目录，或者修改my.cnf中的tmpdir参数，指向具有足够空间的目录。",
                    'HY000:1045' => "数据库配置参数不对，请仔细检查核对。",
                    'HY000:1049' => "数据库不存在，请仔细检查核对。",
                    'HY000:2002' => "你的主机不支持 localhost 连接数据",
                    'HY000:2013' => "可能MySQL服务器不支持127.0.0.1连接",
                    'HY000:1290' => "请重启MySql数据库，或者联系空间服务商处理。",
                );
                $msg = $e->getMessage();
                $msg = iconv('GB2312', 'UTF-8', $msg);
                if (!empty($mysqlcode[$errcode])) {
                    $msg = $mysqlcode[$errcode];
                }
            }
            if (!isset($code)) {
                $code = 500;
            }
            if (!isset($msg)) {
                if (method_exists($e, 'getMessage')) {
                    $msg = $e->getMessage();
                    // 转化编码
                    $msg = iconv('GB2312', 'UTF-8', $msg);
                } else {
                    $msg = '服务器错误';
                }
            }
            $result = array('msg' => $msg, 'code' => $code);
            if ($handler) {
                $response = Response::create($result, 'jsonp', 200);
            } else {
                $response = Response::create($result, 'json', 200);
            }
            return $response;
        }
        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    public function getResponseType($request)
    {
        if ($request->isJson() || $request->isAjax()) {
            return true;
        }
        // 兼容 Cors Postman 请求
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Postman') || isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'cors') {
            return true;
        }
        return false;
    }
}