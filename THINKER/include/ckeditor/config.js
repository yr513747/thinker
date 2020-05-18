/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.editorConfig = function( config )
{
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    config.uiColor = '#F8F8F8';
    // 文件浏览
    config.filebrowserImageBrowseUrl = "../include/dialog/select_images.php";
    config.filebrowserFlashBrowseUrl = "../include/dialog/select_media.php";
    config.filebrowserImageUploadUrl  = "../include/dialog/select_images_post.php";
	
	config.autoParagraph = false;
    config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	
	//代码高亮 在\include\inc\inc_fun_funAdmin.php添加才行,先后顺序问题这里会被覆盖,所以这里注释
	//config.extraPlugins = 'codesnippet';
	config.codeSnippet_theme = 'monokai_sublime';//后台代码高亮主题
};
