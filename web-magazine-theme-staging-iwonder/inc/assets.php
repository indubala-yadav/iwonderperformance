<?php
/**
 * Assets & Enqueuing
 * Scripts, styles, deferring, footer scripts, conditional loading
 */

if (!defined('ABSPATH')) exit;

// Defer all non-critical JS files (except jQuery & admin)
function defer_non_critical_js( $tag, $handle ) {
    if ( is_admin() ) {
        return $tag;
    }

    // Do NOT defer jQuery – breaks many plugins
    if ( in_array( $handle, [ 'jquery-core', 'jquery', 'jquery-migrate' ] ) ) {
        return $tag;
    }

    // Add defer before src
    return str_replace( ' src', ' defer src', $tag );
}
add_filter( 'script_loader_tag', 'defer_non_critical_js', 10, 2 );

function web_magazine_defer_css( $html, $handle, $href, $media ) {
    if ( is_admin() ) {
        return $html;
    }

    $defer_handles = [
        'bootstrap-css',
        'fontawesome-css',
        'web-magazine-layout',
        'web-magazine-style',
        'web-magazine-button',
        'slick-theme-css',
    ];

    if ( ! in_array( $handle, $defer_handles, true ) ) {
        return $html;
    }

    if ( false === strpos( $html, "media='all'" ) && false === strpos( $html, 'media="all"' ) ) {
        return $html;
    }

    $html = str_replace( "media='all'", "media='print' onload=\"this.media='all';this.onload=null;\"", $html );
    $html = str_replace( 'media="all"', 'media="print" onload="this.media=\'all\';this.onload=null;"', $html );

    return $html;
}
add_filter( 'style_loader_tag', 'web_magazine_defer_css', 10, 4 );

/**
 * Enqueue Styles + Scripts (Optimized)
 */
function web_magazine_enqueue_scripts() {

    /* ----------------------------------
       CSS FILES
    ---------------------------------- */

    // Bootstrap CSS (local, optimized)
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/assets/css/bootstrap.min.css',
        [],
        filemtime( get_template_directory() . '/assets/css/bootstrap.min.css' ),
        'all'
    );
    
    wp_enqueue_style(
        'fontawesome-css',
        get_template_directory_uri() . "/assets/fontawesome/css/all.min.css",
        [],
        filemtime( get_template_directory() . '/assets/fontawesome/css/all.min.css' ),
        'all'
    );

    // Layout CSS
    wp_enqueue_style(
        'web-magazine-layout',
        get_template_directory_uri() . "/assets/css/layout.min.css",
        [ 'bootstrap-css' ],
        filemtime( get_template_directory() . '/assets/css/layout.min.css' ),
        'all'
    );

    // Main theme stylesheet (style.css)
    wp_enqueue_style(
        'web-magazine-style',
        get_stylesheet_uri(),
        [ 'bootstrap-css' ],
        filemtime( get_template_directory() . '/style.css' ),
        'all'
    );

      // Button CSS
    wp_enqueue_style(
        'web-magazine-button',
        get_template_directory_uri() . "/assets/css/button.css",
        [ 'bootstrap-css' ],
        filemtime( get_template_directory() . '/assets/css/button.css' ),
        'all'
    );
    // Slick CSS
    wp_enqueue_style(
        'slick-theme-css',
        get_template_directory_uri() . "/assets/css/slick-theme.css",
        [ 'bootstrap-css' ],
        filemtime( get_template_directory() . '/assets/css/slick-theme.css' ),
        'all'
    );



    /* ----------------------------------
       JS FILES
    ---------------------------------- */

    // jQuery (WordPress default)
    wp_enqueue_script( 'jquery' );
    wp_script_add_data( 'jquery', 'strategy', 'defer' );
    wp_script_add_data( 'jquery-core', 'strategy', 'defer' );

    // Header JS
    wp_enqueue_script(
        'header-js',
        get_template_directory_uri() . '/assets/js/header.js',
        [ 'jquery' ],
        filemtime( get_template_directory() . '/assets/js/header.js' ),
        true
    );
    wp_script_add_data( 'header-js', 'strategy', 'defer' );

    // Bootstrap JS
    wp_enqueue_script(
        'bootstrap-js',
        get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js',
        [ 'jquery' ],
        filemtime( get_template_directory() . '/assets/js/bootstrap.bundle.min.js' ),
        true
    );
    wp_script_add_data( 'bootstrap-js', 'strategy', 'defer' );

    // Slick JS
    wp_enqueue_script(
        'slick-js',
        get_template_directory_uri() . '/assets/js/slick.min.js',
        [ 'jquery' ],
        filemtime( get_template_directory() . '/assets/js/slick.min.js' ),
        true
    );
    wp_script_add_data( 'slick-js', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'web_magazine_enqueue_scripts' );

