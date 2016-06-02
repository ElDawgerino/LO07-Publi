<?php

require_once 'database.php';
require_once 'http.php';

class user_management
{
    public static function login($username, $password)
    {
        //Récupération de la bdd
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        if(self::check_connection())
        {
            return http\forbidden();
        }

        //Pour les tests seulement

        $res = $db->query(
            "select id, mdp, admin from Utilisateurs where login = :username",
            array('username' => $username)
        );

        $user_line = $res->fetch();
        if($user_line and $user_line["mdp"] == hash("sha256", $password))
        {
            $response = http\success([ "id" => $user_line["id"] ]);

            //On stocke l'état de connexion dans une session (contrainte du projet)
            // => pas besoin de token donc.
            $_SESSION["connected"] = true;
            $_SESSION["id"] = $user_line["id"];
            $_SESSION["admin"] = $user_line["admin"];
            //TODO: Récupérer les droits de l'utilisateur et autres infos

            return $response;
        }
        else
        {
            $response = http\unauthorized();

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

            return http\success([]);
        }
        else
        {
            return http\unauthorized();
        }
    }

    public static function register($username, $password, $last_name, $first_name, $organisation, $team)
    {
        //Récupération de la bdd
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        //Si déjà connecté, on ne peut pas enregistrer un compte
        if(self::check_connection())
        {
            return http\forbidden();
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
            return http\bad_request();
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

            return http\bad_request();
        }


        return http\success([]);
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

    public static function isAdmin(){
        if(isset($_SESSION["admin"]) && $_SESSION["admin"] == 1){
            return true;
        }
        else {
            return false;
        }
    }

    public static function get_users()
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        $res = $db->query(
            "SELECT * FROM Utilisateurs AS u, Auteurs AS a WHERE u.id = a.id", null
        );

        $users = $res->fetchAll();
        $response = array();
        foreach ($users as $key => $user) {
            $response[] = [
                "id" => $user["id"],
                "username" => $user["login"],
                "last_name" => $user["nom"],
                "first_name" => $user["prenom"],
                "organisation" => $user["organisation"],
                "team" => $user["equipe"]
            ];
        }
        return http\success($response);
    }

    public static function get_current_logged_user()
    {
        if(self::check_connection())
        {
            return http\success([ "id" => $_SESSION["id"] ]);
        }
        else
        {
            return http\unauthorized();
        }
    }

    public static function get_user($id)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        $res = $db->query(
            "SELECT * FROM Utilisateurs AS u, Auteurs AS a WHERE a.id = :id AND u.id = :id",
            array("id" => $id)
        );

        $user = $res->fetch();
        $response = [
            "id" => $user["id"],
            "username" => $user["login"],
            "last_name" => $user["nom"],
            "first_name" => $user["prenom"],
            "organisation" => $user["organisation"],
            "team" => $user["equipe"],
            "admin" => $user["admin"]
        ];

        return http\success($response);
    }

    public static function delete_user($id){
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        if(!user_management::check_connection())
        {
            return http\unauthorized();
        }
        else if(!($_SESSION["id"] == $id || $_SESSION["admin"] == 1)){
            return http\unauthorized();
        }

        $res = $db->query(
            "DELETE FROM Utilisateurs WHERE id = :id",
            array("id" => $id)
        );

        if($_SESSION["id"] == $id){
                session_destroy();
        }

        $response["id"] = $id;
        return http\success($response);

    }
}
