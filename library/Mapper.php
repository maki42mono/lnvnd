<?php


namespace library;


abstract class Mapper
{
    private $pdo;
    private string $table_name;

    abstract protected function targetTable(): string;

    public function __construct()
    {
        $db = DB::instance();
        var_dump($db);
        $this->pdo = $db->get("pdo");
        $this->table_name = $this->targetTable();

        if (!$this->checkIfTableExists()) {
            throw new \Exception("Создайте таблицу {$this->table_name}");
        }
    }

    public function save(DomainObject $object): bool
    {
        //        todo: обработать ошибку
        if (! is_array($object->attributes)) {
            throw new \Exception();
        }

        $sql_value_names = "";
        $sql_new_values = "";
        $is_first = true;
        $delim = "";
        foreach ($object->attributes as $key => $value) {
            if (isset($value) && !is_null($value) && $key != "id") {
                $sql_value_names .= $delim . $key;
                $sql_new_values .= "{$delim}'{$value}'";
                if ($is_first) {
                    $is_first = false;
                    $delim = ", ";
                }
            }
        }

        $sth = $this->pdo
            ->prepare("INSERT INTO {$this->table_name} ({$sql_value_names}) VALUES ({$sql_new_values})");
        $res = $sth->execute();
        $object->setId($this->pdo->lastInsertId());

        $sth->debugDumpParams();

        return $res;
    }

    private function checkIfTableExists(): bool
    {
        $sth = $this->pdo->prepare("SHOW TABLES LIKE '{$this->table_name}'");
        $sth->execute();
        $res = $sth->fetchAll();
        $sth->closeCursor();
        return (count($res) > 0);
    }


}