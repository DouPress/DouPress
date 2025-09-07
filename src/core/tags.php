<?php
require_once dirname(__FILE__) . '/Michelf/MarkdownExtra.inc.php';

use Michelf\MarkdownExtra;

function dp_site_name($print = true)
{
  global $dp_config;

  $site_name = htmlspecialchars($dp_config['site_name']);

  if ($print) {
    echo $site_name;
    return;
  }

  return $site_name;
}

function dp_site_desc($print = true)
{
  global $dp_config;

  $site_desc = htmlspecialchars($dp_config['site_desc']);

  if ($print) {
    echo $site_desc;
    return;
  }

  return $site_desc;
}

/**
 * 网站主域名链接
 */
function dp_site_link($print = true)
{
  global $dp_config;

  $site_link = htmlentities($dp_config['site_link']);

  if ($print) {
    echo $site_link;
    return;
  }

  return $site_link;
}

function dp_nick_name($print = true)
{
  global $dp_config;

  $nick_name = htmlspecialchars($dp_config['nick_name']);

  if ($print) {
    echo $nick_name;
    return;
  }

  return $nick_name;
}

function dp_theme_url($path, $print = true)
{
  global $dp_config;
  $url = htmlentities($dp_config['site_link']) . '/theme/' . $dp_config['site_theme'] . '/' . $path;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

function dp_is_post()
{
  global $dp_path_type;

  return $dp_path_type == 'post';
}

function dp_is_page()
{
  global $dp_path_type;

  return $dp_path_type == 'page';
}

function dp_is_tag()
{
  global $dp_path_type;
  return $dp_path_type == 'tag';
}

function dp_is_date()
{
  global $dp_path_type;
  return $dp_path_type == 'date';
}

function dp_is_archive()
{
  global $dp_path_type;
  return $dp_path_type == 'archive';
}

function dp_tag_name($print = true)
{
  global $dp_path_name;

  if ($print) {
    echo htmlspecialchars($dp_path_name);
    return;
  }

  return $dp_path_name;
}

function dp_date_name($print = true)
{
  global $dp_path_name;

  if ($print) {
    echo htmlspecialchars($dp_path_name);
    return;
  }

  return $dp_path_name;
}

function dp_has_new()
{
  global $dp_page_no;

  return $dp_page_no != 1;
}

function dp_has_old()
{
  global $dp_page_no, $mc_post_count, $mc_post_per_page;

  return $dp_page_no < ($mc_post_count / $mc_post_per_page);
}

function dp_goto_old($text)
{
  global $dp_path_type, $dp_path_name, $dp_page_no, $dp_config;
  echo '<a href="';
  if ($dp_path_type == 'tag') {
    dp_get_url('tag', htmlspecialchars($dp_path_name));
  } else if ($dp_path_type == 'date') {
    dp_get_url('date', htmlspecialchars($dp_path_name));
  }
  echo '/?page=';
  echo ($dp_page_no + 1);
  echo '">';
  echo $text;
  echo '</a>';
}

function dp_goto_new($text)
{
  global $dp_path_type, $dp_path_name, $dp_page_no, $dp_config;
  echo '<a href="';
  if ($dp_path_type == 'tag') {
    dp_get_url('tag', htmlspecialchars($dp_path_name));
  } else if ($dp_path_type == 'date') {
    dp_get_url('date', htmlspecialchars($dp_path_name));
  }
  echo '/?page=';
  echo ($dp_page_no - 1);
  echo '">';
  echo $text;
  echo '</a>';
}

function dp_date_list($item_begin = '<li>', $item_gap = '', $item_end = '</li>')
{
  global $mc_dates, $dp_config;

  if (isset($mc_dates)) {
    $date_count = count($mc_dates);

    for ($i = 0; $i < $date_count; $i++) {
      $date = $mc_dates[$i];

      echo $item_begin;
      echo '<a href="';
      dp_get_url('date', $date);
      echo '">';
      echo $date;
      echo '</a>';
      echo $item_end;

      if ($i < $date_count - 1)
        echo $item_gap;
    }
  }
}

function dp_tag_list($item_begin = '<li>', $item_gap = '', $item_end = '</li>')
{
  global $mc_tags, $dp_config;

  if (isset($mc_tags)) {
    $tag_count = count($mc_tags);

    for ($i = 0; $i < $tag_count; $i++) {
      $tag = $mc_tags[$i];

      echo $item_begin;
      echo '<a href="';
      dp_get_url('tag', urlencode($tag));
      echo '">';
      echo htmlspecialchars($tag);
      echo '</a>';
      echo $item_end;

      if ($i < $tag_count - 1)
        echo $item_gap;
    }
  }
}

function dp_next_post()
{
  global $mc_posts, $mc_post_ids, $mc_post_count, $mc_post_i, $mc_post_i_end, $mc_post_id, $mc_post, $dp_page_no, $mc_post_per_page;

  if (!isset($mc_posts)) {
    return false;
  }

  if (!isset($mc_post_i)) {
    $mc_post_i = 0 + ($dp_page_no - 1) * $mc_post_per_page;
    $mc_post_i_end = $mc_post_i + $mc_post_per_page;
    if ($mc_post_count < $mc_post_i_end)
      $mc_post_i_end = $mc_post_count;
  }

  if ($mc_post_i == $mc_post_i_end) {
    return false;
  }

  if (!isset($mc_post_ids[$mc_post_i])) {
    return false;
  }

  $mc_post_id = $mc_post_ids[$mc_post_i];
  $mc_post = $mc_posts[$mc_post_id];
  $mc_post_i += 1;
  return true;
}

/**
 * 文章标题
 */
function dp_post_title($print = true)
{
  global $mc_post;

  if ($print) {
    echo htmlspecialchars($mc_post['title']);
    return;
  }

  return htmlspecialchars($mc_post['title']);
}

/**
 * 文章发布日期
 */
function dp_post_date($print = true)
{
  global $mc_post;

  if ($print) {
    echo $mc_post['date'];
    return;
  }

  return $mc_post['date'];
}

/**
 * 文章发布时间
 */
function dp_post_time($print = true)
{
  global $mc_post;

  if ($print) {
    echo $mc_post['time'];
    return;
  }

  return $mc_post['time'];
}

/**
 * 文章标签链接
 */
function dp_post_tags($item_begin = '', $item_gap = ', ', $item_end = '', $as_link = true)
{
  global $mc_post, $dp_config;

  $tags = $mc_post['tags'];

  $count = count($tags);

  for ($i = 0; $i < $count; $i++) {
    $tag = $tags[$i];

    echo $item_begin;

    if ($as_link) {
      echo '<a href="';
      dp_get_url('tag', urlencode($tag));
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
function dp_post_content($print = true)
{
  global $mc_data;

  if (!isset($mc_data)) {
    global $mc_post_id;

    $data = unserialize(file_get_contents('data/posts/data/' . $mc_post_id . '.dat'));

    $html = MarkdownExtra::defaultTransform($data['content']);
  } else {
    $html = MarkdownExtra::defaultTransform($mc_data['content']);
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
function dp_post_link()
{
  global $mc_post;

  echo '<a href="';
  dp_post_url();
  echo '">';
  echo htmlspecialchars($mc_post['title']);
  echo '</a>';
}

/**
 * 文章访问URL
 */
function dp_post_url($print = true)
{
  global $mc_post_id, $mc_post, $dp_config;
  $url = dp_get_url('post', $mc_post_id);

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

/**
 * 是否可评论
 */
function dp_can_comment()
{
  global $mc_post_id, $mc_post;

  return isset($mc_post['can_comment']) ? $mc_post['can_comment'] == '1' : true;
}

/**
 * 文章评论代码
 */
function dp_comment_code()
{
  global $dp_config;

  echo isset($dp_config['comment_code']) ? $dp_config['comment_code'] : '';
}

/**
 * 网页底部代码
 */
function dp_footer_code()
{
  global $dp_config;

  echo isset($dp_config['footer_code']) ? $dp_config['footer_code'] : '';
}
