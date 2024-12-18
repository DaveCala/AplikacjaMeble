<?php
// Połączenie z bazą danych
require_once '../db.php'; // Wczytuje konfigurację z db.php

try {
    // Użycie odpowiednich zmiennych z db.php
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

// Pobieranie danych produktu
$productId = $_GET['id'] ?? 0;

if ($productId) {
    $queryProduct = $pdo->prepare("SELECT * FROM products WHERE id = :productId");
    $queryProduct->execute(['productId' => $productId]);
    $product = $queryProduct->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $queryVariations = $pdo->prepare("SELECT * FROM variations WHERE product_id = :productId");
        $queryVariations->execute(['productId' => $productId]);
        $variations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Produkt nie został znaleziony.");
    }
} else {
    die("Nie podano ID produktu.");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edytuj Produkt</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
.file-upload-area {
    position: relative;
    cursor: pointer;
    background-color: #1a202c;
}

.file-upload-area.dragover {
    background-color: #2d3748;
    border-color: #4a5568;
}

.file-upload-area p {
    margin: 0;
}

.file-upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
</style>

</head>
<body class="bg-gray-800 text-white">
<div class="container mx-auto p-6">
    <h1 class="text-3xl mb-6">Edytuj Produkt</h1>
    <?php if ($product['isVariation'] ?? false): ?>
        <!-- Panel edycji produktu, gdy isVariation = true -->
        <form id="edit-product-form" enctype="multipart/form-data">
            <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

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

      <button type="button" id="save-product-details"
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </form>
    <?php else: ?>
      <!-- Panel edycji produktu, gdy isVariation = false -->
      <form id="edit-product-form" enctype="multipart/form-data">
            <input type="hidden" id="product-id" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

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

      <button type="button" id="save-product-details"
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </form>
        <h2 class="text-2xl mb-4">Wariacje</h2>
        <div id="variation-list" class="grid grid-cols-1 gap-2 w-full">
            <?php foreach ($variations as $variation): ?>
                <div class="bg-gray-900 p-4 border border-gray-700 rounded-lg shadow-md flex items-center">
                    <div class="w-1/6 flex justify-center">
                        <img src="../img/<?php echo htmlspecialchars($variation['main_image']); ?>" 
                             alt="Zdjęcie wariacji" 
                             class="h-16 w-16 object-contain rounded-lg">
                    </div>
                    <div class="w-3/6 px-4">
                        <h3 class="text-white text-lg truncate"><?php echo htmlspecialchars($variation['title']); ?></h3>
                        <p class="text-gray-400 text-sm truncate">EAN: <?php echo htmlspecialchars($variation['ean']); ?></p>
                    </div>
                    <div class="w-2/6 flex justify-end">
                        <button class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm toggle-details" 
                                data-variation-id="<?php echo htmlspecialchars($variation['id']); ?>">
                            Obejrzyj
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

  <script>
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

  // Obsługa rozwijania szczegółów po kliknięciu przycisku "Obejrzyj"
document.body.addEventListener('click', (event) => {
  if (event.target.classList.contains('toggle-details')) {
    const button = event.target;
    const variationId = button.getAttribute('data-variation-id');
    const detailsDiv = document.getElementById(`details-${variationId}`);

    // Przełączanie widoczności szczegółów
    if (detailsDiv) {
      detailsDiv.classList.toggle('hidden');
    }
  }
});

// Obsługa przeciągania i upuszczania plików dla zdjęcia wariacji
document.querySelectorAll('.file-upload-area').forEach(area => {
  const input = area.querySelector('.file-input');

  // Kliknięcie na obszar
  area.addEventListener('click', () => input.click());

  // Zdarzenia drag & drop
  area.addEventListener('dragover', (e) => {
    e.preventDefault();
    area.classList.add('dragover');
  });

  area.addEventListener('dragleave', () => {
    area.classList.remove('dragover');
  });

  area.addEventListener('drop', (e) => {
    e.preventDefault();
    area.classList.remove('dragover');

    // Pobierz upuszczone pliki
    if (e.dataTransfer.files.length > 0) {
      input.files = e.dataTransfer.files;
      alert(`Wybrano plik: ${input.files[0].name}`);
    }
  });

  // Obsługa zmiany pliku po kliknięciu
  input.addEventListener('change', () => {
    if (input.files.length > 0) {
      alert(`Wybrano plik: ${input.files[0].name}`);
    }
  });
});

// Obsługa zapisania danych wariacji
document.querySelectorAll('.save-variation').forEach(button => {
  button.addEventListener('click', () => {
    const variationId = button.getAttribute('data-variation-id');
    const form = document.querySelector(`#edit-variation-form-${variationId}`);
    const formData = new FormData(form);

    // Dodanie ID wariacji do FormData
    formData.append('variation_id', variationId);

    fetch('update_variation.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Wariacja została zaktualizowana.');

        // Aktualizacja kafelka wariacji
        const variationTile = document.querySelector(`.toggle-details[data-variation-id="${variationId}"]`).closest('.flex.items-center');
        variationTile.querySelector('h3').textContent = data.updatedTitle;
        variationTile.querySelector('p:nth-child(2)').textContent = `EAN: ${data.updatedEAN}`;

        // Jeśli zdjęcie zostało zaktualizowane
        if (data.updatedImage) {
          variationTile.querySelector('img').src = `../img/${data.updatedImage}`;
        }
      } else {
        alert('Błąd: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Błąd:', error);
      alert('Wystąpił błąd podczas zapisywania.');
    });
  });
});

