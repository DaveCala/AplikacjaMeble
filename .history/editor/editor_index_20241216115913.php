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

  <style> 

    #notificationPanel ul {
      padding: 0;
      flex-grow: 1;
      overflow-y: auto;
    }

    #notificationPanel li {
      padding: 12px;
      border: 1px solid #2d3748;
      transition: background-color 0.3s, border-color 0.3s;
    }

    #notificationPanel li:hover {
      background-color: #b49659;
      border-color: white;
      cursor: pointer;
    }

    #notificationPanel .p-4 {
      border-bottom: 1px solid #2d3748;
    }

    #notificationPanel h3 {
      color: #e2e8f0;
    }

    #notificationPanel > div {
      border-top: 2px solid #2d3748;
      border-radius: 0 0 8px 8px;

    }
  </style>

</head>
<body class="bg-gray-800">

  <!-- Navbar -->
<nav class="bg-gray-900 navbar w-full py-6 text-white flex justify-between items-center">
  <div class="flex items-center ml-6">
    <img src="../img/logo_beautysofa_24_pionowe.png" class="w-10 h-10 mr-3" alt="Logo">
    <div class="text-xl">Witaj <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</div>
  </div>

  <div class="flex items-center space-x-4 mr-6">
    <!-- Przycisk Wyloguj się -->
    <a href="../login/logout.php" 
       class="bg-gray-900 border border-red-500 text-red-500 py-2 px-4 rounded-lg hover:bg-red-800 hover:text-white text-sm">
      Wyloguj się
    </a>

    <!-- Przycisk Zarządzaj użytkownikami (tylko dla admina) -->
    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
      <a href="../admin/users.php" 
         class="bg-gray-900 border border-white text-white py-2 px-4 rounded-lg hover:bg-gray-700 text-sm">
        Zarządzaj użytkownikami
      </a>
    <?php endif; ?>

    <div class="relative flex items-center">
    <img src="../img/mailbox.png" alt="Ikona Powiadomień" 
         class="ml-5 w-10 h-10 cursor-pointer hover:scale-110 transition-transform duration-200"
         onclick="toggleNotifications()">
    <span class="absolute top-0 left-14 bg-red-700 text-white text-xs font-bold px-2 py-1 rounded-full">
      3
    </span>
    
    <!-- Lista powiadomień -->
    <div id="notificationPanel" class="hidden absolute top-14 right-0 bg-gray-900 shadow-lg rounded-md w-80 max-h-96 overflow-y-auto z-50">
      <div class="p-4 border-b">
        <h3 class="text-lg font-bold text-gray-200">Powiadomienia</h3>
      </div>
      <ul class="divide-y">
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Zmieniona cena dla produktu **Produkt A** na **300 zł** przez **Jan Kowalski**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Zmieniona cena dla produktu **Produkt B** na **450 zł** przez **Anna Nowak**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Dodano nowy produkt **Produkt C** przez **Piotr Zieliński**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Nowa aktualizacja dla **Produkt D** przez **Marta Kowalska**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Zmieniona cena dla produktu **Produkt E** na **100 zł** przez **Kamil Nowak**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Nowy użytkownik zarejestrowany: **Łukasz Wiśniewski**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Produkt **Produkt F** został wycofany z oferty.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Nowa recenzja dla **Produkt G** od **Paweł Zieliński**.
        </li>
        <li class="p-4 hover:bg-blue-600 hover:border-blue-500">
          Zmieniona cena dla produktu **Produkt H** na **320 zł** przez **Jan Kowalski**.
        </li>
      </ul>

</nav>

<!-- Pole wyszukiwania -->
<div class="mx-auto my-10 w-3/4 md:w-1/2">
  <input 
    type="text" 
    id="search-bar" 
    placeholder="Wyszukaj meble" 
    class="w-full py-2 px-4 text-lg rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#B79962]">
</div>

