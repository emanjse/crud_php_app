<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/Database.php';
include_once '../models/User.php';


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized."));
    exit();
}


$logged_in_user_id = $_SESSION['user_id'];


$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id !== $logged_in_user_id) {
    http_response_code(403);
    echo json_encode(array("message" => "You can't update another user's data."));
    exit();
}


$database = new Database();
$db = $database->getConnection();
$user = new User($db);


$user->id = $logged_in_user_id;

// Check if the user exists
if (!$user->getUserById()) {
    http_response_code(404);
    echo json_encode(array("message" => "User not found."));
    exit();
}


$data = json_decode(file_get_contents("php://input"));

// Validate input data
if (empty($data->username) || empty($data->address) || empty($data->age) || empty($data->email)) {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
    exit();
}


$user->username = $data->username;
$user->address = $data->address;
$user->age = $data->age;
$user->email = $data->email;


$updateResult = $user->update();

if ($updateResult === 'not_owner') {
    http_response_code(403);
    echo json_encode(array("message" => "You can't update this user."));
} elseif ($updateResult === 'not_exist') {
    http_response_code(404);
    echo json_encode(array("message" => "User does not exist."));
} elseif ($updateResult === true) {
    http_response_code(200);
    echo json_encode(array("message" => "User information updated."));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to update user information."));
}
?>