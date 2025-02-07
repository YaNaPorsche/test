<?php
session_start();

class Feedback {
    private $conn;

    public function __construct($host, $db, $user, $pass) {
        $this->connect($host, $db, $user, $pass);
    }

    private function connect($host, $db, $user, $pass) {
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(['success' => false, 'message' => "Ошибка подключения: " . $e->getMessage()]));
        }
    }

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $message = trim($_POST['message']);

            $validationResult = $this->validate($name, $email, $message);
            if ($validationResult !== true) {
                echo json_encode(['success' => false, 'message' => $validationResult]);
                exit();
            }

            $this->sendMessage($name, $email, $message);
        }
    }

    private function validate($name, $email, $message) {
        if (empty($name) || empty($email) || empty($message)) {
            return "Заполните ВСЕ поля!";
        }

       
        $namePattern = "/^[А-ЯЁ][а-яё]+(\s+[А-ЯЁ][а-яё]+){2}$/u"; 

        if (!preg_match($namePattern, $name)) {
            return "Неверный формат ФИО.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Неверный формат email.";
        }

        return true;
    }

    private function sendMessage($name, $email, $message) {
        try {
            $send = $this->conn->prepare("INSERT INTO `message`(`name`, `email`, `message`) VALUES (:name, :email, :message)");
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

    public function __destruct() {
        $this->conn = null; 
    }
}
$feedback = new Feedback('localhost', 'db_test', 'root', 'Milana0909!');
$feedback->handleRequest();
?>