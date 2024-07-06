<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\Post;

$database = new Database();
$deletePostController = new DeletePostController($database);

$deletePostController->deletePost();

class DeletePostController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function deletePost()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['DELETE']);

        if (!isset($_SESSION['user_id'])) {
            Utils::respondWithError(401, "Unauthorized access.");
        }

        $logged_in_user_id = $_SESSION['user_id'];

        if (!isset($_GET['id'])) {
            Utils::respondWithError(400, "Post ID is required.");
        }

        $post_id = $_GET['id'];

        $post = new Post($this->db);
        $post->id = $post_id;

        if ($post->delete($logged_in_user_id)) {
            Utils::respondWithSuccess(200, "Post deleted.");
        } else {
            Utils::respondWithError(403, "You are not allowed to delete this post or the post does not exist.");
        }
    }
}

?>