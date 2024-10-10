<?php

define('PATH_ROOT', dirname(__FILE__)); // 定义根路径
require_once PATH_ROOT . '/core/common.php';

$mc_post_per_page = 10;
global $dp_config;
if (@$dp_config['site_route'] == 'path') {
  // print_r(var_export($_SERVER, true));
  // path 模式 /post/abc
  $qs = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  $qs = ltrim($qs, '/');
} else {
  // 参数模式/?post/abc
  $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
  // 移除之后一个/
  // $qs = rtrim($qs, '/');
}

if (preg_match('|^post/([a-z0-5]{6})$|', rtrim($qs, '/'), $matches)) {
  $mc_get_type = 'post';
  $mc_get_name = $matches[1];
} else if (preg_match('|^tag/([^/]+)(/\?page=([0-9]+)){0,1}$|', rtrim($qs, '/'), $matches)) {
  $mc_get_type = 'tag';
  $mc_get_name = isset($matches[1]) ? urldecode($matches[1]) : '';
  $mc_page_num = isset($matches[2]) ? $matches[3] : 1;
} else if (preg_match('|^date/([0-9]{4}-[0-9]{2})(/\?page=([0-9]+)){0,1}$|', rtrim($qs, '/'), $matches)) {
  $mc_get_type = 'date';
  $mc_get_name = urldecode($matches[1]);
  $mc_page_num = isset($matches[2]) ? $matches[3] : 1;
} else if (preg_match('|^archive$|', rtrim($qs, '/'), $matches)) {
  $mc_get_type = 'archive';
} else if (rtrim($qs, '/') == 'rss') {
  $mc_get_type = 'rss';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
} else if (rtrim($qs, '/') == 'xml') {
  $mc_get_type = 'xml';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
  // } else if (preg_match('|^(([-a-zA-Z0-5]+/)+)$|', $qs, $matches)) {
} else if (preg_match('|^(([-a-zA-Z0-5/])+)$|', $qs, $matches)) {
  // } else if (preg_match('|^([^/]+)$|', $qs, $matches)) {
  $mc_get_type = 'page';
  $mc_get_name = rtrim($matches[1], '/');
  // $mc_get_name = substr($matches[1], 0, -1);
  // echo $mc_get_name;
  // } else if ('admin' == $qs || preg_match('|^admin/([^/]+)$|', $qs, $matches)) {
  //   $mc_get_type = 'admin';
  //   $mc_get_name = isset($matches[1]) ? $matches[1] : 'index';
  //   // $mc_get_action = isset($matches[2]) ? $matches[2] : '';
  //   // echo $mc_get_name;exit;
} else {
  $mc_get_type = 'index';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
}

if ($mc_get_type == 'post') {
  if (empty($mc_get_name)) {
    app_404();
  }
  require 'data/posts/index/publish.php';

  if (array_key_exists($mc_get_name, $mc_posts)) {
    $mc_post_id = $mc_get_name;
    $mc_post = $mc_posts[$mc_post_id];

    $mc_data = unserialize(file_get_contents('data/posts/data/' . $mc_post_id . '.dat'));
  } else {
    app_404();
  }
} else if ($mc_get_type == 'tag') {
  if (empty($mc_get_name)) {
    app_404();
  }
  require 'data/posts/index/publish.php';

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);

  $mc_tag_posts = array();

  for ($i = 0; $i < $mc_post_count; $i++) {
    $id = $mc_post_ids[$i];
    $post = $mc_posts[$id];
    if (in_array($mc_get_name, $post['tags'])) {
      $mc_tag_posts[$id] = $post;
    }
  }

  $mc_posts = $mc_tag_posts;

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
} else if ($mc_get_type == 'date') {
  require 'data/posts/index/publish.php';

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);

  $mc_date_posts = array();

  for ($i = 0; $i < $mc_post_count; $i++) {
    $id = $mc_post_ids[$i];
    $post = $mc_posts[$id];
    if (strpos($post['date'], $mc_get_name) === 0) {
      $mc_date_posts[$id] = $post;
    }
  }

  $mc_posts = $mc_date_posts;

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
} else if ($mc_get_type == 'archive') {
  require 'data/posts/index/publish.php';

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);

  $tags_array = array();
  $date_array = array();

  for ($i = 0; $i < $mc_post_count; $i++) {
    $post_id = $mc_post_ids[$i];
    $post = $mc_posts[$post_id];
    $date_array[] = substr($post['date'], 0, 7);
    $tags_array = array_merge($tags_array, $post['tags']);
  }

  $mc_tags  = array_values(array_unique($tags_array));
  $mc_dates = array_values(array_unique($date_array));
} else if ($mc_get_type == 'page') {
  require 'data/pages/index/publish.php';
  // print_r($mc_pages);exit;
  if (array_key_exists($mc_get_name, $mc_pages)) {
    $mc_post_id = $mc_get_name;
    $mc_post = $mc_pages[$mc_post_id];

    $mc_data = unserialize(file_get_contents('data/pages/data/' . $mc_post['file'] . '.dat'));
  } else {
    app_404();
  }
  // } else if ($mc_get_type == 'admin') {
  //   // require 'data/pages/index/publish.php';
  //   if (file_exists(PATH_ROOT . '/core/admin/' . $mc_get_name . '.php')) {
  //     require PATH_ROOT . '/core/admin/' . $mc_get_name . '.php';
  //     exit();
  //   } else {
  //     app_404();
  //   }
  // if (array_key_exists($mc_get_name, $mc_pages)) {
  //   $mc_post_id = $mc_get_name;
  //   $mc_post = $mc_pages[$mc_post_id];

  //   $mc_data = unserialize(file_get_contents('data/pages/data/' . $mc_post['file'] . '.dat'));
  // } else {
  //   app_404();
  // }
} else {
  require 'data/posts/index/publish.php';

  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
}

if ($mc_get_type == 'rss') {
  require PATH_ROOT . '/core/rss.php';
} else if ($mc_get_type == 'xml') {
  require PATH_ROOT . '/core/xml.php';
} else {
  require PATH_ROOT . '/theme/' . $dp_config['site_theme'] . '/index.php';
}
