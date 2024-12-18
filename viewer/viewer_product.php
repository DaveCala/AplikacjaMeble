<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Szczegóły Produktu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="styles.css">
    
  </style>
</head>
<body class="bg-gray-800 text-white">

  <!-- Tytuł produktu -->
  <header class="w-full bg-gray-900 py-6 text-center">
    <h1 class="text-3xl font-bold">Biurko młodzieżowe JOVI 14</h1>
  </header>

  <section class="w-full px-6 mt-8">
    <h2 class="text-2xl font-semibold mb-4 border-b-2 border-gray-700 pb-2">Platformy:</h2>

    <!-- Grid kafelków -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
      <!-- Kafelek Allegro -->
      <div class="flex items-center justify-center bg-gray-700 p-4 rounded-lg h-32 cursor-pointer" onclick="setActive(this)">
        <img src="allegro-logo.png" alt="Allegro" class="h-20 object-contain">
      </div>
      
      <!-- Kafelek Erli -->
      <div class="flex items-center justify-center bg-gray-700 p-4 rounded-lg h-32 cursor-pointer" onclick="setActive(this)">
        <img src="erli-logo.png" alt="Erli" class="h-20 object-contain">
      </div>
      
      <!-- Kafelek Amazon -->
      <div class="flex items-center justify-center bg-gray-700 p-4 rounded-lg h-32 cursor-pointer" onclick="setActive(this)">
        <img src="amazon-logo.png" alt="Amazon" class="h-20 object-contain">
      </div>
      
      <!-- Kafelek Ceneo -->
      <div class="flex items-center justify-center bg-gray-700 p-4 rounded-lg h-32 cursor-pointer" onclick="setActive(this)">
        <img src="ceneo-logo.png" alt="Ceneo" class="h-20 object-contain">
      </div>
    </div>
  </section>

  <section class="w-full px-6 mt-8">
    <h2 class="text-2xl font-semibold mb-4 border-b-2 border-gray-700 pb-2">Wybór materiałów:</h2>

    <!-- Kontener głównych materiałów -->
    <div class="flex gap-4 flex-wrap material-container mt-6">
        <!-- Materiały będą dodawane dynamicznie -->
    </div>

    <!-- Przycisk Dodaj wariant -->
    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4" onclick="showMaterialPicker()">Dodaj materiał</button>

    <!-- Przycisk Usuń zaznaczony wariant -->
    <button class="bg-red-500 text-white py-2 px-4 rounded-lg mt-4" onclick="removeSelectedMaterial()">Usuń zaznaczony</button>
</section>

<!-- Panel do wyboru materiałów -->
<div id="material-picker-panel" class="mt-6 hidden">
    <form class="flex gap-2 flex-wrap">
        <label>
            <input type="checkbox" class="material-checkbox" id="material-poso1" value="Poso 1" onchange="updateSelection(this)">
            <div class="w-20 h-20 rounded-lg border-2 border-gray-300 flex items-center justify-center cursor-pointer material-tile">
                Poso 1
            </div>
        </label>
        <label>
            <input type="checkbox" class="material-checkbox" id="material-kronos9" value="Kronos 9" onchange="updateSelection(this)">
            <div class="w-20 h-20 rounded-lg border-2 border-gray-300 flex items-center justify-center cursor-pointer material-tile">
                Kronos 9
            </div>
        </label>
        <label>
            <input type="checkbox" class="material-checkbox" id="material-wenge" value="Wenge" onchange="updateSelection(this)">
            <div class="w-20 h-20 rounded-lg border-2 border-gray-300 flex items-center justify-center cursor-pointer material-tile">
                Wenge
            </div>
        </label>
        <label>
            <input type="checkbox" class="material-checkbox" id="material-bialy" value="Biały" onchange="updateSelection(this)">
            <div class="w-20 h-20 rounded-lg border-2 border-gray-300 flex items-center justify-center cursor-pointer material-tile">
                Biały
            </div>
        </label>
    </form>
    <button class="mt-4 bg-green-500 text-white py-2 px-4 rounded-lg" onclick="addSelectedMaterial()">Zatwierdź</button>
    <button class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4" onclick="hideMaterialPicker()">Schowaj</button>
