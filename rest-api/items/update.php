<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));
if (
    !empty($data->firstname) && !empty($data->lastname) &&
    !empty($data->email)
) {
    $list = array(
        ['Firstname', 'Lastname', 'Email'],
        [$data->firstname, $data->lastname, $data->email]
    );

    $fp = fopen('customer-data.csv', 'w');
    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }
    try {
        fclose($fp);
        http_response_code(201);
        echo json_encode(array("message" => "Customer data updated succesfully.", "status" => "true"));
    } catch (Exception $e) {
        http_response_code(503);
        echo json_encode(array("message" => "Something went wrong while updating customer data.", "status" => "false"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Customer data is incomplete to update the record.", "status" => "false"));
}
