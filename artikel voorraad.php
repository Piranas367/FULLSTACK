<?php
require('indexConfig.php'); 

// Function to execute a prepared statement
function executeQuery($conn, $query, $params, $types = "") {
    $stmt = $conn->prepare($query);
    if ($types != "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

//voegt een locatie en aantal toe in mijn artikel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Toevoegen'])) {

    $idartikel = $_POST['idartikel'];
    $locatie = $_POST['locatie'];
    $aantal = $_POST['aantal'];

    //  Controleert of de locatie al bestaat in de database
    $locatieResult = executeQuery($conn, "SELECT idVestigingen FROM Vestigingen WHERE naam = ?", [$locatie], "s");
    
    // Als de locatie al bestaat, haalt het de idVestigingen op
    if ($locatieResult->num_rows > 0) {
        $idVestiging = $locatieResult->fetch_assoc()['idVestigingen'];
    } else {
        // Als de locatie nog niet bestaat, voegt hij hem toe aan de vestiging tabel
        executeQuery($conn, "INSERT INTO Vestigingen (naam) VALUES (?)", [$locatie], "s");

        //nieuwe gemaakte id
        $idVestiging = $conn->insert_id;
    }

    //Controleert of er al voorraad is voor dit artikel op deze locatie
    $voorraadResult = executeQuery($conn, "SELECT aantal FROM voorraad WHERE idartikel = ? AND idVestigingen = ?", [$idartikel, $idVestiging], "ii");

    // Als er al voorraad bestaat voor dit artikel op deze locatie
    if ($voorraadResult->num_rows > 0) {
        $nieuwAantal = $voorraadResult->fetch_assoc()['aantal'] + $aantal;
        
        // Werk de voorraad bij met het nieuwe aantal
        executeQuery($conn, "UPDATE voorraad SET aantal = ? WHERE idartikel = ? AND idVestigingen = ?", [$nieuwAantal, $idartikel, $idVestiging], "iii");
    } else {
        // Als er nog geen voorraad is voor dit artikel op deze locatie, voeg een nieuw record toe
        executeQuery($conn, "INSERT INTO voorraad (idartikel, idVestigingen, aantal) VALUES (?, ?, ?)", [$idartikel, $idVestiging, $aantal], "iii");
    }

    echo "Product toegevoegd!";
}

// Handle updating an article
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_changes'])) {
    $idartikel      = $_POST['idartikel'];
    $productName    = $_POST['productName'];
    $inkoopprijs    = $_POST['inkoopprijs'];
    $typeProduct    = $_POST['typeProduct'];
    $verkoopprijs   = $_POST['verkoopprijs'];
    $fabriek        = $_POST['fabriek'];
    $aantal         = $_POST['aantal'];

    // Update de gegevens van de artikel
    executeQuery($conn, 
        "UPDATE artikel SET naam = ?, inkoopprijs = ?, typeProduct = ?, verkoopprijs = ?, fabriek = ? WHERE idartikel = ?",
        [$productName, $inkoopprijs, $typeProduct, $verkoopprijs, $fabriek, $idartikel],
        "sdsdsi"
    );

    // Update de voorraad
    executeQuery($conn,
        "UPDATE voorraad SET aantal = ? WHERE idartikel = ?",
        [$aantal, $idartikel],
        "ii"
    );

    echo "Product gegevens aangepast!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verwijderen'])) {
    $idartikel      = $_POST['idartikel'];
    $aantal         = $_POST['aantal'];
    $locatie        = $_POST['locatieNaam'];

    
    $vestigingResult = executeQuery($conn, "SELECT idVestigingen FROM Vestigingen WHERE naam = ? LIMIT 1", [$locatie], "s");

    if ($vestigingResult->num_rows > 0) {
        $idVestiging = $vestigingResult->fetch_assoc()['idVestigingen'];

        // 1. Verwijderd specifiek de voorraad doormiddel van die idartikel en idvestigingen
            //zodat de gegevens er deels nog in zit
        executeQuery($conn, "DELETE FROM voorraad WHERE idartikel = ? AND idVestigingen = ?", [$idartikel, $idVestiging], "ii");

        // 2. Controleer of er nog bestellingen zijn voor dit artikel op andere locaties
        $bestellingCheck = executeQuery($conn, "SELECT COUNT(*) AS total FROM Bestelling WHERE idartikel = ?", [$idartikel], "i");
        $countBestelling = $bestellingCheck->fetch_assoc()['total'];

        if ($countBestelling == 0) {
            echo "Product verwijerd per locatie!";
        } 
}
}

// zoekfunctie
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Laat alle artikelen met de voorraad en locatie zien met joins
$sql = "SELECT artikel.*, voorraad.aantal, Vestigingen.naam AS locatieNaam
        FROM artikel
        INNER JOIN voorraad USING (idartikel)
        INNER JOIN Vestigingen USING (idVestigingen)
        WHERE artikel.naam LIKE ?";
$result = executeQuery($conn, $sql, ["%" . $searchTerm . "%"], "s");

// selecteerd los nog de idartikel
$articlesResult = executeQuery($conn, "SELECT idartikel, naam FROM artikel", []);

