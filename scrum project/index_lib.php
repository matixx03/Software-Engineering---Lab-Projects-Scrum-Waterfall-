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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Library</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index_lib.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="Manage_Catalog_PHP.php" class="navlink">Manage Catalog</a></li>
                <li class="navelement"><a href="return.php" class="navlink">Return Books</a></li>
                <li class="navelement"><a href="logout.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Library Catalog</h1>
    

    <!-- Suchfomular -->
    <div class="control-container">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search for title, author, publisher..." class="search-input">
        </div>
    </div>
    
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
            
            // Rating Anzeige
            echo "<td>";
            if ($row["Rating"]) {
                $rating = round($row["Rating"], 1);
                echo "<div class='static-rating'>";
    
                // Volle Sterne
                for ($i = 1; $i <= floor($rating); $i++) {
                    echo "<i class='fas fa-star'></i>";
                }
    
                // Halber Stern
                if ($rating - floor($rating) >= 0.3) {
                    echo "<i class='fas fa-star-half-alt'></i>";
                }
    
                // Leere Sterne
                $remainingStars = 5 - ceil($rating);
                if ($rating - floor($rating) < 0.3) {
                    $remainingStars = 5 - floor($rating);
                }
                for ($i = 0; $i < $remainingStars; $i++) {
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                let text = '';
                // Durchsuche alle Zellen der Zeile außer der letzten (Action-Spalte)
                for(let i = 0; i < row.cells.length - 1; i++) {
                    text += row.cells[i].textContent.toLowerCase() + ' ';
                }
                
                if(text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
    
</body>
</html>
