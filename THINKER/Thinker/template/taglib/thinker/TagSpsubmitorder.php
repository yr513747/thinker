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
// [ 提交订单 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;

class TagSpsubmitorder extends Base
{ 
    

    /**
     * 会员ID
     */
    public $users_id = 0;
    public $users    = [];
    
    //初始化
    protected function init()
    {
        
        // 会员信息
        $this->users    = session('users');
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }

    /**
     * 获取提交订单数据
     */
    public function getSpsubmitorder()
    {
        // 获取解析数据
        $querystr   = input('param.querystr/s');
        $hash   = input('param.hash/s');
        $auth_code = $this->params['global']['system_auth_code'];
        if(!empty($querystr) && md5("payment".$querystr.$auth_code) != $hash) return $this->error('无效订单！');

        // 数据解析拆分
        parse_str(mchStrCode($querystr,'DECODE'), $querydata);
        $aid = !empty($querydata['aid']) ? intval($querydata['aid']) : 0;
        $num = !empty($querydata['product_num']) ? intval($querydata['product_num']) : 0;
        $spec_value_id = !empty($querydata['spec_value_id']) ? $querydata['spec_value_id'] : '';

        if (!empty($aid)) {
            if ($num >= '1') {
                // 立即购买查询条件
                $ArchivesWhere = [
				//
                    'a.aid'  => $aid,
                    
                ];
                if (!empty($spec_value_id)) $ArchivesWhere['b.spec_value_id'] = $spec_value_id;
                
                $field = 'a.aid, a.title, a.litpic, a.users_price, a.stock_count, a.prom_type, b.spec_price, b.spec_stock, b.spec_value_id, c.spec_is_select';
                $result['list'] = Db::name('archives')->field($field)
                    ->alias('a')
                    ->join('product_spec_value b', 'a.aid = b.aid', 'LEFT')
                    ->join('product_spec_data c', 'a.aid = c.aid', 'LEFT')
                    ->where($ArchivesWhere)
                    ->limit('0, 1')
                    ->getArray();

                if (empty($result['list'][0]['spec_is_select'])) {
                    $result['list'][0]['spec_price']    = '';
                    $result['list'][0]['spec_stock']    = '';
                    $result['list'][0]['spec_value_id'] = '';
                }
                $result['list'][0]['product_num'] = $num;
                $submit_order_type = '1';
                // 加密不允许更改的数据值
                $aid  = mchStrCode($aid,'ENCODE');
                $num  = mchStrCode($num,'ENCODE');
                $type = mchStrCode('1','ENCODE'); // 1表示直接下单购买，不走购物车
                $spec_value_id  = mchStrCode($spec_value_id,'ENCODE');

                $result['list'][0]['ProductHidden'] = '<input type="hidden" name="aid" value="'.$aid.'"> <input type="hidden" name="num" value="'.$num.'"> <input type="hidden" name="type" value="'.$type.'"> <input type="hidden" name="spec_value_id[]" value="'.$spec_value_id.'">';
            } else {
                return action('user/Shop/shop_under_order', false);
               
            }
        } else {
            // 购物车查询条件
            $CartWhere = [
			//
                'a.users_id' => $this->users_id,
                
                'a.selected' => 1,
            ];
            $field = 'a.*, b.aid, b.title, b.litpic, b.users_price, b.stock_count, b.prom_type, c.spec_price, c.spec_stock, d.spec_is_select';
            $result['list'] = Db::name('shop_cart')->field($field)
                ->alias('a')
                ->join('archives b', 'a.product_id = b.aid', 'LEFT')
                ->join('product_spec_value c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                ->join('product_spec_data d', 'a.product_id = d.aid and a.spec_value_id = d.spec_value_id', 'LEFT')
                ->where($CartWhere)
                ->order('a.add_time desc')
                ->getArray();
            $submit_order_type = '0';
        }
        // 获取商城配置信息
        $ConfigData = getUsersConfigData('shop');

        // 如果产品数据为空则调用商城控制器的方式返回提示,中止运行
        if (empty($result['list'])) {
            return action('user/Shop/shop_under_order', false);
            
        }

        $controller_name = 'Product';
        $array_new = get_archives_data($result['list'],'aid');

        // 返回data拼装
        $result['data'] = [
            // 温馨提示内容,为空则不展示
            'shop_prompt'        => !empty($ConfigData['shop_prompt']) ? $ConfigData['shop_prompt'] : '',
            // 是否开启线下支付(货到付款)
            'shop_open_offline'  => !empty($ConfigData['shop_open_offline']) ? $ConfigData['shop_open_offline'] : 0,
            // 是否开启运费设置
            'shop_open_shipping' => !empty($ConfigData['shop_open_shipping']) ? $ConfigData['shop_open_shipping'] : 0,
            // 初始化总额
            'TotalAmount'        => 0,
            // 初始化总数
            'TotalNumber'        => 0,
            // 提交来源:0购物车;1直接下单
            'submit_order_type'  => $submit_order_type,
            // 1表示为虚拟订单
            'PromType'           => 1,
        ];
        $level_discount = $this->users['level_discount'];
        foreach ($result['list'] as $key => $value) {
            /* 未开启多规格则执行 */
            if (!isset($ConfigData['shop_open_spec']) || empty($ConfigData['shop_open_spec'])) {
                $value['spec_value_id'] = $value['spec_price'] = $value['spec_stock'] = 0;
                $result['list'][$key]['spec_value_id'] = $result['list'][$key]['spec_price'] = $result['list'][$key]['spec_stock'] = 0;
            }
            /* END */

            // 购物车商品存在规格并且价格不为空，则覆盖商品原来的价格
            if (!empty($value['spec_value_id']) && $value['spec_price'] >= 0) {
                // 规格价格覆盖商品原价
                $value['users_price'] = $value['spec_price'];
            }
            // 计算折扣后的价格
            if (!empty($level_discount)) {
                // 折扣率百分比 100 != $level_discount
                $discount_price = $level_discount / 100;
                // 会员折扣价
                $result['list'][$key]['users_price'] = $value['users_price'] * $discount_price;
            }

            // 购物车商品存在规格并且库存不为空，则覆盖商品原来的库存
            if (!empty($value['spec_stock'])) {
                // 规格库存覆盖商品库存
                $value['stock_count'] = $value['spec_stock'];
                $result['list'][$key]['stock_count'] = $value['spec_stock'];
            }

            if ($value['product_num'] > $value['stock_count']) {
                $result['list'][$key]['product_num'] = $value['stock_count'];
                $result['list'][$key]['stock_count'] = $value['stock_count'];
            }

            // 若库存为空则清除这条数据
            if (empty($value['stock_count'])) {
                unset($result['list'][$key]);
                continue;
            }
        }

        if (empty($result['list'])) return $this->error('商品库存不足或已过期！');

        // 产品数据处理
        foreach ($result['list'] as $key => $value) {
            if ($value['users_price'] >= 0 && !empty($value['product_num'])) {
                // 计算小计
                $result['list'][$key]['subtotal'] = sprintf("%.2f", $value['users_price'] * $value['product_num']);
                // 计算合计金额
                $result['data']['TotalAmount'] += $result['list'][$key]['subtotal'];
                $result['data']['TotalAmount'] = sprintf("%.2f", $result['data']['TotalAmount']);
                // 计算合计数量
                $result['data']['TotalNumber'] += $value['product_num'];
                // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通产品(实物产品，需要发货物流)，则为普通订单
                // 0表示为普通订单，1表示为虚拟订单，虚拟订单无需发货物流，无需选择收货地址，无需计算运费
                if (empty($value['prom_type'])) {
                    $result['data']['PromType'] = '0';
                }
            }

            // 产品页面链接
            $result['list'][$key]['arcurl'] = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$value['aid']]));

