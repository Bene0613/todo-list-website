<?php
class Database {
    private $conn;

    public function __construct(
        $host = "tramway.proxy.rlwy.net", 
        $user = "root", 
        $pass = "bueESodMuIeGggbVuKVzEDFdbefuJEgo", 
        $dbname = "railway",
        $port = 24724
    ) {
        $this->conn = new mysqli($host, $user, $pass, $dbname, $port);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function getError() {
        return $this->conn->error;
    }

    public function close() {
        $this->conn->close();
    }
}
?>
