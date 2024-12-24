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
    <link rel="stylesheet" href="messages.css">
    <script src="badge_utils.js"></script>
    <title>Library</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index_user.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="return_user.php" class="navlink">Return Books</a></li>
                <li class="navelement"><a href="messages.php" class="navlink">Messages <span id="unread-badge" class="badge"></span></a></li>
                <li class="navelement logout"><a href="logout.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Library Catalog</h1>
    
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
    
    $borrower_email = $_SESSION["email"];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow'])) {
        $book_id = $_POST['book_id'];
       
        $sql = "SELECT ID FROM borrower WHERE E_mail = '$borrower_email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $borrower = $result->fetch_assoc();
            $borrower_id = $borrower['ID'];
            
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

    $sql = "SELECT * FROM book ORDER BY title";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="table-container">';
        echo '<table id="tabletest">';
        echo '<thead>';
        echo '<tr>';
        echo '<th onclick="sortTable(0)">Title</th>';
        echo '<th onclick="sortTable(1)">Author</th>';
        echo '<th onclick="sortTable(2)">Year</th>';
        echo '<th onclick="sortTable(3)">Edition</th>';
        echo '<th onclick="sortTable(4)">Publisher</th>';
        echo '<th onclick="sortTable(5)">Available Pieces</th>';
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
            
            echo "<td>";
            if ($row["Rating"]) {
                $rating = round($row["Rating"], 1);
                echo "<div class='static-rating'>";
    
                for ($i = 1; $i <= floor($rating); $i++) {
                    echo "<i class='fas fa-star'></i>";
                }
    
                if ($rating - floor($rating) >= 0.3) {
                    echo "<i class='fas fa-star-half-alt'></i>";
                }
    
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
                echo "<input type='submit' name='borrow' value='Borrow' class='borrow-btn'>";
                echo "</form>";
            } else {
                echo "Not Available";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    $conn->close();
    ?>

    <script>
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("tabletest");
        switching = true;
        dir = "asc";
        
        while (switching) {
            switching = false;
            rows = table.rows;
            
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];

                var xValue = x.innerHTML.trim();
                var yValue = y.innerHTML.trim();
                
                if (dir == "asc") {
                    if (xValue.localeCompare(yValue) > 0) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (xValue.localeCompare(yValue) < 0) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                let text = '';
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
    document.addEventListener('DOMContentLoaded', initializeBadgeUpdates);
    </script>
</body>
</html>