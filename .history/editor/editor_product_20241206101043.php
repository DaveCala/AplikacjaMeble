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

    <div id="add-variation-form">
  <h2 class="text-2xl text-white mb-4">Dodaj nową wariację</h2>
  <form id="add-variation" method="POST" enctype="multipart/form-data">
    <div class="mb-4 text-white">
      <label for="variation-title" class="block mb-2 text-sm">Tytuł wariacji:</label>
      <input type="text" id="variation-title" name="title" class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div class="mb-4 text-white">
      <label for="variation-ean" class="block mb-2 text-sm">EAN:</label>
      <input type="text" id="variation-ean" name="ean" class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div class="mb-4 text-white">
      <label for="variation-image" class="block mb-2 text-sm">Zdjęcie:</label>
      <input type="file" id="variation-image" name="image" class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="flex justify-center mb-6">
      <button type="submit" class="py-2 px-4 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Dodaj
      </button>
    </div>
  </form>
</div>


    <!-- Lista wariacji -->
    <div class="mt-8">
      <h2 class="text-2xl text-white mb-4">Wariacje</h2>
      <div class="grid grid-cols-1 gap-2 w-full">
        <?php foreach ($variations as $variation) : ?>
          <div class="bg-gray-900 p-4 border border-gray-700 rounded-lg shadow-md flex items-center">
            
            <!-- Checkbox do zaznaczenia wariacji -->
            <div class="flex items-center">
              <input type="checkbox" class="variation-checkbox" data-variation-id="<?php echo $variation['id']; ?>">
            </div>

            <!-- Zdjęcie wariacji - 1/6 szerokości -->
            <div class="w-1/6 flex justify-center">
              <img src="../img/<?php echo htmlspecialchars($variation['main_image']); ?>" 
                   alt="Zdjęcie wariacji" 
                   class="h-16 w-16 object-contain rounded-lg">
            </div>

            <!-- Tytuł wariacji - 3/6 szerokości -->
            <div class="w-3/6 px-4">
              <h3 class="text-white text-lg truncate"><?php echo htmlspecialchars($variation['title']); ?></h3>
              <p class="text-gray-400 text-sm truncate">EAN: <?php echo htmlspecialchars($variation['ean']); ?></p>
            </div>

            <!-- Przycisk "Obejrzyj" - 2/6 szerokości -->
            <div class="w-2/6 flex justify-end">
              <button class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm toggle-details" 
                      data-variation-id="<?php echo $variation['id']; ?>">
                Obejrzyj
              </button>
            </div>
          </div>

          <!-- Miejsce na szczegóły wariacji z formularzem edycji -->
          <div id="details-<?php echo $variation['id']; ?>" class="hidden mt-4 p-4 bg-gray-800 rounded-lg">
    <form id="edit-variation-form-<?php echo $variation['id']; ?>" data-variation-id="<?php echo $variation['id']; ?>">
        <!-- Tytuł -->
        <label for="title-<?php echo $variation['id']; ?>" class="block text-white">Tytuł:</label>
        <input type="text" id="title-<?php echo $variation['id']; ?>" name="title" 
               value="<?php echo htmlspecialchars($variation['title']); ?>" 
               class="w-full p-2 rounded bg-gray-700 text-white">

        <!-- EAN -->
        <label for="ean-<?php echo $variation['id']; ?>" class="block text-white mt-4">EAN:</label>
        <input type="text" id="ean-<?php echo $variation['id']; ?>" name="ean" 
               value="<?php echo htmlspecialchars($variation['ean']); ?>" 
               class="w-full p-2 rounded bg-gray-700 text-white">

        <!-- Zdjęcie główne -->

            <label for="main_image-<?php echo $variation['id']; ?>" class="block text-white mt-4">Główne zdjęcie:</label>
            <div class="file-upload-area border-dashed border-2 border-gray-500 rounded-lg p-4 text-center mt-2">
                <p class="text-gray-400 mb-2">Przeciągnij lub wybierz zdjęcie</p>
                <div class="relative">
                    <img id="preview-<?php echo $variation['id']; ?>" src="#" alt="Podgląd zdjęcia" class="hidden w-32 h-32 object-cover mx-auto mb-4 rounded-lg">
                    <p class="text-gray-400 mb-2" id="file-name-<?php echo $variation['id']; ?>">Brak pliku</p>
                    <input type="file" id="main_image-<?php echo $variation['id']; ?>" name="main_image"
                          class="hidden file-input" accept="image/*" />
                    <button type="button" class="py-2 px-4 bg-blue-600 text-white rounded-lg">
                        Wybierz zdjęcie
                    </button>
                </div>
            </div>

        <!-- Przycisk zapisania zmian -->
        <button type="button" class="mt-4 py-2 px-4 bg-green-600 rounded-lg text-white save-variation"
                data-variation-id="<?php echo $variation['id']; ?>">
            Zapisz zmiany
        </button>
    </form>
