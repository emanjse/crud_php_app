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


$stmt = $user->getAllUsers();
$num = $stmt->rowCount();

// Check if any users exist
if ($num > 0) {
    $users_arr = array();
    $users_arr["users"] = array();

    // Retrieve user rows
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $user_item = array(
            "id" => $id,
            "username" => $username,
            "address" => $address,
            "age" => $age,
            "email" => $email

        );


        array_push($users_arr["users"], $user_item);
    }


    http_response_code(200);


    echo json_encode($users_arr);
} else {
    // No users found
    http_response_code(404);
    echo json_encode(
        array("message" => "No users found.")
    );
}
?>