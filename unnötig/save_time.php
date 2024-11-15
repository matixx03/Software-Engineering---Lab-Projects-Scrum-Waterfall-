<?php
header('Content-Type: application/json');
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "time_management";
$conn = new mysqli($servername, $username, $password, $dbname);
// Bei POST Anfrage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // liest die gesendeten Daten und wandelt sie in ein PHP-Array um
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("INSERT INTO day (date, weekday, time_started, time_ended, time_break, time_worked, comment) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssss", 
        $data['date'],
        $data['weekday'],
        $data['timeStarted'],
        $data['timeEnded'],
        $data['timeBreak'],
        $data['workedTime'],
        $data['comment']
    );
    // führt SQL Statement aus
    $stmt->execute();
    // schließt SQL Statement
    $stmt->close();
    
    echo json_encode(['success' => true]);
}
// Bei GET Anfrage
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // sortiert nach Datum und Startzeit
    $sql = "SELECT date, weekday, time_started, time_break, time_ended, time_worked, comment 
            FROM day 
            ORDER BY date DESC, time_started DESC";
    $result = $conn->query($sql);
    
    $entries = [];      // Array für die Ergebnisse
    // geht durch alle Ergebniszeilen und fügt jeden Eintrag dem Array hinzu
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $entries[] = [
                'date' => $row['date'],
                'weekday' => $row['weekday'],
                'timeStarted' => $row['time_started'],
                'timeEnded' => $row['time_ended'],
                'timeBreak' => $row['time_break'],
                'workedTime' => $row['time_worked'],
                'comment' => $row['comment']
            ];
        }
    }
    echo json_encode($entries);     // gibt die Einträge als JSON zurück
}
$conn->close();
?>