<?php
session_start();
if (!isset($_SESSION["id"])) {
    http_response_code(403);
    echo json_encode(["error" => "No access"]);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$borrower_email = $_SESSION["email"];
$id_query = "SELECT ID from borrower WHERE E_mail = ?";
$stmt = $conn->prepare($id_query);
$stmt->bind_param("s", $borrower_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$borrower_id = $row['ID'];

// Get unread count
$count_query = "SELECT COUNT(*) as unread_count FROM borrowed 
                WHERE Borrower_ID = ? AND is_read = 0";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $borrower_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["unread_count" => $row['unread_count']]);

$conn->close();
?>