<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\User;

$database = new Database();
$getUserById = new GetUserById($database);

$user_id = isset($_GET['id']) ? $_GET['id'] : die();

$getUserById->getUserById($user_id);

class GetUserById
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getUserById($user_id)
    {
        session_start();
        Utils::setHeaders();
        Utils::validateRequestMethod(['GET']);

        $user = new User($this->db);
        $user->id = $user_id;
        $user->getUserById();

        if ($user->username != null) {
            $user_arr = array(
                "id" => $user->id,
                "username" => $user->username,
                "address" => $user->address,
                "age" => $user->age,
                "email" => $user->email
            );

            http_response_code(200);
            echo json_encode($user_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "User does not exist."));
        }
    }
}
?>