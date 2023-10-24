<?php
require_once __DIR__ . "/src/controller/sql.php";

// set headers
// header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

var_dump($_GET);
// GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (sizeof($_GET) == 0) {
        // READ
        echo json_encode(read());
    } elseif (isset($_GET["operation"])) {
        // READ
        if ($_GET["operation"] == "getUser")
            echo json_encode(getUser($_GET["id"]));

        // DELETE
        elseif ($_GET["operation"] == "delete_User")
            echo json_encode(delete_User($_GET["id"]));

        // SESSION
        elseif ($_GET["operation"] == "log_Out")
            echo json_encode(log_Out());
    }
}

// POST
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["operation"])) {
        // CREATE
        if ($_POST["operation"] == "create_User")
            echo json_encode(create_User($_POST["email"], $_POST["password"], $_POST["name"], $_POST["surname"]));

        // UPDATE
        elseif ($_POST["operation"] == "update_User")
            echo json_encode(update_User($_POST["id"], $_POST["email"], $_POST["password"], $_POST["name"], $_POST["surname"]));

        // SESSION
        elseif ($_POST["operation"] == "login")
            echo json_encode(login($_POST["email"], $_POST["password"], $_POST["remember"]));
    }
}
