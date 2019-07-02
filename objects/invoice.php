<?php
    class Invoice {
        private $conn;
        private $table_name = 'invoice';

        public $invoice_id;
        public $company_name;
        public $Contact_Person;
        public $Telephone;
        public $Physical1;
        public $Physical2;
        public $Physical3;
        public $Physical4;
        public $Registration;
        public $Tax_Number;
        public $customerCode;
        public $iARPriceListNameID;
        public $TotalExcl;
        public $TotalTax;
        public $TotalIncl;
        public $InvDate;
        public $InvStatus;
        public $DCLink;
        public $user;
        public $edited;
        public $workflow_id;
        public $invNumber;
        public $poNumber;
        public $notes;
        public $DocTypeID;
        public $invRef;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function invoiceGen($input, $pad_len = 7, $prefix = null) {
            if ($pad_len <= strlen($input))
                trigger_error('<strong>$pad_len</strong> cannot be less than or equal to the length of <strong>$input</strong> to generate invoice number', E_USER_ERROR);
        
            if (is_string($prefix))
                return sprintf("%s%s", $prefix, str_pad($input, $pad_len, "0", STR_PAD_LEFT));
        
            return str_pad($input, $pad_len, "0", STR_PAD_LEFT);
        }

        function createProforma() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        company_name = :company_name,
                        customerCode = :customerCode,
                        user = :user,
                        workflow_id = :workflow_id,
                        InvDate = CURRENT_TIMESTAMP,
                        InvStatus = 1;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':company_name', $this->company_name);
            $stmt->bindParam(':customerCode', $this->customerCode);
            $stmt->bindParam(':user', $this->user);
            $stmt->bindParam(':workflow_id', $this->workflow_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function createInvoice() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        company_name = :company_name,
                        Contact_Person = :Contact_Person,
                        Telephone = :Telephone,
                        Physical1 = :Physical1,
                        Physical2 = :Physical2,
                        Physical3 = :Physical3,
                        Physical4 = :Physical4,
                        Registration = :Registration,
                        Tax_Number = :Tax_Number,
                        customerCode = :customerCode,
                        iARPriceListNameID = :iARPriceListNameID,
                        TotalExcl = :TotalExcl,
                        TotalTax = :TotalTax,
                        TotalIncl = :TotalIncl,
                        DCLink = :DCLink,
                        user = :user,
                        InvStatus = :InvStatus,
                        workflow_id = :workflow_id,
                        invNumber = :invNumber,
                        poNumber = :poNumber,
                        notes = :notes;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':company_name', $this->company_name);
            $stmt->bindParam(':Contact_Person', $this->Contact_Person);
            $stmt->bindParam(':Telephone', $this->Telephone);
            $stmt->bindParam(':Physical1', $this->Physical1);
            $stmt->bindParam(':Physical2', $this->Physical2);
            $stmt->bindParam(':Physical3', $this->Physical3);
            $stmt->bindParam(':Physical4', $this->Physical4);
            $stmt->bindParam(':Registration', $this->Registration);
            $stmt->bindParam(':Tax_Number', $this->Tax_Number);
            $stmt->bindParam(':customerCode', $this->customerCode);
            $stmt->bindParam(':iARPriceListNameID', $this->iARPriceListNameID);
            $stmt->bindParam(':TotalExcl', $this->TotalExcl);
            $stmt->bindParam(':TotalTax', $this->TotalTax);
            $stmt->bindParam(':TotalIncl', $this->TotalIncl);
            $stmt->bindParam(':DCLink', $this->DCLink);
            $stmt->bindParam(':user', $this->user);
            $stmt->bindParam(':InvStatus', $this->InvStatus);
            $stmt->bindParam(':workflow_id', $this->workflow_id);
            $stmt->bindParam(':invNumber', $this->invNumber);
            $stmt->bindParam(':poNumber', $this->poNumber);
            $stmt->bindParam(':notes', $this->notes);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function getInvoices($status) {
            $query = "SELECT 
                a.invoice_id, a.company_name, a.customerCode, a.InvDate, a.InvStatus, a.TotalIncl, a.TotalExcl, a.TotalTax, a.poNumber, a.invNumber, a.workflow_id, a.notes, b.cust_id, c.sales_rep
            FROM 
                {$this->table_name} a,
                del_cust b,
                sales_representative c
            WHERE 
                a.customerCode = b.customerCode 
            AND 
                a.user = c.sales_id 
            AND 
                a.InvStatus = '$status' 
            ORDER BY 
                a.invoice_id DESC;";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getInvoice() {
            $query = "SELECT 
                a.*, b.cust_id, b.priceDefault
            FROM 
                {$this->table_name} a,
                del_cust b
            WHERE 
                a.customerCode = b.customerCode 
            AND 
                a.invoice_id = ?";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->invoice_id);

            $stmt->execute();

            return $stmt;
        }

        function getInvInfoByWF($workflow_id) {
            $query = "SELECT a.invoice_id, a.invRef, a.InvStatus FROM {$this->table_name} a WHERE a.workflow_id = '{$workflow_id}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row;
            } else {
                return null;
            }
        }

        function updateInvoice() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        `company_name` = :company_name,
                        `Contact_Person` = :Contact_Person,
                        `Telephone` = :Telephone,
                        `Physical1` = :Physical1,
                        `Physical2` = :Physical2,
                        `Physical3` = :Physical3,
                        `Physical4` = :Physical4,
                        `Registration` = :Registration,
                        `Tax_Number` = :Tax_Number,
                        `customerCode` = :customerCode,
                        `TotalExcl` = :TotalExcl,
                        `TotalTax` = :TotalTax,
                        `TotalIncl` = :TotalIncl,
                        `DCLink` = :DCLink,
                        `user` = :userId,
                        `InvStatus` = :InvStatus,
                        `workflow_id` = :workflow_id,
                        `invNumber` = :invNumber,
                        `poNumber` = :poNumber,
                        `notes` = :notes
                    WHERE
                        (`invoice_id` = :invoice_id);";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':company_name', $this->company_name, PDO::PARAM_STR);
            $stmt->bindParam(':Contact_Person', $this->Contact_Person, PDO::PARAM_STR);
            $stmt->bindParam(':Telephone', $this->Telephone, PDO::PARAM_STR);
            $stmt->bindParam(':Physical1', $this->Physical1, PDO::PARAM_STR);
            $stmt->bindParam(':Physical2', $this->Physical2, PDO::PARAM_STR);
            $stmt->bindParam(':Physical3', $this->Physical3, PDO::PARAM_STR);
            $stmt->bindParam(':Physical4', $this->Physical4, PDO::PARAM_STR);
            $stmt->bindParam(':Registration', $this->Registration, PDO::PARAM_STR);
            $stmt->bindParam(':Tax_Number', $this->Tax_Number, PDO::PARAM_STR);
            $stmt->bindParam(':customerCode', $this->customerCode, PDO::PARAM_STR);
            $stmt->bindParam(':TotalExcl', $this->TotalExcl, PDO::PARAM_STR);
            $stmt->bindParam(':TotalTax', $this->TotalTax, PDO::PARAM_STR);
            $stmt->bindParam(':TotalIncl', $this->TotalIncl, PDO::PARAM_STR);
            $stmt->bindParam(':DCLink', $this->DCLink, PDO::PARAM_INT);
            $stmt->bindParam(':userId', $this->user, PDO::PARAM_INT);
            $stmt->bindParam(':InvStatus', $this->InvStatus, PDO::PARAM_INT);
            $stmt->bindParam(':workflow_id', $this->workflow_id, PDO::PARAM_INT);
            $stmt->bindParam(':invNumber', $this->invNumber, PDO::PARAM_INT);
            $stmt->bindParam(':poNumber', $this->poNumber, PDO::PARAM_INT);
            $stmt->bindParam(':notes', $this->notes, PDO::PARAM_STR);
            $stmt->bindParam(':invoice_id', $this->invoice_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
                
            return false;
            
        }

        function updateInvRef() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        invRef = ?
                    WHERE
                        invoice_id = ?;";

            $stmt = $this->conn->prepare($query);

            $invRef = $this->invoiceGen($this->invoice_id, 6, "PRO");

            $stmt->bindParam(1, $invRef);
            $stmt->bindParam(2, $this->invoice_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function updateInvStatus() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        InvStatus = ?
                    WHERE
                        invoice_id = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->InvStatus);
            $stmt->bindParam(2, $this->invoice_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    class InvoiceLines {
        private $conn;
        private $table_name = 'invoice_lines';

        public $invlineid;
        public $invoice_id;
        public $line_id;
        public $AveUCst;
        public $Description_1;
        public $Description_2;
        public $Description_3;
        public $Qty_On_Hand;
        public $StockLink;
        public $TaxRate;
        public $idTaxRate;
        public $fExclPrice;
        public $fExclPrice2;
        public $p_id;
        public $qty;
        public $pricecat;
        public $checked;
        public $verified;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getCat($row) {
            $format = "";
            $product = array();
            if ($row['pf_cat_id'] == '995' && $row['category2'] == '1003') {
                if (!strpos($row['Description_2'], 'A4')) {
                    $format = "A4";
                } else if (!strpos($row['Description_2'], 'A3')) {
                    $format = "A3";
                } else if (!strpos($row['Description_2'], 'A5')) {
                    $format = "A5";
                }
                $product['prodtype'] = "paper";
                $product['format'] = $format;

            } else if ($row['pf_cat_id'] == '3') {
                if ($row['category2'] == '21') {
                    $product['prodtype'] = "envelopes";
                } else if ($row['category2'] == '23') {
                    $product['prodtype'] = "files";
                } else if ($row['category2'] == '28') {
                    $product['prodtype'] = "pens";
                } else {
                    $product['prodtype'] = "stationery";
                }
                
            } else if ($row['pf_cat_id'] == '996') {
                $product['prodtype'] = "printing";
            } else if ($row['pf_cat_id'] == '2') {
                $product['prodtype'] = "cleaning";
            } else if ($row['pf_cat_id'] == '414') {
                $product['prodtype'] = "ink & toners";
            } else if ($row['pf_cat_id'] == '1') {
                $product['prodtype'] = "messroom";
            } else {
                $product['prodtype'] = "others";
            }

            return $product;
        }

        function createInvLine() {
            $query = "INSERT INTO
                            {$this->table_name}
                        SET
                            invoice_id = :invoice_id,
                            line_id = :line_id,
                            AveUCst = :AveUCst,
                            Description_1 = :Description_1,
                            Description_2 = :Description_2,
                            Description_3 = :Description_3,
                            Qty_On_Hand = :Qty_On_Hand,
                            StockLink = :StockLink,
                            TaxRate = :TaxRate,
                            idTaxRate = :idTaxRate,
                            fExclPrice = :fExclPrice,
                            p_id = :p_id,
                            qty = :qty,
                            pricecat = :pricecat,
                            fExclPrice2 = :fExclPrice2;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':invoice_id', $this->invoice_id);
            $stmt->bindParam(':line_id', $this->line_id);
            $stmt->bindParam(':AveUCst', $this->AveUCst);
            $stmt->bindParam(':Description_1', $this->Description_1);
            $stmt->bindParam(':Description_2', $this->Description_2);
            $stmt->bindParam(':Description_3', $this->Description_3);
            $stmt->bindParam(':Qty_On_Hand', $this->Qty_On_Hand);
            $stmt->bindParam(':StockLink', $this->StockLink);
            $stmt->bindParam(':TaxRate', $this->TaxRate);
            $stmt->bindParam(':idTaxRate', $this->idTaxRate);
            $stmt->bindParam(':fExclPrice', $this->fExclPrice);
            $stmt->bindParam(':p_id', $this->p_id);
            $stmt->bindParam(':qty', $this->qty);
            $stmt->bindParam(':pricecat', $this->pricecat);
            $stmt->bindParam(':fExclPrice2', $this->fExclPrice2);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function updateInvLine() {
            $query = "UPDATE 
                        {$this->table_name}
                    SET 
                        qty = :qty,
                        fExclPrice = :fExclPrice,
                        fExclPrice2 = :fExclPrice2
                    WHERE 
                        invlineid = :invlineid;";

            $stmt = $this->conn->prepare($query);  
            
            $stmt->bindParam(':qty', $this->qty);
            $stmt->bindParam(':fExclPrice', $this->fExclPrice);
            $stmt->bindParam(':fExclPrice2', $this->fExclPrice2);
            $stmt->bindParam(':invlineid', $this->invlineid);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function deleteInvLine() {
            $query = "DELETE FROM {$this->table_name}
                        WHERE invlineid = :invlineid;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':invlineid', $this->invlineid);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function getInvLines($id) {
            $query = "SELECT * FROM {$this->table_name} WHERE invoice_id = '{$id}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt->rowCount();
        }

        function getInvItems($id) {
            $query = "SELECT * FROM {$this->table_name} WHERE invoice_id = '{$id}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getPaperRangeRecs($range, $brand, $vehicle) {
            $array = explode(' ', $brand);
            $brandsearch = $array[0] . ' ';
            if (isset($array[1])) {
                $brandsearch = $brandsearch . $array[1];
            }

            $brandsearch = "%" . $brandsearch . "%";

            $query = "SELECT
                        a.workflow_id, a.status, b.cust_id, a.data, b.invoice_no,
                        b.comments, b.delivery_status, b.region, b.vehicle, b.urgent, b.deliveryDate, c.qty,
                        f.des2, f.des3
                    FROM
                        workflow a, workflow_delivery b, {$this->table_name} c, invoice e, products f
                    WHERE
                        a.workflow_id = b.workflow_id
                    AND
                        c.invoice_id = e.invoice_id
                    AND
                        a.workflow_id = e.workflow_id
                    AND
                        f.p_id = c.p_id
                    AND
                        e.workflow_id IN $range
                    AND
                        f.des3 LIKE '$brandsearch'
                    AND
                        b.vehicle = '$vehicle';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $arr = array();
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
    
                    if (strpos($des2, 'A4') !== false) {
                        $format = 'A4';
                    } else if (strpos($des2, 'A3') !== false) {
                        $format = 'A3';
                    } else if (strpos($des2, 'A5') !== false) {
                        $format = 'A5';
                    }

                    $arr_item = array(
                        'workflow_id' => $workflow_id,
                        'status' => $status,
                        'cust_id' => $cust_id,
                        // 'company_name' => $company_name,
                        'invoice_no' => $invoice_no,
                        'comments' => $comments,
                        'delivery_status' => $delivery_status,
                        'region' => $region,
                        'format' => $format,
                        'vehicle' => $vehicle,
                        'urgent' => $urgent,
                        'deliveryDate' => $deliveryDate,
                        'qty' => $qty,
                        'des2' => $des2,
                        'des3' => $des3,
                        'data' => $data
                    );

                    array_push($arr, $arr_item);
                }
            }

            return $arr;
        }

        function getPaperRange($range, $brand, $vehicle, $data) {
            $table = "";
            $condition = "";
            switch ($data) {
                case 1:
                    $table = 'del_cust';
                    $condition = " AND a.data = 1 ";
                    break;
                case 2:
                    $table = 'rns_cust';
                    $condition = " AND a.data = 2 ";
                    break;
                case 3:
                    $table = 'pnp_cust';
                    $condition = " AND a.data = 3 ";
                    break;
            }
            $array = explode(' ', $brand);
            $brandsearch = $array[0] . ' ';
            if (isset($array[1])) {
                $brandsearch = $brandsearch . $array[1];
            }

            $brandsearch = "%" . $brandsearch . "%";

            $total = 0;

            $query = "SELECT
                        a.workflow_id, a.status, b.cust_id, d.company_name, b.invoice_no,
                        b.comments, b.delivery_status, b.region, b.vehicle, b.urgent, b.deliveryDate, c.qty,
                        f.des2, f.des3
                    FROM
                        workflow a, workflow_delivery b, {$this->table_name} c, {$table} d, invoice e, products f
                    WHERE
                        a.workflow_id = b.workflow_id
                    AND
                        c.invoice_id = e.invoice_id
                    AND
                        a.workflow_id = e.workflow_id
                    AND
                        b.cust_id = d.cust_id
                    AND
                        f.p_id = c.p_id
                    AND
                        e.workflow_id IN $range
                    AND
                        f.des3 LIKE '$brandsearch'{$condition}
                    AND
                        b.vehicle = '$vehicle';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
    
                    $total = +$qty;
                }
            }

            return $total;
        }

        function getParsedProducts($id) {
            $query = "SELECT
                        a.qty, a.Description_1, a.Description_2, a.Description_3, b.pf_cat_id, b.category2
                    FROM 
                        {$this->table_name} a, products b, invoice c 
                    WHERE
                        a.p_id = b.p_id
                    AND 
                        a.invoice_id = c.invoice_id
                    AND 
                        c.workflow_id = '{$id}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $num = $stmt->rowCount();

            $products = array();

            $paper = 0;
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $products['workflow_id'] = $id;

                    $prodtype = $this->getCat($row);

                    $type = $prodtype['prodtype'];

                    if ($type === 'paper') {
                        $paper++;
                        $format = $prodtype['format'];

                        $products[$type . $paper] = array(
                            true,
                            $row['qty'],
                            $format,
                            $row['Description_3']
                        );
                    } else {
                        $products[$type] = array(
                            true
                        );
                    }
                }

                return $products;
            }
        }

        function getProducts($id) {
            $query = "SELECT
                        a.qty, a.Description_1, a.Description_2, a.Description_3, b.pf_cat_id, b.category2
                    FROM 
                        {$this->table_name} a, products b, invoice c 
                    WHERE
                        a.p_id = b.p_id
                    AND 
                        a.invoice_id = c.invoice_id
                    AND 
                        c.workflow_id = '{$id}';";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $num = $stmt->rowCount();
            $i = 1;
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $productlist = "";
                    $prodtype = $this->getCat($row);
                    
                    if ($prodtype['prodtype'] === 'paper') {
                        $format = $prodtype['format'];
    
                        $productlist .= $row['qty']." ".$format." ".$row['Description_3'];
                        if ($i < $num) {
                            $productlist .= ", ";
                        }
                    } else {
                        if (!strpos($productlist, ucfirst($prodtype['prodtype']))) {
                            $productlist .= ucfirst($prodtype['prodtype']);
                            if ($i < $num) {
                                $productlist .= ", ";
                            }
                        }
                    }

                    

                    $i++;
                }

                return $productlist;
            }
        }

        function markChecked() {
            $query = "UPDATE {$this->table_name} SET checked = 1 WHERE invlineid = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->invlineid);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function markVerified() {
            $query = "UPDATE {$this->table_name} SET verified = 1 WHERE invlineid = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->invlineid);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
?>