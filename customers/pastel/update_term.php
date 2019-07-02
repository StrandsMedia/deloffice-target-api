<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    $data = json_decode(file_get_contents("php://input"));

    switch (+$data->data) {
        case 1:
            $srvdatabase = new DelServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
        case 2:
            $srvdatabase = new RnsServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
        case 3:
            $srvdatabase = new PnpServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
    }

    $client = new Client($srvdb);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $client->DCLink = $data->DCLink;
            $client->AccountTerms = $data->term;

            if ($client->updateTerm()) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Account terms were updated."
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "message" => "Unable to update account terms."
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                "message" => "Unable to update account terms."
            ));
        }
    }
?>