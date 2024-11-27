<?php
// Połączenie z bazą danych
require_once 'db.php';

// Pobranie identyfikatora produktu z URL (przykład: product_details.php?id=1)
$productId = isset($_GET['id']) ? $_GET['id'] : 0;

// Zapytanie o dane produktu
$query = "SELECT * FROM products WHERE id = $productId";
$result = $conn->query($query);

// Sprawdzanie, czy produkt został znaleziony
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Jeśli produkt nie istnieje, ustawiamy domyślne wartości
    $product = null;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Szczegóły Produktu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white">
  <div class="container mx-auto p-6">
    <div id="product-details" class="bg-gray-900 p-6 rounded-lg shadow-lg">
      <!-- Tytuł produktu na górze -->
      <h1 id="product-title" class="text-3xl mb-4">
        <?php echo htmlspecialchars($product['title'] ?? 'Ładowanie...'); ?>
      </h1>

      <!-- Sekcja: Główne zdjęcie i Kategoria w jednym rzędzie -->
      <div class="flex items-center mb-6">
        <!-- Główne zdjęcie -->
        <img id="product-image" src="../img/<?php echo htmlspecialchars($product['image'] ?? ''); ?>" alt="Zdjęcie produktu" class="w-1/3 h-auto max-h-96 object-contain mr-6">
        <!-- Kategoria -->
        <p id="product-category" class="text-xl text-gray-400">
          <?php echo htmlspecialchars($product['category'] ?? 'Ładowanie...'); ?>
        </p>
      </div>

      <!-- Opis produktu -->
      <p id="product-description" class="mb-4">
        <?php echo htmlspecialchars($product['description'] ?? 'Ładowanie...'); ?>
      </p>

      <!-- Cena produktu -->
      <p id="product-price" class="text-lg font-bold mb-4">
        <?php echo htmlspecialchars($product['price'] ?? 'Cena: Ładowanie...'); ?>
      </p>
    </div>

    <!-- Sekcja: Wariacje Produktu -->
    <div id="product-variations" class="mt-10">
      <h2 class="text-2xl mb-6">Wariacje Produktu:</h2>
      <!-- Wariacje będą dynamicznie generowane -->
      <?php
      // Sprawdzamy, czy produkt ma wariacje
      $variationsQuery = "SELECT * FROM variations WHERE product_id = $productId";
      $variationsResult = $conn->query($variationsQuery);
      if ($variationsResult->num_rows > 0) {
          while ($variation = $variationsResult->fetch_assoc()) {
              ?>
              <div class="bg-gray-900 p-4 mb-4 rounded-lg shadow">
                <h3 class="text-xl"><?php echo htmlspecialchars($variation['title']); ?></h3>
                <p><strong>Opis:</strong> <?php echo htmlspecialchars($variation['description']); ?></p>
                <p><strong>Cena:</strong> <?php echo htmlspecialchars($variation['price']); ?></p>
                <p><strong>EAN:</strong> <?php echo htmlspecialchars($variation['ean']); ?></p>
              </div>
              <?php
          }
      } else {
          echo "<p>Brak wariacji dla tego produktu.</p>";
      }
      ?>
    </div>
  </div>
</body>
</html>
