<?php
// 后台登录页
require_once __DIR__ . '/common.php';

// 已登录则直接跳转
if (isset($_COOKIE['token'])
  && $_COOKIE['token'] == md5($app_config['user_name'] . '_' . $app_config['user_pass'])) {
  Header("Location: index.php");
  exit;
}

// 登录失败限流：10 分钟窗口内最多失败 10 次
$fail_file = PATH_ROOT . '/data/.login-fail.dat';
$fail_data = array('count' => 0, 'first' => 0);
if (is_file($fail_file)) {
  $fail_data = unserialize(file_get_contents($fail_file));
  if (!is_array($fail_data) || !isset($fail_data['count'], $fail_data['first'])) {
    $fail_data = array('count' => 0, 'first' => 0);
  }
  // 超过 10 分钟窗口自动重置
  if (time() - $fail_data['first'] > 600) {
    $fail_data = array('count' => 0, 'first' => time());
  }
}

if ($fail_data['count'] >= 10 && time() - $fail_data['first'] <= 600) {
  $display_message = '登录失败次数过多，请 10 分钟后再试';
} elseif (isset($_POST['login'])) {
  if ($_POST['user'] == $app_config['user_name']
    && app_verify_password($_POST['pass'], $app_config['user_pass'])) {
    // 旧明文密码透明升级为哈希存储（token 仍基于配置值计算，保持兼容）
    if (!app_password_is_hashed($app_config['user_pass'])) {
      $app_config['user_pass'] = app_hash_password($app_config['user_pass']);
      app_save_config();
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('token', md5($app_config['user_name'] . '_' . $app_config['user_pass']), 0, '/', '', $secure, true);
    @unlink($fail_file);
    Header("Location: index.php");
    exit;
  } else {
    $fail_data['count']++;
    @file_put_contents($fail_file, serialize($fail_data), LOCK_EX);
    $display_message = '账号或密码错误';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($app_config['site_name']); ?> - Powered by DouPress</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="generator" content="DouPress" />
  <meta name="author" content="DouPress" />
  <link rel="icon" href="../favicon.ico" />
  <style type="text/css">
    * { font-family:"Microsoft YaHei",Segoe UI,Tahoma,Arial,Verdana,sans-serif; }
    body { background:#f9f9f9; font-size:14px; }
    .link { text-decoration:none; color:#0000ff; }
    .link:hover { background:#0000ff; color:#fff; }
    .login-form {width: 360px;margin: 120px auto 0;}
    #login_title { text-align:center; margin: 0 auto; font-size:32px; color:#333; text-shadow: 0 2px 0 #FFFFFF;}
    #login_form { width:360px; margin:0 auto; margin-top:20px; border:solid 1px #ccc; background:#fff; border-radius:3px 3px 3px 3px;}
    #login_form_box { padding:16px; }
    #login_form .label { font-weight:bold; padding-bottom:6px; color:#333; }
    #login_form .textbox { border:1px solid #e0e0e0; padding:6px; margin-bottom:20px; border-radius:3px 3px 3px 3px; }
    #login_form .textbox input { border:none; padding:0; font-size: 20px; width:100%; color:#333; outline: none; }
    #login_form .bottom { text-align:center; }
    #login_form .button { padding:4px 16px; font-size:14px; }
    #footer { text-align:center; margin-top: 10px; color:#888; margin: 10px auto 0; font-size:11px; }
    .display-message { border-radius:3px; border-style:solid; border-width:1px; }
    .display-message { background-color: #FFFFE0; border-color: #E6DB55; padding:8px; margin: 10px 0; }
  </style>
</head>
<body>
  <div class="login-form">
    <div id="login_title"><?php echo htmlspecialchars($app_config['site_name']); ?></div>
    <?php if (isset($display_message)) { ?>
      <div class="display-message"><?php echo $display_message; ?></div>
    <?php } ?>
    <div id="login_form">
      <form action="login.php" method="post" id="login_form_box">
        <div class="label">登录帐号</div>
        <div class="textbox"><input name="user" type="text" /></div>
        <div class="label">登录密码</div>
        <div class="textbox"><input name="pass" type="password" /></div>
        <div class="bottom"><input name="login" type="submit" value="登录" class="button" /></div>
      </form>
    </div>
    <div id="footer">Powered by <a href="http://doupress.org" class="link" target="_blank">DouPress</a></div>
  </div>
</body>
</html>
