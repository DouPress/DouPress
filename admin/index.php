<?php
// 后台首页
define('PATH_ROOT', dirname(dirname(__FILE__))); // 根路径
require_once PATH_ROOT . '/core/common.php';

dp_check_login();

if (isset($_POST['login'])) {
  if (
    $_POST['user'] == $dp_config['user_name']
    && $_POST['pass'] == $dp_config['user_pass']
  ) {
    setcookie('mc_token', md5($dp_config['user_name'] . '_' . $dp_config['user_pass']));
    // Header("Location: post.php");
  }
}
?>
<?php require 'head.php' ?>
<div>
  <div>
    <div class="admin_page_name">网站:
      <a href="<?php mc_site_link(); ?>" class="link_button" target="_blank">首页</a>
      <a href="<?php mc_get_url('archive'); ?>" class="link_button" target="_blank">存档</a>
      <a href="<?php mc_get_url('rss'); ?>" class="link_button" target="_blank">订阅</a>
    </div>
  </div>
  <div class="admin_page_name">页面:
    <a href="page-edit.php" class="link_button">创建页面</a>
    <a href="page.php" class="link_button">所有页面</a>
    <a href="page.php?state=publish" class="link_button">已发布</a>
    <a href="page.php?state=draft" class="link_button">草稿箱</a>
    <a href="page.php?state=delete" class="link_button">回收站</a>
  </div>
</div>
<div>
  <div class="admin_page_name">文章:
    <a href="post-edit.php" class="link_button">撰写文章</a>
    <a href="post.php" class="link_button">所有文章</a>
    <a href="post.php?state=publish" class="link_button">已发布</a>
    <a href="post.php?state=draft" class="link_button">草稿箱</a>
    <a href="post.php?state=delete" class="link_button">回收站</a>
  </div>
</div>
<div>
  <div class="admin_page_name">系统:
    <a href="settings.php" class="link_button">设置</a>
    <a href="logout.php" class="link_button">退出登录</a>
  </div>
</div>
<hr />
<div>
  DouPress v<?php echo $dp_config['version']; ?>
  源码：<a href="https://github.com/doupress" class="link_button" target="_blank">Github</a>
</div>
<?php require 'foot.php' ?>