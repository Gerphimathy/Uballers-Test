<?php

include __DIR__."/../api/exceptions/__includeExceptions.php";
include __DIR__."/../api/tools/HtmlResponseHandler.php";
include __DIR__."/../api/database/DatabaseLinkHandler.php";
include __DIR__."/../api/database/CREDENTIALS.php";
include __DIR__."/../api/tools/const.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);


$route = $_REQUEST["route"] ?? "";

$method = $_SERVER["REQUEST_METHOD"];

if ($route === ""){
    include __DIR__."/views/home.php";
    die();
}

/**
 Response Formats:

Exceptions:
{
    "success":false
    "error":{
        "code":integer to be switched on by the javascript (or eventually java for mobile app)
        "message":general error message
        "debug":{free format, used for internal errors for eventual debugging}
        "info":{strict format, used internally, cf each exception for details}
    }
}


Responses:
{
    "success":false|true
    "token":token for ease of use
    "NAME OF THE INFO":{}
}
 */

$args = explode("?",$route)[1] ?? "";
$route = explode("?",$route)[0] ?? "";

switch ($route){
    case "login":
        include __DIR__."/../api/controllers/Login.php";
        switch ($method){
            case "GET":
                Login::get();
                break;
            case "POST":
                Login::post();
                break;
            case "PUT":
                Login::put();
                break;
            default:
                HtmlResponseHandler::formatedResponse(405, [],["success"=>false]);
        }
        break;
    case "auth":
        include __DIR__."/../api/controllers/Auth.php";
        switch ($method){
            case "GET":
                Auth::get();
                break;
            default:
                HtmlResponseHandler::formatedResponse(405, [],["success"=>false]);
        }
        break;
    default:
        switch ($method){
            case "GET":
                //C'est ici qu'irait une page 404 normalement
                include __DIR__."/views/notFound.php";
                die();
            default:
                HtmlResponseHandler::formatedResponse(204, [],["success"=>false]);
        }
        break;
}