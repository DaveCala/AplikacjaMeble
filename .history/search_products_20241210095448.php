<?php
// Wczytanie konfiguracji bazy danych
include('db.php');

// Pobranie frazy wyszukiwanej
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

try {
    if ($searchQuery === '') {
        // Jeśli brak frazy, zwróć wszystkie produkty
        $sql = "SELECT * FROM products";
        $stmt = $pdo->query($sql);
    } else {
        // Jeśli jest fraza, wyszukaj produkty według tytułu lub kategorii
        $sql = "SELECT * FROM products WHERE title LIKE :query OR category LIKE :query";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['query' => '%' . $searchQuery . '%']);
    }

    // Pobierz wyniki
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Renderuj wyniki jako HTML
    if (!empty($products)) {
        foreach ($products as $product) {
            echo '<div class="bg-gray-900 p-4 border border-gray-700 rounded-lg shadow-md flex items-center">';
            echo '<div class="flex items-center">';
            echo '<input type="checkbox" class="product-checkbox" data-product-id="' . htmlspecialchars($product['id']) . '">';
            echo '</div>';
            echo '<div class="w-1/6 flex justify-center">';
            echo '<img src="../img/' . htmlspecialchars($product['image']) . '" class="h-16 w-16 object-contain rounded-lg" alt="Zdjęcie produktu">';
            echo '</div>';
            echo '<div class="w-3/6 px-4">';
            echo '<h3 class="text-white text-lg truncate">' . htmlspecialchars($product['title']) . '</h3>';
            echo '<p class="text-gray-400 text-sm truncate">' . htmlspecialchars($product['category']) . '</p>';
            echo '</div>';
            echo '<div class="w-2/6 flex justify-end">';
            echo '<a href="editor_product.php?id=' . $product['id'] . '" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm">Obejrzyj</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-white text-center">Brak produktów w bazie danych.</p>';
    }
} catch (Exception $e) {
    echo '<p class="text-red-500 text-center">Błąd podczas wyszukiwania produktów.</p>';
}
?>