// Funkcja do załadowania listy wariacji
function loadVariationList(productId) {
  fetch('fetch_variations.php?product_id=' + productId)
    .then(response => response.text())
    .then(html => {
      document.getElementById('variation-list').innerHTML = html;
    })
    .catch(error => console.error('Błąd podczas ładowania listy wariacji:', error));
}


  //Usuwanie wariacji 
  document.addEventListener("DOMContentLoaded", () => {
  const checkboxes = document.querySelectorAll(".variation-checkbox");
  const deleteButton = document.getElementById("delete-selected-variations");

  // Funkcja sprawdzająca, czy jest zaznaczony przynajmniej jeden checkbox
  const toggleDeleteButton = () => {
    const anyChecked = Array.from(checkboxes).some((checkbox) => checkbox.checked);
    deleteButton.classList.toggle("hidden", !anyChecked);
  };

  // Nasłuchiwanie zmian w checkboxach
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", toggleDeleteButton);
  });

  // Obsługa kliknięcia w przycisk usuwania
  deleteButton.addEventListener("click", () => {
    const selectedVariations = Array.from(checkboxes)
      .filter((checkbox) => checkbox.checked)
      .map((checkbox) => checkbox.dataset.variationId);

    if (selectedVariations.length > 0) {
      const confirmDelete = confirm("Czy na pewno chcesz usunąć zaznaczone wariacje?");
      if (confirmDelete) {
        // Wysłanie żądania do backendu
        fetch("delete_variations.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ ids: selectedVariations }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert("Wariacje zostały usunięte.");
              // Usunięcie zaznaczonych wariacji z DOM
              selectedVariations.forEach((id) => {
                const checkbox = document.querySelector(`.variation-checkbox[data-variation-id="${id}"]`);
                if (checkbox) {
                  checkbox.closest(".bg-gray-900").remove();
                }
              });
              toggleDeleteButton(); // Ukryj przycisk, jeśli nic nie jest zaznaczone
            } else {
              alert("Nie udało się usunąć wariacji: " + data.error);
            }
          })
          .catch((error) => {
            console.error("Błąd:", error);
            alert("Wystąpił błąd podczas usuwania wariacji.");
          });
      }
    }
  });
});


//DODAWANIE WARIACJI
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("add-variation-form");
  const variationsList = document.getElementById("variations-list");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const productId = document.getElementById("product-id").value;

    formData.append("product_id", productId);

    fetch("add_variation.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message || 'Wariacja została dodany pomyślnie.');
          form.reset();
          location.reload();
          // Aktualizuj listę wariacji
          addVariationToList({
            title: formData.get("title"),
            ean: formData.get("ean"),
            main_image: formData.get("main_image").name || null,
          });
          form.reset();
        } else {
          alert("Błąd: " + data.message);
        }
      })
  });

  const addVariationToList = (variation) => {
    const variationItem = document.createElement("div");
    variationItem.className = "bg-gray-900 p-4 mb-4 rounded-lg flex items-center justify-between";

    variationItem.innerHTML = `
      <div>
        <h3 class="text-lg font-bold">${variation.title}</h3>
        <p>EAN: ${variation.ean}</p>
        ${
          variation.main_image
            ? `<img src="../img/${variation.main_image}" alt="${variation.title}" class="w-16 h-16 mt-2 rounded-lg" />`
            : ""
        }
      </div>
    `;

    variationsList.appendChild(variationItem);
  };
});




</script>

</body>
</html>
