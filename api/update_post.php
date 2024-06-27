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

$post_id = isset($_GET['id']) ? $_GET['id'] : die();
$data = json_decode(file_get_contents("php://input"));

$post->id = $post_id;

// Check if the post exists
if (!$post->getPostById()) {
    http_response_code(404);
    echo json_encode(array("message" => "Post does not exist."));
    exit();
}

// Check if user is authorized to update the post
if ($post->user_id != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(array("message" => "You are not authorized to update this post."));
    exit();
}

// Validate input data
if (!isset($data->title) || !isset($data->content)) {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
    exit();
}

$post->title = $data->title;
$post->content = $data->content;

if ($post->update()) {
    http_response_code(200);
    echo json_encode(array("message" => "Post updated successfully."));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to update post."));
}
?>