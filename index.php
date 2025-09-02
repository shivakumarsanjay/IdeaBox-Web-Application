<?php include 'header.php'; ?>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<section class="hero">
    <div class="container">
        <h1>Share Your Innovative Ideas</h1>
        <p>IdeaBox is a platform for students to submit, vote, and collaborate on innovative ideas that can transform our campus and beyond.</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="cta-button">Get Started</a>
        <?php else: ?>
            <a href="submit_idea.php" class="cta-button">Submit an Idea</a>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <h2 class="text-center">Latest Ideas</h2>
    
    <div class="filters">
        <button class="filter-btn active" data-filter="all">All Ideas</button>
        <button class="filter-btn" data-filter="Technology">Technology</button>
        <button class="filter-btn" data-filter="Social">Social</button>
        <button class="filter-btn" data-filter="Education">Education</button>
        <button class="filter-btn" data-filter="Sustainability">Sustainability</button>
        <button class="filter-btn" data-filter="Campus Life">Campus Life</button>
        <button class="filter-btn" data-filter="Other">Other</button>
    </div>
    
    <div class="ideas-grid">
        <?php
        require_once 'database.php';
        
        try {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            $stmt = $pdo->prepare("
                SELECT ideas.*, users.name as author, categories.name as category_name,
                COUNT(votes.vote_id) as vote_count,
                EXISTS(SELECT 1 FROM votes WHERE votes.idea_id = ideas.idea_id AND votes.user_id = ?) as user_voted
                FROM ideas 
                JOIN users ON ideas.user_id = users.user_id 
                JOIN categories ON ideas.category_id = categories.category_id
                LEFT JOIN votes ON ideas.idea_id = votes.idea_id
                WHERE ideas.status = 'active'
                GROUP BY ideas.idea_id
                ORDER BY ideas.created_at DESC
                LIMIT 6
            ");
            $stmt->execute([$user_id]);
            
            while ($idea = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $votedClass = $idea['user_voted'] ? 'voted' : '';
                echo "
                <div class='idea-card' data-category='" . htmlspecialchars($idea['category_name']) . "'>
                    <span class='idea-category'>" . htmlspecialchars($idea['category_name']) . "</span>
                    <h3 class='idea-title'>" . htmlspecialchars($idea['title']) . "</h3>
                    <p class='idea-description'>" . htmlspecialchars($idea['description']) . "</p>
                    <div class='idea-meta'>
                        <span class='idea-author'>By " . htmlspecialchars($idea['author']) . "</span>
                        <button class='vote-btn $votedClass' data-id='" . $idea['idea_id'] . "'>
                            <i class='fas fa-heart'></i>
                            <span class='vote-count'>" . $idea['vote_count'] . "</span>
                        </button>
                    </div>
                </div>
                ";
            }
        } catch(PDOException $e) {
            error_log("Error loading ideas: " . $e->getMessage());
            echo "<div class='alert alert-error'>Error loading ideas. Please try again later.</div>";
        }
        ?>
    </div>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <div class="text-center">
            <a href="dashboard.php" class="btn">View All Ideas</a>
        </div>
    <?php else: ?>
        <div class="text-center">
            <p>Want to see more ideas and participate in voting?</p>
            <a href="register.php" class="btn">Register Now</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>