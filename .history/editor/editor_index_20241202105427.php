<?php
// Dołącz plik z połączeniem do bazy danych
include('../db.php');
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['username'])) {
  // Jeśli użytkownik nie jest zalogowany, przekieruj go na stronę logowania
  header('Location: ../index.php?error=not_logged_in');
  exit;
}

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
      <div class="text-xl">Witaj <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</div>

  </div>

  
  <div class="flex items-center mr-6">
    <!-- Przycisk Wyloguj się -->
  <?php if (isset($_SESSION['user'])) : ?>
            <a href="../login/logout.php" 
            class="bg-gray-900 border border-red-500 text-red-500 py-2 px-4 rounded-lg hover:bg-red-600 hover:text-white text-sm mr-4">
               Wyloguj się
            </a>
        <?php endif; ?>


    <!-- Przycisk Zarządzaj użytkownikami -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') : ?>
        <a href="../admin/users.php" 
          class="bg-gray-900 border border-white text-white py-2 px-4 rounded-lg hover:bg-gray-700 text-sm mr-4">
          Zarządzaj użytkownikami
        </a>
    <?php endif; ?>

    

    <!-- Ikona Powiadomień -->
    <div class="relative">
      <img src="../img/mailbox.png" alt="Ikona Powiadomień" 
           class="w-10 h-10 cursor-pointer ml-8" onclick="toggleNotifications()">
    </div>
  </div>
</nav>


  <!-- Sekcja wyszukiwania -->
  <div class="mx-auto my-10 w-3/4 md:w-1/2">
  <input type="text" placeholder="Wyszukaj meble" 
       class="w-full py-2 px-4 text-lg rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#B79962]">
  </div>

  <div class="flex justify-between items-center mt-10 mb-4 mx-6">
    <h2 class="text-2xl text-white">Baza mebli:</h2>
    <button id="toggle-add-form" 
              class="py-2 px-4 bg-gray-900 rounded-lg border border-green-500 text-green-500 text-lg hover:bg-green-500 text-white">
        Dodaj
    </button>
  </div>


    <!-- Formularz dodawania produktu -->
    <div id="add-product-form" class="hidden bg-gray-900 p-6 rounded-lg shadow-lg mb-6">
      <h2 class="text-2xl text-white mb-4">Dodaj nowy produkt</h2>
      <form id="add-product">
        <div class="mb-4 text-white ">
          <label for="product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
          <input type="text" id="product-title" name="title" 
                 class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                 required>
        </div>
        <div class="mb-4 text-white ">
          <label for="product-category" class="block mb-2 text-sm">Kategoria:</label>
          <input type="text" id="product-category" name="category" 
                 class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                 required>
        </div>
        <div class="mb-4 text-white ">
          <label for="product-image" class="block mb-2 text-sm">Zdjęcie:</label>
          <input type="file" id="product-image" name="image" 
                 class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex justify-center mb-6">
        <button id="toggle-add-form" 
                class="py-2 px-4 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
          Dodaj
        </button>
</div>

      </form>
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
            <a href="editor_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white py-1 px-2 rounded-lg hover:bg-blue-600 text-sm">Obejrzyj</a>
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
  
  <script>
    // Toggle formularza
    document.getElementById('toggle-add-form').addEventListener('click', function () {
      const form = document.getElementById('add-product-form');
      form.classList.toggle('hidden');
    });

    // Obsługa formularza dodawania produktu
    document.getElementById('submit-product').addEventListener('click', function () {
      const form = document.getElementById('add-product');
      const formData = new FormData(form);

      fetch('add_product_backend.php', {
        method: 'POST',
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Produkt został dodany.');
          form.reset(); // Resetuj formularz
          loadProductList(); // Odśwież listę produktów
        } else {
          alert('Błąd: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas dodawania produktu.');
      });
    });

    // Funkcja do załadowania listy produktów
    function loadProductList() {
      fetch('fetch_products.php')
        .then(response => response.text())
        .then(html => {
          document.getElementById('product-list').innerHTML = html;
        })
        .catch(error => console.error('Błąd podczas ładowania listy produktów:', error));
    }

    // Załaduj listę produktów po załadowaniu strony
    document.addEventListener('DOMContentLoaded', loadProductList);
  </script>

</body>
</html>
