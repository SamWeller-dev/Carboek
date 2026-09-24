<?php

session_start();
require 'db.php';

if (!isset($_SESSION['loggedInUser'])) {
    header('location: login.php');
    exit();
}

// Zoekfunctionaliteit
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($searchTerm) {
    $queryAuto = $pdo->prepare("
        SELECT merk, logo_url, id
        FROM auto_merk
        WHERE merk LIKE :search
        ORDER BY merk
    ");
    $queryAuto->bindValue(':search', '%' . $searchTerm . '%');
    $queryAuto->execute();
} else {
    $queryAuto = $pdo->prepare("
        SELECT merk, logo_url, id
        FROM auto_merk
        ORDER BY merk
    ");
    $queryAuto->execute();
}
$row = $queryAuto->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="all">

        <div class="nav-bar">
            <h3><a href="home.php">Home page</a></h3>
            <h3><a href="search.php">Search</a></h3>
            <h3><a href="profile.php">profile</a></h3>
        </div>

        <h1>Search</h1>
        <form class="zoeken" method="GET" action="">
            <input class="search" type="text" name="search" id="search" placeholder="Zoeken" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Zoek</button>
        </form>

        <div class="text">
            <p></p>
            <div class="merken">
                <?php
                if (!empty($row)) {
                    foreach ($row as $merk) {
                        echo '<td><a href="detail_merk.php?id=' . urlencode($merk['id']) . '">
                  <img src="' . htmlspecialchars($merk['logo_url']) . '" alt="' . htmlspecialchars($merk['merk']) . '">
                  ' . htmlspecialchars($merk['merk']) . '</a></td>';
                    }
                } else {
                    echo '<p>Geen resultaten gevonden.</p>';
                }
                ?>
            </div>
        </div>

    </div>
</body>

</html>