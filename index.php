<?php
require_once __DIR__ . "/src/controller/sql.php";

// set headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// var_dump($_POST);
// GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $op = $_GET["operation"] ?? null;

    if ($op == null) {
        echo json_encode(getNotFound());
    } else {
        if ($op = "getAccounts")
            echo json_encode(getAccounts());

        elseif ($op == "getAccount")
            echo json_encode(getAccount($_GET));
    }
}

// POST
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    $op = $_POST["operation"] ?? null;

    if ($op == null) {
        echo json_encode(getNotFound());
    } else {
        // CREATE
        if ($op == "createAccount")
            echo json_encode(createAccount($_POST));

        // UPDATE
        elseif ($op == "updateAccount")
            echo json_encode(updateAccount($_POST));

        // DELETE
        elseif ($op == "deleteAccount")
            echo json_encode(deleteAccount($_POST));

        // LOGIN
        elseif ($op == "login")
            echo json_encode(login($_POST["username"], $_POST["password"]));
    }
}