// selecteerd los nog de idvestiging
$locaties = executeQuery($conn, "SELECT idVestigingen, naam FROM Vestigingen", []);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel Voorraad</title>
    <link rel="stylesheet" href="style.css">
    <script>
        //functie om de bewerknop te veranderen naar de opslaan knop
        function WisselKnop(button, bewerkbaar) {
            const trElement = button.closest('tr');
            const formElements = trElement.querySelectorAll('input');
            formElements.forEach(element => {
                element.disabled = !bewerkbaar;
            });
            trElement.querySelector('.submit_changes').style.display = bewerkbaar ? 'inline' : 'none';
            trElement.querySelector('.edit_button').style.display = bewerkbaar ? 'none' : 'inline';
        }
        // zorgt ervoor dat er een table komt om daarin de locatie en aantal toe te voegen in de artikel
        function toggleAddProductForm() {
            const form = document.getElementById('addProductForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <header>
    <nav>
        <ul>
            <li><a href="../CRUD PHP/homepagina.php"><img src="home logo.webp" alt="Account" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/inlogpagina.php"><img src="uitlog logo.webp" alt="Account" class="logo-img"></a></li>
            <li><a href="../CRUD PHP/instellingen.php"><img src="instellingen.webp" alt="Account" class="logo-img"></a></li>
        </ul>
    </nav>

    <!-- Slide optie Menu -->
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

        // zorgt ervoor dat de optie menu een slide krijgt
        menuButton.addEventListener('click', () => {
            slideMenu.classList.toggle('show');
        });
        window.addEventListener('click', (event) => {
            if (!menuButton.contains(event.target) && !slideMenu.contains(event.target)) {
                slideMenu.classList.remove('show');
            }
        });
    </script>
    
    <h1>Artikelen Voorraad</h1>
    <br>
    <!-- Zoek balk -->
    <form method="get" action="">
        <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Product zoeken...">
        <br>
        <button type="submit">Zoeken</button>
    </form>
    <br>
    <button onclick="toggleAddProductForm()">Product Toevoegen</button>
    <br>
    <!-- Form om een product toe te voegen -->
    <form id="addProductForm" method="POST" action="" style="display:none;">
        <table>
            <tr>
                <td>Product Naam</td>
                <td>
                    <select name="idartikel" required>
                        <?php
                        //laat de artikel naam zien zodat je een locatie en aantal kan toevoegen
                        if ($articlesResult->num_rows > 0) {
                            while ($row = $articlesResult->fetch_assoc()) {
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
                <td>Locatie:</td>
                <td>
                    <select name="locatie" required>
                        <?php
                        //laat de locatie zien zodat je een aantal kan toevoegen
                        if ($locaties->num_rows > 0) {
                            while ($row = $locaties->fetch_assoc()) {
                                echo '<option value="' . $row['naam'] . '">' . $row['naam'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Geen locaties beschikbaar</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Aantal:</td>
                <!-- zorgt ervoor dat je een aantal kan toevoegen in de artikel en locatie -->
                <td><input type="number" name="aantal" required></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit" name="Toevoegen">Toevoegen</button></td>
            </tr>
        </table>
    </form>

    <!-- Table with articles -->
    <div class="table-button-container">
        <table>
            <thead>
                <tr>
                    <th>Product Naam</th>
                    <th>Inkoop Prijs</th>
                    <th>Type</th>
                    <th>Verkoop Prijs</th>
                    <th>Fabriek</th>
                    <th>Aantal</th>
                    <th>Locatie</th>
                    <th>Bewerken</th>
                    <th>Opslaan</th>
                    <th>Verwijderen</th>
                </tr>
            </thead>
            <tbody> 
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Formulier voor aanpassen van gegevens
                        echo '<tr>
                        <form method="POST" action="artikel voorraad.php">
                        <td><input type="text" name="productName" value="' . $row['naam'] . '" disabled></td>
                        <td><input type="number" name="inkoopprijs" value="' . $row['inkoopprijs'] . '" disabled></td>
                        <td><input type="text" name="typeProduct" value="' . $row['typeProduct'] . '" disabled></td>
                        <td><input type="number" name="verkoopprijs" value="' . $row['verkoopprijs'] . '" disabled></td>
                        <td><input type="text" name="fabriek" value="' . $row['fabriek'] . '" disabled></td>
                        <td><input type="number" name="aantal" value="' . $row['aantal'] . '" disabled></td>
                        <td><input type="text" name="locatieNaam" value="' . $row['locatieNaam'] . '" disabled></td>
                        <input type="hidden" name="idartikel" value="' . $row['idartikel'] . '">
                        <td><button type="button" class="edit_button" onclick="WisselKnop(this, true)">Bewerken</button></td>
                        <td><button type="submit" name="submit_changes" class="submit_changes" style="display:none;">Opslaan</button></td>
                        </form>
                        
                        <form method="POST" action="artikel voorraad.php">
                        <input type="hidden" name="idartikel" value="' . $row['idartikel'] . '">
                        <input type="hidden" name="aantal" value="' . $row['aantal'] . '">
                        <input type="hidden" name="locatieNaam" value="' . $row['locatieNaam'] . '">
                        <td><button type="submit" name="verwijderen">Verwijderen</button></td>
                        </form>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="10">Geen artikelen gevonden</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

