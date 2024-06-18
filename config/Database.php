<?php
class Database{
    
    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct(){
        $config = include('config.php');
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->database = $config['database'];
    }

    public function getConnection(){       
        $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        $conn->set_charset("utf8mb4");
        if($conn->connect_error){
            die("Error failed to connect to MySQL: " . $conn->connect_error);
        } else {
            return $conn;
        }
    }
}
?>
