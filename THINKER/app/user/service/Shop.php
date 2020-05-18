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
// [ 商城 SERVICE ]
// --------------------------------------------------------------------------
namespace app\user\service;

use think\facade\Db;

class Shop 
{
   

    // 处理购买订单，超过指定时间修改为已订单过期，针对未付款订单
    public function UpdateShopOrderData($users_id){
        $time  = getTime() - config('global.get_shop_order_validity');
        $where = array(
            'users_id'     => $users_id,
            'order_status' => 0,
          
        );
        $data = [
            'order_status'    => 4,  // 状态修改为订单过期
            'pay_name'        => '', // 订单过期则清空支付方式标记
            'wechat_pay_type' => '', // 订单过期则清空微信支付类型标记
            'update_time'     => getTime(),
        ];

        // 查询订单id数组用于添加订单操作记录
        $OrderIds = Db::name('shop_order')->field('order_id')->where($where)->where('add_time','<',$time)->select();

        // 订单过期，更新规格数量
        model('ProductSpecValue')->SaveProducSpecValueStock($OrderIds, $users_id);

        //批量修改订单状态 
        Db::name('shop_order')->where($where)->update($data);
        
        // 添加订单操作记录
        if (!empty($OrderIds)) {
	        AddOrderAction($OrderIds,$users_id,'0','4','0','0','订单过期！','会员未在订单有效期内支付，订单过期！');
        }
    }

    // 通过商品名称模糊查询订单信息
    public function QueryOrderList($pagesize,$users_id,$keywords,$query_get){
        // 商品名称模糊查询订单明细表，获取订单主表ID
        $DetailsWhere = [
            'users_id' => $users_id,
        ];
        $DetailsWhere['product_name'] =  ['LIKE', "%{$keywords}%"];
        $DetailsData = Db::name('shop_order_details')->field('order_id')->where($DetailsWhere)->select();
        // 若查无数据，则返回false
        if (empty($DetailsData)) {
            return false;
        }

        $order_ids = '';
        // 处理订单ID，查询订单主表信息
        foreach ($DetailsData as $key => $value) {
            if ('0' < $key) {
                $order_ids .= ',';
            }
            $order_ids .= $value['order_id'];
        }
        // 查询条件
        $OrderWhere = [
            'users_id' => $users_id,
            'order_id' => ['IN', $order_ids],
        ];

        $paginate_type = 'usersthinker';
        if (isMobile()) {
            $paginate_type = 'usersmobile';
        }

        $paginate = array(
            'type'     => $paginate_type,
            'var_page' => config('paginate.var_page'),
            'query'    => $query_get,
        );

        $pages = Db::name('shop_order')
            ->field("*")
            ->where($OrderWhere)
            ->order('add_time desc')
            ->paginate($pagesize, false, $paginate);

        $data['list']  = $pages->items();
        $data['pages'] = $pages;

        return $data;
    }

    public function GetOrderIsEmpty($users_id,$keywords,$select_status){
        // 基础查询条件
        $OrderWhere = [
            'users_id' => $users_id,
        ];

        // 应用搜索条件
        if (!empty($keywords)) {
            $OrderWhere['order_code'] =  ['LIKE', "%{$keywords}%"];
        }

        // 订单状态搜索
        if (!empty($select_status)) {
            if ('dzf' === $select_status) {
                $select_status = 0;
            }
            $OrderWhere['order_status'] = $select_status;
        }

        $order = Db::name('shop_order')->where($OrderWhere)->count();
        // 查询存在数据，则返回1
        if (!empty($order)) {
            return 1; exit;
        }
        
        // 查询订单明细表
        if (empty($order) && !empty($keywords)) {
            $DetailsWhere = [
                'users_id' => $users_id,
            ];
            $DetailsWhere['product_name'] =  ['LIKE', "%{$keywords}%"];
            $DetailsData = Db::name('shop_order_details')->field('order_id')->where($DetailsWhere)->select();
            // 查询无数据，则返回0
            if (empty($DetailsData)) {
                return 0; exit;
            }

            $order_ids = '';
            // 处理订单ID，查询订单主表信息
            foreach ($DetailsData as $key => $value) {
                if (0 < $key) {
                    $order_ids .= ',';
                }
                $order_ids .= $value['order_id'];
            }
            // 查询条件
            $OrderWhere = [
                'users_id' => $users_id,
                'order_id' => ['IN', $order_ids],
            ];

            $order2 = Db::name('shop_order')->where($OrderWhere)->count();
            if (!empty($order2)) {
                return 1; exit;
            }else{
                return 0; exit;
            }
        }
    }

