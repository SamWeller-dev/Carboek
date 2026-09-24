<?php

session_start();

require 'db.php';

if (!isset($_SESSION['loggedInUser'])) {
    header('location: login.php');
    exit();
}

// Handle remove from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_auto'])) {
    $auto_id = (int) $_POST['auto_id'];
    $delete_stmt = $pdo->prepare("DELETE FROM verlanglijsten_gebruikers WHERE gebruiker_ID = :user_id AND auto_ID = :auto_id");
    $delete_stmt->execute(['user_id' => $_SESSION['loggedInUser'], 'auto_id' => $auto_id]);
    // Redirect to avoid resubmission
    header('Location: profile.php');
    exit();
}

$query_vl = $pdo->prepare("SELECT * FROM verlanglijsten_gebruikers WHERE gebruiker_ID = :id");
$query_vl->execute(['id' => $_SESSION['loggedInUser']]);
$data = $query_vl->fetchAll();
if (empty($data)) {
    $message = "Nog niks gevonden? Voeg auto's toe aan je verlanglijst!";
} else {
    $message = count($data);
}

$query = $pdo->prepare("SELECT * FROM gebruikers WHERE id = :id");
$query->execute(['id' => $_SESSION['loggedInUser']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$query2 = $pdo->prepare("SELECT verlanglijsten_gebruikers.id AS wishlist_id, auto_model.id AS auto_id, auto_model.foto_auto, auto_model.merk_id, auto_merk.merk, auto_model.model FROM verlanglijsten_gebruikers
    JOIN auto_model ON verlanglijsten_gebruikers.auto_ID = auto_model.id
    JOIN auto_merk ON auto_model.merk_id = auto_merk.id
    WHERE verlanglijsten_gebruikers.gebruiker_ID = :user_id");
$query2->execute(['user_id' => $_SESSION['loggedInUser']]);
$wishlist = $query2->fetchAll(PDO::FETCH_ASSOC);

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

        <h1>Welkom op je profile
            <?php
            echo ($user['username']);
            ?>!</h1>

        <a class="een" href="add.php">+ auto toevoegen +</a>

        <div class="text">

        </div>


    </div>
    <form action="logout.php" method="POST">
        <button type="submit" name="submit" id="submit" class="log">uitloggen?</button>
    </form>
    <div class="verlanglijst">
        <h1>
            verlanglijst:
        </h1>
        <?php if (empty($wishlist)): ?>
            <h1>Nog niks gevonden? Voeg auto's toe aan je verlanglijst!</h1>
        <?php else: ?>
            <?php foreach ($wishlist as $auto): ?>
                <div class="container">
                    <img class="autoMerk" src="<?= ($auto['foto_auto']) ?>" alt="">
                    <p><?= ($auto['merk']) ?> - <?= ($auto['model']) ?></p>
                    <a href="detail_model.php?id=<?= ($auto['auto_id']) ?>" class="detail-btn">Bekijk auto</a>
                    <form action="profile.php" method="POST" style="display: inline;">
                        <input type="hidden" name="auto_id" value="<?= $auto['auto_id'] ?>">
                        <button type="submit" name="remove_auto" class="remove-btn">Verwijder uit verlanglijst</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>