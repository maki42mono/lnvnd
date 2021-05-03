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
        $mapper = $this->targetMapper();
        $date_time = date("Y-m-d H:i:s");

        if ($this->getId()) {
            $this->attributes["updated"] = $date_time;
            $mapper->update($this);
        } else {
            $this->attributes["created"] = $date_time;
            $mapper->save($this);
        }

        return true;
    }
}