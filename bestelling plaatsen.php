<?php
require('indexConfig.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Toevoegen'])) {

    // Get the posted data
    $locatie = $_POST['Locatie']; 
    $aantal = $_POST['aantal'];  
    $idartikel = $_POST['idartikel'];

    $checkLocation = $conn->prepare("SELECT COUNT(*) FROM Vestigingen WHERE idVestigingen = ?");
    $checkLocation->bind_param("i", $locatie);
    $checkLocation->execute();
    $checkLocation->bind_result($count);
    $checkLocation->fetch();
    $checkLocation->close();

    if ($count > 0) {
        $sql = "INSERT INTO bestellijst (idVestigingen, `aantal besteld`) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $locatie, $aantal); 
        $stmt->execute();
        $stmt->close();

        $sql = "INSERT INTO Bestelling (idartikel) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idartikel); 
        $stmt->execute();    
        $stmt->close();



        header("location: bestellingen.php");
    } else {
        echo "Bestelling plaatsen mislukt!";
    }
}

// Fetch data for the form
$idArtikelen = $conn->query("SELECT idartikel, naam FROM artikel");
$locaties = $conn->query("SELECT idvestigingen, naam FROM Vestigingen");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellingen plaatsen</title>
    <link rel="stylesheet" href="bestelling plaatsen.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="../CRUD PHP/homepagina.php"><img src="home logo.webp" alt="Home" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/inlogpagina.php"><img src="uitlog logo.webp" alt="Logout" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/instellingen.php"><img src="instellingen.webp" alt="Settings" class="logo-img"></a></li>
        </ul>
    </nav>

    <!-- Slide Option Menu -->
    <div class="option-menu">
        <button class="menu-button">Opties</button>
        <div class="slide-menu">
            <ul>
                <li><a href="bestelling aanpassen.php">Bestellingen aanpassen</a></li>
                <li><a href="bestellingen.php">Bestelling overzicht</a></li>
            </ul>
        </div>
    </div>
</header>

<h1>Bestellingen Toevoegen</h1>
<form action="bestelling plaatsen.php" method="POST">
    <table>
        <tr>
            <td>Welke product wil je toevoegen</td>
            <td>
                <select name="idartikel" required>
                    <?php
                    if ($idArtikelen->num_rows > 0) {
                        while ($row = $idArtikelen->fetch_assoc()) {
                            echo '<option value="' . $row['idartikel'] . '">' . $row['naam'] . '</option>';
                        }
                    } else {
                        echo '<option value="">Geen artikelen beschikbaar</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Voor welke vestiging?</td>
            <td>
                <select name="Locatie" required>
                    <?php
                    if ($locaties->num_rows > 0) {
                        while ($row = $locaties->fetch_assoc()) {
                            echo '<option value="' . $row['idvestigingen'] . '">' . $row['naam'] . '</option>';
                        }
                    } else {
                        echo '<option value="">Geen locaties beschikbaar</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Hoeveel wil je er toevoegen</td>
            <td>
                <input type="number" name="aantal">
            </td>
        </tr>
        <tr>
        <td>status levering</td>
            <td>
                <input type="number" name="aantal">
            </td>
        </tr>

        <tr>
            <td colspan="2"><button type="submit" name="Toevoegen">Bestelling Toevoegen</button></td>
        </tr>
    </table>
</form>

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

</body>
</html>

