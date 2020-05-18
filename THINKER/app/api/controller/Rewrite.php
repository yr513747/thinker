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
// [ Ajax异步类 ]
// --------------------------------------------------------------------------
namespace app\api\controller;

use app\common\model\Config as ConfigModel;
class Rewrite extends BaseController
{
    /**
     * 设置隐藏index.php
     * @access public
     * @return mixed
     */
    public function setInlet()
    {
        $seo_inlet = input('param.seo_inlet/d', 1);
        ConfigModel::tpCache('seo', ['seo_inlet' => $seo_inlet]);
        @ob_clean();
        return 'Congratulations on passing';
    }
    /**
     * 关闭父弹框
     * @access public
     * @return mixed
     */
    public function closeParentLayer()
    {
        $version = $this->params['version'];
        $str = <<<EOF
    <script type="text/javascript" src="{$this->web_root}/static/common/js/jquery.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$this->web_root}/plugins/layer-v3.1.0/layer.js?v={$version}"></script>
    <script type="text/javascript">
        \$(function(){
            parent.layer.closeAll();
        });
    </script>
EOF;
        return $str;
    }
}