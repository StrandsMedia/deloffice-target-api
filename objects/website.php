<?php
    class Product {
        private $conn;
        private $table_name = 'product';

        public $id;
        public $category1;
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

        function insert() {
            $query = "INSERT
                        {$this->table_name}
                    SET
                        id = :id,
                        category1 = :category1,
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

            $stmt->bindParam(':id', $this->id, PDO::PARAM_STR);

            $stmt->bindParam(':category1', $this->category1, PDO::PARAM_INT);
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

        function isProductLive() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        id = :id;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $this->id);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        }

        function update() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        category1 = :category1,
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
                        id = :id;";

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

            $stmt->bindParam(':id', $this->id, PDO::PARAM_STR);

            $stmt->bindParam(':category1', $this->category1, PDO::PARAM_INT);
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
?>