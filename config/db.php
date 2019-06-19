<?php
    class Database {
        private $host = 'localhost';
        private $db_name = 'test_database';
        private $username = 'root';
        private $password = 'root';
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
                $this->conn->exec('set names utf8');
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $exception) {
                echo 'Connection error: ' . $exception->getMessage();
            }

            return $this->conn;
        }
    }

    class ServerDatabase {
        public $servername = "192.168.100.250";
        public $database = "DELLOFFICE";
        public $uid = "sa";
        public $pwd = "$0ftware";
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("sqlsrv:Server={$this->servername},53220;Database={$this->database};", $this->uid, $this->pwd);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                // echo json_encode(array(
                //     'status' => 'error',
                //     'message' => 'Error connecting to SQL Server: ' . $exception->getMessage()
                // ));
            }

            return $this->conn;
        }
    }

    class DelServerDatabase extends ServerDatabase {
        public $servername = "192.168.100.250";
        public $database = "DELLOFFICE";
    }
    class RnsServerDatabase extends ServerDatabase {
        public $servername = "192.168.100.250";
        public $database = "ROLLNSHEETLTD";
    }
    class PnpServerDatabase extends ServerDatabase {
        public $servername = "192.168.100.250";
        public $database = "IBOLO LTD";
    }


    class CommonServerDatabase {
        private $servername = "192.168.100.250";
        private $database = "EvolutionCommon";
        private $uid = "sa";
        private $pwd = "$0ftware";
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("sqlsrv:Server={$this->servername},53220;Database={$this->database};", $this->uid, $this->pwd);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                // echo json_encode(array(
                //     'status' => 'error',
                //     'message' => 'Error connecting to SQL Server: ' . $exception->getMessage()
                // ));
            }

            return $this->conn;
        }
    }

    class WebServerDatabase {
        private $host = '88.202.190.84';
        private $db_name = 'deloffic_web2';
        private $username = 'deloffic_admin';
        private $password = 'deloffic*';
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
                $this->conn->exec('set names utf8');
            } catch (PDOException $exception) {
                echo 'Connection error: ' . $exception->getMessage();
            }

            return $this->conn;
        }
    }
?>