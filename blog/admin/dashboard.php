<?php
session_start();

// 检查登录状态（session 或 cookies）
$isLoggedIn = false;
$username = '';

if (!empty($_SESSION['admin_logged_in'])) {
    $isLoggedIn = true;
    $username = $_SESSION['admin_username'] ?? '';
} elseif (!empty($_COOKIE['admin_username']) && !empty($_COOKIE['admin_token'])) {
    // 验证 cookies
    $cookieUsername = $_COOKIE['admin_username'];
    $token = $_COOKIE['admin_token'];
    $expectedToken = md5($cookieUsername . 'your_secret_key');
    
    if ($token === $expectedToken) {
        $isLoggedIn = true;
        $username = $cookieUsername;
        // 重新设置 session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
    } else {
        // cookies 无效，清除
        setcookie('admin_username', '', time() - 3600, '/');
        setcookie('admin_token', '', time() - 3600, '/');
    }
}

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

include '../inc/db.php';

// 删除文章
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .table th { border-top: none; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-tachometer-alt mr-2"></i>后台管理
        </a>
        <div class="navbar-nav ml-auto">
            <span class="navbar-text mr-3">
                <i class="fas fa-user mr-1"></i><?= htmlspecialchars($username) ?>
            </span>
            <a class="nav-link" href="new_post.php">
                <i class="fas fa-plus mr-1"></i>发布文章
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt mr-1"></i>退出
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list mr-2"></i>文章管理
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="new_post.php" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>发布新文章
                        </a>
                        <a href="../index.php" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-eye mr-1"></i>查看前台
                        </a>
                    </div>
                    
                    <?php if (empty($posts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">暂无文章</h5>
                            <p class="text-muted">点击上方按钮发布第一篇文章</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>标题</th>
                                        <th>发布时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td><span class="badge badge-secondary"><?= $post['id'] ?></span></td>
                                        <td>
                                            <a href="../post.php?id=<?= $post['id'] ?>" class="text-dark" target="_blank">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $post['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('确定删除这篇文章吗？')">
                                                <i class="fas fa-trash mr-1"></i>删除
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
