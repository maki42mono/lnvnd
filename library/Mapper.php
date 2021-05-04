<?php


namespace library;


abstract class Mapper
{
    private \PDO $pdo;
    private bool $test_mode = false;
    private string $table_name;

    abstract protected function targetTable(): string;

    abstract protected function doCreateObject(array $raw): DomainObject;

    public function __construct()
    {
        $this->pdo = DB::instance()->get("pdo");
        $this->test_mode = DB::instance()->get("test_mode") ?? false;
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

    public function findAll(): array|null
    {
        if (DB::instance()->get("empty_commands")) {
            return null;
        }

        $sth = $this->pdo->prepare("SELECT * FROM {$this->table_name}");

        $sth->execute();
        $rows = $sth->fetchAll();
        $sth->closeCursor();

        if (!is_array($rows)) {
            return null;
        }

        $objects = [];
        foreach ($rows as $row) {
            $objects[] = $this->doCreateObject($row);
        }

        return $objects;
    }

    public function save(DomainObject $object): bool
    {
        if ($this->test_mode) {
            return true;
        }
//        todo: обработать ошибку
        if (!is_array($object->attributes)) {
            throw new \Exception();
        }

        $sql_value_names = $sql_new_values = "";
        $delimit = ", ";
        foreach ($object->attributes as $key => $value) {
            if (isset($value)) {
                $sql_value_names .= $delimit . $key;
                $sql_new_values .= "{$delimit}'{$value}'";
            }
        }

        $sql_value_names = substr($sql_value_names, 2, mb_strlen($sql_value_names) - 2);
        $sql_new_values = substr($sql_new_values, 2, mb_strlen($sql_new_values) - 2);

        $sth = $this->pdo
            ->prepare("INSERT INTO {$this->table_name} ({$sql_value_names}) VALUES ({$sql_new_values})");
        $res = $sth->execute();
        $object->setId($this->pdo->lastInsertId());

        return $res;
    }

    public function update(DomainObject $object): bool
    {
//        todo: обработать ошибку
        if (!is_array($object->attributes)) {
            throw new \Exception();
        }

        $update_values = "";
        $delimit = ", ";
        foreach ($object->attributes as $key => $value) {
            if (isset($value) && $key != "id") {
                $update_values .= "{$delimit}{$key} = '$value'";
            }
        }

        $update_values = substr($update_values, 2, mb_strlen($update_values) - 2);


        $sth = $this->pdo->prepare(
            "UPDATE {$this->table_name} SET {$update_values} WHERE id={$object->getId()}"
        );

//        $res = $sth->execute();

        return $sth->execute();
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
}