<?php if (get_field('references')): ?>
    <div  id="reference-section" class="maxw-720 mx-auto my-5 ">
	<h2 class="add-motify fs-20-24">
			<?php echo esc_html( t('iw_references') ); ?>
		</h2>
        <div class="references-content">
            <?php the_field('references'); ?>
        </div>
    </div>
<?php endif; ?>
