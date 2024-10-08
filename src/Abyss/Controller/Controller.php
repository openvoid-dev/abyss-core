<?php

namespace Abyss\Controller;

use Abyss\Core\Application;

/**
 * Abstract class for every controller
 */
abstract class Controller
{
    /**
     * Render a page with props
     *
     * @param string $page
     * @param array $props
     * @return void
     */
    public static function view(string $page, array $props = []): void
    {
        extract($props);

        require Application::get_base_path("/app/views/" . $page . ".php");
    }
}
