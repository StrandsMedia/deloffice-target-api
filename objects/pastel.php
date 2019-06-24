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
            $query = "SELECT DCLink FROM {$this->table_name} WHERE Account = '{$acc}'";

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

        function saveToPastel() {
            $query = "INSERT INTO
                        {$this->table_name}
                    SET
                        DocType = 4,
                        DocVersion = 1,
                        DocState = 1,
                        DocFlag = 0,
                        OrigDocID = 0,
                        GrvID = 0,
                        AccountID = :AccountID,
                        Description = '',
                        InvDate = :InvDate,
                        OrderDate = :OrderDate,
                        DeliveryDate = :DeliveryDate,
                        TaxInclusive = 0,
                        Email_Sent = 1,
                        DelMethodID = 0,
                        DocRepID = 0,
                        OrderNum = :OrderNum,
                        InvDisc = 0,
                        ProjectID = 0,
                        TillID = 0,
                        GrvSplitFixedCost = 0,
                        GrvSplitFixedAmnt = 0,
                        OrderStatusID = 0,
                        ;";
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
                    $condition = ">= 31 ";
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
                    $condition = "> 180 ";
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

        public $Qty_On_Hand;
        public $AveUCst; 
        public $StockLink; 
        public $Description_1;
        public $Description_2;
        public $Description_3;
        public $cSimpleCode;

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
                        *
                    FROM
                        {$this->table_name}
                    WHERE
                        cSimpleCode = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->cSimpleCode, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getPrice() {
            $query = "SELECT
                        TOP 1
                        Qty_On_Hand, AveUCst, StockLink, Description_1, Description_2, Description_3
                    FROM
                        {$this->table_name}
                    WHERE
                        cSimpleCode = ?;";

            $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            $stmt->bindParam(1, $this->cSimpleCode, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt;
        }

        function getStk($id) {
            $query = "SELECT
                        TOP 1
                        Qty_On_Hand
                    FROM
                        {$this->table_name}
                    WHERE
                        cSimpleCode = ?;";

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
?>