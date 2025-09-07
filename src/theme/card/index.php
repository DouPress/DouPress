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
  <meta name="generator" content="DouPress"/>
  <meta name="author" content="上海程江科技中心" />
  <meta name="copyright" content="程江® 程江科技 CROGRAM" />
  <link rel="icon" href="<?php dp_site_link(); ?>/favicon.ico" />
  <title><?php if (dp_is_post() || dp_is_page()) {
    dp_post_title(); ?> | <?php dp_site_name();
  } else {
    dp_site_name(); ?> | <?php dp_site_desc();
  } ?></title>
  <link href="<?php dp_theme_url('style.css'); ?>" type="text/css" rel="stylesheet" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="sitename"><a href="<?php dp_site_link(); ?>" title="<?php dp_site_desc(); ?>"><?php dp_site_name(); ?></a></div>
    </div>
    <div class="clear"></div>
    <div id="sidebar">
      <div class="photo"><img src="<?php dp_theme_url('photo.jpg'); ?>"></div>
      <div class="about">简短介绍或者联系方式什么的；简短介绍或者联系方式什么的</div>
      <div id="navbar">
        <a href="<?php dp_site_link(); ?>" class="home" title="首页">首页</a>
        <a href="<?php dp_get_url('archive'); ?>" class="archive" title="文章存档">文章存档</a>
        <a href="<?php dp_get_url('about'); ?>" class="contact" title="联系方式">联系方式</a>
        <a href="<?php dp_get_url('rss'); ?>" class="rss" title="RSS订阅" target="_blank">RSS订阅</a>
      </div>
      <div class="clear"></div>
      <div id="footer">Powered by <a href="http://doupress.org" target="_blank">DouPress</a></div>
    </div>
    <div id="content">
      <div id="content_box">
        <?php if (dp_is_post()) { ?>
          <div class="post">
            <h1 class="title"><?php dp_post_link(); ?></h1>
            <div class="content">
              <?php dp_post_content(); ?>
            </div>
            <div class="post_meta">
              <div class="post_date"><?php dp_post_date(); ?></div>
              <div class="post_tag"><?php dp_post_tags('', '', ''); ?></div>
              <div class="post_comm"><a href="<?php
              //mc_post_link(); 
              ?>#comm">评论</a></div>
            </div>
          </div>
        <?php if (dp_can_comment()) { ?>
          <div id="comm"><?php dp_comment_code(); ?></div>
        <?php } ?>

        <?php } else if (dp_is_page()) { ?>
          <div class="post">
            <!-- <h1 class="title"><?php
            //  mc_page_title();
            ?></h1> -->
            <div class="content">
              <?php dp_post_content(); ?>
            </div>
          </div>
          <?php if (dp_can_comment()) { ?>
            <div id="comm"><?php dp_comment_code(); ?></div>
          <?php } ?>
        <?php } else if (dp_is_archive()) { ?>
          <div class="post">
            <h1 class="title">文章存档</h1>
            <div class="content">
              <table width="280" border="0" align="center" cellpadding="0" cellspacing="0" style="margin:30px auto;">
                <tbody>
                  <tr>
                    <td width="140" style="vertical-align:top;">
                      <h1 class="date_list">月份</h1>
                      <ul id="list"><?php dp_date_list(); ?></ul>
                    </td>
                    <td width="140" style="vertical-align:top;">
                      <h1 class="tag_list">标签</h1>
                      <ul id="list"><?php dp_tag_list(); ?></ul>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        <?php } else { ?>
          <?php if (dp_is_tag()) { ?>
            <div id="page_info"><span><?php dp_tag_name(); ?></span></div>
          <?php } else if (dp_is_date()) { ?>
            <div id="page_info"><span><?php dp_date_name(); ?></span></div>
          <?php } ?>
          <?php while (dp_next_post()) { ?>
            <div class="post">
              <h1 class="title"><?php dp_post_link(); ?></h1>
              <div class="content">
                <?php dp_post_content(); ?>
              </div>
              <div class="post_meta">
                <div class="post_date"><?php dp_post_date(); ?></div>
                <div class="post_tag"><?php dp_post_tags('', '', ''); ?></div>
                <div class="post_comm"><a href="<?php 
                //mc_post_link(); 
                ?>#comm">评论</a></div>
              </div>
            </div>
          <?php } ?>
      </div>
      <div class="clear"></div>
      <div id="page_bar">
        <?php if (dp_has_new()) { ?><?php dp_goto_new('«'); ?><?php } ?>
        <?php if (dp_has_old()) { ?><?php dp_goto_old('»'); ?><?php } ?>
      </div>
    <?php } ?>
    </div>
  </div>
<?php dp_footer_code(); ?>
</body>

</html>