<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];

    $stmt = $conn->prepare("SELECT * FROM borrower WHERE E_mail = ? AND Surname = ? AND Name = ?");
    $stmt->bind_param("sss", $email, $surname, $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Login erfolgreich! Willkommen, " . htmlspecialchars($name) . " " . htmlspecialchars($surname) . ".";
    } else {
        echo "Login fehlgeschlagen. Bitte überprüfe deine Eingaben.";
    }

    $stmt->close();
}

$conn->close();
?>


