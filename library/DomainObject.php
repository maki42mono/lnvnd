<?php
/*
 * Абстрактный класс, который обеспечивает минимальный функционал для работы с сущностями БД как с объектами
 * */


namespace library;


abstract class DomainObject
{
    public array $attributes = [];
    private int|null $id;

    abstract protected static function targetMapper(): Mapper;
    abstract protected function beforeSave();
    abstract public static function findOne(array $raw): DomainObject|null;

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

    public function save(): bool
    {
        $this->beforeSave();
        $this->attributes["created"] = date("Y-m-d H:i:s");
        $mapper = $this->targetMapper();
        $mapper->save($this);

        return true;
    }
}