<?php
    class DelOfficeFTP {
        private $host;
        private $user;
        private $pass;
        private $conn;

        private $remoteFile;
        private $localFile;

        public function __construct($host, $user, $pass) {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;

            $this->conn = ftp_connect($this->host) or die('Could not connect to $host');

            if (ftp_login($this->conn, $this->user, $this->pass)) {
                return true;
            }
            
            return false;
        }

        function ftpClose() {
            ftp_close($this->conn);
        }

        function ftpCopyFile($remoteFile, $localFile, $dir) {
            $this->remoteFile = $remoteFile;
            $this->localFile = $localFile;

            if (ftp_chdir($this->conn, $dir)) {

                if (ftp_size($this->conn, $remoteFile) != -1) {
                    // File exists
                    if (ftp_chmod($this->conn, 0666, $this->remoteFile) !== false) {
                        if (ftp_put($this->conn, $this->remoteFile, $this->localFile, FTP_BINARY)) {
                            return true;
                        }
            
                        return false;
                    }
                    return false;
                } else {
                    // File does not exist
                    if (ftp_put($this->conn, $this->remoteFile, $this->localFile, FTP_BINARY)) {
                        return true;
                    }
        
                    return false;
                }

            }

            return false;

        }
    }
?>