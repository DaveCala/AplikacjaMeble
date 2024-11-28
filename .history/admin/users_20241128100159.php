<?php
require_once '../db.php'; // Łączenie z bazą danych

// Pobranie danych z AJAX-a
$data = json_decode(file_get_contents("php://input"), true);

// Walidacja danych
if (isset($data['id'], $data['username'], $data['password'], $data['role'])) {
    $id = $data['id'];
    $username = $data['username'];
    $password = $data['password'];
    $role = $data['role'];

    // Aktualizacja w bazie danych
    $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
    $success = $stmt->execute([
        'username' => $username,
        'password' => $password,
        'role' => $role,
        'id' => $id
    ]);

    // Zwracamy odpowiedź JSON
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
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
                        <th class="border border-gray-700 p-2">Password</th>
                        <th class="border border-gray-700 p-2">Role</th>
                        <th class="border border-gray-700 p-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="usersTable">
                    <?php foreach ($users as $user): ?>
                        <tr data-id="<?= htmlspecialchars($user['id']); ?>">
                        <td class="border border-gray-700 p-2"><?= htmlspecialchars($user['id']); ?></td>
                        <td class="border border-gray-700 p-2 username"><?= htmlspecialchars($user['username']); ?></td>
                        <td class="border border-gray-700 p-2 password"><?= htmlspecialchars($user['password']); ?></td>
                        <td class="border border-gray-700 p-2 role"><?= htmlspecialchars($user['role']); ?></td>
                        <td class="border border-gray-700 p-2">
                            <button onclick="editRow(this)" 
                                    class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
                            Edytuj
                            </button>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>

                <script>
                // Funkcja do edycji wiersza
                function editRow(button) {
                    const row = button.closest('tr');
                    const usernameCell = row.querySelector('.username');
                    const passwordCell = row.querySelector('.password');
                    const roleCell = row.querySelector('.role');
                    const actionsCell = row.querySelector('td:last-child');

                    const username = usernameCell.textContent;
                    const password = passwordCell.textContent;
                    const role = roleCell.textContent;

                    usernameCell.innerHTML = `<input type="text" value="${username}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
                    passwordCell.innerHTML = `<input type="text" value="${password}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
                    roleCell.innerHTML = `<input type="text" value="${role}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;

                    actionsCell.innerHTML = `
                    <button onclick="saveRow(this)" class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mr-2">Zapisz</button>
                    <button onclick="cancelEdit(this, '${username}', '${password}', '${role}')" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">Anuluj</button>
                    `;
                }

                function saveRow(button) {
                    // Kod jak wyżej
                }

                function cancelEdit(button, username, password, role) {
                    // Kod jak wyżej
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
