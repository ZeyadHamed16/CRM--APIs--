<?php

// ----
// -------- DATABASE : CRM
// ----

// ---------------------------------

class DatabaseConnection
{
    private $dsn = "mysql:host=localhost;dbname=CRM";
    private $username = "root";
    private $password = "";
    private $db_connection;

    public function connect()
    {
        $this->db_connection = null;
        try {
            $this->db_connection = new PDO($this->dsn, $this->username, $this->password);
            $this->db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            return false;
        }
        return $this->db_connection;
    }
}

$database = new DatabaseConnection;
$conn = $database->connect();