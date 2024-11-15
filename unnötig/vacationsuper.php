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
                <li class="navelement"><a href="supervisor.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="vacationsuper.php" class="navlink">Vacation Planner</a> </li>
                <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a></li>
                <li class="navelement"><a href="login.php" class="navlink">Log out</a> </li>
            </ul>
        </div>
    </nav>
    <main>
        

        <div class="table-container">
            <form class="buttons" action="vacationsuper.php" method="post">
                <table>
                    <thead>
                        <tr>
                            
                            <th>Begin</th>
                            <th>End</th>
                            <th>Status</th>
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
                            if (isset($_POST['accept'])) {
                                foreach ($_POST['accept'] as $id => $value) {
                                    $sql = "UPDATE `vacation` SET `status` = 'Accepted' WHERE `vacation`.`index` = $id;";
                                    $conn->query($sql);
                                }
                            }
                            if (isset($_POST['decline'])) {
                                foreach ($_POST['decline'] as $id => $value) {
                                    $sql = "UPDATE `vacation` SET `status` = 'Declined' WHERE `vacation`.`index` = $id;";
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
                                echo "<input type='submit' value='Accept' class='accept'  style='margin-left: 5px' name='accept[" . $row["index"] . "]'>";
                                echo "<input type='submit' value='Decline' class='decline' style='margin-left: 5px' name='decline[" . $row["index"] . "]'>";
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
            </form>
        </div>
    </main>
</body>
</html>