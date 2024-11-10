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
          <li class="navelement"><a href="home.php" class="navlink">Home</a></li>
          <li class="navelement"><a href="vacation.php" class="navlink">Vacation Planner</a> </li>
          <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a> </li>
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
                   CASE WHEN time_started > 8 THEN time_started - 8 ELSE 0 END AS overtime_hours,
                   DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') AS current_date, 
                   DAYNAME(CURRENT_DATE) AS weekday 
            FROM day";
    $result = $conn->query($sql);

   
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Datum</th><th>Wochentag</th><th>Stunden gearbeitet</th><th>Überstunden</th></tr>";
        while ($row = $result->fetch_assoc()) {
            if ($row["overtime_hours"] > 0) { 
                echo "<tr><td>" . $row["current_date"]. "</td><td>" . $row["weekday"] . "</td><td>" . $row["time_started"] . "</td><td>" . $row["overtime_hours"] . "</td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "Keine Daten gefunden.";
    }

    $conn->close();
    ?>

    <div class="table-container">
        <table>
          <thead>
            <tr>         
              <th>Date</th>
              <th>Weekday</th>
              <th>Begin</th>          
              <th>Break</th>
              <th>End</th>
              <th>Comments</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
    </div>
</body>
</html>
