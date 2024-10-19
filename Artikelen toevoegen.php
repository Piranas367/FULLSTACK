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

<?php
    require('indexConfig.php');

    // Adding an article
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
            echo "Er is iets mis gegaan bij het toevoegen.";
        }
    }

    // Delete an article
    if (isset($_POST['verwijderen'])) {
        $delete_id = $_POST['delete_id'];

        // Checkt of de artikel gekoppelt is aan de voorraad en of Bestelling 
        $check_voorraad_sql = "SELECT COUNT(*) as count FROM voorraad WHERE idartikel = ?";
        $check_bestelling_sql = "SELECT COUNT(*) as count FROM Bestelling WHERE idartikel = ?";

        $stmt_voorraad = $conn->prepare($check_voorraad_sql);
        $stmt_voorraad->bind_param("i", $delete_id);
        $stmt_voorraad->execute();
        $result_voorraad = $stmt_voorraad->get_result();
        $row_voorraad = $result_voorraad->fetch_assoc();

        $stmt_bestelling = $conn->prepare($check_bestelling_sql);
        $stmt_bestelling->bind_param("i", $delete_id);
        $stmt_bestelling->execute();
        $result_bestelling = $stmt_bestelling->get_result();
        $row_bestelling = $result_bestelling->fetch_assoc();

        $stmt_voorraad->close();
        $stmt_bestelling->close();

        // Als de artikel in 1 van de andere tabellen staat, blokeert hij de verwijder functie
        if ($row_voorraad['count'] > 0 || $row_bestelling['count'] > 0) {
            echo "Kan dit artikel niet verwijderen omdat het nog steeds in voorraad of bestellingen wordt gebruikt.";
        } else {
            // verwijderd de artikel als er niet in een andere tabel staat
            $sql_delete = "DELETE FROM artikel WHERE idartikel = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $delete_id);

            if ($stmt_delete->execute()) {
                echo "Artikel succesvol verwijderd!";
            } else {
                echo "Er is iets mis gegaan bij het verwijderen van het artikel.";
            }

            $stmt_delete->close();
        }
    }

    // Laat alle producten zien
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

<body>
    <h1>Artikelen toevoegen</h1>
    <div class="form-table-container">
        <!-- Formulier sectie -->
        <div class="form-section">
            <h1 class="h1">Voeg hier je artikel toe</h1>
            <form action="Artikelen toevoegen.php" method="post">
                <div class="input-group">
                    <input type="text" id="ProductName" name="ProductName" required>
                    <label for="Product naam">Product naam</label>
                </div>
                <div class="input-group">
                    <input type="text" id="InkoopPrijs" name="InkoopPrijs" step="any">
                    <label for="InkoopPrijs">Inkoop prijs</label>
                </div>
                <div class="input-group">
                    <input type="text" id="type" name="type" required>
                    <label for="type">Type</label>
                </div>
                <div class="input-group">
                    <input type="text" id="VerkoopPrijs" name="VerkoopPrijs" step="any">
                    <label for="VerkoopPrijs">Verkoop Prijs</label>
                </div>
                <div class="input-group">
                    <input type="text" id="Fabriek" name="Fabriek" required>
                    <label for="Fabriek">Fabriek</label>
                </div>
                <button type="submit" name="toevoegen">Voeg artikel toe</button>
            </form>

            <form action="Artikelen toevoegen.php" method="post">
                <button type="submit" name="productenZien">Producten voorraad zien</button>
            </form>
        </div>

        <!-- Tabel sectie -->
        <div class="table-section">
            <?php
            if (isset($message)) {
                echo '<p>' . $message . '</p>';
            }
            if (!empty($products)) {
                echo '<table>';
                echo '<tr><th>idArtikel</th><th>Product Naam</th><th>InkoopPrijs</th><th>Type</th><th>Verkoop Prijs</th><th>Fabriek</th><th>Actie</th></tr>';
                foreach ($products as $product) {
                    echo '<tr>';
                    echo '<td>' . $product["idartikel"] . '</td>';
                    echo '<td>' . $product["naam"] . '</td>';
                    echo '<td>' . $product["inkoopprijs"] . '</td>';
                    echo '<td>' . $product["typeProduct"] . '</td>';
                    echo '<td>' . $product["verkoopprijs"] . '</td>';
                    echo '<td>' . $product["fabriek"] . '</td>';
                    echo '<td>';
                    echo '<form method="post" action="Artikelen toevoegen.php">';
                    echo '<input type="hidden" name="delete_id" value="' . $product["idartikel"] . '">';
                    echo '<button type="submit" name="verwijderen">Verwijderen</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            ?>
        </div>
    </div>
</body>
</html>
