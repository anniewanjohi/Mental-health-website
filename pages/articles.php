<?php 
require_once '../includes/header.php'; 
require_once '../Backend/database.php'; 

session_start(); 

$db = Database::getInstance(); 
$conn = $db->getConnection(); 


$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';


$sql = "SELECT a.*, u.fullname as author_name 
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.user_id
        WHERE a.status = 'published'";

$params = [];

if ($category !== 'all') {
    $sql .= " AND a.category = :category";
    $params[':category'] = $category;
}

if (!empty($search)) {
    $sql .= " AND (a.title LIKE :search OR a.content LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY a.featured DESC, a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);


$countStmt = $conn->query("
    SELECT category, COUNT(*) as count 
    FROM articles 
    WHERE status = 'published' 
    GROUP BY category
");
$categoryCounts = $countStmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Articles - Resources & Insights</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Page Header -->
<div class="bg-primary bg-gradient text-white py-5 mb-5">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-3">📚 Mental Health Articles</h1>
        <p class="lead fs-4">Expert insights, practical tips, and resources for your mental wellness journey</p>
    </div>
</div>

<div class="container mb-5 pb-5">
    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <form method="GET" action="">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search articles..." value="<?= htmlspecialchars($search) ?>">
                    <?php if ($category !== 'all'): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <?php endif; ?>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="?category=all" class="btn <?= $category === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-grid-3x3 me-2"></i>All Articles
                    <span class="badge bg-white text-primary ms-2"><?= array_sum($categoryCounts) ?></span>
                </a>
                <a href="?category=anxiety" class="btn <?= $category === 'anxiety' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-emoji-frown me-2"></i>Anxiety
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['anxiety'] ?? 0 ?></span>
                </a>
                <a href="?category=depression" class="btn <?= $category === 'depression' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-cloud-drizzle me-2"></i>Depression
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['depression'] ?? 0 ?></span>
                </a>
                <a href="?category=stress" class="btn <?= $category === 'stress' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-lightning me-2"></i>Stress
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['stress'] ?? 0 ?></span>
                </a>
                <a href="?category=relationships" class="btn <?= $category === 'relationships' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-people me-2"></i>Relationships
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['relationships'] ?? 0 ?></span>
                </a>
                <a href="?category=self-care" class="btn <?= $category === 'self-care' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-heart me-2"></i>Self-Care
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['self-care'] ?? 0 ?></span>
                </a>
                <a href="?category=general" class="btn <?= $category === 'general' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill">
                    <i class="bi bi-book me-2"></i>General
                    <span class="badge bg-white text-primary ms-2"><?= $categoryCounts['general'] ?? 0 ?></span>
                </a>
            </div>
        </div>
    </div>

    <?php if (count($articles) > 0): ?>
        <!-- Results Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Showing <strong><?= count($articles) ?></strong> article<?= count($articles) !== 1 ? 's' : '' ?>
                    <?php if ($category !== 'all'): ?>
                        in <strong class="text-capitalize"><?= htmlspecialchars($category) ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($search)): ?>
                        matching "<strong><?= htmlspecialchars($search) ?></strong>"
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 <?= $article['featured'] ? 'border border-3 border-warning' : '' ?>">
                        <?php if ($article['featured']): ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark fs-6 rounded-pill shadow">
                                <i class="bi bi-star-fill me-1"></i>FEATURED
                            </span>
                        <?php endif; ?>
                        
                        <div class="bg-primary bg-gradient text-white d-flex align-items-center justify-content-center" style="height: 200px; font-size: 5rem;">
                            <?php
                            $icons = [
                                'anxiety' => '😰',
                                'depression' => '😔',
                                'stress' => '😓',
                                'relationships' => '💑',
                                'self-care' => '💆',
                                'general' => '📖'
                            ];
                            echo $icons[$article['category']] ?? '📚';
                            ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-info text-dark mb-3 align-self-start text-uppercase">
                                <?= htmlspecialchars($article['category']) ?>
                            </span>
                            <h5 class="card-title fw-bold text-primary mb-3">
                                <?= htmlspecialchars($article['title']) ?>
                            </h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(substr($article['content'], 0, 150)) ?>...
                            </p>
                            
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <?= htmlspecialchars($article['author_name'] ?? 'Admin') ?>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span>
                                            <i class="bi bi-clock me-1"></i>
                                            <?= $article['reading_time'] ?> min
                                        </span>
                                        <span>
                                            <i class="bi bi-eye me-1"></i>
                                            <?= number_format($article['views']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="view_article.php?id=<?= $article['article_id'] ?>" class="btn btn-outline-primary mt-3 stretched-link">
                                Read More <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="display-1 mb-4">🔍</div>
                        <h2 class="text-primary mb-3">No Articles Found</h2>
                        <p class="lead text-muted mb-4">We couldn't find any articles matching your criteria.</p>
                        <?php if ($category !== 'all' || !empty($search)): ?>
                            <a href="articles.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-x-circle me-2"></i>Clear Filters & View All
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../Layout/footer.php'; ?>
</body>
</html>
