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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title && $content) {
        $stmt = $pdo->prepare('INSERT INTO posts (title, content) VALUES (?, ?)');
        $stmt->execute([$title, $content]);
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '标题和内容不能为空';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布新文章</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
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
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-list mr-1"></i>文章管理
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt mr-1"></i>退出
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit mr-2"></i>发布新文章
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" id="postForm">
                        <div class="form-group">
                            <label><i class="fas fa-heading mr-2"></i>文章标题</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <!-- 表情包按钮和弹窗 -->
                        <button type="button" class="btn btn-warning mb-2" id="emojiBtn">
                            <i class="far fa-smile mr-1"></i>表情包
                        </button>
                        <div id="emojiModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:9999; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                            <div id="emojiList" style="display:flex; flex-wrap:wrap; gap:10px; max-height:300px; overflow-y:auto;"></div>
                            <button type="button" class="btn btn-secondary mt-2" onclick="document.getElementById('emojiModal').style.display='none'">
                                <i class="fas fa-times mr-1"></i>关闭
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-file-alt mr-2"></i>文章内容</label>
                            <textarea name="content" class="form-control" rows="15"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>返回
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i>发布文章
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
let editorInstance;
ClassicEditor
    .create(document.querySelector('textarea[name="content"]'))
    .then(editor => {
        editorInstance = editor;
        // 表单提交时同步内容并校验不能为空
        document.getElementById('postForm').addEventListener('submit', function(e) {
            const content = editorInstance.getData();
            if (!content.trim()) {
                alert('内容不能为空');
                e.preventDefault();
                return false;
            }
            document.querySelector('textarea[name="content"]').value = content;
        });
    })
    .catch(error => {
        console.error(error);
    });

// 自动生成表情包图片列表（由 PHP 输出）
const emojiImages = [
<?php
$imgDir = '../img';
$imgFiles = array_merge(
    glob($imgDir.'/*.png'),
    glob($imgDir.'/*.gif'),
    glob($imgDir.'/*.jpg'),
    glob($imgDir.'/*.jpeg')
);
foreach ($imgFiles as $file) {
    $basename = basename($file);
    echo "    '../img/{$basename}',\n";
}
?>
];

// 动态生成表情包选择区
const emojiList = document.getElementById('emojiList');
emojiImages.forEach(src => {
    const img = document.createElement('img');
    img.src = src;
    img.style.width = '40px';
    img.style.cursor = 'pointer';
    img.style.borderRadius = '4px';
    img.onclick = function() {
        if(editorInstance) {
            editorInstance.execute('insertImage', { source: [ img.src ] });
        }
        document.getElementById('emojiModal').style.display = 'none';
    };
    emojiList.appendChild(img);
});

// 按钮事件
const emojiBtn = document.getElementById('emojiBtn');
if (emojiBtn) {
    emojiBtn.onclick = function() {
        document.getElementById('emojiModal').style.display = 'block';
    };
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
