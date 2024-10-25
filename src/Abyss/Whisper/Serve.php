<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;

class Serve
{
    /**
     * Start the Abyss server and tailwind server
     *
     * @param string $host
     * @param string $port
     * @return void
     **/
    public function handle($host = "0.0.0.0", $port = "8383"): void
    {
        echo "Abyss development server started: http://{$host}:{$port}\n";
        echo "Started tailwindcss watcher in the background.\n";

        $public_dir = Application::get_base_path("/public");

        // * Run Tailwind watcher in the background
        $tailwindCommand =
            "tailwindcss -i ./app/resources/css/main.css -o ./public/css/main.css --watch > /dev/null 2>&1 &";
        passthru($tailwindCommand);

        // * Run the PHP built-in server
        passthru("php -S {$host}:{$port} -t {$public_dir}");
    }
}
