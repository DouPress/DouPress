<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
require_once '../data/config.php';

if (isset($_COOKIE['mc_token'])) {
  $token = $_COOKIE['mc_token'];

  if ($token != md5($mc_config['user_name'] . '_' . $mc_config['user_pass'])) {
    Header("Location:index.php");
    exit;
  }
} else {
  Header("Location:index.php");
  exit;
}

$page_file = basename($_SERVER['PHP_SELF']);

function shorturl($input)
{
  $base32 = array(
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
    'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
    'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
    'y', 'z', '0', '1', '2', '3', '4', '5'
  );

  $hex = md5('prefix' . $input . 'surfix' . time());
  $hexLen = strlen($hex);
  $subHexLen = $hexLen / 8;
  $output = array();

  for ($i = 0; $i < $subHexLen; $i++) {
    $subHex = substr($hex, $i * 8, 8);
    $int = 0x3FFFFFFF & (1 * hexdec('0x' . $subHex));
    $out = '';
    for ($j = 0; $j < 6; $j++) {
      $val = 0x0000001F & $int;
      $out .= $base32[$val];
      $int = $int >> 5;
    }
    $output[] = $out;
  }
  return $output;
}

function post_sort($a, $b)
{
  $a_date = $a['date'];
  $b_date = $b['date'];

  if ($a_date != $b_date)
    return $a_date > $b_date ? -1 : 1;

  return $a['time'] > $b['time'] ? -1 : 1;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8" />
  <title>DouPress</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="telephone=no,email=no" name="format-detection" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black" />
  <meta name="description" content="DouPress" />
  <meta name="keywords" content="DouPress" />
  <meta name="author" content="上海程江科技中心" />
  <meta name="copyright" content="程江® 程江科技 CROGRAM" />
  <link rel="icon" href="favicon.ico" />
  <link href="style.css" rel="stylesheet" />
</head>

<body>
  <div id="menu">
    <h3 id="menu_title"><a href="index.php"><?php echo htmlspecialchars($mc_config['site_name']); ?></a></h3>
    <ul>
      <li <?php echo $page_file == 'post.php' || $page_file == 'post-edit.php' ? 'class="current"' : ''; ?>><a href="post.php">文章</a></li>
      <li <?php echo $page_file == 'page.php' || $page_file == 'page-edit.php' ? 'class="current"' : ''; ?>><a href="page.php">页面</a></li>
      <li <?php echo $page_file == 'config.php' ? 'class="current"' : ''; ?>><a href="config.php">设置</a></li>
    </ul>
    <div class="clear"></div>
  </div>
  <div id="content">
    <div id="content_box">