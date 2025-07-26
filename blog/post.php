<?php
include 'inc/db.php';
include 'inc/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-5">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    <h2 class="mt-3 text-muted">文章不存在</h2>
                    <p class="text-muted mb-4">抱歉，您访问的文章不存在或已被删除。</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-2"></i>返回首页
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- 面包屑导航 -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent px-0">
                    <li class="breadcrumb-item">
                        <a href="index.php" class="text-decoration-none">
                            <i class="fas fa-home mr-1"></i>首页
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">文章详情</li>
                </ol>
            </nav>

            <!-- 文章内容 -->
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <header class="mb-4">
                        <h1 class="card-title display-4 font-weight-bold text-dark mb-3">
                            <?= htmlspecialchars($post['title']) ?>
                        </h1>
                        <div class="d-flex align-items-center text-muted mb-4">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <time datetime="<?= $post['created_at'] ?>">
                                <?= date('Y年m月d日 H:i', strtotime($post['created_at'])) ?>
                            </time>
                            <span class="mx-2">•</span>
                            <i class="far fa-eye mr-1"></i>
                            <span>阅读</span>
                        </div>
                    </header>

                    <div class="article-content mb-4">
                        <?= $post['content'] ?>
                    </div>

                    <!-- 文章底部 -->
                    <footer class="border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left mr-2"></i>返回首页
                                </a>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-tags mr-1"></i>
                                <span>博客文章</span>
                            </div>
                        </div>
                    </footer>
                </div>
            </article>

            <!-- 相关文章推荐 -->
            <div class="mt-5">
                <div class="row">
                    <?php
                    $relatedStmt = $pdo->prepare('SELECT * FROM posts WHERE id != ? ORDER BY created_at DESC LIMIT 3');
                    $relatedStmt->execute([$id]);
                    $relatedPosts = $relatedStmt->fetchAll();
                    foreach ($relatedPosts as $relatedPost):
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="post.php?id=<?= $relatedPost['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($relatedPost['title']) ?>
                                    </a>
                                </h6>
                                <p class="card-text text-muted small">
                                    <?= date('m-d', strtotime($relatedPost['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.article-content h1, .article-content h2, .article-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
</style>
<?php
}
include 'inc/footer.php';
?>