<?php
    class Printing {
        private $conn;
        private $table_name = 'printing_job';

        public $job_id;
        public $custid;
        public $product;
        public $printwork;
        public $startdate;
        public $enddate;
        public $status;
        public $jobdesc;
        public $paperspecs;
        public $filename;
        public $pc;
        public $printer;
        public $printsetting;
        public $qtyorder;
        public $qtyconsumed;
        public $qtycompleted;
        public $qtyrejected;
        public $remarks;
        public $printedby;
        public $supervisedby;
        public $deliverydate;
        public $dimensions;
        public $ppunit;
        public $data;

        public $company_name;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        custid = :custid,
                        product = :product,
                        printwork = :printwork,
                        startdate = :startdate,
                        enddate = :enddate,
                        status = :status,
                        jobdesc = :jobdesc,
                        paperspecs = :paperspecs,
                        filename = :filename,
                        pc = :pc,
                        printer = :printer,
                        printsetting = :printsetting,
                        qtyorder = :qtyorder,
                        qtyconsumed = :qtyconsumed,
                        qtycompleted = :qtycompleted,
                        qtyrejected = :qtyrejected,
                        remarks = :remarks,
                        printedby = :printedby,
                        supervisedby = :supervisedby,
                        deliverydate = :deliverydate,
                        dimensions = :dimensions,
                        ppunit = :ppunit,
                        data = :data";
            
            $stmt = $this->conn->prepare($query);
            
            $this->product = htmlspecialchars(strip_tags($this->product));
            $this->printwork = htmlspecialchars(strip_tags($this->printwork));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->jobdesc = htmlspecialchars(strip_tags($this->jobdesc));
            $this->paperspecs = htmlspecialchars(strip_tags($this->paperspecs));
            $this->filename = htmlspecialchars(strip_tags($this->filename));
            $this->pc = htmlspecialchars(strip_tags($this->pc));
            $this->printer = htmlspecialchars(strip_tags($this->printer));
            $this->printsetting = htmlspecialchars(strip_tags($this->printsetting));
            $this->qtyorder = htmlspecialchars(strip_tags($this->qtyorder));
            $this->qtyconsumed = htmlspecialchars(strip_tags($this->qtyconsumed));
            $this->qtycompleted = htmlspecialchars(strip_tags($this->qtycompleted));
            $this->qtyrejected = htmlspecialchars(strip_tags($this->qtyrejected));
            $this->remarks = htmlspecialchars(strip_tags($this->remarks));
            $this->printedby = htmlspecialchars(strip_tags($this->printedby));
            $this->supervisedby = htmlspecialchars(strip_tags($this->supervisedby));
            $this->dimensions = htmlspecialchars(strip_tags($this->dimensions));
            $this->ppunit = htmlspecialchars(strip_tags($this->ppunit));

            $this->startdate = isset($this->startdate) ? date("Y-m-d", strtotime($this->startdate)) : NULL;
            $this->enddate = isset($this->enddate) ? date("Y-m-d", strtotime($this->enddate)) : NULL;
            $this->deliverydate = isset($this->deliverydate) ? date("Y-m-d", strtotime($this->deliverydate)) : NULL;

            $stmt->bindParam(':custid', $this->custid);
            $stmt->bindParam(':product', $this->product);
            $stmt->bindParam(':printwork', $this->printwork);
            $stmt->bindParam(':startdate', $this->startdate, PDO::PARAM_STR);
            $stmt->bindParam(':enddate', $this->enddate, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':jobdesc', $this->jobdesc);
            $stmt->bindParam(':paperspecs', $this->paperspecs);
            $stmt->bindParam(':filename', $this->filename);
            $stmt->bindParam(':pc', $this->pc);
            $stmt->bindParam(':printer', $this->printer);
            $stmt->bindParam(':printsetting', $this->printsetting);
            $stmt->bindParam(':qtyorder', $this->qtyorder);
            $stmt->bindParam(':qtyconsumed', $this->qtyconsumed);
            $stmt->bindParam(':qtycompleted', $this->qtycompleted);
            $stmt->bindParam(':qtyrejected', $this->qtyrejected);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':printedby', $this->printedby);
            $stmt->bindParam(':supervisedby', $this->supervisedby);
            $stmt->bindParam(':deliverydate', $this->deliverydate, PDO::PARAM_STR);
            $stmt->bindParam(':dimensions', $this->dimensions);
            $stmt->bindParam(':ppunit', $this->ppunit);
            $stmt->bindParam(':data', $this->data);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read() {
            $condition = "";

            if (isset($this->status)) {
                $condition = " WHERE a.status = {$this->status}";
            }

            // if (isset($this->company_name)) {
            //     $company_name = htmlspecialchars(strip_tags($this->company_name));
            //     $company_name = "'%{$company_name}%'";
            //     $condition = "WHERE b.company_name LIKE {$company_name}";
            // }

            if (isset($this->product)) {
                $product = htmlspecialchars(strip_tags($this->product));
                $product = "'%{$product}%'";
                $condition = " WHERE a.product LIKE {$product}";
            }
            

            $query = "SELECT
                        a.*, a.data
                    FROM
                        {$this->table_name} a
                        {$condition}
                    ORDER BY
                        a.status ASC, a.job_id DESC LIMIT 50;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readOne() {
            
        }

        function readByCust() {
            $query = "SELECT
                        a.*, a.data
                    FROM
                        {$this->table_name} a
                    WHERE
                        a.custid = ?
                    ORDER BY
                        a.job_id DESC LIMIT 5;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->custid);

            $stmt->execute();

            return $stmt;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        product = :product,
                        printwork = :printwork,
                        startdate = :startdate,
                        enddate = :enddate,
                        status = :status,
                        jobdesc = :jobdesc,
                        paperspecs = :paperspecs,
                        filename = :filename,
                        pc = :pc,
                        printer = :printer,
                        printsetting = :printsetting,
                        qtyorder = :qtyorder,
                        qtyconsumed = :qtyconsumed,
                        qtycompleted = :qtycompleted,
                        qtyrejected = :qtyrejected,
                        remarks = :remarks,
                        printedby = :printedby,
                        supervisedby = :supervisedby,
                        deliverydate = :deliverydate,
                        dimensions = :dimensions,
                        ppunit = :ppunit
                    WHERE
                        job_id = :job_id";
            
            $stmt = $this->conn->prepare($query);
            
            $this->job_id = htmlspecialchars(strip_tags($this->job_id));
            $this->product = htmlspecialchars(strip_tags($this->product));
            $this->printwork = htmlspecialchars(strip_tags($this->printwork));

            $this->jobdesc = htmlspecialchars(strip_tags($this->jobdesc));
            $this->paperspecs = htmlspecialchars(strip_tags($this->paperspecs));
            $this->filename = htmlspecialchars(strip_tags($this->filename));
            $this->pc = htmlspecialchars(strip_tags($this->pc));
            $this->printer = htmlspecialchars(strip_tags($this->printer));
            $this->printsetting = htmlspecialchars(strip_tags($this->printsetting));
            $this->qtyorder = htmlspecialchars(strip_tags($this->qtyorder));
            $this->qtyconsumed = htmlspecialchars(strip_tags($this->qtyconsumed));
            $this->qtycompleted = htmlspecialchars(strip_tags($this->qtycompleted));
            $this->qtyrejected = htmlspecialchars(strip_tags($this->qtyrejected));
            $this->remarks = htmlspecialchars(strip_tags($this->remarks));
            $this->printedby = htmlspecialchars(strip_tags($this->printedby));
            $this->supervisedby = htmlspecialchars(strip_tags($this->supervisedby));

            $this->dimensions = htmlspecialchars(strip_tags($this->dimensions));
            $this->ppunit = htmlspecialchars(strip_tags($this->ppunit));

            $this->startdate = isset($this->startdate) ? date("Y-m-d", strtotime($this->startdate)) : NULL;
            $this->enddate = isset($this->enddate) ? date("Y-m-d", strtotime($this->enddate)) : NULL;
            $this->deliverydate = isset($this->deliverydate) ? date("Y-m-d", strtotime($this->deliverydate)) : NULL;

            $stmt->bindParam(':job_id', $this->job_id);
            $stmt->bindParam(':product', $this->product);
            $stmt->bindParam(':printwork', $this->printwork);
            $stmt->bindParam(':startdate', $this->startdate, PDO::PARAM_STR);
            $stmt->bindParam(':enddate', $this->enddate, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':jobdesc', $this->jobdesc);
            $stmt->bindParam(':paperspecs', $this->paperspecs);
            $stmt->bindParam(':filename', $this->filename);
            $stmt->bindParam(':pc', $this->pc);
            $stmt->bindParam(':printer', $this->printer);
            $stmt->bindParam(':printsetting', $this->printsetting);
            $stmt->bindParam(':qtyorder', $this->qtyorder);
            $stmt->bindParam(':qtyconsumed', $this->qtyconsumed);
            $stmt->bindParam(':qtycompleted', $this->qtycompleted);
            $stmt->bindParam(':qtyrejected', $this->qtyrejected);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':printedby', $this->printedby);
            $stmt->bindParam(':supervisedby', $this->supervisedby);
            $stmt->bindParam(':deliverydate', $this->deliverydate, PDO::PARAM_STR);
            $stmt->bindParam(':dimensions', $this->dimensions);
            $stmt->bindParam(':ppunit', $this->ppunit);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }
?>