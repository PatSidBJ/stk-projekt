<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stk_orders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([]));
}

$datum = $_GET['datum'] ?? '';

if (empty($datum)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT cas FROM orders WHERE datum = ?");
$stmt->bind_param("s", $datum);
$stmt->execute();
$result = $stmt->get_result();

$taken = [];
while ($row = $result->fetch_assoc()) {
    $taken[] = $row['cas'];
}

echo json_encode($taken);

$stmt->close();
$conn->close();
?>