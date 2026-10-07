<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="<?php echo __( t('iw_meta_description') ); ?>">
<meta name="google" content="notranslate">
<meta name="googlebot" content="notranslate">
    <!-- <meta property="og:image" content="/wp-content/uploads/2026/05/cropped-iwonder_favicon_final.png"> -->
    
   

    <!-- <meta name="description" content="<?php echo esc_attr( get_bloginfo('description') ); ?>"> -->
<meta name="theme-color" content="#fdc37c">
<script src="https://cdn.userway.org/widget.js" data-account="KMbPyNqLt6" defer></script>
 
    <?php wp_head(); ?>

<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "umb0d1rv3a");
</script>

</head>
<body <?php body_class( is_front_page() || is_home() ? array('home-article') : array() ); ?>>
<?php wp_body_open(); ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T3JD2885" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
 
<!-- Header Section -->
<header class="apum-header px-3 headerbgimg theme_header" >
    <!-- Main Header -->
    <div class="container position-relative">
<!-- 		<img src="/wp-content/uploads/2025/11/origami_birds_header.png" class="position-absolute birds-header " alt="birds-header"> -->
 		 <?php get_template_part('assets/images/header-bird'); ?>
        <div class="text-center logo-area d-flex justify-content-between">

        <a href="https://azimpremjiuniversity.edu.in/" target="_blank" class="apu-logo text-start" rel="home">
    <img src="/wp-content/uploads/2025/11/apu-logo.svg" loading="eager" decoding="async"  alt="<?php echo esc_attr(t('iw_azim_premji_university'));?>" width="175" height="86">
</a>

<a href="<?php echo home_url(); ?>" class="ara-logo text-end" rel="home">
    <img src="<?php echo esc_url( t('iw_logo') ); ?>" loading="eager" decoding="async"  alt="<?php echo esc_attr(t('iw_at_right_angles') . ' ' . t('iw_a_resource_for_school_mathematics')); ?>" width="443" height="86">
</a>
        </div>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-xl nav-top-border line-gradint">
            <div class="d-flex align-items-center justify-content-between flex-xl-grow-0 flex-grow-1">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="<?php echo __( t('iw_toggle_navigation') ); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Mobile search button outside collapse -->
                <ul class="mb-langbar d-xl-none d-flex">
                    <li class="search-icon text-center">
							<i class="fa-solid fa-search search-popup-open rounded-3 mb-0 cursor-pointer" role="button" tabindex="0" aria-label="<?php echo esc_attr( t('iw_search_for') ); ?>"></i>
                    </li>
<?php
$language_query_args = array();

if ( isset( $_GET['from'] ) && $_GET['from'] !== '' ) {
    $language_query_args['from'] = absint( wp_unslash( $_GET['from'] ) );
}

if ( isset( $_GET['search_query'] ) && $_GET['search_query'] !== '' ) {
    $language_query_args['search_query'] = sanitize_text_field( wp_unslash( $_GET['search_query'] ) );
}

$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 1 ) );

if ( ! function_exists( 'iwonder_render_language_switcher' ) ) {
	function iwonder_render_language_switcher( $item_class, $link_class, $languages, $language_query_args ) {
		if ( empty( $languages ) ) {
			return;
		}

		$current_lang = '';
		$has_other_languages = count( $languages ) > 1;
		$link_class = trim( $link_class . ( $has_other_languages ? '' : ' no-dropdown' ) );
		?>
		<li class="<?php echo esc_attr( $item_class ); ?>" style="z-index: 1;">
			<a href="<?php echo $has_other_languages ? 'javascript:void(0)' : '#'; ?>"
			   class="<?php echo esc_attr( $link_class ); ?>"
			   aria-haspopup="true" aria-expanded="false">
				<?php foreach ( $languages as $lang ) {
					if ( $lang['active'] ) {
						$current_lang = $lang['translated_name'];
						?>
						<span><?php echo esc_html( $current_lang ); ?></span>
						<img src="/wp-content/uploads/2025/08/dropdownicon.png" width="12" height="7" alt="" aria-hidden="true" />
						<?php
						break;
					}
				} ?>
			</a>

			<?php if ( $has_other_languages ) : ?>
				<ul class="sub-menu">
					<?php foreach ( $languages as $lang ) {
						if ( ! $lang['active'] ) {
							$lang_url = ! empty( $language_query_args ) ? add_query_arg( $language_query_args, $lang['url'] ) : $lang['url'];
							echo '<li><a href="' . esc_url( $lang_url ) . '">' . esc_html( $lang['translated_name'] ) . '</a></li>';
						}
					} ?>
				</ul>
			<?php endif; ?>
		</li>
		<?php
	}
}

