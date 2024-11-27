function setActive(element) {
    // Usuwamy klasę "active" ze wszystkich kafelków
    document.querySelectorAll('.grid div').forEach(div => {
      div.classList.remove('active');
    });

    // Dodajemy klasę "active" do klikniętego elementu
    element.classList.add('active');
  }

