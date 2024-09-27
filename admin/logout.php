<?php
// 退出登录
setcookie("mc_token", "", time() - 3600);
Header("Location: login.php");
