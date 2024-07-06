<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\Post;

$database = new Database();
$getPostByIdController = new GetPostByIdController($database);

$getPostByIdController->getPostById();

class GetPostByIdController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getPostById()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['GET']);

        $post_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$post_id) {
            Utils::respondWithError(400, "Post ID is required.");
        }

        $post = new Post($this->db);
        $post->id = $post_id;

        $stmt = $post->getPostById();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $post_item = array(
                "id" => $row['id'],
                "user_id" => $row['user_id'],
                "title" => $row['title'],
                "content" => html_entity_decode($row['content']),
                "created_at" => $row['created_at']
            );

            Utils::respondWithSuccess(200, "Post found.", $post_item);
        } else {
            Utils::respondWithError(404, "Post not found.");
        }
    }
}

?>