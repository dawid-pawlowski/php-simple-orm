<?php declare(strict_types = 1);

use ReflectionClass;
use ReflectionProperty;

abstract class Entity {

    protected $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function save(): bool {
        $class = new ReflectionClass($this);
        $tableName = strtolower($class->getShortName());

        $properties = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            // TODO: use prepared statement to insert or update table
        }
    }
}
