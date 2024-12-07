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
                <li class="navelement"><a href="Manage_Catalog_PHP.php" class="navlink">Manage Catalog</a></li>
                <li class="navelement"><a href="return.php" class="navlink">Return Books</a></li>
                <li class="navelement"><a href="logout.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1 class="header1">Add new Users</h1>
    <main> 

    <!-- Suchfomular + Add Button -->
    <div class="control-container">
        <button id="toggleAddBook" class="control-btn">Add New User</button>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search for User" class="search-input">
        </div>
    </div>

    <div id="addBookForm" class="add_book" style="display: none;">
    <form action="account.php" method="POST" class="addform">
        Lastname: <input type="text" name="Surname" class="addform" required>
        Firstname: <input type="text" name="Name" class="addform" required>
        Email: <input type="email" name="E_mail" class="addform" required>
        Password: <input type="password" name="password" class="addform" required>
        Select Role: 
        <select name="role" class="addform" required>
            <option value="librarian">Librarian</option>
            <option value="user">User</option>
        </select>
        <input type="submit" name="add" value=" Add User " class="vacainput">
    </form>
</div>

</div>
<div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Last name</th>
                        <th>First name</th>
                        <th>E-Mail</th>
                        <th>Password</th>
                        <th>Role</th>
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
        $surname = $_POST['Surname'];
        $name = $_POST['Name'];
        $email = $_POST['E_mail'];
        $password = $_POST['password'];
        $role = $_POST['role'];

            $stmt = $conn->prepare("INSERT INTO borrower (Surname, Name, E_mail, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $surname, $name, $email, $password, $role);

            if ($stmt->execute()) {
                echo "<p>New User added successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        
    }
    

    if (isset($_POST['delete'])) {
        foreach ($_POST['delete'] as $id => $value) {
            if (is_numeric($id)) {
                $stmt = $conn->prepare("DELETE FROM borrower WHERE ID = ?");
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
$stmt = $conn->prepare("SELECT ID,Surname, Name, E_mail, password, role FROM borrower ORDER BY ID");
$stmt->execute();
$result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["Surname"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["E_mail"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["password"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
                            echo "<td>";
                            echo "<form action='account.php' method='POST' style='display: inline;'>";
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
                    toggleButton.textContent = 'Hide Add User';
                    addBookForm.classList.add('visible');
                } else {
                    addBookForm.style.display = 'none';
                    toggleButton.textContent = 'Add New User';
                    addBookForm.classList.remove('visible');
                }
            });

        });
        </script>
        
    </main>
</body>
</html>
