<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$capsule_id = $_GET['capsule_id'] ?? 0;
$user = getCurrentUser($pdo);

// Verify capsule belongs to user
$stmt = $pdo->prepare("SELECT * FROM capsules WHERE capsule_id = ? AND user_id = ?");
$stmt->execute([$capsule_id, $_SESSION['user_id']]);
$capsule = $stmt->fetch();

if (!$capsule) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_type = $_POST['content_type'];
    $content_description = trim($_POST['content_description']);
    
    if (empty($content_type) || empty($content_description)) {
        $error = 'Please fill in all fields.';
    } else {
        // Create uploads directory structure if it doesn't exist
        $upload_dir = 'uploads/' . $content_type . 's/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate a simple filename based on description
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $content_description);
        $extension = ($content_type === 'note') ? '.txt' : (($content_type === 'image') ? '.jpg' : '.mp4');
        $content_path = $upload_dir . $filename . $extension;
        
        // For notes, create a simple text file
        if ($content_type === 'note') {
            file_put_contents($content_path, $content_description);
        }
        
        // Insert content record
        $stmt = $pdo->prepare("INSERT INTO capsule_contents (capsule_id, content_type, content_path) VALUES (?, ?, ?)");
        if ($stmt->execute([$capsule_id, $content_type, $content_path])) {
            $success = 'Content added successfully!';
        } else {
            $error = 'Error adding content. Please try again.';
        }
    }
}

// Get existing contents
$contents = getCapsuleContents($pdo, $capsule_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Content - <?php echo htmlspecialchars($capsule['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="hero">
            <h1 class="hero-title">Add Content to "<?php echo htmlspecialchars($capsule['title']); ?>"  <img src="public/images/flower.jpg
                " width="80" height="80" style="border-radius: 50%;"></h1>
            <p class="hero-subtitle">Add memories to your time capsule</p>
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
                    <label for="content_type" class="form-label">Content Type *</label>
                    <select id="content_type" name="content_type" class="form-input form-select" required>
                        <option value="">Select content type...</option>
                        <option value="note" <?php echo ($_POST['content_type'] ?? '') === 'note' ? 'selected' : ''; ?>> <img src="public/images/ccc.jpg
                " width="80" height="80" style="border-radius: 50%;"> Note</option>
                        <option value="image" <?php echo ($_POST['content_type'] ?? '') === 'image' ? 'selected' : ''; ?>> <img src="public/images/ccc.jpg
                " width="80" height="80" style="border-radius: 50%;"> Image</option>
                        <option value="video" <?php echo ($_POST['content_type'] ?? '') === 'video' ? 'selected' : ''; ?>> <img src="public/images/ccc.jpg
                " width="80" height="80" style="border-radius: 50%;"> Video</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content_description" class="form-label">Content Description *</label>
                    <textarea id="content_description" name="content_description" class="form-input form-textarea" required 
                              placeholder="Describe your memory or write your note here..."><?php echo htmlspecialchars($_POST['content_description'] ?? ''); ?></textarea>
                    <small style="color: var(--gray-500); font-size: 0.85rem;">
                        For notes, this will be the content. For images/videos, this will be the description.
                    </small>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary">Add Content</button>
                    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
        
        <?php if (!empty($contents)): ?>
            <section class="mt-8">
                <h2 class="text-center" style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--gray-800); margin-bottom: 2rem;">
                    Current Contents
                </h2>
                <div class="card-grid">
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
                            <p class="capsule-date"><?php echo htmlspecialchars(basename($content['content_path'])); ?></p>
                            
                            <?php if ($content['content_type'] === 'note' && file_exists($content['content_path'])): ?>
                                <div style="background: var(--primary-50); border-radius: var(--border-radius); padding: 1.5rem; margin-top: 1rem;">
                                    <p style="color: var(--gray-700); font-style: italic;">
                                        "<?php echo htmlspecialchars(substr(file_get_contents($content['content_path']), 0, 100)); ?>..."
                                    </p>
                                </div>
                            <?php else: ?>
                                <div style="background: var(--gray-100); border-radius: var(--border-radius); padding: 2rem; text-align: center; margin-top: 1rem;">
                                    <p style="color: var(--gray-600);">
                                        <?php echo $content['content_type'] === 'image' ? '📷' : '🎬'; ?> 
                                        <?php echo ucfirst($content['content_type']); ?> file
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>