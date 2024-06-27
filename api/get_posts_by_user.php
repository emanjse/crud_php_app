<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/Database.php';
include_once '../models/Post.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$post = new Post($db);
$user = new User($db);

$user_id = isset($_GET['id']) ? $_GET['id'] : die();

// Check if the user exists
$user->id = $user_id;
if (!$user->getUserById()) {
    http_response_code(404);
    echo json_encode(array("message" => "User does not exist."));
    exit();
}

$stmt = $post->getPostsByUser($user_id);
$num = $stmt->rowCount();

if ($num > 0) {
    $posts_arr = array();
    $posts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $post_item = array(
            "id" => $id,
            "user_id" => $user_id,
            "title" => $title,
            "content" => html_entity_decode($content),
            "created_at" => $created_at
        );

        array_push($posts_arr["records"], $post_item);
    }

    http_response_code(200);
    echo json_encode($posts_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No posts found for this user."));
}
?>