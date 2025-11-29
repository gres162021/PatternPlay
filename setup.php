<?php
/**
 * Setup Script - Run this file once to set up the database
 * Access via: http://localhost/patternplay/setup.php
 */

require_once 'config.php';

// Connect to MySQL without database
try {
    $pdo_setup = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>PatternPlay Setup</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            h2 { color: #333; }
            .success { color: green; }
            .error { color: red; }
            .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 10px 0; }
            .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0; }
            .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
            ul { line-height: 1.8; }
            hr { margin: 20px 0; }
        </style>
    </head>
    <body>";
    
    echo "<h2>🎮 PatternPlay Database Setup</h2>";
    echo "<hr>";
    
    // Read and execute schema.sql
    $schema = file_get_contents('schema.sql');
    
    if ($schema === false) {
        die("<p class='error'>❌ Error: Cannot read schema.sql file</p></body></html>");
    }
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    $success_count = 0;
    $error_count = 0;
    
    echo "<div class='info'><strong>Executing SQL Statements...</strong></div>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo_setup->exec($statement);
                $preview = substr(str_replace(["\n", "\r", "  "], " ", $statement), 0, 80);
                echo "<p class='success'>✓ " . htmlspecialchars($preview) . "...</p>";
                $success_count++;
            } catch (PDOException $e) {
                echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                $error_count++;
            }
        }
    }
    
    echo "<hr>";
    echo "<h3 class='success'>✓ Setup Complete!</h3>";
    echo "<div class='info'>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Successful: $success_count statements</li>";
    echo "<li>" . ($error_count > 0 ? "⚠" : "✓") . " Errors: $error_count statements</li>";
    echo "<li>✓ Database: <strong>patternplay_db</strong></li>";
    echo "<li>✓ Upload folder created at: <strong>uploads/</strong></li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<p><strong>📧 Default Admin Account:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@patternplay.com</li>";
    echo "<li><strong>Password:</strong> password</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<p>";
    echo "<a class='btn' href='public/login.php'>🎮 User Login</a>";
    echo "<a class='btn' href='admin/login.php'>👨‍💼 Admin Login</a>";
    echo "</p>";
    
    echo "<hr>";
    echo "<div class='warning'>";
    echo "<p><strong>⚠ IMPORTANT SECURITY NOTICE:</strong></p>";
    echo "<p>Setelah setup selesai, <strong>HAPUS atau RENAME file setup.php</strong> ini untuk keamanan!</p>";
    echo "</div>";
    
    echo "</body></html>";
    
} catch (PDOException $e) {
    die("<p class='error'>❌ Connection failed: " . htmlspecialchars($e->getMessage()) . "</p></body></html>");
}
?>