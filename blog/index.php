<?php include 'inc/db.php'; ?>
<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center font-weight-bold text-primary">我的博客</h1>
            <p class="text-center text-muted mb-5">分享技术、记录生活</p>
            
            <?php
            $stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC');
            while ($row = $stmt->fetch()):
            ?>
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="post.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h3>
                        <p class="card-text text-muted small mb-3">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?= date('Y年m月d日 H:i', strtotime($row['created_at'])) ?>
                        </p>
                        <p class="card-text text-muted">
                            <?= mb_substr(strip_tags($row['content']), 0, 150) ?>...
                        </p>
                        <a href="post.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-right mr-1"></i>阅读全文
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <div class="text-center mt-5">
                <a href="admin/login.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-cog mr-1"></i>后台管理
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>