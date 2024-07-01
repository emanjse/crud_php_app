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

$data = json_decode(file_get_contents("php://input"));


if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid JSON data."));
    exit;
}

// Check if all required fields are present
if (
    !isset($data->username) ||
    !isset($data->address) ||
    !isset($data->age) ||
    !isset($data->email) ||
    !isset($data->password)
) {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
    exit;
}

// Assign values to User object properties
$user->username = $data->username;
$user->address = $data->address;
$user->age = $data->age;
$user->email = $data->email;
$user->password = $data->password;

if (!$user->isValidEmail($user->email)) {
    http_response_code(400);
    echo json_encode(array("message" => "Email format is not right."));
    exit;
}

if ($user->register()) {
    http_response_code(201);
    echo json_encode(array("message" => "User created."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user. Invalid data."));
}
?>