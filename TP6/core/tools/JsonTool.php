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

use think\Response;
class JsonTool
{
    private $code = 200;
    public function code(int $code) : self
    {
        $this->code = $code;
        return $this;
    }
    public function make(int $status, string $msg, ?array $data = null) : Response
    {
        $res = compact('status', 'msg');
        if (!is_null($data)) {
            $res['data'] = $data;
        }
        return Response::create($res, 'json', $this->code);
    }
    public function success($msg = 'ok', ?array $data = null) : Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'ok';
        }
        return $this->make(200, $msg, $data);
    }
    public function successful(...$args) : Response
    {
        return $this->success(...$args);
    }
    public function fail($msg = 'fail', ?array $data = null) : Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'ok';
        }
        return $this->make(400, $msg, $data);
    }
    public function status($status, $msg, $result = []) : Response
    {
        $status = strtoupper($status);
        if (is_array($msg)) {
            $result = $msg;
            $msg = 'ok';
        }
        return $this->success($msg, compact('status', 'result'));
    }
}