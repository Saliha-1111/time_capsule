<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$user = null;
if (isLoggedIn()) {
    $user = getCurrentUser($pdo);
    $capsules = getUserCapsules($pdo, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Capsule - Preserve Your Precious Moments</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <?php if (!isLoggedIn()): ?>
            <!-- Hero Section for Non-logged Users -->
            <section class="hero">
                <h1 class="hero-title">Memory Capsule</h1>
                <p class="hero-subtitle">Preserve your precious moments and unlock them when the time is right</p>
                <div class="flex gap-4 justify-center mt-6">
                    <a href="login.php" class="btn btn-primary">Get Started</a>
                    <a href="register.php" class="btn btn-secondary">Create Account</a>
                </div>
            </section>
            
            <!-- Features Section -->
            <section class="card-grid">
                <div class="card">
                    <h3 class="capsule-title">🎭 Create Memories</h3>
                    <p class="capsule-date">Store photos, videos, and notes in beautiful time capsules</p>
                </div>
                <div class="card">
                    <h3 class="capsule-title">👥 Share with Friends</h3>
                    <p class="capsule-date">Include your friends in your memory capsules</p>
                </div>
                <div class="card">
                    <h3 class="capsule-title">⏰ Time-locked</h3>
                    <p class="capsule-date">Set future dates to unlock your precious memories</p>
                </div>
            </section>
        <?php else: ?>
            <!-- Dashboard for Logged Users -->
            <section class="hero">
                <h1 class="hero-title">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! <img src="public/images/dp.jpg
                " width="80" height="80">
</h1>
                <p class="hero-subtitle">Your memory capsules are waiting to be discovered</p>
                <div class="flex gap-4 justify-center mt-6">
                    <a href="create-capsule.php" class="btn btn-primary">Create New Capsule</a>
                    <a href="friends.php" class="btn btn-secondary">Manage Friends</a>
                </div>
            </section>
            
            <!-- Capsules Grid -->
            <?php if (!empty($capsules)): ?>
                <section class="card-grid">
                    <?php foreach ($capsules as $capsule): ?>
                        <div class="card capsule-card">
                            <div class="capsule-status <?php echo isCapsuleUnlocked($capsule['open_date']) ? 'status-unlocked' : 'status-locked'; ?>">
                                <?php echo isCapsuleUnlocked($capsule['open_date']) ? '🔓 Unlocked' : '🔒 Locked'; ?>
                            </div>
                            
                            <h3 class="capsule-title"><?php echo htmlspecialchars($capsule['title']); ?></h3>
                            <p class="capsule-date">Opens on <?php echo formatDate($capsule['open_date']); ?></p>
                            
                            <?php if (isCapsuleUnlocked($capsule['open_date'])): ?>
                                <a href="view-capsule.php?id=<?php echo $capsule['capsule_id']; ?>" class="btn btn-primary w-full mt-4">
                                    Open Capsule ✨
                                </a>
                            <?php else: ?>
                                <div class="btn btn-secondary w-full mt-4" style="cursor: not-allowed; opacity: 0.6;">
                                    Locked until <?php echo formatDate($capsule['open_date']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="capsule-meta">
                                <div class="capsule-friends">
                                    <?php if ($capsule['friends']): ?>
                                       <img src="public/images/friends.jpg
                " width="80" height="80"><?php echo htmlspecialchars($capsule['friends']); ?>
                                    <?php else: ?>
                                        <img src="public/images/logo.jpg
                " width="80" height="80"> Personal capsule
                                    <?php endif; ?>
                                </div>
                                <div class="content-count">
                                    <?php echo $capsule['content_count']; ?> items
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <section class="card text-center">
                    <h3 class="capsule-title">No Memory Capsules Yet</h3>
                    <p class="capsule-date mb-6">Create your first memory capsule to get started!</p>
                    <a href="create-capsule.php" class="btn btn-primary">Create Your First Capsule</a>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>