<?php
session_start();

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
    $password = $_POST['password'];
    

    $stmt = $conn->prepare("SELECT E_Mail, role FROM borrower WHERE E_mail = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $role = $row['role'];
        if ($role === "librarian") {
            $_SESSION["email"] = $email;
            $_SESSION["id"] = 1;
            $_SESSION["role"] = $role;
            echo "<script> location.href='index_lib.php'; </script>";
            exit();
        } elseif ($role === "user") {
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;
            $_SESSION["id"] = 1;
            echo "<script> location.href='index_user.php'; </script>";
            exit();
        }

    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error) && $error) { ?>
            <p style="color: red;"><?= htmlspecialchars($error); ?></p>
         <?php } ?>
        <form action="login.php" method="POST">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
