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
                <li class="navelement"><a href="index.html" class="navlink">Home</a></li>
                <li class="navelement"><a href="vacation.html" class="navlink">Vacation Planner</a></li>
                <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a></li>
            </ul>
        </div>
    </nav>
    <main>
        
        <div class="vacationbox">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="vacationform">
                vacation inquiry from  <input type="date" name="begin" class="vacainput" required>
                to  <input type="date" name="end" class="vacainput" required>
                <input type="submit" value="save" class="vacainput">
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        
                        <th>Begin</th>
                        <th>End</th>
                        <th>status</th>

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

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $begin = $_POST['begin'];
                        $end = $_POST['end'];
                        
                        $sql = "INSERT INTO vacation (begin, end)
                                VALUES ('$begin', '$end')";

                        if ($conn->query($sql) === TRUE) {
                            echo "New record created successfully";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
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