<?php
    class PrepTables {
        private $conn;
        private $table_name = 'prep_table';

        public $tableId;
        public $tableName;
        public $status;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $query = "SELECT * FROM {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function updateStatus() {
            $query = "UPDATE {$this->table_name} SET status = ? WHERE tableId = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->status);
            $stmt->bindParam(2, $this->tableId);

            if ($stmt->execute()){
                return true;
            } else {
                return false;
            }
        }
    }
?>