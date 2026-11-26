<?php
if (!isset($app_config)) exit;

/**
 * Biz — DouPress 企业展示主题
 * 商务风格 / 浅蓝色 / 软件行业 / 开源软件 / 明暗切换 / 无侧边栏 / 返回顶部
 *
 * 结构：header 专业顶部菜单 → （首页）商务 Hero → main 内容单列 → footer 分类链接 + 版权
 */

/* ===== 主题可调参数 ===== */
// 底部用作分类的标签（按顺序匹配实际存在的标签；全部不存在时取前 4 个标签兜底）
$air_footer_tags = array('产品', '帮助', '法律', '通知');
// 公司名（版权行展示；留空则使用站点名称）
$air_company = '';
// 开源仓库地址（顶部 Hero 与底部使用）
$air_repo_url = 'http://doupress.org';
/* ====================== */

// 明暗模式：auto 跟随系统，light / dark 手动指定（JS 写入 localStorage，cookie 兜底）
$air_theme_mode = 'auto';
if (isset($_COOKIE['dp_theme']) && in_array($_COOKIE['dp_theme'], array('auto', 'light', 'dark'), true)) {
  $air_theme_mode = $_COOKIE['dp_theme'];
}

// 顶部菜单里的已发布页面（无侧边栏，页面入口进顶部菜单）
$air_nav_pages = array();
$air_pages_index = PATH_ROOT . '/data/pages/index/publish.php';
if (is_file($air_pages_index)) {
  require $air_pages_index;
  $air_nav_pages = $app_pages;
}

/**
 * 获取底部展示的分类（标签名列表，只放分类链接，不列文章）
 * 优先取 $air_footer_tags 中实际存在的标签；均不存在时取前 4 个实际标签兜底
 */
function air_footer_tags()
{
  global $app_posts, $app_post_i, $app_post_i_end;

  $saved_posts   = isset($app_posts) ? $app_posts : null;
  $saved_i       = isset($app_post_i) ? $app_post_i : null;
  $saved_i_end   = isset($app_post_i_end) ? $app_post_i_end : null;

  $all = array();
  $file = PATH_ROOT . '/data/posts/index/publish.php';
  if (is_file($file)) {
    require $file;
    $all = $app_posts;
  }

  if ($saved_posts !== null) {
    $app_posts = $saved_posts;
  } else {
    unset($app_posts);
  }
  if ($saved_i !== null) {
    $app_post_i = $saved_i;
    $app_post_i_end = $saved_i_end;
  }

  // 已发布文章实际使用的标签（保持出现顺序）
  $used = array();
  foreach ($all as $p) {
    foreach ((array)$p['tags'] as $tag) {
      $used[$tag] = true;
    }
  }
  $used = array_keys($used);

  $result = array();
  foreach ($GLOBALS['air_footer_tags'] as $want) {
    if (in_array($want, $used, true)) {
      $result[] = $want;
    }
  }

  if (empty($result)) {
    $result = array_slice($used, 0, 4);
  }

  return $result;
}

$air_footer_cols = air_footer_tags();

// 版权行的公司名称
$air_company_name = $air_company != '' ? $air_company : app_site_name(false);

// Hero 主按钮：优先指向第一个分类，无分类时回退到资源页
$air_cta_tag = !empty($air_footer_cols) ? $air_footer_cols[0] : '';

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
      <a href="<?php app_get_url('archive'); ?>">资源</a>
      <a href="<?php echo $air_repo_url; ?>" target="_blank">开源仓库</a>
      <a href="<?php app_get_url('rss'); ?>" target="_blank">RSS</a>
      <button class="theme-toggle" id="theme-toggle" type="button" title="切换明暗模式" aria-label="切换明暗模式">◐</button>
    </div>
  </nav>
</header>

<?php if ($app_path_type == 'index' && $app_page_no == 1) { ?>
<section class="hero">
  <span class="hero-badge">开源 · 自托管 · 轻量</span>
  <h1 class="hero-title"><?php app_site_name(); ?></h1>
  <p class="hero-desc"><?php app_site_desc(); ?></p>
  <div class="hero-actions">
    <a class="btn btn-primary" href="<?php
      if ($air_cta_tag !== '') {
        app_get_url('tag', urlencode($air_cta_tag));
      } else {
        app_get_url('archive');
      } ?>">了解产品</a>
    <a class="btn btn-ghost" href="<?php echo $air_repo_url; ?>" target="_blank">开源仓库</a>
  </div>
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
    <h1 class="single-title">资源</h1>
    <div class="content archive">
      <h2>按月份</h2>
      <p class="archive-list"><?php app_date_list('<span>', '', '</span>'); ?></p>
      <h2>按分类</h2>
      <p class="archive-list"><?php app_tag_list('<span>', '', '</span>'); ?></p>
    </div>
  </article>

<?php } else { ?>

  <?php if (app_is_tag()) { ?>
    <p class="filter-info">分类：<strong><?php app_tag_name(); ?></strong></p>
  <?php } else if (app_is_date()) { ?>
    <p class="filter-info">归档：<strong><?php app_date_name(); ?></strong></p>
  <?php } else if ($app_page_no != 1) { ?>
    <p class="filter-info">最新动态 · 第 <?php echo $app_page_no; ?> 页</p>
  <?php } else { ?>
    <div class="section-head"><h2>新闻动态</h2><a href="<?php app_get_url('archive'); ?>">查看全部 →</a></div>
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
  <div class="footer-top">
    <div class="footer-brand">
      <p class="footer-logo"><?php app_site_name(); ?></p>
      <p class="footer-desc"><?php app_site_desc(); ?></p>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">产品分类</p>
      <ul>
        <?php foreach ($air_footer_cols as $air_tag) { ?>
          <li><a href="<?php app_get_url('tag', urlencode($air_tag)); ?>"><?php echo htmlspecialchars($air_tag); ?></a></li>
        <?php } ?>
      </ul>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">快速导航</p>
      <ul>
        <?php foreach ($air_nav_pages as $air_path => $air_page) { ?>
          <li><a href="<?php app_get_url('', $air_path); ?>"><?php echo htmlspecialchars($air_page['title']); ?></a></li>
        <?php } ?>
      </ul>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">相关资源</p>
      <ul>
        <li><a href="<?php app_get_url('archive'); ?>">全部资源</a></li>
        <li><a href="<?php echo $air_repo_url; ?>" target="_blank">开源仓库</a></li>
        <li><a href="<?php app_get_url('rss'); ?>" target="_blank">RSS 订阅</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($air_company_name); ?> 保留所有权利 · Powered by <a href="http://doupress.org" target="_blank">DouPress</a></p>
    <?php if (isset($app_config['site_icpno']) && $app_config['site_icpno'] != '') { ?>
      <p class="footer-icp"><?php echo $app_config['site_icpno']; ?></p>
    <?php } ?>
  </div>
</footer>

<button class="back-top" id="back-top" type="button" title="返回顶部" aria-label="返回顶部">↑</button>

<script>
(function () {
  /* 明暗模式：跟随系统 → 浅色 → 深色 循环切换 */
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

  /* 返回顶部 */
  var topBtn = document.getElementById('back-top');
  if (topBtn) {
    var onScroll = function () {
      topBtn.className = 'back-top' + ((window.scrollY || 0) > 400 ? ' back-top-show' : '');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    topBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
})();
</script>

<?php app_footer_code(); ?>
</body>
</html>
