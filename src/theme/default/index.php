<?php
if (!isset($dp_config)) exit;
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="telephone=no,email=no" name="format-detection" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black" />
  <meta name="description" content="<?php echo $dp_config['site_desc'];?> - DouPress" />
  <meta name="keywords" content="DouPress" />
  <meta name="author" content="上海程江科技中心" />
  <meta name="copyright" content="程江® 程江科技 CROGRAM" />
  <link rel="icon" href="<?php mc_site_link(); ?>/favicon.ico" />
  <title><?php if (mc_is_post() || mc_is_page()) { mc_the_title(); ?> | <?php mc_site_name();} else { mc_site_name(); ?> | <?php mc_site_desc(); } ?></title>
  <link href="<?php mc_theme_url('style.css'); ?>" type="text/css" rel="stylesheet" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="sitename">
        <a href="<?php mc_site_link(); ?>" class="link"><?php mc_site_name(); ?></a>
      </div>
    </div>
    <div id="sidebar">
      <div id="navbar">
        <ul>
          <li><a href="<?php mc_site_link(); ?>" class="link">首页</a></li>
          <li><a href="<?php mc_get_url('archive'); ?>" class="link">存档</a></li>
          <li><a href="<?php mc_get_url('rss'); ?>" class="link">订阅</a></li>
        </ul>
      </div>
    </div>
    <div id="content">
      <div id="content_box">
        <?php if (mc_is_post()) { ?>
          <div class="post">
            <h1 class="title"><?php mc_the_link(); ?></h1>
            <div class="tags"><?php mc_the_tags('', '', ''); ?> by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div>
            <div class="content"><?php mc_the_content(); ?></div>
          </div>
          <?php if (mc_can_comment()) { ?>
            <?php mc_comment_code(); ?>
          <?php } ?>
        <?php } else if (mc_is_page()) { ?>
          <div class="post">
            <?php /*<h1 class="title"><?php mc_the_link(); ?></h1>
            <div class="tags">by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div> */ ?>
            <div class="content"><?php mc_the_content(); ?></div>
          </div>
          <?php if (mc_can_comment()) { ?>
            <?php mc_comment_code(); ?>
          <?php } ?>
        <?php } else if (mc_is_archive()) { ?>
          <div class="date_list">
            <h1>月份</h1>
            <ul><?php mc_date_list(); ?></ul>
          </div>
          <div class="tag_list">
            <h1>标签</h1>
            <ul><?php mc_tag_list(); ?></ul>
          </div>
          <div class="clearer"></div>
        <?php } else { ?>
          <?php if (mc_is_tag()) { ?>
            <div id="page_info">标签：<span><?php mc_tag_name(); ?></span></div>
          <?php } else if (mc_is_date()) { ?>
            <div id="page_info">月份：<span><?php mc_date_name(); ?></span></div>
          <?php } ?>
          <div class="post_list">
            <?php while (mc_next_post()) { ?>
              <div class="post">
                <h1 class="title"><?php mc_the_link(); ?></h1>
                <div class="tags"><?php mc_the_tags('', '', ''); ?> by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div>
                <div class="clearer"></div>
              </div>
            <?php   } ?>
            <div id="page_bar">
              <?php if (mc_has_new()) { ?>
                <span class="prev link" style="float:left;"><?php mc_goto_new('&larr; 较新文章'); ?></span>
              <?php   } ?>
              <?php if (mc_has_old()) { ?>
                <span class="next link" style="float:right;"><?php mc_goto_old('早期文章 &rarr;'); ?></span>
              <?php   } ?>
              <div class="clearer"></div>
            </div>
            <div class="clearer"></div>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="clearer"></div>
    <div id="footer">
      <div>本站由 <a href="https://github.com/doupress/doupress" class="link" target="_blank">DouPress</a> 驱动</div>
      <?php if (!empty($dp_config['site_icpno'])) { ?><p><a href="https://beian.miit.gov.cn/#/Integrated/index" class="link" target="_blank"><?php echo $dp_config['site_icpno'];?></a></p><?php } ?>
    </div>
  </div>
</body>

</html>