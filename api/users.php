<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Models\User;
use Utils\Utils;

$database = new Database();

$usersController = new UsersController($database);
$usersController->getUsers();

class UsersController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getUsers()
    {
        session_start();
        Utils::setHeaders();
        Utils::validateRequestMethod(['GET']);

        $user = new User($this->db);

        $stmt = $user->getAllUsers();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr["users"] = array();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);

                $user_item = array(
                    "id" => $id,
                    "username" => $username,
                    "address" => $address,
                    "age" => $age,
                    "email" => $email
                );

                array_push($users_arr["users"], $user_item);
            }

            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No users found.")
            );
        }
    }
}