AutoVerzamel - Webapplicatie Auto's Beheren

voor dat je verder gaat moet je insert.sgl uitvoeren in phpmyadmin.
vervang de database met jouw gegevens

Webapplicatie voor autoliefhebbers om auto merken en modellen op te slaan en te beheren.
Technologieën: PHP, SQL (PDO), HTML, CSS.


Pagina's Overzicht

login.php - Inlog met username/wachtwoord. Database validatie. Foutmelding: "gebruikersnaam/wachtwoord combinatie onjuist". Gebruikt: password_verify(), prepared statements, sessions.

registreren.php - Registratie. Wachtwoord wordt gehasht met password_hash(PASSWORD_DEFAULT). Redirect naar login.

logout.php - Vernieuwt sessie met session_destroy(). Redirect naar login.

home.php - Toon alle auto modellen. Controleert inlog status met isset($_SESSION).

search.php - Zoek merken met LIKE query van $_GET. Toont "Geen resultaten" als niets gevonden. Prepared statements voorkomen SQL injection.

detail_merk.php - Toon modellen per merk via $_GET['id']. JOIN query.

detail_model.php - Toon auto details (foto, merk, model, specs, waarde, kenteken).

profile.php - Gebruikersprofiel met verlanglijst. DELETE functie voor verwijderen. Toont "Nog niks gevonden?" als leeg. POST Redirect Get patroon. Gebruikt: COUNT, JOIN queries.

add.php - 8 invoelvelden: Merk (dropdown), Model, Foto (file upload), Optrekken, Jaar, Tank, Waarde, Kenteken. Insert in database.


Foutafhandeling add.php (Kern van Project)

Foutmeldingen verschijnen NAAST knop in rood (inline), formulier blijft zichtbaar en ingevuld.

1. Geen foto geupload - "Geen foto geupload."
2. Ongeldig bestandstype - "Ongeldig bestandstype. Alleen JPG, PNG, en GIF"
3. Upload fout - "Fout bij het uploaden van de foto."
4. Database fout - "Er is een fout opgetreden bij het opslaan"

Code:
```php
$error = '';
if (isset($_POST['opslaan'])) {
    if (!isset($_FILES['foto_auto']) || $_FILES['foto_auto']['error']) {
        $error = "Geen foto geupload.";
    }
    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        $error = "Ongeldig bestandstype.";
    }
    if (empty($error)) {
        move_uploaded_file($tmp, "images/$nama");
        $pdo->prepare("INSERT INTO...")->execute($data);
    }
}
<?php if (!empty($error)): ?>
    <span class="error-message"><?php echo $error; ?></span>
<?php endif; ?>
```

Foto handling: $_FILES, pathinfo(), uniqid(), move_uploaded_file() naar /images.


Beveiliging Maatregelen

- Passwords: password_hash(PASSWORD_DEFAULT) + password_verify() (bcrypt)
- SQL Injection: Prepared statements + bindValue()
- XSS: htmlspecialchars() bij output
- File Upload: Extensie whitelist (jpg, png, gif), uniqid() unique names
- Sessions: isset($_SESSION) controle, session_destroy() logout
- Input: trim() whitespace, isset() checks


Database Schema (insert.sql)

gebruikers - id (PK), username (UNIQUE), wachtwoord (hashed)

auto_merk (41 records) - id (PK), merk, logo_url

auto_model (41 records) - id (PK), foto_auto, merk_id (FK), model, optrekken_seconde, jaar, tank (Benzine/Diesel/Elektrisch/Hybride), waarde, kenteken

verlanglijsten_gebruikers - id (PK), gebruiker_ID (FK), auto_ID (FK)


Gebruikte PHP Methodes

Alle pagina's: require (db.php), session_start(), isset($_SESSION['loggedInUser']), header() redirects, htmlspecialchars()

Login: $_SERVER['REQUEST_METHOD'], trim(), password_verify(), $pdo->prepare(), fetch()

Registratie: password_hash(PASSWORD_DEFAULT), execute()

Search: $_GET['search'], LIKE '%..%', bindValue(), fetchAll()

Profile: $_POST['remove_auto'], DELETE query, COUNT(), JOIN

Add: $_FILES['foto_auto'], pathinfo(), move_uploaded_file(), uniqid(), in_array(), INSERT


Testing & Gebruik

1. Registreer op /registreren.php
2. Login met credentials
3. Browse auto's (Home/Search)
4. Klik "+Auto toevoegen" in Profile
5. Vul 8 velden in
6. Upload foto (jpg/png/gif)
7. Klik Opslaan
8. Test fout: Opslaan zonder foto -> "Geen foto geupload." naast knop
9. Upload foto en opslaan weer -> auto verschijnt in verlanglijst
10. Logout test -> session cleared, redirect login
