<?php
require_once '../db.php';

// Sprawdzenie ID produktu
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Nieprawidłowe ID produktu.');
}

$productId = (int)$_GET['id'];

// Pobranie danych produktu
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die('Produkt nie został znaleziony.');
    }
} catch (PDOException $e) {
    die('Błąd bazy danych: ' . $e->getMessage());
}

// Pobranie wariacji
// $variations = [];
// try {
//     $stmt = $pdo->prepare("SELECT * FROM variations WHERE product_id = :product_id");
//     $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
//     $stmt->execute();
//     $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     die('Błąd bazy danych: ' . $e->getMessage());
// }
?>



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
      <!-- Ukryte pole przechowujące ID produktu -->
      <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">

      <!-- Tytuł produktu -->
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="title"
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Kategoria produktu -->
      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="category"
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Zdjęcie produktu -->
      <div class="mb-4">
        <label for="edit-product-image" class="block mb-2 text-sm">Zdjęcie produktu:</label>
        <input type="file" id="edit-product-image" name="image"
               class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
      </div>

      <!-- Opis produktu -->
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
  </div>

  <script>
// Inicjalizacja funkcji po załadowaniu dokumentu
document.addEventListener('DOMContentLoaded', function () {
  setupAddProductForm();
  setupDeleteButton();
  setupToggleAddForm();
  setupProductCheckboxes();
});

// Funkcja obsługująca dodawanie produktów
function setupAddProductForm() {
  const form = document.getElementById('add-product');
  form.addEventListener('submit', function (e) {
    e.preventDefault();
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
        addProductToDOM(data.product); // Dodanie nowego produktu do listy
      } else {
        alert('Błąd: ' + (data.message || 'Nie udało się dodać produktu.'));
      }
    })
    .catch(error => {
      console.error('Błąd:', error);
      alert('Wystąpił błąd podczas dodawania produktu.');
    });
  });
}

// Dodanie produktu do listy bez przeładowania
function addProductToDOM(product) {
  const productList = document.getElementById('product-list');

  // Tworzenie nowego elementu produktu
  const productItem = document.createElement('div');
  productItem.classList.add('product-item');
  productItem.innerHTML = `
    <input type="checkbox" class="product-checkbox" data-product-id="${product.id}">
    <span>${product.name}</span>
  `;

  productList.appendChild(productItem);

  // Dodanie obsługi checkboxa
  productItem.querySelector('.product-checkbox').addEventListener('change', toggleDeleteButtonVisibility);
}

// Funkcja kontrolująca widoczność formularza
function setupToggleAddForm() {
  document.getElementById('toggle-add-form').addEventListener('click', function () {
    document.getElementById('add-product-form').classList.toggle('hidden');
  });
}

// Funkcja kontrolująca widoczność przycisku "Usuń"
function toggleDeleteButtonVisibility() {
  const anyChecked = Array.from(document.querySelectorAll('.product-checkbox')).some(cb => cb.checked);
  const deleteButtonContainer = document.getElementById('delete-button-container');
  deleteButtonContainer.classList.toggle('hidden', !anyChecked);
}

// Funkcja obsługująca usuwanie zaznaczonych produktów
function setupDeleteButton() {
  document.getElementById('delete-selected').addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                              .map(cb => cb.getAttribute('data-product-id'));

    if (selectedIds.length > 0) {
      fetch('delete_products.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ids: selectedIds }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message || 'Produkty zostały usunięte pomyślnie.');
          removeProductsFromDOM(selectedIds); // Usunięcie produktów z DOM
        } else {
          alert('Błąd: ' + (data.message || 'Nie udało się usunąć produktów.'));
        }
      })
      .catch(error => {
        console.error('Błąd podczas usuwania produktów:', error);
        alert('Wystąpił błąd podczas usuwania produktów.');
      });
    } else {
      alert('Nie zaznaczono żadnych produktów!');
    }
  });
}

// Usunięcie produktów z DOM bez przeładowania
function removeProductsFromDOM(ids) {
  ids.forEach(id => {
    const checkbox = document.querySelector(`.product-checkbox[data-product-id="${id}"]`);
    if (checkbox) {
      checkbox.parentElement.remove(); // Usuwamy cały element produktu
    }
  });
  toggleDeleteButtonVisibility(); // Aktualizacja widoczności przycisku "Usuń"
}

// Funkcja dodająca obsługę checkboxów
function setupProductCheckboxes() {
  document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleDeleteButtonVisibility);
  });
}
</script>

</body>
</html>


