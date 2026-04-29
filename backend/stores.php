<?php
require_once "database.php";
require_once "models/Store.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$action = $_SERVER['REQUEST_METHOD'];

if ($action === 'GET') {
    echo json_encode(list_stores());
    exit;
}

if ($action === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || trim($data['name']) === '') {
        http_response_code(400);
        echo json_encode(["error" => "Store name required"]);
        exit;
    }

    $store = new Store(null, $data['name']);
    insert_store($store);

    echo json_encode(["success" => true]);
    exit;
}

if ($action === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Store ID required"]);
        exit;
    }

    delete_store($_GET['id']);

    echo json_encode(["success" => true]);
    exit;
}
