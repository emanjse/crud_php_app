<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../config/Database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized access."));
    exit;
}


$logged_in_user_id = $_SESSION['user_id'];

// Check if user ID is provided
$user_id = isset($_GET['id']) ? $_GET['id'] : die();


$user->id = $user_id;


if ($user->delete($logged_in_user_id)) {
    http_response_code(200);
    echo json_encode(array("message" => "User deleted successfully."));
} else {
    http_response_code(403);
    echo json_encode(array("message" => "You are not allowed to delete this user or the user does not exist."));
}
?>