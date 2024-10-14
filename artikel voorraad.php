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

// Handle adding a new product to the stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Toevoegen'])) {
    $idartikel = $_POST['idartikel'];
    $locatie = $_POST['locatie'];
    $aantal = $_POST['aantal'];

    // Get or insert location
    $locatieResult = executeQuery($conn, "SELECT idVestigingen FROM Vestigingen WHERE naam = ?", [$locatie], "s");
    
    if ($locatieResult->num_rows > 0) {
        $idVestiging = $locatieResult->fetch_assoc()['idVestigingen'];
    } else {
        executeQuery($conn, "INSERT INTO Vestigingen (naam) VALUES (?)", [$locatie], "s");
        $idVestiging = $conn->insert_id;
    }

    // Check and update or insert stock
    $voorraadResult = executeQuery($conn, "SELECT aantal FROM voorraad WHERE idartikel = ? AND idVestigingen = ?", [$idartikel, $idVestiging], "ii");

    if ($voorraadResult->num_rows > 0) {
        $nieuwAantal = $voorraadResult->fetch_assoc()['aantal'] + $aantal;
        executeQuery($conn, "UPDATE voorraad SET aantal = ? WHERE idartikel = ? AND idVestigingen = ?", [$nieuwAantal, $idartikel, $idVestiging], "iii");
    } else {
        executeQuery($conn, "INSERT INTO voorraad (idartikel, idVestigingen, aantal) VALUES (?, ?, ?)", [$idartikel, $idVestiging, $aantal], "iii");
    }

    header("location: artikel voorraad.php");
}

// Handle updating an article
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_changes'])) {
    $idartikel = $_POST['idartikel'];
    $productName = $_POST['productName'];
    $inkoopprijs = $_POST['inkoopprijs'];
    $typeProduct = $_POST['typeProduct'];
    $verkoopprijs = $_POST['verkoopprijs'];
    $fabriek = $_POST['fabriek'];
    $aantal = $_POST['aantal'];

    // Update product details
    executeQuery($conn, 
        "UPDATE artikel SET naam = ?, inkoopprijs = ?, typeProduct = ?, verkoopprijs = ?, fabriek = ? WHERE idartikel = ?",
        [$productName, $inkoopprijs, $typeProduct, $verkoopprijs, $fabriek, $idartikel],
        "sdsdsi"
    );

    // Update stock
    executeQuery($conn,
        "UPDATE voorraad SET aantal = ? WHERE idartikel = ?",
        [$aantal, $idartikel],
        "ii"
    );
}
// Handle deleting an article
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verwijderen'])) {
    $idartikel      = $_POST['idartikel'];
    $aantal         = $_POST['aantal'];
    $locatie        = $_POST['locatieNaam'];

    // Get idVestigingen from locatie
    $vestigingResult = executeQuery($conn, "SELECT idVestigingen FROM Vestigingen WHERE naam = ? LIMIT 1", [$locatie], "s");
    
    if ($vestigingResult->num_rows > 0) {
        $idVestiging = $vestigingResult->fetch_assoc()['idVestigingen'];
        
        // Delete related rows from voorraad
        executeQuery($conn, "DELETE FROM voorraad WHERE idartikel = ? AND aantal = ? AND idVestigingen = ?", [$idartikel, $aantal, $idVestiging], "iii");

        // Check if any stock remains for this location
        $vestigingCheck = executeQuery($conn, "SELECT COUNT(*) AS total FROM voorraad WHERE idVestigingen = ?", [$idVestiging], "i");
        $countResult = $vestigingCheck->fetch_assoc()['total'];
        
        if ($countResult == 0) {
            // Delete the location if no more stock exists
            executeQuery($conn, "DELETE FROM Vestigingen WHERE idVestigingen = ?", [$idVestiging], "i");
        }

        // Finally, delete the article itself
        executeQuery($conn, "DELETE FROM artikel WHERE idartikel = ?", [$idartikel], "i");
    }

    header("Location: artikel voorraad.php");
}


// Handle search functionality
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Fetch data for the table, filtering by search term if provided
$sql = "SELECT artikel.*, voorraad.aantal, Vestigingen.naam AS locatieNaam
        FROM artikel
        INNER JOIN voorraad USING (idartikel)
        INNER JOIN Vestigingen USING (idVestigingen)
        WHERE artikel.naam LIKE ?";
$result = executeQuery($conn, $sql, ["%" . $searchTerm . "%"], "s");

// Fetch all article ids
$idArtikelen = executeQuery($conn, "SELECT idartikel FROM artikel", []);

// Fetch all locations
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
        function WisselKnop(button, bewerkbaar) {
            const trElement = button.closest('tr');
            const formElements = trElement.querySelectorAll('input');
            formElements.forEach(element => {
                element.disabled = !bewerkbaar;
            });
            trElement.querySelector('.submit_changes').style.display = bewerkbaar ? 'inline' : 'none';
            trElement.querySelector('.edit_button').style.display = bewerkbaar ? 'none' : 'inline';
        }

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
                <td>ID van product</td>
                <td>
                    <select name="idartikel" required>
                        <?php
                        if ($idArtikelen->num_rows > 0) {
                            while ($row = $idArtikelen->fetch_assoc()) {
                                echo '<option value="' . $row['idartikel'] . '">' . $row['idartikel'] . '</option>';
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
                    <th>ID van product</th>
                    <th>Product</th>
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
                        <td>' . $row['idartikel'] . '</td>
                        <td><input type="text" name="productName" value="' . $row['naam'] . '" disabled></td>
                        <td><input type="number" name="inkoopprijs" value="' . $row['inkoopprijs'] . '" disabled></td>
                        <td><input type="text" name="typeProduct" value="' . $row['typeProduct'] . '" disabled></td>
                        <td><input type="number" name="verkoopprijs" value="' . $row['verkoopprijs'] . '" disabled></td>
                        <td><input type="text" name="fabriek" value="' . $row['fabriek'] . '" disabled></td>
                        <td><input type="number" name="aantal" value="' . $row['aantal'] . '" disabled></td>
                        <td><input type="text" name="locatieNaam" value="' . $row['locatieNaam'] . '" disabled></td>
                        <input type="hidden" name="idartikel" value="' . $row['idartikel'] . '">
                        <td><button type="button" class="edit_button" onclick="WisselKnop(this, true)">Bewerken</button></td>
                        <td><button type="submit" name="submit_changes" class="submit_changes" style="display:non">Opslaan</button></td>
                        </form>
                        
                        <form method="POST" action="artikel voorraad.php">
                        <input type="hidden" name="idartikel" value="' . $row['idartikel'] . '">
                        <input type="hidden" name="aantal" value="' . $row['aantal'] . '">
                        <input type="hidden" name="locatieNaam" value="' . $row['locatieNaam'] . '">
                        <input type="hidden" name="naam" value="' . $row['naam'] . '">
                        <td><button type="submit" name="verwijderen">Verwijderen</button></td>
                        </form>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="9">Geen artikelen gevonden</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
