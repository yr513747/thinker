<?php
namespace app\api\controller;

use common\util\File;
use think\Image;
class Ueditor extends BaseController
{
    private $sub_name = array('date', 'Ymd');
    private $savePath = 'allimg/';
    private $fileExt = 'jpg,png,gif,jpeg,bmp,ico';
    public function __construct()
    {
    }
}