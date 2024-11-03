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

        // * Start the PHP Server
        $php_server_command = "php -S {$host}:{$port} -t {$public_dir}";

        // * Start the Tailwindcss watcher
        $tailwind_watcher_command =
            "tailwindcss -i ./app/resources/css/main.css -o ./public/css/main.css --watch";

        // * Run both commands concurrently
        $process = proc_open(
            "($php_server_command) & ($tailwind_watcher_command)",
            [STDIN, STDOUT, STDERR],
            $pipes
        );

        if (is_resource($process)) {
            proc_close($process);
        }
    }
}
