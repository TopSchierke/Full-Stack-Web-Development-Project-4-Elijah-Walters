<?php
require_once "database.php";
require_once "models/Item.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$action = $_SERVER["REQUEST_METHOD"];

if ($action === "GET") {
    echo json_encode(list_items());
    exit;
}

if ($action === "POST") {
    if (!isset($_GET["store_id"])) {
        http_response_code(400);
        echo json_encode(["error" => "store_id required"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"]) || trim($data["name"]) === "") {
        http_response_code(400);
        echo json_encode(["error" => "Item name required"]);
        exit;
    }

    $quantity = isset($data["quantity"]) ? (int)$data["quantity"] : 1;

    if ($quantity <= 0) {
        $quantity = 1;
    }

    $item = new Item(
        null,
        $_GET["store_id"],
        $data["name"],
        $quantity
    );

    insert_item($item);

    echo json_encode(["success" => true]);
    exit;
}

if ($action === "PUT") {
    if (!isset($_GET["id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Item id required"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $item = new Item(
        $_GET["id"],
        $data["store_id"] ?? null,
        $data["name"] ?? "",
        $data["quantity"] ?? 1,
        $data["checked"] ?? 0
    );

    update_item($item);

    echo json_encode(["success" => true]);
    exit;
}

if ($action === "DELETE") {
    if (!isset($_GET["id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Item id required"]);
        exit;
    }

    delete_item($_GET["id"]);

    echo json_encode(["success" => true]);
    exit;
}
