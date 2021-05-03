<?php


namespace library;


abstract class Mapper
{
    private \PDO $pdo;
    private string $table_name;

    abstract protected function targetTable(): string;

    public function __construct()
    {
        $db = DB::instance();
        $this->pdo = $db->get("pdo");
        $this->table_name = $this->targetTable();

        $check_if_table_dont_exists = function () {
            $sth = $this->pdo->prepare("SHOW TABLES LIKE '{$this->table_name}'");
            $sth->execute();
            $res = $sth->fetchAll();
            $sth->closeCursor();
            return (count($res) == 0);
        };
        if ($check_if_table_dont_exists()) {
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
        $delimit = "";
        foreach ($object->attributes as $key => $value) {
            if (isset($value)) {
                $sql_value_names .= $delimit . $key;
                $sql_new_values .= "{$delimit}'{$value}'";
                if ($is_first) {
                    $is_first = false;
                    $delimit = ", ";
                }
            }
        }

        $sth = $this->pdo
            ->prepare("INSERT INTO {$this->table_name} ({$sql_value_names}) VALUES ({$sql_new_values})");
        $res = $sth->execute();
        $object->setId($this->pdo->lastInsertId());

        return $res;
    }

    public function findOneByMapper(array $search_raw): DomainObject|null
    {
        $object_raw = $this->getRawDataWhereAnd($search_raw);

        if ($object_raw) {
            return $this->doCreateObject($object_raw);
        }
        return null;
    }

    public function hasOneByMapper(array $search_raw): bool
    {
        $object_raw = $this->getRawDataWhereAnd($search_raw);
        return (bool)$object_raw;
    }

    private function getRawDataWhereAnd(array $search_raw): array|bool
    {
        $sql_where_and = "";
        foreach ($search_raw as $name => $value) {
            $sql_where_and .= "{$name}='{$value}' AND ";
        }
        $sql_where_and = substr($sql_where_and, 0, mb_strlen($sql_where_and) - 5);
        $sql = "SELECT * FROM {$this->table_name} WHERE {$sql_where_and}";
        $sth = $this->pdo
            ->prepare($sql);
        $sth->execute();

        $object_raw = $sth->fetch();
        $sth->closeCursor();

        return $object_raw;
    }

    abstract protected function doCreateObject(array $raw): DomainObject;
}