// Disable Gutenberg styles on home/front page
add_action('wp_enqueue_scripts', function () {
    if ( is_front_page() || is_home() ) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
    }
}, 100);

// Defer scripts for performance
add_filter('script_loader_tag', function ($tag, $handle) {
    $defer = [
        'bootstrap-js',
        'slick-js',
        'header-js'
    ];

    if ( in_array($handle, $defer, true) ) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}, 10, 2);

// Enqueue Tagify (keyword/tag management)
function enqueue_tagify_assets() {
    if ( is_front_page() ) {
        return;
    }

    wp_enqueue_script(
        'tagify',
        'https://cdn.jsdelivr.net/npm/@yaireo/tagify',
        [],
        null,
        true
    );

    wp_enqueue_style(
        'tagify-css',
        'https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css'
    );

    $tags = get_tags(['hide_empty' => false]);
    $tag_names = array_map(fn($tag) => $tag->name, $tags);

    wp_localize_script('tagify', 'tagifyData', [
        'whitelist' => $tag_names,
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_tagify_assets');

// Output inline JS for Tagify
function print_tagify_inline_script() {
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('#keywords');
    if (input && typeof Tagify !== 'undefined' && window.tagifyData) {
        const tagify = new Tagify(input, {
            whitelist: tagifyData.whitelist || [],
            enforceWhitelist: false,
            keepInvalidTags: true,
            delimiters: ",",
            originalInputValueFormat: valuesArr => 
                valuesArr.map(item => item.value).join(', ')
        });

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                const plainValues = tagify.value.map(tag => tag.value);
                input.value = plainValues.join(', ');
            });
        }
    }
});
</script>
<?php
}
add_action('wp_footer', 'print_tagify_inline_script');

