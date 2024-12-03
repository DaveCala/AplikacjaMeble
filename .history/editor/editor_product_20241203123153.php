<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edytuj Produkt</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white">
  <div class="container mx-auto p-6">
    <h1 class="text-3xl mb-6">Edytuj Produkt</h1>
    <form id="edit-product-form" enctype="multipart/form-data">
      <!-- Formularz głównego produktu -->
      <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="title"
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="category"
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label for="edit-product-description" class="block mb-2 text-sm">Opis produktu:</label>
        <textarea id="edit-product-description" name="description" rows="5"
                  class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>
      <button type="button" id="save-product-details"
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </form>

    <!-- Grid kafelków wariacji -->
    <h2 class="text-2xl mt-10 mb-4">Wariacje</h2>
    <div class="grid grid-cols-3 gap-4">
      <!-- Wariacja 1 -->
      <div class="bg-gray-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold">Wariacja 1</h3>
        <p>Kod EAN: 1234567890123</p>
        <button class="mt-2 py-2 px-4 bg-blue-600 rounded-lg text-white hover:bg-blue-500">Edytuj</button>
      </div>
      <!-- Wariacja 2 -->
      <div class="bg-gray-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold">Wariacja 2</h3>
        <p>Kod EAN: 9876543210987</p>
        <button class="mt-2 py-2 px-4 bg-blue-600 rounded-lg text-white hover:bg-blue-500">Edytuj</button>
      </div>
      <!-- Wariacja 3 -->
      <div class="bg-gray-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold">Wariacja 3</h3>
        <p>Kod EAN: 1122334455667</p>
        <button class="mt-2 py-2 px-4 bg-blue-600 rounded-lg text-white hover:bg-blue-500">Edytuj</button>
      </div>
    </div>
  </div>

  <script>
    // Obsługa zapisania danych produktu
    document.getElementById('save-product-details').addEventListener('click', () => {
      const form = document.getElementById('edit-product-form');
      const formData = new FormData(form);

      fetch('update_product.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Produkt został zaktualizowany.');
          location.reload();
        } else {
          alert('Błąd: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas zapisywania.');
      });
    });
  </script>
</body>
</html>
