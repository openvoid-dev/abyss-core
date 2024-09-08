<?php
/**
 * Abstract class for every controller
 */

namespace Abyss\Controller;

use Abyss\Core\Application;

abstract class Controller
{
    public static function view(string $page, array $props = [])
    {
        // extract($props);

        require Application::get_base_path("/app/views/" . $page . '.php');
    }
}
