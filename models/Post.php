<?php
class Post
{
    private $conn;
    private $table_name = "posts";

    public $id;
    public $user_id;
    public $title;
    public $content;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function validateFields()
    {
        return !empty($this->user_id) && !empty($this->title) && !empty($this->content);
    }

    // Create a new post
    public function create()
    {
        if (!$this->validateFields()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (user_id, title, content, created_at) VALUES (:user_id, :title, :content, :created_at)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllPosts($from_record_num, $records_per_page)
    {
        $query = "SELECT id, user_id, title, content, created_at FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?, ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Get total number of posts
    public function countAll()
    {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }

    // Get a single post by ID
    public function getPostById()
    {
        $query = "SELECT id, user_id, title, content, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Get all posts by a specific user
    public function getPostsByUser($user_id)
    {
        $query = "SELECT id, user_id, title, content, created_at FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Update a post
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET title = :title, content = :content WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

     // Delete a post
     public function delete()
     {
         $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':id', $this->id);
         if ($stmt->execute()) {
             return true;
         }
         return false;
     }
}
?>