<?php

class User{

    /**
     * Inserts new use into the database
     * @param string $login
     * @param string $password
     * @param string $firstname
     * @param string $lastname
     * @param string $genre
     * @param string $birthdate
     * @return bool result of the insertion
     * @throws DatabaseConnectionError
     */
    public static function createUser(string $login, string $password, string $firstname, string $lastname, string $genre, string $birthdate):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        return $link->insert("INSERT INTO users (login, password, firstname, lastname, genre, birthdate)
                   VALUES (:login, :password, :firstname, :lastname, :genre, :birthdate)",
            [
                "login"=>$login,
                "password"=>$link::preparePassword($password),
                "firstname"=>$firstname,
                "lastname"=>$lastname,
                "genre"=>$genre,
                "birthdate"=>$birthdate
            ]
        );
    }

    /**
     * Returns ID of the user with correct login-password combination, -1 if user does not exist
     * @param string $login
     * @param string $password
     * @return int ID of the user with correct login-password combination, -1 if user does not exist
     * @throws DatabaseConnectionError
     */
    public static function attemptLogin(string $login, string $password):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);

        $res = $link->query("SELECT id FROM users WHERE login = :login AND password = :password",
            [
                "login"=>$login,
                "password"=>$link::preparePassword($password)
            ]
        );

        if ($res === false) return -1;
        else return $res["id"];
    }

    /**
     * Checks to if login is already in use
     * @throws DatabaseConnectionError
     * @return bool true if login already in use, false if it is free to use
     */
    public static function checkIfLoginExists(string $login):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $res = $link->query("SELECT id FROM users WHERE login = :login", ["login" => $login]);

        if ($res !== false) return true;
        else return false;
    }
}