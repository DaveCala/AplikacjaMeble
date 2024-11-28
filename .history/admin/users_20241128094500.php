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
          <div class="bg-gray-900 w-full p-4 border border-gray-700 rounded-lg">
            <!-- Kontener użytkownika -->
            <div class="grid grid-cols-5 gap-4 items-center">
              
              <!-- ID użytkownika -->
              <div class="col-span-1 text-white text-sm">
                <p><?php echo htmlspecialchars($user['id']); ?></p>
              </div>

              <!-- Nazwa użytkownika -->
              <div class="col-span-2 text-white text-sm truncate">
                <p><?php echo htmlspecialchars($user['username']); ?></p>
              </div>

              <!-- Rola użytkownika -->
              <div class="col-span-1 text-white text-sm">
                <p><?php echo htmlspecialchars($user['role']); ?></p>
              </div>

              <!-- Przycisk akcji -->
              <div class="col-span-1 flex items-center justify-center">
                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white py-1 px-2 rounded-lg hover:bg-blue-600 text-sm">
                  Edytuj
                </a>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="text-gray-400">Brak użytkowników do wyświetlenia.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
