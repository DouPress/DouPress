<?php
if (!isset($app_config)) exit;

/**
 * Firm — DouPress 传统企业网站主题
 * 经典企业官网版式：顶栏 + Logo 头部 + 全宽主导航色条 + Banner
 * 首页分栏目（关于我们 / 产品中心 / 新闻中心带侧栏 / 合作条）+ 深色页脚四栏 + 版权条
 * 明暗切换 / 返回顶部 / 内页带侧栏（文章和页面详情仍为无侧边栏单栏）
 */

/* ===== 主题可调参数 ===== */
// 产品中心栏目使用哪个标签下的文章（标签不存在时取第一个实际存在的标签兜底）
$firm_product_tag = '产品';
// 公司名（页脚展示；留空则使用站点名称）
$firm_company = '';
// 开源仓库地址
$firm_repo_url = 'http://doupress.org';
// 联系方式（页脚"联系我们"栏展示，留空不显示对应行）
$firm_hotline = '';
$firm_email = '';
$firm_address = '';
// "关于我们"右侧的服务/优势卡片（图标 + 标题 + 描述）
$firm_features = array(
  array('icon' => '▣', 'title' => '开源开放', 'desc' => '核心代码完全开源，欢迎社区共同参与建设与贡献。'),
  array('icon' => '⚡', 'title' => '轻量高效', 'desc' => '无框架依赖，部署简单，低配置服务器亦可流畅运行。'),
  array('icon' => '✦', 'title' => '简单易用', 'desc' => '后台操作直观，内容管理一步到位，快速搭建企业站点。'),
);
/* ====================== */

// 明暗模式：auto 跟随系统，light / dark 手动指定（JS 写入 localStorage，cookie 兜底）
$firm_theme_mode = 'auto';
if (isset($_COOKIE['dp_theme']) && in_array($_COOKIE['dp_theme'], array('auto', 'light', 'dark'), true)) {
  $firm_theme_mode = $_COOKIE['dp_theme'];
}

/**
 * 加载已发布文章索引（保存/恢复全局状态，不污染当前页面数据）
 * 返回 $app_posts 数组，加载失败返回空数组
 */
function firm_load_publish()
{
  global $app_posts;

  $saved = isset($app_posts) ? $app_posts : null;

  $file = PATH_ROOT . '/data/posts/index/publish.php';
  $all = array();
  if (is_file($file)) {
    require $file;
    $all = $app_posts;
  }

  if ($saved !== null) {
    $app_posts = $saved;
  } else {
    unset($app_posts);
  }

  return $all;
}

/** 指定标签下的文章（按时间倒序），$limit 限制条数 */
function firm_posts_by_tag($tag, $limit)
{
  $all = firm_load_publish();
  $result = array();
  foreach ($all as $id => $post) {
    if (in_array($tag, (array)$post['tags'], true)) {
      $result[$id] = $post;
    }
  }
  return array_slice($result, 0, $limit, true);
}

/** 最新文章，$limit 限制条数 */
function firm_latest_posts($limit)
{
  return array_slice(firm_load_publish(), 0, $limit, true);
}

/** 已发布文章实际使用的标签（保持出现顺序） */
function firm_used_tags()
{
  $used = array();
  foreach (firm_load_publish() as $post) {
    foreach ((array)$post['tags'] as $tag) {
      $used[$tag] = true;
    }
  }
  return array_keys($used);
}

/* 产品中心实际使用的标签 */
$firm_used_tags = firm_used_tags();
$firm_prod_tag = in_array($firm_product_tag, $firm_used_tags, true) ? $firm_product_tag : (isset($firm_used_tags[0]) ? $firm_used_tags[0] : '');
$firm_products = $firm_prod_tag != '' ? firm_posts_by_tag($firm_prod_tag, 6) : array();

/* 新闻中心取最新文章 */
$firm_news = firm_latest_posts(7);

// 版权行的公司名称
$firm_company_name = $firm_company != '' ? $firm_company : app_site_name(false);

// 顶部菜单里的已发布页面
$firm_nav_pages = array();
$firm_pages_index = PATH_ROOT . '/data/pages/index/publish.php';
if (is_file($firm_pages_index)) {
  require $firm_pages_index;
  $firm_nav_pages = $app_pages;
}

// Banner 主按钮：指向产品中心标签，无标签时回退资源页
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="<?php echo $firm_theme_mode; ?>">
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

