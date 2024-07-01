<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../config/Database.php';
include_once '../models/Post.php';

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized access."));
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];


if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Post ID is required."));
    exit;
}

$post_id = $_GET['id'];


$post->id = $post_id;

if ($post->delete($logged_in_user_id)) {
    http_response_code(200);
    echo json_encode(array("message" => "Post deleted."));
} else {
    http_response_code(403);
    echo json_encode(array("message" => "You are not allowed to delete this post or the post does not exist."));
}