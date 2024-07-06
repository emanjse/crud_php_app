<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Models\User;
use Utils\Utils;

$database = new Database();
$controller = new UpdateUserController($database);

$logged_in_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$data = Utils::parseJsonInput();

if ($logged_in_user_id) {
    $controller->updateUser($logged_in_user_id, $data);
} else {
    Utils::respondWithError(401, "Unauthorized.");
}

class UpdateUserController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function updateUser($logged_in_user_id, $data)
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['POST']);

        $user = new User($this->db);
        $user->id = $logged_in_user_id;

        if (!$user->getUserById()) {
            Utils::respondWithError(404, "User not found.");
        }

        $updateResult = $user->update($data);

        if ($updateResult['success']) {
            Utils::respondWithSuccess(200, $updateResult['message']);
        } else {
            Utils::respondWithError(400, $updateResult['message']);
        }
    }
}





?>