<?php


namespace library;


class DB extends Singleton
{
    public function __construct()
    {
        try {
            $conf = Conf::instance()->get("db");
            $this->values["pdo"] = new \PDO("{$conf["type"]}:host={$conf["host"]};dbname={$conf["db_name"]}",
                $conf["username"], $conf["password"]);
        } catch (\Exception $e) {
            throw new \Exception("ОШИБКА ПРИ ПОДКЛЮЧЕНИИ К БД. ПРОВЕРЬТЕ НАСТРОЙКИ В main-local.php", $e->getCode());
        }
    }
}