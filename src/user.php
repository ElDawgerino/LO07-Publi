<?php

class user_management
{
    public static function login($username, $password)
    {
        if(self::check_connection())
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
            // Vidage de la superglobale de session
            $_SESSION = array();

            // Destruction du cookie de session si PHP est configuré pour l'utiliser
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Destruction de la session
            session_destroy();

            // Recréation d'une nouvelle session neuve
            session_start();

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
