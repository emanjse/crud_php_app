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

if (
    !empty($data->email) &&
    !empty($data->password)
) {
    $user->email = $data->email;
    $user->password = $data->password;

    if (!$user->isValidEmail($user->email)) {
        http_response_code(400);
        echo json_encode(array("message" => "Email format is not right."));
        exit;
    }

    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        http_response_code(200);
        echo json_encode(array("message" => "Login successful.", "user_id" => $user->id));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>