<!-- 顶栏：欢迎语 + 常用链接 -->
<div class="topbar">
  <div class="topbar-inner">
    <span class="topbar-welcome">欢迎访问 <?php app_site_name(); ?> 官方网站！</span>
    <div class="topbar-links">
      <a href="<?php echo $firm_repo_url; ?>" target="_blank">开源仓库</a>
      <a href="<?php app_get_url('archive'); ?>">资源中心</a>
      <a href="<?php app_get_url('rss'); ?>" target="_blank">RSS</a>
    </div>
  </div>
</div>

<!-- 头部：Logo + 明暗切换 -->
<header class="site-header">
  <div class="header-inner">
    <a class="brand" href="<?php app_site_link(); ?>"><?php app_site_name(); ?></a>
    <button class="theme-toggle" id="theme-toggle" type="button" title="切换明暗模式" aria-label="切换明暗模式">◐</button>
  </div>
</header>

<!-- 主导航：全宽色条 -->
<nav class="mainnav">
  <div class="mainnav-inner">
    <a<?php if ($app_path_type == 'index' && $app_page_no == 1) { ?> class="active"<?php } ?> href="<?php app_site_link(); ?>">首页</a>
    <?php foreach ($firm_nav_pages as $firm_path => $firm_page) { ?>
      <a href="<?php app_get_url('', $firm_path); ?>"><?php echo htmlspecialchars($firm_page['title']); ?></a>
    <?php } ?>
    <?php if ($firm_prod_tag != '') { ?>
      <a<?php if (app_is_tag() && app_tag_name(false) == $firm_prod_tag) { ?> class="active"<?php } ?> href="<?php app_get_url('tag', urlencode($firm_prod_tag)); ?>">产品中心</a>
    <?php } ?>
    <a href="<?php app_site_link(false); ?>/#news">新闻动态</a>
    <a<?php if (app_is_archive()) { ?> class="active"<?php } ?> href="<?php app_get_url('archive'); ?>">资源中心</a>
  </div>
</nav>

