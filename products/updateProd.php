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
            $product->pf_cat_id = $data->pf_cat_id;
            $product->category2 = $data->category2;
            $product->category3 = $data->category3;
            $product->category4 = $data->category4;

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

            $product->puprice = $data->puprice;
            $product->coprice = $data->coprice;
            $product->wsprice = $data->wsprice;
            $product->delcityprice = $data->delcityprice;
            $product->delcitypromo = $data->delcitypromo;
            $product->taxcode = $data->taxcode;
            $product->visible = $data->visible;
            $product->avgcost = $data->avgcost;

            if (isset($webdb)) {
                $webproduct->id = $data->p_id;

                $webproduct->category1 = $data->pf_cat_id;
                $webproduct->category2 = $data->category2;
                $webproduct->category3 = $data->category3;
                $webproduct->category4 = $data->category4;

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

                $webproduct->puprice = $data->puprice;
                $webproduct->coprice = $data->coprice;
                $webproduct->wsprice = $data->wsprice;
                $webproduct->delcityprice = $data->delcityprice;
                $webproduct->delcitypromo = $data->delcitypromo;
                $webproduct->taxcode = $data->taxcode;
                $webproduct->visible = $data->visible;
                $webproduct->avgcost = $data->avgcost;

                if ($webproduct->isProductLive()) {
                    if ($webproduct->update()) {
                        if ($product->updateProd()) {
                            http_response_code(200);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Product updated succesfully.'
                            ));
                        }
                    }
                } else {
                    if ($webproduct->insert()) {
                        if ($product->updateProd()) {
                            http_response_code(200);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Product updated succesfully.'
                            ));
                        }
                    }
                }
            } else {
                if ($product->updateProd()) {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Product updated succesfully.'
                    ));
                }
            }

        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Error. Cannot update product.'
            ));
        }
    }


?>