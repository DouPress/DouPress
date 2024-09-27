<?php
// 后台首页
// 跳到 post.php
require_once dirname(dirname(__FILE__)) . '/data/config.php';

if (isset($_COOKIE['mc_token'])) {
  $token = $_COOKIE['mc_token'];

  if ($token == md5($mc_config['user_name'] . '_' . $mc_config['user_pass'])) {
    Header("Location: post.php");
  }
}

if (isset($_POST['login'])) {
  if (
    $_POST['user'] == $mc_config['user_name']
    && $_POST['pass'] == $mc_config['user_pass']
  ) {
    setcookie('mc_token', md5($mc_config['user_name'] . '_' . $mc_config['user_pass']));
    Header("Location: post.php");
  }
}

Header("Location: login.php");
