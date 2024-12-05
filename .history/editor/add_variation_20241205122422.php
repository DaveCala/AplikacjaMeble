document.getElementById('add-variation').addEventListener('submit', function (e) {
  e.preventDefault(); // Zapobiega przeładowaniu strony

  const form = e.target;
  const formData = new FormData(form);

  fetch('add_variation.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message || 'Wariacja została dodana pomyślnie.');
        form.reset();
        loadVariationList(); // Załaduj odświeżoną listę wariacji
      } else {
        alert('Błąd: ' + (data.message || 'Nie udało się dodać wariacji.'));
      }
    })
    .catch(error => {
      console.error('Błąd:', error);
      alert('Wystąpił błąd podczas dodawania wariacji.');
    });
});

// Funkcja do ładowania listy wariacji
function loadVariationList() {
  fetch('fetch_variations.php')
    .then(response => response.text())
    .then(html => {
      document.getElementById('variation-list').innerHTML = html;
    })
    .catch(error => console.error('Błąd podczas ładowania listy wariacji:', error));
}