    // 获取微信公众号access_token
    // 传入微信公众号appid
    // 传入微信公众号secret
    // 返回data
    public function GetWeChatAccessToken($appid,$secret){
        // 获取公众号access_token，接口限制10万次/天
        $time = getTime();
        $get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $TokenData = httpRequest($get_token_url);
        $TokenData = json_decode($TokenData, true);
        if (!empty($TokenData['access_token'])) {
            // 存入缓存配置
            $WechatData  = [
                'wechat_token_value' => $TokenData['access_token'],
                'wechat_token_time'  => $time,
            ];
            getUsersConfigData('wechat',$WechatData);
            $data = [
                'status' => true,
                'token'  => $WechatData['wechat_token_value'],
            ];
        }else{
            $data = [
                'status' => false,
                'prompt' => '错误提示：101，后台配置配置AppId或AppSecret不正确，请检查！',
            ];
        }
        return $data;
    }

    // 获取微信公众号jsapi_ticket
    // 传入微信公众号accesstoken
    // 返回data
    public function GetWeChatJsapiTicket($accesstoken){
        // 获取公众号jsapi_ticket
        $time = getTime();
        $get_ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accesstoken.'&type=jsapi';
        $TicketData = httpRequest($get_ticket_url);
        $TicketData = json_decode($TicketData, true);
        if (!empty($TicketData['ticket'])) {
            // 存入缓存配置
            $WechatData  = [
                'wechat_ticket_value' => $TicketData['ticket'],
                'wechat_ticket_time'  => $time,
            ];
            getUsersConfigData('wechat',$WechatData);
            $data = [
                'status' => true,
                'ticket' => $WechatData['wechat_ticket_value'],
            ];
        }else{
            $data = [
                'status' => false,
                'prompt' => '错误提示：102，后台配置配置AppId或AppSecret不正确，请检查！',
            ];
        }
        return $data;
    }

    // 获取随机字符串
    // 长度 length
    // 结果 str
    public function GetRandomString($length){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    // 产品属性处理
    public function ProductAttrProcessing($value = array())
    {
        $attr_value = '';
        $AttrWhere = [
            'a.aid'     => $value['aid'],
            
        ];
        $AttrData = Db::name('product_attr')
            ->alias('a')
            ->field('a.attr_value,b.attr_name')
            ->join('product_attribute b', 'a.attr_id = b.attr_id', 'LEFT')
            ->where($AttrWhere)
            ->order('b.sort_order asc, a.attr_id asc')
            ->select();
        foreach ($AttrData as $val) {
            $attr_value .= $val['attr_name'].'：'.$val['attr_value'].'<br/>';
        }
        return $attr_value;
    }

    // 产品规格处理
    public function ProductSpecProcessing($value = array())
    {
        $spec_value_s = '';
        if (!empty($value['spec_value_id'])) {
            $spec_value_id = explode('_', $value['spec_value_id']);
            if (!empty($spec_value_id)) {
                $SpecWhere = [
                    'aid'           => $value['aid'],
                    'spec_value_id' => ['IN',$spec_value_id],
                ];
                $ProductSpecData = Db::name("product_spec_data")->where($SpecWhere)->field('spec_name,spec_value')->select();
                foreach ($ProductSpecData as $spec_value) {
                    $spec_value_s .= $spec_value['spec_name'].'：'.$spec_value['spec_value'].'<br/>';
                }
            }
        }
        return $spec_value_s;
    }

    // 产品库存处理
    public function ProductStockProcessing($SpecValue = array())
    {   
        $SpecUpData = []; // 有规格
        $ArcUpData  = []; // 无规格
        foreach ($SpecValue as $key => $value) {
            if (!empty($value['value_id'])) {
                $SpecUpData[] = [
                    'value_id'   => $value['value_id'],
                    'spec_stock' => Db::raw('spec_stock-'.($value['quantity'])),
                    'spec_sales_num' => Db::raw('spec_sales_num+'.($value['quantity'])),
                ];
                
                $ArcUpData[] = [
                    'aid'         => $value['aid'],
                    'stock_count' => Db::raw('stock_count-' . ($value['quantity'])),
                    'sales_num'   => Db::raw('sales_num+' . ($value['quantity']))
                ];
            }else{
                $ArcUpData[] = [
                    'aid'         => $value['aid'],
                    'stock_count' => Db::raw('stock_count-'.($value['quantity'])),
                    'sales_num'   => Db::raw('sales_num+' . ($value['quantity']))
                ];
            }
        }

        // 更新规格库存销量
        if (!empty($SpecUpData)) model('ProductSpecValue')->saveAll($SpecUpData);

        // 更新商品库存销量
        if (!empty($ArcUpData)) model('Archives')->saveAll($ArcUpData);
    }
}