<?php

namespace Abyss\Shade;

use Abyss\Core\Application;
use Error;

class ShadeCompiler
{
    protected static $layout;

    /**
     * Compile Shade template to php
     *
     * @param string $template
     * @return string
     **/
    public static function compile(string $template): string
    {
        $layout = self::extract_layout($template);

        // * Replace `{{ $variable }}` with PHP echo
        $template = preg_replace(
            "/\{\{\s*(.+?)\s*\}\}/",
            '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>',
            $template
        );

        // * Handle basic directives (like @if, @endif)
        $template = preg_replace(
            "/@if\s*\((.+?)\)/",
            '<?php if ($1): ?>',
            $template
        );
        $template = str_replace("@endif", "<?php endif; ?>", $template);

        // * Replace (@php, @endphp)
        $template = preg_replace("/@php/", "<?php", $template);
        $template = str_replace("@endphp", "?>", $template);

        // * Replace (@foreach, @endforeach)
        $template = preg_replace(
            "/@foreach\s*\((.+?)\)/",
            '<?php foreach ($1): ?>',
            $template
        );
        $template = str_replace(
            "@endforeach",
            "<?php endforeach; ?>",
            $template
        );

        // * Replace (@for, @endfor)
        $template = preg_replace(
            "/@for\s*\((.+?)\)/",
            '<?php for ($1): ?>',
            $template
        );
        $template = str_replace("@endfor", "<?php endfor; ?>", $template);

        if (!empty($layout)) {
            $template = self::wrap_in_layout($layout, $template);
        }

        return $template;
    }

    // Method to extract the layout directive
    protected static function extract_layout(string &$template): string|null
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

    protected static function wrap_in_layout(
        string $layout,
        string &$template
    ): string {
        $layout_content = self::get_layout($layout);

        // Replace @slot with template
        $template = str_replace("@slot", $template, $layout_content);

        return $template;
    }

    // Getter for layout
    public static function get_layout(string $layout): ?string
    {
        $layout_file_path = Application::get_base_path(
            "/app/views/layouts/$layout.shade.php"
        );

        if (!file_exists($layout_file_path)) {
            throw new Error("Specified layout does not exist!");
        }

        $layout_content = self::compile(file_get_contents($layout_file_path));

        return $layout_content;
    }
}
