<?php
session_start();
include '../inc/db.php';

// 检查是否已登录（session 或 cookies）
if (!empty($_SESSION['admin_logged_in']) || 
    (!empty($_COOKIE['admin_username']) && !empty($_COOKIE['admin_token']))) {
    
    // 如果有 cookies 但没有 session，验证 cookies
    if (empty($_SESSION['admin_logged_in']) && !empty($_COOKIE['admin_username'])) {
        $username = $_COOKIE['admin_username'];
        $token = $_COOKIE['admin_token'];
        
        // 验证 token（这里用简单的用户名+时间戳验证，实际建议用更安全的方式）
        $expectedToken = md5($username . 'your_secret_key');
        if ($token === $expectedToken) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header('Location: dashboard.php');
            exit;
        } else {
            // cookies 无效，清除
            setcookie('admin_username', '', time() - 3600, '/');
            setcookie('admin_token', '', time() - 3600, '/');
        }
    } else {
        // 已有 session，直接跳转
        header('Location: dashboard.php');
        exit;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    echo $password;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    var_dump($user['password']);
    if ($user && $password==$user['password']) {

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // 如果选择"记住我"，设置 cookies
        if ($remember) {
            $token = md5($username . 'your_secret_key');
            setcookie('admin_username', $username, time() + 30 * 24 * 3600, '/'); // 30天
            setcookie('admin_token', $token, time() + 30 * 24 * 3600, '/');
        }
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-card">
                <div class="login-header text-center p-4">
                    <i class="fas fa-user-shield fa-3x mb-3"></i>
                    <h3>后台管理</h3>
                    <p class="mb-0">请登录您的账户</p>
                </div>
                <div class="p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="form-group">
                            <label><i class="fas fa-user mr-2"></i>用户名</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock mr-2"></i>密码</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                <label class="custom-control-label" for="remember">
                                    <i class="fas fa-clock mr-1"></i>记住我（30天）
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt mr-2"></i>登录
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="../index.php" class="text-muted">
                            <i class="fas fa-arrow-left mr-1"></i>返回首页
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
