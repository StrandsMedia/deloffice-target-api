<?php
    class ProformaHistory {
        private $conn;
        private $table_name = 'proforma_history';

        public $history_id;
        public $workflow_id;
        public $time;
        public $note;
        public $comment;
        public $user;
        public $step;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        workflow_id = :workflow_id,
                        time = CURRENT_TIMESTAMP,
                        note = :note,
                        comment = :comment,
                        user = :user,
                        step = :step;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':workflow_id', $this->workflow_id);
            $stmt->bindParam(':note', $this->note);
            $stmt->bindParam(':comment', $this->comment);
            $stmt->bindParam(':user', $this->user);
            $stmt->bindParam(':step', $this->step);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function readByWF($workflow_id) {
            $query = "SELECT
                        a.*, b.step as 'stepname', c.sales_rep
                    FROM
                        {$this->table_name} a, proforma_steps b, sales_representative c
                    WHERE
                        a.step = b.step_id
                    AND
                        a.user = c.sales_id
                    AND
                        a.workflow_id = ?;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $workflow_id);

            $stmt->execute();

            return $stmt;
        }

        function findUser($workflow_id) {
            $query = "SELECT
                        a.*, b.step as 'stepname', c.sales_rep
                    FROM
                        {$this->table_name} a, proforma_steps b, sales_representative c
                    WHERE
                        a.step = b.step_id
                    AND
                        a.user = c.sales_id
                    AND
                        a.workflow_id = ?
                    ORDER BY
                        a.history_id DESC
                    LIMIT 0,1;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $workflow_id);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['sales_rep'];
        }
    }

    class ProformaSteps {
        private $conn;
        private $table_name = 'proforma_steps';

        public $step_id;
        public $step;

        public function __construct($db) {
            $this->conn = $db;
        }
    }
?>