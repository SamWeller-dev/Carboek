<?php

require 'db.php';

session_start();
if (isset($_POST['opslaan'])) {
    $username = $_POST['username'];
    $wachtwoord = $_POST['wachtwoord'];

    $hashed = password_hash($wachtwoord, PASSWORD_DEFAULT);

    $query = "INSERT INTO gebruikers (username, wachtwoord) VALUES (:username, :wachtwoord)";
    $query_run = $pdo->prepare($query);
    $query_execute = $query_run->execute([
        ':username' => $username,
        ':wachtwoord' => $hashed
    ]);

    if ($query_execute) {
        header('Location: login.php');
        exit;
    } else {
        echo "Er is een fout opgetreden...";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="all">
        <h1>gebruiker toevoegen</h1>
        <?php if (!empty($message)): ?>
            <p class="error_message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="profile.php">
            <button>
                << Terug</button>
        </form>
        <form action="registreren.php" method="POST" enctype="multipart/form-data">
            <div class="merk row">
                <label for="merk">gebruikersnaam</label>
                <input type="text" name="username" id="username">
            </div>
            <div class="model row">
                <label for="model">wachtwoord</label>
                <input type="password" name="wachtwoord" id="wachtwoord">
            </div>
            <button name="opslaan" class="opslaan">aanmaken</button>
    </div>
    </form>
</body>

</html>