<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getUserCapsules($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COUNT(cc.content_id) as content_count,
               GROUP_CONCAT(DISTINCT CONCAT(f.first_name, ' ', f.last_name) SEPARATOR ', ') as friends
        FROM capsules c 
        LEFT JOIN capsule_contents cc ON c.capsule_id = cc.capsule_id
        LEFT JOIN friend_capsule_relation fcr ON c.capsule_id = fcr.capsule_id
        LEFT JOIN friends f ON fcr.friend_id = f.friend_id
        WHERE c.user_id = ? 
        GROUP BY c.capsule_id
        ORDER BY c.open_date DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getCapsuleContents($pdo, $capsule_id) {
    $stmt = $pdo->prepare("SELECT * FROM capsule_contents WHERE capsule_id = ?");
    $stmt->execute([$capsule_id]);
    return $stmt->fetchAll();
}

function getUserFriends($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM friends WHERE user_id = ? ORDER BY first_name");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function isCapsuleUnlocked($open_date) {
    return strtotime($open_date) <= time();
}
?>