<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once 'tags.php';
require_once PATH_ROOT . '/data/config.php';

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
  $url = $app_config['site_link'] . $url;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

