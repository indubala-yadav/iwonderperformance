<?php if (has_post_thumbnail()) : ?>
<figure class="wp-block-image size-full featured-img">
<?php the_post_thumbnail('article-web', ['class' => 'img-fluid w-100', 'alt' => get_the_title(), 'loading' => 'eager', 'decoding' => 'async']); ?>
</figure>
<?php else : ?>
<figure class="wp-block-image size-full featured-img">
<img src="https://placehold.co/600x400?text=Apum" class="img-fluid w-100" alt="Placeholder Image">
</figure>
<?php endif; ?>