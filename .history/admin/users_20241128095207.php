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
          
        <?php endforeach; ?>
      <?php else : ?>
        <p class="text-gray-400">Brak użytkowników do wyświetlenia.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
