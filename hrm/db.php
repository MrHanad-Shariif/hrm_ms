<?php
try {
    $pdo = new PDO('oci:dbname=//localhost:1521/xe', 'system', 'admin123');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>
