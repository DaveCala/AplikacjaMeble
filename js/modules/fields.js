const editFieldsBtn = document.getElementById('edit-fields-btn');
const fieldsPanel = document.getElementById('fields-panel');
const saveFieldsBtn = document.getElementById('save-fields-btn');
const cancelFieldsBtn = document.getElementById('cancel-fields-btn');
const infoFields = document.getElementById('info-fields');

// Pokaż/ukryj panel
editFieldsBtn.addEventListener('click', () => {
    fieldsPanel.classList.toggle('hidden');
    if (!fieldsPanel.classList.contains('hidden')) {
        fieldsPanel.scrollIntoView({ behavior: 'smooth' }); // Przewiń do panelu
    }
});

// Anuluj zmiany
cancelFieldsBtn.addEventListener('click', () => {
    fieldsPanel.classList.add('hidden');
});

// Zapisz wybrane pola
saveFieldsBtn.addEventListener('click', () => {
    const checkboxes = fieldsPanel.querySelectorAll('input[type="checkbox"]');
    const selectedFields = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

    // Usuń wszystkie dodatkowe pola przed dodaniem nowych
    const existingFields = Array.from(infoFields.children).filter(field => !field.querySelector('label[for="title"]') && !field.querySelector('label[for="ean"]'));
    existingFields.forEach(field => infoFields.removeChild(field));

    // Dodaj nowe pola
    selectedFields.forEach(field => {
        const newField = document.createElement('div');
        newField.classList.add('field');
        newField.innerHTML = `
          <label for="${field.toLowerCase()}" class="text-lg font-medium">${field}:</label>
          <input id="${field.toLowerCase()}" type="text" class="bg-gray-700 text-white w-full p-2 rounded-lg" placeholder="Wprowadź ${field.toLowerCase()}">
        `;
        infoFields.appendChild(newField);
    });

    // Ukryj panel po zapisaniu
    fieldsPanel.classList.add('hidden');
});
