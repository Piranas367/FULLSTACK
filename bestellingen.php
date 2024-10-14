<?php
require('indexConfig.php'); 

 function executeQuery($conn, $query, $params, $types = "") {
    $stmt = $conn->prepare($query);
    if ($types != "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
 }

 // Fetch data for the table, filtering by search term if provided
 $searchTerm = '';
 $sql = " SELECT * FROM Bestellingen"; 
 $result = executeQuery($conn, $sql, ["%" . $searchTerm . "%"], "");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bestelling.css">
    <title>Bestellingen</title>
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
                <li><a href="bestelling plaatsen.php">Bestelling plaatsen</a></li>
                <li><a href="bestelling aanpassen.php">Bestelling aanpassen</a></li>
            </ul>
        </div>
    </div>
</header>

<body>
    <h1 style="text-align: center;">Bestellingen</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Aantal</th>
                    <th>Locatie</th>
                    <th>Bestelling Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['naam'] . "</td>";
                        echo "<td>" . $row["aantal"] . "</td>";
                        echo "<td>" . $row['locatieNaam'] . "</td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Geen bestellingen gevonden</td></tr>";
                }
                ?>
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
            </tbody>
        </table>
    </div>  
    
    

    
</body>
</html>
