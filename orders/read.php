<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $server_database = new ServerDatabase();

    $db = $database->getConnection();
    $srvdb = $server_database->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);
    $client = new Client($srvdb);

    $data = json_decode(file_get_contents('php://input'));

    $status = isset($data->status) ? $data->status : null;

    $stmt = $invoice->getInvoices($status);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $invoice_arr = array();
        $invoice_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                'sales_rep' => $sales_rep
            );

            $client_details = $client->getInfo($customerCode);

            $invoice_item['Contact_Person'] = $client_details['Contact_Person'];
            $invoice_item['Physical1'] = $client_details['Physical1'];
            $invoice_item['Physical2'] = $client_details['Physical2'];
            $invoice_item['Physical3'] = $client_details['Physical3'];
            $invoice_item['Physical4'] = $client_details['Physical4'];
            $invoice_item['Physical5'] = $client_details['Physical5'];
            $invoice_item['Telephone'] = $client_details['Telephone'];
            $invoice_item['Tax_Number'] = $client_details['Tax_Number'];
            $invoice_item['Registration'] = $client_details['Registration'];

            $invoice_item['invAmt'] = $lines->getInvLines($invoice_id);

            $stmt2 = $lines->getInvItems($invoice_id);
            $num2 = $stmt2->rowCount();

            $invoice_item['lines'] = array();

            if ($num2 > 0) {
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $product_item = array(
                        'invlineid' => $row2['invlineid'], 
                        'line_id' => $row2['line_id'],
                        'AveUCst' => $row2['AveUCst'],
                        'Description_1' => $row2['Description_1'],
                        'Description_2' => $row2['Description_2'],
                        'Description_3' => $row2['Description_3'],
                        'Qty_On_Hand' => $row2['Qty_On_Hand'],
                        'StockLink' => $row2['StockLink'],
                        'TaxRate' => $row2['TaxRate'],
                        'idTaxRate' => $row2['idTaxRate'],
                        'fExclPrice' => $row2['fExclPrice'],
                        'fExclPrice2' => $row2['fExclPrice2'],
                        'p_id' => $row2['p_id'],
                        'qty' => $row2['qty'],
                        'pricecat' => $row2['pricecat'],
                        // 'checked' => $row2['checked'],
                        // 'verified' => $row2['verified']
                    );

                    array_push($invoice_item['lines'], $product_item);
                }
            }

            array_push($invoice_arr['records'], $invoice_item);
        }

        echo json_encode($invoice_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>