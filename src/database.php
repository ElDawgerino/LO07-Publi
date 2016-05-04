<?php

class database
{
    public function __construct($server, $name, $username, $password)
    {
        $this->mysqli = new mysqli($server, $username, $password, $name);
        $this->is_ok = true;

        if ($this->mysqli->connect_errno)
        {
            //Erreur lors de la connexion
            $this->is_ok = false;
        }
    }

    public function is_ok()
    {
        return $this->is_ok;
    }

    public function query($query)
    {
        return $this->mysqli->query($query);
    }

    public function escape_string($str)
    {
        return $this->mysqli->real_escape_string($str);
    }

    private $mysqli;

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
                $db_settings["server"],
                $db_settings["name"],
                $db_settings["username"],
                $db_settings["password"]
            );
        }

        return self::$db;
    }

    private static $db = null;
}
