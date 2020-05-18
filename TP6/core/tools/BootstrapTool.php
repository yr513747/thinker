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
// [ Bootstrap 分页驱动 ]
// --------------------------------------------------------------------------
declare (strict_types = 1);
namespace core\tools;

use think\Paginator;

class BootstrapTool extends Paginator
{

    /**
     * 首页按钮
     * @param string $text
     * @return string
     */
    protected function getFirstButton($text = "&laquo;"): string
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(1);

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "&laquo;"): string
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 末页按钮
     * @param string $text
     * @return string
     */
    protected function getLastButton($text = '&raquo;'): string
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->lastPage);

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '&raquo;'): string
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 共N页 N条
     * @param string $text
     * @return string
     */
    protected function getTotalResult(): string
    {
        return sprintf(
            lang('sys5'),
            $this->lastPage,
            $this->total
        );
    }

    /**
     * 页码按钮
     * @param string $listsize 当前页对称两边的条数
     * @return string
     */
    protected function getLinks($listsize = 3): string
    {
        if ($this->simple)
            return '';

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = $listsize;
        $window = $side * 2;

        if ($this->lastPage < $window + 2) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage < ($side + 1)) {
            $block['first'] = $this->getUrlRange(1, $window + 1);
        } elseif ($this->currentPage > ($this->lastPage - $side)) {
            $block['last']  = $this->getUrlRange($this->lastPage - $window, $this->lastPage);
        } else {
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @param string $listitem 分页格式显示
     * @param string $listsize 当前页对称两边的条数
     * @return mixed
     */
    public function render($listitem = '', $listsize = '')
    {
        if ($this->hasPages()) { // 有数据的情况下

            $listitemArr = explode(',', $listitem);
            foreach ($listitemArr as $key => $val) {
                $listitemArr[$key] = trim($val);
            }

            $pageArr = array();
            if (in_array('index', $listitemArr)) {
                array_push($pageArr, $this->getFirstButton(lang('sys1')));
            }
            if (in_array('pre', $listitemArr)) {
                array_push($pageArr, $this->getPreviousButton(lang('sys2')));
            }
            if (in_array('pageno', $listitemArr)) {
                array_push($pageArr, $this->getLinks($listsize));
            }
            if (in_array('next', $listitemArr)) {
                array_push($pageArr, $this->getNextButton(lang('sys3')));
            }
            if (in_array('end', $listitemArr)) {
                array_push($pageArr, $this->getLastButton(lang('sys4')));
            }
            if (in_array('info', $listitemArr)) {
                array_push($pageArr, $this->getTotalResult());
            }
            $pageStr = implode(' ', $pageArr);

            return $pageStr;

        } else { // 没有数据的情况下
            return $this->getTotalResult();
        }
    }

    /**
     * 生成一个可点击的数字按钮
     *
     * @param  string $url
     * @param  string $page
     * @return string
     */
    protected function getAvailablePageWrapper2($url, $page): string
    {
        return '<li><a href="' . htmlentities($url) . '" data-ey_fc35fdc="html" data-tmp="1' . '">' . $page . '</a></li>';
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  string $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page): string
    {
        return '<li><a href="' . htmlentities($url) . '" data-ey_fc35fdc="html" data-tmp="1' . '">' . $page . '</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text): string
    {
        return '<li><a>' . $text . '</a></li>';
    }

    /**
     * 生成一个激活的数字按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper2($text): string
    {
        return '<li class="active"><a>' . $text . '</a></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text): string
    {
        return '<li class="active"><a>' . $text . '</a></li>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots(): string
    {
        return $this->getDisabledTextWrapper('···');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls): string
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper2($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  string $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page): string
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * 生成普通页码的数字按钮
     *
     * @param  string $url
     * @param  string $page
     * @return string
     */
    protected function getPageLinkWrapper2($url, $page): string
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper2($page);
        }

        return $this->getAvailablePageWrapper2($url, $page);
    }
}
