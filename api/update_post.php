<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\Post;

$database = new Database();
$updatePostController = new UpdatePostController($database);

$updatePostController->updatePost();

class UpdatePostController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function updatePost()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['POST']);

        $post_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$post_id) {
            Utils::respondWithError(400, "Post ID is required.");
        }

        $post = new Post($this->db);
        $post->id = $post_id;
        if (!$post->getPostById()) {
            Utils::respondWithError(404, "Post does not exist.");
        }

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || !isset($data->content)) {
            Utils::respondWithError(400, "Incomplete data.");
        }

        $post->title = $data->title;
        $post->content = $data->content;

        $updateResult = $post->update();

        if ($updateResult === 'not_owner') {
            Utils::respondWithError(403, "You can't update this post.");
        } elseif ($updateResult === 'not_exist') {
            Utils::respondWithError(404, "Post does not exist.");
        } elseif ($updateResult === true) {
            Utils::respondWithSuccess(200, "Post updated successfully.");
        } else {
            Utils::respondWithError(500, "Unable to update post.");
        }
    }
}

?>