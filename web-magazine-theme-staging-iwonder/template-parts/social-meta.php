<?php

global $wp;

/* =========================================================
 * CURRENT LANGUAGE (WPML)
 * ========================================================= */

$current_lang = apply_filters('wpml_current_language', NULL);

/* =========================================================
 * OG LOCALE MAPPING
 * OpenGraph uses underscore format
 * ========================================================= */

switch($current_lang){

    case 'hi':
        $og_locale = 'hi_IN';
        break;

    case 'kn':
        $og_locale = 'kn_IN';
        break;

    default:
        $og_locale = 'en_US';
}

/* =========================================================
 * TITLE
 * ========================================================= */

$title = wp_get_document_title();

/* =========================================================
 * DESCRIPTION
 * ========================================================= */

if ( is_singular() && has_excerpt() ) {

    $desc = get_the_excerpt();

} else {

    $desc = __( t('iw_meta_description') );
}

/* Clean description */
$desc = wp_strip_all_tags($desc);

/* =========================================================
 * URL
 * ========================================================= */

if ( is_front_page() || is_home() ) {

    $url = home_url('/');

} elseif ( is_singular() ) {

    $url = get_permalink();

} else {

    $url = home_url( add_query_arg(array(), $wp->request) );
}

/* =========================================================
 * OG TYPE
 * ========================================================= */

$og_type = is_singular() ? 'article' : 'website';

/* =========================================================
 * IMAGE
 * ========================================================= */

$image = get_the_post_thumbnail_url(get_the_ID(),'full');

if(!$image){

    $image = site_url('/wp-content/uploads/2026/05/iwonder-1200x675-1.png?v=2');
}

/* =========================================================
 * WPML LANGUAGES
 * ========================================================= */

$languages = apply_filters(
    'wpml_active_languages',
    NULL,
    array(
        'skip_missing' => 0
    )
);

?>

<!-- ========================================================= -->
<!-- CANONICAL -->
<!-- ========================================================= -->

<link rel="canonical"
      href="<?php echo esc_url($url); ?>">

<!-- ========================================================= -->
<!-- HREFLANG -->
<!-- ========================================================= -->

<?php if ( !empty($languages) ) : ?>

    <?php foreach($languages as $lang) : ?>

        <link rel="alternate"
              hreflang="<?php echo esc_attr($lang['language_code']); ?>"
              href="<?php echo esc_url($lang['url']); ?>">

    <?php endforeach; ?>

<?php endif; ?>

<link rel="alternate"
      hreflang="x-default"
      href="<?php echo esc_url(home_url('/')); ?>">

<!-- ========================================================= -->
<!-- OPEN GRAPH -->
<!-- ========================================================= -->

<meta property="og:type"
      content="<?php echo esc_attr($og_type); ?>">

<meta property="og:site_name"
      content="i wonder...">

<meta property="og:title"
      content="<?php echo esc_attr($title); ?>">

<meta property="og:description"
      content="<?php echo esc_attr($desc); ?>">

<meta property="og:url"
      content="<?php echo esc_url($url); ?>">

<meta property="og:image"
      content="<?php echo esc_url($image); ?>">

<meta property="og:image:secure_url"
      content="<?php echo esc_url($image); ?>">

<meta property="og:image:type"
      content="image/png">

<meta property="og:image:width"
      content="1200">

<meta property="og:image:height"
      content="675">

<!-- ========================================================= -->
<!-- OG LOCALE -->
<!-- ========================================================= -->

<meta property="og:locale"
      content="<?php echo esc_attr($og_locale); ?>">

<meta property="og:locale:alternate"
      content="hi_IN">

<meta property="og:locale:alternate"
      content="kn_IN">

<!-- ========================================================= -->
<!-- TWITTER / X -->
<!-- ========================================================= -->

<meta name="twitter:card"
      content="summary_large_image">

<meta name="twitter:title"
      content="<?php echo esc_attr($title); ?>">

<meta name="twitter:description"
      content="<?php echo esc_attr($desc); ?>">

<meta name="twitter:image"
      content="<?php echo esc_url($image); ?>">