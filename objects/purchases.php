<?php
    class PurchaseRequest {
        private $conn;
        private $table_name = 'purchase_req';

        public $req_id;
        public $cust_id;
        public $workflow_id;
        public $data;
        public $type;
        public $completed;
        public $createdAt;
        public $updatedAt;

        /**
         * Completed variable values
         * 0 means requested -> no action taken
         * 1 means requested and action taken
         * 2 means received and accepted
         * 3 means not received and rejected
        */

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = ?,
                        data = ?,
                        type = ?,
                        workflow_id = ?,
                        completed = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->data, PDO::PARAM_INT);
            $stmt->bindParam(3, $this->type, PDO::PARAM_INT);
            $stmt->bindParam(4, $this->workflow_id, PDO::PARAM_INT);
            $stmt->bindParam(5, $this->completed, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function getExisting() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        cust_id = ?
                    AND
                        completed = 0
                    AND
                        type = ?
                    ORDER BY
                        req_id DESC
                    LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id);
            $stmt->bindParam(2, $this->type);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row['req_id'];
            } else {
                return null;
            }
        }

        function read() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        completed = 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readEntries($p_id, $completed) {
            $query = "SELECT
                        a.*, b.*, d.invlineid
                    FROM
                        {$this->table_name} a, purchase_req_prods b, invoice c, invoice_lines d
                    WHERE
                        a.req_id = b.req_id
                    AND
                        a.workflow_id = c.workflow_id
                    AND
                        c.invoice_id = d.invoice_id
                    AND
                        d.p_id = b.p_id
                    AND
                        a.completed = ?
                    AND
                        b.p_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $completed);
            $stmt->bindParam(2, $p_id);

            $stmt->execute();

            return $stmt;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        completed = ?
                    WHERE
                        req_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->completed, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->req_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    class PurchaseRequestProds {
        private $conn;
        private $table_name = 'purchase_req_prods';

        public $reqprod_id;
        public $req_id;
        public $p_id;
        public $qty;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        req_id = ?,
                        p_id = ?,
                        qty = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->req_id);
            $stmt->bindParam(2, $this->p_id);
            $stmt->bindParam(3, $this->qty);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function readAllProd() {
            $query = "SELECT x.*, y.des1, y.des2, y.des3 FROM (SELECT a.p_id, COUNT(*) as `counter`, SUM(a.qty) as `total` FROM {$this->table_name} a, purchase_req b, invoice c, invoice_lines d WHERE a.req_id = b.req_id AND c.invoice_id = d.invoice_id AND c.workflow_id = b.workflow_id AND b.completed != 2 AND a.p_id = d.p_id GROUP BY a.p_id) x, products y WHERE x.p_id = y.p_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function read() {
            $query = "SELECT
                        a.*, b.des1, b.des2, b.des3, b.p_id, c.cust_id, c.data, c.type, c.workflow_id, c.completed, e.purchasestatus, e.transferstatus, e.invlineid
                    FROM
                        {$this->table_name} a, products b, purchase_req c, invoice d, invoice_lines e
                    WHERE
                        a.p_id = b.p_id
                    AND
                        a.req_id = c.req_id
                    AND
                        c.workflow_id = d.workflow_id
                    AND
                        d.invoice_id = e.invoice_id
                    AND
                        e.p_id = a.p_id
                    AND
                        c.type = ?
                    AND
                        c.completed = ?
                    ORDER BY
                        c.createdAt DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->type);
            $stmt->bindParam(2, $this->completed);

            $stmt->execute();

            return $stmt;
        }

        function read2() {
            $query = "SELECT
                        a.*, b.des1, b.des2, b.des3, b.p_id, c.cust_id, c.data, c.type, c.workflow_id, c.completed, e.purchasestatus, e.transferstatus, e.invlineid
                    FROM
                        {$this->table_name} a, products b, purchase_req c, invoice d, invoice_lines e
                    WHERE
                        a.p_id = b.p_id
                    AND
                        a.req_id = c.req_id
                    AND
                        c.workflow_id = d.workflow_id
                    AND
                        d.invoice_id = e.invoice_id
                    AND
                        e.p_id = a.p_id
                    AND
                        c.type = ?
                    AND
                        c.completed = ?
                    ORDER BY
                        c.createdAt DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->type);
            $stmt->bindParam(2, $this->completed);

            $stmt->execute();

            return $stmt;
        }

        function readByProd($type) {
            $query = "SELECT
                        a.*, b.des1, b.des2, b.des3, b.p_id, c.cust_id, c.data, c.type, c.workflow_id, c.completed, e.invlineid
                    FROM
                        {$this->table_name} a, products b, purchase_req c, invoice d, invoice_lines e
                    WHERE
                        a.p_id = b.p_id
                    AND
                        a.req_id = c.req_id
                    AND
                        c.workflow_id = d.workflow_id
                    AND
                        d.invoice_id = e.invoice_id
                    AND
                        e.p_id = a.p_id
                    AND
                        a.p_id = ?
                    AND
                        c.type = ?
                    ORDER BY
                        c.createdAt DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->p_id);
            $stmt->bindParam(2, $type);

            $stmt->execute();

            return $stmt;
        }

        // function update() {
        //     $query = "UPDATE
        //                 {$this->table_name}
        //             SET

        //             WHERE
        //                 reqprod_id = ?;";

        //     $stmt = $this->conn->prepare($query);
        // }
    }

    class PurchaseInit {
        private $conn;
        private $table_name = 'purchase_init';

        public $init_id;
        public $p_id;
        public $qty;
        public $type;
        public $received;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        p_id = ?,
                        qty = ?,
                        type = ?,
                        received = 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->p_id);
            $stmt->bindParam(2, $this->qty);
            $stmt->bindParam(3, $this->type);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function read() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        received = 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        received = 1
                    WHERE
                        init_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->init_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    class PurchaseInitProds {
        private $conn;
        private $table_name = 'purchase_init_alloc';

        public $alloc_id;
        public $init_id;
        public $req_id;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        init_id = ?,
                        req_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->init_id);
            $stmt->bindParam(2, $this->req_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function read() {
            $query = "SELECT
                        a.*, b.cust_id, b.data, b.completed, c.qty
                    FROM
                        {$this->table_name} a, purchase_req b, purchase_req_prods c, purchase_init d
                    WHERE
                        b.req_id = c.req_id
                    AND
                        a.init_id = d.init_id 
                    AND
                        d.p_id = c.p_id
                    AND
                        a.init_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->init_id);

            $stmt->execute();

            return $stmt;
        }

        // function update() {
        //     $query = "UPDATE
        //                 {$this->table_name}
        //             SET
                        
        //             WHERE
        //                 alloc_id = ?;";

        //     $stmt = $this->conn->prepare($query);
        // }
    }
?>