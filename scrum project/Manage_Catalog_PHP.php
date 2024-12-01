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
                    // Hole die Eingabewerte
                    $title = $_POST['Title'];
                    $author = $_POST['Author'];
                    $year = $_POST['Year'];
                    $edition = $_POST['Edition'];
                    $publisher = $_POST['Publisher'];
                    $number_of_pieces = $_POST['Pieces'];

                   
                    if ($number_of_pieces < 1) {
                        echo "<p style='color: red;'>The number of pieces must be at least 1.</p>";
                    } else {
                       
                        $sql = "INSERT INTO book (Title, Author, Year, Edition, Publisher, Pieces)
                                VALUES ('$title', '$author', '$year', '$edition', '$publisher', '$number_of_pieces')";
                        if ($conn->query($sql) === TRUE) {
                            #header('Location: Manage_Catalog_PHP.php');
                            echo "<p>New book added successfully!</p>";
                        } else {
                            echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                        }
                    }
                }

                if (isset($_POST['delete'])) {
                    foreach ($_POST['delete'] as $id => $value) {
                        $sql = "DELETE FROM book WHERE id = $id";
                        $conn->query($sql);
                    }
                }
            }

            
            $sql = "SELECT * FROM book ORDER BY title";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["ID"] . "</td>";
                    echo "<td>" . $row["Title"] . "</td>";
                    echo "<td>" . $row["Author"] . "</td>";
                    echo "<td>" . $row["Year"] . "</td>";
                    echo "<td>" . $row["Edition"] . "</td>";
                    echo "<td>" . $row["Publisher"] . "</td>";
                    echo "<td>" . $row["Pieces"] . "</td>";
                    echo "<td>";
                    echo "<form action='Manage_Catalog_PHP.php' method='POST' style='display: inline;'>";
                    echo "<input type='submit' value='Delete' class='delete' name='delete[" . $row["ID"] . "]'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No data available</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
