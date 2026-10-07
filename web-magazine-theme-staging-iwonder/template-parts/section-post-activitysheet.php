<?php if (get_field('acknowledgements')): ?>
<div class="maxw-720 mx-auto my-5">

    <h2 class="fs-20-24 ">
        <?php echo esc_html( t('iw_acknowledgements') ); ?> 
	
    </h2>

    <div class="activity-sheets-content">
        <?php the_field('acknowledgements'); ?>
    </div>

</div>
<?php endif; ?>
