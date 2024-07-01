<?php
class User
{
    private $conn;
    private $table_name = "users";
    private $error;

    public $id;
    public $username;
    public $address;
    public $age;
    public $email;
    public $password;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Validate email format
    public function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Validate required fields
    private function validateFields()
    {
        return !empty($this->username) && !empty($this->address) && !empty($this->age) && !empty($this->email) && !empty($this->password);
    }

    // Register a new user
    public function register()
    {
        if (!$this->validateFields()) {
            $this->error = "Incomplete data.";
            return false;
        }

        if (!$this->isValidEmail($this->email)) {
            $this->error = "Email format is not right.";
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (username, address, age, email, password) VALUES (:username, :address, :age, :email, :password)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":email", $this->email);

        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $hashed_password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    // Login user
    public function login()
    {
        if (empty($this->email) || empty($this->password) || !$this->isValidEmail($this->email)) {
            return false;
        }

        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $this->email);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->address = $row['address'];
            $this->age = $row['age'];
            return true;
        }
        return false;
    }

    public function getAllUsers()
    {

        $query = "SELECT id, username, address, age, email FROM " . $this->table_name;


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    // Get single user by ID
    public function getUserById()
    {

        $query = "SELECT id, username, address, age, email FROM users WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);


        $stmt->execute();

        // Check if user exists
        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set properties
            $this->username = $row['username'];
            $this->address = $row['address'];
            $this->age = $row['age'];
            $this->email = $row['email'];

            return true;
        }

        return false;
    }


    //Update user
    public function update()
    {
        // Check if the user exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the user doesn't exist
        if (!$row) {
            return 'not_exist';
        }

        // If the logged-in user is not the owner of the user data
        if ($row['id'] != $_SESSION['user_id']) {
            return 'not_owner';
        }

        $query = "UPDATE users SET username = :username, address = :address, age = :age, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    //Delete user
    public function delete($logged_in_user_id)
    {
        // Fetch user details to check ownership
        $query = "SELECT id FROM " . $this->table_name . " WHERE id = :id AND id = :logged_in_user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':logged_in_user_id', $logged_in_user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // User is the owner, proceed with deletion
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                return true;
            }
        }

        return false;
    }

}