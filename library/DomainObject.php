<?php
/*
 * Абстрактный класс, который обеспечивает минимальный функционал для работы с сущностями БД как с объектами
 * */


namespace library;


abstract class DomainObject
{
    public array $attributes = [];
    private int|null $id;

    public function __construct(int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

//    protected static function findAllByMapper(Mapper $mapper): array
//    {
//        return $mapper->findAll();
//    }

//    protected static function findInRangeByMapper(int $rows_count, int $start_from, Mapper $mapper): array
//    {
//        return $mapper->findInRange($rows_count, $start_from);
//    }

    public function save(): bool
    {
        $this->beforeSave();
        $this->attributes["created"] = date("Y-m-d H:i:s");
        $mapper = $this->targetMapper();
        $mapper->save($this);

        return true;
    }

//    public function delete(): bool
//    {
//        $mapper = $this->targetMapper();
//        if (! isset($this->id)) {
//            return false;
//        }
//
//        return $mapper->delete($this);
//    }

//    protected static function getTotalCountByMapper(Mapper $mapper): int
//    {
//        return $mapper->getTotalCount();
//    }

    abstract protected static function targetMapper(): Mapper;

//    abstract public static function findAll(): array;

//    abstract public static function findInRange(int $rows_count, int $start_from): array;

//    abstract public static function getTotalCount(): int;

    abstract protected function beforeSave();

    abstract public static function findOne(array $raw): DomainObject|null;
}