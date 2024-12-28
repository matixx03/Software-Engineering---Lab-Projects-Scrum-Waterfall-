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
    <title>Return Books</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index_lib.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="Manage_Catalog_PHP.php" class="navlink">Manage Catalog</a></li>
                <li class="navelement"><a href="logout.php" class="navlink">Logout</a></li>
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['return'])) {
            // Rückgabecode für den Fall, dass "Return" geklickt wurde
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
        
        // Erweiterung des Rückgabedatums um eine Woche
        if (isset($_POST['extend'])) {
            $borrow_id = $_POST['borrow_id'];
    
            // SQL-Abfrage, um das aktuelle Rückgabedatum zu erhalten
            $sql = "SELECT Return_Date FROM borrowed WHERE ID = $borrow_id";
            $result = $conn->query($sql);
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $return_date = $row['Return_Date'];
    
                // Rückgabedatum um eine Woche verlängern
                $new_return_date = date('Y-m-d', strtotime($return_date . ' +7 days'));
    
                // SQL-Abfrage, um das Rückgabedatum zu aktualisieren
                $sql = "UPDATE borrowed SET Return_Date = '$new_return_date' WHERE ID = $borrow_id";
                if ($conn->query($sql) === TRUE) {
                    echo "<p style='color: blue; text-align: center;'>Return date successfully extended by 1 week!</p>";
                } else {
                    echo "<p style='color: red; text-align: center;'>Error extending the return date.</p>";
                }
            }
        }
    }

    // alle nicht bewertete, ausgeliehene Bücher
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
        echo '<th>Rating</th>';
        echo '<th>Action</th>';

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $result->fetch_assoc()) {      // durchläuft Ergebnisse und speichert borrow-id
            $borrowId = $row["borrow_id"];
            
            echo "<tr>";
            // fügt Buchdaten ein
            echo "<td>" . $row["Title"] . "</td>";
            echo "<td>" . $row["Author"] . "</td>";
            echo "<td>" . $row["Name"] . " " . $row["Surname"] . "</td>";
            echo "<td>" . $row["Borrow_Date"] . "</td>";
            echo "<td>" . $row["Return_Date"] . "</td>";
            echo "<td>";

            echo "<div class='rating-container'>";
            echo "<form method='POST' class='return-form' id='form_" . $borrowId . "'>";    // Formular für jedes Buch
            echo "<input type='hidden' name='borrow_id' value='" . $borrowId . "'>";
            echo "<input type='hidden' name='book_id' value='" . $row["Book_ID"] . "'>";
            echo "<input type='hidden' name='rating' class='rating-input' id='rating_" . $borrowId . "' value=''>";

            echo "<div class='star-rating' data-borrow-id='" . $borrowId . "'>";
            for ($i = 1; $i <= 5; $i++) {
                echo "<i class='far fa-star' data-rating='$i'></i>";
            }
            
            echo "</div>";
            echo "</td>";
            echo "<td>";
            echo "<input type='submit' name='extend' value='Extend' class='return-btn'>";
            echo "<input type='submit' name='return' value='Return' class='return-btn'>";
            echo "</form>";
            echo "</div>";
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.rating-container').forEach(container => {
            const form = container.querySelector('.return-form');
            const ratingInput = container.querySelector('.rating-input');
            const starRating = container.querySelector('.star-rating');
            const stars = container.querySelectorAll('.fa-star');
            
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    highlightStars(stars, rating);
                });

                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    starRating.dataset.selected = rating;
                    highlightStars(stars, rating);
                });
            });

            starRating.addEventListener('mouseleave', function() {
                const selectedRating = this.dataset.selected || 0;
                highlightStars(stars, selectedRating);
            });

            form.addEventListener('submit', function(e) {
                if (e.submitter && e.submitter.name === 'return' && !ratingInput.value) {
                    e.preventDefault();
                    alert('Please select a rating before returning the book.');
                }
            });
        });

        function highlightStars(stars, rating) {
            stars.forEach(star => {
                const starRating = star.dataset.rating;
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }
    });
    </script>
