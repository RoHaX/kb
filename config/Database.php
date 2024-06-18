<?php
class Database
{

    private $host;
    private $user;
    private $password;
    private $database;
    public $conn;
	
    public function __construct(){
        $config = include('config.php');
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->database = $config['database'];
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->user, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

?>
