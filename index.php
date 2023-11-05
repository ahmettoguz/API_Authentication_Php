<?php
require_once __DIR__ . "/src/controller/sql.php";

// set headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// sha 256
$inputString = "123456";
$sha256Hash = hash('sha256', $inputString);
echo "SHA-256 Hash: " . $sha256Hash;
// sha 256

// base64
$data = "123456";
$encodedData = base64_encode($data);
echo "Base64 Encoded Data: " . $encodedData;
$decodedData = base64_decode($encodedData);
echo "Base64 Decoded Data: " . $decodedData;
// base64

exit;

// GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $op = $_GET["operation"] ?? null;

    switch ($op) {
        case 'getAccount':
            echo json_encode(getAccount($_GET));
            break;
        case 'getAccounts':
            echo json_encode(getAccounts());
            break;

        default:
            echo json_encode(getNotFound());
            break;
    }
}

// POST
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // get input as json and not form data
    $jsonData = file_get_contents('php://input');
    // parse the URL-encoded data
    parse_str($jsonData, $data);
    $_POST = $data;


    $op = $_POST["operation"] ?? null;

    switch ($op) {
        case 'createAccount':
            echo json_encode(createAccount($_POST));
            break;
        case 'updateAccount':
            echo json_encode(updateAccount($_POST));
            break;
        case 'deleteAccount':
            echo json_encode(deleteAccount($_POST));
            break;
        case 'login':
            echo json_encode(login($_POST["username"], $_POST["password"]));
            break;
        case 'logout':
            echo json_encode(logout());
            break;

        default:
            echo json_encode(getNotFound());
            break;
    }
}