<!-- Sekcja dodawania produktu -->
<div class="flex justify-between items-center mt-10 mb-4 mx-6">
  <h2 class="text-2xl text-white">Baza mebli:</h2>
  <div class="flex items-center space-x-4">
    <div id="delete-button-container" class="hidden">
      <button id="delete-selected" class="bg-gray-800 text-red-500 border border-red-500 py-2 px-4 rounded-lg hover:bg-red-700 hover:text-white">
        Usuń zaznaczone
      </button>
    </div>
    <button id="toggle-add-form" class="py-2 px-4 bg-gray-800 rounded-lg border border-green-500 text-green-500 text-lg hover:bg-green-500 hover:text-white">
      Dodaj
    </button>
  </div>
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

    <!-- Dodane pole radio dla "Produkt z wariacjami" -->
    <div class="mb-4">
      <label class="block mb-1 text-white">Produkt z wariacjami:</label>
      <div>
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="is_variation"
            value="false"
            class="form-radio text-green-500"
            required
            onclick="toggleVariationForm()"
          />
          <span class="ml-2 text-white">Tak</span>
        </label>
      </div>
      <div>
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="is_variation"
            value="true"
            class="form-radio text-green-500"
            required
            onclick="toggleVariationForm()"
          />
          <span class="ml-2 text-white">Nie</span>
        </label>
      </div>

     <!-- Formularz dodatkowych danych, który pojawi się tylko, gdy "Produkt bez wariacjami" -->
    <div id="variationFields" style="display:none;">
      <div class="mb-4 text-white">
        <label for="price" class="block mb-2 text-sm">Cena:</label>
        <input
          type="text"
          id="price"
          name="price"
          class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div class="mb-4 text-white">
        <label for="description" class="block mb-2 text-sm">Opis:</label>
        <textarea
          id="description"
          name="description"
          class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        ></textarea>
      </div>
    </div>
    
    <!-- Formularz cech, widoczny tylko dla produktów bez wariacji -->
<div id="product-features" class="hidden">
  <h3 class="text-xl text-white mb-4">Cechy produktu:</h3>
  <div class="mb-4 text-white">
    <label class="block mb-2 text-sm">Wybierz cechy:</label>
    <div id="feature-checkboxes" class="flex flex-wrap gap-4">
      <!-- Lista checkboxów -->
      <label class="inline-flex items-center">
        <input type="checkbox" value="Szerokość" class="feature-checkbox form-checkbox text-green-500" />
        <span class="ml-2">Szerokość</span>
      </label>
      <label class="inline-flex items-center">
        <input type="checkbox" value="Wysokość siedziska" class="feature-checkbox form-checkbox text-green-500" />
        <span class="ml-2">Wysokość siedziska</span>
      </label>
      <label class="inline-flex items-center">
        <input type="checkbox" value="Liczba szuflad" class="feature-checkbox form-checkbox text-green-500" />
        <span class="ml-2">Liczba szuflad</span>
      </label>
      <!-- Możesz dodać więcej checkboxów -->
    </div>
  </div>
  <div id="dynamic-fields" class="space-y-4"></div>
</div>

      
    </div>

    <div class="flex justify-center mb-6">
      <button type="submit" class="py-2 px-4 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Dodaj
      </button>
    </div>
  </form>
</div>

<!-- Lista produktów -->
<!-- Grid z kafelkami -->
<?php if (!empty($products)) : ?>
  <div class="grid grid-cols-1 gap-2 w-full">
    <?php foreach ($products as $product) : ?>
      <div class="bg-gray-900 p-4 border border-gray-700 rounded-lg shadow-md flex items-center">
        
       <!-- Checkbox do zaznaczenia produktu -->
       <div class="flex items-center">
          <input type="checkbox" class="product-checkbox" data-product-id="<?php echo $product['id']; ?>">
        </div>

        <!-- Główne zdjęcie - 1/6 szerokości -->
        <div class="w-1/6 flex justify-center">
          <img src="../img/<?php echo htmlspecialchars($product['image']); ?>" 
               alt="Główne zdjęcie" 
               class="h-16 w-16 object-contain rounded-lg">
        </div>

        <!-- Tytuł produktu - 3/6 szerokości -->
        <div class="w-3/6 px-4">
          <h3 class="text-white text-lg truncate"><?php echo htmlspecialchars($product['title']); ?></h3>
          <p class="text-gray-400 text-sm truncate"><?php echo htmlspecialchars($product['category']); ?></p>
        </div>

        <!-- Przycisk - 2/6 szerokości -->
        <div class="w-2/6 flex justify-end">
          <a href="editor_product.php?id=<?php echo $product['id']; ?>" 
             class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 text-sm">
            Obejrzyj
          </a>
        </div>

      </div>
    <?php endforeach; ?>
  </div>
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
      location.reload();
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

  // Funkcja do monitorowania zaznaczenia checkboxów
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
  checkbox.addEventListener('change', function() {
    // Sprawdzenie, czy przynajmniej jeden checkbox jest zaznaczony
    const anyChecked = Array.from(document.querySelectorAll('.product-checkbox')).some(cb => cb.checked);
    
    // Pokazanie lub ukrycie przycisku "Usuń"
    const deleteButtonContainer = document.getElementById('delete-button-container');
    if (anyChecked) {
      deleteButtonContainer.classList.remove('hidden');
    } else {
      deleteButtonContainer.classList.add('hidden');
    }
  });
});


