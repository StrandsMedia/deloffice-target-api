<?php
    class Workflow {
        private $conn;
        private $table_name = 'workflow';

        public $workflow_id;
        public $time;
        public $status;
        public $urgent;
        public $cust_id;
        public $orderNo;
        public $purchase;
        public $invoiceNo;
        public $creditNo;
        public $vehicleNo;
        public $sessionID;
        public $invoice_id;
        public $company_name;
        public $range1;
        public $range2;
        public $data;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
            $sort_col = array();
            foreach($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }

            array_multisort($sort_col, $dir, $arr);
        }

        // Create
        
        function create($num) {
            switch ($num) {
                case 0:
                    $query = "INSERT INTO
                                {$this->table_name}
                            SET
                                status = :status,
                                cust_id = :cust_id;";
                    break;
                case 1:
                    $query = "INSERT INTO
                                {$this->table_name}
                            SET
                                status = :status,
                                cust_id = :cust_id,
                                orderNo = :orderNo;";
            }

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);
            $stmt->bindParam(':cust_id', $this->cust_id, PDO::PARAM_INT);

            if ($num === 1) {
                $stmt->bindParam(':orderNo', $this->orderNo, PDO::PARAM_STR);
            }
            
            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        // Sales & Delivery Workflow

        function read($case, $company) {
            $condition = "";
            $condition2 = "";
            $table = "";

            switch ($company) {
                case 1:
                    $table = 'del_cust';
                    $condition2 = " AND a.data = 1 ";
                    break;
                case 2:
                    $table = 'rns_cust';
                    $condition2 = " AND a.data = 2 ";
                    break;
                case 3:
                    $table = 'pnp_cust';
                    $condition2 = " AND a.data = 3 ";
                    break;
            }
            switch (+$case) {
                case 1:
                    $condition = " AND a.status BETWEEN 1 AND 3 ";
                    break;
                case 2:
                    $condition = " AND a.status IN (3, 25, 26) ";
                    break;
                case 3:
                    $condition = " AND a.status IN (26, 7) ";
                    break;
                case 4:
                    $condition = " AND a.status BETWEEN 5 AND 7 ";
                    break;
                case 5:
                    $condition = " AND a.status IN (6, 9, 8) ";
                    break;
            }
            $query = "SELECT b.cust_id, b.company_name, a.workflow_id, a.time, a.status, a.urgent, a.cust_id, a.orderNo, a.purchase, a.invoiceNo, a.vehicleNo, a.sessionID, b.customerCode, a.invoice_id FROM {$this->table_name} a, {$table} b WHERE b.cust_id = a.cust_id AND a.status <> 0 {$condition}{$condition2} ORDER BY a.time DESC";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        // Workflow Event

        function readEvent($data) {
            $table = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, c.comments as dinstr, c.purchase as pinstr
                    FROM
                        {$this->table_name} a, {$table} b, workflow_delivery c
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.workflow_id = c.workflow_id
                    AND
                        a.workflow_id = ?;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->workflow_id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function findInvoice() {
            if (isset($this->workflow_id)) {
                $query = "SELECT
                            *
                        FROM
                            {$this->table_name}
                        WHERE
                            invoiceNo LIKE ?
                        AND
                            workflow_id <> ?;";
            } else {
                $query = "SELECT
                            *
                        FROM
                            {$this->table_name}
                        WHERE
                            invoiceNo LIKE ?;";
            }

            $stmt = $this->conn->prepare($query);

            $this->invoiceNo = htmlspecialchars(strip_tags($this->invoiceNo));
            $this->invoiceNo = "%{$this->invoiceNo}%";

            $stmt->bindParam(1, $this->invoiceNo);
            
            if (isset($this->workflow_id)) {
                $stmt->bindParam(2, $this->workflow_id);
            }

            $stmt->execute();

            return $stmt;
        }

        function findInvNum($data) {
            $table = "";
            $condition = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    $condition = " AND a.data = 1 ";
                    break;
                case 2:
                    $table = 'rns_cust';
                    $condition = " AND a.data = 2 ";
                    break;
                case 3:
                    $table = 'pnp_cust';
                    $condition = " AND a.data = 3 ";
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.customerCode, c.step
                    FROM
                        {$this->table_name} a, {$table} b, workflow_steps c
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.status = c.step_id
                    AND
                        a.invoiceNo LIKE ?{$condition};";


            $stmt = $this->conn->prepare($query);

            $this->invoiceNo = htmlspecialchars(strip_tags($this->invoiceNo));
            $this->invoiceNo = "%{$this->invoiceNo}%";

            $stmt->bindParam(1, $this->invoiceNo);
        
            $stmt->execute();

            return $stmt;
        }

        function readCompletion($invNum, $data) {
            $table = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.invoiceNo, c.step, b.company_name, a.workflow_id, d.jobID, a.status
                    FROM
                        {$this->table_name} a, {$table} b, workflow_steps c, workflow_delivery d
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.status = c.step_id
                    AND
                        a.workflow_id = d.workflow_id
                    AND
                        a.invoiceNo LIKE ?
                    LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $invNum);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row;
            } else {
                return false;
            }

        }
        
        // Read by Status

        function readByStatus($data) {
            $table = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }
            
            $query = "SELECT a.company_name, b.*, c.purchase as purchaseIns FROM {$table} a, {$this->table_name} b, workflow_delivery c WHERE a.cust_id = b.cust_id AND b.workflow_id = c.workflow_id AND b.status = ? ORDER BY b.workflow_id DESC LIMIT 50;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->status);

            $stmt->execute();

            return $stmt;
        }

        // Read by Customer

        function readByCust() {
            $table = "";
            switch ($this->data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.workflow_id, a.time, b.cust_id, b.company_name, c.step
                    FROM
                        {$this->table_name} a, {$table} b, workflow_steps c
                    WHERE
                        a.status = c.step_id
                    AND
                        a.status BETWEEN 1 AND 9
                    AND
                        a.cust_id = b.cust_id
                    AND
                        a.cust_id = ?
                    ORDER BY workflow_id DESC
                    LIMIT 10;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id);

            $stmt->execute();

            return $stmt;
        }

        // Delivery Session List to Push

        function goodsReady($data) {
            $table = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                b.cust_id, b.company_name, a.workflow_id, a.time, a.status, a.urgent, a.cust_id, a.orderNo, a.purchase, a.invoiceNo, a.vehicleNo, a.sessionID
            FROM " . $this->table_name . " a, {$table} b 
            WHERE b.cust_id = a.cust_id 
            AND a.status <> 0 
            AND a.status = '7' 
            ORDER BY a.time DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getSession() {
            $query = "SELECT
                        workflow_id
                    FROM
                        {$this->table_name}
                    WHERE
                        sessionID = :sessionID;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':sessionID', $this->sessionID);

            $stmt->execute();

            return $stmt;
        }

        // Update Workflow on New Session

        function onSession($workflow_id) {
            $query = "UPDATE 
                    " . $this->table_name . "
            SET status=:status, vehicleNo=:vehicleNo, sessionID=:sessionID
            WHERE workflow_id='" . $workflow_id . "';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function update($num) {
            $condition = "";

            switch ($num) {
                case 0:
                    $condition = ", orderNo = :orderNo ";
                    break;
                case 1:
                    $condition = ", invoiceNo = :invoiceNo ";
                    break;
                case 2:
                    $condition = ", creditNo = :creditNo ";
                    break;
                case 3:
                    $condition = ", vehicleNo = :vehicleNo, sessionID = :session ";
                    break;
                case 4:
                    $condition = "";
                    break;
                case 5:
                    $condition = ", invoice_id = :invoice_id ";
                    break;
            }

            $query = "UPDATE
                        {$this->table_name}
                    SET
                        `status` = :stat{$condition}
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

                switch ($num) {
                    case 0:
                        $stmt->bindParam(':orderNo', $this->orderNo);
                        break;
                    case 1:
                        $stmt->bindParam(':invoiceNo', $this->invoiceNo);
                        break;
                    case 2:
                        $stmt->bindParam(':creditNo', $this->creditNo);
                        break;
                    case 3:
                        $stmt->bindParam(':vehicleNo', $this->vehicleNo);
                        $stmt->bindParam(':session', $this->sessionID);
                        break;
                    case 4:
                        //
                        break;
                    case 5:
                        $stmt->bindParam(':invoice_id', $this->invoice_id);
                        break;
                }

            $stmt->bindParam(':stat', $this->status);
            $stmt->bindParam(':workflow_id', $this->workflow_id);
            
            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateOnSession() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status,
                        vehicleNo = :vehicleNo
                    WHERE
                        sessionID = :sessionID
                    AND
                        status = 9;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':vehicleNo', $this->vehicleNo);
            $stmt->bindParam(':sessionID', $this->sessionID);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateInvoice() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        invoiceNo = :invoiceNo
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $this->invoiceNo = htmlspecialchars(strip_tags($this->invoiceNo));

            $stmt->bindParam(':invoiceNo', $this->invoiceNo);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        }

        function getInvoiceId($workflow_id) {
            $invoiceId = 0;

            $query1 = "SELECT invoice_id FROM workflow WHERE workflow_id = ? LIMIT 0,1;";
            $query2 = "SELECT invoice_id FROM invoice WHERE workflow_id = ? ORDER BY invoice_id DESC LIMIT 0,1;";

            $stmt1 = $this->conn->prepare($query1);
            $stmt2 = $this->conn->prepare($query2);

            $stmt1->bindParam(1, $workflow_id, PDO::PARAM_INT);
            $stmt2->bindParam(1, $workflow_id, PDO::PARAM_INT);

            $stmt1->execute();
            $stmt2->execute();

            if ($stmt1->rowCount() > 0) {
                $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                $invoiceId = $row1['invoice_id'];

                if (!isset($invoiceId) || $invoiceId == 0) {
                    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    $invoiceId = $row2['invoice_id'];
                }
            }

            return $invoiceId;
        }

        function urgentOrPurchase($status) {
            $condition = "";

            if ($status === 'urgent') {
                $condition = " urgent = :status ";
            } else {
                $condition = " purchase = :status ";
            }

            $query = "UPDATE
                        {$this->table_name}
                    SET
                        {$condition}
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->urgent, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class WorkflowSession {
        private $conn;
        private $table_name = 'workflow_session';

        public $sessionID;
        public $sessionDate;
        public $vehicle;
        public $driver;
        public $status;
        public $user;
        public $region;

        public $range1;
        public $range2;
        public $range3;

        public function __construct($db) {
            $this->conn = $db;
        }

        function isThereSession() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        vehicle = :vehicle
                    AND
                        DATE(NOW()) = DATE(sessionDate)
                    AND
                        status = 0;";
        
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':vehicle', $this->vehicle);

            $stmt->execute();

            return $stmt->rowCount();
        }

        function isThereSessionPickup() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        vehicle = 'pickup'
                    AND
                        status = '2'
                    AND
                        DATE(sessionDate) = DATE(NOW())
                    ORDER BY sessionID DESC LIMIT 1";
        
            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt->rowCount();
        }

        function sessionNumber() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        vehicle = :vehicle
                    AND
                        status = 0
                    AND
                        DATE(NOW()) = DATE(sessionDate)
                    LIMIT 1;";
        
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':vehicle', $this->vehicle);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['sessionID'];
        }

        function pickupsessionNumber() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        vehicle = 'pickup'
                    AND
                        status = '2'
                    AND
                        DATE(sessionDate) = DATE(NOW())
                    ORDER BY sessionID DESC LIMIT 1";
        
            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['sessionID'];
        }

        function readSession($operator) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name} a
                    WHERE 
                        (a.status = ?) 
                    OR 
                        (a.status = ? 
                    AND 
                        DATE(a.sessionDate) {$operator} DATE(NOW()))
                    ORDER BY a.sessionID DESC LIMIT 0,25";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(1, $this->range1, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->range2, PDO::PARAM_STR);
            
            $stmt->execute();

            return $stmt;
        }

        function readSesh() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        sessionID = :sessionID;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':sessionID', $this->sessionID);

            $stmt->execute();

            return $stmt;
        }

        function archiveSession($date) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name} a
                    LEFT JOIN 
                        sales_representative b
                    ON 
                        a.user = b.sales_id
                    WHERE
                        DATE(a.sessionDate) = DATE({$date})
                    ORDER BY a.sessionID DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function createSession() {
            $query = "INSERT INTO
                    {$this->table_name}
                SET
                    sessionDate = CURRENT_TIMESTAMP,
                    vehicle = :vehicle;";

            $stmt = $this->conn->prepare($query);

            $this->vehicle = htmlspecialchars(strip_tags($this->vehicle));

            $stmt->bindParam(":vehicle", $this->vehicle);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function createPickup() {
            $query = "INSERT INTO
                    {$this->table_name}
                SET
                    sessionDate = CURRENT_TIMESTAMP,
                    vehicle = 'pickup',
                    status = 2,
                    user = :user;";

            $stmt = $this->conn->prepare($query);

            $this->user = htmlspecialchars(strip_tags($this->user));

            $stmt->bindParam(":user", $this->user);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function closeSession() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status,
                        user = :user,
                        driver = :driver,
                        vehicle = :vehicle,
                        region = :region
                    WHERE
                        sessionID = :sessionID;";
                    
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':user', $this->user);
            $stmt->bindParam(':driver', $this->driver);
            $stmt->bindParam(':vehicle', $this->vehicle);
            $stmt->bindParam(':region', $this->region);
            $stmt->bindParam(':sessionID', $this->sessionID);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function autoArchiveSession() {
            $queryTotal = "SELECT
                        *
                    FROM
                        workflow
                    WHERE
                        sessionID = :sessionID;";

            $stmtTotal = $this->conn->query($queryTotal);

            $stmtTotal->bindParam(':sessionID', $this->sessionID);

            $stmtTotal->execute();

            $numTotal = $stmtTotal->rowCount();

            $queryComplete = "SELECT
                        *
                    FROM
                        workflow
                    WHERE
                        sessionID = :sessionID
                    AND
                        status BETWEEN 1 AND 9;";

            $stmtComplete = $this->conn->query($queryComplete);

            $stmtComplete->bindParam(':sessionID', $this->sessionID);

            $stmtComplete->execute();

            $numComplete = $stmtComplete->rowCount();

            if ($numComplete == $numTotal) {
                $query = "SELECT
                            status
                        FROM
                            {$this->table_name}
                        WHERE
                            sessionID = :sessionID;";

                $stmt = $this->conn->query($query);

                $stmt->bindParam(':sessionID', $this->sessionID);

                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['status'] == 1) {
                    $queryUpdate = "UPDATE
                                        {$this->table_name}
                                    SET
                                        status = 3
                                    WHERE
                                        sessionID = ?;";
                    
                    $stmtUpdate = $this->conn->query($queryUpdate);

                    $stmtUpdate->bindParam(1, $this->sessionID);

                    if ($stmtUpdate->execute()) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }

            
        }
    }

    class WorkflowDelivery {
        private $conn;
        private $table_name = 'workflow_delivery';

        public $deliveryID;
        public $workflow_id;
        public $time;
        public $jobID;
        public $cust_id;
        public $invoice_no;
        public $credit_no;
        public $comments;
        public $purchase;
        public $delivery_status;
        public $region;
        public $vehicle;
        public $urgent;
        public $deliveryDate;

        public $date1;
        public $date2;

        public $company_name;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
            $sort_col = array();
            foreach($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }

            array_multisort($sort_col, $dir, $arr);
        }

        function createDelivery() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        workflow_id = :workflow_id,
                        cust_id = :cust_id,
                        urgent = :urgent,
                        delivery_status = :delivery_status;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':workflow_id', $this->workflow_id);
            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':urgent', $this->urgent);
            $stmt->bindParam(':delivery_status', $this->delivery_status);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        // Delivery Archive

        function readArchive($data) {
            $table = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    break;
                case 2:
                    $table = 'rns_cust';
                    break;
                case 3:
                    $table = 'pnp_cust';
                    break;
            }

            $condition = "";

            if (isset($this->date1) && isset($this->date2)) {
                $condition = " AND DATE(b.time) BETWEEN DATE('{$this->date1}') AND DATE('{$this->date2}')";
            }

            if (isset($this->invoice_no)) {
                $invoice_no = htmlspecialchars(strip_tags($this->invoice_no));
                $invoice_no = "'%{$invoice_no}%'";
                $condition = " AND b.invoice_no LIKE {$invoice_no}";
            }

            if (isset($this->company_name)) {
                $company_name = htmlspecialchars(strip_tags($this->company_name));
                $company_name = "'%{$company_name}%'";
                $condition = " AND a.company_name LIKE {$company_name}";
            }

            $query = "SELECT a.company_name, b.*, c.status FROM {$table} a, {$this->table_name} b, workflow c WHERE a.cust_id = b.cust_id AND b.workflow_id = c.workflow_id AND b.invoice_no <> '' AND (b.delivery_status = '17' OR c.status = '17') {$condition} ORDER BY b.workflow_id DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readJob() {
            $query = "SELECT
                        a.workflow_id, a.jobID, a.cust_id, a.invoice_no, a.comments, a.vehicle, a.time, a.delivery_status,
                        c.status, d.step, c.data
                    FROM
                        {$this->table_name} a, workflow c, workflow_steps d
                    WHERE
                        a.workflow_id = c.workflow_id
                    AND
                        c.status != 0
                    AND
                        d.id = c.status
                    AND
                        a.jobID = :jobID
                    ORDER BY
                        time ASC;";
                    
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':jobID', $this->jobID);

            $stmt->execute();

            return $stmt;
        }

        function invoiceCount($session) {
            $query = "SELECT *
            FROM " . $this->table_name . " WHERE jobID='" . $session . "';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function updateStatus() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        delivery_status = :delivery_status
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':delivery_status', $this->delivery_status, PDO::PARAM_INT);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updatePurchase() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        purchase = :purchase
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $this->purchase = htmlspecialchars(strip_tags($this->purchase));

            $stmt->bindParam(':purchase', $this->purchase);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateComments() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        comments = :comments
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $this->comments = htmlspecialchars(strip_tags($this->comments));

            $stmt->bindParam(':comments', $this->comments);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateInvoice() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        invoice_no = :invoice_no
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':invoice_no', $this->invoice_no);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateCredit() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        credit_no = :credit_no
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':credit_no', $this->credit_no);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateCreditNote() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        credit_no = :credit_no,
                        delivery_status = :delivery_status
                    WHERE
                        workflow_id = :workflow_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':credit_no', $this->credit_no);
            $stmt->bindParam(':delivery_status', $this->delivery_status);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateOnSession($workflow_id) {
            $query = "UPDATE 
                    {$this->table_name}
            SET vehicle=:vehicle, delivery_status=:delivery_status, jobID=:jobID, time=CURRENT_TIMESTAMP
            WHERE workflow_id='" . $workflow_id . "';";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":vehicle", $this->vehicle);
            $stmt->bindParam(":delivery_status", $this->delivery_status);
            $stmt->bindParam(":jobID", $this->jobID);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateCloseSession() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        vehicle = :vehicle
                    WHERE
                        jobID = :jobID;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':jobID', $this->jobID);

            $stmt->execute();

            return $stmt;
        }

        function updateCancel($workflow_id) {
            $query = "UPDATE 
                    {$this->table_name}
            SET vehicle=:vehicle, delivery_status=:delivery_status, jobID=0, time=CURRENT_TIMESTAMP
            WHERE workflow_id='" . $workflow_id . "';";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":vehicle", $this->vehicle);
            $stmt->bindParam(":delivery_status", $this->delivery_status);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class WorkflowHistory {
        private $conn;
        private $table_name = 'workflow_history';

        public $historyid;
        public $workflow_id;
        public $time;
        public $user;
        public $note;
        public $comment;
        public $step;

        public $date1;
        public $date2;

        public function __construct($db) {
            $this->conn = $db;
        }

        function readCount($status, $user) {
            $query = "SELECT * FROM {$this->table_name} WHERE user = '{$user}' AND DATE(time) BETWEEN DATE('{$this->date1}') AND DATE('{$this->date2}') AND step = '{$status}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readHistory($id) {
            $query = "SELECT
                        a.*, b.sales_rep, c.step as stepname
                    FROM
                        {$this->table_name} a, sales_representative b, workflow_steps c
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.step = c.id
                    AND
                        a.workflow_id = ?
                    ORDER BY
                        a.historyid;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function readLastUpdateDate($workflow_id) {
            $query = "SELECT * FROM {$this->table_name} WHERE (workflow_id = '{$workflow_id}' AND step <= 5) OR (workflow_id = '{$workflow_id}' AND step = 6) ORDER BY historyid DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['time'];
        }

        function insertHistory() {
            $query = "INSERT INTO
                    " . $this->table_name . "
                SET 
                    workflow_id=:workflow_id, user=:user, step=:step, note=:note, comment=:comment;";

            $stmt = $this->conn->prepare($query);

            $this->workflow_id = htmlspecialchars(strip_tags($this->workflow_id));
            $this->user = htmlspecialchars(strip_tags($this->user));
            $this->step = htmlspecialchars(strip_tags($this->step));
            $this->note = htmlspecialchars(strip_tags($this->note));
            $this->comment = htmlspecialchars(strip_tags($this->comment));

            $stmt->bindParam(":workflow_id", $this->workflow_id);
            $stmt->bindParam(":user", $this->user, PDO::PARAM_INT);
            $stmt->bindParam(":step", $this->step, PDO::PARAM_INT);
            $stmt->bindParam(":note", $this->note);
            $stmt->bindParam(":comment", $this->comment);
            
            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class WorkflowStep {
        private $conn;
        private $table_name = 'workflow_steps';

        public $step_id;
        public $id;
        public $step;
        public $status;

        public function __construct($db) {
            $this->conn = $db;
        }

        function read() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }

    class WorkflowDetails {
        private $conn;
        private $table_name = 'workflow_details';

        public $details_id;
        public $workflow_id;
        public $qty;
        public $format;
        public $brand;
        public $product;
        public $status;

        public $paperarr = array('paper1', 'paper2', 'paper3', 'paper4', 'paper5');
        public $productarr = array('paper1', 'paper2', 'paper3', 'paper4', 'paper5', 'stationery', 'pens', 'printers', 'files', 'cleaning', 'ink', 'envelopes', 'messroom', 'others');

        public function __construct($db) {
            $this->conn = $db;
        }

        public function inArray($product) {
            $checkVars = array('paper1', 'paper2', 'paper3', 'paper4', 'paper5');
            if (in_array($product, $checkVars)) {
                return true;
            }

            return false;
        }

        function getPaperRange($range, $brand, $vehicle, $data) {
            $table = "";
            $condition = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    $condition = " AND a.data = 1 ";
                    break;
                case 2:
                    $table = 'rns_cust';
                    $condition = " AND a.data = 2 ";
                    break;
                case 3:
                    $table = 'pnp_cust';
                    $condition = " AND a.data = 3 ";
                    break;
            }
            $total = 0;
            $testsum = 0;

            $query = "SELECT
                        c.details_id, a.workflow_id, a.status, b.cust_id, d.company_name, b.invoice_no,
                        b.comments, b.delivery_status, b.region, b.vehicle,
                        b.urgent, b.deliveryDate, c.qty, c.format, c.brand
                    FROM
                        workflow a, workflow_delivery b, {$this->table_name} c, {$table} d
                    WHERE
                        a.workflow_id = b.workflow_id
                    AND
                        a.workflow_id = c.workflow_id
                    AND
                        b.cust_id = d.cust_id
                    AND
                        c.workflow_id IN $range
                    AND
                        c.brand = '$brand'{$condition}
                    AND
                        b.vehicle = '$vehicle';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $total = +$row['qty'];

                    $testsum = $testsum + +$total;
                }
            }

            return $testsum;
            
        }

        function getPaperRangeRecs($range, $brand, $vehicle) {
            $query = "SELECT
                        c.details_id, a.workflow_id, a.status, a.data, b.cust_id, b.invoice_no,
                        b.comments, b.delivery_status, b.region, b.vehicle,
                        b.urgent, b.deliveryDate, c.qty, c.format, c.brand
                    FROM
                        workflow a, workflow_delivery b, {$this->table_name} c
                    WHERE
                        a.workflow_id = b.workflow_id
                    AND
                        a.workflow_id = c.workflow_id
                    AND
                        c.workflow_id IN $range
                    AND
                        c.brand = '$brand'
                    AND
                        b.vehicle = '$vehicle';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $arr = array();
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
    
                    $arr_item = array(
                        'details_id' => $details_id,
                        'workflow_id' => $workflow_id,
                        'status' => $status,
                        'cust_id' => $cust_id,
                        // 'company_name' => $company_name,
                        'invoice_no' => $invoice_no,
                        'comments' => $comments,
                        'delivery_status' => $delivery_status,
                        'region' => $region,
                        'format' => $format,
                        'vehicle' => $vehicle,
                        'urgent' => $urgent,
                        'deliveryDate' => $deliveryDate,
                        'qty' => $qty,
                        'brand' => $row['brand'],
                        'data' => $data
                    );

                    array_push($arr, $arr_item);
                }
            }

            return $arr;
            
        }

        function getParsedProducts($id) {
            $query = "SELECT 
                        *
                    FROM 
                        {$this->table_name}
                    WHERE
                        workflow_id = '{$id}'
                    ORDER BY 
                        brand ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $num = $stmt->rowCount();

            $products = array();

            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    $products['workflow_id'] = $id;

                    if ($this->inArray($product)) {
                        $products[$product] = array(
                            true,
                            $qty,
                            $format,
                            $brand
                        );
                    } else {
                        $products[$product] = array(
                            true
                        );
                    }

                }

                return $products;
            }
        }

        function getProducts($id) {
            $productlist = "";

            $query = "SELECT 
                        *
                    FROM 
                        {$this->table_name}
                    WHERE
                        workflow_id = '{$id}'
                    ORDER BY 
                        brand ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $num = $stmt->rowCount();
            $i = 1;
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($this->inArray($row['product'])) {
                        $productlist .= $row['qty']." ".$row['format']." ".$row['brand'];
                        if ($i < $num) {
                            $productlist .= ", ";
                        }
                    } else {
                        $productlist .= ucfirst($row['product'])."";
                        if ($i < $num) {
                            $productlist .= ", ";
                        }
                    }

                    $i++;
                }

                return $productlist;
            }
        }

        function parseInsert($data) {
            $retdata = array();

            $count = 0;

            foreach($this->productarr as $item => $value) {
                if (isset($data->$value)) {
                    if (isset($data->$value[0]) && $data->$value[0] === true) {
                        if ($this->inArray($value)) {
                            $ret_item = array(
                                'product' => $value,
                                'qty' => $data->$value[1],
                                'format' => $data->$value[2],
                                'brand' => $data->$value[3]
                            );
                        }
                        array_push($retdata, $ret_item);
                    } else if ($data->$value === true) {
                        $ret_item = array(
                            'product' => $value
                        );
    
                        array_push($retdata, $ret_item);
                    }
                }
            }

            foreach($retdata as $ret => $val) {
                $query = "INSERT INTO
                            {$this->table_name}
                        SET
                            workflow_id = :workflow_id,
                            product = :product,
                            qty = :qty,
                            format = :format,
                            brand = :brand;";

                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);
                $stmt->bindParam(':product', $retdata[$ret]['product']);
                $stmt->bindParam(':qty', $retdata[$ret]['qty']);
                $stmt->bindParam(':format', $retdata[$ret]['format']);
                $stmt->bindParam(':brand', $retdata[$ret]['brand']);

                if ($stmt->execute()) {
                    $count ++;
                } else {
                    //
                }

            }
            if ($count == sizeof($retdata)) {
                return true;
            }

            return false;
        }

        function parseUpdate($data) {
            if (isset($data)) {
                $prodarr = array();
                $retdata = array();

                $count = false;

                foreach($this->productarr as $item => $value) {
                    if (isset($data->$value)) {
                        if (isset($data->$value[0]) && $data->$value[0] === true) {
                                $prod_item = $value;
                            array_push($prodarr, $prod_item);
                        } else if ($data->$value === true) {
                            $prod_item = $value;
                            array_push($prodarr, $prod_item);
                        }

                        if (isset($data->$value[0]) && $data->$value[0] === true) {
                            if ($this->inArray($value)) {
                                $ret_item = array(
                                    'product' => $value,
                                    'qty' => $data->$value[1],
                                    'format' => $data->$value[2],
                                    'brand' => $data->$value[3]
                                );
                            } else {
                                $ret_item = array(
                                    'product' => $value
                                );
                            }
                            array_push($retdata, $ret_item);
                        } else if ($data->$value === true) {
                            $ret_item = array(
                                'product' => $value
                            );
                            array_push($retdata, $ret_item);
                        }
                    }
                }


                $querycheck = "SELECT
                                *
                            FROM
                                {$this->table_name}
                            WHERE
                                workflow_id = {$data->workflow_id};";
                $stmtcheck = $this->conn->prepare($querycheck);
                $stmtcheck->execute();

                $num = $stmtcheck->rowCount();

                $prodarr2 = array();
                if ($num > 0) {
                    while ($row = $stmtcheck->fetch(PDO::FETCH_ASSOC)) {
                        array_push($prodarr2, $row['product']);
                    }
                }
                
                $toinsert = array_values(array_diff($prodarr, $prodarr2));
                $toupdate = array_values(array_intersect($prodarr, $prodarr2));
                $todelete = array_values(array_diff($prodarr2, $toupdate));

                foreach($retdata as $ret => $value) {
                    if (in_array($retdata[$ret]['product'], $toinsert)) {
                        if ($this->insert($value)) {
                            $count = true;
                        } else {
                            $count = false;
                        }
                    }
                }


                $stmtcheck->execute();

                if ($num > 0) {
                    while ($row2 = $stmtcheck->fetch(PDO::FETCH_ASSOC)) {
                        
                        if (in_array($row2['product'], $toupdate)) {
                            foreach($retdata as $ret => $value) {
                                if ($retdata[$ret]['product'] === $row2['product']) {
                                    $this->details_id = $row2['details_id'];
                                    if ($this->update($value)) {
                                        $count = true;
                                    } else {
                                        $count = false;
                                    }
                                }
                            }
                        } else if (in_array($row2['product'], $todelete)) {
                            $this->details_id = $row2['details_id'];
                            if ($this->delete()) {
                                $count = true;
                            } else {
                                $count = false;
                            }
                        }
                    }
                }

                if ($count === true) {
                    return true;
                }
    
                return false;

            } else {
                return false;
            }
        }

        function insert($data) {
            if (isset($data)) {
                $query = "INSERT INTO
                            {$this->table_name}
                        SET
                            product = :product,
                            qty = :qty,
                            brand = :brand,
                            format = :format,
                            workflow_id = :workflow_id;";

                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':product', $data['product']);
                $stmt->bindParam(':qty', $data['qty']);
                $stmt->bindParam(':brand', $data['brand']);
                $stmt->bindParam(':format', $data['format']);
                $stmt->bindParam(':workflow_id', $this->workflow_id);

                if ($stmt->execute()) {
                    return true;
                }

                return false;
            }
        }

        function update($data) {
            if (isset($data)) {
                $query = "UPDATE
                            {$this->table_name}
                        SET
                            product = :product,
                            qty = :qty,
                            brand = :brand,
                            format = :format
                        WHERE
                            details_id = :details_id;";
                
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':product', $data['product']);
                $stmt->bindParam(':qty', $data['qty']);
                $stmt->bindParam(':brand', $data['brand']);
                $stmt->bindParam(':format', $data['format']);
                $stmt->bindParam(':details_id', $this->details_id);

                if ($stmt->execute()) {
                    return true;
                }

                return false;
            }
        }

        function delete() {
            $query = "DELETE FROM
                        {$this->table_name}
                    WHERE
                        details_id = :details_id;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':details_id', $this->details_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

    }

    class WorkflowPaper {
        private $conn;
        private $table_name = 'workflow_paper';

        public $paperID;
        public $paperBrand;
        public $status;

        public function __construct($db) {
            $this->conn = $db;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE 
                        status = 0
                    ORDER BY paperBrand;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getAll() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }
?>