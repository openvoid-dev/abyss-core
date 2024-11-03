<?php

namespace Abyss\Shade;

use Abyss\Core\Application;
use Error;

class Layout
{
    /**
     * Extract the layout directive
     *
     * @param string &$template
     * @return string|null
     **/
    public static function extract_layout(string &$template): string|null
    {
        if (
            !preg_match(
                '/@layout\(\s*["\'](.+?)["\']\s*\)/',
                $template,
                $matches
            )
        ) {
            return null;
        }

        $template = str_replace($matches[0], "", $template);
        $template = str_replace("@endlayout", "", $template);

        return $matches[1];
    }

    /**
     * Wrap view in extracted layout using the @slot directive
     *
     * @param string $layout
     * @param string &$template
     * @return string
     **/
    public static function wrap_in_layout(
        string $layout,
        string &$template
    ): string {
        // * Get full layout content
        $layout_content = self::get_layout($layout);

        // Replace @slot with template
        $template = str_replace("@slot", $template, $layout_content);

        return $template;
    }

    /**
     * Get layout and compile it to get vanilla php
     *
     * @param string $layout
     * @return string|Error
     **/
    public static function get_layout(string $layout): string|Error
    {
        $layout_file_path = Application::get_base_path(
            "/app/views/layouts/$layout.shade.php"
        );

        if (!file_exists($layout_file_path)) {
            throw new Error("Specified layout does not exist!");
        }

        $layout_content = ShadeCompiler::compile(
            file_get_contents($layout_file_path)
        );

        return $layout_content;
    }
}
