<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once 'tags.php';
require_once PATH_ROOT . '/data/config.php';

function app_404()
{
  header('HTTP/1.0 404 Not Found');
  echo "<h1>404 Not Found</h1>";
  echo "The page that you have requested could not be found.";
  exit();
}

function mc_get_url($mc_get_type, $mc_get_name, $path = '', $print = true)
{
  global $mc_config;

  $url = $mc_config['site_link'] . '/?' . $mc_get_type . '/' . $mc_get_name;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}
