<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");


include_once '../config/Database.php';
include_once '../models/User.php';


$database = new Database();
$db = $database->getConnection();


$user = new User($db);


$user_id = isset($_GET['id']) ? $_GET['id'] : die();


$user->id = $user_id;


$user->getUserById();

// Check if user exist
if ($user->username != null) {
    $user_arr = array(
        "id" => $user->id,
        "username" => $user->username,
        "address" => $user->address,
        "age" => $user->age,
        "email" => $user->email

    );


    http_response_code(200);


    echo json_encode($user_arr);
} else {

    http_response_code(404);


    echo json_encode(array("message" => "User does not exist."));
}
?>