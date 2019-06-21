<?php
    class Company {
        private $conn;
        private $table_name = 'companies';

        public $companyId;
        public $companyName;
        public $companyReference;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT 
                        {$this->table_name}
                      SET
                      `companyName`=:companyName, `companyReference`=:companyReference;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":companyName", $this->companyName);
            $stmt->bindParam(":companyReference", $this->companyReference);

            if ($stmt->execute()) {
                return true;
            };

            return false;
        }

        function read($companyId) {
            $condition = "";

            if (isset($companyId)) {
                $condition = "WHERE companyId = {$companyId}";
            } 
            $query = "SELECT *
                        FROM {$this->table_name} {$condition} ORDER BY createdAt ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }
?>