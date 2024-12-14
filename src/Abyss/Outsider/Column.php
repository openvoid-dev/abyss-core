<?php

namespace Abyss\Outsider;

class Column
{
    /**
     * Column name
     *
     * @var string;
     */
    public $name;

    /**
     * Column type
     *
     * @var string
     */
    public $type;

    /**
     * Is column auto increment
     *
     * @var bool
     */
    public $auto_increment;

    /**
     * Is column primary
     *
     * @var bool
     */
    public $is_primary;

    /**
     * Can column be nullable
     *
     * @var bool
     */
    public $nullable = false;

    /**
     * Default value for a column
     *
     * @var null|string
     */
    public $default = null;

    /**
     * Is column unique
     *
     * @var bool
     */
    public bool $unique = false;

    /**
     * Is column a foreign key
     *
     * @var bool
     */
    public bool $is_foreign_key = false;

    /**
     * Foreign key table
     *
     * @var string|null
     */
    public ?string $foreign_key_table = null;

    /**
     * Construct new column
     *
     * @param string $name
     * @param string $type
     * @param bool $auto_increment
     * @param bool $is_primary
     */
    public function __construct(
        string $name,
        string $type,
        bool $auto_increment,
        bool $is_primary = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->auto_increment = $auto_increment;
        $this->is_primary = $is_primary;
    }

    /**
     * Set column to be nullable
     *
     * @return self
     */
    public function nullable(): self
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * Add default value for a column
     *
     * @param mixed $default_value
     * @return self
     */
    public function default(mixed $default_value): self
    {
        // Handle CURRENT_TIMESTAMP without quotes
        if (
            strtoupper($default_value) === "CURRENT_TIMESTAMP" ||
            strtoupper($default_value) ===
                "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ) {
            $this->default = $default_value;
        } elseif (is_string($default_value)) {
            // Wrap string default values in single quotes
            $this->default = "'" . $default_value . "'";
        } elseif (is_bool($default_value)) {
            // Convert boolean to 0 or 1
            $this->default = $default_value ? 1 : 0;
        } else {
            // For numbers or other types, no quotes are needed
            $this->default = $default_value;
        }

        return $this;
    }

    /**
     * Set column to unique
     *
     * @return self
     */
    public function unique(): self
    {
        $this->unique = true;

        return $this;
    }

    /**
     * Set column to be a foreign key
     *
     * @param string $table
     * @return static
     */
    public function foreign_key($table)
    {
        $this->is_foreign_key = true;
        $this->foreign_key_table = $table;

        return $this;
    }

    /**
     * Get column in sql query
     *
     * @return string
     */
    public function to_sql()
    {
        $sql = "{$this->name} {$this->type}";

        if ($this->is_primary) {
            $sql .= " PRIMARY KEY";
        }

        if ($this->auto_increment) {
            $sql .= " AUTOINCREMENT";
        }

        if ($this->nullable) {
            $sql .= " NULL";
        } else {
            $sql .= " NOT NULL";
        }

        if ($this->default !== null) {
            $sql .= " DEFAULT {$this->default}";
        }

        if ($this->unique) {
            $sql .= " UNIQUE";
        }

        return $sql;
    }
}
