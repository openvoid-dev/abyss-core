<?php

namespace Abyss\Shade;

class ShadeCompiler
{
    /**
     * Layout of a page
     *
     * @var null|string
     **/
    protected static $layout;

    /**
     * Compile Shade template to vanilla php
     *
     * @param string $template
     * @return string
     **/
    public static function compile(string $template): string
    {
        // * Extract layout
        $layout = Layout::extract_layout($template);

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

        // * Add all components
        $template = Component::add_components($template);

        // * If layout exists, wrap it in it
        if (!empty($layout)) {
            $template = Layout::wrap_in_layout($layout, $template);
        }

        // * Return plain vanilla php
        return $template;
    }

    // protected static function
}
