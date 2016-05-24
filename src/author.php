<?php

require_once 'database.php';
require_once 'http.php';

class author
{
    public static function get_authors(){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $auteurs = $db->query(
            "SELECT nom, prenom, organisation, equipe
            FROM Auteurs;",
            []
        );

        $auteurs_lines = $auteurs->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "auteurs" => $auteurs_lines
        ]);
    }

    public static function get_author($id){
        $db = database_factory::get_db();
        if(!$db->is_ok()){
            return http\internal_error();
        }

        $auteur = $db->query("SELECT * FROM Auteurs WHERE id = :id", ["id" =>$id]);
        $auteur_lines = $auteur->fetchAll(PDO::FETCH_ASSOC);
        if(count($auteur_lines) == 0){
            return http\not_found();
        }

        $publis = $db->query(
            "SELECT p.id, p.titre, p.description, p.statut, p.categorie, p.annee_publication
            FROM RelationsAuteurs as ra, Publications as p
            WHERE ra.auteur_id = :id AND ra.publication_id = p.id
            GROUP BY p.id;",
            ["id" => $id]
        );

        $publis_lines = $publis->fetchAll(PDO::FETCH_ASSOC);

        return http\success([
            "auteur" => $auteur_lines,
            "publis" => $publis_lines
        ]);
    }
}

?>