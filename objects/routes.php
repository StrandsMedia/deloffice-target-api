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