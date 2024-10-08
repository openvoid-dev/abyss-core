<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;

class Serve
{
    public function handle($host = "0.0.0.0", $port = "8383")
    {
        $public_dir = Application::get_base_path("/public");

        echo "Abyss development server started: http://{$host}:{$port}\n";
        passthru("php -S {$host}:{$port} -t {$public_dir}");
    }
}
