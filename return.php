<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Manage_Catalog_css.css">
    <meta charset="UTF-8">
    <title>Return Books</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="Manage_Catalog_PHP.php" class="navlink">Manage Catalog</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Login</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Return Books</h1>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return'])) {
        $borrow_id = $_POST['borrow_id'];
        $book_id = $_POST['book_id'];
        $rating = $_POST['rating'];

        // Rating in der borrowed Tabelle
        $sql = "UPDATE borrowed SET Rating = '$rating' WHERE ID = $borrow_id";
        $conn->query($sql);

        // erhöhe verfügbare Exemplare
        $sql = "UPDATE book SET Pieces = Pieces + 1 WHERE ID = $book_id";
        $conn->query($sql);

        // Berechnung Bewertung der Bücher
        $sql = "UPDATE book SET Rating = (
            SELECT AVG(Rating) 
            FROM borrowed 
            WHERE Book_ID = $book_id 
            AND Rating != ''
        ) WHERE ID = $book_id";
        $conn->query($sql);

        echo "<p style='color: green; text-align: center;'>Book successfully returned and rated!</p>";
    }

    $sql = "SELECT borrowed.ID as borrow_id, borrowed.Book_ID, borrowed.Borrow_Date, borrowed.Return_Date, 
            book.Title, book.Author, borrower.Name, borrower.Surname, borrower.E_mail
            FROM borrowed 
            JOIN book ON borrowed.Book_ID = book.ID 
            JOIN borrower ON borrowed.Borrower_ID = borrower.ID
            WHERE borrowed.Rating = ''
            ORDER BY borrowed.Return_Date";
            
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="table-container">';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Title</th>';
        echo '<th>Author</th>';
        echo '<th>Borrower</th>';
        echo '<th>Borrow Date</th>';
        echo '<th>Return Date</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Title"] . "</td>";
            echo "<td>" . $row["Author"] . "</td>";
            echo "<td>" . $row["Name"] . " " . $row["Surname"] . "</td>";
            echo "<td>" . $row["Borrow_Date"] . "</td>";
            echo "<td>" . $row["Return_Date"] . "</td>";
            echo "<td>";
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='borrow_id' value='" . $row["borrow_id"] . "'>";
            echo "<input type='hidden' name='book_id' value='" . $row["Book_ID"] . "'>";
            echo "<select name='rating' required>";
            echo "<option value=''>Select Rating</option>";
            for ($i = 1; $i <= 5; $i++) {
                echo "<option value='$i'>$i Stars</option>";
            }
            echo "</select>";
            echo "<input type='submit' name='return' value='Return' class='return-btn'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo "<p style='text-align: center;'>No books currently borrowed</p>";
    }

    $conn->close();
    ?>
</body>
</html>