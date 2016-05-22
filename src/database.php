<?php

class database
{
    public function __construct($dsn, $username, $password)
    {
        try {
            $this->pdo = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            $this->is_ok = true;
        }
        catch (PDOException $e) {
            $this->is_ok = false;
        }
    }

    public function is_ok()
    {
        return $this->is_ok;
    }

    public function begin_transaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }

    public function query($query, $data)
    {
        $prepared = $this->pdo->prepare($query);
        $success = $prepared->execute($data);
        return ($success ? $prepared : false);
    }

    public function get_last_insert_id()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Récupère l'id d'une entrée de la table $table dont tous les champs correspondent aux valeurs dans le
     * tableau associatif $criterias. Si $create_if_not_exists est true et si aucune entrée ne répond aux critères,
     * crée un nouvel enregistrement avec les valeurs de $criterias et retourne son id.
     */
    public function get_id_of($table, $criterias, $create_if_not_exists)
    {
        //On essaye de trouver l'entrée de la table correspondante
        $request = "select id from ".$table." where ";
        foreach($criterias as $key => $value)
        {
            $request = $request.$key." = :".$key." and ";
        }
        $request = preg_replace('/ and $/', '', $request);
        $request = $request.";";

        $res = $this->query(
            $request,
            $criterias
        );

        if($res->rowCount() >= 1)
        {
            return $res->fetch()["id"];
        }
        else
        {
            if(!$create_if_not_exists)
                return false;

            //Création de l'enregistrement

            //Construction de la requête (INSERT INTO $table ($colonne1, $colonne2, ...) VALUES ($valeur1, $valeur2, ...);)
            $insert_request = "insert into ".$table." (";
            foreach($criterias as $key => $value)
            {
                $insert_request = $insert_request.$key.",";
            }
            $insert_request = rtrim($insert_request, ",");
            $insert_request = $insert_request.") values (";

            foreach($criterias as $key => $value)
            {
                $insert_request = $insert_request.":".$key.",";
            }
            $insert_request = rtrim($insert_request, ",");
            $insert_request = $insert_request.");";

            $insert_res = $this->query(
                $insert_request,
                $criterias
            );

            if($insert_res === false)
                return false;

            return $this->get_last_insert_id();
        }
    }

    private $pdo;

    private $is_ok;
}

class database_factory
{
    public static function get_db()
    {
        global $app;

        if(!self::$db)
        {
            //Instanciation de la base de données
            $db_settings = $app->getContainer()->get("settings")["database"];
            self::$db = new database(
                $db_settings["dsn"],
                $db_settings["username"],
                $db_settings["password"]
            );
        }

        return self::$db;
    }

    private static $db = null;
}
