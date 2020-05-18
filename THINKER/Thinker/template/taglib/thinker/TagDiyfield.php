<?php
namespace Thinker\template\taglib\thinker;

class TagDiyfield extends Base
{
    /**
     * 自定义字段
     */
    public function getDiyfield($data = '', $type = 'default')
    {
        if (!empty($data)) {
            switch ($type) {
                case 'imgs':
                    // 多图
                    $list = [];
                    foreach ($data as $key => $val) {
                        $val['image_url'] = handle_subdir($val['image_url']);
                        $list[$key] = $val;
                    }
                    $data = $list;
                    break;
                case 'files':
                    // 多文件
                    $list = [];
                    foreach ($data as $key => $val) {
                        $list[$key]['downurl'] = handle_subdir($val);
                        $list[$key]['title'] = '';
                    }
                    $data = $list;
                    break;
                case 'radio':
                // 单选项
                case 'select':
                // 下拉框
                case 'checkbox':
                    // 多选项
                    $list = [];
                    $row = [];
                    if (is_array($data)) {
                        $row = $data;
                    } else {
                        $row = explode(',', $data);
                    }
                    foreach ($row as $key => $val) {
                        $list[$key]['value'] = $val;
                    }
                    $data = $list;
                    break;
                default:
                    $list = [];
                    $row = [];
                    if (is_array($data)) {
                        $row = $data;
                    } else {
                        $row[] = $data;
                    }
                    foreach ($row as $key => $val) {
                        $list[$key]['value'] = $val;
                    }
                    $data = $list;
                    break;
            }
        }
        return $data;
    }
}