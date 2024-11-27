<?php
// Dołącz plik z połączeniem do bazy danych
include('../db.php');

// Zapytanie do bazy danych w celu pobrania wszystkich produktów
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);

// Pobieramy wszystkie produkty
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Baza mebli</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800">

  <!-- Navbar -->
  <nav class="bg-gray-900 navbar w-full py-6 text-white flex justify-between items-center">
    <div class="flex items-center ml-6">
      <img src="../img/logo_beautysofa_24_pionowe.png" class="w-10 h-10 mr-3" alt="Logo">
      <div class="text-xl">
        Witaj!
      </div>
    </div>
    <div class="mr-6">
      <div class="relative">
        <img src="../img/mailbox.png" alt="Ikona Powiadomień" class="w-10 h-10 cursor-pointer" onclick="toggleNotifications()">
      </div>
    </div>
  </nav>

  <!-- Sekcja wyszukiwania -->
  <div class="mx-auto my-10 w-3/4 md:w-1/2">
    <input type="text" placeholder="Wyszukaj meble" class="w-full py-2 px-4 text-lg rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
  </div>

  <div class="flex justify-between items-center mt-10 mb-4 mx-6">
    <h2 class="text-2xl text-white">Baza mebli:</h2>
    <button class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">DODAJ</button>
  </div>

  <!-- Produkty -->
  <div class="container mx-auto p-4">
    <?php foreach ($products as $product): ?>
      <div class="bg-gray-900 w-full p-4 mb-6 rounded-lg shadow-lg">
        <!-- Tytuł produktu -->
        <h3 class="text-xl text-white mb-4"><?php echo htmlspecialchars($product['title']); ?></h3>

        <!-- Główne zdjęcie produktu -->
        <div class="flex justify-center mb-4">
          <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Główne zdjęcie" class="h-48 w-auto object-contain rounded-lg">
        </div>

        <!-- Kategoria produktu -->
        <div class="flex justify-center mb-4">
          <p class="text-sm text-white"><?php echo htmlspecialchars($product['category']); ?></p>
        </div>

        <!-- Przycisk do obejrzenia szczegółów -->
        <div class="flex justify-center">
          <a href="editor_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm">Obejrzyj</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>
</html>