// Enqueue Gutenberg Lightbox
function enqueue_custom_gutenberg_lightbox() {
    if (is_singular('post')) {
        wp_enqueue_script(
            'custom-gutenberg-lightbox',
            get_stylesheet_directory_uri() . '/assets/js/gutenberg-lightbox.js',
            [],
            '1.0.19',
            true
        );

        wp_enqueue_style(
            'custom-gutenberg-lightbox-style',
            get_stylesheet_directory_uri() . '/assets/css/gutenberg-lightbox.css',
            [],
            '1.0.17'
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_gutenberg_lightbox');

// reCAPTCHA for call-for-articles page only
function load_recaptcha_on_call_for_articles() {
    if (is_page('call-for-articles')) {
        if (function_exists('wpcf7_enqueue_scripts')) {
            wpcf7_enqueue_scripts();
        }
        if (function_exists('wpcf7_enqueue_styles')) {
            wpcf7_enqueue_styles();
        }

        add_action('wp_enqueue_scripts', function() {
            if (class_exists('WPCF7_RECAPTCHA')) {
                $recaptcha = WPCF7_RECAPTCHA::get_instance();
                if (method_exists($recaptcha, 'enqueue_script')) {
                    $recaptcha->enqueue_script();
                }
            }
        });
    } else {
        add_filter('wpcf7_load_js', '__return_false');
        add_filter('wpcf7_load_css', '__return_false');
    }
}
add_action('wp', 'load_recaptcha_on_call_for_articles');

// Footer Scripts
function apu_footer_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        if ($.fn.slick) {
            $('.announcement-slider').slick({
                dots: true,
                arrows: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000
            });
        } else {
            console.warn('Slick JS not loaded.');
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'apu_footer_scripts', 100);

// Loading Screen
function loading_screen() {
    global $post;
    $page_slug = is_page() && isset($post->post_name) ? $post->post_name : '';

    if (
        is_search() ||
        is_post_type_archive(['resource', 'magazine_issue', 'authors_bio']) ||
        is_tax('resource_type') ||
        is_home() ||
        is_category() ||
        is_single() ||
        is_404() ||
        is_page_template('front-page.php') ||
        in_array($page_slug, ['all-articles', 'all-resources', 'old-magazine-issues', 'articles'])
    ) {

        // ✅ CSS (NO CHANGE)
        echo '<style>
            #loading {
                position: fixed;
                display: none;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                text-align: center;
                opacity: 0.7;
                background-color: var(--theme-clr-light);
                z-index: 9999;
            }
            #loader {
                border: 6px solid #f3f3f3;
                border-radius: 50%;
                border-top: 6px solid var(--theme-clr);
                width: 48px;
                height: 48px;
                animation: spin 1s linear infinite;
            }
            .center {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                margin: auto;
            }
            @keyframes spin {
                100% { transform: rotate(360deg); }
            }
        </style>';

        // ✅ HTML
        echo '<div id="loading"><div id="loader" class="center"></div></div>';

        // ✅ JS FIXED (IMPORTANT PART)
        echo '<script>
        jQuery(document).ready(function($) {

            function showLoader() {
                $("#loading").css("display", "flex");
            }

            function hideLoader() {
                $("#loading").hide();
            }

            // ----------------------------------
            // ✅ ONLY FILTER SEARCH FORM
            // ----------------------------------
            $("form.search-form").on("submit", function(e) {

                // Prevent Ctrl+Enter multiple triggers issue
                if (e.originalEvent && e.originalEvent.submitter) {
                    showLoader();
                } else {
                    showLoader();
                }
            });

            // ----------------------------------
            // ✅ PREVENT CTRL + ENTER STUCK ISSUE
            // ----------------------------------
            $(document).on("keydown", function(e) {
                if (e.ctrlKey && e.key === "Enter") {
                    // Let form submit normally, but ensure loader hides if cancelled
                    setTimeout(function() {
                        hideLoader();
                    }, 1500);
                }
            });

            // ----------------------------------
            // ❌ DO NOT SHOW FOR NORMAL LINKS
            // ----------------------------------
            // (Removed global <a> click loader intentionally)

            // ----------------------------------
            // ✅ HIDE ON PAGE LOAD
            // ----------------------------------
            $(window).on("load", function() {
                hideLoader();
            });

        });

        // ----------------------------------
        // ✅ FIX BACK/FORWARD CACHE ISSUE
        // ----------------------------------
        window.addEventListener("pageshow", function(event) {
            var loadingEl = document.getElementById("loading");

            if (loadingEl) {
                loadingEl.style.display = "none";
            }
        });
        </script>';
    }
}
add_action('wp_footer', 'loading_screen');

// PDF Loader
function custom_pdf_loader_markup() {
    ?>
    <!-- Loader Overlay -->
    <div id="pdf-loader" style="display: none;">
        <div class="spinner"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const pdfButtons = document.querySelectorAll('.pgfw-single-pdf-download-button');

        pdfButtons.forEach(button => {
            button.addEventListener('click', function () {
                const loader = document.getElementById('pdf-loader');
                if (loader) {
                    loader.style.display = 'flex';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 1000);
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_pdf_loader_markup');

// CF7 Word Count Script
function cf7_word_count_script() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const counters = document.querySelectorAll(".word-count");

        counters.forEach(counter => {
            const fieldName = counter.getAttribute("data-for");
            const field = document.getElementById(fieldName);

            if (!field) return;

            function updateCount() {
                let text = field.value.trim();
                let words = text.length ? text.split(/\s+/).length : 0;
                counter.textContent = words;
            }

            field.addEventListener("input", updateCount);
            updateCount();
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'cf7_word_count_script');

// Localize Header JS for WPML
function my_headerjs_localize() {
    wp_localize_script(
        'header-js',
        'wpml_strings',
        array(
            'related_links' => esc_html__( 'Related links', 'script-text-domain' ),
            'close_text'    => esc_html__( 'Close', 'script-text-domain' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'my_headerjs_localize' );

// PDF Generator Styles
add_filter('wp_pdf_generator_show_header', '__return_false');
add_filter('wp_pdf_generator_show_footer', '__return_false');

add_filter('wp_pdf_generator_pdf_styles', function($styles) {
    $styles .= "
        .page-break { page-break-before: always; }
        .avoid-break { page-break-inside: avoid; }
        img { max-width: 100%; height: auto; }
    ";
    return $styles;
});


