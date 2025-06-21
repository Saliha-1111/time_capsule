<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$capsule_id = $_GET['id'] ?? 0;
$user = getCurrentUser($pdo);

// Get capsule details
$stmt = $pdo->prepare("
    SELECT c.*, 
           GROUP_CONCAT(DISTINCT CONCAT(f.first_name, ' ', f.last_name) SEPARATOR ', ') as friends
    FROM capsules c 
    LEFT JOIN friend_capsule_relation fcr ON c.capsule_id = fcr.capsule_id
    LEFT JOIN friends f ON fcr.friend_id = f.friend_id
    WHERE c.capsule_id = ? AND c.user_id = ?
    GROUP BY c.capsule_id
");
$stmt->execute([$capsule_id, $_SESSION['user_id']]);
$capsule = $stmt->fetch();

if (!$capsule) {
    header('Location: index.php');
    exit();
}

// Check if capsule is unlocked
if (!isCapsuleUnlocked($capsule['open_date'])) {
    header('Location: index.php');
    exit();
}

// Get capsule contents
$contents = getCapsuleContents($pdo, $capsule_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($capsule['title']); ?> - Memory Capsule</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="hero">
            <h1 class="hero-title"><?php echo htmlspecialchars($capsule['title']); ?> ✨</h1>
            <p class="hero-subtitle">Opened on <?php echo formatDate($capsule['open_date']); ?></p>
            <?php if ($capsule['friends']): ?>
                <p style="color: var(--gray-600); position: relative; z-index: 10;">
                    Shared with: <?php echo htmlspecialchars($capsule['friends']); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($contents)): ?>
            <section class="card-grid">
                <?php foreach ($contents as $content): ?>
                    <div class="card">
                        <h3 class="capsule-title">
                            <?php 
                            switch($content['content_type']) {
                                case 'image': echo '🖼️ Image'; break;
                                case 'video': echo '🎥 Video'; break;
                                case 'note': echo '📝 Note'; break;
                                default: echo '📄 Content';
                            }
                            ?>
                        </h3>
                        <p class="capsule-date"><?php echo htmlspecialchars($content['content_path']); ?></p>
                        
                        <?php if ($content['content_type'] === 'image'): ?>
                            <div style="background: var(--gray-100); border-radius: var(--border-radius); padding: 2rem; text-align: center; margin-top: 1rem;">
                                <p style="color: var(--gray-600);">📷 Image file</p>
                                <small style="color: var(--gray-500);"><?php echo basename($content['content_path']); ?></small>
                            </div>
                        <?php elseif ($content['content_type'] === 'video'): ?>
                            <div style="background: var(--gray-100); border-radius: var(--border-radius); padding: 2rem; text-align: center; margin-top: 1rem;">
                                <p style="color: var(--gray-600);">🎬 Video file</p>
                                <small style="color: var(--gray-500);"><?php echo basename($content['content_path']); ?></small>
                            </div>
                        <?php elseif ($content['content_type'] === 'note'): ?>
                            <div style="background: var(--primary-50); border-radius: var(--border-radius); padding: 1.5rem; margin-top: 1rem;">
                                <p style="color: var(--gray-700); font-style: italic;">
                                    "<?php echo htmlspecialchars(basename($content['content_path'], '.txt')); ?>"
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <section class="card text-center">
                <h3 class="capsule-title">Empty Capsule</h3>
                <p class="capsule-date">This capsule doesn't contain any memories yet.</p>
            </section>
        <?php endif; ?>
        
        <div class="text-center mt-8">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>