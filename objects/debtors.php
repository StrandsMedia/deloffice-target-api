<?php
    class DebtorsControl {
        private $conn;
        private $table_name = 'debtors_control';

        public $dc_id;
        public $cust_id;
        public $agent;
        public $data;
        public $dispute;
        public $inv;
        public $dn;
        public $crn;
        public $oth;
        public $adjs;
        public $issue;
        public $reminder;
        public $task;
        public $cheque;
        public $debt_id;
        public $status;
        public $createdAt;
        public $updatedAt;

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

        public function getStatus($status) {
            switch ($status) {
                case 1:
                    return 'To Contact';
                    break;
                case 3:
                    return 'Awaiting Feedback';
                    break;
                case 4:
                    return 'Expecting Payment';
                    break;
                case 8:
                    return 'Search Documents';
                    break;
                case 9:
                    return 'Chase';
                    break;
                case 10:
                    return 'Dispute';
                    break;
                case 5:
                    return 'Cheque Ready';
                    break;
                case 6:
                    return 'Cleared';
                    break;
            }
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        agent = :agent,
                        data = :data,
                        status = :status;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id, PDO::PARAM_INT);
            $stmt->bindParam(':agent', $this->agent, PDO::PARAM_INT);
            $stmt->bindParam(':data', $this->data, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read($operator, $data) {
            $condition = "";

            if ($operator == 1) {
                $condition = "IN (1,3,4,8,9,10,5)";
            } else {
                $condition = "IN (6)";
            }

            $cust_table = "";
            $lookfor = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email, b.contact_person_acc, b.tel_acc, b.fax_acc, b.mob_acc, b.email_acc";
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email";
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email";
                    break;
            }

            $query = "SELECT
                        a.*, {$lookfor}, c.sales_rep
                    FROM
                        {$this->table_name} a, {$cust_table} b, sales_representative c
                    WHERE
                        a.agent = c.sales_id
                    AND
                        a.cust_id = b.cust_id
                    AND
                        a.status {$condition}
                    AND
                        a.data = {$data}
                    ORDER BY
                        a.dc_id DESC";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function search($keywords, $data) {
            $condition = "";

            $cust_table = "";
            $lookfor = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email, b.contact_person_acc, b.tel_acc, b.fax_acc, b.mob_acc, b.email_acc";
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email";
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.address, b.tel, b.fax, b.mob, b.email";
                    break;
            }

            $query = "SELECT
                        a.*, {$lookfor}, c.sales_rep
                    FROM
                        {$this->table_name} a, {$cust_table} b, sales_representative c
                    WHERE
                        a.agent = c.sales_id
                    AND
                        a.cust_id = b.cust_id
                    AND
                        a.data = {$data}
                    AND
                        a.status IN (1,3,4,8,9,10,5)
                    AND
                        b.company_name LIKE ?
                    ORDER BY
                        a.dc_id DESC";

            $stmt = $this->conn->prepare($query);

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%${keywords}%";

            $stmt->bindParam(1, $keywords);

            $stmt->execute();

            return $stmt;
        }

        function findEntry($id, $data) {
            $cust_table = "";
            $lookfor = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        *
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.data = {$data}
                    AND
                        a.status IN (1,3,4,8,9,10,5)
                    AND
                        b.cust_id LIKE ?
                    ORDER BY
                        a.dc_id DESC";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $id);

            $stmt->execute();

            return $stmt;
        }

        function readOne($data) {
            $cust_table = "";
            $lookfor = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.tel, b.fax, b.mob, b.email, b.contact_person_acc, b.tel_acc, b.fax_acc, b.mob_acc, b.email_acc";
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.tel, b.fax, b.mob, b.email";
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    $lookfor = "b.customerCode, b.company_name, b.contact_person, b.tel, b.fax, b.mob, b.email";
                    break;
            }

            $query = "SELECT
                        a.*, {$lookfor}, c.sales_rep
                    FROM
                        {$this->table_name} a, {$cust_table} b, sales_representative c
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.agent = c.sales_id
                    AND
                        a.dc_id = :dc_id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dc_id', $this->dc_id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        dispute = :dispute,
                        inv = :inv,
                        dn = :dn,
                        crn = :crn,
                        oth = :oth,
                        adjs = :adjs,
                        issue = :issue,
                        cheque = :cheque,
                        status = :status,
                        payment = :payment
                    WHERE
                        dc_id = :dc_id;";

            $stmt = $this->conn->prepare($query);

            $this->issue = htmlspecialchars(strip_tags($this->issue));

            $stmt->bindParam(':dispute', $this->dispute);
            $stmt->bindParam(':inv', $this->inv);
            $stmt->bindParam(':dn', $this->dn);
            $stmt->bindParam(':crn', $this->crn);
            $stmt->bindParam(':oth', $this->oth);
            $stmt->bindParam(':adjs', $this->adjs);
            $stmt->bindParam(':issue', $this->issue);
            $stmt->bindParam(':cheque', $this->cheque);
            $stmt->bindParam(':payment', $this->payment);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);
            $stmt->bindParam(':dc_id', $this->dc_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function collectUpdate() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        debt_id = :debt_id,
                        status = 5
                    WHERE
                        dc_id = :dc_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':debt_id', $this->debt_id, PDO::PARAM_INT);
            $stmt->bindParam(':dc_id', $this->dc_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function statusUpdate() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status
                    WHERE
                        dc_id = :dc_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);
            $stmt->bindParam(':dc_id', $this->dc_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class DebtorsCtrlCmt {
        private $conn;
        private $table_name = 'debtors_comment';

        public $dccomm_id;
        public $dc_id;
        public $dc_comment;
        public $type;
        public $user;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function insertComment() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        dc_id = :dc_id,
                        dc_comment = :dc_comment,
                        user = :user;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dc_id', $this->dc_id);
            $stmt->bindParam(':dc_comment', $this->dc_comment);
            $stmt->bindParam(':user', $this->user);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function getComments($id) {
            $query = "SELECT
                        a.dc_comment, a.createdAt, b.sales_rep
                    FROM
                        {$this->table_name} a, sales_representative b
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.dc_id = :dc_id
                    ORDER BY
                        a.createdAt
                    DESC;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dc_id', $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function getLastComment($id) {
            $query = "SELECT
                        a.dc_comment, a.createdAt, b.sales_rep
                    FROM
                        {$this->table_name} a, sales_representative b
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.dc_id = {$id}
                    ORDER BY
                        a.createdAt
                    DESC LIMIT 1;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($row['dc_comment'])) {
                return $row['sales_rep'] . ' - ' . $row['dc_comment'];
            }

            return null;
        }
    }

    class DebtorsReview {
        private $conn;
        private $table_name = 'debtors_review';

        public $dr_id;
        public $dc_id;
        public $active;
        public $reviewAt;
        public $reviewPeriod;
        public $createdAt;
        public $updatedAt;

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

        function insert() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        dc_id = :dc_id,
                        active = 1,
                        user = :user;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dc_id', $this->dc_id, PDO::PARAM_INT);
            $stmt->bindParam(':user', $this->user, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read($data) {
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.status, b.cust_id, b.data, c.company_name
                    FROM
                        {$this->table_name} a, debtors_control b, {$cust_table} c
                    WHERE
                        a.dc_id = b.dc_id
                    AND
                        b.data = :data
                    AND
                        b.cust_id = c.cust_id
                    AND
                        a.user = :user
                    AND
                        a.active = :active;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':data', $data, PDO::PARAM_INT);
            $stmt->bindParam(':user', $this->user, PDO::PARAM_INT);
            $stmt->bindParam(':active', $this->active, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function readByDC($id, $user) {
            $query = "SELECT
                        a.*
                    FROM
                        {$this->table_name} a
                    WHERE
                        a.dc_id = {$id}
                    AND
                        a.user = {$user}
                    ORDER BY
                        a.createdAt DESC
                    LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        }

        function isInReview() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        dc_id = ?
                    AND
                        active = 0
                    AND
                        user = ?
                    ORDER BY
                        createdAt DESC
                    LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->dc_id);
            $stmt->bindParam(2, $this->user);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row->dr_id;
            } else {
                return null;
            }

        }

        function reactivate() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        active = 1
                    WHERE
                        dr_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->dr_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function release() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        active = 0,
                        reviewAt = CURRENT_TIMESTAMP,
                        reviewPeriod = ?
                    WHERE
                        dr_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->reviewPeriod, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->dr_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    class DebtCollect {
        private $conn;
        private $table_name = 'debt_collect';

        public $debt_id;
        public $cust_id;
        public $pay_method;
        public $delivery_pay;
        public $collected;
        public $comment;
        public $type;
        public $amount;
        public $remarks;
        public $region;
        public $data;
        public $createdAt;
        public $updatedAt;

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

        function createEntry() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        pay_method = :pay_method,
                        delivery_pay = :delivery_pay,
                        amount = :amount,
                        type = :type,
                        remarks = :remarks,
                        region = :region,
                        data = :data;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':pay_method', $this->pay_method);
            $stmt->bindParam(':delivery_pay', $this->delivery_pay);
            $stmt->bindParam(':amount', $this->amount);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':region', $this->region);
            $stmt->bindParam(':data', $this->data);

            if ($stmt->execute()) {
                return true;
            }

            return false;

        }

        function readActiveEntry($id) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        collected = 0
                    AND
                        debt_id = {$id}
                    LIMIT 1;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }

        function readEntries($data) {
            $cust_table = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.contact_person, b.tel, b.address
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.collected = :collected
                    AND
                        a.type = :type
                    AND
                        a.data = :data
                    ORDER BY
                        a.createdAt DESC, b.company_name ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':collected', $this->collected);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':data', $data);

            $stmt->execute();

            return $stmt;
        }

        function updateEntry() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        collected = :collected,
                        comment = :comment
                    WHERE
                        debt_id = :debt_id;";

            $stmt = $this->conn->prepare($query);

            $this->comment = htmlspecialchars(strip_tags($this->comment));

            $stmt->bindParam(':collected', $this->collected);
            $stmt->bindParam(':comment', $this->comment);
            $stmt->bindParam(':debt_id', $this->debt_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

    }

    class DebtReminder {
        private $conn;
        private $table_name = 'debt_reminder';

        public $dbt_rem_id;
        public $cust_id;
        public $amt;
        public $amtpaid;
        public $comment;
        public $status;
        public $courtstatus;
        public $sentDate;
        public $courtDate;
        public $data;
        public $active;
        public $approved;

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

        public function getReminderName($status) {
            switch ($status) {
                case 0:
                    return '';
                    break;
                case 1:
                    return '1st Friendly Reminder';
                    break;
                case 2:
                    return '2nd Friendly Reminder';
                    break;
                case 3:
                    return '3rd Reminder';
                    break;
                case 4:
                    return '4th Reminder';
                    break;
                case 5:
                    return '5th & Final Reminder';
                    break;
                case 6:
                    return '48hrs Notice';
                    break;
            }
        }

        function create($data) {
            $query = "";
            switch ($data) {
                case 1:
                    $query = "INSERT INTO
                                {$this->table_name}
                            SET
                                cust_id = :cust_id,
                                amt = :amt,
                                status = :status,
                                data = :data,
                                active = :active;";
                    break;
                case 2:
                    $query = "INSERT INTO
                                {$this->table_name}
                            SET
                                cust_id = :cust_id,
                                amt = :amt,
                                status = :status,
                                data = :data,
                                active = :active;";
                    break;
            }
            
            $stmt = $this->conn->prepare($query);

            $date = date("F j, Y \a\t g:ia");

            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':amt', $this->amt);
            $stmt->bindParam(':status', $this->status);

            $stmt->bindParam(':data', $this->data);
            $stmt->bindParam(':active', $this->active);

            if ($stmt->execute()) {
                return true;
            }

            return false;
            
        }

        function read($data) {
            $cust_table = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.customerCode
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.status = :status
                    AND
                        a.data = {$data}
                    AND
                        a.active = '0';";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function findEntry($id, $data) {
            $cust_table = "";
            $lookfor = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.customerCode
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.status IN (0,1,2,3,4,5,6,7)
                    AND
                        a.data = {$data}
                    AND
                        b.cust_id = ?
                    AND
                        a.active = '0';";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $id);

            $stmt->execute();

            return $stmt;
        }

        function readAvailable($data, $cust) {
            $cust_table = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.customerCode
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.status BETWEEN 0 AND 7
                    AND
                        a.cust_id = {$cust}
                    AND
                        a.data = {$data}
                    AND
                        a.active = '0'
                    ORDER BY dbt_rem_id DESC
                    LIMIT 0, 1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row;
            } else {
                return null;
            }

        }

        function readArchive($data) {
            $cust_table = "";
            switch ($data) {
                case 1:
                    $cust_table = 'del_cust';
                    break;
                case 2:
                    $cust_table = 'rns_cust';
                    break;
                case 3:
                    $cust_table = 'pnp_cust';
                    break;
            }

            $query = "SELECT
                        a.*, b.company_name, b.customerCode
                    FROM
                        {$this->table_name} a, {$cust_table} b
                    WHERE
                        a.cust_id = b.cust_id
                    AND
                        a.data = {$data}
                    AND
                        active = '1';";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function update($data) {
            $condition = "";
            switch ($data) {
                case 1:
                    $condition = "status";
                    break;
                case 2:
                    $condition = "courtstatus";
                    break;
            }
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        {$condition} = :status,
                        sentDate = :sentDate,
                        approved = 0
                    WHERE
                        dbt_rem_id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $this->sentDate = date('Y-m-d H:i:s');

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':sentDate', $this->sentDate);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }

        }

        function updateCourtDate() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        courtDate = :courtDate
                    WHERE
                        dbt_rem_id = :id";
            
            $stmt = $this->conn->prepare($query);

            $this->courtDate = date('Y-m-d H:i:s', strtotime($this->courtDate));

            $stmt->bindParam(':courtDate', $this->courtDate);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateAmtDue() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        amt = :amt
                    WHERE
                        dbt_rem_id = :id";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':amt', $this->amt);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateAmtPaid() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        amtpaid = :amtpaid
                    WHERE
                        dbt_rem_id = :id";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':amtpaid', $this->amtpaid);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function archive() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status,
                        active = :active
                    WHERE
                        dbt_rem_id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':active', $this->active);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function sendToCourt() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status,
                        courtDate = :courtDate
                    WHERE
                        dbt_rem_id = :id";

            $stmt = $this->conn->prepare($query);

            $this->status = 7;

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':courtDate', date('Y-m-d H:i:s'));

            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function authReminder() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        approved = :approved
                    WHERE
                        dbt_rem_id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':approved', $this->approved);
            $stmt->bindParam(':id', $this->dbt_rem_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class DebtReminderComment {
        private $conn;
        private $table_name = 'debt_remcomm';

        public $dbt_comm_id;
        public $dbt_rem_id;
        public $dbt_comment;
        public $user;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function insert() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        dbt_rem_id = :dbt_rem_id,
                        dbt_comment = :dbt_comment,
                        user = :user;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dbt_rem_id', $this->dbt_rem_id);
            $stmt->bindParam(':dbt_comment', $this->dbt_comment);
            $stmt->bindParam(':user', $this->user);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read() {
            $query = "SELECT
                        a.*, b.sales_id, b.sales_rep
                    FROM
                        {$this->table_name} a, sales_representative b
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.dbt_rem_id = :dbt_rem_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dbt_rem_id', $this->dbt_rem_id);

            $stmt->execute();

            return $stmt;
        }

        function readLast($id) {
            $query = "SELECT
                        a.dbt_comment
                    FROM
                        {$this->table_name} a, sales_representative b
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.dbt_rem_id = :dbt_rem_id
                    ORDER BY a.dbt_comm_id DESC
                    LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':dbt_rem_id', $id);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($row['dbt_comment'])) {
                return $row['dbt_comment'];
            }

            return '';
        }
    }
?>