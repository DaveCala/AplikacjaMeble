<?php
// Dołącz plik z połączeniem do bazy danych
include('../db.php');
session_start();

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
      <div class="text-xl">Witaj <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
  </div>

  
  <div class="flex items-center mr-6">
    <!-- Przycisk Zarządzaj użytkownikami -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <a href="../admin/users.php" 
          class="bg-gray-900 border border-white text-white py-2 px-4 rounded-lg hover:bg-gray-700 text-sm mr-4">
          Zarządzaj użytkownikami
        </a>
    <?php endif; ?>


    <!-- Ikona Powiadomień -->
    <div class="relative">
      <img src="../img/mailbox.png" alt="Ikona Powiadomień" 
           class="w-10 h-10 cursor-pointer" onclick="toggleNotifications()">
    </div>
  </div>
</nav>


  <!-- Sekcja wyszukiwania -->
  <div class="mx-auto my-10 w-3/4 md:w-1/2">
    <input type="text" placeholder="Wyszukaj meble" class="w-full py-2 px-4 text-lg rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
  </div>

  <div class="flex justify-between items-center mt-10 mb-4 mx-6">
    <h2 class="text-2xl text-white">Baza mebli:</h2>
  </div>

  <!-- Grid z kafelkami -->
  <?php if (!empty($products)) : ?>
    <?php foreach ($products as $product) : ?>
      <div class="bg-gray-900 w-full p-4 border border-gray-700">
        <!-- Kontener produktu -->
        <div class="grid grid-cols-5 gap-4 w-full h-12 items-center rounded-lg p-2 overflow-hidden">
          
          <!-- Tytuł - 2/5 szerokości -->
          <div class="col-span-2 flex items-center justify-center text-white text-sm overflow-hidden">
            <h3 class="truncate text-center"><?php echo htmlspecialchars($product['title']); ?></h3>
          </div>

          <!-- Główne zdjęcie - 1/5 szerokości -->
          <div class="col-span-1 flex items-center justify-center">
            <img src="../img/<?php echo htmlspecialchars($product['image']); ?>" alt="Główne zdjęcie" class="h-full max-h-8 w-auto object-contain rounded-lg">
          </div>

          <!-- Kategoria - 1/5 szerokości -->
          <div class="col-span-1 flex items-center justify-center text-white overflow-hidden">
            <p class="truncate text-sm"><?php echo htmlspecialchars($product['category']); ?></p>
          </div>

          <!-- Przycisk - 1/5 szerokości -->
          <div class="col-span-1 flex items-center justify-center">
            <a href="viewer_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white py-1 px-2 rounded-lg hover:bg-blue-600 text-sm">Obejrzyj</a>
          </div>

        </div> 
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <p class="text-white text-center">Brak produktów w bazie danych.</p>
  <?php endif; ?>

  <div class="container mx-auto my-10">
    <!-- Miejsce na treść strony -->
  </div>
  
</body>
</html>
