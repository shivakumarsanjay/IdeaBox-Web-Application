<?php
include 'header.php';
include 'auth_check.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'database.php';

// Handle idea status update
if (isset($_POST['update_status'])) {
    $idea_id = $_POST['idea_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE ideas SET status = ? WHERE idea_id = ?");
    $stmt->execute([$status, $idea_id]);
}

// Handle idea deletion
if (isset($_POST['delete_idea'])) {
    $idea_id = $_POST['idea_id'];
    
    $stmt = $pdo->prepare("DELETE FROM ideas WHERE idea_id = ?");
    $stmt->execute([$idea_id]);
}

// Get all ideas with user information
$stmt = $pdo->query("
    SELECT ideas.*, users.name as author, categories.name as category_name,
    COUNT(votes.vote_id) as vote_count
    FROM ideas 
    JOIN users ON ideas.user_id = users.user_id 
    JOIN categories ON ideas.category_id = categories.category_id
    LEFT JOIN votes ON ideas.idea_id = votes.idea_id
    GROUP BY ideas.idea_id
    ORDER BY ideas.created_at DESC
");
$ideas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Admin Panel</h2>
    
    <div class="card">
        <h3 class="card-title">Manage Ideas</h3>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Votes</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($ideas) > 0): ?>
                    <?php foreach ($ideas as $idea): ?>
                        <tr>
                            <td><?php echo $idea['title']; ?></td>
                            <td><?php echo $idea['author']; ?></td>
                            <td><?php echo $idea['category_name']; ?></td>
                            <td><?php echo $idea['vote_count']; ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="idea_id" value="<?php echo $idea['idea_id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="active" <?php echo $idea['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="hidden" <?php echo $idea['status'] == 'hidden' ? 'selected' : ''; ?>>Hidden</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($idea['created_at'])); ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this idea?');">
                                    <input type="hidden" name="idea_id" value="<?php echo $idea['idea_id']; ?>">
                                    <button type="submit" name="delete_idea" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No ideas found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
</html>