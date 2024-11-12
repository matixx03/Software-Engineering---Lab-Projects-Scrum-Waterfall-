<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="timetracking.css">
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
        <li class="navelement"><a href="sickdays.html" class="navlink">Sickday Planner</a> </li>
        <li class="navelement logout"><a href="login.php" class="navlink log">Log out</a> </li>

      </ul>
    </div>
    </nav>
    
    <main>
    <div class ="calbox">
      <p class="calp">your calendar</p>
    </div>

    <div class = buttonbox>
      <button type="button" class ="statsbutton"><a href="statistics.php">statistics</a></button>
    </div>

    <div class = buttonbox>
      <button type="button" class ="statsbutton"><a href="notifications.html">notifications</a></button>
    </div>

    <div class="status-box">
      <div id="timeStatus" class="time-status">
          not on the clock
      </div>

      <div class="flex-time">
          Overtime: 0:00
      </div>
    </div>

    <div class="control-panel">
      <div class="button-container"> 
        <button class="time-button" id="startButton">Start Working</button>
        <button class="time-button manual-entry-button" id="manualEntryButton">Manual Time Entry</button>
      </div>
      
      <div id="manualEntryForm" class="manual-entry-form hidden">
          <div class="form-row">
              <div class="form-group">
                  <label for="entryDate">Date:</label>
                  <input type="date" id="entryDate" class="form-input">
              </div>
              <div class="form-group">
                  <label for="startTime">Begin:</label>
                  <input type="time" id="startTime" class="form-input">
              </div>
              <div class="form-group">
                  <label for="endTime">End:</label>
                  <input type="time" id="endTime" class="form-input">
              </div>
              <div class="form-group">
                  <label for="breakTime">Pause (Minutes):</label>
                  <input type="number" id="breakTime" class="form-input" min="0" step="15" value="30">
              </div>
              <div class="form-group">
                  <label for="comment">Comment:</label>
                  <input type="text" id="comment" class="form-input">
              </div>
              <div class="form-group button-group">
                  <button class="time-button" id="saveManualEntry">Save</button>
                  <button class="time-button cancel-button" id="cancelManualEntry">Abort</button>
              </div>
          </div>
      </div>

      <div id="breakButtons">
          <button class="time-button break-button">30min Pause</button>
          <button class="time-button break-button">45min Pause</button>
      </div>
    </div>
    


        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weekday</th>
                        <th>Begin</th>
                        <th>Break</th>
                        <th>End</th>
                        <th>time_worked</th>
                        <th>Comments</th>
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
                        $date = $_POST['date'];
                        $time_started = $_POST['time_started'];
                        $time_break = $_POST['time_break'];
                        $time_ended = $_POST['time_ended'];
                        $comment = $_POST['comment'];
                        $dayofweek = date('l', strtotime($date));

                        $sql = "INSERT INTO day (date, weekday, time_started, time_break, time_ended, comment)
                                VALUES ('$date', '$dayofweek', '$time_started', '$time_break', '$time_ended', '$comment')";

                        if ($conn->query($sql) === TRUE) {
                            echo "New record created successfully";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    }

                    $sql = "SELECT * FROM day ORDER BY date";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["date"] . "</td>";
                            echo "<td>" . $row["weekday"] . "</td>";
                            echo "<td>" . $row["time_started"] . "</td>";
                            echo "<td>" . $row["time_break"] . "</td>";
                            echo "<td>" . $row["time_ended"] . "</td>";
                            echo "<td>" . $row["comment"] . "</td>";
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
    <script type="module" src="js/timeTracking.js"></script>
</body>
</html>