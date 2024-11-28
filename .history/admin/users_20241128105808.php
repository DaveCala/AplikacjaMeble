<?php
require_once '../db.php'; // Połączenie z bazą danych

// Pobranie wszystkich użytkowników
$stmt = $pdo->query("SELECT id, username, password, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie użytkownikami</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-800 text-white">
    <!-- Navbar -->
    <nav class="bg-gray-900 navbar w-full py-6 text-white flex justify-between items-center">
        <div class="flex items-center ml-6">
            <img src="../img/logo_beautysofa_24_pionowe.png" class="w-10 h-10 mr-3" alt="Logo">
            <div class="text-xl">Panel zarządzania użytkownikami</div>
        </div>
        <div class="mr-6">
            <a href="editor_index.php" class="text-white hover:text-gray-300">Powrót</a>
        </div>
    </nav>

    <!-- Tabela użytkowników -->
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

            const username = usernameCell.textContent.trim();
            const password = passwordCell.textContent.trim();
            const role = roleCell.textContent.trim();

            usernameCell.innerHTML = `<input type="text" value="${username}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
            passwordCell.innerHTML = `<input type="text" value="${password}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
            
            // Opcje do wyboru dla pola roli
            const roles = ['editor', 'viewer', 'admin'];
            let roleOptions = roles.map(r => `<option value="${r}" ${r === role ? 'selected' : ''}>${r}</option>`).join('');
            roleCell.innerHTML = `<select class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">${roleOptions}</select>`;

            actionsCell.innerHTML = `
                <button onclick="saveRow(this)" class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mr-2">Zapisz</button>
                <button onclick="cancelEdit(this, '${username}', '${password}', '${role}')" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">Anuluj</button>
            `;
        }


        // Funkcja do zapisywania zmian
        function saveRow(button) {
            const row = button.closest('tr');
            const userId = row.getAttribute('data-id'); // Pobieramy ID użytkownika
            const usernameInput = row.querySelector('.username input').value.trim();
            const passwordInput = row.querySelector('.password input').value.trim();
            const roleSelect = row.querySelector('.role select').value;

            // Dane do wysłania
            const updatedUser = {
                id: userId,
                username: usernameInput,
                password: passwordInput,
                role: roleSelect,
            };

            // Wyślij dane do serwera
            fetch('update_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updatedUser),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aktualizujemy widok
                    row.querySelector('.username').textContent = usernameInput;
                    row.querySelector('.password').textContent = passwordInput;
                    row.querySelector('.role').textContent = roleSelect;

                    const actionsCell = row.querySelector('td:last-child');
                    actionsCell.innerHTML = `
                        <button onclick="editRow(this)" 
                                class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
                            Edytuj
                        </button>
                    `;
                } else {
                    alert('Wystąpił błąd podczas zapisywania: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                alert('Wystąpił błąd podczas zapisywania zmian.');
            });
        }

    </script>
</body>
</html>
