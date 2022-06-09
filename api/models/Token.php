<?php

class Token{

    /**
     * Insert new id+agent token combination and returns generated token
     * @param int $id
     * @param string $agent
     * @return string generated token
     * @throws DatabaseConnectionError
     */
    public static function generateToken(int $id, string $agent):string{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        $token = DatabaseLinkHandler::generateRandomString(30);

        $res = $link->insert("INSERT INTO tokens (client, id_user, token, expires)
                   VALUES (:agent, :uid, :token, :date)",
            [
                "agent"=>$agent,
                "uid"=>$id,
                "token"=>$token,
                "date"=>date("Y-m-d H:i:s",strtotime(TOKEN_VALIDITY))
            ]
        );

        if ($res === false) throw new DatabaseConnectionError("N/A", "Could not insert into database");
        else return $token;
    }

    /**
     * Updates already existing id+agent token combination and returns newly generated token
     * @param int $id
     * @param string $agent
     * @return string
     * @throws DatabaseConnectionError
     */
    public static function refreshToken(int $id, string $agent):string{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        $token = DatabaseLinkHandler::generateRandomString(30);

        $res = $link->insert("UPDATE tokens SET token = :token, expires = :date WHERE client = :agent AND id_user = :uid",
            [
                "agent"=>$agent,
                "uid"=>$id,
                "token"=>$token,
                "date"=>date("Y-m-d H:i:s",strtotime(TOKEN_VALIDITY))
            ]
        );

        if ($res === false) throw new DatabaseConnectionError("N/A", "Could not update database");
        else return $token;
    }

    /**
     * Checks if id+agent combination exists
     * @param int $id
     * @param string $agent
     * @return bool true if it already exists, false if it doesn't
     * @throws DatabaseConnectionError
     */
    public static function tokenExists(int $id, string $agent):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        $res = $link->query("SELECT token FROM tokens WHERE id_user =:uid AND client = :agent",
            [
                "agent"=>$agent,
                "uid"=>$id
            ]
        );

        if ($res === false) return false;
        else return true;
    }

    /**
     * Checks if token is valid and returns uid
     * @param string $token
     * @param string $agent
     * @return int -1 is token is not valid, uid if token is valid
     * @throws DatabaseConnectionError
     */
    public static function tokenIsValid(string $token, string $agent):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        $token = DatabaseLinkHandler::sanitizeStringQuotes($token);

        $res = $link->query("SELECT id_user FROM tokens WHERE token = :token AND expires > NOW() AND client = :agent",
            [
                "agent"=>$agent,
                "token"=>$token
            ]
        );

        if ($res === false) return -1;
        else return $res["id_user"];
    }

}