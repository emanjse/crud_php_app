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
$post->id = $post_id;

// Check if the post exists
if (!$post->getPostById()) {
    http_response_code(404);
    echo json_encode(array("message" => "Post not found."));
    exit();
}

// Check if the user is authorized to delete the post
if (!isset($_SESSION['user_id']) || $post->user_id != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(array("message" => "You are not authorized to delete this post."));
    exit();
}

// Attempt to delete the post
if ($post->delete()) {
    http_response_code(200);
    echo json_encode(array("message" => "Post deleted successfully."));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to delete post."));
}
?>