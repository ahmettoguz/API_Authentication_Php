<?php
require_once __DIR__ . "/../database/dbConnection.php";
require_once __DIR__ . "/auth.php";

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

    // generate token
    $token = generateToken($lastInsertedId, $username);

    // set response header 
    header("Authorization: Bearer $token");
    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "Account created successfully",
        "data" => ["insertedId" => $lastInsertedId]
    ];
    return $response;
}

//---------------------- - READ - ----------------------
function getAccounts()
{
    if (checkToken() !== true)
        exitByInvalidToken();

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
        "message" => "Get all accounts successfully",
        "data" => ["accounts" => $rows]
    ];
    return $response;
}

function getAccount($payload)
{
    if (checkToken() !== true)
        exitByInvalidToken();

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
        "message" => "Get specific account successfully",
        "data" => ["account" => $user]
    ];
    return $response;
}


//---------------------- - UPDATE -  ----------------------
function updateAccount($payload)
{
    if (checkToken() !== true)
        exitByInvalidToken();

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

    $account = getAccount($payload)["data"]["account"];

    if ($account == false) {
        http_response_code(400);
        $response = [
            "status" => 400,
            "state" => false,
            "message" => "There is no account with id $id",
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
        "message" => "Account updated successfully",
        "data" => ["updatedId" => $id]
    ];
    return $response;
}

//---------------------- - DELETE -  ----------------------
function deleteAccount($payload)
{
    if (checkToken() !== true)
        exitByInvalidToken();

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

    $account = getAccount($payload)["data"]["account"];

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
        "message" => "Account deleted successfully",
        "data" => ["deletedAccount" => $account]
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
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Unauthorized."
        ];
        return $response;
    }

    // generate token
    $token = generateToken($row["id"], $row["username"]);

    // Bu api'da cookiler kullanmamaktadır. JWT Header ile gönderilmekte ve header ile alınmaktadır.
    //set cookie
    setcookie("AccessToken", $token, time() + 60 * 60 * 24 * 1, "/", "", false, false);
    // true as the 6th parameter makes the cookie secure, ensuring that it is only transmitted over HTTPS connections.
    // true as the 7th parameter sets the HttpOnly flag, which prevents the cookie from being accessed by JavaScript.

    // set response header 
    header("Authorization: Bearer $token");
    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "Login operation is successfull.",
        "data" => [
            "account" => $row,
            "accessToken" => "Authorization: Bearer " . $token
        ],
    ];

    return $response;
}

//---------------------- - LOGOUT -  ----------------------
function logout()
{
    // for php remove cookie data
    unset($_COOKIE["AccessToken"]);

    // unset cookie from browser
    setcookie('AccessToken', '', time() - 3600, '/');

    $response = [
        "status" => 200,
        "state" => true,
        "message" => "Logout successfully done.",
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
