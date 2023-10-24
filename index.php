<?php
require_once __DIR__ . "/src/controller/sql.php";

// set headers
// header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

var_dump($_POST);
// GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (sizeof($_GET) == 0) {
        // READ
        echo json_encode(read());
    } elseif (isset($_GET["operation"])) {
        // READ
        if ($_GET["operation"] == "getAccount")
            echo json_encode(getAccount($_GET["id"]));
    }
}

// POST
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["operation"])) {

        // CREATE
        if ($_POST["operation"] == "createAccount")
            echo json_encode(createAccount($_POST["username"], $_POST["password"]));

        // UPDATE
        elseif ($_POST["operation"] == "updateAccount")
            echo json_encode(updateAccount($_POST["id"], $_POST["username"], $_POST["password"]));

        // DELETE
        elseif ($_POST["operation"] == "deleteAccount")
            echo json_encode(deleteAccount($_POST["id"]));
    }
}


