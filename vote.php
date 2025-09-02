<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to vote.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idea_id = filter_input(INPUT_POST, 'idea_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id'];
    
    if (!$idea_id || !in_array($action, ['vote', 'unvote'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit();
    }
    
    try {
        if ($action == 'vote') {
            // Check if user already voted for this idea
            $stmt = $pdo->prepare("SELECT * FROM votes WHERE user_id = ? AND idea_id = ?");
            $stmt->execute([$user_id, $idea_id]);
            
            if ($stmt->rowCount() == 0) {
                $stmt = $pdo->prepare("INSERT INTO votes (user_id, idea_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $idea_id]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'You have already voted for this idea.']);
            }
        } elseif ($action == 'unvote') {
            $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND idea_id = ?");
            $stmt->execute([$user_id, $idea_id]);
            echo json_encode(['status' => 'success']);
        }
    } catch(PDOException $e) {
        error_log("Vote error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>