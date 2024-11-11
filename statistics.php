<!DOCTYPE html>
<html lang="de">
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
          <li class="navelement"><a href="vacation.php" class="navlink">Vacation Planner</a> </li>
          <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a> </li>
          <li class="navelement"><a href="login.php" class="navlink">Log out</a> </li>
        </ul>
      </div>
    </nav>
    
    <main>
    <div class="calbox">
      <p class="calp">Your Calendar</p>
    </div>

    <?php 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "time_management";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT time_started,
    CASE 
        WHEN HOUR(time_started) > 8 
        THEN HOUR(time_started) - 8 
        ELSE 0 
    END AS overtime_hours,
    DATE_FORMAT(CURDATE(), '%Y-%m-%d') AS todays_date,
    DAYNAME(CURDATE()) AS weekday
    FROM day";
    $result = $conn->query($sql);

   
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Datum</th><th>Wochentag</th><th>Stunden gearbeitet</th><th>Überstunden</th></tr>";
        while ($row = $result->fetch_assoc()) {
            if ($row["overtime_hours"] > 0) { 
                echo "<tr><td>" . $row["todays_date"]. "</td><td>" . $row["weekday"] . "</td><td>" . $row["time_started"] . "</td><td>" . $row["overtime_hours"] . "</td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "Keine Daten gefunden.";
    }

    $conn->close();
    ?>
</body>
</html>
