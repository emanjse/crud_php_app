<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/Database.php';
include_once '../models/Post.php';

$database = new Database();
$db = $database->getConnection();

$post = new Post($db);

$post->id = isset($_GET['id']) ? $_GET['id'] : die();

$stmt = $post->getPostById();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $post_item = array(
        "id" => $id,
        "user_id" => $user_id,
        "title" => $title,
        "content" => html_entity_decode($content),
        "created_at" => $created_at
    );

    http_response_code(200);
    echo json_encode($post_item);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Post not found."));
}
?>