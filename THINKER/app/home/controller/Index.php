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
// [ 首页 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use app\user\logic\PayLogic;
class Index extends BaseController
{
    protected function initialize()
    {
        parent::initialize();
        $this->alipayReturn();
        $this->expressReturn();
    }
    public function index()
    {
        // 首页焦点
        $active = input('param.active/s');
        if (empty($active)) {
            $this->request->withGet(['active' => 'Index']);
        }
        // 获取当前页面URL
        $result['pageurl'] = url('home/Index/index');
        $thinker = array('field' => $result);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
        return $this->fetch(":index");
    }
    /**
     * 支付宝回调
     */
    private function alipayReturn()
    {
        $param = input('param.');
        if (isset($param['transaction_type']) && isset($param['is_ailpay_notify'])) {
            // 跳转处理回调信息
            $pay_logic = new PayLogic();
            $pay_logic->alipay_return();
        }
    }
    /**
     * 快递100返回时，重定向关闭父级弹框
     */
    private function expressReturn()
    {
        $coname = input('param.coname/s', '');
        if (!empty($coname) || 'user' == $this->params['app_name']) {
            if (isWeixin()) {
                return $this->redirect('user/Shop/shop_centre');
            } else {
                return $this->redirect('api/Rewrite/close_parent_layer');
            }
        }
    }
}