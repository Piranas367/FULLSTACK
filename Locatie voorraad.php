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

<?php
    require('indexConfig.php');

    //voegt nieuwe locatie toe
    if (isset($_POST['Toevoegen'])) {
        $locatie = $_POST['LocatieToevoegen'];

        // Checkt of er dezelfde locatie bestaat
        $check_sql = "SELECT * FROM Vestigingen WHERE naam = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $locatie);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Deze locatie is al toegevoegd!";
        } else {
            $sql = "INSERT INTO Vestigingen (naam) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $locatie);

            if($stmt->execute()){
                echo "Locatie toegevoegd!";
            } else {
                echo "Er is iets mis gegaan.";
            }
        }
    }

// verwijder functie
if (isset($_POST['Verwijderen'])) {
    $locatie = $_POST['locatieNaam'];

    // Checkt dat er een iets aan die locatie is toegevoegd zoals een artikel voorraad
    $check_sql = "SELECT COUNT(*) as count FROM voorraad WHERE idVestigingen = (SELECT idVestigingen FROM Vestigingen WHERE naam = ?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $locatie);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    // dit kijkt of er een voorraad in de locatie zit, geeft het een foutmelding
    if ($row['count'] > 0) {
        echo "Kan deze locatie niet verwijderen omdat er artikelen aan zijn gekoppeld.";
    } else {
        // als er geen verdere dingen in de locatie zit, verwijderd hij de locatie
        $sql = "DELETE FROM Vestigingen WHERE naam = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $locatie);

        if ($stmt->execute()) {
            echo "Locatie verwijderd!";
        } else {
            echo "Er is iets mis gegaan.";
        }
    }
}


    // zorgt ervoor dat alle locaties te zien krijgt
    $products = [];
    if (isset($_POST['locatieZien'])) {
        $sql = "SELECT * FROM Vestigingen";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            $message = "Geen locatie's toegevoegd.";
        }
    }
?>

<body>
<h1>Locatie toevoegen</h1>
<div class="form-table-container">
<div class="form-section">
  <h1 class="h1">Voeg hier je locatie toe</h1>
  <form action="Locatie voorraad.php" method="post">
    <div class="input-group">
      <input type="text" id="LocatieToevoegen" name="LocatieToevoegen" placeholder="Voer hier een nieuwe locatie in" required>
    </div>
    <button type="submit" name="Toevoegen" value="">Voeg nieuwe locatie toe</button>
  </form>
  <form action="Locatie voorraad.php" method="post">
    <button type="submit" name="locatieZien" value="">Laat alle locatie's zien</button>
  </form>    
</div>

<div class="table-section">
        <?php
        //laat met de locatieZien functie de locatie zien met een table
        if (isset($message)) {
            echo '<p>' . $message . '</p>';
        }
        if (!empty($products)) {
            echo '<table>';
            echo '<tr><th>Locatie</th><th>Actie</th></tr>';
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . $product["naam"] . '</td>';
                echo '<td>';
                echo '<form method="POST" action="Locatie voorraad.php">';
                echo '<input type="hidden" name="locatieNaam" value="' . $product["naam"] . '">';
                echo '<button type="submit" name="Verwijderen">Verwijder</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>  
</div>
</body>
</html>
