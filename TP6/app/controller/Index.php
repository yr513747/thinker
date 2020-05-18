<?php
namespace app\controller;

use think\App;
use think\FileInfo;
use core\tools\ZipTool;
use think\FileObject;
use think\Util;
use Exception;
use PDO;
use think\TempFileObject;
use think\filesystem\ZipArchive;
use think\facade\File;
use app\BaseController;
class Index extends BaseController
{
	use \think\traits\app\ErrorPage;
    /**
     * 文件操作对象
     * @var null|SplFileObject
     */
    protected $file = null;
    /**
     * 输出内容
     * @var string
     */
    protected $content;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $css = <<<css
<style type="text/css"><!--
    body {
        color: #444444;
        background-color: #C55042;
        font-family: 'Trebuchet MS', sans-serif;
        font-size: 100%;
    }
    h1 { font-size: 600px; font-weight: normal;}
    h2 { font-size: 1.2em; }
    #page{
        background-color: #FFFFFF;
        width: 60%;
        margin: 24px auto;
        padding: 12px;
    }
    #header {
        /*padding: 6px ;*/
        text-align: center;
    }
    .status3xx { background-color: #475076; color: #FFFFFF; }
    /*.status4xx { background-color: #C55042; color: #FFFFFF; }*/
    .status5xx { background-color: #F2E81A; color: #000000; }
    #content {
        padding: 4px 0 24px 0;
    }
    #footer {
        color: #666666;
        background: #f9f9f9;
        padding: 10px 20px;
        border-top: 5px #efefef solid;
        font-size: 0.8em;
        text-align: center;
    }
    #footer a {
        color: #999999;
    }

    --></style>
css;
        $this->content = $css . '<div id="header" class="status4xx"> <h1>忄束负</h1</div>';
        $this->basePath = $this->app->getBasePath();
        $this->rootPath = $this->app->getRootPath();
    }
    public function index()
    {
		
        return;
    }
    public function v()
    {
        return $this->redirect("https://www.huya.com/g/seeTogether");
    }
}