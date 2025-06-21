<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$user = getCurrentUser($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($full_name)) {
        $error = 'Full name is required.';
    } elseif (!empty($new_password)) {
        if ($current_password !== $user['password']) {
            $error = 'Current password is incorrect.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } else {
            // Update with new password
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, password = ? WHERE user_id = ?");
            if ($stmt->execute([$full_name, $new_password, $_SESSION['user_id']])) {
                $success = 'Profile updated successfully!';
                $user['full_name'] = $full_name;
                $user['password'] = $new_password;
            } else {
                $error = 'Error updating profile.';
            }
        }
    } else {
        // Update only full name
        $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE user_id = ?");
        if ($stmt->execute([$full_name, $_SESSION['user_id']])) {
            $success = 'Profile updated successfully!';
            $user['full_name'] = $full_name;
        } else {
            $error = 'Error updating profile.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Memory Capsule</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="hero">
            <h1 class="hero-title">Your Profile <img src="public/images/butter.jpg
                " width="80" height="80" style="border-radius: 30%;"></h1>
            <p class="hero-subtitle">Manage your account settings</p>
        </div>
        
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" class="form-input" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small style="color: var(--gray-500); font-size: 0.85rem;">Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" required 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>
                
                <hr style="border: none; border-top: 1px solid var(--gray-200); margin: 2rem 0;">
                
                <h3 style="color: var(--gray-700); margin-bottom: 1rem;">Change Password (Optional)</h3>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input">
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>