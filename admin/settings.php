<?php
define('PATH_ROOT', dirname(dirname(__FILE__))); // 定义根路径
require_once PATH_ROOT . '/core/common.php';

dp_check_login();

$display_info = false;

$theme_files = scandir(PATH_ROOT . '/theme');
$theme_list = array();
$filter = array('.', '..', '.cvs', '.svn', '.git', '.DS_Store');
foreach ($theme_files as $file) {
    if (!is_dir($file) && !in_array($file, $filter)) {
      $theme_list[] = $file;
    }
}

if (isset($_POST['save'])) {
  $user_name_changed = $_POST['user_name'] != $dp_config['user_name'];

  $dp_config['site_name']    = $_POST['site_name'];
  $dp_config['site_desc']    = $_POST['site_desc'];
  $dp_config['site_link']    = $_POST['site_link'];
  $dp_config['site_theme']   = $_POST['site_theme'];
  $dp_config['site_route']   = $_POST['site_route'];
  $dp_config['site_icpno']   = $_POST['site_icpno'];
  $dp_config['user_nick']    = $_POST['user_nick'];
  $dp_config['user_name']    = $_POST['user_name'];
  $dp_config['comment_code'] = trim($_POST['comment_code']);

  if ($_POST['user_pass'] != '') {
    $dp_config['user_pass'] = $_POST['user_pass'];
  }

  $code = "<?php\n\$dp_config = " . var_export($dp_config, true) . "\n?>";

  file_put_contents(PATH_ROOT . '/data/config.php', $code);

  if ($_POST['user_pass'] != '' || $user_name_changed) {
    setcookie('mc_token', md5($dp_config['user_name'] . '_' . $dp_config['user_pass']));
  }

  $display_info = true;
}

$site_name   = $dp_config['site_name'];
$site_desc   = $dp_config['site_desc'];
$site_link   = $dp_config['site_link'];
$site_theme  = $dp_config['site_theme'];
$site_route  = $dp_config['site_route'];
$site_icpno  = $dp_config['site_icpno'];
$user_nick   = $dp_config['user_nick'];
$user_name   = $dp_config['user_name'];
$comment_code = isset($dp_config['comment_code']) ? $dp_config['comment_code'] : '';
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
      <div class="info">用简洁的文字没描述本站点。</div>
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
      <div class="label">网站URL类型</div>
      <select name="site_route" class="textbox">
        <option value="default" <?php if (empty($site_route) || $site_route == 'default') echo 'selected="selected"'; ?>>默认形式（domain.com/?post/abcdef）</option>
        <option value="path" <?php if ($site_route == 'path') echo 'selected="selected"'; ?>>路径模式（domain.com/post/abcdef）</option>
      </select>
      <div class="info">注意：路径模式需要服务端支持</div>
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
      <input class="textbox" type="text" name="user_nick" value="<?php echo htmlspecialchars($user_nick); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台帐号</div>
      <input class="textbox" type="text" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台密码</div>
      <input class="textbox" type="password" name="user_pass" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">确认密码</div>
      <input class="textbox" type="password" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">评论代码</div>
      <textarea rows="5" class="textbox" name="comment_code"><?php echo htmlspecialchars($comment_code); ?></textarea>
      <div class="info">第三方评论服务所提供的评论代码，例如：<a href="http://disqus.com/" target="_blank">Disqus</a>、<a href="http://open.weibo.com/widget/comments.php" target="_blank">新浪微博评论箱</a> 等。设置此选项后，DouPress 就拥有了评论功能。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <!-- <div class="label"></div> -->
      <div class="field_body"><input class="button" type="submit" name="save" value="保存设置" /></div>
      <!-- <div class="info"></div> -->
    </div>
    <div class="clear"></div>
  </div>
</form>
<?php require 'foot.php' ?>
