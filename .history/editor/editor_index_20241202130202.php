<?php
// Dołącz plik z połączeniem do bazy danych
include('../db.php');
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['username'])) {
  header('Location: ../index.php?error=not_logged_in');
  exit;
}

// Pobranie wszystkich produktów z bazy danych
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);
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
    <a href="../login/logout.php" class="bg-gray-900 border border-red-500 text-red-500 py-2 px-4 rounded-lg hover:bg-red-600 hover:text-white text-sm mr-4">
      Wyloguj się
    </a>

    <!-- Przycisk Zarządzaj użytkownikami (tylko dla admina) -->
    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
      <a href="../admin/users.php" class="bg-gray-900 border border-white text-white py-2 px-4 rounded-lg hover:bg-gray-700 text-sm mr-4">
        Zarządzaj użytkownikami
      </a>
    <?php endif; ?>
  </div>

   <!-- Ikona Powiadomień -->
   <<div class="relative flex items-center ml-6">
  <img src="../img/mailbox.png" alt="Ikona Powiadomień" 
       class="w-10 h-10 cursor-pointer hover:scale-110 transition-transform duration-200">
  <span class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
    3
  </span>
</div>


</nav>

<!-- Sekcja wyszukiwania -->
<div class="mx-auto my-10 w-3/4 md:w-1/2">
  <input type="text" placeholder="Wyszukaj meble" class="w-full py-2 px-4 text-lg rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#B79962]">
</div>

<!-- Sekcja dodawania produktu -->
<div class="flex justify-between items-center mt-10 mb-4 mx-6">
  <h2 class="text-2xl text-white">Baza mebli:</h2>
  <button id="toggle-add-form" class="py-2 px-4 bg-gray-800 rounded-lg border border-green-500 text-green-500 text-lg hover:bg-green-500 hover:text-white">
    Dodaj
  </button>
</div>

<div id="add-product-form" class="hidden bg-gray-900 p-6 rounded-lg shadow-lg mb-6">
  <h2 class="text-2xl text-white mb-4">Dodaj nowy produkt</h2>
  <form id="add-product" method="POST" enctype="multipart/form-data" action="add_product.php">
    <div class="mb-4 text-white">
      <label for="product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
      <input type="text" id="product-title" name="title" class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div class="mb-4 text-white">
      <label for="product-category" class="block mb-2 text-sm">Kategoria:</label>
      <input type="text" id="product-category" name="category" class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div class="mb-4 text-white">
      <label for="product-image" class="block mb-2 text-sm">Zdjęcie:</label>
      <input type="file" id="product-image" name="image" class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div class="flex justify-center mb-6">
      <button type="submit" class="py-2 px-4 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Dodaj
      </button>
    </div>
  </form>
</div>

<!-- Lista produktów -->
<div class="container mx-auto">
  <?php if (!empty($products)) : ?>
    <?php foreach ($products as $product) : ?>
      <div class="bg-gray-900 w-full p-4 border border-gray-700 mb-4">
        <div class="grid grid-cols-5 gap-4 items-center">
          <div class="col-span-2 text-white text-sm">
            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
          </div>
          <div class="col-span-1">
            <img src="../img/<?php echo htmlspecialchars($product['image']); ?>" alt="Produkt" class="h-16 w-auto object-contain">
          </div>
          <div class="col-span-1 text-white">
            <p><?php echo htmlspecialchars($product['category']); ?></p>
          </div>
          <div class="col-span-1">
            <a href="editor_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white py-1 px-2 rounded-lg hover:bg-blue-600 text-sm">Obejrzyj</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <p class="text-white text-center">Brak produktów w bazie danych.</p>
  <?php endif; ?>
</div>

<script>
  document.getElementById('add-product').addEventListener('submit', function (e) {
  e.preventDefault(); // Zapobiega przeładowaniu strony

  const form = e.target;
  const formData = new FormData(form);

  fetch('add_product.php', {
    method: 'POST',
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message || 'Produkt został dodany pomyślnie.');
      form.reset();
      loadProductList(); // Odśwież listę produktów
    } else {
      alert('Błąd: ' + (data.message || 'Nie udało się dodać produktu.'));
    }
  })
  .catch(error => {
    console.error('Błąd:', error);
    alert('Wystąpił błąd podczas dodawania produktu.');
  });
});


  // Funkcja toggle dla formularza
  document.getElementById('toggle-add-form').addEventListener('click', function () {
    document.getElementById('add-product-form').classList.toggle('hidden');
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
</script>

</body>
</html>
