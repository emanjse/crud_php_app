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
if (!$post->getPostById()) {
    http_response_code(404);
    echo json_encode(array("message" => "Post does not exist."));
    exit();
}

$data = json_decode(file_get_contents("php://input"));

// Validate input data
if (!isset($data->title) || !isset($data->content)) {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
    exit();
}


$post->title = $data->title;
$post->content = $data->content;


$updateResult = $post->update();

if ($updateResult === 'not_owner') {
    http_response_code(403);
    echo json_encode(array("message" => "You can't update this post."));
} elseif ($updateResult === 'not_exist') {
    http_response_code(404);
    echo json_encode(array("message" => "Post does not exist."));
} elseif ($updateResult === true) {
    http_response_code(200);
    echo json_encode(array("message" => "Post updated successfully."));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to update post."));
}
?>