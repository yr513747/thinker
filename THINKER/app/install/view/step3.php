<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<meta http-equiv="Content-Language" content="zh-cn"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<title>{$steps[3]}_{$title}_{$powered}</title>
<link rel="stylesheet" href="{__INSTALL_PATH__}/css/install.css?v=v1.3.1" />
<script src="{__INSTALL_PATH__}/js/jquery.js?v=v1.3.1"></script>
{:token_meta('__token__')}
</head>
<body>
<div class="wrap"> {include file="header"}
  <section class="section">
    <div class="blank30"></div>
    <div class="go go3"></div>
    <div class="blank30"></div>
    <form id="J_install_form" action="{:url("install/Index/step4")}" method="post">
      <input type="hidden" name="force" value="0" />
      <div class="server">
        <table width="100%" id="table" border="0" cellspacing="1" cellpadding="4">
          <tr>
            <td class="td1" colspan="2">数据库信息</td>
          </tr>
          <tr>
            <td class="tar">数据库地址</td>
            <td><input type="text" name="dbhost" id="dbhost" value="127.0.0.1" class="input">
              <div id="J_install_tip_dbhost"><span class="gray">一般为127.0.0.1 或 localhost</span></div></td>
          </tr>
          <tr>
            <td class="tar">数据库端口</td>
            <td><input type="text" name="dbport" id="dbport" value="3306" class="input">
              <div id="J_install_tip_dbport"><span class="gray">一般为3306</span></div></td>
          </tr>
          <tr>
            <td class="tar">数据库账号</td>
            <td><input type="text" name="dbuser" id="dbuser" value="root" class="input">
              <div id="J_install_tip_dbuser"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库密码</td>
            <td><input type="password" name="dbpw" id="dbpw" value="" class="input" autoComplete="off" onBlur="TestDbPwd(0)">
              <div id="J_install_tip_dbpw"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库名</td>
            <td><input type="text" name="dbname" id="dbname" value="thinker" class="input" onBlur="TestDbPwd(0)">
              <div id="J_install_tip_dbname"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库表前缀</td>
            <td><input type="text" name="dbprefix" id="dbprefix" value="thinker_" class="input" >
              <div id="J_install_tip_dbprefix"><span class="gray">推荐使用&nbsp;thinker_</span></div></td>
          </tr>
        </table>
        <table width="100%" id="table" border="0" cellspacing="1" cellpadding="4">
          <tr>
            <td class="td1" colspan="2">管理员信息</td>
          </tr>
          <tr>
            <td class="tar">管理员帐号</td>
            <td><input type="text" name="manager" id="manager" value="admin" class="input">
              <div id="J_install_tip_manager"></div></td>
          </tr>
          <tr>
            <td class="tar">管理员密码</td>
            <td><input type="password" name="manager_pwd" id="manager_pwd" class="input" autoComplete="off">
              <div id="J_install_tip_manager_pwd"></div></td>
          </tr>
          <tr>
            <td class="tar">请确认密码</td>
            <td><input type="password" name="manager_ckpwd" id="manager_ckpwd" class="input" autoComplete="off">
              <div id="J_install_tip_manager_ckpwd"></div></td>
          </tr>
        </table>
        <div id="J_response_tips" style="display:none;"></div>
      </div>
      <div class="blank20"></div>
      <div class="bottom tac">
        <center>
          <a href="{:url("install/Index/step2")}" class="btn_b">上一步</a>
          <button id="next_submit" type="button" onClick="checkForm();" class="btn btn_submit J_install_btn">创建数据</button>
        </center>
      </div>
      <div class="blank20"></div>
    </form>
  </section>
  <div  style="width:0;height:0;overflow:hidden;"> <img src="{__INSTALL_PATH__}/images/pop_loading.gif"> </div>
  
