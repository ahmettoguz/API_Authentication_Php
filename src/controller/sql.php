<?php
require_once __DIR__ . "/../database/dbConnection.php";

//---------------------- - CREATE - ----------------------
function createAccount($payload)
{
    $username = $payload["username"] ?? null;
    $password = $payload["password"] ?? null;

    if ($username == null || $password == null) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "Payload error"
        ];
        return $response;
    }

    // html, js injection preventation
    $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    global $db;

    try {
        $sql = "insert into account (username, password) values (:username, :password)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $lastInsertedId = $db->lastInsertId();
    } catch (PDOException $ex) {
        http_response_code(500);
        $response = [
            "status" => 500,
            "state" => false,
            "message" => $ex,
        ];
        return $response;
    }

    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "account created",
        "data" => ["insertedId" => $lastInsertedId]
    ];
    return $response;
}

//---------------------- - READ - ----------------------
function getAccounts()
{
    global $db;

    try {
        $sql = "select * from account";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $stmt->rowCount();
    } catch (Exception $ex) {
        http_response_code(500);
        $response = [
            "status" => 500,
            "state" => false,
            "message" => $ex,
        ];
        return $response;
    }

    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "account created",
        "data" => ["accounts" => $rows]
    ];
    return $response;
}

function getAccount($payload)
{
    $id = $payload["id"] ?? null;

    if ($id == null) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "Payload error"
        ];
        return $response;
    }

    global $db;

    try {
        $sql = "select id, username, password
        from account
        where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // $stmt->rowCount();
    } catch (Exception $ex) {
        http_response_code(500);
        $response = [
            "status" => 500,
            "state" => false,
            "message" => $ex,
        ];
        return $response;
    }
    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "account created",
        "data" => ["account" => $user]
    ];
    return $response;
}

//---------------------- - DELETE -  ----------------------
function deleteAccount($payload)
{
    $id = $payload["id"] ?? null;

    if ($id == null) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "Payload error"
        ];
        return $response;
    }

    global $db;

    $account = getAccount(["id" => $id])["data"]["account"];

    if ($account == false) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "There is no account with id $id",
        ];
        return $response;
    }

    try {
        $sql = "delete from account where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        // $deletedRowCount = $stmt->rowCount();
    } catch (PDOException $ex) {
        http_response_code(500);
        $response = [
            "status" => 500,
            "state" => false,
            "message" => $ex,
        ];
        return $response;
    }

    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "account deleted",
        "data" => ["deletedAccount" => $account]
    ];
    return $response;
}

//---------------------- - UPDATE -  ----------------------
function updateAccount($payload)
{
    $id = $payload["id"] ?? null;
    $username = $payload["username"] ?? null;
    $password = $payload["password"] ?? null;

    if ($id == null || $username == null || $password == null) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "Payload error"
        ];
        return $response;
    }

    // html, js injection preventation
    $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    global $db;

    try {
        $sql = "update account
                set username = :username, password = :password
                where id = :id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        $stmt->execute();
        // $updatedRowCount = $stmt->rowCount();
    } catch (PDOException $ex) {
        http_response_code(500);
        $response = [
            "status" => 500,
            "state" => false,
            "message" => $ex,
        ];
        return $response;
    }

    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "account updated",
        "data" => ["updatedId" => $id]
    ];
    return $response;
}

//---------------------- - 404 -  ----------------------
function getNotFound()
{
    http_response_code(404);
    $response = [
        "status" => 404,
        "state" => false,
        "message" => "Payload error"
    ];
    return $response;
}




//---------------------- - LOGIN -  ----------------------
function login($username, $password)
{
    global $db;

    // $password = sha1($password . "SOCI");


    try {
        $sql = "select id, username from account where username = :username and password = :password";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        die("Query Error : " . $ex->getMessage());
    }

    if ($row == false) {
        http_response_code(401);
        return false;
    }

    $tokenn = "q2e";

    http_response_code(200);
    header("Authorization: Bearer $tokenn");
    return $row;
}

//---------------------- - LOGOUT -  ----------------------
function log_Out()
{
    $_SESSION = [];

    // delete cookie
    setcookie(session_name(), "", 1, "/"); // delete memory cookie 

    // delete session file from tmp
    session_destroy();

    // header("Location:http://localhost/AhmetOguzErgin/Web/project_manager/");

    return true;
}
