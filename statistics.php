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
          <li class="navelement"><a href="index.html" class="navlink">Home</a></li>
          <li class="navelement"><a href="vacation.html" class="navlink">Vacation Planner</a> </li>
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

    $sql = "SELECT time_started, -
                   CASE WHEN time_started > 8 THEN time_started - 8 ELSE 0 END AS overtime_hours,
                   DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') AS current_date, 
                   DAYNAME(CURRENT_DATE) AS weekday 
            FROM time_management";
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
            <tr><td>Eintrag 1</td><td>Eintrag 2</td><td>Eintrag 3</td></tr>
            <tr><td>Eintrag 4</td><td>Eintrag 5</td><td>Eintrag 6</td></tr>
            <tr><td>Eintrag 7</td><td>Eintrag 8</td><td>Eintrag 9</td></tr>
            <tr><td>Eintrag 10</td><td>Eintrag 11</td><td>Eintrag 12</td></tr>
            <tr><td>Eintrag 13</td><td>Eintrag 14</td><td>Eintrag 15</td></tr>
            <tr><td>Eintrag 16</td><td>Eintrag 17</td><td>Eintrag 18</td></tr>
            <tr><td>Eintrag 19</td><td>Eintrag 20</td><td>Eintrag 21</td></tr>
            <tr><td>Eintrag 22</td><td>Eintrag 23</td><td>Eintrag 24</td></tr>
            <tr><td>Eintrag 25</td><td>Eintrag 26</td><td>Eintrag 27</td></tr>
            <tr><td>Eintrag 28</td><td>Eintrag 29</td><td>Eintrag 30</td></tr>
          </tbody>
        </table>
    </div>
</body>
</html>
