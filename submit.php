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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => "Заполните ВСЕ поля!"]);
        exit();
    }

    // Регулярное выражение для проверки ФИО
    $namePattern = "/^[А-ЯЁ][а-яё]+(\s+[А-ЯЁ][а-яё]+){2}$/u"; // Проверка на ФИО

    // Проверка имени
    if (!preg_match($namePattern, $name)) {
        echo json_encode(['success' => false, 'message' => "Неверный формат ФИО."]);
        exit();
    }

    // Проверка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => "Неверный формат email."]);
        exit();
    }

    try {
        $send = $conn->prepare("INSERT INTO `message`(`name`, `email`, `message`) VALUES (:name, :email, :message)");
        $send->bindParam(':name', $name);
        $send->bindParam(':email', $email);
        $send->bindParam(':message', $message); 

        if ($send->execute()) {
            echo json_encode(['success' => true, 'message' => ""]);
            exit();
 } else {
            echo json_encode(['success' => false, 'message' => "Ошибка отправления сообщения."]);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Ошибка отправления сообщения: " . $e->getMessage()]);
        exit();
    }
}

$conn = null; 
?>
