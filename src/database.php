<?php

class database
{
    public function __construct($dsn, $username, $password)
    {
      try {
        $this->pdo = new PDO($dsn, $username, $password);
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

    public function query($query, $data)
    {
        $prepared = $this->pdo->prepare($query);
        $prepared->execute($data);
        return $prepared;
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
