<?php
require_once dirname(__FILE__) . '/Michelf/MarkdownExtra.inc.php';

use Michelf\MarkdownExtra;

function app_site_name($print = true)
{
  global $app_config;

  $site_name = htmlspecialchars($app_config['site_name']);

  if ($print) {
    echo $site_name;
    return;
  }

  return $site_name;
}

function app_site_desc($print = true)
{
  global $app_config;

  $site_desc = htmlspecialchars($app_config['site_desc']);

  if ($print) {
    echo $site_desc;
    return;
  }

  return $site_desc;
}

/**
 * 网站副标题（未配置时用网站描述）
 */
function app_site_subname($print = true)
{
  global $app_config;

  $subname = (isset($app_config['site_subname']) && $app_config['site_subname'] != '')
    ? $app_config['site_subname']
    : $app_config['site_desc'];

  $subname = htmlspecialchars($subname);

  if ($print) {
    echo $subname;
    return;
  }

  return $subname;
}

/**
 * 网站主域名链接
 */
function app_site_link($print = true)
{
  global $app_config;

  $site_link = htmlentities($app_config['site_link']);

  if ($print) {
    echo $site_link;
    return;
  }

  return $site_link;
}

/**
 * 站长称呼
 */
function app_nick_name($print = true)
{
  global $app_config;

  $nick_name = htmlspecialchars($app_config['nick_name']);

  if ($print) {
    echo $nick_name;
    return;
  }

  return $nick_name;
}

/**
 * 网页标题（文章/页面用标题，其他用网站名称）
 */
function app_seo_title($print = true)
{
  if (app_is_post() || app_is_page()) {
    $title = app_post_title(false) . ' | ' . app_site_name(false);
  } else {
    $title = app_site_name(false) . ' | ' . app_site_subname(false);
  }

  if ($print) {
    echo $title;
    return;
  }

  return $title;
}

/**
 * 网页SEO描述（文章/页面用desc，其他用网站描述）
 */
function app_seo_description($print = true)
{
  global $app_seo_description, $app_config;

  $desc = !empty($app_seo_description) ? $app_seo_description : $app_config['site_desc'];
  $desc = htmlspecialchars($desc);

  if ($print) {
    echo $desc;
    return;
  }

  return $desc;
}

/**
 * 网页SEO关键词（文章/页面用tags，其他用网站关键词；$fallback为无数据时的回退值）
 */
function app_seo_keywords($fallback = '', $print = true)
{
  global $app_seo_keywords, $app_config;

  if (!empty($app_seo_keywords)) {
    $keywords = $app_seo_keywords;
  } else if ($fallback != '') {
    $keywords = $fallback;
  } else {
    $keywords = isset($app_config['site_keywords']) ? $app_config['site_keywords'] : '';
  }

  $keywords = htmlspecialchars($keywords);

  if ($print) {
    echo $keywords;
    return;
  }

  return $keywords;
}

