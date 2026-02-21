<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Token.php';

class AuthResource {

    private $conn;
    private $tokenModel;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->tokenModel = new Token();
    }

    public function login() {

        $data = json_decode(file_get_contents("php://input"), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $query = "SELECT * FROM api_users WHERE username = ? AND status = 'ACTIVE'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(["message" => "Credenciales inválidas"]);
            return;
        }

        $tokenData = $this->tokenModel->create($user['id']);
        echo json_encode($tokenData);
    }
}
?>