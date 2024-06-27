<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/Database.php';
include_once '../models/Post.php';

$database = new Database();
$db = $database->getConnection();

$post = new Post($db);

$data = json_decode(file_get_contents("php://input"));

// Check if JSON decoding failed
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid JSON data."));
    exit;
}

// Check if all required fields are present
if (
    !isset($data->user_id) ||
    !isset($data->title) ||
    !isset($data->content)
) {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
    exit;
}

// Assign values to Post object properties
$post->user_id = $data->user_id;
$post->title = $data->title;
$post->content = $data->content;
$post->created_at = date('Y-m-d H:i:s');

if ($post->create()) {
    http_response_code(201);
    echo json_encode(array("message" => "Post created."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create post."));
}
?>