<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Utils\Utils;
use Config\Database;
use Models\User;

$database = new Database();
$RegisterController = new RegisterController($database);

$RegisterController->register();


class RegisterController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function register()
    {
        session_start();
        Utils::setHeaders();
        Utils::validateRequestMethod(['POST']);

        $user = new User($this->db);
        $data = Utils::parseJsonInput();

        if (
            !isset($data->username) ||
            !isset($data->address) ||
            !isset($data->age) ||
            !isset($data->email) ||
            !isset($data->password)
        ) {
            Utils::respondWithError(400, "Incomplete data.");
        }

        $user->username = $data->username;
        $user->address = $data->address;
        $user->age = $data->age;
        $user->email = $data->email;
        $user->password = $data->password;

        if (!$user->isValidEmail($user->email)) {
            Utils::respondWithError(400, "Email format is not right.");
        }

        if ($user->register()) {
            Utils::respondWithSuccess(201, "User created.");
        } else {
            Utils::respondWithError(400, "Unable to create user. Invalid data.");
        }
    }
}
?>