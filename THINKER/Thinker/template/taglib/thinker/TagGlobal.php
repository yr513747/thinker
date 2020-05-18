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
// [ 全局变量 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagGlobal extends Base
{
    /**
     * 获取全局变量
     * @access public
     * @param  string  $name
     * @return mixed
     */
    public function getGlobal($name = '')
    {
        if (empty($name)) {
            return '标签global报错：缺少属性 name 。';
        }
        $param = explode('|', $name);
        $name = trim($param[0], '$');
        $value = '';
        // PC端与手机端的变量名自适应，可彼此通用
        if (in_array($name, ['web_thirdcode_pc', 'web_thirdcode_wap'])) {
            $name = 'web_thirdcode_' . (isMobile() ? 'wap' : 'pc');
        }
        $globalArr = $this->getDataAndValue($name);
        if (!empty($globalArr['data'])) {
            $value = $globalArr['value'];
            $globalData = $globalArr['data'];
            switch ($name) {
                // case 'web_basehost':
                case 'web_cmsurl':
                   
                    $value = url('home/Index/index');
                    
                    break;
                case 'web_recordnum':
                    if (!empty($value)) {
                        $value = '<a href="http://www.beian.miit.gov.cn/" rel="nofollow" target="_blank">' . $value . '</a>';
                    }
                    break;
                case 'web_thirdcode_pc':
                case 'web_thirdcode_wap':
                    $value = '';
                    break;
                default:
                    // 支持子目录
                    $value = handle_subdir($value, 'html');
                    $value = handle_subdir($value);
                    break;
            }
            foreach ($param as $key => $val) {
                if ($key == 0) {
                    continue;
                }
                $value = $val($value);
            }
            $value = htmlspecialchars_decode($value);
        }
        return $value;
    }
    /**
     * 返回变量数组集合
     * @access private
     * @param  string  $name
     * @return mixed
     */
    private function getDataAndValue($name)
    {
        static $globalTpCache = null;
        null === $globalTpCache && ($globalTpCache = $this->params['global']);
        $value = isset($globalTpCache[$name]) ? $globalTpCache[$name] : '';
        $tmpName = 'web_copyright';
        //加密
        if ($name == $tmpName) {
            $is_author_key = 'web_is_authortoken';
            //加密
            if (!empty($globalTpCache[$is_author_key]) && -1 == intval($globalTpCache[$is_author_key])) {
                $value .= '<a href="http://www.thinker.com/plus/powerby.php" target="_blank"> Powered by Thinker</a>';
                //加密
            }
        }
        return ['value' => $value, 'data' => $globalTpCache];
    }
}