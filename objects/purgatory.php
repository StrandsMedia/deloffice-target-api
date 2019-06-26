<?php
    class Purgatory {
        private $conn;
        private $table_name = 'purgatory';

        public $entryId;
        public $invoice_id;
        public $invlineid;
        public $p_id;
        public $debit;
        public $credit;
        public $outstd;
        public $entryType;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT
                        {$this->table_name}
                    SET
                        invoice_id = :invoice_id,
                        invlineid = :invlineid,
                        p_id = :p_id,
                        debit = :debit,
                        credit = :credit,
                        outstd = :outstd,
                        entryType = :entryType;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':invoice_id', $this->invoice_id);
            $stmt->bindParam(':invlineid', $this->invlineid);
            $stmt->bindParam(':p_id', $this->p_id);
            $stmt->bindParam(':debit', $this->debit);
            $stmt->bindParam(':credit', $this->credit);
            $stmt->bindParam(':outstd', $this->outstd);
            $stmt->bindParam(':entryType', $this->entryType);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
        
        function read() {
            $query = "SELECT 
                        a.*, b.invRef, b.company_name, d.des1, d.des2, d.des3
                    FROM 
                        {$this->table_name} a, invoice b, invoice_lines c, products d
                    WHERE
                        a.invoice_id = b.invoice_id
                    AND
                        a.invlineid = c.invlineid
                    AND
                        a.p_id = d.p_id
                    AND
                        a.entryType = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->entryType, PDO::PARAM_INT);

            return $stmt;
        }
    }
?>