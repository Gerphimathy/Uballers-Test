<?php

class Auth{

    /**
     * Get auth status
     */
    public static function get(){
        include __DIR__."/../models/Token.php";

        if(empty($_GET["token"])){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "token", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        try{
            $id = Token::tokenIsValid($_GET["token"], $_SERVER['HTTP_USER_AGENT']);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }

        $info = [];

        $info["token"] = $_GET["token"];
        $info["agent"] = $_SERVER['HTTP_USER_AGENT'];
        $info["isvalid"] = ($id === -1 ? false:true);

        HtmlResponseHandler::formatedResponse(200, [], ["success"=>true, "info"=>(object)$info]);
    }
}