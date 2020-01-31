<?php
    class Comment {
        public $conn;
        public $table_name;

        public $cd_id;
        public $cust_id;
        public $comment;
        public $date;
        public $user;
        public $taskBy;
        public $date2;
        public $data;

        public $date0;
        public $date1;

        public $interactionType;
        public $interactionOutcome;
        
        public function __construct($db) {
            $this->conn = $db;
        }

        function read($optional) {
            $condition = "";

            if ($optional['user']) {
                $condition = " AND a.user = {$optional['user']} ";
            } else {
                $condition = $condition . "";
            }
            if (isset($optional['cust'])) {
                $condition = " AND a.cust_id = {$optional['cust']} AND a.data = {$optional['data']} ";
            } else {
                $condition = $condition . "";
            }

            $query = "SELECT 
                        a.*, c.sales_rep, c.dept
                    FROM
                        {$this->table_name} a, sales_representative c 
                    WHERE
                        a.user = c.sales_id{$condition}
                    ORDER BY
                        a.cd_id DESC LIMIT 0, 100;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function report() {
            $query = "SELECT
                        a.cd_id, a.cust_id, a.comment, a.date, a.user, a.date2, a.taskBy, a.data, c.sales_rep, c.dept 
                    FROM
                        {$this->table_name} a, sales_representative c
                    WHERE
                        a.user = c.sales_id
                    AND
                        DATE(a.date2) BETWEEN ? AND ?
                    AND
                        a.user = ?
                    ORDER BY 
                        a.cd_id DESC
                    LIMIT 0, 100;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->date0);
            $stmt->bindParam(2, $this->date1);
            $stmt->bindParam(3, $this->user);

            $stmt->execute();

            return $stmt;
        }

        public function insertComment() {
            if (isset($this->interactionType)) {
                $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        comment = :comment,
                        date = :date,
                        user = :user,
                        data = :data,
                        interactionType = :interactionType,
                        interactionOutcome = :interactionOutcome;";
            } else {
                $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        comment = :comment,
                        date = :date,
                        user = :user,
                        data = :data;";
            }
            
            
            $stmt = $this->conn->prepare($query);

            $this->comment = strip_tags($this->comment);

            $stmt->bindParam(":cust_id", $this->cust_id);
            $stmt->bindParam(":comment", $this->comment);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":user", $this->user);
            $stmt->bindParam(":data", $this->data);

            if (isset($this->interactionType)) {
                $stmt->bindParam(":interactionType", $this->interactionType);
                $stmt->bindParam(":interactionOutcome", $this->interactionOutcome);
            }

            if ($stmt->execute()) {
                return true;
            }

            return false; 
        }
        
        public function readLastComment($custid) {
            $query = "SELECT * FROM {$this->table_name} a WHERE a.cust_id = ? ORDER BY a.date DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $custid);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['comment'];
        }
    }

    class SalesComment extends Comment {
        public $table_name = 'comment_date';

        public function readCount($user) {
            $query = "SELECT * FROM {$this->table_name} WHERE user = {$user} AND DATE(date2) BETWEEN DATE('{$this->date0}') AND DATE('{$this->date1}');";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }       
    }

    class DebtorsComment extends Comment {
        public $table_name = 'comment_date2';
    }
?>