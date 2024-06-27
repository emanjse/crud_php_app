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


$user->id = isset($_GET['id']) ? $_GET['id'] : die();


if ($user->delete()) {

    http_response_code(200);
    echo json_encode(array("message" => "User deleted successfully."));
} else {
    if ($error = $user->getError()) {
        if ($error === "User does not exist.") {
            http_response_code(404);
        } else {
            http_response_code(503);
        }
        echo json_encode(array("message" => $error));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete user."));
    }
}
?>