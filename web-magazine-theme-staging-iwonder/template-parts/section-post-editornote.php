<?php 
$editor_note = get_field('editor_note'); 
if ($editor_note) : 

    // REMOVE unwanted wrapper divs from the ACF field
    $editor_note_clean = preg_replace(
        '/<div class="maxw-720">|<div class="short-note hide-sharedaddy">|<\/div>/i',
        '',
        $editor_note
    );
?>
<div class="mx-5 maxw-720 mx-auto my-5">
    <h2 class="fs-20-24"><?php echo esc_html( t('iw_editor_apos_s_note') ); ?></h2>
    <?php echo wp_kses_post($editor_note_clean); ?>
</div>

<?php endif; ?>
