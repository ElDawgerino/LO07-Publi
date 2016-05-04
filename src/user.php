<?php

class user_management
{
    public static function login($username, $password)
    {
        if(!self::check_connection())
        {
            $response = [
                "state" => "already"
            ];

            return $response;
        }

        //Pour les tests seulement
        if(($username == "test1" || $username == "test2") && $password == "angular")
        {
            $response = [
                "state" => "succeed",
                "username" => $username,
                "roles" => ["foo", "bar", "foobar"]
            ];

            //On stocke l'état de connexion dans une session (contrainte du projet)
            // => pas besoin de token donc.
            $_SESSION["connected"] = true;
            $_SESSION["username"] = $username;

            return $response;
        }
        else
        {
            $response = [
                "state" => "invalid"
            ];

            $_SESSION["connected"] = false;
            $_SESSION["username"] = "";

            return $response;
        }
    }

    public static function logout()
    {
        if(self::check_connection())
        {
            $_SESSION["connected"] = false;
            $_SESSION["username"] = "";

            return ["state" => "succeed"];
        }
        else
        {
            return ["state" => "was_not_connected"];
        }
    }

    public static function check_connection()
    {
        if(isset($_SESSION["connected"])
            && $_SESSION["connected"] == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
