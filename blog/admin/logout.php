<?php
session_start();

// 清除 session
session_destroy();

// 清除 cookies
setcookie('admin_username', '', time() - 3600, '/');
setcookie('admin_token', '', time() - 3600, '/');

// 跳转到登录页
header('Location: login.php');
exit; 