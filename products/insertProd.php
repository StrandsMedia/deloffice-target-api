<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/products.php';
    include_once '../objects/website.php';

    $database = new Database();
    $db = $database->getConnection();

    $product = new Products($db);

    $webdatabase = new WebServerDatabase();
    $webdb = $webdatabase->getConnection();

    if (isset($webdb)) {
        $webproduct = new Product($webdb);
    }

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $product->p_id = $data->p_id;
            $product->pf_cat_id = isset($data->pf_cat_id) ? $data->pf_cat_id : 0;
            $product->category2 = isset($data->category2) ? $data->category2 : 0;
            $product->category3 = isset($data->category3) ? $data->category3 : 0;
            $product->category4 = isset($data->category4) ? $data->category4 : 0;

            $product->des1 = $data->des1;
            $product->des2 = $data->des2;
            $product->des3 = $data->des3;

            $product->deslong1 = $data->deslong1;
            $product->deslong2 = $data->deslong2;
            $product->deslong3 = $data->deslong3;
            $product->deslong4 = $data->deslong4;
            $product->deslong5 = $data->deslong5;
            $product->deslong6 = $data->deslong6;
            $product->deslong7 = $data->deslong7;

            $product->puprice = isset($data->puprice) ? $data->puprice : 0.00;
            $product->coprice = isset($data->coprice) ? $data->coprice : 0.00;
            $product->wsprice = isset($data->wsprice) ? $data->wsprice : 0.00;
            $product->delcityprice = isset($data->delcityprice) ? $data->delcityprice : 0.00;
            $product->delcitypromo = isset($data->delcitypromo) ? $data->delcitypromo : 0.00;
            $product->taxcode = $data->taxcode;
            $product->visible = $data->visible;
            $product->avgcost = isset($data->avgcost) ? $data->avgcost : 0.00;

            if (isset($webdb)) {
                $webproduct->id = $data->p_id;

                $webproduct->category1 = isset($data->pf_cat_id) ? $data->pf_cat_id : 0;
                $webproduct->category2 = isset($data->category2) ? $data->category2 : 0;
                $webproduct->category3 = isset($data->category3) ? $data->category3 : 0;
                $webproduct->category4 = isset($data->category4) ? $data->category4 : 0;

                $webproduct->des1 = $data->des1;
                $webproduct->des2 = $data->des2;
                $webproduct->des3 = $data->des3;

                $webproduct->deslong1 = $data->deslong1;
                $webproduct->deslong2 = $data->deslong2;
                $webproduct->deslong3 = $data->deslong3;
                $webproduct->deslong4 = $data->deslong4;
                $webproduct->deslong5 = $data->deslong5;
                $webproduct->deslong6 = $data->deslong6;
                $webproduct->deslong7 = $data->deslong7;

                $webproduct->puprice = isset($data->puprice) ? $data->puprice : 0.00;
                $webproduct->coprice = isset($data->coprice) ? $data->coprice : 0.00;
                $webproduct->wsprice = isset($data->wsprice) ? $data->wsprice : 0.00;
                $webproduct->delcityprice = isset($data->delcityprice) ? $data->delcityprice : 0.00;
                $webproduct->delcitypromo = isset($data->delcitypromo) ? $data->delcitypromo : 0.00;
                $webproduct->taxcode = isset($data->taxcode) ? $data->taxcode : 1.4;
                $webproduct->visible = $data->visible;
                $webproduct->avgcost = isset($data->avgcost) ? $data->avgcost : 0.00;

                if ($webproduct->isProductLive()) {
                    if ($webproduct->update()) {
                        if ($product->insertProd()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Product inserted succesfully.'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Failed to insert local'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Failed to insert web'
                        ));
                    }
                } else {
                    if ($webproduct->insert()) {
                        if ($product->insertProd()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Product inserted succesfully.'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Failed to insert local'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Failed to insert web'
                        ));
                    }
                }
            } else {
                if ($product->insertProd()) {
                    http_response_code(201);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Product inserted succesfully.'
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Failed to insert local'
                    ));
                }
            }

        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Error. Cannot insert product.'
            ));
        }
    }


?>