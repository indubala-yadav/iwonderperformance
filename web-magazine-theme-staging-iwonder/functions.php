<?php

/**
 * iWonder Theme Functions - Modular Version
 *
 * This file loads theme functionality from the /inc/ directory.
 * Performance and maintainability optimized.
 */

// Define directory constants
define('IW_THEME_DIR', get_template_directory());
define('IW_THEME_URL', get_template_directory_uri());
define('IW_INC_DIR', IW_THEME_DIR . '/inc');

/**
 * 1. Setup & Security
 * Security headers, theme support, localization, and breadcrumbs.
 */
require_once IW_INC_DIR . '/setup.php';

/**
 * 6. Dynamic CSS Variables
 * Loaded from template-parts for category coloring.
 */
get_template_part('template-parts/category.color');

/**
 * 2. Assets & Footer Scripts
 * Enqueues for CSS/JS, deferring scripts, and dynamic footer logic.
 */
require_once IW_INC_DIR . '/assets.php';

/**
 * 3. Custom Post Types & Taxonomies
 * Definitions for Resources, Magazine Issues, Authors, and keywords.
 */
require_once IW_INC_DIR . '/cpts.php';

/**
 * 4. Helper Functions & Business Logic
 * Optimized category displays, author links, and redirection logic.
 */
require_once IW_INC_DIR . '/helpers.php';

/**
 * 5. Admin Tools (CSV Export)
 * Custom dashboard tools for content exports.
 */
require_once IW_INC_DIR . '/csv-export.php';



// Optional legacy plugin template parts
get_template_part('template-parts/post-review');

get_template_part('template-parts/share-query-noindex');
/**
 * Note: If you add new custom functionality, prefer creating a new file
 * in the /inc/ directory and requiring it here to keep functions.php clean.
 */
/**
 * Disable Jetpack Sharing automatic insertion into post content.
 *
 * Sharing buttons are displayed manually using sharing_display().
 */
