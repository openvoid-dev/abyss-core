<?php

namespace Abyss\Outsider;

abstract class Migration
{
    /**
     * List of tables this table is dependent on
     *
     * @var array
     **/
    public array $depends_on = [];

    /**
     * Create a table
     *
     * @return void
     */
    abstract public function up();

    /**
     * Drop table
     *
     * @return void
     */
    abstract public function down();
}