if ( ! empty( $languages ) ) :
    iwonder_render_language_switcher( 'lang-menu', 'text-white text-decoration-none d-flex gap-8 align-items-center', $languages, $language_query_args );
?>
<?php endif; ?>
                </ul>
            </div>

 
            <div class="collapse navbar-collapse align-items-center justify-content-between" id="navbarNav">
                <div class="d-xl-flex justify-content-between align-items-xl-center flex-xl-row">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="scroll-home-btn m-2 my-0 d-none d-xl-block" aria-label="<?php echo __( t('iw_call_home') ); ?>">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'navbar-nav',
                        'fallback_cb' => '__return_false',
                        'depth' => 3
                    ));
                    ?>
                </div>

                <ul class="calltoaction-bar text-white d-flex justify-content-end align-items-center gap-1 my-2 my-xl-0 p-0">
                    <li class="text-white text-decoration-none bg-lightwhite">
<?php
$submit_article_page = get_page_by_path('submit-an-article');

$submit_article_url = home_url('/');

if ( $submit_article_page ) {
    $submit_article_id = $submit_article_page->ID;

    if ( has_filter( 'wpml_object_id' ) ) {
        $translated_submit_article_id = apply_filters( 'wpml_object_id', $submit_article_id, 'page', true );
        if ( $translated_submit_article_id ) {
            $submit_article_id = $translated_submit_article_id;
        }
    }

    $submit_article_url = get_permalink( $submit_article_id );
}
?>
                       <a href="<?php echo esc_url( $submit_article_url . '#submitarticle' ); ?>">
						<?php echo __( t('iw_submit_article') ); ?>
					</a>

                    </li>
                    <li class="text-white text-decoration-none bg-lightwhite">
                        <a href="https://forms.office.com/pages/responsepage.aspx?id=OK6aNP56KkW1fkXOvQx6UF8iVSto8sRBlbbPR2QdPhlUOUtEWUYzTVNNN1lLMjFaRUFRMUlLVzQyOS4u&route=shorturl" target="_blank"><?php echo __( t('iw_subscribe_for_free') ); ?></a>
                    </li>

                    <!-- Desktop search button -->
                    <li class="search-popup-open cursor-pointer mb-0">
                            <i class="fa-solid fa-search" role="button" tabindex="0" aria-label="<?php echo esc_attr( t('iw_search_for') ); ?>"></i>
                    </li>

<?php
$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 1 ) );
if ( ! empty( $languages ) ) :
    iwonder_render_language_switcher( 'web-langbar', 'text-white text-decoration-none d-flex gap-8', $languages, $language_query_args );
?>
<?php endif; ?>
                </ul>
            </div>

            <!-- Search Popup (Global for both mobile and desktop) -->
          <!-- Search Popup -->
<div id="searchPopup" class="search-popup">
    
    <!-- Close popup button -->
    <div class="btn-search-cancel cursor-pointer d-flex align-items-center">
        <i class="fa-solid fa-xmark fs-20-40"></i>
        <span><?php echo esc_html( t('iw_cancel') ); ?></span>
    </div>

    <div class="search-popup-content">
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            
            <div class="input-group">
                
                <label for="global-sw" class="visually-hidden">Search</label>

                <input 
                    type="search" 
                    id="global-sw" 
                    class="form-control search-global"
                    placeholder="<?php echo esc_attr__( t('iw_search_for') ); ?>" 
                    value="<?php echo get_search_query(); ?>" 
                    name="s"
                    autocomplete="off"
                >

                <!-- Clear button -->
                <button 
    type="button"
    onclick="return false;"
    id="clear-search-btn"
    class="clear-search-btn me-3 cursor-pointer z-99"
    style="display: <?php echo get_search_query() ? 'block' : 'none'; ?>;"
    aria-label="<?php echo esc_html( t('iw_clear_search') ); ?>"
>
    <i class="fa-solid fa-xmark"></i>
</button>

                <!-- Submit -->
                <button type="submit" class="btn">
                    <i class="fas fa-search"></i>
                    <span class="visually-hidden">Search</span>
                </button>

            </div>
        </form>
    </div>
</div>
        </nav>
    </div>
</header>

<?php //wp_body_open(); 
