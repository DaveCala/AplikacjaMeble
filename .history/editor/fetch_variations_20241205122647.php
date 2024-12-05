<?php
require_once '../db.php';

$productId = $_GET['product_id'] ?? null;

if (!$productId) {
    echo 'Brak ID produktu.';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM variations WHERE product_id = :product_id");
$stmt->bindParam(':product_id', $productId);
$stmt->execute();

$variations = $stmt->fetchAll();

foreach ($variations as $variation) {
    echo '<div class="variation-item">';
    echo '<h3>' . htmlspecialchars($variation['title']) . '</h3>';
    echo '<p>EAN: ' . htmlspecialchars($variation['ean']) . '</p>';
    if ($variation['image']) {
        echo '<img src="../img/variations/' . htmlspecialchars($variation['image']) . '" alt="' . htmlspecialchars($variation['title']) . '" />';
    }
    echo '</div>';
}
