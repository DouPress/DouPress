<?php
define('PATH_ROOT', dirname(dirname(__FILE__))); // 定义根路径
require_once PATH_ROOT . '/core/common.php';

function app_check_login()
{
  if (isset($_COOKIE['token'])) {
    $token = $_COOKIE['token'];
    global $app_config;
    if ($token != md5($app_config['user_name'] . '_' . $app_config['user_pass'])) {
      Header("Location:login.php");
      exit;
    }
  } else {
    Header("Location:login.php");
    exit;
  }
}

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

    if ($a_date != $b_date) {
        return $a_date > $b_date ? -1 : 1;
    }

    return $a['time'] > $b['time'] ? -1 : 1;
}
