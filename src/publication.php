<?php

require_once 'database.php';
require_once 'user.php';

class publication
{
    static private $upload_dir_path = "uploads";

    /**
     * Le tableau associatif doit contenir tous les champs requis et peut aussi contenir d'autres champs optionnels
     *
     * Champs :
     * ========
     * - titre (R) (chaîne de caractère)
     * - description (O) (chaîne de caractère)
     * - statut (R) (chaîne de caractère parmi "Soumis", "En révision", "Publié")
     * - categorie (R) (chaîne de caractère parmi 'RI', 'CI', 'RF', 'CF', 'OS', 'TD', 'BV', 'AP')
     * - annee_publication (O) (entier)
     * - fichier (O) (structure générée par le module angular-base64 qui contient le fichier et ses informations)
     *
     * Si la publication a été publiée dans un journal scientifique :
     * --------------------------------------------------------------
     * - journal_titre (O) (chaîne de caractère)
     * - journal_editeur (O) (chaîne de caractère)
     * - journal_volume (O) (chaîne de caractère) : le numéro du volume où a été publié la publication
     * - pages (O) (chaîne de caractère) : indique les pages où est l'extrait publié (exemple : "56-62")
     *
     * Note : s'il n'existe pas de Journal avec les même valeurs de journal_titre et journal_editeur, un nouveau sera créé.
     * ====== Il peut être utile pour l'interface du site de proposer une autocomplétion du titre ET de l'éditeur (en fonction du titre) du Journal
     *        Pareil pour le volume de l'édition du Journal.
     *        Si journal_edition_volume n'est pas précisé contrairement à journal_titre et journal_editeur alors la Publication sera associées qu'au Journal
     *        mais pas à une édition précise.
     *
     * Si la publication a été publiée ou présentée dans une conférence :
     * ------------------------------------------------------------------
     * - conference_nom (O) (chaîne de caractère)
     * - conference_date (O) (date en chaîne de caractère)
     * - conference_lieu (O) (chaîne de caractère)
     *
     * Note : s'il n'existe pas de Conference avec exactement les même valeurs alors une nouvelle Conférence est créée et associée à la Publication
     * ======
     *
     * R => champs requis
     * O => optionnel
     */
    public static function add_publication($publication)
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

        //Début de la transaction SQL
        $db->begin_transaction();

        //Décodage du fichier (qui était en base64)
        $file_id = null;
        if(isset($publication["fichier"]))
        {
            $file_info = $publication["file"];
            $original_name = $file_info["filename"];
            $base64_content = $file_info["base64"];
            $file_size = $file_info["filesize"];
            $file_type = $file_info["filetype"];

            $destination_path =
                self::$upload_dir_path."/".urlencode($_SESSION["id"])."_".hash("sha256", $base64_content);

            if(file_put_contents($destination_path, base64_decode($base64_content)) === false)
            {
                return [
                    "status" => "server_error"
                ];
            }

            //Ajout dans la table Fichiers
            $res = $db->query(
                "insert into Fichiers (nom_original, chemin_server) values (:original_name, :path_on_server)",
                [
                    "original_name" => $original_name,
                    "path_on_server" => $destination_path
                ]
            );

            if($res === false)
            {
                $db->rollback();
                return ["status" => "insertion_error"];
            }

            $file_id = $db->get_last_insert_id(); //Récupération de l'id généré par l'ajout de la ligne dans Files
        }

        //Si une publication a été publiée dans un journal
        $journal_id = null;
        $journal_edition_id = null;
        if(isset($publication["journal_titre"]) and isset($publication["journal_editeur"]))
        {
            //Récupération de l'id du journal (ou création du journal si besoin)
            if(($journal_id = self::get_journal_id($publication["journal_titre"], $publication["journal_editeur"])) === false)
            {
                //Il y a eu une erreur lors de l'accès à la bdd, on stoppe tout !
                $db->rollback();
                return ["status" => "insertion_error"];
            }
        }

        //Si une publication a été présentée lors d'une conférence
        $conference_id = null;
        if(isset($publication["conference_nom"]) and isset($publication["conference_date"]) and isset($publication["conference_lieu"]))
        {
            //Récupération de l'id du journal (ou création du journal si besoin)
            if(($conference_id = self::get_conference_id($publication["conference_nom"], $publication["conference_date"], $publication["conference_lieu"])) === false)
            {
                //Il y a eu une erreur lors de l'accès à la bdd, on stoppe tout !
                $db->rollback();
                return ["status" => "insertion_error"];
            }
        }

        //Ajout de la publication
        $res = $db->query(
            "insert into Publications (titre, description, statut, categorie, annee_publication, journal_id, journal_volume, pages, conference_id, fichier_id)
            values (:titre, :description, :statut, :categorie, :annee_publication, :journal_id, :journal_volume, :pages, :conference_id, :fichier_id)",
            [
                "titre" => $publication["titre"],
                "description" => (isset($publication["description"]) ? $publication["description"] : null),
                "statut" => $publication["statut"],
                "categorie" => $publication["categorie"],
                "annee_publication" => (isset($publication["annee_publication"]) ? $publication["annee_publication"] : null),
                "journal_id" => $journal_id,
                "journal_volume" => (isset($publication["journal_volume"]) ? $publication["journal_volume"] : null),
                "pages" => (isset($publication["pages"]) ? $publication["pages"] : null),
                "conference_id" => $conference_id,
                "fichier_id" => $file_id
            ]
        );

        if($res === false)
        {
            $db->rollback();
            return ["status" => "insertion_error"];
        }

        //Ajout de l'utilisateur actuel en auteur (temporaire, les auteurs passés en paramètre seront bientôt utilisés)
        //TODO

        $db->commit();

        return [
            "status" => "succeed",
            //"id" => $publication_id TODO
        ];
    }

    public static function get_publication_file_info($publication_id)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return [
                "status" => "db_error"
            ];
        }

        $res = $db->query(
            "select original_name, path_on_server from Files as f, Publications as p where p.id = :publication_id and f.id = p.file_id",
            [
                "publication_id" => $publication_id
            ]
        );

        if($res->rowCount() == 0)
        {
            return [
                "status" => "invalid"
            ];
        }

        $file_line = $res->fetch();

        return [
            "status" => "succeed",
            "original_name" => $file_line["original_name"],
            "path_on_server" => $file_line["path_on_server"]
        ];
    }

    /**
     * Récupère l'id d'un journal suivant les critères donnés ou crée un nouveau journal si aucun ne répond à ces critères.
     *
     * Retourne false en cas d'erreur avec la base de données
     */
    private static function get_journal_id($title, $editor)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return false;
        }

        return $db->get_id_of(
            "Journaux",
            [
                "titre" => $title,
                "editeur" => $editor
            ],
            true
        );
    }

    /**
     * Récupère l'id d'une conférence suivant les critères donnés ou crée une nouvelle conférence si aucune ne répond à ces critères.
     *
     * Retourne false en cas d'erreur avec la base de données
     */
    private static function get_conference_id($name, $date, $location)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return false;
        }

        return $db->get_id_of(
            "Conferences",
            [
                "nom" => $name,
                "date_conference" => $date,
                "lieu" => $location
            ],
            true
        );
    }
}
