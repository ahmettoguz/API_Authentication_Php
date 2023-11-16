<?php
//---------------------- - Authentication Token Functions -  ----------------------
function AesEncrypt($data, $key)
{
    // symmetrical encryption
    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $key,  0, $iv);
    return base64_encode($iv . $encrypted);
}

function AesDecrypt($data, $key)
{
    $cipher = "aes-256-cbc";
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $data = substr($data, $ivlen);
    return openssl_decrypt($data, $cipher, $key, 0, $iv);
}

function generateToken($id, $name)
{
    // base64 header and body encode with base64
    // $decodedData = base64_decode($encodedData);
    $jwtHeader = [
        "alg" => "SHA256",
        "typ" => "JWT"
    ];
    $jwtHeaderJson = json_encode($jwtHeader);
    $jwtHeaderEncoded = base64_encode($jwtHeaderJson);

    $jwtBody = [
        "id" => $id,
        "name" => $name,
        "iat" => time(),
        "exp" => time() + 60 * 60 * 24 // Expiration time 1 day
    ];
    $jwtBodyJson = json_encode($jwtBody);
    $jwtBodyJsonEncrypted = AesEncrypt($jwtBodyJson, "key");
    $jwtBodyFinal = base64_encode($jwtBodyJsonEncrypted);

    // sha 256 signiture
    $signiture = getSigniture($jwtHeaderEncoded, $jwtBodyFinal);

    $token = $jwtHeaderEncoded . "." . $jwtBodyFinal . "." . $signiture;

    return $token;
}

function getSigniture($jwtHeaderEncoded, $jwtBodyFinal)
{
    $secretKey = "secret";
    $jwtHeaders = $jwtHeaderEncoded . $jwtBodyFinal . $secretKey;

    $signiture = hash('sha256', $jwtHeaders);
    $signitureEncoded = base64_encode($signiture);

    return $signitureEncoded;
}

function checkToken()
{
    if (!isset($_SERVER['HTTP_ACCESSTOKEN'])) {
        http_response_code(401);
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Unauthorized."
        ];
        return $response;
    }

    $jwtToken = $_SERVER['HTTP_ACCESSTOKEN'];

    // Remove the "Bearer " prefix
    $jwtToken = str_replace('Bearer ', '', $jwtToken);

    // Split the JWT token by periods
    $tokenParts = explode(".", $jwtToken);

    // check parts
    if (count($tokenParts) !== 3) {
        http_response_code(401);
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Unauthorized."
        ];
        return $response;
    }

    // Decode the base64-encoded payload 
    $payload = base64_decode($tokenParts[1]);
    $payload = AesDecrypt($payload, "key");
    $payloadData = json_decode($payload, true);

    // if token is expired
    if (time() > $payloadData["exp"]) {
        http_response_code(401);
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Unauthorized."
        ];
        return $response;
    }

    // check the integrity
    $signiture = getSigniture($tokenParts[0], $tokenParts[1]);
    if ($tokenParts[2] !== $signiture) {
        http_response_code(401);
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Integrity of the token is not valid."
        ];
        return $response;
    }

    // return true
    return true;
}

function getTokenData()
{
    if (checkToken() !== true)
        exitByInvalidToken();

    $jwtToken = $_SERVER['HTTP_ACCESSTOKEN'];

    // Remove the "Bearer " prefix
    $jwtToken = str_replace('Bearer ', '', $jwtToken);

    // Split the JWT token and get pody
    $payload = explode(".", $jwtToken)[1];

    // Decode the base64-encoded payload 
    $payload = base64_decode($payload);
    $payload = AesDecrypt($payload, "key");
    $payloadData = json_decode($payload, true);


    $id = $payloadData["id"];
    $account = getAccount(["id" => $id])["data"]["account"];

    http_response_code(200);
    $response = [
        "status" => 200,
        "state" => true,
        "message" => "Account retrivied from token successfully.",
        "data" => ["account" => $account, "accessToken" => $payloadData]
    ];
    return $response;
}

function exitByInvalidToken()
{
    if (checkToken() !== true) {
        http_response_code(401);
        $response = [
            "status" => 401,
            "state" => false,
            "message" => "Access Token is not valid."
        ];
        echo json_encode($response);
        exit;
    }
}
