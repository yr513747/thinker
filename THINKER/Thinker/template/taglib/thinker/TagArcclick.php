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
// [ 在内容页模板追加显示浏览量 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagArcclick extends Base
{
    public $aid = 0;
    //初始化
    protected function init()
    {
        $this->aid = input('param.aid/d', 0);
    }
    public function getArcclick($aid = '', $value = '', $type = '')
    {
        $aid = !empty($aid) ? intval($aid) : $this->aid;
        if (empty($aid)) {
            return '标签arcclick报错：缺少属性 aid 值。';
        }
        // 内容页或者其他页
        if (empty($type)) {
            if (!empty($this->aid)) {
                $type = 'view';
            } else {
                $type = 'lists';
            }
        }
        static $arcclick_js = null;
        if (null === $arcclick_js) {
            $arcclick_url = url("api/Ajax/arcclick");
            $arcclick_js = <<<EOF
<script type="text/javascript">
    function tag_arcclick(aid)
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("get", "{$arcclick_url}?aid="+aid+"&type={$type}&_ajax=1", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        //步骤三:发送请求
        ajax.send();
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("thinker_arcclick_"+aid).innerHTML = ajax.responseText;
          　}
        } 
    }
</script>
EOF;
        } else {
            $arcclick_js = '';
        }
        $parseStr = <<<EOF
<i id="thinker_arcclick_{$aid}" class="thinker_arcclick" style="font-style:normal"></i> 
<script type="text/javascript">tag_arcclick({$aid});</script>
EOF;
        $parseStr = $arcclick_js . $parseStr;
        return $parseStr;
    }
}