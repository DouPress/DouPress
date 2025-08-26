<?php
// 后台登录页
require_once dirname(dirname(__FILE__)).'/data/config.php';

if (isset($_COOKIE['dp_token'])) {
  $token = $_COOKIE['dp_token'];

  if ($token == md5($dp_config['user_name'].'_'.$dp_config['user_pass'])) {
    Header("Location: index.php");
  }
}

if (isset($_POST['login'])) {
  if ($_POST['user'] == $dp_config['user_name'] 
  && $_POST['pass'] == $dp_config['user_pass']) {
    setcookie('dp_token', md5($dp_config['user_name'].'_'.$dp_config['user_pass']));
    Header("Location: index.php");
  } else {
    $display_message = '账号或密码错误';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($dp_config['site_name']); ?> - Powered by DouPress</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="telephone=no,email=no" name="format-detection" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black" />
  <meta name="description" content="DouPress" />
  <meta name="keywords" content="DouPress" />
  <meta name="author" content="上海程江科技中心" />
  <meta name="copyright" content="程江® 程江科技 CROGRAM" />
  <link rel="icon" href="../favicon.ico" />
  <style type="text/css">
    * { font-family:"Microsoft YaHei",Segoe UI,Tahoma,Arial,Verdana,sans-serif; }
    body { background:#f9f9f9; font-size:14px; }
    .link { text-decoration:none; color:#0000ff; }
    .link:hover { background:#0000ff; color:#fff; }
    .login-form {width: 360px;margin: 120px auto 0;}
    #login_title { text-align:center; margin: 0 auto; font-size:32px; color:#333; text-shadow: 0 2px 0 #FFFFFF;}
    #login_form { width:360px; margin:0 auto; margin-top:20px; border:solid 1px #00f; background:#fff; border-radius:3px 3px 3px 3px;}
    #login_form_box { padding:16px; }
    #login_form .label { font-weight:bold; padding-bottom:6px; color:#333; }
    #login_form .textbox { border:1px solid #e0e0e0; padding:6px; margin-bottom:20px; border-radius:3px 3px 3px 3px; }
    #login_form .textbox input { border:none; padding:0; font-size: 20px; width:100%; color:#333; outline: none; }
    #login_form .bottom { text-align:center; }
    #login_form .button { padding:4px 16px; font-size:14px; }
    #footer { text-align:center; margin-top: 10px; padding-top: 10px; border-top: solid 1px #e0e0e0; }
    .display-message { border-radius:3px; border-style:solid; border-width:1px; }
    .display-message { background-color: #FFFFE0; border-color: #E6DB55; padding:8px; margin: 10px 0; }
  </style>
</head>
<body>
  <form action="login.php" method="post" class="login-form">
    <div id="login_title"><?php echo htmlspecialchars($dp_config['site_name']); ?></div>
    <?php if (isset($display_message)) { ?>
      <div class="display-message"><?php echo $display_message; ?></div>
    <?php } ?>
    <div id="login_form">
      <div id="login_form_box">
        <div class="label">登录帐号</div>
        <div class="textbox"><input name="user" type="text" /></div>
        <div class="label">登录密码</div>
        <div class="textbox"><input name="pass" type="password" /></div>
        <div class="bottom"><input name="login" type="submit" value="登录" class="button" /></div>
      </div>
    </div>
    <div id="footer">Powered by <a href="http://doupress.com" class="link" target="_blank">DouPress</a></div>
  </form>
</body>
</html>
