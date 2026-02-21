<?php
require_once __DIR__ . '/../../models/product.php';

class ProductResource {

    private $product;

    public function __construct() {
        $this->product = new Product();
    }

    public function index() {
        echo json_encode($this->product->getAll());
    }

    public function show($id) {
        echo json_encode($this->product->getById($id));
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->product->create($data);
        echo json_encode(["message" => "Producto creado"]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->product->update($id, $data);
        echo json_encode(["message" => "Producto actualizado"]);
    }

    public function destroy($id) {
        $this->product->delete($id);
        echo json_encode(["message" => "Producto eliminado"]);
    }
}
?>