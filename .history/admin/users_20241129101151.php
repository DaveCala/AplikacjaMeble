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
            <a href="../editor/editor_index.php" class="text-white hover:text-gray-300">Powrót</a>
        </div>
    </nav>

    <button type="button" onclick="openAddUserModal()" 
    class="w-full py-3 bg-blue-600 rounded-lg text-white text-lg hover:bg-blue-500 mt-4">
    Dodaj użytkownika
</button>

<!-- Modal do dodawania użytkownika -->
<div id="addUserModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-90 flex justify-center items-center">
    <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl text-center mb-4">Dodaj nowego użytkownika</h2>

        <form id="addUserForm">
            <label for="newUsername" class="block text-sm mb-2">Nazwa użytkownika</label>
            <input type="text" id="newUsername" name="newUsername" placeholder="Nazwa użytkownika"
                class="w-full p-3 mb-4 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>

            <label for="newPassword" class="block text-sm mb-2">Hasło</label>
            <input type="password" id="newPassword" name="newPassword" placeholder="Hasło"
                class="w-full p-3 mb-4 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>

            <label for="newRole" class="block text-sm mb-2">Rola</label>
            <select id="newRole" name="newRole" 
                class="w-full p-3 mb-4 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </select>

            <button type="submit" 
                class="w-full py-3 bg-green-600 rounded-lg text-white text-lg hover:bg-green-500">
                Dodaj użytkownika
            </button>
        </form>

        <button onclick="closeAddUserModal()" 
            class="w-full py-3 bg-red-600 rounded-lg text-white text-lg hover:bg-red-500 mt-4">
            Anuluj
        </button>
    </div>
</div>


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
                        <td class="border border-gray-700 p-2 password">********</td>
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
            const roleCell = row.querySelector('.role');
            const actionsCell = row.querySelector('td:last-child');

            const username = usernameCell.textContent.trim();
            const role = roleCell.textContent.trim();

            usernameCell.innerHTML = `<input type="text" value="${username}" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;

            // Opcje do wyboru dla pola roli
            const roles = ['editor', 'viewer', 'admin'];
            let roleOptions = roles.map(r => `<option value="${r}" ${r === role ? 'selected' : ''}>${r}</option>`).join('');
            roleCell.innerHTML = `<select class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">${roleOptions}</select>`;

            actionsCell.innerHTML = `
                <button onclick="saveRow(this)" class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mr-2">Zapisz</button>
            `;

            // Dodaj pole do zmiany hasła (opcjonalne)
            const passwordCell = row.querySelector('.password');
            passwordCell.innerHTML = `<input type="password" placeholder="Nowe hasło (opcjonalne)" class="w-full bg-gray-800 text-green-500 border border-green-500 p-2 rounded">`;
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
                    row.querySelector('.password').textContent = '********'; // Ukrywamy hasło
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

        function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    document.getElementById('addUserForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(event.target);

        fetch('add_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Użytkownik został dodany!');
                closeAddUserModal();
            } else {
                alert('Błąd: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Błąd:', error);
            alert('Wystąpił błąd przy dodawaniu użytkownika.');
        });
    });


    </script>
</body>
</html>
