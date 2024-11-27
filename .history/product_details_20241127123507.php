<?php
// Połączenie z bazą danych
$host = 'localhost';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// Pobranie id produktu z URL
$productId = $_GET['id'];

// Zapytanie do bazy o szczegóły produktu
$sql = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Jeśli produkt nie istnieje w bazie danych
if (!$product) {
    echo "<p>Produkt nie został znaleziony.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['title']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white">
  <div class="container mx-auto p-6">
    <div id="product-details" class="bg-gray-900 p-6 rounded-lg shadow-lg">
      <!-- Tytuł produktu na górze -->
      <h1 class="text-3xl mb-4"><?php echo htmlspecialchars($product['title']); ?></h1>

      <!-- Sekcja: Główne zdjęcie i Kategoria w jednym rzędzie -->
      <div class="flex items-center mb-6">
        <!-- Główne zdjęcie -->
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Zdjęcie produktu" class="w-1/3 h-auto max-h-96 object-contain mr-6">
        <!-- Kategoria -->
        <p class="text-xl text-gray-400"><?php echo htmlspecialchars($product['category']); ?></p>
      </div>

      <!-- Opis produktu -->
      <p class="mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
      <p class="text-lg font-bold mb-4"><?php echo "Cena: " . htmlspecialchars($product['price']); ?></p>
    </div>

    <!-- Sekcja: Wariacje Produktu -->
    <div id="product-variations" class="mt-10">
      <h2 class="text-2xl mb-6">Wariacje Produktu:</h2>
      <!-- Wariacje będą generowane w przyszłości -->
    </div>
  </div>
</body>
</html>
