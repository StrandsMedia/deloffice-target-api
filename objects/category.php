<?php
    class Category {
        public $conn;
        public $table_name = 'category';

        public $cat_id;
        public $category_name;
        public $abre;
        public $status;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function read() {
            $query = "SELECT * FROM {$this->table_name} ORDER BY cat_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }
    class Sector extends Category {
        public $table_name = 'sector';
    }
    class Subsector extends Category {
        public $table_name = 'subsector';

        public $upcat;
    }
?>