<?php
    class Client {
        private $conn;
        private $table_name = 'Client';

        public $DCLink;
        public $Account;
        public $Name;
        public $Title;
        public $Init;
        public $Contact_Person;
        public $Physical1;
        public $Physical2;
        public $Physical3;
        public $Physical4;
        public $Physical5;
        public $PhysicalPC;
        public $Addressee;
        public $Post1;
        public $Post2;
        public $Post3;
        public $Post4;
        public $Post5;
        public $AccountTerms;
        public $Tax_Number;
        public $Registration;
        public $Credit_Limit;
        public $EMail;

        public function __construct($db) {
            $this->conn = $db;
        }

        function getInfo($acc) {
            $query = "SELECT * FROM {$this->table_name} WHERE Account = '{$acc}'";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        }

        function getClients() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            return $stmt;
        }

        function getEmail($acc) {
            $query = "SELECT * FROM {$this->table_name} WHERE Account = '{$acc}'";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $email = str_replace(' ', '', $row['EMail']);
            $email = str_replace('/', ';', $email);

            return $email;
        }

        function getTerms($acc) {
            /** 
             * 0 is Current
             * 1 is 30 Days
             * 2 is 60 Days
             * 3 is 90 Days
             * 4 is 120 Days
             * 5 is 150 Days
             * 6 is 180 Days
            */

            $query = "SELECT AccountTerms FROM {$this->table_name} WHERE Account = '{$acc}'";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['AccountTerms'];
        }

        function getDCLink($acc) {
            $query = "SELECT TOP 1 DCLink FROM {$this->table_name} WHERE Account = '{$acc}'";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['DCLink'];
        }

        function updateTerm() {
            $query = "UPDATE
                        {$this->table_name}
                    SET
                        AccountTerms = :AccountTerms
                    WHERE
                        DCLink = :DCLink;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':AccountTerms', $this->AccountTerms, PDO::PARAM_INT);
            $stmt->bindParam(':DCLink', $this->DCLink, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class InvNum {
        private $conn;
        private $table_name = 'InvNum';

        public $AutoIndex;
        public $DocType;
        public $DocVersion;
        public $DocState;
        public $DocFlag;
        public $OrigDocID;
        public $InvNumber;
        public $GrvNumber;
        public $GrvID;
        public $AccountID;
        public $Description;
        public $InvDate;
        public $OrderDate;
        public $DueDate;
        public $DeliveryDate;
        public $TaxInclusive;
        public $Email_Sent;
        public $Address1;
        public $Address2;
        public $Address3;
        public $Address4;
        public $Address5;
        public $Address6;
        public $PAddress1;
        public $PAddress2;
        public $PAddress3;
        public $PAddress4;
        public $PAddress5;
        public $PAddress6;
        public $DelMethodID;
        public $DocRepID;
        public $OrderNum;
        public $DeliveryNote;
        public $InvDisc;
        public $Message1;
        public $Message2;
        public $Message3;
        public $ProjectID;
        public $TillID;
        public $POSAmntTendered;
        public $POSChange;
        public $GrvSplitFixedCost;
        public $GrvSplitFixedAmnt;
        public $OrderStatusID;
        public $OrderPriorityID;
        public $ExtOrderNum;
        public $ForeignCurrencyID;
        public $InvDiscAmnt;
        public $InvDiscAmntEx;
        public $InvTotExclDEx;
        public $InvTotTaxDEx;
        public $InvTotInclDEx;
        public $InvTotExcl;
        public $InvTotTax;
        public $InvTotIncl;
        public $OrdDiscAmnt;
        public $OrdDiscAmntEx;
        public $OrdTotExclDEx;
        public $OrdTotTaxDEx;
        public $OrdTotInclDEx;
        public $OrdTotExcl;
        public $OrdTotTax;
        public $OrdTotIncl;
        public $bUseFixedPrices;
        public $iINVNUMAgentID;
        public $cTaxNumber;
        public $cAccountName;
        public $bInvRounding;
        public $InvTotInclExRounding;
        public $OrdTotInclExRounding;
        public $fInvTotInclForeignExRounding;
        public $fOrdTotInclForeignExRounding;
        public $iEUNoTCID;
        public $iPOAuthStatus;
        public $iPOIncidentID;
        public $iSupervisorID;
        public $iMergedDocID;

        public function __construct($db) {
            $this->conn = $db;
        }

        function getLastId() {
            $query = "SELECT MAX(AutoIndex) as LastId FROM {$this->table_name}";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['LastId'];
        }

        function saveToPastel() {
            // $query = "INSERT INTO
            //             {$this->table_name}
            //         SET
            //             DocType = 4,
            //             DocVersion = 1,
            //             DocState = 1,
            //             DocFlag = 0,
            //             OrigDocID = 0,
            //             GrvID = 0,
            //             AccountID = :AccountID,
            //             Description = '',
            //             InvDate = :InvDate,
            //             OrderDate = :OrderDate,
            //             DueDate = :DueDate,
            //             DeliveryDate = :DeliveryDate,
            //             TaxInclusive = 0,
            //             Email_Sent = 1,
            //             DelMethodID = 0,
            //             DocRepID = 0,
            //             OrderNum = :OrderNum,
            //             InvDisc = 0,
            //             ProjectID = 0,
            //             TillID = 0,
            //             GrvSplitFixedCost = 0,
            //             GrvSplitFixedAmnt = 0,
            //             OrderStatusID = 0,
            //             OrderPriorityID = 0,
            //             ExtOrderNum = 'BY MAIL',
            //             ForeignCurrencyID = 0,
            //             InvDiscAmnt = 0,
            //             InvDiscAmntEx = 0,
            //             InvTotExclDEx = :InvTotExclDEx,
            //             InvTotTaxDEx = :InvTotTaxDEx,
            //             InvTotInclDEx = :InvTotInclDEx,
            //             InvTotExcl = :InvTotExcl,
            //             InvTotTax = :InvTotTax,
            //             InvTotIncl = :InvTotIncl,
            //             OrdDiscAmnt = 0,
            //             OrdDiscAmntEx = 0,
            //             OrdTotExclDEx = :OrdTotExclDEx,
            //             OrdTotTaxDEx = :OrdTotTaxDEx,
            //             OrdTotInclDEx = :OrdTotInclDEx,
            //             OrdTotExcl = :OrdTotExcl,
            //             OrdTotTax = :OrdTotTax,
            //             OrdTotIncl = :OrdTotIncl,
            //             bUseFixedPrices = 0,
            //             iINVNUMAgentID = 10,
            //             cTaxNumber = :cTaxNumber,
            //             cAccountName = :cAccountName,
            //             bInvRounding = 1,
            //             InvTotInclExRounding = :InvTotInclExRounding,
            //             OrdTotInclExRounding = :OrdTotInclExRounding,
            //             fInvTotInclForeignExRounding = 0,
            //             fOrdTotInclForeignExRounding = 0,
            //             iEUNoTCID = 0,
            //             iPOAuthStatus = 0,
            //             iPOIncidentID = 0,
            //             iSupervisorID = 0,
            //             iMergedDocID = 0;";
            $query = "INSERT INTO
                        {$this->table_name} (
                        DocType,
                        DocVersion,
                        DocState,
                        DocFlag,
                        OrigDocID,
                        GrvID,
                        AccountID,
                        Description,
                        InvDate,
                        OrderDate,
                        DueDate,
                        DeliveryDate,
                        TaxInclusive,
                        Email_Sent,
                        DelMethodID,
                        DocRepID,
                        OrderNum,
                        InvDisc,
                        ProjectID,
                        TillID,
                        GrvSplitFixedCost,
                        GrvSplitFixedAmnt,
                        OrderStatusID,
                        OrderPriorityID,
                        ExtOrderNum,
                        ForeignCurrencyID,
                        InvDiscAmnt,
                        InvDiscAmntEx,
                        InvTotExclDEx,
                        InvTotTaxDEx,
                        InvTotInclDEx,
                        InvTotExcl,
                        InvTotTax,
                        InvTotIncl,
                        OrdDiscAmnt,
                        OrdDiscAmntEx,
                        OrdTotExclDEx,
                        OrdTotTaxDEx,
                        OrdTotInclDEx,
                        OrdTotExcl,
                        OrdTotTax,
                        OrdTotIncl,
                        bUseFixedPrices,
                        iINVNUMAgentID,
                        cTaxNumber,
                        cAccountName,
                        bInvRounding,
                        InvTotInclExRounding,
                        OrdTotInclExRounding,
                        fInvTotInclForeignExRounding,
                        fOrdTotInclForeignExRounding,
                        iEUNoTCID,
                        iPOAuthStatus,
                        iPOIncidentID,
                        iSupervisorID,
                        iMergedDocID
                    ) VALUES (
                        4,
                        1,
                        1,
                        0,
                        0,
                        0,
                        :AccountID,
                        :Descr,
                        :InvDate,
                        :OrderDate,
                        :DueDate,
                        :DeliveryDate,
                        0,
                        1,
                        0,
                        0,
                        :OrderNum,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        'BY MAIL',
                        0,
                        0,
                        0,
                        :InvTotExclDEx,
                        :InvTotTaxDEx,
                        :InvTotInclDEx,
                        :InvTotExcl,
                        :InvTotTax,
                        :InvTotIncl,
                        0,
                        0,
                        :OrdTotExclDEx,
                        :OrdTotTaxDEx,
                        :OrdTotInclDEx,
                        :OrdTotExcl,
                        :OrdTotTax,
                        :OrdTotIncl,
                        0,
                        10,
                        :cTaxNumber,
                        :cAccountName,
                        1,
                        :InvTotInclExRounding,
                        :OrdTotInclExRounding,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    );";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(':AccountID', $this->AccountID, PDO::PARAM_INT);

            $stmt->bindParam(':Descr', $this->Description);
            $stmt->bindParam(':InvDate', $this->InvDate);
            $stmt->bindParam(':OrderDate', $this->OrderDate);
            $stmt->bindParam(':DueDate', $this->DueDate);
            $stmt->bindParam(':DeliveryDate', $this->DeliveryDate);

            $stmt->bindParam(':OrderNum', $this->OrderNum, PDO::PARAM_STR);

            $stmt->bindParam(':InvTotExclDEx', $this->InvTotExclDEx);
            $stmt->bindParam(':InvTotTaxDEx', $this->InvTotTaxDEx);
            $stmt->bindParam(':InvTotInclDEx', $this->InvTotInclDEx);
            $stmt->bindParam(':InvTotExcl', $this->InvTotExcl);
            $stmt->bindParam(':InvTotTax', $this->InvTotTax);
            $stmt->bindParam(':InvTotIncl', $this->InvTotIncl);

            $stmt->bindParam(':OrdTotExclDEx', $this->OrdTotExclDEx);
            $stmt->bindParam(':OrdTotTaxDEx', $this->OrdTotTaxDEx);
            $stmt->bindParam(':OrdTotInclDEx', $this->OrdTotInclDEx);
            $stmt->bindParam(':OrdTotExcl', $this->OrdTotExcl);
            $stmt->bindParam(':OrdTotTax', $this->OrdTotTax);
            $stmt->bindParam(':OrdTotIncl', $this->OrdTotIncl);

            $stmt->bindParam(':cTaxNumber', $this->cTaxNumber, PDO::PARAM_STR);
            $stmt->bindParam(':cAccountName', $this->cAccountName, PDO::PARAM_STR);

            $stmt->bindParam(':InvTotInclExRounding', $this->InvTotInclExRounding);
            $stmt->bindParam(':OrdTotInclExRounding', $this->OrdTotInclExRounding);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        function fetchInvoiceByRef() {
            $query = "SELECT
                        TOP 1 *
                    FROM
                        {$this->table_name}
                    WHERE
                        Description = ?
                    ORDER BY
                        InvDate DESC;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->Description);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                return $row['InvNumber'];
            } else {
                return null;
            }
        }

        function fetchMktReport($range) {
            $condition = "";

            if (isset($range) && strlen($range) > 0) {
                $condition = " AND c.cSimpleCode IN $range ";
            }

            $query = "SELECT
                        *
                    FROM
                        (SELECT SUM(b.fQtyProcessedLineTotExcl) as 'thirty' FROM InvNum a, _btblInvoiceLines b, StkItem c WHERE a.AccountID = ? AND a.AutoIndex = b.iInvoiceID AND b.iStockCodeID = c.StockLink AND a.InvNumber LIKE 'INV%' {$condition} AND DATEDIFF(dayofyear, Cast(a.InvDate as datetime), GETDATE()) BETWEEN 31 AND 60) a,
                        (SELECT SUM(b.fQtyProcessedLineTotExcl) as 'sixty' FROM InvNum a, _btblInvoiceLines b, StkItem c WHERE a.AccountID = ? AND a.AutoIndex = b.iInvoiceID AND b.iStockCodeID = c.StockLink AND a.InvNumber LIKE 'INV%' {$condition} AND DATEDIFF(dayofyear, Cast(a.InvDate as datetime), GETDATE()) BETWEEN 61 AND 90) b,
                        (SELECT SUM(b.fQtyProcessedLineTotExcl) as 'current' FROM InvNum a, _btblInvoiceLines b, StkItem c WHERE a.AccountID = ? AND a.AutoIndex = b.iInvoiceID AND b.iStockCodeID = c.StockLink AND a.InvNumber LIKE 'INV%' {$condition} AND DATEDIFF(dayofyear, Cast(a.InvDate as datetime), GETDATE()) BETWEEN 0 AND 30) c, 
                        (SELECT TOP 1 InvDate as lastorderdate FROM InvNum WHERE AccountID = ? ORDER BY InvDate DESC) d, 
                        (SELECT TOP 1 DCLink, Account, Name FROM Client WHERE DCLink = ?) e;";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->AccountID);
            $stmt->bindParam(2, $this->AccountID);
            $stmt->bindParam(3, $this->AccountID);
            $stmt->bindParam(4, $this->AccountID);
            $stmt->bindParam(5, $this->AccountID);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        }

        function fetchNextOrder() {
            $query = "SELECT
                        TOP 1 OrderNum
                    FROM
                        {$this->table_name}
                    WHERE
                        OrderNum
                    LIKE
                        'SO%_%_%_%_%_'
                    ORDER BY OrderNum DESC;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $add = substr($row['OrderNum'], 2) + 1;
            $newval = 'SO' . '' . $add;

            return $newval;
        }

        function checkInvoice() {
            $query = "SELECT
                        a.*, b.Name, b.Account
                    FROM
                        {$this->table_name} a, Client b
                    WHERE
                        a.AccountID = b.DCLink
                    AND
                        a.InvNumber = ? OR a.OrderNum = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->InvNumber);
            $stmt->bindParam(2, $this->InvNumber);

            $stmt->execute();

            return $stmt;
        }

        function findCompletion() {
            $query = "SELECT
                        a.*, b.Name, b.Account
                    FROM
                        {$this->table_name} a, Client b
                    WHERE
                        a.AccountID = b.DCLink
                    AND
                        a.InvNumber != ''
                    AND
                        a.InvNumber LIKE 'INV%'
                    AND
                        a.OrderDate BETWEEN DATEADD(day, -7, GETDATE()) AND GETDATE()
                    ORDER BY
                        a.OrderDate DESC, a.InvNumber DESC";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            return $stmt;
        }

    }

    class PostAR {
        private $conn;
        private $table_name = 'PostAR';

        public $AutoIdx;
        public $TxDate;
        public $id;
        public $AccountLink;
        public $TrCodeID;
        public $Debit;
        public $Credit;
        public $iCurrencyID;
        public $fExchangeRate;
        public $fForeignDebit;
        public $fForeignCredit;
        public $Description;
        public $TaxTypeID;
        public $Reference;
        public $Order_No;
        public $ExtOrderNum;
        public $Audit_No;
        public $Tax_Amount;
        public $fForeignTax;
        public $Project;
        public $Outstanding;
        public $fForeignOutstanding;
        public $cAllocs;
        public $InvNumKey;
        public $RepID;
        public $LinkAccCode;
        public $TillID;
        public $CRCCheck;
        public $DTStamp;
        public $UserName;
        public $iTaxPeriodID;
        public $cReference2;
        public $fJCRepCost;
        public $iAge;
        public $dDateAged;
        public $iPostSettlementTermsID;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function allocBD($alloc) {
            $alloc_arr = array();

            if (stripos($alloc, ';') !== false) {
                $first_split = explode('|', $alloc);
    
                foreach($first_split as $idx => $value) {
                    $split = explode(';', $value);
    
                    if ($split[0]) {
                        $pattern = '/I=/';
                        $outcome = preg_replace($pattern, '', $split[0]);
                    }
    
                    if ($split[1]) {
                        $pattern2 = '/A=/';
                        $outcome2 = preg_replace($pattern2, '', $split[1]);
                    }
    
                    if (isset($outcome) && isset($outcome2)) {
                        $arr = array(
                            'idx' => $outcome,
                            'amt' => $outcome2
                        );
        
                        array_push($alloc_arr, $arr);
                    }
                }
    
                return $alloc_arr;
            } else {
                return '';
            }

        }

        function findCRN($OrderNo) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        Order_No = '{$OrderNo}'
                    AND
                        Id = 'Crn'
                    ORDER BY
                        TxDate DESC;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function getOutstanding($period) {
            $condition = "";

            switch (+$period) {
                case 0:
                    $condition = ">= 30 ";
                    break;
                case 1:
                    $condition = ">= 61 ";
                    break;
                case 2:
                    $condition = ">= 91 ";
                    break;
                case 3:
                    $condition = ">= 121 ";
                    break;
                case 4:
                    $condition = ">= 151 ";
                    break;
                case 5:
                    $condition = ">= 180 ";
                    break;
                case 6:
                    $condition = "> 180 ";
                    break;
            }

            $query = "SELECT
                        SUM(Outstanding) as Outstanding
                    FROM
                        {$this->table_name}
                    WHERE
                        DATEDIFF(dayofyear, Cast(TxDate as datetime), GETDATE()) {$condition}
                    AND
                        AccountLink = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->AccountLink);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['Outstanding'];
        }

        function getAgeAnalysis($period) {
            $condition = "";
            
            switch ($period) {
                case 0:
                    $condition = "<= 30 ";
                    break;
                case 1:
                    $condition = "BETWEEN 31 AND 60 ";
                    break;
                case 2:
                    $condition = "BETWEEN 61 AND 90 ";
                    break;
                case 3:
                    $condition = "BETWEEN 91 AND 120 ";
                    break;
                case 4:
                    $condition = "BETWEEN 121 AND 150 ";
                    break;
                case 5:
                    $condition = "BETWEEN 151 AND 180 ";
                    break;
                case 6:
                    $condition = "> 180 ";
                    break;
            }

            $query = "SELECT
                        SUM(Outstanding) as Outstanding
                    FROM
                        {$this->table_name}
                    WHERE
                        DATEDIFF(dayofyear, Cast(TxDate as datetime), GETDATE()) {$condition}
                    AND
                        AccountLink = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->AccountLink);

            $stmt->execute();

            return $stmt;
        }

        function ifAlloc($inv) {
            $query = "SELECT
                        TOP 1
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        Reference = '{$inv}'
                    ORDER BY
                        TxDate DESC;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['cAllocs'];
            } else {
                return null;
            }

        }

        function getAllocs($orientation, $date) {
            $condition = "";
            if ($orientation === 1) {
                $condition = " AND a.TrCodeID = 35) ";
            } else if ($orientation === 2) {
                $condition = " AND a.TrCodeID != 35) ";
            } else {
                $condition = " AND a.cAllocs IS NULL) OR (a.TrCodeID = b.idTrCodes AND a.AccountLink = ? AND a.TrCodeID = '35' AND a.Outstanding != '0' AND a.Debit != a.Outstanding) OR (a.TrCodeID = b.idTrCodes AND a.AccountLink = ? AND a.Outstanding != '0' AND a.Credit != a.Outstanding) ";
            }

            if ($date['start']) {

            }

            $query = "SELECT
                        a.AutoIdx, b.Code, a.TxDate, a.Reference, a.cReference2, a.Description, a.Debit, a.Credit, a.Outstanding, a.cAllocs
                    FROM
                        {$this->table_name} a, TrCodes b
                    WHERE
                        (a.TrCodeID = b.idTrCodes
                    AND
                        a.AccountLink = ?
                        {$condition}
                    ORDER BY
                        a.TxDate ASC;";
            
            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->AccountLink);
            
            if ($orientation === 3) {
                $stmt->bindParam(2, $this->AccountLink);
                $stmt->bindParam(3, $this->AccountLink);
            }

            $stmt->execute();

            return $stmt;
        }

        function getPairedAllocs($idx) {
            $query = "SELECT
                        a.AutoIdx, b.Code, a.TxDate, a.Reference, a.cReference2, a.Description, a.Debit, a.Credit, a.Outstanding, a.cAllocs
                    FROM
                        {$this->table_name} a, TrCodes b
                    WHERE
                        a.AutoIdx = '{$idx}' 
                    AND
                        a.TrCodeID = b.idTrCodes
                    ORDER BY
                        a.TxDate ASC;";
            
            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            return $stmt;
        }
    }

    class PostST {
        private $conn;
        private $table_name = 'PostST';

        public $Reference;
        public $cReference2;

        public function __construct($db) {
            $this->conn = $db;
        }

        function getProducts() {
            $query = "SELECT
                        a.*, b.Description_1, b.Description_2, b.Description_3, a.Quantity
                    FROM
                        {$this->table_name} a, StkItem b
                    WHERE
                        a.AccountLink = b.StockLink
                    AND
                        a.Reference = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->Reference);

            $stmt->execute();

            return $stmt;
        }

        function getProducts2() {
            $query = "SELECT
                        a.*, b.Description_1, b.Description_2, b.Description_3, a.Quantity, b.cSimpleCode
                    FROM
                        {$this->table_name} a, StkItem b, _etblStockDetails c
                    WHERE
                        a.AccountLink = b.StockLink
                    AND
                        c.StockID = b.StockLink
                    AND
                        c.GroupID = 23
                    AND
                        a.Reference = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->Reference);

            $stmt->execute();

            return $stmt;
        }

        function getQtyReport($range, $year, $month) {
            $searchrange = " ";
            $month = +$month;
            $year = +$year;

            for ($i = 1; $i <= 6; $i++) { 
                $searchrange = $searchrange . "(
                    SELECT (x.Qty - y.Qty) as Qty 
                        FROM 
                        (
                            (SELECT SUM(a.Quantity) as Qty FROM PostST a, StkItem b WHERE a.AccountLink = b.StockLink AND MONTH(a.TxDate) = {$month} AND YEAR(a.TxDate) = '{$year}' AND a.TrCodeID = '35' AND b.cSimpleCode IN {$range})) x,
                            (SELECT SUM(a.Quantity) as Qty FROM PostST a, StkItem b WHERE a.AccountLink = b.StockLink AND MONTH(a.TxDate) = {$month} AND YEAR(a.TxDate) = '{$year}' AND a.TrCodeID = '31' AND b.cSimpleCode IN {$range}) y
                        ) table{$i}";

                if ($i !== 6) {
                    $searchrange = $searchrange . ', ';
                }

                $month--;
                
                if ($month === 0) {
                    $month = 12;
                    $year = $year - 1;
                }
            }

            $query = "SELECT
                        table1.Qty as Qty1,
                        table2.Qty as Qty2,
                        table3.Qty as Qty3,
                        table4.Qty as Qty4,
                        table5.Qty as Qty5,
                        table6.Qty as Qty6
                    FROM 
                        {$searchrange};";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            return $stmt;
        }

    }

    class StkItem {
        private $conn;
        private $table_name = 'StkItem';

        public $StockLink;
        public $Code;
        public $Description_1;
        public $Description_2;
        public $Description_3;
        public $ServiceItem;
        public $ItemActive;
        public $WhseItem;
        public $SerialItem;
        public $DuplicateSN;
        public $StrictSN;
        public $BomCode;
        public $SMtrxCol;
        public $PMtrxCol;
        public $cModel;
        public $cRevision;
        public $cComponent;
        public $dDateReleased;
        public $dStkitemTimeStamp;
        public $iInvSegValue1ID;
        public $iInvSegValue2ID;
        public $iInvSegValue3ID;
        public $iInvSegValue4ID;
        public $iInvSegValue5ID;
        public $iInvSegValue6ID;
        public $iInvSegValue7ID;
        public $cExtDescription;
        public $cSimpleCode;
        public $bCommissionItem;
        public $bLotItem;
        public $iLotStatus;
        public $bLotMustExpire;
        public $iItemCostingMethod;
        public $iEUCommodityID;
        public $iEUSupplementaryUnitID;
        public $fNetMass;
        public $iUOMStockingUnitID;
        public $iUOMDefPurchaseUnitID;
        public $iUOMDefSellUnitID;
        public $ucllDesc1;
        public $ucllDesc2;

        public $Qty_On_Hand;
        public $AveUCst; 

        public function __construct($db) {
            $this->conn = $db;
        }

        function search($keywords) {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        cSimpleCode = ?
                    OR
                        Description_1 = ?
                    OR
                        Description_2 = ?
                    OR
                        Description_3 = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $keywords = htmlspecialchars(strip_tags($keywords));
            $keywords = "%{$keywords}%";

            $stmt->bindParam(1, $keywords);
            $stmt->bindParam(2, $keywords);
            $stmt->bindParam(3, $keywords);
            $stmt->bindParam(4, $keywords);

            $stmt->execute();

            return $stmt;
        }

        function getProductInfo() {
            $query = "SELECT
                        TOP 1
                        b.QtyOnHand as Qty_On_Hand, c.AverageCost as AveUCst, a.*
                    FROM
                        {$this->table_name} a, _etblStockQtys b, _etblStockCosts c
                    WHERE
                        a.StockLink = b.StockID
                    AND
                        a.StockLink = c.StockID
                    AND
                        a.cSimpleCode = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->cSimpleCode, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getPrice() {
            $query = "SELECT
                        TOP 1
                        b.QtyOnHand as Qty_On_Hand, c.AverageCost as AveUCst, a.StockLink, a.Description_1, a.Description_2, a.Description_3
                    FROM
                        {$this->table_name} a, _etblStockQtys b, _etblStockCosts c
                    WHERE
                        a.StockLink = b.StockID
                    AND
                        a.StockLink = c.StockID
                    AND
                        a.cSimpleCode = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->cSimpleCode, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getStk($id) {
            $query = "SELECT
                        TOP 1
                        b.QtyOnHand as Qty_On_Hand
                    FROM
                        {$this->table_name} a, _etblStockQtys b
                    WHERE
                        a.StockLink = b.StockID
                    AND
                        a.cSimpleCode = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $id, PDO::PARAM_STR);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['Qty_On_Hand'];
        } 
    }

    class TaxRate {
        private $conn;
        private $table_name = 'TaxRate';

        public $idTaxRate;
        public $Code;
        public $Description;
        public $TaxRate;

        public function __construct($db) {
            $this->conn = $db;
        }

        function getTaxRates() {
            $query = "SELECT * FROM {$this->table_name};";

            $stmt = $this->conn->prepare($query);

            $stmt->execute();

            return $stmt;
        }

        function getTaxRate($id) {
            $query = "SELECT * FROM {$this->table_name} WHERE Code = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $id);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row;
        }
    }

    class _tSessions {
        private $conn;
        private $table_name = '_tSessions';

        public $idSessions;
        public $SessionID;
        public $SQLServer;
        public $DatabaseName;
        public $AgentName;
        public $ConnectTime;
        public $RefreshTime;

        public function __construct($db) {
            $this->conn = $db;
        }

        function get() {
            $query = "SELECT
                        *
                    FROM
                        {$this->table_name};";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->execute();

            return $stmt;
        }

        function kill() {
            $query = "DELETE FROM
                        {$this->table_name}
                    WHERE
                        idSessions = ?;";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->idSessions);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        }
    }

    class _btblInvoiceLines {
        private $conn;
        private $table_name = '_btblInvoiceLines';

        public $idInvoiceLines;
        public $iInvoiceID;
        public $iOrigLineID;
        public $iGrvLineID;
        public $cDescription;
        public $iUnitsOfMeasureStockingID;
        public $iUnitsOfMeasureCategoryID;
        public $iUnitsOfMeasureID;
        public $fQuantity;
        public $fQtyChange;
        public $fQtyToProcess;
        public $fQtyLastProcess;
        public $fQtyProcessed;
        public $fQtyReserved;
        public $fQtyReservedChange;
        public $cLineNotes;
        public $fUnitPriceExcl;
        public $fUnitPriceIncl;
        public $fUnitCost;
        public $fLineDiscount;
        public $fTaxRate;
        public $bIsSerialItem;
        public $bIsWhseItem;
        public $fAddCost;
        public $cTradeinItem;
        public $iStockCodeID;
        public $iJobID;
        public $iWarehouseID;
        public $iTaxTypeID;
        public $iPriceListNameID;
        public $fQuantityLineTotIncl;
        public $fQuantityLineTotExcl;
        public $fQuantityLineTotInclNoDisc;
        public $fQuantityLineTotExclNoDisc;
        public $fQuantityLineTaxAmount;
        public $fQuantityLineTaxAmountNoDisc;
        public $fQtyChangeLineTotIncl;
        public $fQtyChangeLineTotExcl;
        public $fQtyChangeLineTotInclNoDisc;
        public $fQtyChangeLineTotExclNoDisc;
        public $fQtyChangeLineTaxAmount;
        public $fQtyChangeLineTaxAmountNoDisc;
        public $fQtyToProcessLineTotIncl;
        public $fQtyToProcessLineTotExcl;
        public $fQtyToProcessLineTotInclNoDisc;
        public $fQtyToProcessLineTotExclNoDisc;
        public $fQtyToProcessLineTaxAmount;
        public $fQtyToProcessLineTaxAmountNoDisc;
        public $fQtyLastProcessLineTotIncl;
        public $fQtyLastProcessLineTotExcl;
        public $fQtyLastProcessLineTotInclNoDisc;
        public $fQtyLastProcessLineTotExclNoDisc;


        public $fQtyLastProcessLineTaxAmount;
        public $fQtyLastProcessLineTaxAmountNoDisc;
        public $fQtyProcessedLineTotIncl;
        public $fQtyProcessedLineTotExcl;
        public $fQtyProcessedLineTotInclNoDisc;
        public $fQtyProcessedLineTotExclNoDisc;

        public $fQtyProcessedLineTaxAmount;
        public $fQtyProcessedLineTaxAmountNoDisc;

        public $fUnitPriceExclForeign;
        public $fUnitPriceInclForeign;
        public $fUnitCostForeign;
        public $fAddCostForeign;
        public $fQuantityLineTotInclForeign;
        public $fQuantityLineTotExclForeign;
        public $fQuantityLineTotInclNoDiscForeign;
        public $fQuantityLineTotExclNoDiscForeign;
        public $fQuantityLineTaxAmountForeign;
        public $fQuantityLineTaxAmountNoDiscForeign;
        public $fQtyChangeLineTotInclForeign;
        public $fQtyChangeLineTotExclForeign;
        public $fQtyChangeLineTotInclNoDiscForeign;
        public $fQtyChangeLineTotExclNoDiscForeign;
        public $fQtyChangeLineTaxAmountForeign;
        public $fQtyChangeLineTaxAmountNoDiscForeign;
        public $fQtyToProcessLineTotInclForeign;
        public $fQtyToProcessLineTotExclForeign;
        public $fQtyToProcessLineTotInclNoDiscForeign;
        public $fQtyToProcessLineTotExclNoDiscForeign;
        public $fQtyToProcessLineTaxAmountForeign;
        public $fQtyToProcessLineTaxAmountNoDiscForeign;
        public $fQtyLastProcessLineTotInclForeign;
        public $fQtyLastProcessLineTotExclForeign;
        public $fQtyLastProcessLineTotInclNoDiscForeign;
        public $fQtyLastProcessLineTotExclNoDiscForeign;
        public $fQtyLastProcessLineTaxAmountForeign;
        public $fQtyLastProcessLineTaxAmountNoDiscForeign;
        public $fQtyProcessedLineTotInclForeign;
        public $fQtyProcessedLineTotExclForeign;
        public $fQtyProcessedLineTotInclNoDiscForeign;
        public $fQtyProcessedLineTotExclNoDiscForeign;
        public $fQtyProcessedLineTaxAmountForeign;
        public $fQtyProcessedLineTaxAmountNoDiscForeign;
        public $iLineRepID;
        public $iLineProjectID;
        public $iLedgerAccountID;
        public $iModule;
        public $bChargeCom;
        public $bIsLotItem;
        // public $iLotID;
        // public $cLotNumber;
        // public $dLotExpiryDate;
        public $iMFPID;
        public $iLineID;
        public $iLinkedLineID;
        public $fQtyLinkedUsed;
        public $ImportDate;
        public $iUnitPriceOverrideReasonID;
        public $iLineDiscountReasonID;
        public $iReturnReasonID;
        public $iLineDocketMode;
        public $_btblInvoiceLines_iBranchID;
        public $_btblInvoiceLines_dCreatedDate;
        public $_btblInvoiceLines_dModifiedDate;
        public $_btblInvoiceLines_iCreatedBranchID;
        public $_btblInvoiceLines_iModifiedBranchID;
        public $_btblInvoiceLines_iCreatedAgentID;
        public $_btblInvoiceLines_iModifiedAgentID;
        public $_btblInvoiceLines_iChangeSetID;
        public $_btblInvoiceLines_CheckSum;
        public $fUnitPriceInclOrig;
        public $fUnitPriceExclOrig;
        public $fUnitPriceInclForeignOrig;
        public $fUnitPriceExclForeignOrig;
        public $iDeliveryMethodID;
        public $fQtyDeliver;
        public $dDeliveryDate;
        public $iDeliveryStatus;
        public $fQtyForDelivery;
        public $bPromotionApplied;
        public $fPromotionPriceExcl;
        public $fPromotionPriceIncl;
        public $cPromotionCode;
        public $iSOLinkedPOLineID;
        public $fLength;
        public $fWidth;
        public $fHeight;
        public $iPieces;
        public $iPiecesToProcess;
        public $iPiecesLastProcess;
        public $iPiecesProcessed;
        public $iPiecesReserved;
        public $iPiecesDeliver;
        public $iPiecesForDelivery;
        public $fQuantityUR;
        public $fQtyChangeUR;
        public $fQtyToProcessUR;
        public $fQtyLastProcessUR;
        public $fQtyProcessedUR;
        public $fQtyReservedUR;
        public $fQtyReservedChangeUR;
        public $fQtyDeliverUR;
        public $fQtyForDeliveryUR;
        public $fQtyLinkedUsedUR;
        public $iPiecesLinkedUsed;
        public $iSalesWhseID;
        public $iMajorIndustryCodeID;
        public $iCancellationReasonID;
        public $bReverseChargeApplied;


        public function __construct($db) {
            $this->conn = $db;
        }

        function insert() {
            // $query = "INSERT INTO
            //             {$this->table_name}
            //         SET
            //             iInvoiceID = :iInvoiceID,
            //             iOrigLineID = 0,
            //             iGrvLineID = 0,
            //             cDescription = :cDescription,
            //             iUnitsOfMeasureStockingID = 0,
            //             iUnitsOfMeasureCategoryID = 0,
            //             fQuantity = :fQuantity,
            //             fQtyChange = :fQtyChange,
            //             fQtyToProcess = :fQtyToProcess,
            //             fQtyLastProcess = 0,
            //             fQtyProcessed = 0,
            //             fQtyReserved = 0,
            //             fQtyReservedChange = 0,
            //             fUnitPriceExcl = :fUnitPriceExcl,
            //             fUnitPriceIncl = :fUnitPriceIncl,
            //             fUnitCost = :fUnitCost,
            //             fTaxRate = :fTaxRate,
            //             bIsSerialItem = 0,
            //             bIsWhseItem = 0,
            //             fAddCost = 0,
            //             iStockCodeID = :iStockCodeID,
            //             iJobID = 0,
            //             iWarehouseID = 0,
            //             iTaxTypeID = :iTaxTypeID,
            //             iPriceListNameID = 0,
            //             fQuantityLineTotIncl = :fQuantityLineTotIncl,
            //             fQuantityLineTotExcl = :fQuantityLineTotExcl,
            //             fQuantityLineTotInclNoDisc = :fQuantityLineTotInclNoDisc,
            //             fQuantityLineTotExclNoDisc = :fQuantityLineTotExclNoDisc,
            //             fQuantityLineTaxAmount = :fQuantityLineTaxAmount,
            //             fQuantityLineTaxAmountNoDisc = :fQuantityLineTaxAmountNoDisc,
            //             fQtyChangeLineTotIncl = :fQtyChangeLineTotIncl,
            //             fQtyChangeLineTotExcl = :fQtyChangeLineTotExcl,
            //             fQtyChangeLineTotInclNoDisc = :fQtyChangeLineTotInclNoDisc,
            //             fQtyChangeLineTotExclNoDisc = :fQtyChangeLineTotExclNoDisc,
            //             fQtyChangeLineTaxAmount = :fQtyChangeLineTaxAmount,
            //             fQtyChangeLineTaxAmountNoDisc = :fQtyChangeLineTaxAmountNoDisc,
            //             fQtyToProcessLineTotExclNoDisc = :fQtyToProcessLineTotExclNoDisc,
            //             fQtyToProcessLineTaxAmount = :fQtyToProcessLineTaxAmount,
            //             fQtyToProcessLineTaxAmountNoDisc = :fQtyToProcessLineTaxAmountNoDisc,
            //             fQtyLastProcessLineTotIncl = 0,
            //             fQtyLastProcessLineTotExcl = 0,
            //             fQtyLastProcessLineTotInclNoDisc = 0,
            //             fQtyLastProcessLineTotExclNoDisc = 0,
            //             fQtyLastProcessTaxAmount = 0,
            //             fQtyLastProcessTaxAmountNoDisc = 0,
            //             fQtyProcessedLineTotIncl = 0,
            //             fQtyProcessedLineTotExcl = 0,
            //             fQtyProcessedLineTotInclNoDisc = 0,
            //             fQtyProcessedLineTaxAmount = 0,
            //             fQtyProcessedLineTaxAmountNoDisc = 0,
            //             fUnitPriceExclForeign = 0,
            //             fUnitPriceInclForeign = 0,
            //             fAddCostForeign = 0,
            //             fQuantityLineTotInclForeign = 0,
            //             fQuantityLineTotExclForeign = 0,
            //             fQuantityLineTotInclNoDiscForeign = 0,
            //             fQuantityLineTotExclNoDiscForeign = 0,
            //             fQuantityLineTaxAmountForeign = 0,
            //             fQuantityLineTaxAmountNoDiscForeign = 0,
            //             fQtyChangeLineTotInclForeign = 0,
            //             fQtyChangeLineTotExclForeign = 0,
            //             fQtyChangeLineTotInclNoDiscForeign = 0,
            //             fQtyChangeLineTotExclNoDiscForeign = 0,
            //             fQtyChangeLineTaxAmountForeign = 0,
            //             fQtyChangeLineTaxAmountNoDiscForeign = 0,
            //             fQtyToProcessLineTotInclForeign = 0,
            //             fQtyToProcessLineTotExclForeign = 0,
            //             fQtyToProcessLineTotInclNoDiscForeign = 0,
            //             fQtyToProcessLineTotExclNoDiscForeign = 0,
            //             fQtyToProcessLineTaxAmountForeign = 0,
            //             fQtyToProcessLineTaxAmountNoDiscForeign = 0,
            //             fQtyLastProcessLineTotInclForeign = 0,
            //             fQtyLastProcessLineTotExclForeign = 0,
            //             fQtyLastProcessLineTotInclNoDiscForeign = 0,
            //             fQtyLastProcessLineTotExclNoDiscForeign = 0,
            //             fQtyLastProcessLineTaxAmountForeign = 0,
            //             fQtyLastProcessLineTaxAmountNoDiscForeign = 0,
            //             fQtyProcessedLineTotInclForeign = 0,
            //             fQtyProcessedLineTotExclForeign = 0,
            //             fQtyProcessedLineTotInclNoDiscForeign = 0,
            //             fQtyProcessedLineTotExclNoDiscForeign = 0,
            //             fQtyProcessedLineTaxAmountForeign = 0,
            //             fQtyProcessedLineTaxAmountNoDiscForeign = 0,
            //             iLineRepID = 0,
            //             iLineProjectID = 0,
            //             iLedgerAccountID = 0,
            //             iModule = 0,
            //             bChargeCom = 1,
            //             bIsLotItem = 0,
            //             iLotID = 0,
            //             cLotNumber = '',
            //             dLotExpiryDate = null,
            //             iMFPID = 0,
            //             iLineID = :iLineID,
            //             iLinkedLineID = 0,
            //             fQtyLinkedUsed = null,
            //             iUnitsofMeasureID = 0,
            //             fQtyToProcessLineTotIncl = :fQtyToProcessLineTotIncl,
            //             fQtyToProcessLineTotExcl = :fQtyToProcessLineTotExcl,
            //             fQtyToProcessLineTotInclNoDisc = :fQtyToProcessLineTotInclNoDisc;";

            $query = "INSERT INTO
            {$this->table_name}
            (
                iInvoiceID,
                iOrigLineID,
                iGrvLineID,
                cDescription,
                iUnitsOfMeasureStockingID,
                iUnitsOfMeasureCategoryID,
                fQuantity,
                fQtyChange,
                fQtyToProcess,
                fQtyLastProcess,
                fQtyProcessed,
                fQtyReserved,
                fQtyReservedChange,
                fUnitPriceExcl,
                fUnitPriceIncl,
                fUnitCost,
                fTaxRate,
                bIsSerialItem,
                bIsWhseItem,
                fAddCost,
                iStockCodeID,
                iJobID,
                iWarehouseID,
                iTaxTypeID,
                iPriceListNameID,
                fQuantityLineTotIncl,
                fQuantityLineTotExcl,
                fQuantityLineTotInclNoDisc,
                fQuantityLineTotExclNoDisc,
                fQuantityLineTaxAmount,
                fQuantityLineTaxAmountNoDisc,
                fQtyChangeLineTotIncl,
                fQtyChangeLineTotExcl,
                fQtyChangeLineTotInclNoDisc,
                fQtyChangeLineTotExclNoDisc,
                fQtyChangeLineTaxAmount,
                fQtyChangeLineTaxAmountNoDisc,
                fQtyToProcessLineTotExclNoDisc,
                fQtyToProcessLineTaxAmount,
                fQtyToProcessLineTaxAmountNoDisc,
                fQtyLastProcessLineTotIncl,
                fQtyLastProcessLineTotExcl,
                fQtyLastProcessLineTotInclNoDisc,
                fQtyLastProcessLineTotExclNoDisc,
                fQtyLastProcessLineTaxAmount,
                fQtyLastProcessLineTaxAmountNoDisc,
                fQtyProcessedLineTotIncl,
                fQtyProcessedLineTotExcl,
                fQtyProcessedLineTotInclNoDisc,
                fQtyProcessedLineTaxAmount,
                fQtyProcessedLineTaxAmountNoDisc,
                fUnitPriceExclForeign,
                fUnitPriceInclForeign,
                fAddCostForeign,
                fQuantityLineTotInclForeign,
                fQuantityLineTotExclForeign,
                fQuantityLineTotInclNoDiscForeign,
                fQuantityLineTotExclNoDiscForeign,
                fQuantityLineTaxAmountForeign,
                fQuantityLineTaxAmountNoDiscForeign,
                fQtyChangeLineTotInclForeign,
                fQtyChangeLineTotExclForeign,
                fQtyChangeLineTotInclNoDiscForeign,
                fQtyChangeLineTotExclNoDiscForeign,
                fQtyChangeLineTaxAmountForeign,
                fQtyChangeLineTaxAmountNoDiscForeign,
                fQtyToProcessLineTotInclForeign,
                fQtyToProcessLineTotExclForeign,
                fQtyToProcessLineTotInclNoDiscForeign,
                fQtyToProcessLineTotExclNoDiscForeign,
                fQtyToProcessLineTaxAmountForeign,
                fQtyToProcessLineTaxAmountNoDiscForeign,
                fQtyLastProcessLineTotInclForeign,
                fQtyLastProcessLineTotExclForeign,
                fQtyLastProcessLineTotInclNoDiscForeign,
                fQtyLastProcessLineTotExclNoDiscForeign,
                fQtyLastProcessLineTaxAmountForeign,
                fQtyLastProcessLineTaxAmountNoDiscForeign,
                fQtyProcessedLineTotInclForeign,
                fQtyProcessedLineTotExclForeign,
                fQtyProcessedLineTotInclNoDiscForeign,
                fQtyProcessedLineTotExclNoDiscForeign,
                fQtyProcessedLineTaxAmountForeign,
                fQtyProcessedLineTaxAmountNoDiscForeign,
                iLineRepID,
                iLineProjectID,
                iLedgerAccountID,
                iModule,
                bChargeCom,
                bIsLotItem,

                iMFPID,
                iLineID,
                iLinkedLineID,
                fQtyLinkedUsed,
                iUnitsofMeasureID,
                fQtyToProcessLineTotIncl,
                fQtyToProcessLineTotExcl,
                fQtyToProcessLineTotInclNoDisc
            )
            VALUES
            (
                :iInvoiceID,
                0,
                0,
                :cDescription,
                0,
                0,
                :fQuantity,
                :fQtyChange,
                :fQtyToProcess,
                0,
                0,
                0,
                0,
                :fUnitPriceExcl,
                :fUnitPriceIncl,
                :fUnitCost,
                :fTaxRate,
                0,
                0,
                0,
                :iStockCodeID,
                0,
                0,
                :iTaxTypeID,
                0,
                :fQuantityLineTotIncl,
                :fQuantityLineTotExcl,
                :fQuantityLineTotInclNoDisc,
                :fQuantityLineTotExclNoDisc,
                :fQuantityLineTaxAmount,
                :fQuantityLineTaxAmountNoDisc,
                :fQtyChangeLineTotIncl,
                :fQtyChangeLineTotExcl,
                :fQtyChangeLineTotInclNoDisc,
                :fQtyChangeLineTotExclNoDisc,
                :fQtyChangeLineTaxAmount,
                :fQtyChangeLineTaxAmountNoDisc,
                :fQtyToProcessLineTotExclNoDisc,
                :fQtyToProcessLineTaxAmount,
                :fQtyToProcessLineTaxAmountNoDisc,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                1,
                0,
 
                0,
                :iLineID,
                0,
                null,
                0,
                :fQtyToProcessLineTotIncl,
                :fQtyToProcessLineTotExcl,
                :fQtyToProcessLineTotInclNoDisc
            );";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':iInvoiceID', $this->iInvoiceID);

            $stmt->bindParam(':cDescription', $this->cDescription);

            $stmt->bindParam(':fQuantity', $this->fQuantity);
            $stmt->bindParam(':fQtyChange', $this->fQtyChange);
            $stmt->bindParam(':fQtyToProcess', $this->fQtyToProcess);

            $stmt->bindParam(':fUnitPriceExcl', $this->fUnitPriceExcl);
            $stmt->bindParam(':fUnitPriceIncl', $this->fUnitPriceIncl);
            $stmt->bindParam(':fUnitCost', $this->fUnitCost);
            $stmt->bindParam(':fTaxRate', $this->fTaxRate);

            $stmt->bindParam(':iStockCodeID', $this->iStockCodeID);

            $stmt->bindParam(':iTaxTypeID', $this->iTaxTypeID);

            $stmt->bindParam(':fQuantityLineTotIncl', $this->fQuantityLineTotIncl);
            $stmt->bindParam(':fQuantityLineTotExcl', $this->fQuantityLineTotExcl);

            $stmt->bindParam(':fQuantityLineTotInclNoDisc', $this->fQuantityLineTotInclNoDisc);
            $stmt->bindParam(':fQuantityLineTotExclNoDisc', $this->fQuantityLineTotExclNoDisc);
            
            $stmt->bindParam(':fQuantityLineTaxAmount', $this->fQuantityLineTaxAmount);
            $stmt->bindParam(':fQuantityLineTaxAmountNoDisc', $this->fQuantityLineTaxAmountNoDisc);
            $stmt->bindParam(':fQtyChangeLineTotIncl', $this->fQtyChangeLineTotIncl);
            $stmt->bindParam(':fQtyChangeLineTotExcl', $this->fQtyChangeLineTotExcl);

            $stmt->bindParam(':fQtyChangeLineTotInclNoDisc', $this->fQtyChangeLineTotInclNoDisc);
            $stmt->bindParam(':fQtyChangeLineTotExclNoDisc', $this->fQtyChangeLineTotExclNoDisc);

            $stmt->bindParam(':fQtyChangeLineTaxAmount', $this->fQtyChangeLineTaxAmount);
            $stmt->bindParam(':fQtyChangeLineTaxAmountNoDisc', $this->fQtyChangeLineTaxAmountNoDisc);
            $stmt->bindParam(':fQtyToProcessLineTotExclNoDisc', $this->fQtyToProcessLineTotExclNoDisc);
            $stmt->bindParam(':fQtyToProcessLineTaxAmount', $this->fQtyToProcessLineTaxAmount);
            $stmt->bindParam(':fQtyToProcessLineTaxAmountNoDisc', $this->fQtyToProcessLineTaxAmountNoDisc);
            
            $stmt->bindParam(':iLineID', $this->iLineID);
            
            $stmt->bindParam(':fQtyToProcessLineTotIncl', $this->fQtyToProcessLineTotIncl);
            $stmt->bindParam(':fQtyToProcessLineTotExcl', $this->fQtyToProcessLineTotExcl);
            $stmt->bindParam(':fQtyToProcessLineTotInclNoDisc', $this->fQtyToProcessLineTotInclNoDisc);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
?>