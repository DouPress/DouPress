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

/**
 * CSRF：double-submit cookie 方案（项目无 session，用 cookie dp_csrf + 表单/URL 参数比对）
 * 必须在输出任何内容之前调用（setcookie 要求），每个后台页面加载时调用一次
 */
function app_csrf_token()
{
  if (empty($_COOKIE['dp_csrf'])) {
    $token = bin2hex(random_bytes(16));
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('dp_csrf', $token, 0, '/', '', $secure, true);
    $_COOKIE['dp_csrf'] = $token; // 当次请求内立即可用
  }

  return $_COOKIE['dp_csrf'];
}

/**
 * 校验 CSRF 令牌（POST 写操作校验 $_POST，GET 写操作校验 $_GET）
 */
function app_verify_csrf()
{
  $sent = '';
  if (isset($_POST['_dp_token'])) {
    $sent = $_POST['_dp_token'];
  } elseif (isset($_GET['_dp_token'])) {
    $sent = $_GET['_dp_token'];
  }

  if ($sent == '' || empty($_COOKIE['dp_csrf']) || !hash_equals($_COOKIE['dp_csrf'], $sent)) {
    die('非法请求（CSRF 校验失败），请刷新页面后重试。');
  }
}

/**
 * 输出 CSRF 隐藏表单字段
 */
function app_csrf_field()
{
  echo '<input type="hidden" name="_dp_token" value="' . app_csrf_token() . '" />';
}

/**
 * 密码助手（app_password_is_hashed / app_verify_password / app_hash_password）
 * 已移至 core/compat.php，兼容 PHP 5.x（无 password_* 扩展时走 crypt 实现）
 */

/**
 * 生成配置文件代码并加锁写入（var_export 防注入）
 */
function app_save_config()
{
  global $app_config;

  $code = "<?php\n\$app_config = " . var_export($app_config, true) . ";\n?>";

  return file_put_contents(PATH_ROOT . '/data/config.php', $code, LOCK_EX);
}

/**
 * 生成唯一的内容 ID：先用标题生成 shorturl 候选，全部冲突时用随机串兜底
 */
function app_random_id($data_dir, $title = '')
{
  if ($title != '') {
    foreach (shorturl($title) as $name) {
      if (!is_file($data_dir . '/' . $name . '.dat')) {
        return $name;
      }
    }
  }

  do {
    $name = substr(bin2hex(random_bytes(4)), 0, 6);
  } while (is_file($data_dir . '/' . $name . '.dat'));

  return $name;
}

/**
 * 文章 ID 白名单校验（防止路径穿越）
 */
function app_valid_post_id($id)
{
  return is_string($id) && preg_match('/^[a-z0-9]{1,8}$/', $id);
}

/**
 * 页面 path 白名单校验（字母/数字/中划线/下划线/点/斜杠，禁止 .. 穿越和多级异常）
 */
function app_valid_page_id($id)
{
  if (!is_string($id) || $id == '' || strpos($id, '..') !== false) {
    return false;
  }

  return preg_match('/^[a-z0-9._\-]+(\/[a-z0-9._\-]+)*$/i', $id) === 1;
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
