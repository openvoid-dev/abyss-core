<?php

namespace Abyss\Outsider;

abstract class Model
{
    protected $table;
    protected $primary_key = 'id';
    protected $attributes  = [];

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
        return new QueryBuilder((new static())->get_table());
    }

    public static function find($id)
    {
        return static::query()->where((new static())->primary_key, '=', $id)->first();
    }

    public function save()
    {
        if (isset($this->attributes[$this->primary_key])) {
            // Update existing record
            static::query()->update($this->attributes, $this->primary_key, $this->attributes[$this->primary_key]);
        } else {
            // Insert new record
            $this->attributes[$this->primary_key] = static::query()->insert($this->attributes);
        }
    }

    public function delete()
    {
        static::query()->delete($this->primary_key, $this->attributes[$this->primary_key]);
    }

    public function get_table()
    {
        return $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }
}
