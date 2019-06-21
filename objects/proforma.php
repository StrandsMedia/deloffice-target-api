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