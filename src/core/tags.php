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

function app_tag_name($print = true)
{
  global $app_path_name;

  if ($print) {
    echo htmlspecialchars($app_path_name);
    return;
  }

  return $app_path_name;
}

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

  return $app_page_no < ($app_post_count / $app_post_per_page);
}

function app_goto_old($text)
{
  global $app_path_type, $app_path_name, $app_page_no, $app_config;
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
  global $app_path_type, $app_path_name, $app_page_no, $app_config;
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
  global $app_dates, $app_config;

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
  global $app_tags, $app_config;

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
 * 文章标签链接
 */
function app_post_tags($item_begin = '', $item_gap = ', ', $item_end = '', $as_link = true)
{
  global $app_post, $app_config;

  $tags = $app_post['tags'];

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
  global $app_post_id, $app_post, $app_config;
  $url = app_get_url('post', $app_post_id);

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
  global $app_post_id, $app_post;

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
