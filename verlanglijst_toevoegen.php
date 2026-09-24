<?php
session_start();

require 'db.php';

if (!isset($_SESSION['loggedInUser'])) {
    header('location: login.php');
    exit();
}
if (!isset($_POST['auto_ID'])) {
    die("Geen auto geselecteerd.");
}

$auto_id = $_POST['auto_ID'];

try {
    $stmt = $pdo->prepare("
        INSERT INTO verlanglijsten_gebruikers (gebruiker_ID, auto_ID)
        VALUES (:gebruiker_ID, :auto_ID)
    ");

    $stmt->execute([
        ':gebruiker_ID' => $_SESSION['loggedInUser'],
        ':auto_ID' => $auto_id
    ]);

    echo "Auto succesvol toegevoegd aan je verlanglijst!";
} catch (PDOException $e) {
    echo "Fout bij toevoegen: " . $e->getMessage();
}
header('location: profile.php');
exit();
?>
