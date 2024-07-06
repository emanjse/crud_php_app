<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\Post;

$database = new Database();
$createPostController = new CreatePostController($database);

$createPostController->createPost();

class CreatePostController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function createPost()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['POST']);

        $data = Utils::parseJsonInput();
        if (!isset($_SESSION['user_id']) || empty($data->title) || empty($data->content)) {
            Utils::respondWithError(400, "Incomplete data.");
        }

        $post = new Post($this->db);
        $post->user_id = $_SESSION['user_id'];
        $post->title = $data->title;
        $post->content = $data->content;
        $post->created_at = date('Y-m-d H:i:s');

        if ($post->create()) {
            Utils::respondWithSuccess(201, "Post created.");
        } else {
            Utils::respondWithError(400, "Unable to create post.");
        }
    }
}

?>