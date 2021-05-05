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
            if (Mode::instance()->get("mode") == Mode::TEST_MODE) {
                $this->attributes["id"] = -1;
            }
            $mapper->save($this);
        }

        return true;
    }

    public function delete(): bool
    {
        $mapper = $this->targetMapper();
        if (! isset($this->id)) {
            return false;
        }

        return $mapper->delete($this);

    }
}