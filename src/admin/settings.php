<?php
require_once 'common.php';

dp_check_login();

$display_info = false;

$theme_files = scandir(PATH_ROOT . '/theme');
$theme_list = array();
$filter = array('.', '..', '.cvs', '.svn', '.git', '.DS_Store', '._.DS_Store');
foreach ($theme_files as $file) {
    if (!is_dir($file) && !in_array($file, $filter)) {
      $theme_list[] = $file;
    }
}

if (isset($_POST['save'])) {
  $user_name_changed = $_POST['user_name'] != $dp_config['user_name'];

  $dp_config['site_name']     = $_POST['site_name'];
  $dp_config['site_desc']     = $_POST['site_desc'];
  $dp_config['site_keywords'] = $_POST['site_keywords'];
  $dp_config['site_link']     = $_POST['site_link'];
  $dp_config['site_theme']    = $_POST['site_theme'];
  $dp_config['site_route']    = $_POST['site_route'];
  $dp_config['site_icpno']    = $_POST['site_icpno'];
  $dp_config['site_status']   = $_POST['site_status'];
  $dp_config['nick_name']     = $_POST['nick_name'];
  $dp_config['user_name']     = $_POST['user_name'];
  $dp_config['comment_code']  = trim($_POST['comment_code']);
  $dp_config['footer_code']   = trim($_POST['footer_code']);

  if ($_POST['user_pass'] != '') {
    $dp_config['user_pass'] = $_POST['user_pass'];
  }

  $code = "<?php\n\$dp_config = " . var_export($dp_config, true) . "\n?>";

  file_put_contents(PATH_ROOT . '/data/config.php', $code);

  if ($_POST['user_pass'] != '' || $user_name_changed) {
    setcookie('dp_token', md5($dp_config['user_name'] . '_' . $dp_config['user_pass']));
  }

  $display_info = true;
}

$site_name     = $dp_config['site_name'];
$site_desc     = $dp_config['site_desc'];
$site_keywords = $dp_config['site_keywords'];
$site_link     = $dp_config['site_link'];
$site_theme    = $dp_config['site_theme'];
$site_route    = $dp_config['site_route'];
$site_status   = $dp_config['site_status'];
$site_icpno    = $dp_config['site_icpno'];
$nick_name     = $dp_config['nick_name'];
$user_name     = $dp_config['user_name'];
$comment_code  = isset($dp_config['comment_code']) ? $dp_config['comment_code'] : '';
$footer_code   = isset($dp_config['footer_code']) ? $dp_config['footer_code'] : '';

?>
<?php require 'head.php'; ?>
<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
  <?php if ($display_info) { ?>
    <div class="updated">设置保存成功！</div>
  <?php } ?>
  <div class="admin_page_name">站点设置</div>
  <div class="small_form small_form2">
    <div class="field">
      <div class="label">网站标题</div>
      <input class="textbox" type="text" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" />
      <div class="info">起个好听的名字。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站描述</div>
      <input class="textbox" type="text" name="site_desc" value="<?php echo htmlspecialchars($site_desc); ?>" />
      <div class="info">用简洁的文字描述本站点。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站关键词</div>
      <input class="textbox" type="text" name="site_keywords" value="<?php echo htmlspecialchars($site_keywords); ?>" />
      <div class="info">用关键词描述本站点，请使用半角逗号,分隔。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站地址</div>
      <input class="textbox" type="text" name="site_link" value="<?php echo htmlspecialchars($site_link); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站主题</div>
      <select name="site_theme" class="textbox">
      <?php foreach ($theme_list as $theme) { ?>
        <option
          value="<?php echo $theme; ?>"
          <?php if ($theme == $site_theme || empty($site_theme) && $theme == 'default') echo 'selected="selected";' ?>
        >
          <?php echo $theme; ?>
        </option>
      <?php } ?>
      </select>
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">链接形式</div>
      <select name="site_route" class="textbox">
        <option value="default" <?php if (empty($site_route) || $site_route == 'default') echo 'selected="selected"'; ?>>默认形式 domain.com/?post/pathname</option>
        <option value="path" <?php if ($site_route == 'path') echo 'selected="selected"'; ?>>路径模式 domain.com/post/pathname</option>
      </select>
      <div class="info">注意：路径模式需要服务端支持</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站状态</div>
      <select name="site_status" class="textbox">
        <option value="open" <?php if (empty($site_status) || $site_status == 'open') echo 'selected="selected"'; ?>>开通</option>
        <option value="closed" <?php if ($site_status == 'closed') echo 'selected="selected"'; ?>>关闭</option>
      </select>
      <div class="info">关闭网站时，前台显示关闭维护页面，所有页面不可看。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">ICP备案号</div>
      <input class="textbox" type="text" name="site_icpno" value="<?php echo $site_icpno; ?>" placeholder="工信部ICP备案号" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">站长昵称</div>
      <input class="textbox" type="text" name="nick_name" value="<?php echo htmlspecialchars($nick_name); ?>" placeholder="站长昵称" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">管理员帐号</div>
      <input class="textbox" type="text" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>" placeholder="后台管理员帐号" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">管理员密码</div>
      <input class="textbox" type="password" name="user_pass" placeholder="如需修改请填写新密码" />
      <div class="info">不修改请留空</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">确认密码</div>
      <input class="textbox" type="password" placeholder="如需修改请填写" />
      <div class="info">不修改请留空</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">评论代码</div>
      <textarea rows="5" class="textbox" name="comment_code"><?php echo htmlspecialchars($comment_code); ?></textarea>
      <div class="info">第三方评论服务所提供的评论代码，例如：<a href="http://disqus.com/" target="_blank">Disqus</a>、<a href="http://open.weibo.com/widget/comments.php" target="_blank">新浪微博评论箱</a> 等。设置此选项后，DouPress 就拥有了评论功能。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">底部代码</div>
      <textarea rows="5" class="textbox" name="footer_code"><?php echo htmlspecialchars($footer_code); ?></textarea>
      <div class="info">网站底部代码：如统计代码、文本文案等。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label"></div>
      <div class="field_body"><input class="button" type="submit" name="save" value="保存设置" /></div>
      <!-- <div class="info"></div> -->
    </div>
    <div class="clear"></div>
  </div>
</form>
<?php require 'foot.php' ?>
