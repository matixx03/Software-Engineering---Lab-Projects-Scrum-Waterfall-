<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
    <title>STC Time Management System</title>
</head>

<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="employee.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="vacationemplo.php" class="navlink">Vacation Planner</a> </li>
                <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Log out</a> </li>
            </ul>
        </div>
    </nav>
    <main>
        
        <div class="vacationbox">
        <form action="vacationemplo.php" method="POST" class="vacationform">
                vacation inquiry from  <input type="date" name="begin" class="vacainput">
                to  <input type="date" name="end" class="vacainput">
                <input type="submit" name="save" value="save" class="vacainput">
        </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        
                        <th>Begin</th>
                        <th>End</th>
                        <th>status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "time_management";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    if ($_SERVER["REQUEST_METHOD"] === "POST") {
                        if (isset($_POST['save']) && isset($_POST['begin']) && isset($_POST['end'])) {
                            $begin = $_POST['begin'];
                            $end = $_POST['end'];
                            
                            $sql = "INSERT INTO vacation (begin, end, status)
                                VALUES ('$begin', '$end', 'pending')";
                            $conn->query($sql);
                        }

                       if (isset($_POST['delete'])) {
                        foreach ($_POST['delete'] as $id => $value) {
                            $sql = "DELETE FROM `vacation` WHERE `vacation`.`index` = $id;";
                            $conn->query($sql);
                        }
                    }
                    }

                    $sql = "SELECT * FROM vacation ORDER BY begin";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["begin"] . "</td>";
                            echo "<td>" . $row["end"] . "</td>";
                            echo "<td>" . $row["status"] . "</td>";
                            echo "<td>";
                            echo "<form action='vacationemplo.php' method='POST' style='display: inline;'>";
                            echo "<input type='submit' value='Delete' class='delete' name='delete[" . $row["index"] . "]'>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>