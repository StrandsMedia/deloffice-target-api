<?php
    class Route {
        private $conn;
        private $table_name = 'routes';
        
        public $routeId;
        public $routeRef;
        public $routeName;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT 
                        {$this->table_name}
                      SET
                      `routeRef`=:routeRef, `routeName`=:routeName;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":routeRef", $this->routeRef);
            $stmt->bindParam(":routeName", $this->routeName);

            if ($stmt->execute()) {
                return true;
            };

            return false;
        }

        function read($routeId) {
            $condition = "";

            if (isset($routeId)) {
                $condition = "WHERE routeId = {$routeId}";
            } 
            $query = "SELECT *
                        FROM {$this->table_name} {$condition} ORDER BY createdAt ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getNextRef($prefix) {
            $query = "SELECT * FROM {$this->table_name} WHERE routeRef LIKE ? ORDER BY routeRef DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $newprefix = $prefix . "%";

            $stmt->bindParam(1, $newprefix);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $newone = str_split($row['routeRef'], 3);

                $number = intval($newone[1]) + 1;


                return $newone[0] . sprintf('%03d', $number);


            } else {
                return $prefix . '001';
            }
        }
    }

    class Location {
        private $conn;
        private $table_name = 'locations';

        public $locationId;
        public $locationRef;
        public $locationName;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function create() {
            $query = "INSERT 
                        {$this->table_name}
                      SET
                      `locationRef`=:locationRef, `locationName`=:locationName;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":locationRef", $this->locationRef);
            $stmt->bindParam(":locationName", $this->locationName);

            if ($stmt->execute()) {
                return true;
            };

            return false;
        }

        function read($locationId) {
            $condition = "";

            if (isset($locationId)) {
                $condition = "WHERE routeId = {$locationId}";
            } 
            $query = "SELECT *
                        FROM {$this->table_name} {$condition} ORDER BY locationRef ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getNextRef($prefix) {
            $query = "SELECT * FROM {$this->table_name} WHERE locationRef LIKE ? ORDER BY locationRef DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $newprefix = $prefix . "%";

            $stmt->bindParam(1, $newprefix);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $newone = str_split($row['locationRef'], 3);

                $number = intval($newone[1]) + 1;


                return $newone[0] . sprintf('%03d', $number);


            } else {
                return $prefix . '001';
            }
        }
    }

    class RouteLocation {
        private $conn;
        private $table_name = 'route_location';

        public $routelocId;
        public $routeId;
        public $locationId;
        public $rank;
        public $createdAt;
        public $updatedAt;

        public function __construct($db) {
            $this->conn = $db;
        }

        function readByRoute() {
            $query = "SELECT a.*, b.locationRef, b.locationName FROM {$this->table_name} a, locations b WHERE a.locationId = b.locationId AND a.routeId = ? ORDER BY a.rank;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->routeId);

            $stmt->execute();

            $data_arr = array();

            while($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row2);

                $location_item = array(
                    'locationId' => $row2['locationId'],
                    'locationRef' => $row2['locationRef'],
                    'locationName' => $row2['locationName'],
                    'rank' => $row2['rank'],
                    'routelocId' => $row2['routelocId'],
                    'routeId' => $row2['routeId']
                );

                array_push($data_arr, $location_item);
            }

            return $data_arr;
        }

        function getNextRank() {
            $query = "SELECT * FROM {$this->table_name} WHERE routeId = ? ORDER BY rank DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->routeId);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return +$row['rank'] + 1;
            } else {
                return 1;
            }            
        }

        function getNextRankItem() {
            $query = "SELECT * FROM {$this->table_name} WHERE rank = ? AND routeId = ? ORDER BY rank DESC LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, +$this->rank + 1);
            $stmt->bindParam(2, $this->routeId);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row;
            }
        }

        function changeRank() {
            $query = "UPDATE {$this->table_name} SET rank = ? WHERE routelocId = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->rank);
            $stmt->bindParam(2, $this->routelocId);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function switchRank($routelocId) {
            $query = "SELECT * FROM {$this->table_name} WHERE routeId = ? AND ;";
        }

        function create() {
            $query = "INSERT
                        {$this->table_name}
                    SET
                        routeId = :routeId,
                        locationId = :locationId,
                        `rank` = :rnk;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':routeId', $this->routeId);
            $stmt->bindParam(':locationId', $this->locationId);
            $stmt->bindParam(':rnk', $this->rank);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

?>