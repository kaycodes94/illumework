<?php
// ============================================================
// ILLUME — Password Reset Utility
// ============================================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

echo "<h2>ILLUME — Database Maintenance</h2>";

try {
    $pdo = db();
    
    // New hash for 'password'
    $new_hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Update Founder
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'founder@illume.ng'");
    $stmt->execute([$new_hash]);
    echo "✔ Founder password reset to: password<br>";
    
    // Update Staff
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'adaeze@illume.ng'");
    $stmt->execute([$new_hash]);
    echo "✔ Staff password reset to: password<br>";
    
    // Update Demo Client
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'chioma@example.com'");
    $stmt->execute([$new_hash]);
    echo "✔ Client password reset to: password<br>";

    echo "<p style='color:green; font-weight:bold;'>Maintenance Complete. You can now login with the credentials provided.</p>";
    echo "<p>Please delete this file (<code>auth/reset_passwords.php</code>) for security.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Maintenance Failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Ensure your <code>illume_db</code> exists and <code>config/config.php</code> is correct.</p>";
}
