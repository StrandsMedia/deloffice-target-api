<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/pastel.php';
    include_once '../objects/purgatory.php';

    $database = new Database();
    $db = $database->getConnection();

    $srvdatabase = new DelServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);
    $purgatory = new Purgatory($db);

    if (isset($srvdb)) {
        $client = new Client($srvdb);
    }

    $invoice->invoice_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $invoice->getInvoice();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $invoice_arr = array();
        $invoice_arr['records'] = array();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);

        $invoice_item = array(
            'invoice_id' => $invoice_id, 
            'company_name' => $company_name,
            'customerCode' => $customerCode,
            'InvDate' => $InvDate,
            'InvStatus' => $InvStatus,
            'TotalIncl' => $TotalIncl,
            'TotalExcl' => $TotalExcl,
            'TotalTax' => $TotalTax,
            'poNumber' => $poNumber,
            'invNumber' => $invNumber,
            'workflow_id' => $workflow_id,
            'notes' => $notes,
            'cust_id' => $cust_id,
            'user' => $user,
            'invRef' => $invRef
        );

        if (isset($srvdb) && isset($customerCode)) {
            $client_data = $client->getInfo($customerCode);

            $invoice_item['DCLink'] = $client_data['DCLink'];
            $invoice_item['Physical1'] = $client_data['Physical1'];
            $invoice_item['Physical2'] = $client_data['Physical2'];
            $invoice_item['Physical3'] = $client_data['Physical3'];
            $invoice_item['Physical4'] = $client_data['Physical4'];
            $invoice_item['Tax_Number'] = $client_data['Tax_Number'];
            $invoice_item['Registration'] = $client_data['Registration'];
            $invoice_item['Contact_Person'] = $client_data['Contact_Person'];
            $invoice_item['Telephone'] = $client_data['Telephone'];
        }

        $invoice_item['entries'] = array();

        $stmt2 = $lines->getInvItems($invoice_id);
        
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $line_item = array(
                'invlineid' => $invlineid,
                'invoice_id' => $invoice_id,
                'line_id' => $line_id,
                'AveUCst' => $AveUCst,
                'Description_1' => $Description_1,
                'Description_2' => $Description_2,
                'Description_3' => $Description_3,
                'Qty_On_Hand' => $Qty_On_Hand,
                'StockLink' => $StockLink,
                'TaxRate' => $TaxRate,
                'idTaxRate' => $idTaxRate,
                'fExclPrice' => $fExclPrice,
                'p_id' => $p_id,
                'qty' => $qty,
                'pricecat' => $pricecat,
                'fExclPrice2' => $fExclPrice2,
                'checked' => $checked,
                'verified' => $verified,
                'purgatory' => $purgatory->ifPurgatory($invlineid)
            );

            if (isset($srvdb)) {
                $stk = new StkItem($srvdb);

                $line_item['Qty_On_Hand'] = $stk->getStk($p_id);
            }

            array_push($invoice_item['entries'], $line_item);
        }

        array_push($invoice_arr['records'], $invoice_item);
        

        echo json_encode($invoice_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>