<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的博客</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: bold; }
        .card { transition: transform 0.2s; }
        .card:hover { transform: translateY(-2px); }
        .btn-outline-primary:hover { transform: translateY(-1px); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand text-primary" href="http://localhost:8001/blog/index.php">
            <i class="fas fa-blog mr-2"></i>我的博客
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:8001/blog/index.php">
                        <i class="fas fa-home mr-1"></i>首页
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:8001/blog/admin/login.php">
                        <i class="fas fa-cog mr-1"></i>后台管理
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>