document.getElementById('delete-selected').addEventListener('click', function() {
  const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                            .map(cb => cb.getAttribute('data-product-id'));

  if (selectedIds.length > 0) {
    // Wysłanie zaznaczonych ID do skryptu PHP
    fetch('delete_products.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(selectedIds),
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message || 'Produkty zostały usunięte pomyślnie.');
        location.reload();
      } else {
        alert('Błąd: ' + (data.message || 'Nie udało się usunąć produktów.'));
      }
    })
    .catch(error => {
      console.error('Błąd podczas usuwania produktów:', error);
      alert('Wystąpił błąd podczas usuwania produktów.');
    });

    // Ukrycie przycisku po usunięciu
    document.getElementById('delete-button-container').classList.add('hidden');
  } else {
    alert('Nie zaznaczono żadnych produktów!');
  }
});

function toggleNotifications() {
      const notificationPanel = document.getElementById('notificationPanel');
      notificationPanel.classList.toggle('hidden');
    }

//WYSZUKIWARKA
document.querySelector('input[placeholder="Wyszukaj meble"]').addEventListener('input', function () {
    const query = this.value;

    // Wyślij zapytanie AJAX do search_products.php
    fetch(`../search_products.php?query=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Błąd sieci');
            }
            return response.text();
        })
        .then(data => {
            // Zaktualizuj listę produktów
            const productList = document.querySelector('.grid');
            if (productList) {
                productList.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Błąd podczas wyszukiwania:', error);
        });
});

// Funkcja do pokazania lub ukrycia formularza w zależności od wyboru
function toggleVariationForm() {
    const isVariation = document.querySelector('input[name="is_variation"]:checked').value;
    const variationFields = document.getElementById('variationFields');
    
    if (isVariation === 'true') {
      // Pokazuje pola dla produktów bez wariacjami
      variationFields.style.display = 'block';
    } else {
      // Ukrywa pola dla produktów z wariacjami
      variationFields.style.display = 'none';
    }
  }

// PRACA NAD CECHAMI
document.addEventListener("DOMContentLoaded", function () {
  const featureCheckboxes = document.querySelectorAll(".feature-checkbox");
  const dynamicFieldsContainer = document.getElementById("dynamic-fields");

  featureCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const featureName = this.value;

      if (this.checked) {
        // Tworzenie nowego pola
        const newField = document.createElement("div");
        newField.classList.add("feature-field", "mb-4", "text-white");
        newField.setAttribute("data-feature", featureName);

        newField.innerHTML = `
          <label class="block mb-2 text-sm">${featureName}:</label>
          <input 
            type="text" 
            name="features[${featureName}]" 
            class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="Podaj wartość dla ${featureName}" 
          />
        `;

        dynamicFieldsContainer.appendChild(newField);
      } else {
        // Usunięcie pola, gdy checkbox jest odznaczony
        const existingField = document.querySelector(`.feature-field[data-feature="${featureName}"]`);
        if (existingField) {
          dynamicFieldsContainer.removeChild(existingField);
        }
      }
    });
  });
});



</script>

</body>
</html>