<?php

require_once 'database.php';
require_once 'user.php';

class publication
{
    static private $upload_dir_path = "uploads";

    /**
     * file_info doit contenir un tableau associatif representant le fichier en base64, son nom, sa taille et son type
     * tel que créé avec le module angular-base64-upload
     */
    public static function add_publication($title, $description, $status, $publication_title, $publication_year, $conference_location, $file_info)
    {
        if(!user_management::check_connection())
        {
            return [
                "status" => "unauthorized"
            ];
        }

        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        //Décodage du fichier (qui était en base64)
        $original_name = $file_info["filename"];
        $base64_content = $file_info["base64"];
        $file_size = $file_info["filesize"];
        $file_type = $file_info["filetype"];

        $destination_path =
            self::$upload_dir_path."/".urlencode($_SESSION["username"])."_".hash("sha256", $base64_content)."_".urlencode($original_name);

        if(file_put_contents($destination_path, base64_decode($base64_content)) === false)
        {
            return [
                "status" => "server_error"
            ];
        }

        //Ajout dans la table Files
        $res = $db->query(
            "insert into Files (original_name, path_on_server) values (:original_name, :path_on_server)",
            [
                "original_name" => $original_name,
                "path_on_server" => $destination_path
            ]
        );

        if($res === false)
        {
            return [
                "status" => "insertion_error"
            ];
        }

        $file_id = $db->get_last_insert_id(); //Récupération de l'id généré par l'ajout de la ligne dans Files

        //Ajout de la publication dans la table Publications
        $res = $db->query(
            "insert into Publications (title, description, status, publication_title, publication_year, conference_location, file_id)
            values (:title, :description, :status, :publication_title, :publication_year, :conference_location, :file_id)",
            [
                "title" => $title,
                "description" => $description,
                "status" => $status,
                "publication_title" => $publication_title,
                "publication_year" => $publication_year,
                "conference_location" => $conference_location,
                "file_id" => $file_id
            ]
        );

        if($res === false)
        {
            return [
                "status" => "insertion_error"
            ];
        }

        return [
            "status" => "succeed"
        ];
    }
}
