<?php
require('indexConfig.php');


if (isset($_POST['Toevoegen'])){
    $locatie = $_POST['LocatieToevoegen'];
    
    $sql = "INSERT INTO Vestigingen (naam) VALUES (?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $locatie);

    if($stmt->execute()){
        echo"Product toegevoegd!";
    } else {
        echo "Er is iets mis gegaan";
    }
}

?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Voorraad per locatie</title>
        <link rel="stylesheet" href="Locatie voorraad.css">
    </head>
    <header>
    <nav>
        <ul>
            <li><a href="../CRUD PHP/homepagina.php"><img src="home logo.webp" alt="Account" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/inlogpagina.php"><img src="uitlog logo.webp" alt="Account" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/instellingen.php"><img src="instellingen.webp" alt="Account" class="logo-img"></a></li>
        </ul>
    </nav>

    <!-- Slide Option Menu -->
    <div class="option-menu">
        <button class="menu-button">Opties</button>
        <div class="slide-menu">
            <ul>
                <li><a href="bestelling aanpassen.php">bestellingen aanpassen</a></li>
                <li><a href="bestellingen.php">Bestelling overzicht</a></li>
            </ul>
        </div>
    </div>
</header>
    <script>
        const menuButton = document.querySelector('.menu-button');
        const slideMenu = document.querySelector('.slide-menu');

        // Toggle the slide menu visibility on button click
        menuButton.addEventListener('click', () => {
            slideMenu.classList.toggle('show');
        });
        window.addEventListener('click', (event) => {
            if (!menuButton.contains(event.target) && !slideMenu.contains(event.target)) {
                slideMenu.classList.remove('show');
            }
        });
    </script>
    <body>
    <h1>Locatie toevoegen</h1>
    <div class="login-container">
        <h1 class="h1">Voeg hier je locatie toe</h1>
        <form action="Locatie voorraad.php" method="post">
            <div class="input-group">
                <input type="text" id="LocatieToevoegen" name="LocatieToevoegen" required>
                <label for="LocatieToevoegen">Voer hier een nieuwe locatie</label>
            </div>
            <button type="submit" name="Toevoegen" value="">Voeg nieuwe locatie toe</button>
</form>

</body>
</html>