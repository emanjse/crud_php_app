<?php

namespace Utils;
require_once __DIR__ . '/../vendor/autoload.php';
class Utils
{
    public static function setHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
    }

    public static function respondWithError($code, $message)
    {
        http_response_code($code);
        echo json_encode(array("message" => $message));
        exit;
    }

    public static function respondWithSuccess($code, $message, $data = [])
    {
        http_response_code($code);
        echo json_encode(array_merge(["message" => $message], $data));
        exit;
    }

    public static function validateRequestMethod($allowedMethods)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (!in_array($method, $allowedMethods)) {
            self::respondWithError(405, "Method Not Allowed.");
        }
    }

    public static function parseJsonInput()
    {
        $data = json_decode(file_get_contents("php://input"));
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::respondWithError(400, "Invalid JSON format.");
        }
        return $data;
    }

    public static function getPaging($page, $total_rows, $records_per_page, $page_url)
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
}

?>