<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Utils\Utils;
use Config\Database;
use Models\User;


$database = new Database();
$LoginController = new LoginController($database);

$LoginController->login();
class LoginController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function login()
    {
        session_start();
        Utils::setHeaders();
        Utils::validateRequestMethod(['POST']);

        $user = new User($this->db);
        $data = Utils::parseJsonInput();

        if (!empty($data->email) && !empty($data->password)) {
            $user->email = $data->email;
            $user->password = $data->password;

            if (!$user->isValidEmail($user->email)) {
                Utils::respondWithError(400, "Email format is not right.");
            }

            if ($user->login()) {
                $_SESSION['user_id'] = $user->id;
                Utils::respondWithSuccess(200, "Login successful.", ["user_id" => $user->id]);
            } else {
                Utils::respondWithError(401, "Login failed.");
            }
        } else {
            Utils::respondWithError(400, "Incomplete data.");
        }
    }
}

?>