<?php
    class User {
        private $conn;
        private $table_name = 'sales_representative';

        public $sales_id;
        public $sales_rep;
        public $rep_initial;
        public $dept;
        public $visible;
        public $status;
        public $password;

        public function __construct($db) {
            $this->conn = $db;
        }

        function createUser() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        sales_rep = :sales_rep,
                        rep_initial = :rep_initial,
                        dept = :dept,
                        password = :password,
                        visible = :visible,
                        status = :status;";

            $stmt = $this->conn->prepare($query);

            $this->sales_rep = htmlspecialchars(strip_tags($this->sales_rep));
            $this->password = htmlspecialchars(strip_tags($this->password));

            $stmt->bindParam(':sales_rep', $this->sales_rep, PDO::PARAM_STR);
            $stmt->bindParam(':rep_initial', $this->rep_initial, PDO::PARAM_STR);
            $stmt->bindParam(':dept', $this->dept, PDO::PARAM_STR);
            $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
            $stmt->bindParam(':visible', $this->visible, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }

        function login() {
            $query = "SELECT * FROM " . $this->table_name . " WHERE sales_rep=:username AND password=:password;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":username", $this->sales_rep);
            $stmt->bindParam(":password", $this->password);

            $stmt->execute();

            return $stmt;
        }

        function getUser($id) {
            $query = "SELECT sales_rep FROM " . $this->table_name . " WHERE sales_id = '" . $id . "';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $this->sales_rep = $id;

            if ($stmt->rowCount() == 1) {

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                $this->sales_rep = $row['sales_rep'];
            } else {
                $this->sales_rep = 'rep';
            }
        }

        function getUsers() {
            $query = "SELECT * FROM {$this->table_name} WHERE status = 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getActiveUsers() {
            $query = "SELECT * FROM {$this->table_name} WHERE status = 0 AND visible = 1;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function updateUser() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        sales_rep = :sales_rep,
                        rep_initial = :rep_initial,
                        dept = :dept,
                        password = :password,
                        visible = :visible,
                        status = :status
                    WHERE
                        sales_id = :sales_id;";

            $stmt = $this->conn->prepare($query);

            $this->sales_rep = htmlspecialchars(strip_tags($this->sales_rep));
            $this->password = htmlspecialchars(strip_tags($this->password));

            $stmt->bindParam(':sales_rep', $this->sales_rep, PDO::PARAM_STR);
            $stmt->bindParam(':rep_initial', $this->rep_initial, PDO::PARAM_STR);
            $stmt->bindParam(':dept', $this->dept, PDO::PARAM_STR);
            $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
            $stmt->bindParam(':visible', $this->visible, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);
            $stmt->bindParam(':sales_id', $this->sales_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
?>