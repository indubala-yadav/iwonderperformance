  
   <?php
$key_takeawys = get_field('key_takeawys'); 

if ($key_takeawys) : 

    // Remove ONLY the wrapper opening DIVs — not all closing </div>
    $key_takeawys_clean = preg_replace(
        [
            '/<div class="maxw-720">/i',
            '/<div class="short-note hide-sharedaddy">/i'
        ],
        '',
        $key_takeawys
    );

    // Remove only matching closing </div> for those wrappers
    // (the last 2 closing </div> in the field)
    $key_takeawys_clean = preg_replace('/<\/div>\s*<\/div>$/i', '', $key_takeawys_clean);
?>
<div class="key_wrap mx-5 maxw-720 mx-auto my-5">
    <div class="key_takeawys d-flex flex-column-reverse flex-md-row gap-4">

        <div class="d-flex flex-column gap-3">
            <h4 class="fs-20-24 mb-0 text-uppercase">
				<?php echo esc_html( __('Key takeaways', 'your-text-domain') ); ?>
			</h4>

			 <?php 
        if (has_post_thumbnail()) {
            the_post_thumbnail('large', [
                'class' => 'img-fluid align-self-baseline ',
                'alt'   => get_the_title()
            ]);
        } else {
        ?>
            <img 
                src="https://placehold.co/600x400/cccccc/1f4d7c?text=Key%20Takeaways" 
                class="img-fluid align-self-baseline"
                alt="Placeholder image">
        <?php } ?>
            <?php echo wp_kses_post($key_takeawys_clean); ?>
        </div>

       

    </div>
</div>
<?php endif; ?>