<?php

define('PATH_ROOT', dirname(__FILE__)); // 定义根路径
require_once PATH_ROOT . '/core/common.php';

$mc_post_per_page = 10;
global $dp_config;
// 处理URL
// print_r(var_export($_SERVER, true));
// exit;
// 移除文件名
$subpath = str_replace('/' . basename(__FILE__), '', $_SERVER['SCRIPT_NAME']);
// 完整的访问路径
$fullpath = str_replace('/' . basename(__FILE__), '', $_SERVER['REQUEST_URI']);
// 移除参数
$fullpath = str_replace($_SERVER['QUERY_STRING'], '', $fullpath);

// 移除子路径
$route = preg_replace('/^' . preg_quote($subpath, '/') . '/', '', $fullpath, 1);
// 参数模式移除?
$route = preg_replace('/^\/\?/', '/', $route, 1);
// if (@$dp_config['site_route'] == 'path') {
//   // path 模式 /post/abc
// } else {
//   // 参数模式/?post/abc
//   $route = preg_replace('/^\/\?/', '/', $route, 1);
// }

// 分配路由
$mc_get_type = '';
if (preg_match('|^/post/([a-z0-5]{6})$|', rtrim($route, '/'), $matches)) {
  $mc_get_type = 'post';
  $mc_get_name = $matches[1];
} else if (preg_match('|^/tag/([^/]+)(/\?page=([0-9]+)){0,1}$|', rtrim($route, '/'), $matches)) {
  $mc_get_type = 'tag';
  $mc_get_name = isset($matches[1]) ? urldecode($matches[1]) : '';
  $mc_page_num = isset($matches[2]) ? $matches[3] : 1;
} else if (preg_match('|^/date/([0-9]{4}-[0-9]{2})(/\?page=([0-9]+)){0,1}$|', rtrim($route, '/'), $matches)) {
  $mc_get_type = 'date';
  $mc_get_name = urldecode($matches[1]);
  $mc_page_num = isset($matches[2]) ? $matches[3] : 1;
} else if (preg_match('|^/archive$|', rtrim($route, '/'), $matches)) {
  $mc_get_type = 'archive';
} else if (rtrim($route, '/') == '/rss') {
  $mc_get_type = 'rss';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
} else if (rtrim($route, '/') == '/xml') {
  $mc_get_type = 'xml';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
  // } else if (preg_match('|^(([-a-zA-Z0-5]+/)+)$|', $qs, $matches)) {
} else if (preg_match('|^/(([-a-zA-Z0-5/])+)$|', $route, $matches)) {
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
// } else {
} else if (empty($route) || $route == '/') {
  $mc_get_type = 'index';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
}
// 加载数据
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
} else if ($mc_get_type == 'index') {
  require 'data/posts/index/publish.php';
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
} else if ($mc_get_type == 'xml') {
  require 'data/posts/index/publish.php';
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
} else if ($mc_get_type == 'rss') {
  require 'data/posts/index/publish.php';
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
}
// 加载视图
if (in_array($mc_get_type, array('index', 'post', 'tag', 'date', 'archive', 'page'))) {
  require PATH_ROOT . '/theme/' . $dp_config['site_theme'] . '/index.php';
} else if ($mc_get_type == 'xml') {
  require PATH_ROOT . '/core/xml.php';
} else if ($mc_get_type == 'rss') {
  require PATH_ROOT . '/core/rss.php';
} else {
  app_404();
}
