<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
</head>
<body>
    <form action="login.php" method="POST">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required>
        
        <?php if (isset($_GET['error'])): ?>
            <p style="color:red;">Błędne dane logowania.</p>
        <?php endif; ?>
        
        <button type="submit">Zaloguj się</button>
    </form>
</body>
</html>

