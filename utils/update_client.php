<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    ini_set('max_execution_time', 0);

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    for ($x = 1; $x <= 3; $x++) {
        switch ($x) {
            case 1:
                $srvdatabase = new DelServerDatabase();
                $srvdb = $srvdatabase->getConnection();

                $customer = new DelCustomer($db);
                break;
            case 2:
                $srvdatabase = new RnsServerDatabase();
                $srvdb = $srvdatabase->getConnection();

                $customer = new RnsCustomer($db);
                break;
            case 3:
                $srvdatabase = new PnpServerDatabase();
                $srvdb = $srvdatabase->getConnection();

                $customer = new PnpCustomer($db);
                break;
        }

        if (isset($srvdb)) {
            $client = new Client($srvdb);
            if (isset($client)) {
                $stmt = $client->getClients();
        
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    if ($customer->existAll($row['Account'])) {
                        // echo $customer->updateAll($row);
                    } else {
                        if ($customer->insertAll($row)) {
                            echo $row['Account'] . '\n';
                        }
                    }
                }
            }
        }
    } 
?>