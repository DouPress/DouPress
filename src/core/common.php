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

function dp_get_url($dp_path_type, $dp_path_name = '', $path = '', $print = true)
{
  global $dp_config;
  $r = @$dp_config['site_route'] == 'path' ? '/' : '/?';
  $t = empty($dp_path_type) ? '' : $dp_path_type . '/';
  $n = empty($dp_path_name) ? '' : $dp_path_name;

  $url = $r . $t . $n;
  $url = str_replace('//', '/', $url);
  $url = rtrim($url, '/');
  $url = $dp_config['site_link'] . $url;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

