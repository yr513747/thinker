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
// [ 分页解析类，修改自tp3.2版本 ]
// --------------------------------------------------------------------------
namespace core\tools;

use think\facade\Request;

class PageTool
{
    /**
     * 起始行数
     * @var int
     */
    public $firstRow;
    /**
     * 列表每页显示行数
     * @var int
     */
    public $listRows;
    /**
     * 分页跳转时要带的参数
     * @var array
     */
    public $parameter;
    /**
     * 总行数
     * @var int
     */
    public $totalRows;
    /**
     * 分页总页面数
     * @var int
     */
    public $totalPages;
    /**
     * 分页栏每页显示的页数
     * @var int
     */
    public $rollPage = 11;
    /**
     * 最后一页是否显示总页数
     * @var bool
     */
    public $lastSuffix = true;
    /**
     * 分页参数名
     * @var string
     */
    public $p = 'p';
    /**
     * 当前链接URL
     * @var string
     */
    public $url = '';
    /**
     * 当前页码
     * @var int
     */
    public $nowPage = 1;
    /**
     * 分页显示定制
     * @var array
     */
    private $config = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        /*
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        */
        'prev' => '上一页',
        'next' => '下一页',
        'first' => '首页',
        'last' => '尾页',
        'theme' => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );
    /**
     * 架构函数
     * @param int $totalRows  总的记录数
     * @param int $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows = 20, $parameter = array())
    {
        // config('var_page') && $this->p = config('var_page'); //设置分页参数名称
        // 基础设置
        $p = input("get.{$this->p}");
        $this->totalRows = $totalRows;
        $this->listRows = intval($listRows);
        $this->parameter = empty($parameter) ? input() : $parameter;
        $this->nowPage = empty($p) ? 1 : intval($p);
        $this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
        $this->firstRow = $this->listRows * ($this->nowPage - 1);
		$this->firstRow = intval($this->firstRow);
        // 计算分页信息,总页数		
        $this->totalPages = ceil($this->totalRows / $this->listRows) > 0 ? ceil($this->totalRows / $this->listRows) : 1;
    }
    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    public function url($page)
    {
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }
    /**
     * 组装分页链接
     * @return string
     */
    public function show()
    {
        if (0 == $this->totalRows) {
            return '';
        }
        // 生成URL
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = url(Request::action(), $this->parameter);
        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        // 计算分页临时变量
        $now_cool_page = $this->rollPage / 2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && ($this->config['last'] = $this->totalPages);
        $this->config['last'] = '尾页';
        // 上一页
        $up_row = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<li id="example1_previous" class="paginate_button previous"><a class="prev" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a></li>' : '';
        // 下一页
        $down_row = $this->nowPage + 1;
        $down_page = $down_row <= $this->totalPages ? '<li id="example1_next" class="paginate_button next"><a class="next" href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a></li>' : '';
        // 第一页
        $the_first = '';
        if ($this->totalPages > $this->rollPage && $this->nowPage - $now_cool_page >= 1) {
            $the_first = '<li id="example1_previous" class="paginate_button previous disabled"><a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '</a></li>';
        }
        // 最后一页
        $the_end = '';
        if ($this->totalPages > $this->rollPage && $this->nowPage + $now_cool_page < $this->totalPages) {
            $the_end = '<li id="example1_previous" class="paginate_button previous disabled"><a class="end" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a></li>';
        }
        // 数字连接
        $link_page = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            if ($this->nowPage - $now_cool_page <= 0) {
                $page = $i;
            } elseif ($this->nowPage + $now_cool_page - 1 >= $this->totalPages) {
                $page = $this->totalPages - $this->rollPage + $i;
            } else {
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if ($page > 0 && $page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $link_page .= '<li class="paginate_button"><a class="num" href="' . $this->url($page) . '">' . $page . '</a></li>';
                } else {
                    break;
                }
            } else {
                if ($page > 0 && $this->totalPages != 1) {
                    //$link_page .= '<span class="current">' . $page . '</span>';
                    $link_page .= '<li class="paginate_button active"><a tabindex="0" data-dt-idx="1" aria-controls="example1" href="#">' . $page . '</a></li>';
                }
            }
        }
        // 替换分页内容
        $page_str = str_replace(array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'), array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages), $this->config['theme']);
        return "<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>{$page_str}</ul></div>";
    }
}