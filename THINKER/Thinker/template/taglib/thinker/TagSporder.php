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
// [ 订单明细 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagSporder extends Base
{
    public $users_id = 0;
    //初始化
    protected function init()
    {
        $this->users_id = session('users_id');
    }
    /**
     * 获取订单明细数据
     */
    public function getSporder($order_id)
    {
        if (empty($order_id)) {
            $order_id = input('param.order_id');
        }
        if (!empty($order_id)) {
            // 公共条件
            $Where = [
                //
                'order_id' => $order_id,
                'users_id' => $this->users_id,
            ];
            // 订单主表
            $result['OrderData'] = Db::name("shop_order")->where($Where)->getOne();
            //虚拟商品拼接回复
            $virtual_delivery = '';
            // 获取当前链接及参数，用于手机端查询快递时返回页面
            $ReturnUrl = request()->url(true);
            // 封装查询物流链接
            $result['OrderData']['LogisticsInquiry'] = $MobileExpressUrl = '';
            if (('2' == $result['OrderData']['order_status'] || '3' == $result['OrderData']['order_status']) && empty($result['OrderData']['prom_type'])) {
                // 移动端查询物流链接
                $result['OrderData']['MobileExpressUrl'] = "//m.kuaidi100.com/app/query/?com=" . $result['OrderData']['express_code'] . "&nu=" . $result['OrderData']['express_order'] . "&callbackurl=" . $ReturnUrl;
                $MobileExpressUrl = "//m.kuaidi100.com/index_all.html?type=" . $result['OrderData']['express_code'] . "&postid=" . $result['OrderData']['express_order'] . "&callbackurl=" . $ReturnUrl;
                $result['OrderData']['LogisticsInquiry'] = " onclick=\"LogisticsInquiry('{$MobileExpressUrl}');\" ";
            }
            // 是否移动端，1表示移动端，0表示PC端
            $result['OrderData']['IsMobile'] = isMobile() ? 1 : 0;
            // 获取订单状态列表
            $order_status_arr = config('global.order_status_arr');
            $result['OrderData']['order_status_name'] = $order_status_arr[$result['OrderData']['order_status']];
            $result['OrderData']['TotalAmount'] = '0';
            if (!empty($result['OrderData'])) {
                // 订单明细表
                $result['DetailsData'] = Db::name("shop_order_details")->order('product_price desc, product_name desc')->where($Where)->getArray();
                $controller_name = 'Product';
                $array_new = get_archives_data($result['DetailsData'], 'product_id');
                $virtual_delivery_status = false;
                // 产品处理
                foreach ($result['DetailsData'] as $key => $value) {
                    //虚拟需要自动发货的商品卖家回复拼接
                    if ($value['prom_type'] == 2 && 2 <= intval($result['OrderData']['order_status'])) {
                        $virtual_delivery_status = true;
                        //查询商品名称
                        $product_title = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                        //查网盘信息
                        $netdisk = Db::name("product_netdisk")->where('aid', $value['product_id'])->getOne();
                        if ($netdisk) {
                            $virtual_delivery .= "<b>商品标题：</b>" . $product_title . "</br>";
                            $virtual_delivery .= "<b>网盘地址：</b> <a target='_blank' href=" . $netdisk['netdisk_url'] . ">" . $netdisk['netdisk_url'] . "</a></br>";
                            if (!empty($netdisk['netdisk_pwd'])) {
                                $virtual_delivery .= "<b>提取码：</b>" . $netdisk['netdisk_pwd'] . "</br>";
                            }
                            if (!empty($netdisk['unzip_pwd'])) {
                                $virtual_delivery .= "<b>解压密码：</b>" . $netdisk['unzip_pwd'] . "</br>";
                            }
                            $virtual_delivery .= "--------------------</br>";
                        }
                    } elseif ($value['prom_type'] == 3 && 2 <= intval($result['OrderData']['order_status'])) {
                        $virtual_delivery_status = true;
                        //查询商品名称
                        $product_title = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                        //查网盘信息
                        $netdisk = Db::name("product_netdisk")->where('aid', $value['product_id'])->getOne();
                        if ($netdisk) {
                            $virtual_delivery .= "<b>商品标题：</b>" . $product_title . "</br>";
                            $virtual_delivery .= "<b>文本内容：</b>" . $netdisk['text_content'] . "</br>";
                            $virtual_delivery .= "--------------------</br>";
                        }
                    } elseif ($value['prom_type'] == 1) {
                        $virtual_delivery_status = true;
                        //查询商品名称
                        if (!empty($result['OrderData']['virtual_delivery'])) {
                            $product_title = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                            $virtual_delivery .= "<b>商品标题：</b>" . $product_title . "</br>";
                            $virtual_delivery .= $result['OrderData']['virtual_delivery'] . "</br>";
                            $virtual_delivery .= "--------------------</br>";
                        }
                    }
                    // 产品属性处理
                    $value['data'] = unserialize($value['data']);
                    $attr_value = htmlspecialchars_decode($value['data']['attr_value']);
                    $attr_value = htmlspecialchars_decode($attr_value);
                    $spec_value = htmlspecialchars_decode($value['data']['spec_value']);
                    $spec_value = htmlspecialchars_decode($spec_value);
                    $result['DetailsData'][$key]['data'] = $attr_value . $spec_value;
                    // 产品内页地址
                    $result['DetailsData'][$key]['arcurl'] = urldecode(arcurl('home/' . $controller_name . '/view', $array_new[$value['product_id']]));
                    // 图片处理
                    $result['DetailsData'][$key]['litpic'] = handle_subdir(get_default_pic($value['litpic']));
                    // 小计
                    $result['DetailsData'][$key]['subtotal'] = $value['product_price'] * $value['num'];
                    $result['DetailsData'][$key]['subtotal'] = sprintf("%.2f", $result['DetailsData'][$key]['subtotal']);
                    // 合计金额
                    $result['OrderData']['TotalAmount'] += $result['DetailsData'][$key]['subtotal'];
                    $result['OrderData']['TotalAmount'] = sprintf("%.2f", $result['OrderData']['TotalAmount']);
                }
                if (!empty($virtual_delivery)) {
                    $result['OrderData']['virtual_delivery'] = $virtual_delivery;
                }
                if (empty($result['OrderData']['order_status'])) {
                    // 付款地址处理，对ID和订单号加密，拼装url路径
                    $querydata = [
                        //
                        'order_id' => $result['OrderData']['order_id'],
                        'order_code' => $result['OrderData']['order_code'],
                    ];
                    /*加密防止利用序列化注入SQL*/
                    $querystr = '';
                    foreach ($querydata as $_qk => $_qv) {
                        $querystr .= $querystr ? "&{$_qk}={$_qv}" : "{$_qk}={$_qv}";
                    }
                    $querystr = str_replace('=', '', mchStrCode($querystr));
                    $auth_code = $this->params['global']['system_auth_code'];
                    $hash = md5("payment" . $querystr . $auth_code);
                    /*end*/
                    $result['OrderData']['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail', ['querystr' => $querystr, 'hash' => $hash]));
                }
                // 处理订单主表的地址数据
                $result['OrderData']['country'] = '中国';
                $result['OrderData']['province'] = get_province_name($result['OrderData']['province']);
                $result['OrderData']['city'] = get_city_name($result['OrderData']['city']);
                $result['OrderData']['district'] = get_area_name($result['OrderData']['district']);
                // 封装获取订单支付方式名称
                $pay_method_arr = config('global.pay_method_arr');
                if (!empty($result['OrderData']['payment_method'])) {
                    $result['OrderData']['pay_name'] = '货到付款（ 快递代收 ）';
                } else {
                    $pay_name = '未支付';
                    if (!empty($result['OrderData']['pay_name'])) {
                        $pay_name = $pay_method_arr[$result['OrderData']['pay_name']];
                    }
                    $result['OrderData']['pay_name'] = '在线支付（ ' . $pay_name . ' ）';
                }
                // 封装取消订单JS
                $result['OrderData']['CancelOrder'] = " onclick=\"CancelOrder('{$order_id}');\" ";
                // 封装收货地址
                $result['OrderData']['ConsigneeInfo'] = $result['OrderData']['consignee'] . ' ' . $result['OrderData']['mobile'] . ' ' . $result['OrderData']['country'] . ' ' . $result['OrderData']['province'] . ' ' . $result['OrderData']['city'] . ' ' . $result['OrderData']['district'] . ' ' . $result['OrderData']['address'];
                // 传入JS参数
                $data['shop_order_cancel'] = url('user/Shop/shop_order_cancel');
                $data_json = json_encode($data);
                $version = $this->params['version'];
                // 循环中第一个数据带上JS代码加载
                if ($result['OrderData']['prom_type'] == 0 && $virtual_delivery_status) {
                    $result['OrderData']['hidden'] = <<<EOF
<div style="display: none;" id="virtual_delivery_1575423534">
<div class='col-xs-4 col-md-3 col-xl-2 text-sm-left order-info-name'>商家回复 :</div><div class='col-xs-8 col-md-9 col-xl-10' >{$result['OrderData']['virtual_delivery']}</div>
</div>
<script type="text/javascript">
    var eeb8a85ee533f74014310e0c0d12778 = {$data_json};
    var panel_body = document.getElementsByClassName("panel-body order-info")[0];
    var dom=document.createElement('div');
    dom.className='row m-t-10';
    dom.innerHTML=document.getElementById('virtual_delivery_1575423534').innerHTML;
    panel_body.appendChild(dom);
</script>
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_sporder.js?v={$version}"></script>
EOF;
                } else {
                    $result['OrderData']['hidden'] = <<<EOF
<script type="text/javascript">
    var eeb8a85ee533f74014310e0c0d12778 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_sporder.js?v={$version}"></script>
EOF;
                }
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}