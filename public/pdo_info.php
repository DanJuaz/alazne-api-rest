<?php

// Mostrar información sobre PDO
$availableDrivers = PDO::getAvailableDrivers();
echo "<h1>PDO Available Drivers:</h1>";
echo "<ul>";
foreach ($availableDrivers as $driver) {
    echo "<li>$driver</li>";
}
echo "</ul>";

// Intentar crear una conexión PDO usando mysql
try {
    $pdo = new PDO('mysql:host=localhost');
    echo "<h2>PDO MySQL Connection Test (localhost)</h2>";
    echo "<p>Connection successful!</p>";
} catch (Exception $e) {
    echo "<h2>PDO MySQL Connection Test (localhost)</h2>";
    echo "<p>Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Intentar crear una conexión PDO usando el host remoto
try {
    $pdo = new PDO('mysql:host=192.168.1.111');
    echo "<h2>PDO MySQL Connection Test (192.168.1.111)</h2>";
    echo "<p>Connection successful!</p>";
} catch (Exception $e) {
    echo "<h2>PDO MySQL Connection Test (192.168.1.111)</h2>";
    echo "<p>Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
