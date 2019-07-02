<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/workflow.php';
    include_once '../objects/invoice.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new Customer($db);

    $delivery = new WorkflowDelivery($db);
    $details = new WorkflowDetails($db);
    $session = new WorkflowSession($db);
    $paper = new WorkflowPaper($db);

    $lines = new InvoiceLines($db);

    $delivery->jobID = isset($_GET['id']) ? $_GET['id'] : die();
    $session->sessionID = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $delivery->readJob();

    $stmt2 = $session->readSesh();
    $stmt3 = $paper->get();

    $num = $stmt->rowCount();
    $numall = $stmt->rowCount() + $stmt2->rowCount();

    $job_arr = array();
    if ($numall > 0) {
        $job_arr['records'] = array();

        $i = 0;
        $i2 = 0;
        $range = '(';

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            if (++$i2 === $num) {
                $range .= $workflow_id . ')';
            } else {
                $range .= $workflow_id . ',';
            }

            $job_arr['jobID'] = $jobID;
            $job_arr['invCount'] = $num;
            $job_arr['vehicle'] = $vehicle;

            if ($i === 0) {
                $job_arr['time'] = $time;
            }

            $job_item = array(
                'workflow_id' => $workflow_id,
                'jobID' => $jobID,
                'cust_id' => $cust_id,
                'invoice_no' => $invoice_no,
                'comments' => $comments,
                'vehicle' => $vehicle,
                'delivery_status' => $delivery_status,
                'status' => $status,
                'step' => $step,
                'time' => $time,
                'data' => $data
            );

            $custdata = $customer->getCustDetails($data, $cust_id);

            $job_item['company_name'] = $custdata['company_name'];
            $job_item['address'] = str_replace('\n', '', $custdata['address']);
            $job_item['tel'] = $custdata['tel'];

            $job_item['product'] = $details->getProducts($workflow_id);

            if (!isset($job_item['product'])) {
                $job_item['product'] = $lines->getProducts($workflow_id);
            }

            array_push($job_arr['records'], $job_item);

            $i++;
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $job_arr['driver'] = ucfirst($row2['driver']);
            $job_arr['seshstatus'] = $row2['status'];
        }

        if ($stmt3->rowCount() > 0) {
            $job_arr['papersumm'] = array();
            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $paper_sum = 0;
                extract($row3);

                $paper_sum += $details->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 1);
                $paper_sum += $details->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 2);
                $paper_sum += $details->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 3);

                $paper_sum += $lines->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 1);
                $paper_sum += $lines->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 2);
                $paper_sum += $lines->getPaperRange($range, $paperBrand, $job_arr['vehicle'], 3);

                $invlines = $lines->getPaperRangeRecs($range, $paperBrand, $job_arr['vehicle']);

                $detlines = $details->getPaperRangeRecs($range, $paperBrand, $job_arr['vehicle']);

                foreach ($invlines as $line) {
                    $custdata = $customer->getCustDetails(+$line['data'], +$line['cust_id']);
                    $line['company_name'] = $custdata['company_name'];
                }

                foreach ($detlines as $line) {
                    $custdata = $customer->getCustDetails(+$line['data'], +$line['cust_id']);
                    $line['company_name'] = $custdata['company_name'];
                }

                $paper_item = array(
                    'brand' => $paperBrand,
                    'total' => $paper_sum
                );

                $paper_item['records'] = array_merge($invlines, $detlines);

                if ($paper_sum !== 0) {
                    array_push($job_arr['papersumm'], $paper_item);
                }


            
            }
        }


        http_response_code(200);
        echo json_encode($job_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>