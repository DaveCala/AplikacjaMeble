<?php
session_start();
require_once '../db.php'; // Połączenie z bazą danych

// Pobranie użytkowników z bazy
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zarządzaj użytkownikami</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-800 text-white">

  <div class="container mx-auto p-4">
    <h1 class="text-2xl mb-4">Zarządzaj użytkownikami</h1>
    
    <!-- Grid z użytkownikami -->
    <div class="space-y-4">
      <?php if (!empty($users)) : ?>
        <?php foreach ($users as $user) : ?>
            <div class="w-full bg-gray-900 p-4">
  <table class="w-full text-white">
    <thead>
      <tr>
        <th class="border border-gray-700 p-2">ID</th>
        <th class="border border-gray-700 p-2">Username</th>
        <th class="border border-gray-700 p-2">Role</th>
        <th class="border border-gray-700 p-2">Actions</th>
      </tr>
    </thead>
    <tbody id="usersTable">
      <!-- Przykładowy wiersz -->
      <tr data-id="1">
        <td class="border border-gray-700 p-2">1</td>
        <td class="border border-gray-700 p-2 username">dawid</td>
        <td class="border border-gray-700 p-2 role">editor</td>
        <td class="border border-gray-700 p-2">
          <button onclick="editRow(this)" 
                  class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
            Edytuj
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script>
  function editRow(button) {
    const row = button.closest('tr'); // Pobiera aktualny wiersz
    const usernameCell = row.querySelector('.username');
    const roleCell = row.querySelector('.role');
    const actionsCell = row.querySelector('td:last-child');

    // Zmieniamy komórki na pola tekstowe
    const username = usernameCell.textContent;
    const role = roleCell.textContent;

    usernameCell.innerHTML = `<input type="text" value="${username}" 
                              class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
    roleCell.innerHTML = `<input type="text" value="${role}" 
                           class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;

    // Tworzymy przyciski "Zapisz" i "Anuluj"
    actionsCell.innerHTML = `
      <button onclick="saveRow(this)" 
              class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mr-2">
        Zapisz
      </button>
      <button onclick="cancelEdit(this, '${username}', '${role}')" 
              class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">
        Anuluj
      </button>
    `;
  }

  function saveRow(button) {
    const row = button.closest('tr');
    const id = row.dataset.id; // Pobiera ID użytkownika z atrybutu data-id
    const usernameInput = row.querySelector('.username input');
    const roleInput = row.querySelector('.role input');

    const updatedUsername = usernameInput.value;
    const updatedRole = roleInput.value;

    // AJAX do zapisania zmian w bazie danych (przykład)
    fetch('update_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, username: updatedUsername, role: updatedRole })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Zmiana pola na nową wartość
          row.querySelector('.username').textContent = updatedUsername;
          row.querySelector('.role').textContent = updatedRole;
          row.querySelector('td:last-child').innerHTML = `
            <button onclick="editRow(this)" 
                    class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
              Edytuj
            </button>
          `;
        } else {
          alert('Błąd podczas zapisywania zmian.');
        }
      });
  }

  function cancelEdit(button, originalUsername, originalRole) {
    const row = button.closest('tr');
    row.querySelector('.username').textContent = originalUsername;
    row.querySelector('.role').textContent = originalRole;

    row.querySelector('td:last-child').innerHTML = `
      <button onclick="editRow(this)" 
              class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
        Edytuj
      </button>
    `;
  }
</script>

        <?php endforeach; ?>
      <?php else : ?>
        <p class="text-gray-400">Brak użytkowników do wyświetlenia.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
