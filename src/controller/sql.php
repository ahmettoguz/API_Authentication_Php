<?php
require_once __DIR__ . "/../database/dbConnection.php";

//---------------------- - CREATE - ----------------------
function createAccount($username, $password)
{
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
        die("<p>Insert Error : " . $ex->getMessage());
        return false;
    }

    return $lastInsertedId;
}

//---------------------- - READ - ----------------------
function read()
{
    global $db;

    try {
        $sql = "select * from account";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $stmt->rowCount();
    } catch (Exception $ex) {
        die("Query Error : " . $ex->getMessage());
        return false;
    }

    return $rows;
}

function getAccount($id)
{
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
        die("Query Error : " . $ex->getMessage());
        return false;
    }

    return $user;
}

//---------------------- - DELETE -  ----------------------
function deleteAccount($id)
{
    global $db;

    try {
        $sql = "delete from account where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        // $deletedRowCount = $stmt->rowCount();
    } catch (PDOException $ex) {
        return false;
        die("<p>Update Error : " . $ex->getMessage());
    }

    return true;
}

//---------------------- - UPDATE -  ----------------------
function updateAccount($id, $username, $password)
{
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
        die("Insert Error : " . $ex->getMessage());
        return false;
    }

    return true;
}







function login($email, $password, $remember)
{
    global $db;

    $password = sha1($password . "SOCI");

    try {
        $sql = "select email, name, surname from user where email = :email and password = :password";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        die("Query Error : " . $ex->getMessage());
    }

    if ($row == false)
        return false;
    else {
        // correct login, form session
        $_SESSION["user"] = $row;
        if ($remember == "true") {
            setcookie(session_name(), session_id(), time() + 60 * 60 * 24 * 7, "/");
        }

        return true;
    }
}

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