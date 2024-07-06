<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Utils\Utils;
use Models\Post;
use Models\User;

$database = new Database();
$getPostsByUserController = new GetPostsByUserController($database);

$getPostsByUserController->getPostsByUser();

class GetPostsByUserController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getPostsByUser()
    {
        Utils::setHeaders();
        Utils::validateRequestMethod(['GET']);

        $user_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$user_id) {
            Utils::respondWithError(400, "User ID is required.");
        }

        $user = new User($this->db);
        $user->id = $user_id;
        if (!$user->getUserById()) {
            Utils::respondWithError(404, "User does not exist.");
        }

        $post = new Post($this->db);
        $stmt = $post->getPostsByUser($user_id);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $posts_arr = array();
            $posts_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $post_item = array(
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "title" => $row['title'],
                    "content" => html_entity_decode($row['content']),
                    "created_at" => $row['created_at']
                );

                array_push($posts_arr["records"], $post_item);
            }

            Utils::respondWithSuccess(200, "Posts found.", $posts_arr);
        } else {
            Utils::respondWithError(404, "No posts found for this user.");
        }
    }
}

?>