<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = "productos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (nombre, precio, stock) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['nombre'], $data['precio'], $data['stock']]);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET nombre=?, precio=?, stock=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['nombre'], $data['precio'], $data['stock'], $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>