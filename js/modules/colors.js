let selectedMaterial = null; // Przechowuje aktualnie zaznaczony materiał
let materialList = []; // Lista materiałów dodanych do głównego menu

// Pokaż panel wyboru materiałów
function showMaterialPicker() {
    const panel = document.getElementById('material-picker-panel');
    if (!panel) {
        console.error('Element #material-picker-panel nie został znaleziony.');
        return; // Wyjście z funkcji, jeśli element nie istnieje
    }
    panel.classList.remove('hidden');
}

// Schowaj panel wyboru materiałów
function hideMaterialPicker() {
    document.getElementById('material-picker-panel').classList.add('hidden');
}

// Aktualizacja zaznaczenia materiału
function updateSelection(checkbox) {
    const materialTile = checkbox.closest('label').querySelector('.material-tile');
    if (checkbox.checked) {
        materialTile.classList.add('border-blue-500');
    } else {
        materialTile.classList.remove('border-blue-500');
    }
}

// Dodaj zaznaczone materiały do głównego menu
function addSelectedMaterial() {
    const selectedCheckboxes = document.querySelectorAll('.material-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Wybierz co najmniej jeden materiał.');
        return;
    }

    selectedCheckboxes.forEach(checkbox => {
        const material = checkbox.value;
        if (!materialList.includes(material)) {
            materialList.push(material);

            const newMaterialDiv = document.createElement('div');
            newMaterialDiv.classList.add('w-20', 'h-20', 'rounded-lg', 'border-2', 'border-gray-300', 'flex', 'items-center', 'justify-center', 'cursor-pointer', 'material-item');
            newMaterialDiv.textContent = material;

            // Obsługuje kliknięcie na kafelek
            newMaterialDiv.onclick = () => highlightMaterial(newMaterialDiv, material);

            document.querySelector('.material-container').appendChild(newMaterialDiv);
        }
    });

    // Zresetuj wybór w panelu
    hideMaterialPicker();
    resetSelection();
}

// Zaznacz materiał w głównym menu
function highlightMaterial(element, material) {
    // Usuń podświetlenie ze wszystkich materiałów
    document.querySelectorAll('.material-item').forEach(item => {
        item.classList.remove('border-blue-500');
        item.classList.add('border-gray-300');
    });

    // Podświetl wybrany materiał
    element.classList.add('border-blue-500');
    element.classList.remove('border-gray-300');

    // Ustaw wybrany materiał
    selectedMaterial = material;
}

// Usuń zaznaczony materiał
function removeSelectedMaterial() {
    if (!selectedMaterial) {
        alert('Nie wybrano żadnego materiału do usunięcia.');
        return;
    }

    const materialContainer = document.querySelector('.material-container');

    // Usuń zaznaczony materiał z DOM
    const materialDivs = Array.from(materialContainer.children);
    const selectedMaterialDiv = materialDivs.find(child => child.classList.contains('border-blue-500'));

    if (selectedMaterialDiv) {
        materialList = materialList.filter(material => material !== selectedMaterial);
        materialContainer.removeChild(selectedMaterialDiv);
    }

    // Zresetuj zaznaczenie
    selectedMaterial = null;
}

// Resetowanie zaznaczeń w panelu
function resetSelection() {
    document.querySelectorAll('.material-checkbox').forEach(checkbox => checkbox.checked = false);
    document.querySelectorAll('.material-tile').forEach(tile => tile.classList.remove('border-blue-500'));
}
