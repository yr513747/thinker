<?php
namespace app\api\controller;

use common\util\File;
use think\Image;
class Uploadify extends BaseController
{
    private $sub_name = array('date', 'Ymd');
    private $savePath = 'allimg/';
    private $image_type = '';
    public function __construct()
    {
    }
}