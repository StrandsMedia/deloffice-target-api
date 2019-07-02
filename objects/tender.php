<?php
    class Tender {
        private $conn;
        private $table_name = 'tender';

        public $tid;
        public $cust_id;
        public $product;
        public $estimated_quantity;
        public $schedule;
        public $receive_date;
        public $closing_date;
        public $actual_quantity;
        public $delivery;
        public $product_quoted;
        public $price_quoted;
        public $attachment;
        public $result;
        public $comments;
        public $status;
        public $createdAt;
        public $updatedAt;
        public $data;

        public $company_name;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        product = :product,
                        estimated_quantity = :estimated_quantity,
                        schedule = :schedule,
                        receive_date = :receive_date,
                        closing_date = :closing_date,
                        actual_quantity = :actual_quantity,
                        delivery = :delivery,
                        product_quoted = :product_quoted,
                        price_quoted = :price_quoted,
                        attachment = :attachment,
                        result = :result,
                        comments = :comments,
                        status = :status;";

            $stmt = $this->conn->prepare($query);

            $this->schedule = isset($this->schedule) ? date("Y-m-d H:i:s", strtotime($this->schedule)) : NULL;
            $this->receive_date = isset($this->receive_date) ? date("Y-m-d H:i:s", strtotime($this->receive_date)) : NULL;
            $this->closing_date = isset($this->closing_date) ? date("Y-m-d H:i:s", strtotime($this->closing_date)) : NULL;

            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':product', $this->product);
            $stmt->bindParam(':estimated_quantity', $this->estimated_quantity);
            $stmt->bindParam(':schedule', $this->schedule, PDO::PARAM_STR);
            $stmt->bindParam(':receive_date', $this->receive_date, PDO::PARAM_STR);
            $stmt->bindParam(':closing_date', $this->closing_date, PDO::PARAM_STR);
            $stmt->bindParam(':actual_quantity', $this->actual_quantity);
            $stmt->bindParam(':delivery', $this->delivery);
            $stmt->bindParam(':product_quoted', $this->product_quoted);
            $stmt->bindParam(':price_quoted', $this->price_quoted);
            $stmt->bindParam(':attachment', $this->attachment);
            $stmt->bindParam(':result', $this->result);
            $stmt->bindParam(':comments', $this->comments);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT, 1);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read() {
            $condition = "";

            if (isset($this->status)) {
                $condition = "AND a.status = {$this->status}";
            }

            if (isset($this->company_name)) {
                $company_name = htmlspecialchars(strip_tags($this->company_name));
                $company_name = "'%{$company_name}%'";
                $condition = "AND b.company_name LIKE {$company_name}";
            }

            $query = "SELECT a.*, a.data FROM {$this->table_name} a WHERE {$condition} ORDER BY a.schedule LIMIT 50;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readByCust() {
            $query = "SELECT
                        a.*, a.data
                    FROM
                        {$this->table_name} a
                    WHERE
                        a.cust_id = ?
                    ORDER BY
                        a.schedule DESC LIMIT 5;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id);

            $stmt->execute();

            return $stmt;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        product = :product,
                        estimated_quantity = :estimated_quantity,
                        schedule = :schedule,
                        receive_date = :receive_date,
                        closing_date = :closing_date,
                        actual_quantity = :actual_quantity,
                        delivery = :delivery,
                        product_quoted = :product_quoted,
                        price_quoted = :price_quoted,
                        attachment = :attachment,
                        result = :result,
                        comments = :comments,
                        status = :status
                    WHERE
                        tid = :tid;";

            $stmt = $this->conn->prepare($query);

            $this->schedule = isset($this->schedule) ? date("Y-m-d H:i:s", strtotime($this->schedule)) : NULL;
            $this->receive_date = isset($this->receive_date) ? date("Y-m-d H:i:s", strtotime($this->receive_date)) : NULL;
            $this->closing_date = isset($this->closing_date) ? date("Y-m-d H:i:s", strtotime($this->closing_date)) : NULL;

            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':product', $this->product);
            $stmt->bindParam(':estimated_quantity', $this->estimated_quantity);
            $stmt->bindParam(':schedule', $this->schedule, PDO::PARAM_STR);
            $stmt->bindParam(':receive_date', $this->receive_date, PDO::PARAM_STR);
            $stmt->bindParam(':closing_date', $this->closing_date, PDO::PARAM_STR);
            $stmt->bindParam(':actual_quantity', $this->actual_quantity);
            $stmt->bindParam(':delivery', $this->delivery);
            $stmt->bindParam(':product_quoted', $this->product_quoted);
            $stmt->bindParam(':price_quoted', $this->price_quoted);
            $stmt->bindParam(':attachment', $this->attachment);
            $stmt->bindParam(':result', $this->result);
            $stmt->bindParam(':comments', $this->comments);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT, 1);

            $stmt->bindParam(':tid', $this->tid);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class TenderAttachment {
        private $conn;
        private $table_name = 'tender_attachment';

        public $taid;
        public $tid;
        public $path;

        public function __construct($db) {
            $this->conn = $db;
        }

        function getPath($id) {
            $query = "SELECT * FROM {$this->table_name} WHERE tid = {$id};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->path = $row['path'];
            } else {
                $this->path = '';
            }
        }
    }
?>