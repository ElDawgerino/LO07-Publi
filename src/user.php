<?php

require_once 'database.php';

class user_management
{
    public static function login($username, $password)
    {
        //Récupération de la bdd
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        if(self::check_connection())
        {
            $response = [
                "status" => "already_connected"
            ];

            return $response;
        }

        //Pour les tests seulement

        $res = $db->query(
            "select id, password from Users where username = \"".$db->escape_string($username)."\""
        );

        $user_line = $res->fetch_assoc();
        if($user_line and $user_line["password"] == $password)
        {
            $response = [
                "status" => "succeed",
                "user" => [
                    "id" => $user_line["id"],
                    "username" => $username,
                ],
            ];

            //On stocke l'état de connexion dans une session (contrainte du projet)
            // => pas besoin de token donc.
            $_SESSION["connected"] = true;
            $_SESSION["username"] = $username;
            //TODO: Récupérer les droits de l'utilisateur et autres infos

            return $response;
        }
        else
        {
            $response = [
                "status" => "invalid"
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

            return ["status" => "succeed"];
        }
        else
        {
            return ["status" => "was_not_connected"];
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
