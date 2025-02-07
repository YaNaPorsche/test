<?php
session_start();
$host = 'localhost'; 
$db = 'db_test'; 
$user = 'root'; 
$pass = 'Milana0909!'; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Ошибка подключения: " . $e->getMessage()]));
}

try {
    $sql = "SELECT * FROM `message`";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $messages]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Ошибка получения сообщений: " . $e->getMessage()]);
}

$conn = null; 
?>