</div>
{include file="footer"} 
<script src="{__INSTALL_PATH__}/js/layer-v3.1.1/layer/layer.js?v=v1.3.1"></script> 
<script src="{__INSTALL_PATH__}/js/validate.js?v=9.0"></script> 
<script src="{__INSTALL_PATH__}/js/ajaxForm.js?v=9.0"></script> 
<script type="text/javascript">
function TestDbPwd(connect_db) {
    var dbHost = $('#dbhost').val();
    var dbUser = $('#dbuser').val();
    var dbPwd = $('#dbpw').val();
    var dbName = $('#dbname').val();
    var dbport = $('#dbport').val();

    data = {
        dbHost: dbHost,
        dbUser: dbUser,
        dbPwd: dbPwd,
        dbName: dbName,
        dbport: dbport
    };
    var url = "{:url("install/Index/testdbpwd")}";
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        beforeSend: function() {},
        success: function(res) {
            if (res.errcode == 1) {
                if (connect_db == 1) {
                    ajaxSubmit(); 
                    return false;
                }
                $('#J_install_tip_dbpw').html(res.dbpwmsg);
                $('#J_install_tip_dbname').html(res.dbnamemsg);
            } else if (res.errcode == -1) {
                $('#J_install_tip_dbpw').html(res.dbpwmsg);
            } else if (res.errcode == -2) {
                $('#J_install_tip_dbname').html(res.dbnamemsg);
            } else {
                $('#J_install_tip_dbpw').html(res.dbpwmsg);
            }
        },
        complete: function() {},
        error: function() {
            $('#J_install_tip_dbpw').html('<span for="dbname" generated="true" class="tips_error" style="">数据库连接失败，请重新设定</span>');
        }
    });
}

function ajaxSubmit() {
    $.ajax({
        async:false,
        url: $('#J_install_form').attr('action'),
        data: $('#J_install_form').serialize(),
        type: "POST",
        dataType: "json",
        success: function(res) {
            if (1 == res.code) {
                window.location.href = res.url;
            } else {
                layer.closeAll();
                layer.msg(res.msg, {
                    icon: 5
                });
            }
            return false;
        },
        error: function() {
            layer.closeAll();
            layer.alert('未知错误，无法继续！', {
                icon: 5,
                title: false
            });
            return false;
        }
    });
}

function checkForm() {
    dbhost = $.trim($('#dbhost').val()); //数据库地址
    dbport = $.trim($('#dbport').val()); //数据库端口
    dbuser = $.trim($('#dbuser').val()); //数据库账号
    dbpw = $.trim($('#dbpw').val()); //数据库密码
    dbname = $.trim($('#dbname').val()); //数据库名
    dbprefix = $.trim($('#dbprefix').val()); //数据库表前缀
    manager = $.trim($('#manager').val()); //用户名表单
    manager_pwd = $.trim($('#manager_pwd').val()); //密码表单
    manager_ckpwd = $.trim($('#manager_ckpwd').val()); //密码提示区

    if (dbhost.length == 0) {
        $('#dbhost').focus();
        layer.msg('数据库地址不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (dbport.length == 0) {
        $('#dbport').focus();
        layer.msg('数据库端口不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (dbuser.length == 0) {
        $('#dbuser').focus();
        layer.msg('数据库账号不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (dbpw.length == 0) {
        $('#dbpw').focus();
        layer.msg('数据库密码不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (dbname.length == 0) {
        $('#dbname').focus();
        layer.msg('数据库名不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (dbprefix.length == 0) {
        $('#dbprefix').focus();
        layer.msg('数据库表前缀不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (manager.length == 0) {
        $('#manager').focus();
        layer.msg('管理员账号不能为空', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (manager_pwd.length < 5) {
        $('#manager_pwd').focus();
        layer.msg('管理员密码必须5位数以上', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    if (manager_ckpwd != manager_pwd) {
        $('#manager_ckpwd').focus();
        layer.msg('两次密码不一致', {
            icon: 5,
            time: 1500
        });
        return false;
    }
    layer_loading('正在安装');
    TestDbPwd(1);
}

function layer_loading(msg) {
    var loading = layer.msg(
        msg + '...<img src="{__INSTALL_PATH__}/images/loading-0.gif"/>&nbsp;请勿刷新页面', {
            icon: 1,
            time: 3600000,
            shade: [0.2]
        });

    return loading;
}
$(function() {
    $('#next_submit').focus();
    $(document).keydown(function(event) {
        if (event.keyCode == 13) {
            checkForm();
            return false;
        }
    });
});
</script>
</body>
</html>