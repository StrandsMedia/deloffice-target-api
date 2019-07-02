<?php
    class Customer {
        public $conn;
        public $table_name;

        public $cust_id;
        public $customerCode;
        public $company_name;
        public $address;
        public $address2;
        public $address3;

        public $location;
        public $location2;
        public $location3;

        public $notes;
        public $tel;
        public $tel2;
        public $tel3;
        public $tel_acc;
        public $fax;
        public $fax2;
        public $fax3;
        public $fax_acc;
        public $mob;
        public $mob2;
        public $mob3;
        public $mob_acc;
        public $email;
        public $email2;
        public $email3;
        public $email_acc;
        public $contact_person;
        public $title;
        public $contact_person2;
        public $title2;
        public $contact_person3;
        public $title3;
        public $contact_person_acc;
        public $category;
        public $sector;
        public $subsector;
        public $region;
        public $statusDefault;
        public $priceDefault;
        public $createdAt;
        public $updatedAt;
        public $sector_name;
        public $subsector_name;

        public function __construct($db) {
            $this->conn = $db;
        }

        // Create

        function create() {
            $query = "INSERT INTO
                    {$this->table_name}
                SET
                    customerCode=:customerCode, company_name=:company_name, contact_person=:contact_person, email=:email, tel=:tel, fax=:fax, mob=:mob, address=:address, sector=:sector, subsector=:subsector, category=:category, location=:location;";

            $stmt = $this->conn->prepare($query);

            $this->customerCode = htmlspecialchars(strip_tags($this->customerCode));
            $this->company_name = htmlspecialchars(strip_tags($this->company_name));
            $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
            $this->address = htmlspecialchars(strip_tags($this->address));
            $this->location = htmlspecialchars(strip_tags($this->location));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->tel = htmlspecialchars(strip_tags($this->tel));
            $this->fax = htmlspecialchars(strip_tags($this->fax));
            $this->mob = htmlspecialchars(strip_tags($this->mob));
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->sector = htmlspecialchars(strip_tags($this->sector));
            $this->subsector = htmlspecialchars(strip_tags($this->subsector));

            $stmt->bindParam(":customerCode", $this->customerCode);
            $stmt->bindParam(":company_name", $this->company_name);
            $stmt->bindParam(":contact_person", $this->contact_person);
            $stmt->bindParam(":address", $this->address);
            $stmt->bindParam(":location", $this->location);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":tel", $this->tel);
            $stmt->bindParam(":fax", $this->fax);
            $stmt->bindParam(":mob", $this->mob);
            $stmt->bindParam(":category", $this->category);
            $stmt->bindParam(":sector", $this->sector);
            $stmt->bindParam(":subsector", $this->subsector);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        // Read

        function read($keywords) {
            if (isset($keywords)) {
                $keywords = htmlspecialchars(strip_tags($keywords));
                $keywords = "'%{$keywords}%'";
                $keywords = " WHERE company_name LIKE " . $keywords . " OR customerCode LIKE " . $keywords . "";
            }

            $query = "SELECT cust_id, company_name, contact_person, tel, fax, mob, email, notes, updatedAt FROM " . $this->table_name . $keywords . " ORDER BY updatedAt DESC LIMIT 15;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function readOne() {
            $query = "SELECT a.*, b.category_name as sector_name, c.category_name as subsector_name FROM " . $this->table_name . " a, sector b, subsector c WHERE b.cat_id = a.sector AND c.cat_id = a.subsector AND c.upcat = b.cat_id AND a.cust_id = ?";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id);

            $stmt->execute();

            if ($stmt->rowCount() < 1) {
                $query = "SELECT a.* FROM " . $this->table_name . " a WHERE a.cust_id = ?";
    
                $stmt = $this->conn->prepare($query);
    
                $stmt->bindParam(1, $this->cust_id);
    
                $stmt->execute();
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->cust_id = $row['cust_id'];
            $this->company_name = $row['company_name'];
            $this->customerCode = $row['customerCode'];
            $this->category = $row['category'];
            $this->sector = $row['sector'];
            $this->subsector = $row['subsector'];
            $this->sector_name = isset($row['sector_name']) ? $row['sector_name'] : null;
            $this->subsector_name = isset($row['subsector_name']) ? $row['subsector_name'] : null;
            $this->address = $row['address'];
            $this->address2 = $row['address2'];
            $this->address3 = $row['address3'];
            $this->location = $row['location'];
            $this->location2 = $row['location2'];
            $this->location3 = $row['location3'];
            $this->notes = $row['notes'];
            $this->comment = $row['comment'];
            $this->contact_person = $row['contact_person'];
            $this->tel = $row['tel'];
            $this->fax = $row['fax'];
            $this->mob = $row['mob'];
            $this->email = $row['email'];
            $this->contact_person2 = $row['contact_person2'];
            $this->tel2 = $row['tel2'];
            $this->fax2 = $row['fax2'];
            $this->mob2 = $row['mob2'];
            $this->email2 = $row['email2'];
            $this->contact_person3 = $row['contact_person3'];
            $this->tel3 = $row['tel3'];
            $this->fax3 = $row['fax3'];
            $this->mob3 = $row['mob3'];
            $this->email3 = $row['email3'];
            $this->contact_person_acc = $row['contact_person_acc'];
            $this->tel_acc = $row['tel_acc'];
            $this->fax_acc = $row['fax_acc'];
            $this->mob_acc = $row['mob_acc'];
            $this->email_acc = $row['email_acc'];
            $this->createdAt = $row['createdAt'];
            $this->updatedAt = $row['updatedAt'];
        }

        function search($keywords) {
            $query = "SELECT cust_id, company_name, customerCode FROM {$this->table_name} WHERE company_name LIKE ? OR customerCode LIKE ? ORDER BY company_name ASC LIMIT 5;";

            $stmt = $this->conn->prepare($query);

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%{$keywords}%";

            $stmt->bindParam(1, $keywords);
            $stmt->bindParam(2, $keywords);

            $stmt->execute();

            return $stmt;
        }

        function searchCust($keywords) {
            $query = "SELECT cust_id, company_name, customerCode, address FROM {$this->table_name} WHERE company_name LIKE ? AND customerCode != '' ORDER BY company_name ASC LIMIT 5;";

            $stmt = $this->conn->prepare($query);

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%{$keywords}%";

            $stmt->bindParam(1, $keywords);

            $stmt->execute();

            return $stmt;
        }

        function fetchDefaultPrice() {
            $query = "SELECT
                        priceDefault
                    FROM
                        {$this->table_name}
                    WHERE
                        cust_id = :cust_id;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id);

            $stmt->execute();

            $num = $stmt->rowCount();

            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['priceDefault'];
            } else {
                return null;
            }

        }

        // Update

        function updateProfile() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        company_name = :company_name,
                        customerCode = :customerCode,
                        address = :address,
                        address2 = :address2,
                        address3 = :address3,

                        location = :location,
                        location2 = :location2,
                        location3 = :location3,

                        category = :category,
                        sector = :sector,
                        subsector = :subsector
                    WHERE
                        cust_id = :cust_id;";

            $stmt = $this->conn->prepare($query);

            $this->company_name = strip_tags($this->company_name);
            $this->address = htmlspecialchars(strip_tags($this->address));

            $stmt->bindParam(':company_name', $this->company_name);
            $stmt->bindParam(':customerCode', $this->customerCode);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':address2', $this->address2);
            $stmt->bindParam(':address3', $this->address3);

            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':location2', $this->location2);
            $stmt->bindParam(':location3', $this->location3);

            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':sector', $this->sector);
            $stmt->bindParam(':subsector', $this->subsector);
            $stmt->bindParam(':cust_id', $this->cust_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function existAll($customerCode) {
            $query = "SELECT * FROM {$this->table_name} a WHERE a.customerCode = '{$customerCode}' LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function insertAll($row) {
            $query = "INSERT INTO
                    {$this->table_name}
                SET
                    customerCode=:customerCode, company_name=:company_name, address=:address;";

            $stmt = $this->conn->prepare($query);

            $address =  $row['Physical1'] . ' ' . $row['Physical2'] . ' ' . $row['Physical3'] . ' ' . $row['Physical4'];

            $stmt->bindParam(":customerCode", $row['Account']);
            $stmt->bindParam(":company_name", $row['Name']);
            $stmt->bindParam(":address", $address);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateAll($row) {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        company_name = :company_name,
                        address = :address
                    WHERE
                        customerCode = :acc;";

            $stmt = $this->conn->prepare($query);

            $address =  $row['Physical1'] . ' ' . $row['Physical2'] . ' ' . $row['Physical3'] . ' ' . $row['Physical4'];

            $stmt->bindParam(':company_name', $row['Name']);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':acc', $row['Account']);

            return $query;

            // if ($stmt->execute()) {
            //     return true;
            // }

            // return false;
        }

        function updateDetails($id) {
            $condition = "";
            switch ($id) {
                case 1:
                    $condition = "contact_person = :contact_person, tel = :tel, fax = :fax, mob = :mob, email = :email ";
                    break;
                case 2:
                    $condition = "contact_person2 = :contact_person, tel2 = :tel, fax2 = :fax, mob2 = :mob, email2 = :email ";
                    break;
                case 3:
                    $condition = "contact_person3 = :contact_person, tel3 = :tel, fax3 = :fax, mob3 = :mob, email3 = :email ";
                    break;
                case 4:
                    $condition = "contact_person_acc = :contact_person, tel_acc = :tel, fax_acc = :fax, mob_acc = :mob, email_acc = :email ";
                    break;
            }

            $query = "UPDATE
                    {$this->table_name}
                SET
                    {$condition}
                WHERE
                    cust_id = :id";

            $stmt = $this->conn->prepare($query);

            $this->cust_id = htmlspecialchars(strip_tags($this->cust_id));
            $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
            $this->tel = htmlspecialchars(strip_tags($this->tel));
            $this->mob = htmlspecialchars(strip_tags($this->mob));
            $this->fax = htmlspecialchars(strip_tags($this->fax));
            $this->email = htmlspecialchars(strip_tags($this->email));

            $stmt->bindParam(":id", $this->cust_id);
            $stmt->bindParam(":contact_person", $this->contact_person);
            $stmt->bindParam(":tel", $this->tel);
            $stmt->bindParam(":mob", $this->mob);
            $stmt->bindParam(":fax", $this->fax);
            $stmt->bindParam(":email", $this->email);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateComment() {
            $query = "UPDATE
                    {$this->table_name}
                SET
                    comment = :comment
                WHERE
                    cust_id = :id";

            $stmt = $this->conn->prepare($query);

            $this->cust_id = htmlspecialchars(strip_tags($this->cust_id));
            $this->comment = htmlspecialchars(strip_tags($this->comment));

            $stmt->bindParam(':comment', $this->comment);
            $stmt->bindParam(':id', $this->cust_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        // Delete

        function delete() {
            $query = "DELETE FROM " . $this->table_name . " WHERE cust_id = ?";

            $stmt = $this->conn->prepare($query);

            $this->cust_id = htmlspecialchars(strip_tags($this->cust_id));

            $stmt->bindParam(1, $this->cust_id);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        // Get Details

        function getCustDetails($data, $cust_id) {
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

            $query = "SELECT * FROM {$table} WHERE cust_id = ? LIMIT 0,1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cust_id);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        }
    }

    class DelCustomer extends Customer {
        public $table_name = 'del_cust';
    }

    class RnsCustomer extends Customer {
        public $table_name = 'rns_cust';
    }

    class PnpCustomer extends Customer {
        public $table_name = 'pnp_cust';
    }

    class StatusCust {
        private $conn;
        private $table_name = 'status_cust';

        public $sta_id;
        public $cust_id;
        public $pf_id;
        public $statusNum;

        public function __construct($db) {
            $this->conn = $db;
        }

        function insertStatus() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        statusNum = :status,
                        pf_id = :pf_id,
                        cust_id = :cust_id;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->statusNum);
            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':pf_id', $this->pf_id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }

        function getStatusByCust() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        cust_id = :cust_id";
                
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id);

            $stmt->execute();

            return $stmt;
        }

        function getStatusByCustOne() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        cust_id = :cust_id
                    AND
                        pf_id = :pf_id;";
                
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':pf_id', $this->pf_id);

            $stmt->execute();

            return $stmt->rowCount();
        }

        function updateStatus() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        statusNum = :status
                    WHERE
                        pf_id = :pf_id
                    AND
                        cust_id = :cust_id;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':status', $this->statusNum);
            $stmt->bindParam(':cust_id', $this->cust_id);
            $stmt->bindParam(':pf_id', $this->pf_id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
  
?>