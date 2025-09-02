<?php
include 'header.php';
include 'auth_check.php';

require_once 'database.php';

$error = '';
$success = '';

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'];
    
    if (empty($title) || empty($description) || empty($category_id)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO ideas (user_id, title, description, category_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $category_id]);
            
            $success = "Idea submitted successfully!";
            
            // Clear form
            $title = $description = '';
            $category_id = '';
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 800px; margin: 2rem auto;">
        <h2 class="card-title">Submit Your Idea</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title" class="form-label">Idea Title</label>
                <input type="text" id="title" name="title" class="form-input" value="<?php echo isset($title) ? $title : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo (isset($category_id) && $category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo $category['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Idea Description</label>
                <textarea id="description" name="description" class="form-textarea" required><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn">Submit Idea</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
</html>