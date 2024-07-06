<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Models\Post;
use Utils\Utils;

$database = new Database();
$GetPostsController = new GetPostsController($database);

$GetPostsController->getPosts();
class GetPostsController
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getPosts()
    {
        session_start();
        Utils::setHeaders();
        Utils::validateRequestMethod(['GET']);

        $post = new Post($this->db);

        $home_url = "http://localhost/php_crud_app/api/";

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $records_per_page = isset($_GET['per_page']) ? $_GET['per_page'] : 5;
        $from_record_num = ($records_per_page * $page) - $records_per_page;

        $stmt = $post->getAllPosts($from_record_num, $records_per_page);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $posts_arr = array();
            $posts_arr["records"] = array();
            $posts_arr["paging"] = array();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
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

            $total_rows = $post->countAll();
            $page_url = "{$home_url}get_posts.php?";
            $paging = Utils::getPaging($page, $total_rows, $records_per_page, $page_url);
            $posts_arr["paging"] = $paging;

            http_response_code(200);
            echo json_encode($posts_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No posts found.")
            );
        }
    }
}
?>