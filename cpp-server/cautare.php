<?php

try {
    $pdo = new PDO("mysql:host=192.168.0.3;dbname=recursivitate;charset=utf8", "tudor", "tudor");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM grila");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    var_dump($row);
}

?>