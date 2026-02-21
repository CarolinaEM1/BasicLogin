<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Token.php';

class UserResource
{
    private $db;
    private $user;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // 🔐 FUNCIÓN DE AUTENTICACIÓN (DENTRO DE LA CLASE)
    private function checkAuth()
    {
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

    // GET /api/v1/users
    public function index()
    {
        $this->checkAuth(); // 🔐 proteger

        header("Content-Type: application/json");

        $stmt = $this->user->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array(
                    "id" => $id,
                    "name" => $name,
                    "email" => $email,
                    "created_at" => $created_at
                );
                array_push($users_arr["records"], $user_item);
            }

            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
    }

    // GET /api/v1/users/{id}
    public function show($id)
    {
        $this->checkAuth();

        header("Content-Type: application/json");

        $this->user->id = $id;

        if ($this->user->readOne()) {
            $user_arr = array(
                "id" => $this->user->id,
                "name" => $this->user->name,
                "email" => $this->user->email,
                "created_at" => $this->user->created_at
            );

            http_response_code(200);
            echo json_encode($user_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Usuario no encontrado"));
        }
    }

    // POST /api/v1/users
    public function store()
    {
        $this->checkAuth();

        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->name) && !empty($data->email)) {
            $this->user->name = $data->name;
            $this->user->email = $data->email;

            if ($this->user->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "message" => "Usuario creado exitosamente",
                    "id" => $this->user->id
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el usuario"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos"));
        }
    }

    // PUT /api/v1/users/{id}
    public function update($id)
    {
        $this->checkAuth();

        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        $this->user->id = $id;

        if (!empty($data->name) && !empty($data->email)) {
            $this->user->name = $data->name;
            $this->user->email = $data->email;

            if ($this->user->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Usuario actualizado exitosamente"));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo actualizar el usuario"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos"));
        }
    }

    // DELETE /api/v1/users/{id}
    public function destroy($id)
    {
        $this->checkAuth();

        header("Content-Type: application/json");

        $this->user->id = $id;

        if ($this->user->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Usuario eliminado exitosamente"));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "No se pudo eliminar el usuario"));
        }
    }
}
?>