<?php if ($app_path_type == 'index' && $app_page_no == 1) { ?>

<!-- Banner：传统企业站横幅 -->
<section class="banner">
  <div class="banner-inner">
    <span class="banner-badge">开源 · 自托管 · 轻量</span>
    <h1 class="banner-title"><?php app_site_name(); ?></h1>
    <p class="banner-desc"><?php app_site_desc(); ?></p>
    <div class="banner-actions">
      <a class="btn btn-primary" href="<?php
        if ($firm_prod_tag != '') {
          app_get_url('tag', urlencode($firm_prod_tag));
        } else {
          app_get_url('archive');
        } ?>">了解产品</a>
      <a class="btn btn-ghost" href="<?php echo $firm_repo_url; ?>" target="_blank">开源仓库</a>
    </div>
  </div>
</section>

<main class="home">

  <!-- 关于我们 -->
  <section class="section" id="about">
    <div class="section-title-wrap">
      <h2 class="section-title">关于我们</h2>
      <span class="section-sub">ABOUT US</span>
    </div>
    <div class="about-grid">
      <div class="about-intro">
        <p><?php app_site_desc(); ?></p>
        <p>我们坚持开源与自托管的理念，为用户提供轻量、稳定、易用的软件产品与服务，欢迎垂询与交流。</p>
        <a class="more-link" href="<?php app_get_url('archive'); ?>">了解更多 →</a>
      </div>
      <div class="about-features">
        <?php foreach ($firm_features as $f) { ?>
          <div class="feature-card">
            <span class="feature-icon"><?php echo $f['icon']; ?></span>
            <div>
              <p class="feature-title"><?php echo htmlspecialchars($f['title']); ?></p>
              <p class="feature-desc"><?php echo htmlspecialchars($f['desc']); ?></p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- 产品中心 -->
  <section class="section section-alt" id="products">
    <div class="section-title-wrap">
      <h2 class="section-title">产品中心</h2>
      <span class="section-sub">PRODUCTS</span>
      <a class="more-link" href="<?php app_get_url('tag', urlencode($firm_prod_tag)); ?>">更多产品 &gt;</a>
    </div>
    <?php if (!empty($firm_products)) { ?>
      <div class="product-grid">
        <?php foreach ($firm_products as $firm_pid => $firm_post) { ?>
          <a class="product-card" href="<?php app_get_url('post', $firm_pid); ?>">
            <span class="product-tag"><?php echo htmlspecialchars($firm_prod_tag); ?></span>
            <p class="product-title"><?php echo htmlspecialchars($firm_post['title']); ?></p>
            <p class="product-desc"><?php echo htmlspecialchars($firm_post['desc'] != '' ? $firm_post['desc'] : '查看详情'); ?></p>
            <span class="product-more">查看详情 →</span>
          </a>
        <?php } ?>
      </div>
    <?php } else { ?>
      <p class="empty-tip">暂无产品内容，可在后台为文章添加「<?php echo htmlspecialchars($firm_product_tag); ?>」标签后展示。</p>
    <?php } ?>
  </section>

  <!-- 新闻中心（左侧列表 + 右侧栏） -->
  <section class="section" id="news">
    <div class="section-title-wrap">
      <h2 class="section-title">新闻中心</h2>
      <span class="section-sub">NEWS</span>
      <a class="more-link" href="<?php app_get_url('archive'); ?>">更多动态 &gt;</a>
    </div>
    <div class="news-grid">
      <div class="news-list">
        <?php foreach ($firm_news as $firm_nid => $firm_post) { ?>
          <a class="news-item" href="<?php app_get_url('post', $firm_nid); ?>">
            <span class="news-item-dot"></span>
            <span class="news-item-title"><?php echo htmlspecialchars($firm_post['title']); ?></span>
            <time class="news-item-date"><?php echo $firm_post['date']; ?></time>
          </a>
        <?php } ?>
      </div>
      <aside class="news-side">
        <div class="side-card">
          <p class="side-title">产品分类</p>
          <ul class="side-tags">
            <?php foreach ($firm_used_tags as $firm_tag) { ?>
              <li><a href="<?php app_get_url('tag', urlencode($firm_tag)); ?>"><?php echo htmlspecialchars($firm_tag); ?></a></li>
            <?php } ?>
          </ul>
        </div>
        <div class="side-card side-card-accent">
          <p class="side-title">联系我们</p>
          <ul class="side-contact">
            <?php if ($firm_hotline != '') { ?><li>服务热线：<?php echo htmlspecialchars($firm_hotline); ?></li><?php } ?>
            <?php if ($firm_email != '') { ?><li>邮箱：<?php echo htmlspecialchars($firm_email); ?></li><?php } ?>
            <?php if ($firm_address != '') { ?><li>地址：<?php echo htmlspecialchars($firm_address); ?></li><?php } ?>
            <li><a href="<?php echo $firm_repo_url; ?>" target="_blank">访问开源仓库 →</a></li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  <!-- 合作条 -->
  <section class="cta">
    <div class="cta-inner">
      <div>
        <p class="cta-title">携手共创 · 合作共赢</p>
        <p class="cta-desc">欢迎与我们联系，共同探讨软件合作与技术交流。</p>
      </div>
      <a class="btn btn-inverse" href="<?php echo $firm_repo_url; ?>" target="_blank">联系我们</a>
    </div>
  </section>

</main>

<?php } else { ?>

<div class="page-wrap">
  <main class="inner">

  <?php if (app_is_post()) { ?>
    <article class="single">
      <h1 class="single-title"><?php app_post_title(); ?></h1>
      <div class="meta">
        <time><?php app_post_date(); ?></time>
        <span><?php $firm_post_tags = isset($app_post['tags']) ? $app_post['tags'] : array(); foreach ($firm_post_tags as $firm_tag) { ?><a class="meta-tag" href="<?php app_get_url('tag', urlencode($firm_tag)); ?>"><?php echo htmlspecialchars($firm_tag); ?></a><?php } ?></span>
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
      <h1 class="single-title">资源中心</h1>
      <div class="content archive">
        <h2>按月份</h2>
        <p class="archive-list"><?php app_date_list('<span>', '', '</span>'); ?></p>
        <h2>按分类</h2>
        <p class="archive-list"><?php app_tag_list('<span>', '', '</span>'); ?></p>
      </div>
    </article>

  <?php } else { ?>
    <!-- 列表页：传统企业站内页带侧栏 -->
    <div class="list-grid">
      <div class="list-main">
        <?php if (app_is_tag()) { ?>
          <p class="filter-info">分类：<strong><?php app_tag_name(); ?></strong></p>
        <?php } else if (app_is_date()) { ?>
          <p class="filter-info">归档：<strong><?php app_date_name(); ?></strong></p>
        <?php } else if ($app_page_no != 1) { ?>
          <p class="filter-info">新闻动态 · 第 <?php echo $app_page_no; ?> 页</p>
        <?php } else { ?>
          <p class="filter-info"><strong>新闻动态</strong></p>
        <?php } ?>

        <div class="news-list">
          <?php while (app_next_post()) { ?>
            <a class="news-item" href="<?php app_post_url(); ?>">
              <span class="news-item-dot"></span>
              <span class="news-item-title"><?php app_post_title(); ?></span>
              <time class="news-item-date"><?php app_post_date(); ?></time>
            </a>
          <?php } ?>
        </div>

        <?php if (app_has_new() || app_has_old()) { ?>
          <nav class="pager">
            <?php if (app_has_new()) { app_goto_new('← 上一页'); } ?>
            <?php if (app_has_old()) { app_goto_old('下一页 →'); } ?>
          </nav>
        <?php } ?>
      </div>

      <aside class="news-side">
        <div class="side-card">
          <p class="side-title">产品分类</p>
          <ul class="side-tags">
            <?php foreach ($firm_used_tags as $firm_tag) { ?>
              <li><a href="<?php app_get_url('tag', urlencode($firm_tag)); ?>"><?php echo htmlspecialchars($firm_tag); ?></a></li>
            <?php } ?>
          </ul>
        </div>
        <div class="side-card">
          <p class="side-title">最近动态</p>
          <ul class="side-posts">
            <?php foreach (firm_latest_posts(6) as $firm_nid => $firm_post) { ?>
              <li><a href="<?php app_get_url('post', $firm_nid); ?>"><?php echo htmlspecialchars($firm_post['title']); ?></a></li>
            <?php } ?>
          </ul>
        </div>
        <div class="side-card side-card-accent">
          <p class="side-title">联系我们</p>
          <ul class="side-contact">
            <?php if ($firm_hotline != '') { ?><li>服务热线：<?php echo htmlspecialchars($firm_hotline); ?></li><?php } ?>
            <?php if ($firm_email != '') { ?><li>邮箱：<?php echo htmlspecialchars($firm_email); ?></li><?php } ?>
            <li><a href="<?php app_get_url('archive'); ?>">全部资源 →</a></li>
          </ul>
        </div>
      </aside>
    </div>
  <?php } ?>

  </main>
</div>

<?php } ?>

<!-- 页脚：深色底四栏 -->
<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-col footer-brand">
      <p class="footer-logo"><?php app_site_name(); ?></p>
      <p class="footer-desc"><?php app_site_desc(); ?></p>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">产品分类</p>
      <ul>
        <?php foreach ($firm_used_tags as $firm_tag) { ?>
          <li><a href="<?php app_get_url('tag', urlencode($firm_tag)); ?>"><?php echo htmlspecialchars($firm_tag); ?></a></li>
        <?php } ?>
      </ul>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">快速导航</p>
      <ul>
        <li><a href="<?php app_site_link(false); ?>/#about">关于我们</a></li>
        <?php if ($firm_prod_tag != '') { ?>
          <li><a href="<?php app_site_link(false); ?>/#products">产品中心</a></li>
        <?php } ?>
        <li><a href="<?php app_site_link(false); ?>/#news">新闻动态</a></li>
        <?php foreach ($firm_nav_pages as $firm_path => $firm_page) { ?>
          <li><a href="<?php app_get_url('', $firm_path); ?>"><?php echo htmlspecialchars($firm_page['title']); ?></a></li>
        <?php } ?>
      </ul>
    </div>

    <div class="footer-col">
      <p class="footer-col-title">联系我们</p>
      <ul>
        <?php if ($firm_hotline != '') { ?><li>服务热线：<?php echo htmlspecialchars($firm_hotline); ?></li><?php } ?>
        <?php if ($firm_email != '') { ?><li>邮箱：<?php echo htmlspecialchars($firm_email); ?></li><?php } ?>
        <?php if ($firm_address != '') { ?><li>地址：<?php echo htmlspecialchars($firm_address); ?></li><?php } ?>
        <li><a href="<?php app_get_url('archive'); ?>">资源中心</a></li>
        <li><a href="<?php echo $firm_repo_url; ?>" target="_blank">开源仓库</a></li>
        <li><a href="<?php app_get_url('rss'); ?>" target="_blank">RSS 订阅</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($firm_company_name); ?> 版权所有 · Powered by <a href="http://doupress.org" target="_blank">DouPress</a></p>
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
