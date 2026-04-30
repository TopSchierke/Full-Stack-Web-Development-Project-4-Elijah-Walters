<?php
//This file handles all the routing to the endpoints
file_put_contents("debug.log", $_SERVER["REQUEST_URI"]);

require_once "../database.php";
require_once "../repositories/storeRepo.php";
require_once "../repositories/itemRepo.php";
require_once "../models/Store.php";
require_once "../models/Item.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$method = $_SERVER["REQUEST_METHOD"];
$route  = $_GET["route"] ?? null;
$id     = $_GET["id"] ?? null;


if ($route === "stores") {//get post and delete for stores

    if ($method === "GET" && !$id) {
        echo json_encode(get_stores());
        exit;
    }

    if ($method === "POST" && !$id) {
        $data = json_decode(file_get_contents("php://input"), true);

        $store = new Store(null, $data["name"]);
        insert_store($store);

        echo json_encode(["success" => true]);
        exit;
    }

    if ($method === "DELETE" && $id) {
        delete_store($id);
        echo json_encode(["success" => true]);
        exit;
    }
}


if ($route === "items") {//get post put and delete for items. Also includes a route for getting all items from a store

    if ($method === "GET") {

        $storeId = $_GET["store_id"] ?? null;

        if ($storeId) {
            echo json_encode(get_items_by_store($storeId));
        } else {
            echo json_encode(get_items());
        }

        exit;
    }

    if ($method === "POST") {

        $storeId = $_GET["store_id"] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);

        $item = new Item(
            null,
            $storeId,
            $data["name"],
            $data["quantity"] ?? 1,
            0
        );

        insert_item($item);

        echo json_encode(["success" => true]);
        exit;
    }

    if ($method === "PUT" && $id) {

        $data = json_decode(file_get_contents("php://input"), true);

        $item = new Item(
            $id,
            $data["store_id"] ?? null,
            $data["name"],
            $data["quantity"],
            $data["checked"]
        );

        update_item($item);

        echo json_encode(["success" => true]);
        exit;
    }

    if ($method === "DELETE" && $id) {
        delete_item($id);
        echo json_encode(["success" => true]);
        exit;
    }
}
