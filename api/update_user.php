<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/Database.php';
include_once '../models/User.php';


$database = new Database();
$db = $database->getConnection();


$user = new User($db);


$user_id = isset($_GET['id']) ? $_GET['id'] : die();

// Check if user exist
$user->id = $user_id;
if (!$user->getUserById()) {
    http_response_code(404);
    echo json_encode(array("message" => "User not found."));
    exit;
}

$data = json_decode(file_get_contents("php://input"));

// Check if data is received and valid
if (
    !empty($data->username) &&
    !empty($data->address) &&
    !empty($data->age) &&
    !empty($data->email)
) {
    // Assign updated values
    $user->username = $data->username;
    $user->address = $data->address;
    $user->age = $data->age;
    $user->email = $data->email;


    if ($user->update()) {

        http_response_code(200);
        echo json_encode(array("message" => "User information updated."));
    } else {

        http_response_code(503);
        echo json_encode(array("message" => "Unable to update user information."));
    }
} else {

    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>