</div>


  
    <section class="w-full px-6 mt-8">
      <h2 class="text-2xl font-semibold mb-4 border-b-2 border-gray-700 pb-2">Zdjęcia:</h2>
    
      <div class="flex gap-6">
        <!-- Główne zdjęcie -->
        <div class="flex-grow lg:w-1/2 bg-gray-700 p-4 rounded-lg">
          <img src="main-photo.jpg" alt="Główne zdjęcie" class="w-full h-auto object-contain">
        </div>
    
        <!-- Miniaturki zdjęć miniature )-->
        <div class="grid grid-cols-3 lg:grid-cols-3 gap-4 lg:w-1/2">
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb1.jpg" alt="Miniatura 1" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb2.jpg" alt="Miniatura 2" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb3.jpg" alt="Miniatura 3" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb4.jpg" alt="Miniatura 4" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb5.jpg" alt="Miniatura 5" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
          <div class="bg-gray-700 p-2 rounded-lg">
            <img src="thumb6.jpg" alt="Miniatura 6" class="w-full h-auto object-contain cursor-pointer" onclick="changeMainPhoto(this)">
          </div>
        </div>
      </div>
    </section>
    
    <section class="w-full px-6 mt-8">
      <h2 class="text-2xl font-semibold mb-4 border-b-2 border-gray-700 pb-2">Informacje:</h2>
  
      <!-- Kontener pól -->
      <div id="info-fields" class="field-container">
        <!-- Domyślne pola -->
        <div class="field">
          <label for="title" class="text-lg font-medium">Tytuł:</label>
          <input id="title" type="text" class="bg-gray-700 text-white w-full p-2 rounded-lg" placeholder="Wprowadź tytuł">
        </div>
        <div class="field">
          <label for="ean" class="text-lg font-medium">Kod EAN:</label>
          <input id="ean" type="text" class="bg-gray-700 text-white w-full p-2 rounded-lg" placeholder="Wprowadź kod EAN">
        </div>
      </div>
      
      <div class="field mt-4">
        <label for="description" class="text-lg font-medium">Opis:</label>
        <textarea id="description" class="bg-gray-700 text-white w-full p-2 rounded-lg h-40 resize-none" placeholder="Wprowadź opis produktu"></textarea>
      </div>

      <!-- Przycisk do otwierania panelu -->
      <button id="edit-fields-btn" class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4 block mx-auto">
        Dodaj lub edytuj pola
      </button>
  
      <!-- Panel z checkboxami -->
      <div id="fields-panel" class="panel hidden">
        <h3 class="text-lg font-medium mb-4">Wybierz dodatkowe pola:</h3>
        <div class="flex flex-col gap-4">
          <label>
            <input type="checkbox" value="Opis" class="mr-2"> Opis
          </label>
          <label>
            <input type="checkbox" value="Cena" class="mr-2"> Cena
          </label>
          <label>
            <input type="checkbox" value="Waga" class="mr-2"> Waga
          </label>
          <label>
            <input type="checkbox" value="Kod produktu" class="mr-2"> Kod produktu
          </label>
        </div>
        <div class="flex gap-4 mt-6">
          <button id="save-fields-btn" class="bg-green-500 text-white py-2 px-4 rounded-lg">Zapisz</button>
          <button id="cancel-fields-btn" class="bg-red-500 text-white py-2 px-4 rounded-lg">Anuluj</button>
        </div>
      </div>
    </section>

    <script src="js/modules/colors.js"></script>
    <script src="js/modules/fields.js"></script>
    <script src="js/modules/tiles.js"></script>
    <script src="js/app.js"></script>
</body>
</html>