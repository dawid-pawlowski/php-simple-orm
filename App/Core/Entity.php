<?php declare(strict_types = 1);

namespace App\Core;

use ReflectionClass;
use ReflectionProperty;
use App\Database\Database;

abstract class Entity {

    private static Database $database;
    protected int $id = 0;

    public function __construct(Database $database) {
        static::$database = $database;
    }

    public function save() {
        $class = new ReflectionClass($this);
        $table = strtolower($class->getShortName());

        $properties = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $properties[$propertyName] = $this->{$propertyName};
        }

        if ($this->id != 0) {
            // create set string - append " = ?" to each element
            $set = implode(', ', array_map(fn($e) => $e . ' = ?', array_keys($properties)));
            static::$database->run("UPDATE {$table} SET {$set} WHERE {$table}.id = {$this->id}", array_values($properties));
        } else {
            // create columns string
            $columns = implode(', ', array_keys($properties));
            // create "?" placeholders for properties
            $values = implode(', ', array_fill(0, count($properties), '?'));
            static::$database->run("INSERT INTO {$table} ({$columns}) VALUES ({$values})", array_values($properties));
            // set entity id
            $this->id = static::$database->lastInsertId();
        }

    }

    private static function map(array $object): static {
        $class = new ReflectionClass(get_called_class());
        $entity = $class->newInstance(static::$database);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $property) {
            if (isset($object[$property->getName()])) {
                $property->setValue($entity, $object[$property->getName()]);
            }
        }

        return $entity;
    }

    public static function find(int $id): static {
        $class = new ReflectionClass(get_called_class());
        $table = strtolower($class->getShortName());

        $result = self::$database->run("SELECT * FROM {$table} WHERE {$table}.id = ? LIMIT 1", [$id])->fetch();
        return static::map($result);
    }

    public function getId(): int {
        return $this->id;
    }
}