</div>


        <?php endforeach; ?>
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

   // Obsługa rozwijania szczegółów po kliknięciu przycisku "Obejrzyj"
    document.querySelectorAll('.toggle-details').forEach(button => {
      button.addEventListener('click', () => {
        const variationId = button.getAttribute('data-variation-id');
        const detailsDiv = document.getElementById(`details-${variationId}`);
        
        // Przełączanie widoczności szczegółów
        if (detailsDiv.classList.contains('hidden')) {
          detailsDiv.classList.remove('hidden');
        } else {
          detailsDiv.classList.add('hidden');
        }
      });
    });


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
      location.reload(); // Odświeżenie strony, aby zaktualizować dane
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
document.querySelectorAll('.toggle-details').forEach(button => {
  button.addEventListener('click', () => {
    const variationId = button.getAttribute('data-variation-id');
    const detailsDiv = document.getElementById(`details-${variationId}`);
    
    // Przełączanie widoczności szczegółów
    if (detailsDiv.classList.contains('hidden')) {
      detailsDiv.classList.remove('hidden');
    } else {
      detailsDiv.classList.add('hidden');
    }
  });
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

document.addEventListener('DOMContentLoaded', () => {
  // Obsługa zapisania zmian dla wariacji
  document.querySelectorAll('.save-variation').forEach(button => {
    button.addEventListener('click', function() {
      const variationId = this.getAttribute('data-variation-id');
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
});


// Funkcja obsługująca otwieranie/zamykanie szczegółów
function handleToggleDetails(event) {
  const button = event.target;
  const variationId = button.getAttribute('data-variation-id');
  const detailsContainer = document.querySelector(`#details-${variationId}`);

  if (detailsContainer) {
    detailsContainer.classList.toggle('hidden'); // Przełącz widoczność szczegółów
  }
}


// Obsługa dodawania nowej wariacji
document.getElementById('add-variation').addEventListener('submit', function (e) {
  e.preventDefault(); // Zapobiega przeładowaniu strony

  const form = e.target;
  const formData = new FormData(form);
  
  // Pobranie ID produktu z URL
  const productId = new URLSearchParams(window.location.search).get('id');
  
  if (productId) {
    formData.append('product_id', productId); // Dodajemy product_id do danych formularza
  } else {
    alert('Nie znaleziono ID produktu.');
    return;
  }

  fetch('add_variation.php', {
    method: 'POST',
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message || 'Wariacja została dodana pomyślnie.');
      form.reset();
      loadVariationList(); // Zaktualizowanie listy wariacji po dodaniu
    } else {
      alert('Błąd: ' + (data.message || 'Nie udało się dodać wariacji.'));
    }
  })
  .catch(error => {
    console.error('Błąd:', error);
    alert('Wystąpił błąd podczas dodawania wariacji.');
  });
});

// Funkcja do załadowania listy wariacji
function loadVariationList() {
  fetch('fetch_variations.php')
    .then(response => response.text())
    .then(html => {
      document.getElementById('variation-list').innerHTML = html;
    })
    .catch(error => console.error('Błąd podczas ładowania listy wariacji:', error));
}


// Funkcja monitorująca zaznaczenie checkboxów wariacji
document.querySelectorAll('.variation-checkbox').forEach(checkbox => {
  checkbox.addEventListener('change', function() {
    // Sprawdzenie, czy przynajmniej jeden checkbox jest zaznaczony
    const anyChecked = Array.from(document.querySelectorAll('.variation-checkbox')).some(cb => cb.checked);
    
    // Pokazanie lub ukrycie przycisku "Usuń"
    const deleteButtonContainer = document.getElementById('delete-button-container');
    if (anyChecked) {
      deleteButtonContainer.classList.remove('hidden');
    } else {
      deleteButtonContainer.classList.add('hidden');
    }
  });
});

// Funkcja do usuwania zaznaczonych wariacji
document.getElementById('delete-selected').addEventListener('click', function() {
  const selectedIds = Array.from(document.querySelectorAll('.variation-checkbox:checked'))
                            .map(cb => cb.getAttribute('data-variation-id'));

  if (selectedIds.length > 0) {
    // Wysłanie zaznaczonych ID do skryptu PHP
    fetch('delete_variations.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(selectedIds),
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message || 'Wariacje zostały usunięte pomyślnie.');
        location.reload(); // Przeładowanie strony po usunięciu
      } else {
        alert('Błąd: ' + (data.message || 'Nie udało się usunąć wariacji.'));
      }
    })
    .catch(error => {
      console.error('Błąd podczas usuwania wariacji:', error);
      alert('Wystąpił błąd podczas usuwania wariacji.');
    });

    // Ukrycie przycisku po usunięciu
    document.getElementById('delete-button-container').classList.add('hidden');
  } else {
    alert('Nie zaznaczono żadnych wariacji!');
  }
});

  </script>
</body>
</html>