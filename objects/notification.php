<?php
    class UserTasks {
        private $conn;
        private $table_name = 'user_tasks';

        public $task_id;
        public $taskname;
        public $status;
        public $user;
        public $assignedTo;
        public $acknowledged;
        public $origin;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function deptArray($dept) {
            $query = "SELECT
                        sales_id
                    FROM
                        sales_representative
                    WHERE
                        dept = ?
                    AND
                        status = 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $dept);

            $stmt->execute();

            $arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($arr, $row['sales_id']);
            }

            return $arr;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        taskname = :taskname,
                        status = :status,
                        user = :user,
                        assignedTo = :assignedTo,
                        acknowledged = :acknowledged,
                        origin = :origin;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':taskname', $this->taskname);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':user', $this->user);
            $stmt->bindParam(':assignedTo', $this->assignedTo);
            $stmt->bindParam(':acknowledged', $this->acknowledged);
            $stmt->bindParam(':origin', $this->origin);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function read($num) {
            $condition = "";

            switch ($num) {
                case 0:
                    $condition = " a.user = ? AND a.assignedTo = a.user AND a.status = 0 ";
                    break;
                case 1:
                    $condition = " a.user = ? AND a.assignedTo = a.user AND a.status = 1 ";
                    break;
                case 2:
                    $condition = " a.assignedTo = ? AND a.assignedTo != a.user AND a.status = 0 AND a.acknowledged = 0 ";
                    break;
                case 3:
                    $condition = " a.user = ? AND a.user != a.assignedTo ";
                    break;
            }

            $query = "SELECT
                        a.*, b.sales_rep as username, c.sales_rep as userassigned
                    FROM
                        {$this->table_name} a, sales_representative b, sales_representative c
                    WHERE
                        a.user = b.sales_id
                    AND
                        a.assignedTo = c.sales_id
                    AND
                        {$condition}
                    ORDER BY
                        a.task_id DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->user);

            $stmt->execute();

            return $stmt;
        }

        function markAsAcknowledged() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        acknowledged = ?
                    WHERE
                        task_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->acknowledged, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->task_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function markAsDone() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = ?
                    WHERE
                        task_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->status, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->task_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class UserReminders {
        private $conn;
        private $table_name = 'user_reminders';

        public $reminder_id;
        public $reminder_name;
        public $reminder_time;
        public $status;
        public $user;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        reminder_name = :reminder_name,
                        reminder_time = :reminder_time,
                        user = :user;";

            $stmt = $this->conn->prepare($query);

            $this->reminder_time = isset($this->reminder_time) ? date("Y-m-d H:i:s", strtotime($this->reminder_time)) : NULL;

            $stmt->bindParam(':reminder_name', $this->reminder_name);
            $stmt->bindParam(':reminder_time', $this->reminder_time);
            $stmt->bindParam(':user', $this->user);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function getReminder() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        user = :user
                    AND
                        status = :status
                    ORDER BY
                        reminder_id DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user', $this->user, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function remindMe() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        user = :user
                    AND
                        status = :status
                    AND
                        NOW() >= reminder_time;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user', $this->user, PDO::PARAM_INT);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function dismiss() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        status = :status
                    WHERE
                        reminder_id = :reminder_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':reminder_id', $this->reminder_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function snooze() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        reminder_time = :reminder_time
                    WHERE
                        reminder_id = :reminder_id;";

            $stmt = $this->conn->prepare($query);

            $this->reminder_time = isset($this->reminder_time) ? date("Y-m-d", strtotime($this->reminder_time)) : NULL;

            $stmt->bindParam(':reminder_time', $this->reminder_time);
            $stmt->bindParam(':reminder_id', $this->reminder_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }
?>