<?php

class ConexaoBD {
    private $host = 'localhost';
    private $db_name = 'frang566_frangonacaixa';
    private $username = "root"; //'frang566_neto';
    private $password = ""; //'Akjon2dx@';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
