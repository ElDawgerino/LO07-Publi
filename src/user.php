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
            "select id, mdp from Utilisateurs where login = :username",
            array('username' => $username)
        );

        $user_line = $res->fetch();
        if($user_line and $user_line["mdp"] == hash("sha256", $password))
        {
            $response = [
                "status" => "succeed",
                "id" => $user_line["id"],
            ];

            //On stocke l'état de connexion dans une session (contrainte du projet)
            // => pas besoin de token donc.
            $_SESSION["connected"] = true;
            $_SESSION["id"] = $user_line["id"];
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

    public static function register($username, $password, $last_name, $first_name, $organisation, $team)
    {
        //Récupération de la bdd
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        //Si déjà connecté, on ne peut pas enregistrer un compte
        if(self::check_connection())
        {
            $response = [
                "status" => "already_connected"
            ];

            return $response;
        }

        //Il n'existe pas d'utilisateur avec le même username
        //Ajout de l'utilisateur

        //Dans la table Auteurs
        $res = $db->query(
            "INSERT INTO Auteurs (nom, prenom, organisation, equipe)
            VALUES (:last_name, :first_name, :organisation, :team)",
            [
                "last_name" => $last_name,
                "first_name" => $first_name,
                "organisation" => $organisation,
                "team" => $team
            ]
        );
        if(!$res)
        {
            return [
                "status" => "invalid"
            ];
        }

        //Puis dans la table Utilisateurs (en utilisant l'id généré lors de l'ajout dans Auteurs)
        $author_id = $db->get_last_insert_id();
        $res = $db->query(
            "INSERT INTO Utilisateurs (id, login, mdp)
            VALUES (:author_id, :username, :password)",
            [
                "author_id" => $author_id,
                "username" => $username,
                "password" => hash("sha256", $password)
            ]
        );
        if(!$res)
        {
            //Retirer de la table Auteurs si l'ajout à la table Utilisateurs a échoué.
            $db->query(
                "DELETE FROM Auteurs WHERE id = :author_id",
                [
                    "author_id" => $author_id
                ]
            );

            return [
                "status" => "invalid"
            ];
        }


        return [
            "status" => "succeed"
        ];
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

    public static function get_users()
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        $res = $db->query(
            "SELECT * FROM Utilisateurs", null
        );

        $users = $res->fetchAll();
        $response = array();
        foreach ($users as $key => $user) {
            $response[] = [
                "id" => $user["id"],
                "username" => $user["username"],
            ];
        }
        return $response;
    }

    public static function get_current_logged_user()
    {
        if(self::check_connection())
        {
            return [
                "status" => "succeed",
                "id" => $_SESSION["id"]
            ];
        }
        else
        {
            return [
                "status" => "invalid"
            ];
        }
    }

    public static function get_user($id)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        $res = $db->query(
            "SELECT * FROM Utilisateurs WHERE id = :id",
            array("id" => $id)
        );

        $user = $res->fetch();
        $response = [
            "id" => $user["id"],
            "username" => $user["username"],
        ];

        return $response;
    }
}
