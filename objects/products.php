<?php
    class Category1 {
        private $conn;
        private $table_name = 'category1';

        public $id;
        public $position;
        public $description;
        
        public function __construct($db) {
            $this->conn = $db;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    ORDER BY
                        position ASC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }

    class Category2 {
        private $conn;
        private $table_name = 'category2';

        public $id;
        public $description;
        public $upcat;
        public $last;

        public function __construct($db) {
            $this->conn = $db;
        }

        function irr_get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            
            return $stmt;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        upcat = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->upcat);

            $stmt->execute();
            
            return $stmt;
        }
    }

    class Category3 {
        private $conn;
        private $table_name = 'category3';

        public $id;
        public $description;
        public $upcat;
        public $last;

        public function __construct($db) {
            $this->conn = $db;
        }

        function irr_get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            
            return $stmt;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        upcat = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->upcat);

            $stmt->execute();
            
            return $stmt;
        }
    }

    class Category4 {
        private $conn;
        private $table_name = 'category4';

        public $id;
        public $description;
        public $upcat;

        public function __construct($db) {
            $this->conn = $db;
        }

        function irr_get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            
            return $stmt;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        upcat = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->upcat);

            $stmt->execute();
            
            return $stmt;
        }
    }

    class Products {
        private $conn;
        private $table_name = 'products';

        public $p_id;
        public $pf_cat_id;
        public $category2;
        public $category3;
        public $category4;
        public $des1;
        public $des2;
        public $des3;
        public $puprice;
        public $coprice;
        public $spprice;
        public $boxsize;
        public $deslong1;
        public $deslong2;
        public $deslong3;
        public $deslong4;
        public $deslong5;
        public $deslong6;
        public $deslong7;
        public $familycode;
        public $oversize;
        public $taxcode;
        public $visible;
        public $new;
        public $special;
        public $features;
        public $fieldnote;
        public $printer;
        public $extrafld1;
        public $extrafld2;
        public $volume;
        public $largeproduct;
        public $avgcost;
        public $lastpurchasecost;
        public $qty;
        public $wsprice;
        public $delcityprice;
        public $delcitypromo;
        public $visible2;
        public $special2;
        public $features2;
        public $new2;
        public $video;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getProdFamily($row) {
            if (isset($row)) {
                if ($row['pf_cat_id'] === '995' && $row['category2'] === '1003') {
                    return '1';
                } else if ($row['pf_cat_id'] === '3') {
                    switch ($row['category2']) {
                        case '21':
                            return '5';
                            break;
                        case '23':
                            return '4';
                            break;
                        case '28':
                            return '3';
                            break;
                        default:
                            return '2';
                            break;
                    }
                } else if ($row['pf_cat_id'] === '996') {
                    return '9';
                } else if ($row['pf_cat_id'] === '2') {
                    return '8';
                } else if ($row['pf_cat_id'] === '414') {
                    return '6';
                } else if ($row['pf_cat_id'] === '1') {
                    return '7';
                } else {
                    return '10';
                }
            }
        }

        public function getProductCodes($cat) {
            $condition = "";

            switch ($cat) {
                case 'paper':
                    $condition = "pf_cat_id = 995 AND category2 = 1003";
                    break;
                case 'envelopes':
                    $condition = "pf_cat_id = 3 AND category2 = 21";
                    break;
                case 'files':
                    $condition = "pf_cat_id = 3 AND category2 = 23";
                    break;
                case 'pens':
                    $condition = "pf_cat_id = 3 AND category2 = 28";
                    break;
                case 'stationery':
                    $condition = "pf_cat_id = 3 AND category2 NOT IN (21, 23, 28)";
                    break;
                case 'printing':
                    $condition = "pf_cat_id = 996";
                    break;
                case 'cleaning':
                    $condition = "pf_cat_id = 2";
                    break;
                case 'ink':
                    $condition = "pf_cat_id = 414";
                    break;
                case 'messroom':
                    $condition = "pf_cat_id = 1";
                    break;
            }

            $query = "SELECT
                        p_id
                    FROM
                        {$this->table_name}
                    WHERE
                        {$condition}";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $string = "(";
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                extract($row);
                $string = $string . "'{$p_id}'";
                if ($i === $stmt->rowCount()) {
                    $string = $string . ")";
                } else {
                    $string = $string . ",";
                }
            }

            return $string;
        }

        function getProdById() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        p_id = ?";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->p_id, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getProdByName($mode, $keywords) {
            $condition = "";
            if ($mode === 1) {
                $condition = "visible = 'Y' OR visible = 'N'";
            } else {
                $condition = "visible = 'P'";
            }

            $query = "SELECT
                        p_id, des1, des2, des3, puprice, wsprice, coprice, avgcost, delcityprice, delcitypromo, visible, taxcode
                    FROM
                        {$this->table_name}
                    WHERE
                        ({$condition}) AND p_id LIKE ?
                    OR
                        ({$condition}) AND des1 LIKE ?
                    OR
                        ({$condition}) AND des2 LIKE ?
                    OR
                        ({$condition}) AND des3 LIKE ?;";

            $stmt = $this->conn->prepare($query);

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%{$keywords}%";

            $stmt->bindParam(1, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(2, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(3, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(4, $keywords, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getProdByCat($mode) {
            $condition = "";
            if ($mode === 1) {
                $condition = "visible = 'Y' OR visible = 'N'";
            } else {
                $condition = "visible = 'P'";
            }

            $query = "SELECT
                        p_id, des1, des2, des3, puprice, wsprice, coprice, avgcost, delcityprice, delcitypromo, visible, taxcode
                    FROM
                        {$this->table_name}
                    WHERE
                        ({$condition}) AND pf_cat_id = ?
                    OR
                        ({$condition}) AND category2 = ?
                    OR
                        ({$condition}) AND category3 = ?
                    OR
                        ({$condition}) AND category4 = ?;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->pf_cat_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->pf_cat_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $this->pf_cat_id, PDO::PARAM_INT);
            $stmt->bindParam(4, $this->pf_cat_id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        function search($keywords) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        p_id LIKE ?
                    OR
                        des1 LIKE ?
                    OR
                        des2 LIKE ?
                    OR
                        des3 LIKE ?;";

            $stmt = $this->conn->prepare($query);

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%{$keywords}%";

            $stmt->bindParam(1, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(2, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(3, $keywords, PDO::PARAM_STR);
            $stmt->bindParam(4, $keywords, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function isProduct() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        p_id = :p_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':p_id', $this->p_id);

            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return true;
            } 
            return false;
        }

        function watsProduct() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        p_id = :p_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':p_id', $this->p_id);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stmt->rowCount() > 0) {
                return $row['visible'];
            } else {
                return 'Y';
            }
        } 

        function insertProd() {
            $query = "INSERT
                        {$this->table_name}
                    SET
                        p_id = :p_id,
                        pf_cat_id = :pf_cat_id,
                        category2 = :category2,
                        category3 = :category3,
                        category4 = :category4,
                        des1 = :des1,
                        des2 = :des2,
                        des3 = :des3,
                        deslong1 = :deslong1,
                        deslong2 = :deslong2,
                        deslong3 = :deslong3,
                        deslong4 = :deslong4,
                        deslong5 = :deslong5,
                        deslong6 = :deslong6,
                        deslong7 = :deslong7,
                        puprice = :puprice,
                        coprice = :coprice,
                        wsprice = :wsprice,
                        delcityprice = :delcityprice,
                        delcitypromo = :delcitypromo,
                        taxcode = :taxcode,
                        visible = :visible,
                        avgcost = :avgcost;";

            $stmt = $this->conn->prepare($query);

            $this->des1 = htmlspecialchars(strip_tags($this->des1));
            $this->des2 = htmlspecialchars(strip_tags($this->des2));
            $this->des3 = htmlspecialchars(strip_tags($this->des3));

            $this->deslong1 = htmlspecialchars(strip_tags($this->deslong1));
            $this->deslong2 = htmlspecialchars(strip_tags($this->deslong2));
            $this->deslong3 = htmlspecialchars(strip_tags($this->deslong3));
            $this->deslong4 = htmlspecialchars(strip_tags($this->deslong4));
            $this->deslong5 = htmlspecialchars(strip_tags($this->deslong5));
            $this->deslong6 = htmlspecialchars(strip_tags($this->deslong6));
            $this->deslong7 = htmlspecialchars(strip_tags($this->deslong7));

            $stmt->bindParam(':p_id', $this->p_id, PDO::PARAM_STR);

            $stmt->bindParam(':pf_cat_id', $this->pf_cat_id, PDO::PARAM_INT);
            $stmt->bindParam(':category2', $this->category2, PDO::PARAM_INT);
            $stmt->bindParam(':category3', $this->category3, PDO::PARAM_INT);
            $stmt->bindParam(':category4', $this->category4, PDO::PARAM_INT);

            $stmt->bindParam(':des1', $this->des1, PDO::PARAM_STR);
            $stmt->bindParam(':des2', $this->des2, PDO::PARAM_STR);
            $stmt->bindParam(':des3', $this->des3, PDO::PARAM_STR);

            $stmt->bindParam(':deslong1', $this->deslong1, PDO::PARAM_STR);
            $stmt->bindParam(':deslong2', $this->deslong2, PDO::PARAM_STR);
            $stmt->bindParam(':deslong3', $this->deslong3, PDO::PARAM_STR);
            $stmt->bindParam(':deslong4', $this->deslong4, PDO::PARAM_STR);
            $stmt->bindParam(':deslong5', $this->deslong5, PDO::PARAM_STR);
            $stmt->bindParam(':deslong6', $this->deslong6, PDO::PARAM_STR);
            $stmt->bindParam(':deslong7', $this->deslong7, PDO::PARAM_STR);

            $stmt->bindParam(':puprice', $this->puprice);
            $stmt->bindParam(':coprice', $this->coprice);
            $stmt->bindParam(':wsprice', $this->wsprice);
            $stmt->bindParam(':delcityprice', $this->delcityprice);
            $stmt->bindParam(':delcitypromo', $this->delcitypromo);
            $stmt->bindParam(':taxcode', $this->taxcode);
            $stmt->bindParam(':visible', $this->visible);
            $stmt->bindParam(':avgcost', $this->avgcost);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }

        function updateProd() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        pf_cat_id = :pf_cat_id,
                        category2 = :category2,
                        category3 = :category3,
                        category4 = :category4,
                        des1 = :des1,
                        des2 = :des2,
                        des3 = :des3,
                        deslong1 = :deslong1,
                        deslong2 = :deslong2,
                        deslong3 = :deslong3,
                        deslong4 = :deslong4,
                        deslong5 = :deslong5,
                        deslong6 = :deslong6,
                        deslong7 = :deslong7,
                        puprice = :puprice,
                        coprice = :coprice,
                        wsprice = :wsprice,
                        delcityprice = :delcityprice,
                        delcitypromo = :delcitypromo,
                        taxcode = :taxcode,
                        visible = :visible,
                        avgcost = :avgcost
                    WHERE
                        p_id = :p_id;";

            $stmt = $this->conn->prepare($query);

            $this->des1 = htmlspecialchars(strip_tags($this->des1));
            $this->des2 = htmlspecialchars(strip_tags($this->des2));
            $this->des3 = htmlspecialchars(strip_tags($this->des3));

            $this->deslong1 = htmlspecialchars(strip_tags($this->deslong1));
            $this->deslong2 = htmlspecialchars(strip_tags($this->deslong2));
            $this->deslong3 = htmlspecialchars(strip_tags($this->deslong3));
            $this->deslong4 = htmlspecialchars(strip_tags($this->deslong4));
            $this->deslong5 = htmlspecialchars(strip_tags($this->deslong5));
            $this->deslong6 = htmlspecialchars(strip_tags($this->deslong6));
            $this->deslong7 = htmlspecialchars(strip_tags($this->deslong7));

            $stmt->bindParam(':p_id', $this->p_id, PDO::PARAM_STR);

            $stmt->bindParam(':pf_cat_id', $this->pf_cat_id, PDO::PARAM_INT);
            $stmt->bindParam(':category2', $this->category2, PDO::PARAM_INT);
            $stmt->bindParam(':category3', $this->category3, PDO::PARAM_INT);
            $stmt->bindParam(':category4', $this->category4, PDO::PARAM_INT);

            $stmt->bindParam(':des1', $this->des1, PDO::PARAM_STR);
            $stmt->bindParam(':des2', $this->des2, PDO::PARAM_STR);
            $stmt->bindParam(':des3', $this->des3, PDO::PARAM_STR);

            $stmt->bindParam(':deslong1', $this->deslong1, PDO::PARAM_STR);
            $stmt->bindParam(':deslong2', $this->deslong2, PDO::PARAM_STR);
            $stmt->bindParam(':deslong3', $this->deslong3, PDO::PARAM_STR);
            $stmt->bindParam(':deslong4', $this->deslong4, PDO::PARAM_STR);
            $stmt->bindParam(':deslong5', $this->deslong5, PDO::PARAM_STR);
            $stmt->bindParam(':deslong6', $this->deslong6, PDO::PARAM_STR);
            $stmt->bindParam(':deslong7', $this->deslong7, PDO::PARAM_STR);

            $stmt->bindParam(':puprice', $this->puprice);
            $stmt->bindParam(':coprice', $this->coprice);
            $stmt->bindParam(':wsprice', $this->wsprice);
            $stmt->bindParam(':delcityprice', $this->delcityprice);
            $stmt->bindParam(':delcitypromo', $this->delcitypromo);
            $stmt->bindParam(':taxcode', $this->taxcode);
            $stmt->bindParam(':visible', $this->visible);
            $stmt->bindParam(':avgcost', $this->avgcost);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class ProductFamily {
        private $conn;
        private $table_name = 'products_family';

        public $pf_id;
        public $pf_cat_id;
        public $pf_subcat_id;
        public $pf_name;

        public function __construct($db) {
            $this->conn = $db;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";
            
            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }
    }

    class Target {
        private $conn;
        private $table_name = 'target';

        public $tar_id;
        public $cust_id;
        public $pf_id;
        public $p_id;
        public $pricecat_id;
        public $customprice;
        public $validity_date;
        public $createdAt;
        public $updatedAt;
        public $tar_notes;
        public $user;

        public function __construct($db) {
            $this->conn = $db;
        }

        function insertTgtPrice() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        cust_id = :cust_id,
                        pf_id = :pf_id,
                        p_id = :p_id,
                        pricecat_id = :pricecat_id,
                        customprice = :customprice,
                        validity_date = :validity_date,
                        tar_notes = :tar_notes,
                        user = :user;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id, PDO::PARAM_INT);
            $stmt->bindParam(':pf_id', $this->pf_id, PDO::PARAM_INT);
            $stmt->bindParam(':p_id', $this->p_id, PDO::PARAM_INT);
            $stmt->bindParam(':pricecat_id', $this->pricecat_id, PDO::PARAM_INT);
            $stmt->bindParam(':customprice', $this->customprice, PDO::PARAM_NULL);
            $stmt->bindParam(':validity_date', $this->validity_date, PDO::PARAM_NULL);
            $stmt->bindParam(':tar_notes', $this->tar_notes);
            $stmt->bindParam(':user', $this->user, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }

        function getTgtByCust() {
            $query = "SELECT
                        x.tar_id,
                        a.p_id, a.customprice, a.validity_date, a.createdAt, a.tar_notes, a.pricecat_id,
                        b.des1, b.des2, b.des3, b.pf_cat_id, b.category2, b.puprice, b.coprice, b.spprice, b.wsprice, b.delcityprice, b.delcitypromo,
                        c.sales_rep
                    FROM
                        {$this->table_name} a, products b, sales_representative c,
                        (SELECT MAX(tar_id) as tar_id, cust_id, p_id FROM {$this->table_name} WHERE cust_id = :cust_id GROUP BY p_id) x
                    WHERE
                        a.p_id = b.p_id
                    AND
                        a.user = c.sales_id
                    AND
                        a.cust_id = x.cust_id
                    AND
                        a.tar_id = x.tar_id
                    AND
                        DATEDIFF(DATE(a.validity_date), DATE(NOW())) > 0;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cust_id', $this->cust_id);

            $stmt->execute();

            return $stmt;
        }

        function getTgtByCustProd() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        tar_id = :tar_id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':tar_id', $this->tar_id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;
        }

        
    }
?>