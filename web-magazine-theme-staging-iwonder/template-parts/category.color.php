<?php
/**
 * Convert ANY color → HEX (#RRGGBB)
 */

delete_transient('iw_global_category_css');

function any_color_to_hex($color) {
    $color = trim($color);

    if (preg_match('/^#([a-fA-F0-9]{3})$/', $color, $m)) {
        return '#' . $m[1][0].$m[1][0] . $m[1][1].$m[1][1] . $m[1][2].$m[1][2];
    }

    if (preg_match('/^#([a-fA-F0-9]{6})$/', $color)) {
        return $color;
    }

    if (preg_match('/^#([a-fA-F0-9]{8})$/', $color)) {
        return '#' . substr($color, 1, 6);
    }

    if (preg_match('/rgba?\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)/', $color, $m)) {
        return sprintf("#%02x%02x%02x", $m[1], $m[2], $m[3]);
    }

    return "#000000";
}

/**
 * Convert HEX → "R, G, B"
 */
function hex_to_rgb_commas($hex) {
    $hex = str_replace('#', '', $hex);

    if (!preg_match('/^[a-fA-F0-9]{3}$|^[a-fA-F0-9]{6}$/', $hex)) {
        return "0, 0, 0";
    }

    if (strlen($hex) == 3) {
        $r = hexdec($hex[0] . $hex[0]);
        $g = hexdec($hex[1] . $hex[1]);
        $b = hexdec($hex[2] . $hex[2]);
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }

    return "{$r}, {$g}, {$b}";
}

/**
 * Generate CSS variables for any taxonomy
 */
function iw_generate_taxonomy_css($taxonomy) {

    $css = "";

    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ]);

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {

            if (empty($term) || !isset($term->term_id)) continue;

            $unique = get_field('category__class', "{$taxonomy}_{$term->term_id}");
            if (!$unique) continue;

            $unique = sanitize_title($unique);

            $color = get_field('category_color', "{$taxonomy}_{$term->term_id}");
            if (!$color) continue;

            $hex = any_color_to_hex($color);
            $rgb = hex_to_rgb_commas($hex);

            $css .= "--cat-{$unique}: rgb({$rgb});\n";
            $css .= "--cat-{$unique}-light: color-mix(in srgb, rgb({$rgb}) 15%, white);\n";
        }
    }

    return $css;
}

/**
 * Main CSS generator
 */
function iwonder_category_colors_css() {

    $global_css = get_transient('iw_global_category_css');

    if ($global_css === false) {

        $global_css = "";

        // ✅ All taxonomies in one place
        $taxonomies = [
            'category',
            'resource_type',
            'resource_categories'
        ];

        foreach ($taxonomies as $tax) {
            $global_css .= iw_generate_taxonomy_css($tax);
        }

        set_transient('iw_global_category_css', $global_css, DAY_IN_SECONDS);
    }

    /* ---------- THEME COLOR ---------- */
    $theme_color = '#BB5927';

    // Blog Post
    if (is_singular('post')) {
        $terms = get_the_category();

        if (!empty($terms) && isset($terms[0]->term_id)) {
            $color = get_field('category_color', "category_{$terms[0]->term_id}");
            if ($color) $theme_color = any_color_to_hex($color);
        }
    }

    // Category archive
    elseif (is_category()) {
        $id = get_queried_object_id();
        $color = get_field('category_color', "category_{$id}");

        if ($color) $theme_color = any_color_to_hex($color);
    }

    // Resource single
    elseif (is_singular('resource')) {

        // Priority 1 → resource_type
        $terms = wp_get_post_terms(get_the_ID(), 'resource_type');

        if (!empty($terms) && !is_wp_error($terms)) {
            $color = get_field('category_color', "resource_type_{$terms[0]->term_id}");

            if ($color) {
                $theme_color = any_color_to_hex($color);
            }
        } else {

            // Priority 2 → resource_categories
            $terms = wp_get_post_terms(get_the_ID(), 'resource_categories');

            if (!empty($terms) && !is_wp_error($terms)) {
                $color = get_field('category_color', "resource_categories_{$terms[0]->term_id}");

                if ($color) {
                    $theme_color = any_color_to_hex($color);
                }
            }
        }
    }

    // Resource Type taxonomy
    elseif (is_tax('resource_type')) {
        $obj = get_queried_object();

        if ($obj && isset($obj->term_id)) {
            $color = get_field('category_color', "resource_type_{$obj->term_id}");

            if ($color) $theme_color = any_color_to_hex($color);
        }
    }

    // Resource Categories taxonomy
    elseif (is_tax('resource_categories')) {
        $obj = get_queried_object();

        if ($obj && isset($obj->term_id)) {
            $color = get_field('category_color', "resource_categories_{$obj->term_id}");

            if ($color) $theme_color = any_color_to_hex($color);
        }
    }

    /* ---------- FINAL CSS ---------- */
    $theme_rgb = hex_to_rgb_commas($theme_color);

    $full_css = ":root {\n";
    $full_css .= $global_css;

    $full_css .= "--theme-clr: rgb({$theme_rgb});\n";
    $full_css .= "--theme-clr-values: {$theme_rgb};\n";
    $full_css .= "--theme-clr-rgb: rgb(var(--theme-clr-values));\n";

    $full_css .= "--theme-clr-50:  color-mix(in srgb, var(--theme-clr-rgb) 10%, white);\n";
    $full_css .= "--theme-clr-100: color-mix(in srgb, var(--theme-clr-rgb) 20%, white);\n";
    $full_css .= "--theme-clr-200: color-mix(in srgb, var(--theme-clr-rgb) 30%, white);\n";
    $full_css .= "--theme-clr-300: color-mix(in srgb, var(--theme-clr-rgb) 40%, white);\n";
    $full_css .= "--theme-clr-400: color-mix(in srgb, var(--theme-clr-rgb) 50%, white);\n";
    $full_css .= "--theme-clr-500: var(--theme-clr-rgb);\n";
    $full_css .= "--theme-clr-600: color-mix(in srgb, var(--theme-clr-rgb) 70%, black);\n";
    $full_css .= "--theme-clr-700: color-mix(in srgb, var(--theme-clr-rgb) 60%, black);\n";
    $full_css .= "--theme-clr-800: color-mix(in srgb, var(--theme-clr-rgb) 50%, black);\n";
    $full_css .= "--theme-clr-900: color-mix(in srgb, var(--theme-clr-rgb) 40%, black);\n";
    $full_css .= "--theme-clr-950: color-mix(in srgb, var(--theme-clr-rgb) 30%, black);\n";

    $full_css .= "}\n";

    wp_register_style('iwonder-dynamic', false);
    wp_enqueue_style('iwonder-dynamic');
    wp_add_inline_style('iwonder-dynamic', $full_css);
}

add_action('wp_enqueue_scripts', 'iwonder_category_colors_css', 20);


