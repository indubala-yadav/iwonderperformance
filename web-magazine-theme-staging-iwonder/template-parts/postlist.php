
<?php
/*
Template Name: Post Language Report (WPML Fixed)
*/

$post_types = ['post', 'resource'];

$languages = [
    'en'    => 'English',
    'hi'    => 'Hindi',
    'kn-in' => 'Kannada'
];


/*
|--------------------------------------------------------------------------
| Export CSV
|--------------------------------------------------------------------------
*/
if (isset($_GET['export_excel']) && $_GET['export_excel'] == '1') {

    if (ob_get_length()) {
        ob_end_clean();
    }

    nocache_headers();

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=post-language-report.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // UTF-8 BOM
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, [
        'Post Name',
        'Category Name',
        'Post Type',
        'Language',
        'URL'
    ]);

    foreach ($post_types as $type) {

        foreach ($languages as $lang_code => $lang_name) {

            do_action('wpml_switch_language', $lang_code);

            $query = new WP_Query([
                'post_type'        => $type,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'suppress_filters' => false
            ]);

            while ($query->have_posts()) {

                $query->the_post();

                if ($type === 'resource') {
                    $terms = get_the_terms(get_the_ID(), 'resource_categories');
                } else {
                    $terms = get_the_category();
                }

                $category_names = [];

                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $category_names[] = $term->name;
                    }
                }

                // Raw DB title
                $title = get_post_field('post_title', get_the_ID(), 'raw');

                $title = html_entity_decode(
                    $title,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $title = wp_strip_all_tags($title);

                $type_label = ($type === 'post') ? 'Article' : 'Resource';

fputcsv($output, [
    $title,
    implode(', ', $category_names),
    $type_label,
    $lang_name,
    get_permalink()
]);
            }

            wp_reset_postdata();
        }
    }

    do_action('wpml_switch_language', null);

    fclose($output);
    exit;
}

get_header();
?>

<main id="primary" class="apum-section position-relative section-pad pt-5 pt-lg-4 pb-200 post-list-section">

    <div class="container apum-innerpage-body bg-white pt-80 pb-0 rounded">

        <h1>Post & Resources Report</h1>

        <p style="margin-bottom:20px;">
            <a href="<?php echo esc_url(add_query_arg('export_excel', '1')); ?>"
               style="display:inline-block;padding:10px 20px;background:#2271b1;color:#fff;text-decoration:none;border-radius:4px;">
                Download CSV
            </a>
        </p>

        <?php foreach ($post_types as $type) : ?>

            <section style="margin-top:40px; padding:20px; border:1px solid #ddd;">

                <h2><?php echo esc_html(strtoupper($type)); ?></h2>

                <?php foreach ($languages as $lang_code => $lang_name) : ?>

                    <?php

                    do_action('wpml_switch_language', $lang_code);

                    $query = new WP_Query([
                        'post_type'        => $type,
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'orderby'          => 'title',
                        'order'            => 'ASC',
                        'suppress_filters' => false
                    ]);
                    ?>

                    <div style="margin-top:20px;">

                        <h3><?php echo esc_html($lang_name); ?></h3>

                        <?php if ($query->have_posts()) : ?>

                            <table class="table table-bordered" style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="border:1px solid #ddd;padding:10px;">Post Name</th>
                                        <th style="border:1px solid #ddd;padding:10px;">Category Name</th>
                                        <th style="border:1px solid #ddd;padding:10px;">Post Type</th>
                                        <th style="border:1px solid #ddd;padding:10px;">URL</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php while ($query->have_posts()) : $query->the_post(); ?>

                                    <?php

                                    if ($type === 'resource') {
                                        $terms = get_the_terms(get_the_ID(), 'resource_categories');
                                    } else {
                                        $terms = get_the_category();
                                    }

                                    $category_names = [];

                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        foreach ($terms as $term) {
                                            $category_names[] = $term->name;
                                        }
                                    }

                                    $title = html_entity_decode(
                                        wp_strip_all_tags(get_the_title()),
                                        ENT_QUOTES | ENT_HTML5,
                                        'UTF-8'
                                    );
                                    ?>

                                    <tr>

                                        <td style="border:1px solid #ddd;padding:10px;">
                                           
                                                <?php echo esc_html($title); ?>
                                           
                                        </td>

                                        <td style="border:1px solid #ddd;padding:10px;">
                                            <?php echo !empty($category_names) ? esc_html(implode(', ', $category_names)) : '-'; ?>
                                        </td>
<td style="border:1px solid #ddd;padding:10px;">
    <?php
    if ($type === 'post') {
        echo 'Article';
    } elseif ($type === 'resource') {
        echo 'Resource';
    } else {
        echo esc_html(ucfirst($type));
    }
    ?>
</td>

                                        <td style="border:1px solid #ddd;padding:10px;">
                                            <a href="<?php the_permalink(); ?>" target="_blank">
                                                <?php echo esc_url(get_permalink()); ?>
                                            </a>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                                </tbody>
                            </table>

                            <p>
                                <strong>
                                    Total <?php echo esc_html($lang_name); ?>
                                    (<?php echo esc_html(strtoupper($type)); ?>):
                                    <?php echo intval($query->found_posts); ?>
                                </strong>
                            </p>

                        <?php else : ?>

                            <p>No posts found</p>

                        <?php endif; ?>

                        <?php wp_reset_postdata(); ?>

                    </div>

                <?php endforeach; ?>

            </section>

        <?php endforeach; ?>

    </div>

</main>

<?php
do_action('wpml_switch_language', null);
wp_reset_postdata();
get_footer();
?>

