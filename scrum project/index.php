<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Manage_Catalog_css.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Library</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="Manage_Catalog_PHP.php" class="navlink">Manage Catalog</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Login</a></li>
                <li class="navelement"><a href="return.php" class="navlink">Return Books</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Library Catalog</h1>
    
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ausleihformular
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow'])) {
        $book_id = $_POST['book_id'];
        $borrower_email = $_POST['borrower_email'];
        
        // suche in der borrower-Tabelle nach der E-Mail
        $sql = "SELECT ID FROM borrower WHERE E_mail = '$borrower_email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $borrower = $result->fetch_assoc();
            $borrower_id = $borrower['ID'];
            
            // prüft in book-Tabelle ob noch bücher da sind
            $sql = "SELECT Pieces FROM book WHERE ID = $book_id";
            $result = $conn->query($sql);
            $book = $result->fetch_assoc();
            
            if ($book['Pieces'] > 0) {
                $borrow_date = date('Y-m-d');
                $return_date = date('Y-m-d', strtotime('+14 days'));

                $sql = "INSERT INTO borrowed (Book_ID, Borrower_ID, Borrow_Date, Return_Date)
                VALUES ($book_id, $borrower_id, '$borrow_date', '$return_date')";
                        
                if ($conn->query($sql) === true) {
                    $sql = "UPDATE book SET Pieces = Pieces - 1 WHERE ID = $book_id";
                    $conn->query($sql);
                    echo "<p style='color: green; text-align: center;'>Book successfully borrowed</p>";
                }
            } else {
                echo "<p style='color: red; text-align: center;'>No copies available</p>";
            }
        } else {
            echo "<p style='color: red; text-align: center;'>Email not found. Please register first.</p>";
        }
    }

    $sql = "SELECT * FROM book WHERE Pieces > 0 ORDER BY title";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="table-container">';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';

        echo '<th>Title</th>';
        echo '<th>Author</th>';
        echo '<th>Year</th>';
        echo '<th>Edition</th>';
        echo '<th>Publisher</th>';
        echo '<th>Available Pieces</th>';
        echo '<th>Rating</th>';
        echo '<th>Action</th>';
        
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Title"] . "</td>";
            echo "<td>" . $row["Author"] . "</td>";
            echo "<td>" . $row["Year"] . "</td>";
            echo "<td>" . $row["Edition"] . "</td>";
            echo "<td>" . $row["Publisher"] . "</td>";
            echo "<td>" . $row["Pieces"] . "</td>";
            
            // fürs Rating
            echo "<td>";
            if ($row["Rating"]) {
                $rating = round($row["Rating"], 1);
                echo "<div class='static-rating'>";
                // volle Sterne
                for ($i = 1; $i <= floor($rating); $i++) {
                    echo "<i class='fas fa-star'></i>";
                }
                // halber Stern
                if ($rating - floor($rating) >= 0.5) {
                    echo "<i class='fas fa-star-half-alt'></i>";
                }
                // leere Sterne
                for ($i = ceil($rating); $i < 5; $i++) {
                    echo "<i class='far fa-star'></i>";
                }
                echo " <span class='rating-number'>(" . $rating . ")</span>";
                echo "</div>";
            } else {
                echo "No ratings yet";
            }
            echo "</td>";
            
            echo "<td>";
            if ($row["Pieces"] > 0) {
                echo "<form method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='book_id' value='" . $row["ID"] . "'>";
                echo "<input type='email' name='borrower_email' placeholder='Your Email' required>";
                echo "<input type='submit' name='borrow' value='Borrow' class='borrow-btn'>";
                echo "</form>";
            } else {
                echo "Not Available";
            }
            echo "</td>";
            echo "</tr>";
        }
    }
    $conn->close();
    ?>
</body>
</html>