function app_theme_url($path, $print = true)
{
  global $app_config;
  $url = htmlentities($app_config['site_link']) . '/theme/' . $app_config['site_theme'] . '/' . $path;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

function app_is_post()
{
  global $app_path_type;

  return $app_path_type == 'post';
}

function app_is_page()
{
  global $app_path_type;

  return $app_path_type == 'page';
}

function app_is_tag()
{
  global $app_path_type;
  return $app_path_type == 'tag';
}

function app_is_date()
{
  global $app_path_type;
  return $app_path_type == 'date';
}

function app_is_archive()
{
  global $app_path_type;
  return $app_path_type == 'archive';
}

/**
 * 标签列表
 */
function app_tag_name($print = true)
{
  global $app_path_name;

  if ($print) {
    echo htmlspecialchars($app_path_name);
    return;
  }

  return $app_path_name;
}

/**
 * 年月列表
 */
function app_date_name($print = true)
{
  global $app_path_name;

  if ($print) {
    echo htmlspecialchars($app_path_name);
    return;
  }

  return $app_path_name;
}

function app_has_new()
{
  global $app_page_no;

  return $app_page_no != 1;
}

function app_has_old()
{
  global $app_page_no, $app_post_count, $app_post_per_page;

  return $app_page_no < ceil($app_post_count / $app_post_per_page);
}

function app_goto_old($text)
{
  global $app_path_type, $app_path_name, $app_page_no;
  echo '<a href="';
  if ($app_path_type == 'tag') {
    app_get_url('tag', htmlspecialchars($app_path_name));
  } else if ($app_path_type == 'date') {
    app_get_url('date', htmlspecialchars($app_path_name));
  }
  echo '/?page=';
  echo ($app_page_no + 1);
  echo '">';
  echo $text;
  echo '</a>';
}

function app_goto_new($text)
{
  global $app_path_type, $app_path_name, $app_page_no;
  echo '<a href="';
  if ($app_path_type == 'tag') {
    app_get_url('tag', htmlspecialchars($app_path_name));
  } else if ($app_path_type == 'date') {
    app_get_url('date', htmlspecialchars($app_path_name));
  }
  echo '/?page=';
  echo ($app_page_no - 1);
  echo '">';
  echo $text;
  echo '</a>';
}

function app_date_list($item_begin = '<li>', $item_gap = '', $item_end = '</li>')
{
  global $app_dates;

  if (isset($app_dates)) {
    $date_count = count($app_dates);

    for ($i = 0; $i < $date_count; $i++) {
      $date = $app_dates[$i];

      echo $item_begin;
      echo '<a href="';
      app_get_url('date', $date);
      echo '">';
      echo $date;
      echo '</a>';
      echo $item_end;

      if ($i < $date_count - 1)
        echo $item_gap;
    }
  }
}

function app_tag_list($item_begin = '<li>', $item_gap = '', $item_end = '</li>')
{
  global $app_tags;

  if (isset($app_tags)) {
    $tag_count = count($app_tags);

    for ($i = 0; $i < $tag_count; $i++) {
      $tag = $app_tags[$i];

      echo $item_begin;
      echo '<a href="';
      app_get_url('tag', urlencode($tag));
      echo '">';
      echo htmlspecialchars($tag);
      echo '</a>';
      echo $item_end;

      if ($i < $tag_count - 1)
        echo $item_gap;
    }
  }
}

function app_next_post()
{
  global $app_posts, $app_post_ids, $app_post_count, $app_post_i, $app_post_i_end, $app_post_id, $app_post, $app_page_no, $app_post_per_page;

  if (!isset($app_posts)) {
    return false;
  }

  if (!isset($app_post_i)) {
    $app_post_i = 0 + ($app_page_no - 1) * $app_post_per_page;
    $app_post_i_end = $app_post_i + $app_post_per_page;
    if ($app_post_count < $app_post_i_end)
      $app_post_i_end = $app_post_count;
  }

  if ($app_post_i == $app_post_i_end) {
    return false;
  }

  if (!isset($app_post_ids[$app_post_i])) {
    return false;
  }

  $app_post_id = $app_post_ids[$app_post_i];
  $app_post = $app_posts[$app_post_id];
  $app_post_i += 1;
  return true;
}

/**
 * 文章标题
 */
function app_post_title($print = true)
{
  global $app_post;

  if ($print) {
    echo htmlspecialchars($app_post['title']);
    return;
  }

  return htmlspecialchars($app_post['title']);
}

/**
 * 文章发布日期
 */
function app_post_date($print = true)
{
  global $app_post;

  if ($print) {
    echo $app_post['date'];
    return;
  }

  return $app_post['date'];
}

/**
 * 文章发布时间
 */
function app_post_time($print = true)
{
  global $app_post;

  if ($print) {
    echo $app_post['time'];
    return;
  }

  return $app_post['time'];
}

/**
 * 是否有最后修改时间（无修改或与发布时间一致时不显示）
 */
function app_mod_time_has()
{
  global $app_data;

  return isset($app_data['mod_time']) && $app_data['mod_time'] != '';
}

/**
 * 最后修改时间
 */
function app_mod_time($print = true)
{
  global $app_data;

  $mod = isset($app_data['mod_time']) ? $app_data['mod_time'] : '';

  if ($print) {
    echo $mod;
    return;
  }

  return $mod;
}

/**
 * 文章标签链接
 */
function app_post_tags($item_begin = '', $item_gap = ', ', $item_end = '', $as_link = true)
{
  global $app_post;

  $tags = isset($app_post['tags']) ? $app_post['tags'] : array();

  $count = count($tags);

  for ($i = 0; $i < $count; $i++) {
    $tag = $tags[$i];

    echo $item_begin;

    if ($as_link) {
      echo '<a href="';
      app_get_url('tag', urlencode($tag));
      echo '">';
    }

    echo htmlspecialchars($tag);

    if ($as_link) {
      echo '</a>';
    }

    echo $item_end;

    if ($i < $count - 1) {
      echo $item_gap;
    }
  }
}

/**
 * 文章内容
 */
function app_post_content($print = true)
{
  global $app_data;

  if (!isset($app_data)) {
    global $app_post_id;

    $data = unserialize(file_get_contents('data/posts/data/' . $app_post_id . '.dat'));

    $html = MarkdownExtra::defaultTransform($data['content']);
  } else {
    $html = MarkdownExtra::defaultTransform($app_data['content']);
  }

  if ($print) {
    echo htmlspecialchars_decode($html);
    return;
  }

  return $html;
}

/**
 * 文章标题+链接
 */
function app_post_link()
{
  global $app_post;

  echo '<a href="';
  app_post_url();
  echo '">';
  echo htmlspecialchars($app_post['title']);
  echo '</a>';
}

/**
 * 文章访问URL
 */
function app_post_url($print = true)
{
  global $app_post_id, $app_path_type;
  $url = app_get_url($app_path_type == 'page' ? '' : 'post', $app_post_id);

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

/**
 * 是否可评论
 */
function app_can_comment()
{
  global $app_post;

  return isset($app_post['can_comment']) ? $app_post['can_comment'] == '1' : true;
}

/**
 * 文章评论代码
 */
function app_comment_code()
{
  global $app_config;

  echo isset($app_config['comment_code']) ? $app_config['comment_code'] : '';
}

/**
 * 网页底部代码
 */
function app_footer_code()
{
  global $app_config;

  echo isset($app_config['footer_code']) ? $app_config['footer_code'] : '';
}

/**
 * 网页头部代码（</head>前）
 */
function app_header_code()
{
  global $app_config;

  echo isset($app_config['header_code']) ? $app_config['header_code'] : '';
}

/**
 * 备案号链接（未配置时输出空）
 */
function app_icp_no($print = true)
{
  global $app_config;

  $icp = '';
  if (!empty($app_config['site_icpno'])) {
    $icp = '<a href="https://beian.miit.gov.cn/#/Integrated/index" class="link" target="_blank">' . htmlspecialchars($app_config['site_icpno']) . '</a>';
  }

  if ($print) {
    echo $icp;
    return;
  }

  return $icp;
}

function app_hits_inc($path_id, $type = 'post')
{
  $type_dir = $type == 'page' ? 'pages' : 'posts';
  $hits_file = PATH_ROOT . '/data/hits/' . $type_dir . '/' . $path_id . '.dat';

  $hits = 0;
  if (file_exists($hits_file)) {
    $hits = intval(file_get_contents($hits_file));
  }
  $hits++;
  file_put_contents($hits_file, $hits);

  // 站点总浏览量同步 +1（汇总文件不存在时先聚合初始化，此时已包含本次更新）
  $site_file = app_site_hits_file();
  if (!file_exists($site_file)) {
    app_site_hits_rebuild();
  } else {
    $site_hits = intval(file_get_contents($site_file));
    file_put_contents($site_file, $site_hits + 1);
  }

  return $hits;
}

function app_hits_get($path_id, $type = 'post')
{
  $type_dir = $type == 'page' ? 'pages' : 'posts';
  $hits_file = PATH_ROOT . '/data/hits/' . $type_dir . '/' . $path_id . '.dat';

  if (!file_exists($hits_file)) {
    return 0;
  }

  return intval(file_get_contents($hits_file));
}

/**
 * 站点总浏览量文件
 */
function app_site_hits_file()
{
  return PATH_ROOT . '/data/hits/site.dat';
}

/**
 * 聚合所有单篇浏览量，生成站点总浏览量文件
 */
function app_site_hits_rebuild()
{
  $total = 0;
  $types = array('posts', 'pages');
  foreach ($types as $type) {
    $type_dir = PATH_ROOT . '/data/hits/' . $type;
    if (!is_dir($type_dir)) {
      continue;
    }
    $files = glob($type_dir . '/*.dat');
    foreach ($files as $file) {
      $total += intval(file_get_contents($file));
    }
  }

  file_put_contents(app_site_hits_file(), $total);

  return $total;
}

function app_site_hits($print = true)
{
  $site_file = app_site_hits_file();

  if (!file_exists($site_file)) {
    $total = app_site_hits_rebuild();
  } else {
    $total = intval(file_get_contents($site_file));
  }

  if ($print) {
    echo $total;
  }
  return $total;
}

function app_post_hits($print = true)
{
  global $app_post_id, $app_path_type, $app_post;

  $type = $app_path_type == 'page' ? 'page' : 'post';
  $id = $app_path_type == 'page' ? $app_post['file'] : $app_post_id;
  $hits = app_hits_get($id, $type);

  if ($print) {
    echo $hits;
  }
  return $hits;
}
