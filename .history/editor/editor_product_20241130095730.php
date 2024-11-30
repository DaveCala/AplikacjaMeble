<div id="product-details" class="bg-gray-900 p-6 rounded-lg shadow-lg">
  <h1 class="text-3xl mb-6">Szczegóły Produktu</h1>

  <!-- Ukryte pole przechowujące ID produktu -->
  <input type="hidden" id="product-id" value="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">

  <!-- Sekcja: Szczegóły produktu ułożone poziomo -->
  <div class="flex flex-wrap lg:flex-nowrap gap-6 items-start">
    <!-- Główne zdjęcie produktu -->
    <div class="flex-shrink-0">
      <img id="product-image" 
           src="../img/<?php echo htmlspecialchars($product['image'] ?? ''); ?>" 
           alt="Zdjęcie produktu" 
           class="w-full max-w-xs h-auto max-h-96 object-contain bg-gray-800 p-4 rounded-lg">
      <div class="mt-4">
        <label class="block mb-2 text-sm">Zmień zdjęcie:</label>
        <input type="file" id="edit-product-image" name="product-image" 
               class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <!-- Informacje o produkcie -->
    <div class="flex-1">
      <div class="mb-4">
        <label for="edit-product-title" class="block mb-2 text-sm">Tytuł produktu:</label>
        <input type="text" id="edit-product-title" name="product-title" 
               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" 
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div class="mb-4">
        <label for="edit-product-category" class="block mb-2 text-sm">Kategoria:</label>
        <input type="text" id="edit-product-category" name="product-category" 
               value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>" 
               class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <button id="save-product-details" 
              class="py-3 px-6 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
        Zapisz zmiany
      </button>
    </div>
  </div>
</div>
