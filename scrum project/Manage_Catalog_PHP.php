<?php
session_start();
if ((!isset($_SESSION["id"]))) {
    echo "no Access";
    echo "<script> location.href='login.php'; </script>";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Manage_Catalog_css.css">
    <meta charset="UTF-8">
    <title>Catalog</title>
</head>

<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index_lib.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="logout.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Manage Catalog</h1>
    <main> 

    <!-- Suchfomular + Add Button -->
    <div class="control-container">
        <button id="toggleAddBook" class="control-btn">Add New Book</button>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search for title, author, publisher..." class="search-input">
        </div>
    </div>

    <div id="addBookForm" class="add_book" style="display: none;">
        <form action="Manage_Catalog_PHP.php" method="POST" class="addform">
            Title: <input type="text" name="Title" class="addform" required>
            Author: <input type="text" name="Author" class="addform" required>
            Year: <input type="number" name="Year" class="addform" value="2000" required>
            Edition: <input type="text" name="Edition" class="addform" required>
            Publisher: <input type="text" name="Publisher" class="addform" required>
            Number of Pieces: <input type="number" name="Pieces" class="addform" min="1" value="1" required>
            <input type="submit" name="add" value=" Add Book " class="vacainput">
        </form>
    </div>

</div>
<div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Edition</th>
                        <th>Publisher</th>
                        <th>Number of Pieces</th>
                        <th>Action</th>
                    </tr>
                </thead>
        <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add'])) {
        // Eingabewerte absichern und hinzufügen
        $title = $_POST['Title'];
        $author = $_POST['Author'];
        $year = $_POST['Year'];
        $edition = $_POST['Edition'];
        $publisher = $_POST['Publisher'];
        $pieces = $_POST['Pieces'];

        if ($pieces < 1) {
            echo "<p style='color: red;'>The number of pieces must be at least 1.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO book (Title, Author, Year, Edition, Publisher, Pieces) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssissi", $title, $author, $year, $edition, $publisher, $pieces);

            if ($stmt->execute()) {
                echo "<p>New book added successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    }

    if (isset($_POST['delete'])) {
        foreach ($_POST['delete'] as $id => $value) {
            if (is_numeric($id)) {
                $stmt = $conn->prepare("DELETE FROM book WHERE ID = ?");
                $stmt->bind_param("i", $id);

                if (!$stmt->execute()) {
                    echo "<p style='color: red;'>Error deleting record: " . $stmt->error . "</p>";
                }
                $stmt->close();
            }
        }
    }
}

if (isset($_POST['decrease'])) {
    foreach ($_POST['decrease'] as $id => $value) {
        if(!empty($_POST['decreasenumber'])) {
            $decreasenumber = $_POST['decreasenumber'];
            $stmt = $conn->prepare("UPDATE book SET Pieces = GREATEST(0, Pieces - ?) WHERE id = ?");
            $stmt->bind_param("ii", $decreasenumber, $id);
            $stmt->execute();
        }
        else {
            $stmt = $conn->prepare("UPDATE book SET Pieces = GREATEST(0, Pieces - 1) WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        $stmt->close();
    }
}


// Datenbankabfrage für die Anzeige der Tabelle
$stmt = $conn->prepare("SELECT ID, Title, Author, Year, Edition, Publisher, Pieces FROM book ORDER BY ID");
$stmt->execute();
$result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["ID"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Title"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Author"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Year"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Edition"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Publisher"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Pieces"]) . "</td>";
                            echo "<td>";
                            echo "<form action='Manage_Catalog_PHP.php' method='POST' style='display: inline;'>";
                            echo "<input type='number' class='decreasenumber' style='margin-right: 5px' name='decreasenumber' placeholder='Amount to decrease Pieces'>";
                            echo "<input type='submit' value='Decrease' class='decrease' style='margin-right: 5px' name='decrease[" . htmlspecialchars($row["ID"]) . "]'>";
                            echo "<input type='submit' value='Delete' class='delete' name='delete[" . htmlspecialchars($row["ID"]) . "]'>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No data available</td></tr>";
                    }
                    $stmt->close();
                    ?>
            </table>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');

            // bei Eingabe ins Suchfeld
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                tableRows.forEach(row => {
                    let text = '';
                    // Durchsuche alle Zellen der Zeile außer der letzten (Action-Spalte)
                    for(let i = 0; i < row.cells.length - 1; i++) {
                        text += row.cells[i].textContent.toLowerCase() + ' ';
                    }
                    
                    if(text.includes(searchTerm)) {
                        row.style.display = '';     // Zeigt Zeile an, wenn Text gefunden
                    } else {
                        row.style.display = 'none';     // versteckt wenn nicht
                    }
                });
            });

            const toggleButton = document.getElementById('toggleAddBook');  // Toggle-Button
            const addBookForm = document.getElementById('addBookForm');     // Formular

            toggleButton.addEventListener('click', function() {
                if(addBookForm.style.display === 'none') {
                    addBookForm.style.display = 'block';
                    toggleButton.textContent = 'Hide Add Book';
                    addBookForm.classList.add('visible');
                } else {
                    addBookForm.style.display = 'none';
                    toggleButton.textContent = 'Add New Book';
                    addBookForm.classList.remove('visible');
                }
            });

        });
        </script>
        
    </main>
</body>
</html>
