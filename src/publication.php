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

        //TODO: Ajout à la base de donnée (une entrée dans Files et une autre dans Publications)

        return [
            "status" => "succeed"
        ];
    }
}
