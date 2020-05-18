{{include file="public/header" /}} 
<!-- conntent start  -->
<div class="am-g inside create">
  <h2>数据库信息</h2>
  <div class="am-alert am-radius" data-am-alert>
    <button type="button" class="am-close">&times;</button>
    <p class="am-text-xs">温馨提示</p>
    <p class="am-text-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;建议采用utf8mb4编码、MySQL版本5.6或5.7</p>
    {{if !empty($charset_type_list)}}
    {{foreach $charset_type_list as $v}}
    <p class="am-text-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$v.charset}}编码、MySQL版本需 {{$v.version}} 及以上版本 （{{$v.collate}}）</p>
    {{/foreach}}
    {{/if}} </div>
  <form class="am-form am-form-horizontal form-validation" method="post" action="{{:url('install/index/performInstallation')}}" request-type="ajax-url" request-value="{{:url('install/index/step4')}}" timeout="60000">
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库类型</label>
      <div class="am-u-sm-10">
        <input type="text" name="DB_TYPE" placeholder="数据库类型" value="mysql" class="am-radius am-input-sm" disabled data-validation-message="请选择数据库类型" required />
      </div>
    </div>
    <div class="am-form-group am-form-select">
      <label class="am-u-sm-2 am-form-label">数据库编码</label>
      <div class="am-u-sm-10">
        <select class="am-input-sm am-radius chosen-select" name="DB_CHARSET" data-validation-message="请选择数据编码" required>
          
          
          
          
                    {{if !empty($charset_type_list)}}
                        {{foreach $charset_type_list as $v}}
                            
          
          
          
          <option value="{{$v.charset}}">{{$v.charset}} - {{$v.collate}} （mysql版本>={{$v.version}}）</option>
          
          
          
          
                        {{/foreach}}
                    {{/if}}
                
        
        
        
        </select>
      </div>
    </div>
    <div class="am-form-group am-form-select">
      <label class="am-u-sm-2 am-form-label">数据表引擎</label>
      <div class="am-u-sm-10">
        <select class="am-input-sm am-radius chosen-select" name="DB_ENGINE" data-validation-message="请选择数据表引擎" required>
          <option value="InnoDB">InnoDB</option>
          <option value="MyISAM">MyISAM</option>
        </select>
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库服务器</label>
      <div class="am-u-sm-10">
        <input type="text" name="DB_HOST" placeholder="数据库服务器" value="127.0.0.1" class="am-radius am-input-sm" data-validation-message="请填写数据库服务器地址" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库端口</label>
      <div class="am-u-sm-10">
        <input type="number" name="DB_PORT" placeholder="数据库端口" value="3306" class="am-radius am-input-sm" data-validation-message="请填写数据库端口" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库名</label>
      <div class="am-u-sm-10">
        <input type="text" name="DB_NAME" placeholder="数据库名" value="thinker" class="am-radius am-input-sm" data-validation-message="请填写数据库名" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库用户名</label>
      <div class="am-u-sm-10">
        <input type="text" name="DB_USER" placeholder="数据库用户名" value="root" class="am-radius am-input-sm" data-validation-message="请填写数据库用户名" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据库密码</label>
      <div class="am-u-sm-10">
        <input type="password" name="DB_PWD" placeholder="数据库密码" value="" class="am-radius am-input-sm" data-validation-message="请填写数据库密码" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">数据表前缀</label>
      <div class="am-u-sm-10">
        <input type="text" name="DB_PREFIX" placeholder="数据表前缀" value="tp_" class="am-radius am-input-sm" data-validation-message="请填写数据表前缀" required />
      </div>
    </div>
    <h2>管理员信息</h2>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">管理员帐号</label>
      <div class="am-u-sm-10">
        <input type="text" name="ADMIN_USER_NAME" placeholder="管理员帐号" value="admin" class="am-radius am-input-sm" data-validation-message="请填写管理员帐号" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">管理员密码</label>
      <div class="am-u-sm-10">
        <input type="password" name="ADMIN_USER_PWD" placeholder="管理员密码" value="" class="am-radius am-input-sm" data-validation-message="请填写管理员密码" required />
      </div>
    </div>
    <div class="am-form-group">
      <label class="am-u-sm-2 am-form-label">确认密码</label>
      <div class="am-u-sm-10">
        <input type="password" name="ADMIN_USER_PWD" placeholder="确认密码" value="" class="am-radius am-input-sm" data-validation-message="请填写确认密码" required />
      </div>
    </div>
    <div class="am-form-group am-form-group-refreshing agree ongoing-button"> <a href="{{:url('install/index/check')}}" class="am-btn am-btn-secondary am-radius">上一步</a>
      <button type="submit" class="am-btn am-btn-success am-radius btn-loading-example" data-am-loading="{spinner:'circle-o-notch', loadingText:'安装中...'}">确认</button>
    </div>
  </form>
</div>
<!-- conntent end  --> 
{{include file="public/footer" /}}