<?php

namespace Abyss\Shade;

use Abyss\Core\Application;
use Error;

class Component
{
    /**
     * Extract all components and add them to the template
     *
     * @param string &$template
     * @return string
     **/
    public static function add_components(string &$template): string
    {
        $components = self::extract_components($template);

        if (empty($components)) {
            return $template;
        }

        foreach ($components[1] as $component_index => $component) {
            $template = self::add_component_to_template(
                $component,
                $components[0][$component_index],
                $template
            );
        }

        return $template;
    }

    /**
     * Add single component to the template
     *
     * @param string $component
     * @param string $component_directive
     * @param string $template
     * @return string
     **/
    public static function add_component_to_template(
        string $component,
        string $component_directive,
        string $template
    ): string {
        // * Get component content
        $component_content = self::get_component($component);

        // Replace @component directive with component
        $template = str_replace(
            $component_directive,
            $component_content,
            $template
        );

        return $template;
    }

    /**
     * Get component content
     *
     * @param string $component
     * @return string
     **/
    public static function get_component(string $component): string
    {
        $component_file_path = Application::get_base_path(
            "/app/views/components/$component.shade.php"
        );

        if (!file_exists($component_file_path)) {
            throw new Error("Component $component does not exist!");
        }

        $component_content = ShadeCompiler::compile(
            file_get_contents($component_file_path)
        );

        return $component_content;
    }

    /**
     * Extract all components from the shade view
     *
     * @param string &$template
     * @return array|null
     **/
    public static function extract_components(string &$template): array|null
    {
        // * Try to get all components (@component(*))
        if (
            !preg_match_all(
                '/@component\(\s*["\'](.+?)["\'](?:\s*,\s*(\[.*?\]))?\s*\)/s',
                $template,
                $matches
            )
        ) {
            return null;
        }

        return $matches;
    }
}
