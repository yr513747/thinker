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
declare (strict_types=1);
namespace think\exception;

class FileSystemException extends \RuntimeException 
{
	private $path;
    public function __construct(string $message = null, $code = 0, \Throwable $previous = null, string $path = null)
    {
		if (null === $message) {
            if (null === $path) {
                $message = 'File could not be found.';
            } else {
                $message = sprintf('File "%s" could not be found.', $path);
            }
        }
		$this->path = $path;
        parent::__construct((string) $message, (int) $code, $previous);
    }
	/**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}