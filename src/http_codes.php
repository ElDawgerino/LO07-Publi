<?php

namespace http_codes;

define("http_codes\SUCCESS", 200);
define("http_codes\BAD_REQUEST", 400);
define("http_codes\UNAUTHORIZED", 401);
define("http_codes\FORBIDDEN", 403);
define("http_codes\NOT_FOUND", 404);
define("http_codes\INTERNAL_ERROR", 500);

function success($data)
{
    $data["status"] = SUCCESS;
    return $data;
}

function bad_request()
{
    return ["status" => BAD_REQUEST];
}

function unauthorized()
{
    return ["status" => UNAUTHORIZED];
}

function forbidden()
{
    return ["status" => FORBIDDEN];
}

function not_found()
{
    return ["status" => NOT_FOUND];
}

function internal_error()
{
    return ["status" => INTERNAL_ERROR];
}


?>
