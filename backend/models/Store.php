<?php

class Store {
    private $id, $name, $created_at;

    public function __construct($id, $name, $created_at = null) {
        $this->set_id($id);
        $this->set_name($name);
        $this->created_at = $created_at;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_name($name) {
        $this->name = trim($name);
    }

    public function get_name() {
        return $this->name;
    }

    public function get_created_at() {
        return $this->created_at;
    }
}

function list_stores() {
    global $database;

    $query = "SELECT * FROM stores ORDER BY created_at DESC";
    $statement = $database->prepare($query);
    $statement->execute();

    $stores = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    $store_array = [];

    foreach ($stores as $store) {
        $store_array[] = [
            "id" => $store['id'],
            "name" => $store['name'],
            "created_at" => $store['created_at']
        ];
    }

    return $store_array;
}

function delete_store($id) {
    global $database;

    $query = "DELETE FROM stores WHERE id = :id";
    $statement = $database->prepare($query);
    $statement->bindValue(":id", $id);
    $statement->execute();
    $statement->closeCursor();
}

function insert_store($store) {
    global $database;

    $query = "INSERT INTO stores (name) VALUES (:name)";
    $statement = $database->prepare($query);
    $statement->bindValue(":name", $store->get_name());
    $statement->execute();
    $statement->closeCursor();
}