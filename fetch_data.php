<?php
session_start();

class MessageFetcher {
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

    public function fetchMessages() {
        try {
            $sql = "SELECT * FROM `message`";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $messages]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => "Ошибка получения сообщений: " . $e->getMessage()]);
        }
    }

    public function __destruct() {
        $this->conn = null; 
    }
}

$messageFetcher = new MessageFetcher('localhost', 'db_test', 'root', 'Milana0909!');
$messageFetcher->fetchMessages();
?>
