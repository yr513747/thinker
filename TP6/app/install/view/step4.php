{{include file="public/header" /}}
<!-- conntent start  -->
<div class="am-g inside success">
    <i class="am-icon-btn am-success am-icon-sm am-icon-check"></i>
    <h2>恭喜您安装成功</h2>
    <div class="box">
        <a href="{{$domain}}/admin.php" target="_blank">后台管理</a><br />
        <p class="tips-sweet">默认后台地址入口文件为 admin.php 可在站点根目录和public目录下更改相应的文件名称，避免后台管理入口被非法入侵。</p>
        <br />
        <a href="{{$domain}}" target="_blank">访问首页</a>
        <p class="tips-sweet">请尽快修改管理员密码，以防被非法入侵。<br />并删除应用目录下的 install 目录和静态资源文件 ./static/install。</p>
    </div>
</div>
<!-- conntent end  -->
{{include file="public/footer" /}}