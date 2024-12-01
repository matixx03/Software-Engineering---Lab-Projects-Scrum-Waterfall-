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
                <li class="navelement"><a href="index.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Add book</h1>
    <main> 
    <div class="add_book">
    <form action="Manage_Catalog_PHP.php" method="POST" class="addform">
            Title: <input type="text" name="Title" class="addform" required>
            Author: <input type="text" name="Author" class="addform" required>
            Year: <input type="number" name="Year" class="addform" value="2000" required>
            Edition: <input type="text" name="Edition" class="addform"min="1" value="1" required>
            Publisher: <input type="text" name="Publisher" class="addform" required>
            Number of Pieces: <input type="number" name="Pieces" class="addform"min="1" value="1"required>
            <input type="submit" name="add" value=" Add Book " class="vacainput">
        </form>
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
        <tbody>
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

// Datenbankabfrage für die Anzeige der Tabelle
$stmt = $conn->prepare("SELECT ID, Title, Author, Year, Edition, Publisher, Pieces FROM book ORDER BY Title");
$stmt->execute();
$result = $stmt->get_result();

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
                <li class="navelement"><a href="index.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>

    <h1 class="header1">Add book</h1>
    <main>
        <div class="add_book">
            <form action="Manage_Catalog_PHP.php" method="POST" class="addform">
                Title: <input type="text" name="Title" class="addform" required>
                Author: <input type="text" name="Author" class="addform" required>
                Year: <input type="number" name="Year" class="addform" value="2000" required>
                Edition: <input type="text" name="Edition" class="addform" min="1" value="1" required>
                Publisher: <input type="text" name="Publisher" class="addform" required>
                Number of Pieces: <input type="number" name="Pieces" class="addform" min="1" value="1" required>
                <input type="submit" name="add" value=" Add Book " class="vacainput">
            </form>
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
                <tbody>
                    <?php
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
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
