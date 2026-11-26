<?php

require_once PATH_ROOT . '/core/compat.php';
require_once PATH_ROOT . '/core/tags.php';
require_once PATH_ROOT . '/data/config.php';

// 调试模式：仅在配置中显式开启 debug 时回显错误，避免生产环境泄漏敏感信息
if (isset($app_config['debug']) && $app_config['debug']) {
  ini_set('display_errors', 'On');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', 'Off');
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// X-Powered-By 改为程序名，不暴露 PHP 版本号（覆盖 PHP 默认响应头）
header('X-Powered-By: DouPress');

function app_404()
{
  header('HTTP/1.0 404 Not Found');
  echo "<h1>404 Not Found</h1><p>The page that you have requested could not be found.</p>";
  exit();
}

function app_get_url($app_path_type, $app_path_name = '', $path = '', $print = true)
{
  global $app_config;
  $r = @$app_config['site_route'] == 'path' ? '/' : '/?';
  $t = empty($app_path_type) ? '' : $app_path_type . '/';
  $n = empty($app_path_name) ? '' : $app_path_name;

  $url = $r . $t . $n;
  $url = str_replace('//', '/', $url);
  $url = rtrim($url, '/');
  $url = app_site_link(false) . $url;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

