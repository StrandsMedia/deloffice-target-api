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
                        FROM {$this->table_name} {$condition} ORDER BY createdAt ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
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
    }

?>