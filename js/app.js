// Możesz dodać dodatkowe funkcje inicjalizacyjne, jeżeli chcesz
// Inicjalizowanie funkcji z colorPicker.js
document.addEventListener('DOMContentLoaded', () => {
    // Przypisz zdarzenia do przycisków, np. dla przycisku "Dodaj kolor"
    document.getElementById('show-color-picker-btn').addEventListener('click', showColorPicker);
    document.getElementById('hide-color-picker-btn').addEventListener('click', hideColorPicker);
    document.getElementById('add-selected-colors-btn').addEventListener('click', addSelectedColors);
    document.getElementById('remove-selected-colors-btn').addEventListener('click', removeSelectedColor);


    // Inicjalizowanie innych akcji związanych z wyborem kolorów, jeżeli trzeba
    const colorItems = document.querySelectorAll('.color-item');
    colorItems.forEach(item => {
        item.onclick = function() {
            selectColor(item); // Wywołanie funkcji z colors.js
        };
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Pobierz wszystkie kafelki w gridzie
        const gridItems = document.querySelectorAll('.grid div');
    
        // Dodaj obsługę kliknięcia dla każdego kafelka
        gridItems.forEach(item => {
            item.addEventListener('click', () => setActive(item));
        });
    });
});
