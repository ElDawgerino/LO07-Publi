<?php

class user_management
{
    public static function login($username, $password)
    {
        if(isset($_SESSION["connected"]) && $_SESSION["connected"] == true)
        {
            $response = [
                "state" => "already"
            ];

            return $response;
        }

        //Pour les tests seulement
        if(($username == "test1" || $username == "test2") && $password == "angular")
        {
            $connection_token = $rand = md5(microtime());

            $response = [
                "state" => "succeed",
                "token" => $connection_token,
                "username" => $username,
                "roles" => ["foo", "bar", "foobar"]
            ];

            $_SESSION["connected"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["token"] = $connection_token;

            return $response;
        }
        else
        {
            $response = [
                "state" => "invalid"
            ];

            $_SESSION["connected"] = false;
            $_SESSION["username"] = "";
            $_SESSION["token"] = "";

            return $response;
        }
    }

    public static function logout($token)
    {
        /* TODO: Activer cette vérification dès que les tokens pourront être stockés côté Angular
        if(not self::check_token($token))
        {
            return ["state" => "auth_error"];
        }*/

        if(isset($_SESSION["connected"]) && $_SESSION["connected"] == true)
        {
            $_SESSION["connected"] = false;
            $_SESSION["username"] = "";
            $_SESSION["token"] = "";

            return ["state" => "succeed"];
        }
        else
        {
            return ["state" => "was_not_connected"];
        }
    }

    public static function check_token($token)
    {
        if(isset($_SESSION["connected"])
            && $_SESSION["connected"] == true
            && $_SESSION["token"] == $token)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
