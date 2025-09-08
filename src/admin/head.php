<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
require_once '../data/config.php';

$pagefile = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8" />
  <title>DouPress</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="telephone=no,email=no" name="format-detection" />
  <meta name="generator" content="DouPress"/>
  <link rel="icon" href="../favicon.ico" />
  <link href="style.css" rel="stylesheet" />
</head>

<body>
  <div id="menu">
    <h3 id="menu_title">
      <a href="index.php" class="link"><?php echo htmlspecialchars($app_config['site_name']); ?></a>
    </h3>
    <ul>
      <li <?php echo $pagefile == 'post.php' || $pagefile == 'post-edit.php' ? 'class="current"' : ''; ?>><a href="post.php">文章</a></li>
      <li <?php echo $pagefile == 'page.php' || $pagefile == 'page-edit.php' ? 'class="current"' : ''; ?>><a href="page.php">页面</a></li>
      <li <?php echo $pagefile == 'settings.php' ? 'class="current"' : ''; ?>><a href="settings.php">设置</a></li>
      <li><a href="logout.php">登出</a></li>
    </ul>
    <div class="clear"></div>
  </div>
  <div id="content">
    <div id="content_box">