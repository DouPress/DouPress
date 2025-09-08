<?php
// 退出登录
setcookie("token", "", time() - 3600);
Header("Location: login.php");
