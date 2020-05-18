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
// [ 留言表单 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagGuestbookform extends Base
{
    public $tid = '';
    //初始化
    protected function init()
    {
        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');
        $this->tid = $this->getTrueTypeid($this->tid);
    }
    /**
     * 获取留言表单
     */
    public function getGuestbookform($typeid = '', $type = 'default', $beforeSubmit = '')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        if (empty($typeid)) {
            echo '标签guestbookform报错：缺少属性 typeid 值。';
            return false;
        }
        $result = false;
        /*当前栏目下的表单属性*/
        $row = Db::name('guestbook_attribute')->where('typeid', $typeid)->where('is_del', 0)->order('sort_order asc, attr_id asc')->getArray();
        /*--end*/
        if (empty($row)) {
            echo '标签guestbookform报错：该栏目下没有新增表单属性。';
            return false;
        } else {
            $newAttribute = array();
            $attr_input_type_1 = 1;
            // 兼容之前的版本
            //检测规则
            $validate_type_list = config("global.validate_type_list");
            $check_js = '';
            foreach ($row as $key => $val) {
                $attr_id = $val['attr_id'];
                /*字段名称*/
                $name = 'attr_' . $attr_id;
                if (in_array($val['attr_input_type'], [4])) {
                    // 多选框、上传图片或附件
                    $newAttribute[$name] = $name . "[]";
                } else {
                    $newAttribute[$name] = $name;
                }
                /*--end*/
                /*表单提示文字*/
                $itemname = 'itemname_' . $attr_id;
                $newAttribute[$itemname] = $val['attr_name'];
                /*--end*/
                /*针对下拉选择框*/
                if (in_array($val['attr_input_type'], [1, 3, 4])) {
                    $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                    $options = array();
                    foreach ($tmp_option_val as $k2 => $v2) {
                        $tmp_val = array('value' => $v2);
                        array_push($options, $tmp_val);
                    }
                    $newAttribute['options_' . $attr_id] = $options;
                    /*兼容之前的版本*/
                    if (1 == $attr_input_type_1) {
                        $newAttribute['options'] = $options;
                    }
                    ++$attr_input_type_1;
                    /*--end*/
                }
                /*--end*/
                //是否必填（js判断）
                if (!empty($val['required'])) {
                    $check_js .= "\r\n                        if(x[i].name == '" . 'attr_' . $val['attr_id'] . "' && x[i].value.length == 0){\r\n                            alert('" . $val['attr_name'] . "不能为空！');\r\n                            return false;\r\n                        }\r\n                    ";
                }
                //是否正则限制（js判断）
                if (!empty($val['validate_type']) && !empty($validate_type_list[$val['validate_type']]['value'])) {
                    $check_js .= " \r\n                    if(x[i].name == '" . 'attr_' . $val['attr_id'] . "' && !(" . $validate_type_list[$val['validate_type']]['value'] . ".test( x[i].value))){\r\n                        alert('" . $val['attr_name'] . "格式不正确！');\r\n                        return false;\r\n                    }\r\n                   ";
                }
            }
            if (!empty($check_js)) {
                $check_js = <<<EOF
    var x = elements;
    for (var i=0;i<x.length;i++) {
        {$check_js}
    }
EOF;
            }
            if (!empty($beforeSubmit)) {
                $beforeSubmit = "try{if(false=={$beforeSubmit}()){return false;}}catch(e){}";
            }
            $token_id = md5('guestbookform_token_' . $typeid . md5(getTime() . uniqid(mt_rand(), TRUE)));
            
            $submit = 'submit' . $token_id;
            $thinker_fleshVerify_url = url('api/Ajax/captcha', ['type' => 'guestbook']);
            $token_field = token_field();
            $tokenStr = <<<EOF
<script type="text/javascript">
    function {$submit}(elements)
    {
        {$check_js}
        {$beforeSubmit}
        elements.submit();
    }

    function thinker_fleshVerify(id)
    {
        var src = "{$thinker_fleshVerify_url}";
        src += "&r="+ Math.floor(Math.random()*100);
        document.getElementById(id).src = src;
    }
</script>
EOF;
            $hidden = '<input type="hidden" name="typeid" value="' . $typeid . '" />' . $token_field . $tokenStr;
            $newAttribute['hidden'] = $hidden;
            $action = url('home/Lists/gbookSubmit');
            $newAttribute['action'] = $action;
            $newAttribute['formhidden'] = ' enctype="multipart/form-data" ';
            $newAttribute['submit'] = "return {$submit}(this);";
            /*验证码处理*/
            // 默认开启验证码
            $IsVertify = 1;
            $guestbook_captcha = config('captcha.guestbook');
            if (!function_exists('imagettftext') || empty($guestbook_captcha['is_on'])) {
                $IsVertify = 0;
                // 函数不存在，不符合开启的条件
            }
            $newAttribute['IsVertify'] = $IsVertify;
            if (1 == $IsVertify) {
                // 留言验证码数据
                $VertifyUrl = url('api/Ajax/captcha', ['type' => 'guestbook', 'r' => mt_rand(0, 10000)]);
                $newAttribute['VertifyData'] = " src='{$VertifyUrl}' id='verify_{$token_id}' onclick='thinker_fleshVerify(\"verify_{$token_id}\");' ";
            }
            /* END */
            $result[0] = $newAttribute;
        }
        return $result;
    }
    /**
     * 动态获取留言栏目属性输入框 根据不同的数据返回不同的输入框类型
     * @param int $typeid 留言栏目id
     */
    public function getAttrInput($typeid)
    {
        $attributeList = Db::name('guestbook_attribute')->where("typeid", $typeid)->order('sort_order asc')->getArray();
        $form_arr = array();
        $i = 1;
        foreach ($attributeList as $key => $val) {
            $str = "";
            switch ($val['attr_input_type']) {
                case '0':
                    $str = "<input class='guest-input " . $this->inputstyle . "' id='attr_" . $i . "' type='text' value='" . $val['attr_values'] . "' name='attr_{$val['attr_id']}[]' placeholder='" . $val['attr_name'] . "'/>";
                    break;
                case '1':
                    $str = "<select class='guest-select " . $this->inputstyle . "' id='attr_" . $i . "' name='attr_{$val['attr_id']}[]'><option value=''>无</option>";
                    $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                    foreach ($tmp_option_val as $k2 => $v2) {
                        $str .= "<option value='{$v2}'>{$v2}</option>";
                    }
                    $str .= "</select>";
                    break;
                case '2':
                    $str = "<textarea class='guest-textarea " . $this->inputstyle . "' id='attr_" . $i . "' cols='40' rows='3' name='attr_{$val['attr_id']}[]' placeholder='" . $val['attr_name'] . "'>" . $val['attr_values'] . "</textarea>";
                    break;
                default:
                    # code...
                    break;
            }
            $i++;
            $form_arr[$key] = array('value' => $str, 'attr_name' => $val['attr_name']);
        }
        return $form_arr;
    }
}