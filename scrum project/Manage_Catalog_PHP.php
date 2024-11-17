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
    <form action="Manage_Catalogp.php" method="POST" class="addform">
            Title: <input type="text" name="title" class="addform" required>
            Author: <input type="text" name="author" class="addform" required>
            Year: <input type="number" name="year" class="addform" value="2000" required>
            Edition: <input type="text" name="edition" class="addform" required>
            Publisher: <input type="text" name="publisher" class="addform" required>
            Number of Pieces: <input type="number" name="number_of_pieces" class="addform"min="1" value="1"required>
            <input type="submit" name="save" value="Save" class="vacainput">
        </form>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Year</th>
                <th>Edition</th>
                <th>Publisher</th>
                <th>Number of Pieces</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "libary";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (isset($_POST['save'])) {
                    // Hole die Eingabewerte
                    $title = $_POST['title'];
                    $author = $_POST['author'];
                    $year = $_POST['year'];
                    $edition = $_POST['edition'];
                    $publisher = $_POST['publisher'];
                    $number_of_pieces = $_POST['number_of_pieces'];

                   
                    if ($number_of_pieces < 1) {
                        echo "<p style='color: red;'>The number of pieces must be at least 1.</p>";
                    } else {
                       
                        $sql = "INSERT INTO catalog (title, author, year, edition, publisher, number_of_pieces)
                                VALUES ('$title', '$author', '$year', '$edition', '$publisher', '$number_of_pieces')";
                        if ($conn->query($sql) === TRUE) {
                            echo "<p>New book added successfully!</p>";
                        } else {
                            echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                        }
                    }
                }

                if (isset($_POST['delete'])) {
                    foreach ($_POST['delete'] as $id => $value) {
                        $sql = "DELETE FROM catalog WHERE id = $id";
                        $conn->query($sql);
                    }
                }
            }

            
            $sql = "SELECT * FROM catalog ORDER BY title";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["author"] . "</td>";
                    echo "<td>" . $row["year"] . "</td>";
                    echo "<td>" . $row["edition"] . "</td>";
                    echo "<td>" . $row["publisher"] . "</td>";
                    echo "<td>" . $row["number_of_pieces"] . "</td>";
                    echo "<td>";
                    echo "<form action='Manage_Catalogp.php' method='POST' style='display: inline;'>";
                    echo "<input type='submit' value='Delete' class='delete' name='delete[" . $row["id"] . "]'>";
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
