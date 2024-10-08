<?php

namespace Abyss\Outsider;

class Column
{
    /**
     * Column name
     *
     * @var string;
     */
    protected $name;

    /**
     * Column type
     *
     * @var string
     */
    protected $type;

    /**
     * Is column auto increment
     *
     * @var bool
     */
    protected $auto_increment;

    /**
     * Is column primary
     *
     * @var bool
     */
    protected $is_primary;

    /**
     * Can column be nullable
     *
     * @var bool
     */
    protected $nullable = false;

    /**
     * Default value for a column
     *
     * @var null|string
     */
    protected $default = null;

    /**
     * Is column unique
     *
     * @var bool
     */
    protected $unique = false;

    /**
     * Construct new column
     *
     * @param string $name
     * @param string $type
     * @param bool $auto_increment
     * @param bool $is_primary
     */
    public function __construct(
        $name,
        $type,
        $auto_increment,
        $is_primary = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->auto_increment = $auto_increment;
        $this->is_primary = $is_primary;
    }

    /**
     * Set column to be nullable
     *
     * @return static
     */
    public function nullable()
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * Add default value for a column
     *
     * @param mixed $default_value
     * @return static
     */
    public function default($default_value)
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
     * @return static
     */
    public function unique()
    {
        $this->unique = true;

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

        if ($this->auto_increment) {
            $sql .= " AUTO_INCREMENT";
        }

        if ($this->is_primary) {
            $sql .= " PRIMARY KEY";
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
