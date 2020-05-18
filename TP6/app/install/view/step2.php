{{include file="public/header" /}} 
<!-- conntent start  -->
<div class="am-g inside check">
  <h2>环境检查</h2>
  <table class="am-table">
    <tr>
      <th width="25%">环境</th>
      <th width="25%">程序所需</th>
      <th width="25%">当前服务器</th>
      <th width="25%">是否符合</th>
    </tr>
    <tr class="<?php if(function_exists('php_uname')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>操作系统</td>
      <td>无限制</td>
      <td><?php echo function_exists('php_uname') ? php_uname('s') : '未知（php_uname函数未启用）'; ?></td>
      <td><?php echo function_exists('php_uname') ? '√' : '×'; ?></td>
    </tr>
    <?php $php_version = explode('.', PHP_VERSION); ?>
    <tr class="<?php if(($php_version['0'] >= 7)){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>PHP版本</td>
      <td>>=7.1</td>
      <td><?php echo PHP_VERSION ?></td>
      <td><?php if(($php_version['0']>=7)): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <?php
            if(function_exists('gd_info'))
            {
                $tmp = gd_info();
                preg_match("/[\d.]+/", $tmp['GD Version'], $match);
                unset($tmp);
            }
        ?>
    <tr class="<?php if(isset($match[0]) && $match[0] > 2){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>GD库</td>
      <td>2.0</td>
      <td><?php echo isset($match[0]) ? $match[0] : '未知（gd_info函数未启用）'; ?></td>
      <td><?php if(isset($match[0]) && $match[0] > 2): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
  </table>
  <h2>目录/文件权限检查</h2>
  <table class="am-table">
    <tr>
      <th width="25%">环境</th>
      <th width="25%">所需状态</th>
      <th width="25%">当前状态</th>
      <th width="25%">是否符合</th>
    </tr>
    <tr class="<?php if(is_writable(root_path())){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>./</td>
      <td>可写</td>
      <td><?php if (is_writable(root_path())): ?>
        可写
        <?php else: ?>
        不可写
        <?php endif ?></td>
      <td><?php if (is_writable(root_path())): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(is_writable(root_path().'config')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>./config</td>
      <td>可写</td>
      <td><?php if (is_writable(root_path().'config')): ?>
        可写
        <?php else: ?>
        不可写
        <?php endif ?></td>
      <td><?php if (is_writable(root_path().'config')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(is_writable(root_path().'route')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>./route</td>
      <td>可写</td>
      <td><?php if (is_writable(root_path().'route')): ?>
        可写
        <?php else: ?>
        不可写
        <?php endif ?></td>
      <td><?php if (is_writable(root_path().'route')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(is_writable(root_path().'runtime')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>./runtime</td>
      <td>可写</td>
      <td><?php if (is_writable(root_path().'runtime')): ?>
        可写
        <?php else: ?>
        不可写
        <?php endif ?></td>
      <td><?php if (is_writable(root_path().'runtime')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(is_writable(root_path().'public')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>./public</td>
      <td>可写</td>
      <td><?php if (is_writable(root_path().'public')): ?>
        可写
        <?php else: ?>
        不可写
        <?php endif ?></td>
      <td><?php if (is_writable(root_path().'public')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
  </table>
  <h2>函数/类</h2>
  <table class="am-table">
    <tr>
      <th width="25%">环境</th>
      <th width="25%">所需状态</th>
      <th width="25%">当前状态</th>
      <th width="25%">是否符合</th>
    </tr>
    <tr class="<?php if(function_exists('curl_init')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>curl_init 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('curl_init')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('curl_init')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('fsockopen')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>fsockopen 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('fsockopen')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('fsockopen')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('file_get_contents')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>file_get_contents 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('file_get_contents')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('file_get_contents')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('mb_convert_encoding')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>mb_convert_encoding 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('mb_convert_encoding')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('mb_convert_encoding')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('json_encode')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>json_encode 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('json_encode')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('json_encode')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('json_decode')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>json_decode 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('json_decode')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('json_decode')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('simplexml_load_string')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>simplexml_load_string 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('simplexml_load_string')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('simplexml_load_string')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('mb_substr')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>mb_substr 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('mb_substr')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('mb_substr')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(function_exists('mb_strlen')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>mb_strlen 函数</td>
      <td>支持</td>
      <td><?php if (function_exists('mb_strlen')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (function_exists('mb_strlen')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(class_exists('mysqli')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>mysqli 类</td>
      <td>支持</td>
      <td><?php if (class_exists('mysqli')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (class_exists('mysqli')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(class_exists('pdo')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>pdo 类</td>
      <td>支持</td>
      <td><?php if (class_exists('pdo')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (class_exists('pdo')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
    <tr class="<?php if(class_exists('ZipArchive')){ echo 'yes'; } else { echo 'am-danger'; } ?>">
      <td>ZipArchive 类</td>
      <td>支持</td>
      <td><?php if (class_exists('ZipArchive')): ?>
        支持
        <?php else: ?>
        不支持
        <?php endif ?></td>
      <td><?php if (class_exists('ZipArchive')): ?>
        √
        <?php else: ?>
        ×
        <?php endif ?></td>
    </tr>
  </table>
  <div class="agree ongoing-button"> <a href="{{:url('install/index/index')}}" class="am-btn am-btn-secondary am-radius">上一步</a>
    <button type="button" class="am-btn am-btn-success am-radius check-submit" data-url="{{:url('install/index/step3')}}">下一步</button>
  </div>
</div>
<!-- conntent end  --> 
{{include file="public/footer" /}}