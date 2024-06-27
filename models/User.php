<?php
class User
{
    private $conn;
    private $table_name = "users";

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
    private function isValidEmail($email)
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
        if (!$this->validateFields() || !$this->isValidEmail($this->email)) {
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

        printf("Error: %s.\n", $stmt->error);

        return false;
    }

    //Delete user
    public function delete()
    {
        // Check if the user exists
        if (!$this->getUserById()) {
            $this->error = "User does not exist.";
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        $this->error = "Unable to delete user.";
        return false;
    }

    public function getError()
    {
        return $this->error;
    }

}