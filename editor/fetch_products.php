<?php
require_once '../db.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $product) {
    echo '<div class="bg-gray-900 p-4 mb-4 rounded-lg shadow">';
    echo '<h3 class="text-xl">' . htmlspecialchars($product['title']) . '</h3>';
    echo '<p>Kategoria: ' . htmlspecialchars($product['category']) . '</p>';
    if (!empty($product['image'])) {
        echo '<img src="../img/' . htmlspecialchars($product['image']) . '" alt="Zdjęcie produktu" class="w-32 h-32 object-cover">';
    }
    echo '</div>';
}
?>
