<?php

require_once "../database.php";
require_once "../models/Item.php";


function get_items() {
    global $database;//uses database
    //query gets all items pulls store name as store_name, connects items to stores, 
    //groups them alphabetically and sorts newest first
    $query = "
        SELECT items.*, stores.name AS store_name
        FROM items
        JOIN stores ON items.store_id = stores.id
        ORDER BY stores.name ASC, items.created_at DESC
    ";

    $statement = $database->prepare($query);//prepares runs and closes query
    $statement->execute();

    $items = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    $item_array = [];

    foreach ($items as $item) {//saves all the items to an array
        $item_array[] = [
            "id" => $item["id"],
            "store_id" => $item["store_id"],
            "name" => $item["name"],
            "quantity" => $item["quantity"],
            "checked" => $item["checked"],
            "created_at" => $item["created_at"],
            "store_name" => $item["store_name"]
        ];
    }

    return $item_array;//returns that array
}

function get_items_by_store($storeId) {//gets items from a specific store
    global $database;

    $query = "
        SELECT items.*, stores.name AS store_name
        FROM items
        JOIN stores ON items.store_id = stores.id
        WHERE items.store_id = :store_id
        ORDER BY items.created_at DESC
    ";

    $statement = $database->prepare($query);//creates prepares and executes the query
    $statement->bindValue(":store_id", $storeId);
    $statement->execute();

    $items = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    $item_array = [];

    foreach ($items as $item) {//puts all of them into an array and returns it
        $item_array[] = [
            "id" => $item["id"],
            "store_id" => $item["store_id"],
            "name" => $item["name"],
            "quantity" => $item["quantity"],
            "checked" => $item["checked"],
            "created_at" => $item["created_at"],
            "store_name" => $item["store_name"]
        ];
    }

    return $item_array;
}

function insert_item($item) {//takes an item object and posts it to database
    global $database;
    //inserts store_id name and quantity into database, db handles item id and checked status
    $query = "
        INSERT INTO items (store_id, name, quantity)
        VALUES (:store_id, :name, :quantity)
    ";

    $statement = $database->prepare($query);//prepares runs and closes query
    $statement->bindValue(":store_id", $item->get_store_id());
    $statement->bindValue(":name", $item->get_name());
    $statement->bindValue(":quantity", $item->get_quantity());

    $statement->execute();
    $statement->closeCursor();
}

function update_item($item) {//puts item updates into the databbase
    global $database;

    //sets each field of an item to the updated data
    $query = "
        UPDATE items
        SET name = :name,
            quantity = :quantity,
            checked = :checked,
            store_id = :store_id
        WHERE id = :id
    ";

    $statement = $database->prepare($query);//prepares runs and closes query
    $statement->bindValue(":id", $item->get_id());
    $statement->bindValue(":name", $item->get_name());
    $statement->bindValue(":quantity", $item->get_quantity());
    $statement->bindValue(":checked", $item->get_checked());
    $statement->bindValue(":store_id", $item->get_store_id());

    $statement->execute();
    $statement->closeCursor();
}

function delete_item($id) {
    global $database;

    $query = "DELETE FROM items WHERE id = :id";

    $statement = $database->prepare($query);
    $statement->bindValue(":id", $id);

    $statement->execute();
    $statement->closeCursor();
}
