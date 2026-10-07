<?php

/**
 * Redirect Jetpack share query URLs to the clean canonical URL.
 *
 * Examples:
 * /example/?share=facebook
 * /example/?share=x
 * /example/?nb=1
 *
 * Note:
 * This runs inside WordPress/PHP, so it does NOT prevent the request
 * from consuming a PHP worker. Use Cloudflare/web-server blocking
 * for crawler traffic if PHP exhaustion is the problem.
 */
function apu_handle_share_query_redirect()
{
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }

    if (isset($_GET['share']) || isset($_GET['nb'])) {
        $clean_url = remove_query_arg(
            array('share', 'nb')
        );

        wp_safe_redirect($clean_url, 301);
        exit;
    }
}

add_action('template_redirect', 'apu_handle_share_query_redirect', 1);


function apu_disable_jetpack_auto_sharing()
{

    remove_filter('the_content', 'sharing_display', 19);
    remove_filter('the_excerpt', 'sharing_display', 19);
}
add_action('wp', 'apu_disable_jetpack_auto_sharing');
