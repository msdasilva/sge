<?php

    include_once '../config/database.php';

    class Aluno {
    
        // database connection and table name
        private $conn;
        private $table_name = "categoria";
    
        // object properties
        public $id;
        public $nome;
        public $descricao;
        public $ativo;
        public $cricacao;
        public $modificacao;
    
        // constructor with $db as database connection
        public function __construct() {

            $database = new Database();
            $this->conn = $database->getConnection();
            
        }

        // findAll products
        public function findAll() {
        
            // select all query
            $query = "SELECT p.id, p.nome, p.descricao, p.ativo, p.cricacao, p.modificacao ";
            $query .= "FROM " . $this->table_name . " p ";
            $query .= "ORDER BY p.cricacao DESC";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // execute query
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function find($id) {
        
            // select all query
            $query = "SELECT p.id, p.nome, p.descricao, p.ativo, p.cricacao, p.modificacao ";
            $query .= "FROM " . $this->table_name . " p ";
            $query .= "WHERE id = ? ";
            $query .= "ORDER BY p.cricacao DESC";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            // execute query
            $stmt->execute();
        
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // create product
        public function insert($objeto) {

            // query to insert record
            $query = "INSERT INTO ";
            $query .= " " . $this->table_name . " ";
            $query .= "SET nome=:nome, ativo=:ativo, descricao=:descricao, cricacao=:cricacao ";
        
            // prepare query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->nome = htmlspecialchars(strip_tags($objeto['nome']));
            $this->ativo = htmlspecialchars(strip_tags($objeto['ativo']));
            $this->descricao = htmlspecialchars(strip_tags($objeto['descricao']));
            $this->cricacao = date('Y-m-d H:i:s');
        
            // bind values
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":ativo", $this->ativo);
            $stmt->bindParam(":descricao", $this->descricao);
            $stmt->bindParam(":cricacao", $this->cricacao);
        
            // execute query
            if($stmt->execute()){
                return true;
            }
        
            return false;
            
        }

        public function update($id, $objeto) {

            $query = "UPDATE ";
            $query .= " " . $this->table_name . " ";
            $query .= "SET nome=:nome, ativo=:ativo, descricao=:descricao, modificacao=:modificacao ";
            $query .= "WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $this->nome = htmlspecialchars(strip_tags($objeto['nome']));
            $this->ativo = htmlspecialchars(strip_tags($objeto['ativo']));
            $this->descricao = htmlspecialchars(strip_tags($objeto['descricao']));
            $this->modificacao = date('Y-m-d H:i:s');
        
            // bind values
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":ativo", $this->ativo);
            $stmt->bindParam(":descricao", $this->descricao);
            $stmt->bindParam(":modificacao", $this->modificacao);
            $stmt->bindParam(":id", $id);

            if($stmt->execute()) {
                return $stmt->rowCount();
            }
               
        }

        public function delete($id) {

            $query = "DELETE FROM " . $this->table_name . " ";
            $query .= "WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            if($stmt->execute()){
                return $stmt->rowCount();
            }
               
        }
}
