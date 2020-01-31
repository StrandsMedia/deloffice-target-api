<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            switch($data->data) {
                case 1:
                    $srv_database = new DelServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
                case 2:
                    $srv_database = new RnsServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
                case 3:
                    $srv_database = new PnpServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
            }

            $invnum = new InvNum($srvdb);
            $invlines = new _btblInvoiceLines($srvdb);
            $workflow = new Workflow($db);

            $invnum->AccountID = $data->DCLink;

            $invnum->Description = str_pad($data->workflow_id, 8, '0', STR_PAD_LEFT);
            $invnum->InvDate = date('Y-m-d H:i:s');
            $invnum->OrderDate = date('Y-m-d H:i:s');
            $invnum->DueDate = date('Y-m-d H:i:s');
            $invnum->DeliveryDate = date('Y-m-d H:i:s');

            $invnum->OrderNum = $invnum->fetchNextOrder();

            $invnum->InvTotExclDEx = $data->TotalExcl;
            $invnum->InvTotTaxDEx = $data->TotalTax;
            $invnum->InvTotInclDEx = $data->TotalIncl;
            $invnum->InvTotExcl = $data->TotalExcl;
            $invnum->InvTotTax = $data->TotalTax;
            $invnum->InvTotIncl = $data->TotalIncl;

            $invnum->OrdTotExclDEx = $data->TotalExcl;
            $invnum->OrdTotTaxDEx = $data->TotalTax;
            $invnum->OrdTotInclDEx = $data->TotalIncl;
            $invnum->OrdTotExcl = $data->TotalExcl;
            $invnum->OrdTotTax = $data->TotalTax;
            $invnum->OrdTotIncl = $data->TotalIncl;

            $invnum->cTaxNumber = $data->Tax_Number ? $data->Tax_Number : 'N/A';
            $invnum->cAccountName = $data->company_name;

            $invnum->InvTotInclExRounding = $data->TotalIncl;
            $invnum->OrdTotInclExRounding = $data->TotalIncl;

            if ($invnum->saveToPastel()) {
                $lastId = $invnum->getLastId();

                $countDone = 0;

                foreach($data->entries as $idx => $entry) {
                    $invlines->iInvoiceID = $lastId;
                    
                    $invlines->cDescription = $entry->Description_1;

                    $invlines->fQuantity = $entry->qty;
                    $invlines->fQtyChange = $entry->qty;
                    $invlines->fQtyToProcess = $entry->qty;

                    $invlines->fUnitPriceExcl = $entry->fExclPrice;
                    $invlines->fUnitPriceIncl = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fUnitCost = $entry->AveUCst;
                    $invlines->fTaxRate = $entry->TaxRate;

                    $invlines->iStockCodeID = $entry->StockLink;

                    $invlines->iTaxTypeID = $entry->idTaxRate;

                    $invlines->fQuantityLineTotIncl = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fQuantityLineTotExcl = $entry->fExclPrice;
                    $invlines->fQuantityLineTotInclNoDisc = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fQuantityLineTotExclNoDisc = $entry->fExclPrice;
                    $invlines->fQuantityLineTaxAmount = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    $invlines->fQuantityLineTaxAmountNoDisc = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    $invlines->fQtyChangeLineTotIncl = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fQtyChangeLineTotExcl = $entry->fExclPrice;
                    $invlines->fQtyChangeLineTotInclNoDisc = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fQtyChangeLineTotExclNoDisc = $entry->fExclPrice;
                    $invlines->fQtyChangeLineTaxAmount = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    $invlines->fQtyChangeLineTaxAmountNoDisc = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    $invlines->fQtyToProcessLineTotExclNoDisc = $entry->fExclPrice;
                    $invlines->fQtyToProcessLineTaxAmount = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    $invlines->fQtyToProcessLineTaxAmountNoDisc = ($entry->fExclPrice) * (($entry->TaxRate) / 100);
                    
                    $invlines->iLineID = $idx;

                    $invlines->fQtyToProcessLineTotIncl = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);
                    $invlines->fQtyToProcessLineTotExcl = $entry->fExclPrice;
                    $invlines->fQtyToProcessLineTotInclNoDisc = ($entry->fExclPrice) * (($entry->TaxRate + 100) / 100);

                    if ($invlines->insert()) {
                        $countDone++;
                    }
                }

                if ($countDone > 0) {
                    $workflow->status = 5;
                    $workflow->workflow_id = $data->workflow_id;
                    if ($workflow->update(4)) {
                        http_response_code(200);
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => "{$countDone} have been sent to Pastel."
                        ));
                    } else {
                        http_response_code(503);
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'An unknown error occured. Please try again later.'
                        ));
                    }
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'An unknown error occured. Please try again later.'
                    ));
                }
            } else {
                http_response_code(503);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'An unknown error occured. Please try again later.'
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No data found.'
            ));
        }
    }
?>