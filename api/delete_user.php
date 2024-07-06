<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\User;

$database = new Database();
$deleteUserController = new DeleteUserController($database);

$deleteUserController->deleteUser();

class DeleteUserController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function deleteUser()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['DELETE']);

        if (!isset($_SESSION['user_id'])) {
            Utils::respondWithError(401, "Unauthorized access.");
        }

        $logged_in_user_id = $_SESSION['user_id'];

        $user_id = isset($_GET['id']) ? $_GET['id'] : die();

        $user = new User($this->db);
        $user->id = $user_id;

        if ($user->delete($logged_in_user_id)) {
            Utils::respondWithSuccess(200, "User deleted successfully.");
        } else {
            Utils::respondWithError(403, "You are not allowed to delete this user or the user does not exist.");
        }
    }
}
 ?>