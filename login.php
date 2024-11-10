<?php 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "time_management";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT employee_type FROM employee WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $employee_type = $row['employee_type'];
        if ($employee_type === "employee") {
            header("Location: employee.php");
            exit();
        } elseif ($employee_type === "supervisor") {
            header("Location: supervisor.php");
            exit();
        }
    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css"> 
    <title>STC Time Management System - Login</title>
   
</head>
<body class="bodyloginclass">
    <nav>
        <div>
            <h1 class=" headerclass" >
                <strong> STC Time Management System</strong></h1>
        </div>
    </nav>
    
    <h2 class ="headerclass">Please enter your credentials:</h2>
    
    <?php if (isset($error) && $error) { ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php } ?>

    <form class="form_style" action="login.php" method="post">
        <div class="form-group">
            <label for="name">Username:</label>
            <input type="text" id="name" name="username" required>
        </div>
    <br>
        <div class="form-group1">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="submit">
            <input type="submit" value="Send">
        </div>
    </form>
</body>
</html>