            // 图片处理
            $result['list'][$key]['litpic'] = handle_subdir(get_default_pic($value['litpic']));
             
            // 若不存在则重新定义,避免报错
            if (empty($result['list'][$key]['ProductHidden'])) {
                $result['list'][$key]['ProductHidden'] = '<input type="hidden" name="spec_value_id[]" value="'.$value['spec_value_id'].'">';
            }

            // 产品属性处理
            if (!empty($value['aid'])) { 
                $attrData   = Db::name('product_attr')->where('aid',$value['aid'])->field('attr_value,attr_id')->getArray();
                $attr_value = '';
                foreach ($attrData as $val) {
                    $attr_name  = Db::name('product_attribute')->where('attr_id',$val['attr_id'])->field('attr_name')->getOne();
                    $attr_value .= $attr_name['attr_name'].'：'.$val['attr_value'].'<br/>';
                }
                $result['list'][$key]['attr_value'] = $attr_value;
            }

            // 规格处理
            if (!empty($value['spec_value_id'])) {
                $spec_value_id = explode('_', $value['spec_value_id']);
                if (!empty($spec_value_id)) {
                    $SpecWhere = [
                        
                    ];
                    $ProductSpecData = Db::name("product_spec_data")->where('aid',$value['aid'])->where('spec_value_id','IN',$spec_value_id)->field('spec_name,spec_value')->getArray();
                    foreach ($ProductSpecData as $spec_value) {
                        $result['list'][$key]['attr_value'] .= $spec_value['spec_name'].'：'.$spec_value['spec_value'].'<br/>';
                    }
                }
            }
        }
        
        // 封装初始金额隐藏域
        $result['data']['TotalAmountOld'] = '<input type="hidden" id="TotalAmount_old" value="'.$result['data']['TotalAmount'].'">';
        // 封装订单支付方式隐藏域
        $result['data']['PayTypeHidden']  = '<input type="hidden" name="payment_method" id="payment_method" value="0">';
        // 封装添加收货地址JS
        if (isWeixin() && !isWeixinApplets()) {
            $result['data']['ShopAddAddr'] = " onclick=\"GetWeChatAddr();\" ";
            $data['shop_add_address']        = url('user/Shop/shop_get_wechat_addr');
        }else{
            $result['data']['ShopAddAddr']  = " onclick=\"ShopAddAddress();\" ";
            $data['shop_add_address']       = url('user/Shop/shop_add_address');
        }

        // 封装UL的ID,用于添加收货地址
        $result['data']['UlHtmlId']       = " id=\"UlHtml\" ";
        // 封装选择支付方式JS
        $result['data']['OnlinePay']      = " onclick=\"ColorS('zxzf')\" id=\"zxzf\"  ";
        $result['data']['DeliveryPay']    = " onclick=\"ColorS('hdfk')\" id=\"hdfk\"  ";
        // 封装运费信息
        if (empty($result['data']['shop_open_shipping'])) {
            $result['data']['Shipping'] = " 免运费 ";
        }else{
            $result['data']['Shipping'] = " <span id=\"template_money\">￥0.00</span> ";
        }
        // 封装全部产品总额ID,用于计算总额
        $result['data']['TotalAmountId'] = " id=\"TotalAmount\" ";
        // 封装返回购物车链接
        $result['data']['ReturnCartUrl'] = url('user/Shop/shop_cart_list');
        // 封装提交订单JS
        $result['data']['ShopPaymentPage'] = " onclick=\"ShopPaymentPage();\" ";
        // 封装表单验证隐藏域
       
        $token = token();
        $result['data']['TokenValue'] = " <input type=\"hidden\" name=\"__token__\" value=\"{$token}\"/> ";

        // 传入JS参数
        $data['shop_edit_address'] = url('user/Shop/shop_edit_address');
        $data['shop_del_address']  = url('user/Shop/shop_del_address');
        $data['shop_inquiry_shipping']  = url('user/Shop/shop_inquiry_shipping');
        $data['shop_payment_page'] = url('user/Shop/shop_payment_page');
        if (isWeixin() || isMobile()) {
            $data['addr_width']  = '100%';
            $data['addr_height'] = '100%';
        }else{
            $data['addr_width']  = '350px';
            $data['addr_height'] = '480px';
        }
        $data_json = json_encode($data);
        $version   = $this->params['version'];
        // 循环中第一个数据带上JS代码加载
        $result['data']['hidden'] = <<<EOF
<script type="text/javascript">
    var b1decefec6b39feb3be1064e27be2a9 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_spsubmitorder.js?v={$version}"></script>
EOF;

        if (empty($result['list'])) {
            return action('user/Shop/shop_under_order', false);
           
        }

        return $result;
    }
}