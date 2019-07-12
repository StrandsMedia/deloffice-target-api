<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/pastel.php';
    include_once '../objects/products.php';

    $data = json_decode(file_get_contents('php://input'));

    $database = new Database();
    $db = $database->getConnection();

    $srvdatabase = new DelServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $customer = new DelCustomer($db);
    $product = new Products($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $searchstring = $data->prodsearch;

            $customer->cust_id = $data->cust_id;
            $priceDefault = $customer->fetchDefaultPrice();

            $stmt = $product->searchOne($searchstring);
            $num = $stmt->rowCount();

            if ($num > 0) {
                $prod_arr = array();
                $prod_arr['records'] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
            
                    $prod_item = array(
                        'p_id' => $p_id,
                        'Description_1' => $des1,
                        'Description_2' => $des2,
                        'Description_3' => $des3,
                        'PP' => $puprice, // Public Price
                        'CP1' => $coprice, // Corporate Price 1
                        'CP2' => $delcityprice, // Corporate Price 2
                        'WP' => $wsprice, // Wholesale Price
                        'TP' => $delcitypromo, // Tender Price
                    );

                    switch (+$priceDefault) {
                        case 1:
                            $prod_item['fExclPrice'] = +$puprice;
                            $prod_item['pricecat'] = 'PP';
                            break;
                        case 2:
                            $prod_item['fExclPrice'] = +$coprice;
                            $prod_item['pricecat'] = 'CP1';
                            break;
                        case 3:
                            $prod_item['fExclPrice'] = +$wsprice;
                            $prod_item['pricecat'] = 'WP';
                            break;
                        case 4:
                            $prod_item['fExclPrice'] = +$delcityprice;
                            $prod_item['pricecat'] = 'CP2';
                            break;
                        case 5:
                            $prod_item['fExclPrice'] = +$delcitypromo;
                            $prod_item['pricecat'] = 'TP';
                            break;
                    }

                    if (isset($srvdb)) {
                        $stkitem = new StkItem($srvdb);
                        $taxrate = new TaxRate($srvdb);

                        $stkitem->cSimpleCode = $p_id;
                        $stkinfo = $stkitem->getProductInfo();

                        $row2 = $stkinfo->fetch(PDO::FETCH_ASSOC);

                        $prod_item['StockLink'] = $row2['StockLink'];
                        $prod_item['Qty_On_Hand'] = $row2['Qty_On_Hand'];
                        $prod_item['AveUCst'] = $row2['AveUCst'];

                        $fetchtax = $taxrate->getTaxRate(1.4);
                        $prod_item['TaxRate'] = +$fetchtax['TaxRate'];
                        $prod_item['idTaxRate'] = $fetchtax['idTaxRate'];
                    }


                    array_push($prod_arr['records'], $prod_item);
                }
        
                http_response_code(200);
                echo json_encode($prod_arr);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => 'No product found.'
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'message' => 'Service unavailable.'
            ));
        }
    }
    
?>