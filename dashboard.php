<?php
include 'header.php';
include 'auth_check.php';

require_once 'database.php';

// Get filter if set
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Build query based on filter
$query = "
    SELECT ideas.*, users.name as author, categories.name as category_name,
    COUNT(votes.vote_id) as vote_count,
    EXISTS(SELECT 1 FROM votes WHERE votes.idea_id = ideas.idea_id AND votes.user_id = ?) as user_voted
    FROM ideas 
    JOIN users ON ideas.user_id = users.user_id 
    JOIN categories ON ideas.category_id = categories.category_id
    LEFT JOIN votes ON ideas.idea_id = votes.idea_id
    WHERE ideas.status = 'active'
";

$params = [$_SESSION['user_id']];

if ($category_filter !== 'all') {
    $query .= " AND categories.name = ?";
    $params[] = $category_filter;
}

$query .= " GROUP BY ideas.idea_id ORDER BY ideas.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$ideas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Ideas Dashboard</h2>
    
    <div class="filters">
        <a href="dashboard.php" class="filter-btn <?php echo $category_filter == 'all' ? 'active' : ''; ?>">All Ideas</a>
        <?php foreach ($categories as $category): ?>
            <a href="dashboard.php?category=<?php echo urlencode($category['name']); ?>" class="filter-btn <?php echo $category_filter == $category['name'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div class="ideas-grid">
        <?php if (count($ideas) > 0): ?>
            <?php foreach ($ideas as $idea): ?>
                <div class="idea-card">
                    <span class="idea-category"><?php echo htmlspecialchars($idea['category_name']); ?></span>
                    <h3 class="idea-title"><?php echo htmlspecialchars($idea['title']); ?></h3>
                    <p class="idea-description"><?php echo htmlspecialchars($idea['description']); ?></p>
                    <div class="idea-meta">
                        <span class="idea-author">By <?php echo htmlspecialchars($idea['author']); ?></span>
                        <button class="vote-btn <?php echo $idea['user_voted'] ? 'voted' : ''; ?>" data-id="<?php echo $idea['idea_id']; ?>">
                            <i class="fas fa-heart"></i>
                            <span class="vote-count"><?php echo $idea['vote_count']; ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p>No ideas found in this category.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
</html>