<?php

require_once 'database.php';
require_once 'http.php';
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
     * - auteurs (R) (un tableau contenant les nom, prenom, organisation et equipe représentant les auteurs ou un id d'auteur (la fonction saura s'il faut créer un nouvel auteur ou non))
     *
     * Si la publication a été publiée dans un journal scientifique :
     * --------------------------------------------------------------
     * - journal_titre (O) (chaîne de caractère)
     * - journal_editeur (O) (chaîne de caractère)
     *           OU
     * - journal_id (O) (entier, id du journal déjà existant)
     *
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
     *            OU
     * - conference_id (O) (entier, id d'une conférence déjà existante)
     *
     * Note : s'il n'existe pas de Conference avec exactement les même valeurs alors une nouvelle Conférence est créée et associée à la Publication
     * ======
     *
     * R => champs requis
     * O => optionnel
     *
     * Retourne :
     * ==========
     * Un tableau associatif contenant :
     * - status : un code de réponse HTTP
     * - id : l'id de la publication nouvellement créée
     */
    public static function add_publication($publication)
    {
        if(!user_management::check_connection())
        {
            return http\unauthorized();
        }

        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        if(!isset($publication["titre"]) or !isset($publication["statut"]) or !isset($publication["categorie"]))
        {
            return http\bad_request();
        }

        //Début de la transaction SQL
        $db->begin_transaction();

        //Décodage du fichier (qui était en base64)
        $file_id = null;
        if(isset($publication["fichier"]))
        {
            $file_info = $publication["fichier"];
            $original_name = $file_info["filename"];
            $base64_content = $file_info["base64"];
            $file_size = $file_info["filesize"];
            $file_type = $file_info["filetype"];

            $destination_path =
                self::$upload_dir_path."/".urlencode($_SESSION["id"])."_".hash("sha256", $base64_content);

            if(file_put_contents($destination_path, base64_decode($base64_content)) === false)
            {
                return http\internal_error();
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
                return http\internal_error();
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
                return http\internal_error();
            }
        }
        else if(isset($publication["journal_id"]))
        {
            $journal_id = $publication["journal_id"];
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
                return http\internal_error();
            }
        }
        else if(isset($publication["conference_id"]))
        {
            $conference_id = $publication["conference_id"];
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
            return http\internal_error();
        }
        $publication_id = $db->get_last_insert_id();

        //Ajout des auteurs
        for($i = 0; $i < count($publication["auteurs"]); $i++)
        {
            $auteur = $publication["auteurs"][$i];
            if(isset($auteur["id"]))
            {
                $res = $db->query(
                    "insert into RelationsAuteurs values (:publication_id, :numero, :auteur_id);",
                    [
                        "publication_id" => $publication_id,
                        "numero" => $i,
                        "auteur_id" => $auteur["id"]
                    ]
                );
                //Pas de traitement particulier en cas d'échec (id qui ne satisfait pas la contraite de clé étrangère)
                //On ignorera l'auteur
            }
            else if(isset($auteur["nom"]) and isset($auteur["prenom"]) and isset($auteur["organisation"]) and isset($auteur["equipe"]))
            {
                $res = $db->query(
                    "insert into RelationsAuteurs values (:publication_id, :numero, :auteur_id);",
                    [
                        "publication_id" => $publication_id,
                        "numero" => $i,
                        "auteur_id" => self::get_author_id(
                            $auteur["nom"],
                            $auteur["prenom"],
                            $auteur["organisation"],
                            $auteur["equipe"]
                        )
                    ]
                );
            }
        }

        $db->commit();

        return http\success(["id" => $publication_id]);
    }

    /**
     * Le premier paramètre est l'id de la publication à modifier.
     * Le second est un tableau associatif comme pour add_publication.
     *
     * Attention : le comportement de la clé "fichier" est différent :
     * ===========  - si elle n'est pas définie, le fichier actuel est supprimé (la publication n'a plus de fichier associé)
     *              - si elle est définie à un nouveau fichier (même structure que add_publication) alors un nouveau fichier est uploadé et affecté
     *              - si elle est définie à "garder", le fichier est conservé (pas besoin de le réenvoyer !)
     *
     * Retourne :
     * ==========
     * un code d'erreur HTTP
     */
    public static function update_publication($id, $publication)
    {
        if(!user_management::check_connection())
        {
            return http\unauthorized();
        }

        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        //On vérifie si la publication existe
        if($db->query(
                "SELECT p.* FROM Publications AS p WHERE p.id = :id;",
                [
                    "id" => $id
                ]
            )->rowCount() == 0)
        {
            return http\not_found();
        }

        //On vérifie si l'utilisateur est un auteur de la publication et donc s'il a le droit de la modifier
        $current_user_id = user_management::get_current_logged_user()["id"];
        if($db->query(
                "SELECT ra.* FROM RelationsAuteurs AS ra WHERE ra.publication_id = :id AND ra.auteur_id = :user_id;",
                [
                    "id" => $id,
                    "user_id" => $current_user_id
                ]
            )->rowCount() == 0)
        {
            return http\forbidden();
        }

        if(!isset($publication["titre"]) or !isset($publication["statut"]) or !isset($publication["categorie"]))
        {
            return http\bad_request();
        }

        //Début de la transaction SQL
        $db->begin_transaction();

        //Décodage du fichier (qui était en base64)
        $file_id = null;
        if(isset($publication["fichier"]))
        {
            if($publication["fichier"] !== "garder")
            {
                //L'utilisateur souhaite mettre à jour la publication avec un nouveau fichier
                $file_info = $publication["fichier"];
                $original_name = $file_info["filename"];
                $base64_content = $file_info["base64"];
                $file_size = $file_info["filesize"];
                $file_type = $file_info["filetype"];

                $destination_path =
                    self::$upload_dir_path."/".urlencode($_SESSION["id"])."_".hash("sha256", $base64_content);

                if(file_put_contents($destination_path, base64_decode($base64_content)) === false)
                {
                    return http\internal_error();
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
                    return http\internal_error();
                }

                $file_id = $db->get_last_insert_id(); //Récupération de l'id généré par l'ajout de la ligne dans Files
            }
            else
            {
                //L'utilisateur souhaite conserver l'ancien fichier de la publication associé
                $res = $db->query(
                    "SELECT fichier_id FROM Publications WHERE id=:id;",
                    [
                        "id" => $id
                    ]
                );
                $file_id = $res->fetch(PDO::FETCH_ASSOC)["fichier_id"];
            }
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
                return http\internal_error();
            }
        }
        else if(isset($publication["journal_id"]))
        {
            $journal_id = $publication["journal_id"];
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
                return http\internal_error();
            }
        }
        else if(isset($publication["conference_id"]))
        {
            $conference_id = $publication["conference_id"];
        }

        //Ajout de la publication
        $res = $db->query(
            "UPDATE Publications SET titre=:titre, description=:description, statut=:statut, categorie=:categorie, annee_publication=:annee_publication,
            journal_id=:journal_id, journal_volume=:journal_volume, pages=:pages, conference_id=:conference_id, fichier_id=:fichier_id
            WHERE id = :id;",
            [
                "id" => $id,
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
            return http\internal_error();
        }

        //On enlève tous les auteurs pour les réajouter dans le bon ordre
        $db->query(
            "DELETE FROM RelationsAuteurs WHERE publication_id=:id",
            [
                "id" => $id
            ]
        );

        //Ajout des auteurs
        for($i = 0; $i < count($publication["auteurs"]); $i++)
        {
            $auteur = $publication["auteurs"][$i];
            if(isset($auteur["id"]))
            {
                $res = $db->query(
                    "insert into RelationsAuteurs values (:publication_id, :numero, :auteur_id);",
                    [
                        "publication_id" => $id,
                        "numero" => $i,
                        "auteur_id" => $auteur["id"]
                    ]
                );
                //Pas de traitement particulier en cas d'échec (id qui ne satisfait pas la contraite de clé étrangère)
                //On ignorera l'auteur
            }
            else if(isset($auteur["nom"]) and isset($auteur["prenom"]) and isset($auteur["organisation"]) and isset($auteur["equipe"]))
            {
                $res = $db->query(
                    "insert into RelationsAuteurs values (:publication_id, :numero, :auteur_id);",
                    [
                        "publication_id" => $id,
                        "numero" => $i,
                        "auteur_id" => self::get_author_id(
                            $auteur["nom"],
                            $auteur["prenom"],
                            $auteur["organisation"],
                            $auteur["equipe"]
                        )
                    ]
                );
            }
        }

        $db->commit();

        $data["id"] = $id;
        return http\success($data);
    }

    public static function get_publications()
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        $res = $db->query(
            "SELECT p.id, p.titre, p.description, p.statut, p.categorie, p.annee_publication, p.journal_volume, p.pages,
            j.titre as journal_titre, j.editeur as journal_editeur,
            c.nom as conference_nom, c.date_conference as conference_date, c.lieu as conference_lieu
            FROM Publications AS p LEFT JOIN Journaux AS j ON p.journal_id = j.id LEFT JOIN Conferences AS c ON p.conference_id = c.id
            ORDER BY  p.titre;",
            []
        );

        if($res->rowCount() === 0)
        {
            return http\success([]);
        }

        $publiLines = $res->fetchAll(PDO::FETCH_ASSOC);

        $publication = array();
        foreach ($publiLines as $publi) {
            $publications[] = $publi;
        }

        return http\success($publications);
    }

    public static function get_publication($id)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        //Récupération de la publication, des éventuels journal ou conférence associés
        $res = $db->query(
            "SELECT p.id, p.titre, p.description, p.statut, p.categorie, p.annee_publication, p.journal_volume, p.pages,
            j.titre as journal_titre, j.editeur as journal_editeur,
            c.nom as conference_nom, c.date_conference as conference_date, c.lieu as conference_lieu
            FROM Publications AS p
            LEFT JOIN Journaux AS j ON p.journal_id = j.id
            LEFT JOIN Conferences AS c ON p.conference_id = c.id
            WHERE p.id = :id ;",
            [
                "id" => $id
            ]
        );

        if($res->rowCount() === 0)
        {
            return http\not_found();
        }

        $publication = $res->fetch(PDO::FETCH_ASSOC);

        //Récupération du fichier associé
        $info_fichier = self::get_publication_file_info($id);
        if($info_fichier["status"] == http\SUCCESS)
        {
            $path_on_server = $info_fichier["chemin_server"];

            if(file_exists($path_on_server))
            {
                $publication["fichier"]["nom"] = $info_fichier["nom_original"];
                $publication["fichier"]["taille"] = $info_fichier["taille"];
            }
        }

        //Récupération des auteurs
        $authors_res = $db->query(
            "SELECT a.id, a.nom, a.prenom, a.organisation, a.equipe
            FROM RelationsAuteurs AS r, Auteurs AS a
            WHERE r.publication_id = :id AND r.auteur_id = a.id
            ORDER BY r.numero_auteur ASC;",
            [
                "id" => $id
            ]
        );

        $publication["auteurs"] = [];
        $authors_lines = $authors_res->fetchAll(PDO::FETCH_ASSOC);
        foreach($authors_lines as $author)
        {
            $publication["auteurs"][] = $author;
        }

        return http\success([
            "publication" => $publication
        ]);
    }

    public static function get_publication_file_info($publication_id)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return http\internal_error();
        }

        $res = $db->query(
            "select nom_original, chemin_server from Fichiers as f, Publications as p where p.id = :publication_id and f.id = p.fichier_id",
            [
                "publication_id" => $publication_id
            ]
        );

        if($res->rowCount() == 0)
        {
            return http\not_found();
        }

        $file_line = $res->fetch();

        return http\success([
            "nom_original" => $file_line["nom_original"],
            "chemin_server" => $file_line["chemin_server"],
            "taille" => (file_exists($file_line["chemin_server"]) ? filesize($file_line["chemin_server"]) : 0)
        ]);
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

    /**
     * Récupère l'id d'un auteur selon les critères donnés ou crée un nouvel auteur si aucun ne correspond à ces critères.
     *
     * Retourne false en cas d'erreur avec la base de données
     */
    private static function get_author_id($name, $first_name, $organisation, $team)
    {
        $db = database_factory::get_db();
        if(!$db->is_ok())
        {
            return false;
        }

        return $db->get_id_of(
            "Auteurs",
            [
                "nom" => $name,
                "prenom" => $first_name,
                "organisation" => $organisation,
                "equipe" => $team
            ],
            true
        );
    }

    public static function getJournaux(){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $journaux = $db->query(
            "SELECT titre, editeur
            FROM Journaux;",
            []
        );

        $journaux_lines = $journaux->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "journaux" => $journaux_lines
        ]);
    }

    public static function getConferences(){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $conferences = $db->query(
            "SELECT nom, date_conference, lieu
            FROM Conferences;",
            []
        );

        $conf_lines = $conferences->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "conferences" => $conf_lines
        ]);
    }

    public static function search($params){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        //Note : prepare ne marche pas avec des colonnes en paramètre
        $query = $db->build_search_query($params["field"]);

        $result = $db->query(
            $query,
            [
                "keyword" => $params["keyword"],
                "order" => $params["order"]
            ]
        );

        $result_lines = $result->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "resultat" => $result_lines
        ]);
    }

    public static function stats(){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $annee = $db->query(
        "SELECT annee_publication, COUNT(id) AS nbrPublis FROM `publications` GROUP BY annee_publication;",
        []
        );

        $statsAnnee = $annee->fetchAll(PDO::FETCH_ASSOC);

        $auteurs = $db->query(
        "SELECT nom, prenom, COUNT(publication_id) AS nbrPublis FROM `relationsauteurs` JOIN auteurs ON auteur_id = id GROUP BY auteur_id;",
        []
        );

        $statsAuteurs = $auteurs->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "auteurs" => $statsAuteurs,
            "annees" => $statsAnnee
        ]);
    }

    public static function anomalies(){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $doublons = $db->query(
        "SELECT   COUNT(titre) AS nbr_doublon, id, titre, statut, categorie, annee_publication
        FROM     publications
        GROUP BY titre
        HAVING   COUNT(titre) > 1;",
        []
        );

        $doublonsLines = $doublons->fetchAll(PDO::FETCH_ASSOC);

        $publisAuteurs = $db->query(
        "SELECT publication_id, nom, prenom, organisation
        FROM `relationsauteurs`
        JOIN auteurs ON auteurs.id = auteur_id
        ORDER BY publication_id;",
        []
        );

        $publisAuteursLines = $publisAuteurs->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($publisAuteursLines as $line) {
            if(!isset($result[$line["publication_id"]]) || $result[$line["publication_id"]] != true){
                if($line["organisation"] == "UTT"){
                    $result[$line["publication_id"]] = true;
                }
                else {
                    $result[$line["publication_id"]] = false;
                }
            };
        }

        $publisSansUTT = [];
        foreach ($result as $key => $value) {
            if(!$value){
                $publi = $db->query("SELECT id, titre, statut, categorie, annee_publication
                    FROM publications WHERE id = :key",
                    ["key" => $key]);
                    array_push($publisSansUTT, $publi->fetch(PDO::FETCH_ASSOC));
            }
        }

        return http\success([
            "doublons" => $doublonsLines,
            "publis" => $publisSansUTT
        ]);

    }
}
