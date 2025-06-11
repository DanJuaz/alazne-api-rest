<?php

try {
    $host = '192.168.1.111';
    $dbname = 'baltack_extranet_3000';
    $username = 'julio';
    $password = 'Urdanet.2024';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<h1>Database connection successful!</h1>";
    echo "<p>PHP version: " . phpversion() . "</p>";
    
    // Try a simple query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p>MySQL version: " . $result['version'] . "</p>";
    
} catch (PDOException $e) {
    echo "<h1>Connection failed:</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p>DSN: $dsn</p>";
    
    // Check loaded extensions
    echo "<h2>Loaded PHP Extensions:</h2>";
    echo "<ul>";
    $extensions = get_loaded_extensions();
    foreach ($extensions as $ext) {
        echo "<li>$ext</li>";
    }
    echo "</ul>";
}
