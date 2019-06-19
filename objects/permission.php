<?php
    class UserPermission {
        private $conn;
        private $table_name = 'user_permission';

        public $moduleId;
        public $moduleName;
        public $create;
        public $update;
        public $read;
        public $delete;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function change($op) {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        `{$op}` = JSON_MERGE_PATCH(`{$op}`, ?)
                    WHERE
                        moduleId = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->create);
            $stmt->bindParam(2, $this->moduleId);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read() {
            $query = "SELECT * FROM {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readByModule() {
            $query = "SELECT * FROM {$this->table_name} WHERE moduleId = ? LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->moduleId);

            $stmt->execute();

            return $stmt;
        }
    }
?>