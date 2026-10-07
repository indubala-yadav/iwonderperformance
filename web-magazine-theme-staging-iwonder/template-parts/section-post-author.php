<?php
// Combine all authors
// $resources_authors = get_field('resources_author') ?: [];
// $article_authors   = get_field('article_author') ?: [];
// $all_authors       = array_merge($resources_authors, $article_authors);
// $total_authors     = count($all_authors);

// Combine all authors (raw ACF values for better preview compatibility)
$resources_authors = get_field('resources_author', false, false);
$article_authors   = get_field('article_author', false, false);

$resources_authors = is_array($resources_authors) ? $resources_authors : [];
$article_authors   = is_array($article_authors) ? $article_authors : [];

$all_authors   = array_merge($resources_authors, $article_authors);
$total_authors = count($all_authors);

// Determine layout class
$layout_class = $total_authors > 2 ? 'row row-cols-1 row-cols-md-2 g-3' : 'd-flex flex-column';
?>

<div class="page-authors-area mt-4 <?php echo esc_attr($layout_class); ?>">

    <?php
    foreach ($all_authors as $author) :

        $author_id    = is_object($author) ? $author->ID : $author;
        $author_name  = get_the_title($author_id);
        $author_email = get_field('authors_bio_mail', $author_id);
        $author_image = get_the_post_thumbnail_url($author_id, 'thumbnail');
        $author_link  = get_permalink($author_id);

        // Wrap each author in Bootstrap column if more than 2 authors
        if ($total_authors > 2) echo '<div class="col">';
    ?>
        <div class="d-flex gap-3 align-items-center article-page-author w-100">
            <?php if (!empty($author_image)) : ?>
                <a href="<?php echo esc_url($author_link); ?>" tabindex="0"
                    class="author-popover-trigger"
                    data-bs-toggle="popover"
                    data-bs-html="true"
                    title="<?php echo esc_attr($author_name); ?>"
                    data-bs-content="<?php echo $author_email ? "Email: <a href='mailto:" . esc_attr($author_email) . "'>" . esc_html($author_email) . "</a>" : "No email available."; ?>">
                    <img src="<?php echo esc_url($author_image); ?>"
                        alt="<?php echo esc_attr($author_name); ?>"
                        class="test rounded-1 author-avatar objectfit-cover"
                        width="64" height="64">
                </a>
            <?php endif; ?>

            <div class="author-content">
                <a href="<?php echo esc_url($author_link); ?>" class="author-name text-decoration-none text-dark fw-bold text-uppercase d-block">
                    <?php echo esc_html($author_name); ?>
                </a>
                <?php if ($author_email) : ?>
                    <a href="mailto:<?php echo esc_attr($author_email); ?>" target="_blank" class="author-email fst-italic"><?php echo esc_html($author_email); ?></a>
                <?php endif; ?>
            </div>
        </div>
    <?php
        if ($total_authors > 2) echo '</div>'; // close col
    endforeach;
    ?>
</div>