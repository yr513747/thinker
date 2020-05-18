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
// [ 在内容页模板显示下载次数 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagDowncount extends Base
{
    public $aid = 0;
    //初始化
    protected function init()
    {
        $this->aid = input('param.aid/d', 0);
    }
    /**
     * 在内容页模板显示下载次数
     */
    public function getDowncount($aid = '')
    {
        $aid = !empty($aid) ? intval($aid) : $this->aid;
        if (empty($aid)) {
            return '标签downcount报错：缺少属性 aid 值。';
        }
        static $downcount_js = null;
        if (null === $downcount_js) {
            $downcount_url = url("api/Ajax/downcount");
            $downcount_js = <<<EOF
<script type="text/javascript">
    function tag_downcount(aid)
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("get", "{$downcount_url}?aid="+aid+"&_ajax=1", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        //步骤三:发送请求
        ajax.send();
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("thinker_downcount_"+aid).innerHTML = ajax.responseText;
          　}
        } 
    }
</script>
EOF;
        } else {
            $downcount_js = '';
        }
        $parseStr = <<<EOF
<i id="thinker_downcount_{$aid}" class="thinker_downcount" style="font-style:normal"></i> 
<script type="text/javascript">tag_downcount({$aid});</script>
EOF;
        $parseStr = $downcount_js . $parseStr;
        return $parseStr;
    }
}