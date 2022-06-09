<?php

class Login{
    /**
     * Get login page
     */
    public static function get(){
        include __DIR__."/../../public/views/login.php";
        die();
    }

    /**
     * Creates New User
     * @throws DatabaseConnectionError
     */
    public static function put(){
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(empty($json->login)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "login", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->password)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "password", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->firstname)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "firstname", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->lastname)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "lastname", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->genre)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "genre", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->birthdate)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "birthdate", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        self::checkParams([
            "login"=>$json->login,
            "password"=>$json->password,
            "firstname"=>$json->firstname,
            "lastname"=>$json->lastname,
            "genre"=> $json->genre,
            "birthdate"=>$json->birthdate
        ]);

        try {
            if(User::checkIfLoginExists($json->login))
                HtmlResponseHandler::formatedResponse(409, [], ["success" => false]);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Duplicate verification");
            $e->respondWithError();
        }

        try{
            if (User::createUser($json->login, $json->password, $json->firstname, $json->lastname, $json->genre, $json->birthdate) === false){
                $e = new DatabaseConnectionError("Unknown error", "Internal Error - Could not write to database", "Database insertion");
                $e->respondWithError();
            }
        }catch (DatabaseConnectionError $err){
            $err->setStep("Database insertion");
            $err->respondWithError();
        }

        HtmlResponseHandler::formatedResponse(200, [], ["success" => true]);
    }

    /**
     * Login
     * @return void
     */
    public static function post(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Token.php";

        $json = json_decode(file_get_contents("php://input"));

        if(empty($json->login)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "login", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if(empty($json->password)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "password", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        try{
            $id = User::attemptLogin($json->login, $json->password);
        } catch (DatabaseConnectionError $e){
            $e->setStep("Login attempt");
            $e->respondWithError();
            die();
        }

        if($id === -1) HtmlResponseHandler::formatedResponse(403, [], ["success" => false]);

        $agent = $_SERVER['HTTP_USER_AGENT'];

        try {
            if(Token::tokenExists($id, $agent))
                $token = Token::refreshToken($id, $agent);
            else
                $token = Token::generateToken($id, $agent);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token generation");
            $e->respondWithError();
            die();
        }

        HtmlResponseHandler::formatedResponse(200, [], ["success" => true, "token"=>$token]);
    }

    /**
     * Validate form inputs individually
     * @param mixed $param input to be validated
     * @param string $paramName name of input to be validated
     * @param int $minLength (optional) minimum length of input
     * @param int $maxLength (optional) maximum length of input
     * @throws InvalidParameterError
     */
    private static function validateParam(mixed $param, string $paramName, int $minLength = 1, int $maxLength = 20){
        if(strlen($param) > $maxLength)
            throw new InvalidParameterError(ParameterErrorCase::Long, $paramName, "Invalid Request - Parameter length exceeded");
        if(strlen($param) < $minLength)
            throw new InvalidParameterError(ParameterErrorCase::Short, $paramName, "Invalid Request - Parameter length bellow minimum");

        switch($paramName){
            case "lastname":
            case "firstname":
                $inputWithoutSpaces = str_replace(' ', '', $param);
                if(!(ctype_alpha($inputWithoutSpaces)))
                    throw new InvalidParameterError(ParameterErrorCase::Format, $paramName,  "Invalid Request - Parameter is of wrong format");
                break;

            case "birthdate":
                $reg = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
                if(!preg_match($reg, $param))
                    throw new InvalidParameterError(ParameterErrorCase::Format, $paramName, "Invalid Request - Parameter is of wrong format");
                break;

            case "login":
                if (!is_numeric($param)&&!filter_var($param, FILTER_VALIDATE_EMAIL))
                    throw new InvalidParameterError(ParameterErrorCase::Format, $paramName, "Invalid Request - Parameter is of wrong format");
                break;

            case "genre":
                if ($param!="f"&&$param!="m")
                    throw new InvalidParameterError(ParameterErrorCase::Format, $paramName, "Invalid Request - Parameter is of wrong format");
                break;
        }
    }

    /**
     * Checks input params array and kills+respond if any param is invalid
     * @param array $params
     * @return void
     */
    public static function checkParams(array $params): void{
        foreach ($params as $paramName => $param) {
            $min = 0; $max = 0;
            switch ($paramName) {
                case "lastname":
                case "firstname":
                    $min = 2;
                    $max = 32;
                    break;
                case "login":
                    $min = 5;
                    $max = 64;
                    break;
                case "password":
                    $min = 8;
                    $max = 64;
                    break;
                case "genre":
                    $min = 1;
                    $max = 1;
                    break;
                case "birthdate":
                    $min = 10;
                    $max = 10;
                    break;
            }
            try {
                self::validateParam($param, $paramName, $min, $max);
            } catch (InvalidParameterError $e) {
                $e->respondWithError();
            }
        }
    }
}