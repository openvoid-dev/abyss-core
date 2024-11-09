<?php

namespace Abyss\Controller;

use Abyss\Core\Application;
use Abyss\Shade\ShadeCompiler;
use parallel\Events\Error;

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
        $file_path = Application::get_base_path(
            "/app/views/" . $page . ".shade.php"
        );

        if (!file_exists($file_path)) {
            throw new \Exception("View {$page} not found.");
        }

        extract($props);

        // Compile the Shade template
        $compiled = ShadeCompiler::compile(file_get_contents($file_path));

        // Evaluate the compiled PHP code
        eval("?>" . $compiled);
    }

    /**
     * Redirect to another page
     *
     * @param string $page
     * @return void
     **/
    public static function redirect(string $page): void
    {
        header("Location: $page");
        die();
    }
}
