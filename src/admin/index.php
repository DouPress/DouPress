<?php
// 后台首页
require_once 'common.php';
app_check_login();

if (isset($_POST['login'])) {
  if (
    $_POST['user'] == $app_config['user_name']
    && $_POST['pass'] == $app_config['user_pass']
  ) {
    setcookie('token', md5($app_config['user_name'] . '_' . $app_config['user_pass']));
    // Header("Location: post.php");
  }
}
?>
<?php require 'head.php' ?>
<div>
  <div class="admin_page_name">管理面板</div>
  <div class="admin_page_name">
    <span>页面:</span>
    <a href="page-edit.php" class="link">创建页面</a>
    <a href="page.php" class="link">所有页面</a>
    <a href="page.php?state=publish" class="link">已发布</a>
    <a href="page.php?state=draft" class="link">草稿箱</a>
    <a href="page.php?state=delete" class="link">回收站</a>
  </div>
  <div class="admin_page_name">
    <span>文章:</span>
    <a href="post-edit.php" class="link">撰写文章</a>
    <a href="post.php" class="link">所有文章</a>
    <a href="post.php?state=publish" class="link">已发布</a>
    <a href="post.php?state=draft" class="link">草稿箱</a>
    <a href="post.php?state=delete" class="link">回收站</a>
  </div>
  <div class="admin_page_name">
    <span>前台:</span>
    <a href="<?php app_site_link(); ?>" class="link" target="_blank">首页</a>
    <a href="<?php app_get_url('archive'); ?>" class="link" target="_blank">存档</a>
    <a href="<?php app_get_url('rss'); ?>" class="link" target="_blank">订阅</a>
    <a href="<?php app_get_url('xml'); ?>" class="link" target="_blank">地图</a>
  </div>
  <div class="admin_page_name">
    <span>系统:</span>
    <a href="settings.php" class="link">设置</a>
    <a href="logout.php" class="link">退出登录</a>
  </div>
</div>

<div class="admin_index_help">
  <span>资源链接：</span>
  <span><a href="https://github.com/DouPress/DouPress" class="link" target="_blank">@Github</a></span>
  <span><a href="https://gitee.com/doupress/doupress" class="link" target="_blank">@Gitee</a></span>
  <span><a href="http://doupress.org" class="link" target="_blank">官方网站</a></span>
  <span><a href="http://crogram.com" class="link" target="_blank">技术支持</a></span>
</div>

<?php require 'foot.php' ?>