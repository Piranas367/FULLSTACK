<?php
require('indexConfig.php'); 

function executeQuery($conn, $query, $params = [], $types = "") {
    $stmt = $conn->prepare($query);
    if ($types != "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

$sql = "SELECT 
            bestellijst.idbestellijst, 
            bestellijst.idVestigingen, 
            bestellijst.leveringsstatus, 
            Bestelling.aantal, 
            Bestelling.idartikel, 
            Vestigingen.naam AS locatieNaam, 
            artikel.naam AS artikelNaam
        FROM 
            bestellijst
        INNER JOIN 
            Bestelling ON bestellijst.idbestellijst = Bestelling.idbestellijst
        INNER JOIN 
            Vestigingen ON bestellijst.idVestigingen = Vestigingen.idVestigingen  
        INNER JOIN 
            artikel ON Bestelling.idartikel = artikel.idartikel";

$result = executeQuery($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bestelling.css">
    <title>Bestellingen</title>
    <script>
        // Functie om een bevestigingsmelding te tonen voor het verwijderen
        function bevestigVerwijderen() {
            return confirm('Weet je zeker dat je je Bestelling wilt verwijderen?');
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
                <li><a href="bestelling aanpassen.php">Bestellingen aanpassen</a></li>
                <li><a href="bestelling plaatsen.php">Bestelling plaatsen</a></li>
            </ul>
        </div>
    </div>
</header>

<h1 style="text-align: center;">Bestellingen</h1>

<div class="table-container">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_changes'])) {
        $leveringsstatus    = 'Geleverd!';
        $idbestellijst      = $_POST['idbestellijst'];

        executeQuery($conn,
            "UPDATE bestellijst SET leveringsstatus = ? WHERE idbestellijst = ? AND leveringsstatus = 'niet geleverd'",
            [$leveringsstatus, $idbestellijst],
            "si"
        );

        $result = executeQuery($conn, $sql);
        echo "Bestelling status aangepast!";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verwijder'])) {
        $idbestellijst = $_POST['idbestellijst'];

        executeQuery($conn, "DELETE FROM Bestelling WHERE idbestellijst = ?",
        [$idbestellijst], "i"
        );

        executeQuery($conn, "DELETE FROM bestellijst WHERE idbestellijst = ?",
        [$idbestellijst], 
        "i"
        );

        $result = executeQuery($conn, $sql);
        echo "Bestelling verwijderd!";
    }

    $last_idbestellijst = null; 
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($last_idbestellijst != $row['idbestellijst']) {
                if ($last_idbestellijst != null) {
                    echo "</tbody></table></div><br>"; 
                }

                echo "<div class='order-group'>
                        <h3 class='order-heading'>Bestelling ID: " . $row['idbestellijst'] . "</h3>";
                echo "<table>";
                echo "<thead><tr>
                        <th>Artikel Naam</th>
                        <th>Vestiging</th>
                        <th>Aantal</th>
                        <th>Leveringsstatus</th>
                        <th>Naam besteller</th>
                        <th>Actie</th>
                      </tr></thead><tbody>";

                $last_idbestellijst = $row['idbestellijst'];
            }

            // gegevens in de bestelling 
            echo "<tr>";
            echo "<td>" . $row['artikelNaam'] . "</td>";
            echo "<td>" . $row['locatieNaam'] . "</td>";
            echo "<td>" . $row['aantal'] . "</td>";
            echo "<td>" . $row['leveringsstatus'] . "</td>";
            echo "<td>" . $row['Besteller'] . "</td>";

            // buttons om te kunnen verwijderen en/of status aan te passen
            echo "<td>
                    <form method='POST' onsubmit='return bevestigVerwijderen()'>
                        <input type='hidden' name='idbestellijst' value='" . $row['idbestellijst'] . "'>
                        <button type='submit' name='submit_changes' class='submit_changes'>Status aanpassen</button>
                        <button type='submit' name='verwijder' class='verwijder'>Verwijder bestelling</button>
                    </form>
                  </td>";      
            echo "</tr>";
        }
        echo "</tbody></table></div>"; 
    } else {
        echo "<tr><td colspan='5'>Geen bestellingen gevonden</td></tr>";
    }
    ?>
</div>

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
