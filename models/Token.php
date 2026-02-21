<?php
require_once __DIR__ . '/../config/database.php';

class Token {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "INSERT INTO api_tokens (user_id, token, expires_at)
                  VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $token, $expiresAt]);

        return [
            "access_token" => $token,
            "expires_at" => $expiresAt
        ];
    }

    public function validate($token) {
        $query = "SELECT * FROM api_tokens
                  WHERE token = ?
                  AND revoked = 0
                  AND expires_at > NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>