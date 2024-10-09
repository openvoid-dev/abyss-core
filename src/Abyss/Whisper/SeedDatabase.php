<?php

namespace Abyss\Whisper;

use App\Database\Seeders\DatabaseSeeder;

class SeedDatabase
{
    /**
     * Seed database
     *
     * @return void
     **/
    public function run(): void
    {
        echo "Running db seeder...\n";

        // * Init DatabaseSeeder and run it
        $db_seeder = new DatabaseSeeder();
        $db_seeder->run();

        echo "Done with seeding database.\n";
    }
}
