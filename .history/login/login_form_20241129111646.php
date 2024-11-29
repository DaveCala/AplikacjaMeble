<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 flex items-center justify-center min-h-screen">

    <div class="bg-gray-900 p-6 rounded-lg w-full max-w-md">
        <h1 class="text-white text-2xl mb-4 text-center">Logowanie</h1>

        <!-- Wyświetlanie komunikatu o błędzie -->
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="bg-red-600 text-white py-2 px-4 rounded-lg mb-4">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); // Usuwamy komunikat po wyświetleniu ?>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="mb-4">
                <label for="username" class="block text-white">Nazwa użytkownika:</label>
                <input type="text" name="username" id="username" class="w-full py-2 px-4 rounded-lg bg-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-white">Hasło:</label>
                <input type="password" name="password" id="password" class="w-full py-2 px-4 rounded-lg bg-gray-700 text-white">
            </div>
            <button type="submit" class="w-full bg-blue-500 py-2 px-4 rounded-lg text-white hover:bg-blue-600">Zaloguj się</button>
        </form>
    </div>

</body>
</html>
