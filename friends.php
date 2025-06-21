<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$user = getCurrentUser($pdo);
$friends = getUserFriends($pdo, $_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $favorite_color = trim($_POST['favorite_color']);
    $birthdate = $_POST['birthdate'];
    $friendship_start_date = $_POST['friendship_start_date'];
    $meeting_description = trim($_POST['meeting_description']);
    
    if (empty($first_name) || empty($last_name) || empty($favorite_color) || 
        empty($birthdate) || empty($friendship_start_date) || empty($meeting_description)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO friends (user_id, first_name, last_name, favorite_color, birthdate, friendship_start_date, meeting_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$_SESSION['user_id'], $first_name, $last_name, $favorite_color, $birthdate, $friendship_start_date, $meeting_description])) {
            $success = 'Friend added successfully!';
            $friends = getUserFriends($pdo, $_SESSION['user_id']); // Refresh list
        } else {
            $error = 'Error adding friend. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends - Memory Capsule</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="hero">
            <h1 class="hero-title">Your Friends <img src="public/images/friends.jpg
                " width="80" height="80" style="border-radius: 50%;"></h1>
            <p class="hero-subtitle">Manage your friends to share memory capsules with them</p>
        </div>
        
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2 class="capsule-title">Add New Friend</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="favorite_color" class="form-label">Favorite Color *</label>
                    <input type="text" id="favorite_color" name="favorite_color" class="form-input" required 
                           placeholder="e.g., Sky Blue, Rose Pink"
                           value="<?php echo htmlspecialchars($_POST['favorite_color'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="birthdate" class="form-label">Birthdate *</label>
                    <input type="date" id="birthdate" name="birthdate" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="friendship_start_date" class="form-label">Friendship Start Date *</label>
                    <input type="date" id="friendship_start_date" name="friendship_start_date" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['friendship_start_date'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="meeting_description" class="form-label">How You Met *</label>
                    <textarea id="meeting_description" name="meeting_description" class="form-input form-textarea" required 
                              placeholder="Tell the story of how you first met..."><?php echo htmlspecialchars($_POST['meeting_description'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Friend</button>
            </form>
        </div>
        
        <?php if (!empty($friends)): ?>
            <section class="friends-grid">
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-card">
                        <h3 class="friend-name"><?php echo htmlspecialchars($friend['first_name'] . ' ' . $friend['last_name']); ?></h3>
                        <div class="friend-color"><?php echo htmlspecialchars($friend['favorite_color']); ?></div>
                        <div class="friend-info">
                            <p><strong>Birthday:</strong> <?php echo formatDate($friend['birthdate']); ?></p>
                            <p><strong>Friends since:</strong> <?php echo formatDate($friend['friendship_start_date']); ?></p>
                            <p><strong>How we met:</strong> <?php echo htmlspecialchars($friend['meeting_description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <section class="card text-center mt-8">
                <h3 class="capsule-title">No Friends Added Yet</h3>
                <p class="capsule-date">Add your first friend to start sharing memory capsules!</p>
            </section>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>