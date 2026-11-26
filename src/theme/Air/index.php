<?php
if (!isset($app_config)) exit;

/**
 * Air — DouPress 主题
 * 大气简洁 / 浅色系 / 首页 Hero 大屏 / 顶部菜单 / 无侧边栏 / 明暗模式自动+手动切换
 *
 * 结构：header 顶部菜单 → （首页）Hero → main 内容单列 → footer
 */

// 顶部菜单里的已发布页面（无侧边栏设计，页面入口直接放进顶部菜单）
$air_nav_pages = array();
$air_pages_index = PATH_ROOT . '/data/pages/index/publish.php';
if (is_file($air_pages_index)) {
  require $air_pages_index;
  $air_nav_pages = $app_pages;
}

// 明暗模式：auto 跟随系统，light / dark 手动指定（JS 写入 localStorage）
$air_theme_mode = 'auto';
if (isset($_COOKIE['dp_theme']) && in_array($_COOKIE['dp_theme'], array('auto', 'light', 'dark'), true)) {
  $air_theme_mode = $_COOKIE['dp_theme'];
}

?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="<?php echo $air_theme_mode; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php app_seo_description(); ?>" />
  <meta name="keywords" content="<?php app_seo_keywords(); ?>" />
  <meta name="generator" content="DouPress" />
  <link rel="icon" href="<?php app_site_link(); ?>/favicon.ico" />
  <title><?php app_seo_title(); ?></title>
  <script>
  // 明暗模式早期初始化：读取本地偏好并写到 <html>，避免页面加载闪烁
  (function () {
    try {
      var t = localStorage.getItem('dp_theme');
      if (t === 'light' || t === 'dark' || t === 'auto') {
        document.documentElement.setAttribute('data-theme', t);
      }
    } catch (e) {}
  })();
  </script>
  <link href="<?php app_theme_url('style.css'); ?>" rel="stylesheet" />
  <?php app_header_code(); ?>
</head>
<body>

<header class="site-header">
  <nav class="nav">
    <a class="brand" href="<?php app_site_link(); ?>"><?php app_site_name(); ?></a>

    <div class="menu">
      <?php foreach ($air_nav_pages as $air_path => $air_page) { ?>
        <a href="<?php app_get_url('', $air_path); ?>"><?php echo htmlspecialchars($air_page['title']); ?></a>
      <?php } ?>
      <a href="<?php app_get_url('archive'); ?>">存档</a>
      <a href="<?php app_get_url('rss'); ?>" target="_blank">RSS</a>
      <button class="theme-toggle" id="theme-toggle" type="button" title="切换明暗模式" aria-label="切换明暗模式">◐</button>
    </div>
  </nav>
</header>

<?php if ($app_path_type == 'index' && $app_page_no == 1) { ?>
<section class="hero">
  <h1 class="hero-title"><?php app_site_name(); ?></h1>
  <p class="hero-desc"><?php app_site_desc(); ?></p>
  <a class="hero-more" href="#posts" aria-label="向下浏览">↓</a>
</section>
<?php } ?>

<main id="posts">

<?php if (app_is_post()) { ?>
  <article class="single">
    <h1 class="single-title"><?php app_post_title(); ?></h1>
    <div class="meta">
      <time><?php app_post_date(); ?></time>
      <span><?php app_post_tags('', '', '', true); ?></span>
      <span><?php app_post_hits(); ?> 次浏览</span>
      <?php if (app_mod_time_has()) { ?><span>更新于 <?php app_mod_time(); ?></span><?php } ?>
    </div>
    <div class="content">
      <?php app_post_content(); ?>
    </div>
  </article>
  <?php if (app_can_comment()) { ?>
    <section class="comments"><?php app_comment_code(); ?></section>
  <?php } ?>

<?php } else if (app_is_page()) { ?>
  <article class="single">
    <h1 class="single-title"><?php app_post_title(); ?></h1>
    <div class="meta">
      <span><?php app_post_hits(); ?> 次浏览</span>
      <?php if (app_mod_time_has()) { ?><span>更新于 <?php app_mod_time(); ?></span><?php } ?>
    </div>
    <div class="content">
      <?php app_post_content(); ?>
    </div>
  </article>
  <?php if (app_can_comment()) { ?>
    <section class="comments"><?php app_comment_code(); ?></section>
  <?php } ?>

<?php } else if (app_is_archive()) { ?>
  <article class="single">
    <h1 class="single-title">存档</h1>
    <div class="content archive">
      <h2>月份</h2>
      <p class="archive-list"><?php app_date_list('<span>', '', '</span>'); ?></p>
      <h2>标签</h2>
      <p class="archive-list"><?php app_tag_list('<span>', '', '</span>'); ?></p>
    </div>
  </article>

<?php } else { ?>

  <?php if (app_is_tag()) { ?>
    <p class="filter-info">标签：<strong><?php app_tag_name(); ?></strong> 下的文章</p>
  <?php } else if (app_is_date()) { ?>
    <p class="filter-info">归档：<strong><?php app_date_name(); ?></strong> 的文章</p>
  <?php } ?>

  <div class="post-list">
    <?php while (app_next_post()) { ?>
      <a class="post-item" href="<?php app_post_url(); ?>">
        <span class="post-item-title"><?php app_post_title(); ?></span>
        <time class="post-item-date"><?php app_post_date(); ?></time>
      </a>
    <?php } ?>
  </div>

  <?php if (app_has_new() || app_has_old()) { ?>
    <nav class="pager">
      <?php if (app_has_new()) { app_goto_new('← 上一页'); } ?>
      <?php if (app_has_old()) { app_goto_old('下一页 →'); } ?>
    </nav>
  <?php } ?>

<?php } ?>

</main>

<footer class="site-footer">
  <p>
    © <?php echo date('Y'); ?> <?php app_site_name(); ?>
    · Powered by <a href="http://doupress.org" target="_blank">DouPress</a>
    · <?php app_site_hits(); ?> 次浏览
  </p>
</footer>

<script>
(function () {
  var KEY = 'dp_theme';
  var ORDER = ['auto', 'light', 'dark'];
  var ICON = { auto: '◐', light: '☀', dark: '☾' };
  var TITLE = { auto: '当前：跟随系统（点击切换为浅色）', light: '当前：浅色（点击切换为深色）', dark: '当前：深色（点击恢复跟随系统）' };
  var root = document.documentElement;
  var btn = document.getElementById('theme-toggle');

  function apply(mode) {
    root.setAttribute('data-theme', mode);
    if (btn) {
      btn.textContent = ICON[mode];
      btn.title = TITLE[mode];
    }
  }

  apply(root.getAttribute('data-theme') || 'auto');

  if (btn) {
    btn.addEventListener('click', function () {
      var cur = root.getAttribute('data-theme') || 'auto';
      var next = ORDER[(ORDER.indexOf(cur) + 1) % ORDER.length];
      try { localStorage.setItem(KEY, next); } catch (e) {}
      apply(next);
    });
  }
})();
</script>

<?php app_footer_code(); ?>
</body>
</html>
