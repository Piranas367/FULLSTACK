<?php
require('indexConfig.php');

if (isset($_POST['toevoegen'])) {
    $ProductName  = $_POST['ProductName'];
    $InkoopPrijs  = $_POST['InkoopPrijs'];
    $type         = $_POST['type'];
    $VerkoopPrijs = $_POST['VerkoopPrijs'];
    $Fabriek      = $_POST['Fabriek'];

    $sql = "INSERT INTO artikel (naam, inkoopprijs, typeProduct, verkoopprijs, fabriek) VALUES (?, ?, ?, ?, ?)";
    $stmtinsert = $conn->prepare($sql);
    $stmtinsert->bind_param("sdsds", $ProductName, $InkoopPrijs, $type, $VerkoopPrijs, $Fabriek);

    if ($stmtinsert->execute()) {
        echo "Artikel is toegevoegd!";
    } else {
        echo "Er is iets mis gegaan";
    }
}

$products = [];
if (isset($_POST['productenZien'])) {
    $sql = "SELECT * FROM artikel";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    } else {
        $message = "Geen producten gevonden.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikelen toevoegen</title>
    <link rel="stylesheet" href="style.css" />
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
    <h1>Artikelen toevoegen</h1>
    <div class="login-container">
        <h1 class="h1">Voeg hier je artikel toe</h1>
        <form action="Artikelen toevoegen.php" method="post">
            <div class="input-group">
                <input type="text" id="ProductName" name="ProductName" required>
                <label for="ProductName">Product naam</label>
            </div>
            <div class="input-group">
                <input type="decimal" id="InkoopPrijs" name="InkoopPrijs">
                <label for="InkoopPrijs">InkoopPrijs</label>
            </div>
            <div class="input-group">
                <input type="text" id="type" name="type" required>
                <label for="type">Type</label>
            </div>
            <div class="input-group">
                <input type="decimal" id="VerkoopPrijs" name="VerkoopPrijs">
                <label for="VerkoopPrijs">Verkoop Prijs</label>
            </div>
            <div class="input-group">
                <input type="text" id="Fabriek" name="Fabriek" required>
                <label for="Fabriek">Fabriek</label>
            </div>
            <button type="submit" name="toevoegen" value="">Voeg artikel toe</button>
        </form>

        <form action="Artikelen toevoegen.php" method="post">
            <button type="submit" name="productenZien" value="">Producten voorraad zien</button>
        </form>

        <?php
        if (isset($message)) {
            echo '<p>' . $message . '</p>';
        }
        if (!empty($products)) {
            echo '<table>';
            echo '<tr><th>idArtikel</th><th>Product Naam</th><th>InkoopPrijs</th><th>Type</th><th>Verkoop Prijs</th><th>Fabriek</th></tr>';
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . $product["idartikel"] . '</td>';
                echo '<td>' . $product["naam"] . '</td>';
                echo '<td>' . $product["inkoopprijs"] . '</td>';
                echo '<td>' . $product["typeProduct"] . '</td>';
                echo '<td>' . $product["verkoopprijs"] . '</td>';
                echo '<td>' . $product["fabriek"] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </div>
</body>
</html>




