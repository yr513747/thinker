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
// [ 购物车列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagSpcart extends Base
{
    /**
     * 会员ID
     */
    public $users_id = 0;
    public $users = [];
    //初始化
    protected function init()
    {
        // 会员信息
        $this->users = session('users');
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }
    /**
     * 获取购物车数据
     */
    public function getSpcart($limit = '')
    {
        // 查询条件
        $condition = array();
        $condition = array('a.users_id', '=', $this->users_id);
        // 带审核稿件不查询(同等伪删除)
        $condition = array('b.arcrank', '>=', '0');
        $list = Db::name("shop_cart")
		->field('a.*,b.title,b.litpic,b.users_price,b.stock_count,c.spec_price,c.spec_stock')
		->alias('a')
		->join('archives b', 'a.product_id = b.aid', 'LEFT')
		->join('product_spec_value c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
		->where($condition)
		->limit($limit)
		->order('a.selected desc, a.add_time desc')
		->getArray();
        if (empty($list)) {
            return false;
        }
        // 规格商品价格及库存处理
        $CartIds = $CartIdsNew = [];
        foreach ($list as $key => $value) {
            if (!empty($value['spec_value_id'])) {
                if (!empty($value['spec_price'])) {
                    // 购物车商品存在规格并且价格不为空，则覆盖商品原来的价格
                    $list[$key]['users_price'] = $value['spec_price'];
                }
                if (!empty($value['spec_stock'])) {
                    // 购物车商品存在规格并且库存不为空，则覆盖商品原来的库存
                    $list[$key]['stock_count'] = $value['spec_stock'];
                    $value['stock_count'] = $value['spec_stock'];
                } else {
                    $list[$key]['stock_count'] = 0;
                    $list[$key]['selected'] = 0;
                    $list[$key]['IsSoldOut'] = 1;
                    // 已售罄
                    array_push($CartIds, $value['cart_id']);
                }
            } else {
                if (empty($value['stock_count'])) {
                    $list[$key]['stock_count'] = 0;
                    $list[$key]['selected'] = 0;
                    $list[$key]['IsSoldOut'] = 1;
                    // 已售罄
                    array_push($CartIds, $value['cart_id']);
                }
            }
            if ($value['product_num'] > $value['stock_count']) {
                $CartIdsNew[] = [
                    //
                    'cart_id' => $value['cart_id'],
                    'product_num' => $value['stock_count'],
                    'update_time' => getTime(),
                    'key' => $key,
                ];
            }
        }
        // 更新购物车库存为空的商品
        if (!empty($CartIds)) {
            Db::name("shop_cart")->where('cart_id', 'IN', $CartIds)->update(['selected' => 0, 'update_time' => getTime()]);
        }
        if (!empty($CartIdsNew)) {
            // 当购物车库存超过商品库存则执行购物车库存为商品最大库存
            foreach ($CartIdsNew as $value) {
                Db::name("shop_cart")->where('cart_id', $value['cart_id'])->update($value);
                $list[$value['key']]['product_num'] = $value['product_num'];
            }
        }
        // 订单数据处理
        $result = [
            //
            'TotalAmount' => 0,
            'TotalNumber' => 0,
            'AllSelected' => 0,
        ];
        $selected = 0;
        $controller_name = 'Product';
        $array_new = get_archives_data($list, 'product_id');
        $level_discount = $this->users['level_discount'];
        foreach ($list as $key => $value) {
            // 购物车商品存在规格并且价格不为空，则覆盖商品原来的价格
            if (!empty($level_discount)) {
                // 折扣率百分比
                $discount_price = $level_discount / 100;
                $value['users_price'] = $value['users_price'] * $discount_price;
                $list[$key]['users_price'] = $value['users_price'];
            }
            $list[$key]['subtotal'] = 0;
            if (!empty($value['users_price'])) {
                // 计算小计
                $list[$key]['subtotal'] = $value['users_price'] * $value['product_num'];
                $list[$key]['subtotal'] = sprintf("%.2f", $list[$key]['subtotal']);
                // 计算购物车中已勾选的产品总数和总额
                if (!empty($value['selected'])) {
                    // 合计金额
                    $result['TotalAmount'] += $list[$key]['subtotal'];
                    $result['TotalAmount'] = sprintf("%.2f", $result['TotalAmount']);
                    // 合计数量
                    $result['TotalNumber'] += $value['product_num'];
                    // 选中的产品个数
                    $selected++;
                }
            }
            // 产品内页地址
            $list[$key]['arcurl'] = urldecode(arcurl('home/' . $controller_name . '/view', $array_new[$value['product_id']]));
            // 图片处理
            $list[$key]['litpic'] = handle_subdir(get_default_pic($value['litpic']));
            // 产品属性处理
            $list[$key]['attr_value'] = '';
            if (!empty($value['product_id'])) {
                $attrData = Db::name("product_attr")->where('aid', $value['product_id'])->field('attr_value,attr_id')->getArray();
                $attr_value = '';
                foreach ($attrData as $val) {
                    $attr_name = Db::name("product_attribute")->where('attr_id', $val['attr_id'])->field('attr_name')->getOne();
                    $attr_value .= $attr_name['attr_name'] . '：' . $val['attr_value'] . '<br/>';
                    $list[$key]['attr_value'] = $attr_value;
                }
            }
            // 规格处理
            if (!empty($value['spec_value_id'])) {
                $spec_value_id = explode('_', $value['spec_value_id']);
                if (!empty($spec_value_id)) {
                    $SpecWhere = [];
                    $ProductSpecData = Db::name("product_spec_data")->where('aid', $value['product_id'])->where('spec_value_id', 'IN', $spec_value_id)->field('spec_name,spec_value')->getArray();
                    foreach ($ProductSpecData as $spec_value) {
                        $list[$key]['attr_value'] .= $spec_value['spec_name'] . '：' . $spec_value['spec_value'] . '<br/>';
                    }
                }
            }
            if (isset($value['IsSoldOut']) && !empty($value['IsSoldOut'])) {
                $list[$key]['CartChecked'] = " disabled='true' title='已售罄' ";
                $list[$key]['ReduceQuantity'] = " onclick=\"CartUnifiedAlgorithm('IsSoldOut');\" ";
                $list[$key]['UpdateQuantity'] = " onchange=\"CartUnifiedAlgorithm('IsSoldOut');\" value=\"0\" ";
                $list[$key]['IncreaseQuantity'] = " onclick=\"CartUnifiedAlgorithm('IsSoldOut');\" ";
            } else {
                $list[$key]['CartChecked'] = " name=\"tp_buynum\" id=\"{$value['cart_id']}_checked\" cart-id=\"{$value['cart_id']}\" product-id=\"{$value['product_id']}\" onclick=\"Checked('{$value['cart_id']}','{$value['selected']}');\" ";
                $list[$key]['ReduceQuantity'] = " onclick=\"CartUnifiedAlgorithm('{$value['stock_count']}','{$value['product_id']}','-','{$value['selected']}','{$value['spec_value_id']}','{$value['cart_id']}');\" ";
                $list[$key]['UpdateQuantity'] = " onkeyup=\"this.value=this.value.replace(/[^0-9\\.]/g,'')\" onafterpaste=\"this.value=this.value.replace(/[^0-9\\.]/g,'')\"  onchange=\"CartUnifiedAlgorithm('{$value['stock_count']}','{$value['product_id']}','change','{$value['selected']}','{$value['spec_value_id']}','{$value['cart_id']}');\" value=\"{$value['product_num']}\" id=\"{$value['cart_id']}_num\" ";
                $list[$key]['IncreaseQuantity'] = " onclick=\"CartUnifiedAlgorithm('{$value['stock_count']}','{$value['product_id']}','+','{$value['selected']}','{$value['spec_value_id']}','{$value['cart_id']}');\" ";
            }
            $list[$key]['ProductId'] = " id=\"{$value['cart_id']}_product\" ";
            $list[$key]['SubTotalId'] = " id=\"{$value['cart_id']}_subtotal\" ";
            $list[$key]['UsersPriceId'] = " id=\"{$value['cart_id']}_price\" ";
            $list[$key]['CartDel'] = " href=\"javascript:void(0);\" onclick=\"CartDel('{$value['cart_id']}','{$value['title']}');\" ";
            $list[$key]['hidden'] = <<<EOF
<input type="hidden" id="{$value['cart_id']}_Selected" value="{$value['selected']}">
<input type="hidden" id="SpecStockCount" value="{$value['spec_stock']}">
<script type="text/javascript">
\$(function(){
    if ('1' == \$('#'+{$value['cart_id']}+'_Selected').val()) {
        \$('#'+{$value['cart_id']}+'_checked').prop('checked','true');
    }
}); 
</script>
EOF;
        }
        $result['list'] = $list;
        // 是否购物车的产品全部选中
        $listcount = count($list);
        if ($listcount == $selected) {
            $result['AllSelected'] = '1';
        }
        // 下单地址
        $result['ShopOrderUrl'] = urldecode(url('user/Shop/shop_under_order'));
        $result['SubmitOrder'] = " onclick=\"SubmitOrder('{$result['ShopOrderUrl']}');\" ";
        $result['InputChecked'] = " id=\"AllChecked\" onclick=\"Checked('*','{$result['AllSelected']}');\" ";
        $result['InputHidden'] = " <input type=\"hidden\" id=\"AllSelected\" value='{$result['AllSelected']}'> ";
        $result['TotalNumberId'] = " id=\"TotalNumber\" ";
        $result['TotalAmountId'] = " id=\"TotalAmount\" ";
        // 传入JS文件的参数
        $data['cart_unified_algorithm_url'] = url('user/Shop/cart_unified_algorithm');
        $data['cart_checked_url'] = url('user/Shop/cart_checked');
        $data['cart_del_url'] = url('user/Shop/cart_del');
        $data['cart_stock_detection'] = url('user/Shop/cart_stock_detection', [], true, false, 1, 1);
        $data_json = json_encode($data);
        $version = $this->params['version'];
        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var b82ac06cf24687eba9bc5a7ba92be4c8 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_spcart.js?v={$version}"></script>
EOF;
        return $result;
    }
}