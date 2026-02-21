<?php
require_once __DIR__ . '/../../models/Token.php';
require_once __DIR__ . '/../../models/product.php';

class ProductResource {

    private $product;

    public function __construct() {
        $this->product = new Product();
    }

    // 🔐 FUNCIÓN DE AUTENTICACIÓN (DENTRO DE LA CLASE)
    private function checkAuth() {

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["message" => "Token requerido"]);
            exit;
        }

        $token = $matches[1];
        $tokenModel = new Token();

        if (!$tokenModel->validate($token)) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido o expirado"]);
            exit;
        }
    }

    public function index() {
        $this->checkAuth(); // 🔐 proteger
        echo json_encode($this->product->getAll());
    }

    public function show($id) {
        $this->checkAuth();
        echo json_encode($this->product->getById($id));
    }

    public function store() {
        $this->checkAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        $this->product->create($data);
        echo json_encode(["message" => "Producto creado"]);
    }

    public function update($id) {
        $this->checkAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        $this->product->update($id, $data);
        echo json_encode(["message" => "Producto actualizado"]);
    }

    public function destroy($id) {
        $this->checkAuth();
        $this->product->delete($id);
        echo json_encode(["message" => "Producto eliminado"]);
    }
}
?>