<?php

namespace Abyss\Outsider;

abstract class Migration
{
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
