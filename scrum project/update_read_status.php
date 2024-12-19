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

$book_id = isset($_POST['book_id']) ? $_POST['book_id'] : null;
$borrower_email = $_SESSION["email"];

if (!$book_id) {
    http_response_code(400);
    echo json_encode(["error" => "Book ID is required"]);
    exit;
}

$id_query = "SELECT ID from borrower WHERE E_mail = ?";
$stmt = $conn->prepare($id_query);
$stmt->bind_param("s", $borrower_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$borrower_id = $row['ID'];

$update_query = "UPDATE borrowed SET is_read = 1 
                 WHERE Book_ID = ? AND Borrower_ID = ? AND is_read = 0";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("ii", $book_id, $borrower_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "No update needed or record not found"]);
}

$conn->close();
?>