<?php

namespace Abyss\Outsider;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        $this->fill($attributes);
    }

    // Set attributes for the model
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public static function query()
    {
        return new QueryBuilder((new static())->getTable());
    }

    public static function find($id)
    {
        return static::query()->where((new static())->primaryKey, '=', $id)->first();
    }

    public function save()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            // Update existing record
            static::query()->update($this->attributes, $this->primaryKey, $this->attributes[$this->primaryKey]);
        } else {
            // Insert new record
            $this->attributes[$this->primaryKey] = static::query()->insert($this->attributes);
        }
    }

    public function delete()
    {
        static::query()->delete($this->primaryKey, $this->attributes[$this->primaryKey]);
    }

    public function getTable()
    {
        return $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }
}
