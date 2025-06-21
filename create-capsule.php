<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$user = getCurrentUser($pdo);
$friends = getUserFriends($pdo, $_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $open_date = $_POST['open_date'];
    $selected_friends = $_POST['friends'] ?? [];
    
    if (empty($title) || empty($open_date)) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($open_date) <= time()) {
        $error = 'Open date must be in the future.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insert capsule
            $stmt = $pdo->prepare("INSERT INTO capsules (user_id, title, open_date, is_locked) VALUES (?, ?, ?, 1)");
            $stmt->execute([$_SESSION['user_id'], $title, $open_date]);
            $capsule_id = $pdo->lastInsertId();
            
            // Insert friend relations
            if (!empty($selected_friends)) {
                $stmt = $pdo->prepare("INSERT INTO friend_capsule_relation (capsule_id, friend_id) VALUES (?, ?)");
                foreach ($selected_friends as $friend_id) {
                    $stmt->execute([$capsule_id, $friend_id]);
                }
            }
            
            $pdo->commit();
            $success = 'Memory capsule created successfully!';
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error creating capsule. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Capsule - Memory Capsule</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="hero">
            <h1 class="hero-title">Create New Memory Capsule <img src="public/images/camera.jpg
                " width="80" height="80"></h1>
            <p class="hero-subtitle">Preserve your precious moments for the future</p>
        </div>
        
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">View Dashboard</a>
                        <a href="add-content.php?capsule_id=<?php echo $capsule_id ?? ''; ?>" class="btn btn-secondary">Add Content</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title" class="form-label">Capsule Title *</label>
                    <input type="text" id="title" name="title" class="form-input" required 
                           placeholder="e.g., Summer Vacation 2024"
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="open_date" class="form-label">Open Date *</label>
                    <input type="date" id="open_date" name="open_date" class="form-input" required 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                           value="<?php echo htmlspecialchars($_POST['open_date'] ?? ''); ?>">
                    <small style="color: var(--gray-500); font-size: 0.85rem;">
                        Choose when this capsule should be unlocked
                    </small>
                </div>
                
                <?php if (!empty($friends)): ?>
                <div class="form-group">
                    <label class="form-label">Share with Friends (Optional)</label>
                    <div style="max-height: 200px; overflow-y: auto; border: 2px solid var(--gray-200); border-radius: var(--border-radius); padding: 1rem;">
                        <?php foreach ($friends as $friend): ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="friends[]" value="<?php echo $friend['friend_id']; ?>"
                                       <?php echo in_array($friend['friend_id'], $_POST['friends'] ?? []) ? 'checked' : ''; ?>>
                                <span style="color: var(--gray-700);">
                                    <?php echo htmlspecialchars($friend['first_name'] . ' ' . $friend['last_name']); ?>
                                </span>
                                <small style="color: var(--gray-500);">
                                    (<?php echo htmlspecialchars($friend['favorite_color']); ?>)
                                </small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary">Create Capsule</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>