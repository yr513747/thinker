<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think\response;

use think\Cookie;
use think\Response;

/**
 * Image Response by 仰融
 */
class Image extends Response
{
    /**
     * 输出type
     * @var string
     */
    protected $contentType = 'image/png';

    public function __construct(Cookie $cookie, $data = '', int $code = 200)
    {
		parent::__construct();
        $this->init($data, $code);
        $this->cookie = $cookie;
    }
	
	/**
     * 页面输出类型
     * @param string $contentType 输出类型
	 * @param string $charset     输出编码只是为了兼容父类方法
     * @return $this
     */
    public function contentType($contentType = '', $charset = '')
    {
		if (!empty($contentType)) {
            $this->contentType = $contentType;
        }
        $this->header['Content-Type'] = $this->contentType;
        return $this;
    }
}
