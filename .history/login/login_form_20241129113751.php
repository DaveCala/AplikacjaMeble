<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz logowania</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-800 text-white flex justify-center items-center min-h-screen m-0">

    <!-- Wyświetlanie komunikatu o błędzie -->
    <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="bg-red-600 text-white py-2 px-4 rounded-lg mb-4">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); // Usuwamy komunikat po wyświetleniu ?>
    <?php endif; ?>

    <form action="login.php" method="POST" class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-sm">
        <div class="flex justify-center items-center">
            <img src="../img/logo_beautysofa_24_pionowe.png" style="width: 100px; height: 100px; object-fit: contain;" alt="Logo BeautySofa">
        </div>
        <h2 class="text-2xl text-center mb-6">Zaloguj się</h2>

        <label for="username" class="block text-sm mb-2">Login</label>
        <input type="text" id="username" name="username" placeholder="Imię i nazwisko"
            class="w-full p-3 mb-4 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <label for="password" class="block text-sm mb-2">Hasło</label>
        <input type="password" id="password" name="password" placeholder="Hasło"
            class="w-full p-3 mb-4 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <!-- Wyświetlanie komunikatu o błędzie -->
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="bg-red-600 text-white py-2 px-4 rounded-lg mb-4">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); // Usuwamy komunikat po wyświetleniu ?>
        <?php endif; ?>

        <button type="submit"
            class="w-full py-3 bg-gray-600 rounded-lg text-white text-lg hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Zaloguj się
        </button>
    </form>

</body>

</html>