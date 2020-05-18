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
// [ 字段逻辑定义 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\logic;

use Thinker\basic\BaseLogic;
use think\facade\Db;

class FieldLogic extends BaseLogic
{
    /**
     * 查询解析模型数据用以页面展示
     * @param array $data 表数据
     * @param intval $channel_id 模型ID
     * @param array $batch 是否批量列表
     */
    public function getChannelFieldList($data, $channel_id = '', $batch = false)
    {
        if (!empty($data) && !empty($channel_id)) {
            // 获取模型对应的附加表字段信息
            $map = array(
                'channel_id'    => $channel_id,
            );
            $fieldInfo = M('Channelfield')->getListByWhere($map, '*', 'name');
            
            $data = $this->handleAddonFieldList($data, $fieldInfo, $batch);
        } else {
            $data = array();
        }
       
        return $data;
    }

    /**
     * 查询解析单个数据表的数据用以页面展示
     * @param array $data 表数据
     * @param intval $channel_id 模型ID
     * @param array $batch 是否批量列表
     */
    public function getTableFieldList($data, $channel_id = '', $batch = false)
    {
        if (!empty($data) && !empty($channel_id)) {
            /*获取自定义表字段信息*/
            $map = array(
                'channel_id'    => $channel_id,
            );
            $fieldInfo = M('Channelfield')->getListByWhere($map, '*', 'name');
            /*--end*/
            $data = $this->handleAddonFieldList($data, $fieldInfo, $batch);
        } else {
            $data = array();
        }

        return $data;
    }

    /**
     * 处理自定义字段的值
     * @param array $data 表数据
     * @param array $fieldInfo 自定义字段集合
     * @param array $batch 是否批量列表
     */
    public function handleAddonFieldList($data, $fieldInfo, $batch = false)
    {
        if (false !== $batch) {
            return $this->handleBatchAddonFieldList($data, $fieldInfo);
        }

        if (!empty($data) && !empty($fieldInfo)) {
            foreach ($data as $key => $val) {
                $dtype = !empty($fieldInfo[$key]) ? $fieldInfo[$key]['dtype'] : '';
                $dfvalue_unit = !empty($fieldInfo[$key]) ? $fieldInfo[$key]['dfvalue_unit'] : '';
                switch ($dtype) {
                    case 'int':
                    case 'float':
                    case 'decimal':
                    case 'text':
                    {
                        $data[$key.'_unit'] = $dfvalue_unit;
                        break;
                    }

                    case 'imgs':
                    {
                        if (!is_array($val)) {
                            $thinker_imgupload_list = @unserialize($val);
                            if (false === $thinker_imgupload_list) {
                                $thinker_imgupload_list = [];
                                $thinker_imgupload_data = explode(',', $val);
                                foreach ($thinker_imgupload_data as $k1 => $v1) {
                                    $thinker_imgupload_list[$k1] = [
                                        'image_url' => handle_subdir($v1),
                                        'intro'     => '',
                                    ];
                                }
                            }
                        } else {
                            $thinker_imgupload_list = [];
                            $thinker_imgupload_data = $val;
                            foreach ($thinker_imgupload_data as $k1 => $v1) {
                                $v1['image_url'] = handle_subdir($v1['image_url']);
                                $thinker_imgupload_list[$k1] = $v1;
                            }
                        }
                        $val = $thinker_imgupload_list;
                        break;
                    }

                    case 'checkbox':
                    case 'files':
                    {
                        if (!is_array($val)) {
                            $val = !empty($val) ? explode(',', $val) : array();
                        }
                        /*支持子目录*/
                        foreach ($val as $k1 => $v1) {
                            $val[$k1] = handle_subdir($v1);
                        }
                        /*--end*/
                        break;
                    }

                    case 'htmltext':
                    {
                        $val = htmlspecialchars_decode($val);

                        /*追加指定内嵌样式到编辑器内容的img标签，兼容图片自动适应页面*/
                        $titleNew = !empty($data['title']) ? $data['title'] : '';
                        $val = img_style_wh($val, $titleNew);
                        /*--end*/

                        /*支持子目录*/
                        $val = handle_subdir($val, 'html');
                        /*--end*/
                        break;
                    }

                    case 'decimal':
                    {
                        $val = number_format($val,'2','.',',');
                        break;
                    }
                    
                    default:
                    {
                        /*支持子目录*/
                        if (is_string($val)) {
                            $val = handle_subdir($val, 'html');
                            $val = handle_subdir($val);
                        }
                        /*--end*/
                        break;
                    }
                }
                $data[$key] = $val;
            }
        }
        return $data;
    }

    /**
     * 列表批量处理自定义字段的值
     * @param array $data 表数据
     * @param array $fieldInfo 自定义字段集合
     */
    public function handleBatchAddonFieldList($data, $fieldInfo)
    {
        if (!empty($data) && !empty($fieldInfo)) {
            foreach ($data as $key => $subdata) {
                $data[$key] = $this->handleAddonFieldList($subdata, $fieldInfo);
            }
        }
        return $data;
    }
}
