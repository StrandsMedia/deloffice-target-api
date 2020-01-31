<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $delivery = new WorkflowDelivery($db);
    $history = new WorkflowHistory($db);
    $details = new WorkflowDetails($db);
    $session = new WorkflowSession($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $history->user = $data->user;
            $session->user = $data->user;
            $workflow->data = $data->data;

            switch (+$data->data) {
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
            
            if (isset($srvdb)) {
                $client = new Client($srvdb);
                $postar = new PostAR($srvdb);
            }

            $step = $data->step;
            switch ($step) {
                case 1:
                    if ($data->status == '1') {
                        $workflow->cust_id = $data->cust_id;

                        $credit = "Entry sent to Credit Control.";
                    
                        if (isset($srvdb)) {
                            if (isset($data->customerCode)) {
                                $link = $client->getDCLink($data->customerCode);
                                $term = $client->getTerms($data->customerCode);

                                $postar->AccountLink = $link;

                                if ($term !== 6) {
                                    if ($postar->getOutstanding($term) > 0) {
                                        $workflow->status = $data->status;
                                        $workflow->creditCtrl = 1;
                                        $credit = $credit . ' Account is over terms.';
                                    } else {
                                        $workflow->status = $data->status;
                                        $workflow->creditCtrl = 0;
                                        $credit = 'Entry processed successfully.';
                                    }
                                } else {
                                    $workflow->status = $data->status;
                                    $workflow->creditCtrl = 0;
                                    $credit = 'Entry processed successfully.';
                                }
                            } else {
                                $workflow->status = $data->status;
                                $workflow->creditCtrl = 1;
                                $credit = $credit . ' Customer Code Missing.';
                            }
                        } else {
                            $workflow->status = $data->status;
                            $workflow->creditCtrl = 0;
                            $credit = 'Entry processed successfully.';
                        }

                        $workflow->status = $data->status;
                        if ($workflow->create(3)) {
                            $lastId = $db->lastInsertId();

                            $workflow->workflow_id = $lastId;

                            $delivery->workflow_id = $lastId;
                            $delivery->urgent = 0;
                            $delivery->delivery_status = $data->status;
                            $delivery->cust_id = $data->cust_id;

                            if ($delivery->createDelivery()) {
                                $history->workflow_id = $workflow->workflow_id;
                                $history->step = $data->status;
                                if ($history->insertHistory()) {
                                    $details->workflow_id = $workflow->workflow_id;
                                    if ($details->parseInsert($data)) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to create entry.'
                                            )
                                        );
                                    }
                                }
                            }
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to create session.'
                                )
                            );
                        }
                    } else {
                        $workflow->cust_id = $data->cust_id;
                        $workflow->status = $data->status;
                        $workflow->orderNo = $data->orderNo;

                        $credit = "Entry sent to Credit Control.";
                        
                        if (isset($srvdb)) {
                            if (isset($data->customerCode)) {
                                $link = $client->getDCLink($data->customerCode);
                                $term = $client->getTerms($data->customerCode);

                                $postar->AccountLink = $link;

                                if ($term !== 6) {
                                    if ($postar->getOutstanding($term) > 0) {
                                        $workflow->status = $data->status;
                                        $workflow->creditCtrl = 1;
                                        $credit = $credit . ' Account is over terms.';
                                    } else {
                                        $workflow->status = $data->status;
                                        $workflow->creditCtrl = 0;
                                        $credit = 'Entry processed successfully.';
                                    }
                                } else {
                                    $workflow->status = $data->status;
                                    $workflow->creditCtrl = 0;
                                    $credit = 'Entry processed successfully.';
                                }
                            } else {
                                $workflow->status = $data->status;
                                $workflow->creditCtrl = 1;
                                $credit = $credit . ' Customer Code Missing.';
                            }
                        } else {
                            $workflow->status = $data->status;
                            $workflow->creditCtrl = 0;
                            $credit = 'Entry processed successfully.';
                        }


                        if ($workflow->create(2)) {
                            $lastId = $db->lastInsertId();
                            $workflow->workflow_id = $lastId;

                            $delivery->workflow_id = $lastId;
                            $delivery->urgent = 0;
                            $delivery->delivery_status = $data->status;
                            $delivery->cust_id = $data->cust_id;

                            if ($delivery->createDelivery()) {
                                $history->workflow_id = $workflow->workflow_id;
                                $history->step = $data->status;
                                if ($history->insertHistory()) {
                                    $details->workflow_id = $workflow->workflow_id;
                                    if ($details->parseInsert($data)) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to create entry.'
                                            )
                                        );
                                    }
                                } else {
                                    echo json_encode(
                                        array(
                                            'status' => 'error',
                                            'message' => 'Unable to create entry.'
                                        )
                                    );
                                }
                            } else {
                                echo json_encode(
                                    array(
                                        'status' => 'error',
                                        'message' => 'Unable to create entry.'
                                    )
                                );
                            }

                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to create entry.'
                                )
                            );
                        }
                    }
                    break;
                case 2:
                    $workflow->workflow_id = $data->workflow_id;
                    $workflow->status = $data->status;
                    if ($workflow->update(4)) {
                        $history->workflow_id = $data->workflow_id;
                        $history->step = $data->status;
                        $history->comment = $data->note;
                        if ($history->insertHistory()) {
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Entry created successfully.'
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
                case 3:
                    $credit = "Entry sent to Credit Control.";

                    $workflow->workflow_id = $data->workflow_id;

                    $creditCtrl = $workflow->getCreditCtrlStatus();
                    
                    if ($creditCtrl == 3) {
                        $workflow->status = $data->status;
                        $workflow->creditCtrl = 3;
                        $credit = 'Entry processed successfully.';
                    } else {
                        if (isset($srvdb)) {
                            if (isset($data->customerCode)) {
                                $link = $client->getDCLink($data->customerCode);
                                $term = $client->getTerms($data->customerCode);
    
                                $postar->AccountLink = $link;
    
                                if ($term !== 6) {
                                    if ($postar->getOutstanding($term) > 0) {
                                        $workflow->status = $data->status;
                                            $workflow->creditCtrl = 1;
                                        $credit = $credit . ' Account is over terms.';
                                    } else {
                                        $workflow->status = $data->status;
                                        $workflow->creditCtrl = 0;
                                        $credit = 'Entry processed successfully.';
                                    }
                                } else {
                                    $workflow->status = $data->status;
                                    $workflow->creditCtrl = 0;
                                    $credit = 'Entry processed successfully.';
                                }
                            } else {
                                $workflow->status = $data->status;
                                $workflow->creditCtrl = 1;
                                $credit = $credit . ' Customer Code Missing.';
                            }
                        } else {
                            $workflow->status = $data->status;
                            $workflow->creditCtrl = 0;
                            $credit = 'Entry processed successfully.';
                        }
                    }

                    $workflow->workflow_id = $data->workflow_id;
                    
                    $workflow->orderNo = $data->orderNo;
                    if ($workflow->status != 27) {
                        if ($workflow->update(7)) {
                            $delivery->delivery_status = $workflow->status;
                            $delivery->workflow_id = $data->workflow_id;
                            if ($delivery->updateStatus()) {
                                $history->workflow_id = $data->workflow_id;
                                $history->step = $workflow->status;
                                $history->note = $data->orderNo;
                                $history->comment = $data->note;
                                if ($history->insertHistory()) {
                                    $details->workflow_id = $data->workflow_id;
                                    if ($details->parseUpdate($data)) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        $response = array(
                                            'status' => 'success',
                                            'message' => $credit
                                        );
                                        echo json_encode($response);
                                    }
                                } else {
                                    echo json_encode(
                                        array(
                                            'status' => 'error',
                                            'message' => 'Unable to update entry.'
                                        )
                                    );
                                }
                            }
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        if ($workflow->update(7)) {
                            $delivery->delivery_status = $workflow->status;
                            $delivery->workflow_id = $data->workflow_id;
                            if ($delivery->updateStatus()) {
                                $history->workflow_id = $data->workflow_id;
                                $history->step = $workflow->status;
                                $history->note = $data->orderNo;
                                $history->comment = $data->note;
                                if ($history->insertHistory()) {
                                    $details->workflow_id = $data->workflow_id;
                                    if ($details->parseUpdate($data)) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry updated successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to update entry. No products found.'
                                            )
                                        );
                                    }
                                } else {
                                    echo json_encode(
                                        array(
                                            'status' => 'error',
                                            'message' => 'Unable to insert history.'
                                        )
                                    );
                                }
                            } else {
                                echo json_encode(
                                    array(
                                        'status' => 'error',
                                        'message' => 'Unable to update delivery status.'
                                    )
                                );
                            }
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update workflow.'
                                )
                            );
                        }
                    }
                    break;
                case 4:
                    $workflow->workflow_id = $data->workflow_id;
                    $workflow->status = $data->status;
                    $workflow->invoiceNo = $data->invoiceNo;
                    if ($workflow->update(1)) {
                        $history->workflow_id = $data->workflow_id;
                        $history->step = $data->status;
                        $history->note = $data->invoiceNo;
                        $history->comment = $data->note;
                        if ($history->insertHistory()) {
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Entry created successfully.'
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
                case 5:
                    $workflow->workflow_id = $data->workflow_id;
                    $workflow->status = $data->status;
                    if ($workflow->update(4)) {
                        $history->workflow_id = $data->workflow_id;
                        $history->step = $data->status;
                        $history->comment = $data->note;
                        if ($history->insertHistory()) {
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Entry created successfully.'
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
                case 6:
                    $workflow->workflow_id = $data->workflow_id;
                    $workflow->status = $data->status;
                    $workflow->invoiceNo = $data->invoiceNo;
                    if ($workflow->update(1)) {
                        $delivery->invoice_no = $data->invoiceNo;
                        $delivery->workflow_id = $data->workflow_id;
                        if ($delivery->updateInvoice()) {
                            $history->workflow_id = $data->workflow_id;
                            $history->step = $data->status;
                            $history->note = $data->invoiceNo;
                            $history->comment = $data->note;
                            if ($history->insertHistory()) {
                                $details->workflow_id = $data->workflow_id;
                                if ($details->parseUpdate($data)) {
                                    echo json_encode(
                                        array(
                                            'status' => 'success',
                                            'message' => 'Entry created successfully.'
                                        )
                                    );
                                }
                            } else {
                                echo json_encode(
                                    array(
                                        'status' => 'error',
                                        'message' => 'Unable to update entry.'
                                    )
                                );
                            }
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
                case 7:
                    $workflow->workflow_id = $data->workflow_id;
                    $workflow->status = $data->status;
                    $workflow->creditNo = $data->note;
                    if ($workflow->update(2)) {
                        $delivery->credit_no = $data->note;
                        $delivery->delivery_status = $data->status;
                        $delivery->workflow_id = $data->workflow_id;
                        if ($delivery->updateCreditNote()) {
                            $history->workflow_id = $data->workflow_id;
                            $history->step = $data->status;
                            $history->note = $data->note;
                            $history->comment = $data->comment;
                            if ($history->insertHistory()) {
                                echo json_encode(
                                    array(
                                        'status' => 'success',
                                        'message' => 'Entry created successfully.'
                                    )
                                );
                            } else {
                                echo json_encode(
                                    array(
                                        'status' => 'error',
                                        'message' => 'Unable to update entry.'
                                    )
                                );
                            }
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
                case 8:
                    if (!isset($data->session)) {
                        $session->vehicle = $data->vehicle;

                        switch (+$data->status) {
                            case 9:
                                if ($session->isThereSession() === 0) {
                                    if ($session->createSession()) {
                                        $lastId = $db->lastInsertId(); // fetch last inserted ID; after success.
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Session cannot be created at the moment.'
                                            )
                                        );
                                    }
                                } else {
                                    $lastId = $session->sessionNumber();
                                }
                                break;
                            case 17:
                                if ($session->isThereSessionPickup() === 0) {
                                    if ($session->createPickup()) {
                                        $lastId = $db->lastInsertId(); // fetch last inserted ID; after success.
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Session cannot be created at the moment.'
                                            )
                                        );
                                    }
                                } else {
                                    $lastId = $session->pickupsessionNumber();
                                } 
                                break;
                        }
                        
                        if (!is_array($data->workflow_id)) {
                            $workflow->workflow_id = $data->workflow_id;
                            $workflow->vehicleNo = $data->vehicle;
                            $workflow->sessionID = $lastId;
                            $workflow->status = $data->status;
                            if ($workflow->update(3)) {
                                $delivery->vehicle = $data->vehicle;
                                $delivery->delivery_status = $data->status;
                                $delivery->jobID = $lastId;
                                if ($delivery->updateOnSession($workflow->workflow_id)) {
                                    $history->workflow_id = $workflow->workflow_id;
                                    $history->note = $data->vehicle;
                                    $history->step = $data->status;
                                    if ($history->insertHistory()) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to update entry.'
                                            )
                                        );
                                    }
                                }
                            }
                        } else {
                            $count = 0;

                            foreach($data->workflow_id as $item => $wfid) {
                                $workflow->workflow_id = $wfid;
                                $workflow->vehicleNo = $data->vehicle;
                                $workflow->sessionID = $lastId;
                                $workflow->status = $data->status;
                                if ($workflow->update(3)) {
                                    $delivery->vehicle = $data->vehicle;
                                    $delivery->delivery_status = $data->status;
                                    $delivery->jobID = $lastId;
                                    if ($delivery->updateOnSession($workflow->workflow_id)) {
                                        $history->workflow_id = $workflow->workflow_id;
                                        $history->note = $data->vehicle;
                                        $history->step = $data->status;
                                        if ($history->insertHistory()) {
                                            $count++;
                                        } else {
                                           //
                                        }
                                    }
                                }
                            }

                            if ($count === sizeof($data->workflow_id)) {
                                echo json_encode(
                                    array(
                                        'status' => 'success',
                                        'message' => 'Entry created successfully.'
                                    )
                                );
                            } else {
                                echo json_encode(
                                    array(
                                        'status' => 'error',
                                        'message' => 'Unable to update entry.'
                                    )
                                );
                            }

                        }
                    } else {
                        $session->vehicle = $data->vehicle;
                        if ($session->isThereSession() > 0) {
                            $session->sessionID = $data->session;

                            $workflow->workflow_id = $data->workflow_id;
                            $workflow->vehicleNo = $data->vehicle;
                            $workflow->sessionID = $session->sessionID;
                            $workflow->status = $data->status;
                            if ($workflow->update(3)) {
                                $delivery->vehicle = $data->vehicle;
                                $delivery->delivery_status = $data->status;
                                $delivery->jobID = $session->sessionID;
                                if ($delivery->updateOnSession($workflow->workflow_id)) {
                                    $history->workflow_id = $workflow->workflow_id;
                                    $history->note = $data->vehicle;
                                    $history->step = $data->status;
                                    if ($history->insertHistory()) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to update entry.'
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 9:
                    if ($data->status == 'full') {
                        $data->status = 17;
                        $history->step = 17;
                    } else if ($data->status == 'undelivered') {
                        $data->status = 7;
                        $history->step = 12;
                        $session->sessionID = 0;
                    } else if ($data->status == 'partialRoll') {
                        $data->status = 7;
                        $history->step = 10;
                    } else if ($data->status == 'partialCredit') {
                        $data->status = 11;
                        $history->step = 10;
                    } else if ($data->status == 'allReturn') {
                        $data->status = 11;
                        $history->step = 13;
                    } else if ($data->status == 'back') {
                        $data->status = 7;
                        $history->step = 15;
                        $session->sessionID = 0;
                    } else if ($data->status == 'pickup') {
                        $data->status = 17;
                        $history->step = 18;

                        if ($session->isThereSessionPickup() > 0) {
                            $session->sessionID = $session->pickupsessionNumber();
                        } else {
                            if ($session->createPickup()) {
                                $session->sessionID = $db->lastInsertId();
                            }
                        }
                    }

                    if (is_array($data->workflow_id)) {
                        $count = 0;
                        foreach($data->workflow_id as $item => $wfid) {
                            $workflow->workflow_id = $wfid;
                            $workflow->status = $data->status;
                            if ($workflow->update(4)) {
                                $delivery->vehicle = $data->vehicle;
                                $delivery->delivery_status = $data->status;
                                if ($history->step === 15 || $history->step === 12) {
                                    $delivery->jobID = $session->sessionID;
                                    if ($delivery->updateOnSession($workflow->workflow_id)) {
                                        $history->workflow_id = $workflow->workflow_id;
                                        $history->note = $data->vehicle;
                                        if ($history->insertHistory()) {
                                            $count++;
                                        }
                                    }
                                } else {
                                    $delivery->delivery_status = $data->status;
                                    $delivery->workflow_id = $wfid;
                                    if ($delivery->updateStatus()) {
                                        $history->workflow_id = $workflow->workflow_id;
                                        $history->note = $data->vehicle;
                                        if ($history->insertHistory()) {
                                            $count++;
                                        }
                                    }
                                }
                            }
                        }
                        if ($count === sizeof($data->workflow_id)) {
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Entry created successfully.'
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        $workflow->workflow_id = $data->workflow_id;
                        $workflow->status = $data->status;
                        if ($workflow->update(4)) {
                            $delivery->vehicle = $data->vehicle;
                            $delivery->delivery_status = $data->status;
                            if ($history->step === 15 || $history->step === 12) {
                                $delivery->jobID = $session->sessionID;
                                if ($delivery->updateOnSession($workflow->workflow_id)) {
                                    $history->workflow_id = $workflow->workflow_id;
                                    $history->note = $data->vehicle;
                                    if ($history->insertHistory()) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to update entry.'
                                            )
                                        );
                                    }
                                }
                            } else {
                                $delivery->delivery_status = $data->status;
                                $delivery->workflow_id = $data->workflow_id;
                                if ($delivery->updateStatus()) {
                                    $history->workflow_id = $workflow->workflow_id;
                                    $history->note = $data->vehicle;
                                    if ($history->insertHistory()) {
                                        echo json_encode(
                                            array(
                                                'status' => 'success',
                                                'message' => 'Entry created successfully.'
                                            )
                                        );
                                    } else {
                                        echo json_encode(
                                            array(
                                                'status' => 'error',
                                                'message' => 'Unable to update entry.'
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    }

                    if ($data->status != 'pickup') {
                        if (isset($data->jobID)) {
                            $session->sessionID = $data->jobID;
                            // $session->autoArchiveSession();
                        }
                    }
                    break;
                case 16:
                    $history->workflow_id = $data->workflow_id;
                    $history->step = 16;
                    if ($history->insertHistory()) {
                        $details->workflow_id = $data->workflow_id;

                        if ($details->parseUpdate($data)) {
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Entry created successfully.'
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    'status' => 'error',
                                    'message' => 'Unable to update entry.'
                                )
                            );
                        }
                    } else {
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Unable to update entry.'
                            )
                        );
                    }
                    break;
            }
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }

?>