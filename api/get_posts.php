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

// Define the base URL for pagination
$home_url = "http://localhost/php_crud_app/api/";

// Get pagination parameters
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = isset($_GET['per_page']) ? $_GET['per_page'] : 5;
$from_record_num = ($records_per_page * $page) - $records_per_page;

// Get posts
$stmt = $post->getAllPosts($from_record_num, $records_per_page);
$num = $stmt->rowCount();

if ($num > 0) {
    $posts_arr = array();
    $posts_arr["records"] = array();
    $posts_arr["paging"] = array();

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

    // Include paging
    $total_rows = $post->countAll();
    $page_url = "{$home_url}get_posts.php?";
    $paging = getPaging($page, $total_rows, $records_per_page, $page_url);
    $posts_arr["paging"] = $paging;

    http_response_code(200);
    echo json_encode($posts_arr);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No posts found.")
    );
}

function getPaging($page, $total_rows, $records_per_page, $page_url)
{
    $paging_arr = array();

    $total_pages = ceil($total_rows / $records_per_page);
    $paging_arr["total_pages"] = $total_pages;
    $paging_arr["current_page"] = $page;

    if ($page > 1) {
        $paging_arr["previous"] = "{$page_url}page=" . ($page - 1) . "&per_page=" . $records_per_page;
    }

    if ($page < $total_pages) {
        $paging_arr["next"] = "{$page_url}page=" . ($page + 1) . "&per_page=" . $records_per_page;
    }

    return $paging_arr;
}
?>