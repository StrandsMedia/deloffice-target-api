<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);

    $customer->cust_id = isset($_GET['id']) ? $_GET['id'] : die();

    $customer->readOne();

    $contact_arr = array(
        0 => array(
            'id' => 1,
            'contact_person' => $customer->contact_person,
            'tel' => $customer->tel,
            'fax' => $customer->fax,
            'mob' => $customer->mob,
            'email' => $customer->email,
        ),
        1 => array(
            'id' => 2,
            'contact_person' => $customer->contact_person2,
            'tel' => $customer->tel2,
            'fax' => $customer->fax2,
            'mob' => $customer->mob2,
            'email' => $customer->email2,
        ),
        2 => array(
            'id' => 3,
            'contact_person' => $customer->contact_person3,
            'tel' => $customer->tel3,
            'fax' => $customer->fax3,
            'mob' => $customer->mob3,
            'email' => $customer->email3,
        ),
        3 => array(
            'id' => 4,
            'contact_person' => $customer->contact_person_acc,
            'tel' => $customer->tel_acc,
            'fax' => $customer->fax_acc,
            'mob' => $customer->mob_acc,
            'email' => $customer->email_acc,
        )
    );

    $customer_arr = array(
        'cust_id' => $customer->cust_id,
        'company_name' => $customer->company_name,
        'customerCode' => $customer->customerCode,
        'category' => $customer->category,
        'address' => $customer->address,
        'address2' => $customer->address2,
        'address3' => $customer->address3,
        'location' => $customer->location,
        'location2' => $customer->location2,
        'location3' => $customer->location3,
        'notes' => html_entity_decode($customer->notes),
        'comment' => html_entity_decode($customer->comment),
        'contact_details' => $contact_arr,
        'sector' => $customer->sector,
        'sector_name' => $customer->sector_name,
        'subsector' => $customer->subsector,
        'subsector_name' => $customer->subsector_name,
        'createdAt' => $customer->createdAt,
        'updatedAt' => $customer->updatedAt,
    );

    print_r(json_encode($customer_arr));

?>