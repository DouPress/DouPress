<?php

define('PATH_ROOT', dirname(__FILE__)); // 定义根路径
require_once PATH_ROOT . '/core/common.php';

$app_post_per_page = 10;
global $app_config;

// 处理 URL
function parseRoute($requestUri, $scriptName, $siteRoute)
{
  // print_r(var_export($_SERVER, true));
  // exit;
  // 移除文件名
  $subpath = str_replace('/' . basename(__FILE__), '', $scriptName);
  // 完整的访问路径
  $fullpath = str_replace('/' . basename(__FILE__), '', $requestUri);

  if ($siteRoute == 'path') {
    // 移除参数
    $fullpath = str_replace($_SERVER['QUERY_STRING'], '', $fullpath);
  }
  // 移除子路径
  $route = preg_replace('/^' . preg_quote($subpath, '/') . '/', '', $fullpath, 1);
  // 参数模式移除?
  $route = preg_replace('/^\/\?/', '/', $route, 1);
  // 移除 ?i=数字 参数
  $route = preg_replace('/\?i=\d+(&|$)/', '', $route);
  $route = rtrim($route, '?&'); // 移除末尾的 ? 或 &
  return $route;
}

// 解析当前请求的 URL
$route = parseRoute($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'], @$app_config['site_route']);
// print_r($route);exit;
// 分配路由
$app_path_type = '';
if (preg_match('|^/post/([a-z0-5]{6})$|', rtrim($route, '/'), $matches)) {
  $app_path_type = 'post';
  $app_path_name = $matches[1];
} elseif (preg_match('|^/tag/([^/]+)(/\?page=([0-9]+)){0,1}$|', rtrim($route, '/'), $matches)) {
  $app_path_type = 'tag';
  $app_path_name = isset($matches[1]) ? urldecode($matches[1]) : '';
  $app_page_no = isset($matches[3]) ? $matches[3] : 1;
} elseif (preg_match('|^/date/([0-9]{4}-[0-9]{2})(/\?page=([0-9]+)){0,1}$|', rtrim($route, '/'), $matches)) {
  $app_path_type = 'date';
  $app_path_name = urldecode($matches[1]);
  $app_page_no = isset($matches[3]) ? $matches[3] : 1;
} elseif (preg_match('|^/archive$|', rtrim($route, '/'), $matches)) {
  $app_path_type = 'archive';
} elseif (rtrim($route, '/') == '/rss') {
  $app_path_type = 'rss';
  $app_path_name = '';
  $app_page_no = isset($_GET['page']) ? $_GET['page'] : 1;
} elseif (rtrim($route, '/') == '/xml') {
  $app_path_type = 'xml';
  $app_path_name = '';
  $app_page_no = isset($_GET['page']) ? $_GET['page'] : 1;
} elseif (preg_match('|^/(([-a-zA-Z0-9/])+)$|', $route, $matches)) {
  $app_path_type = 'page';
  $app_path_name = rtrim($matches[1], '/');
} elseif (empty($route) || $route == '/' || preg_match('|^/page=([0-9]+)$|', $route, $matches)) {
  $app_path_type = 'index';
  $app_path_name = '';
  $app_page_no = isset($_GET['page']) ? $_GET['page'] : 1;
}

// 加载数据
switch ($app_path_type) {
  case 'post':
    if (empty($app_path_name)) {
      app_404();
    }
    require 'data/posts/index/publish.php';

    if (array_key_exists($app_path_name, $app_posts)) {
      $app_post_id = $app_path_name;
      $app_post = $app_posts[$app_post_id];

      $app_data = unserialize(file_get_contents('data/posts/data/' . $app_post_id . '.dat'));
    } else {
      app_404();
    }
    break;
  case 'tag':
    if (empty($app_path_name)) {
      app_404();
    }
    require 'data/posts/index/publish.php';

    $app_post_ids = array_keys($app_posts);
    $app_post_count = count($app_post_ids);

    $app_tag_posts = array();

    for ($i = 0; $i < $app_post_count; $i++) {
      $id = $app_post_ids[$i];
      $post = $app_posts[$id];
      if (in_array($app_path_name, $post['tags'])) {
        $app_tag_posts[$id] = $post;
      }
    }

    $app_posts = $app_tag_posts;

    $app_post_ids = array_keys($app_posts);
    $app_post_count = count($app_post_ids);
    break;
  case 'date':
    require 'data/posts/index/publish.php';

    $app_post_ids = array_keys($app_posts);
    $app_post_count = count($app_post_ids);

    $app_date_posts = array();

    for ($i = 0; $i < $app_post_count; $i++) {
      $id = $app_post_ids[$i];
      $post = $app_posts[$id];
      if (strpos($post['date'], $app_path_name) === 0) {
        $app_date_posts[$id] = $post;
      }
    }

    $app_posts = $app_date_posts;

    $app_post_ids = array_keys($app_posts);
    $app_post_count = count($app_post_ids);
    break;
  case 'archive':
    require 'data/posts/index/publish.php';
    $tags_array = [];
    $date_array = [];
    foreach ($app_posts as $post) {
      $date_array[] = substr($post['date'], 0, 7);
      $tags_array = array_merge($tags_array, $post['tags']);
    }

    $app_tags = array_values(array_unique($tags_array));
    $app_dates = array_values(array_unique($date_array));
    break;
  case 'page':
    require 'data/pages/index/publish.php';
    if (array_key_exists($app_path_name, $app_pages)) {
      $app_post_id = $app_path_name;
      $app_post = $app_pages[$app_post_id];
      $app_data = unserialize(file_get_contents('data/pages/data/' . $app_post['file'] . '.dat'));
    } else {
      app_404();
    }
    break;
  case 'index':
  case 'xml':
  case 'rss':
    require 'data/posts/index/publish.php';
    $app_post_ids = array_keys($app_posts);
    $app_post_count = count($app_post_ids);
    break;
  default:
    app_404();
}

// 加载视图
if (isset($app_config['site_status']) && $app_config['site_status'] == 'closed') {
  require PATH_ROOT . '/core/closed.php';
} else if (in_array($app_path_type, ['index', 'post', 'tag', 'date', 'archive', 'page'])) {
  require PATH_ROOT . '/theme/' . $app_config['site_theme'] . '/index.php';
} elseif ($app_path_type == 'xml') {
  require PATH_ROOT . '/core/xml.php';
} elseif ($app_path_type == 'rss') {
  require PATH_ROOT . '/core/rss.php';
} else {
  app_404();
}
