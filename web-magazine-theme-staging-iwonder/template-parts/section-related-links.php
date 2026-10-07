<?php
$items = get_field('attachment_sheet_items');
if ($items): ?>
    <div class="maxw-720 mx-auto my-5">

        <div class="accordion">
            <div class="accordion-item d-flex flex-column gap-3 iw-related-links">


                <div class="fw-semibold text-uppercase d-flex align-items-center accordion-title">
                    <span><?php echo esc_html(t('iw_Related_Links')) ?></span>

                </div>


                <div class="accordion-body d-flex flex-column gap-2">
                    <?php
                    $home_url = home_url(); // your site URL

                    foreach ($items as $item):
                        $title = $item['related_link_title'];
                        $url   = $item['related_links'];
                        $related_category = $item['related_category'];
                        $related_type = $item['related_type'];
                        if ($related_category) {


                            // CASE 1: ACF returns TERM ID (integer)
                            if (is_numeric($related_category)) {
                                $related_category = get_term($related_category);
                            }

                            // CASE 2: ACF returns array (rare if field set to Term ID)
                            if (is_array($related_category) && isset($related_category['term_id'])) {
                                $related_category = get_term($related_category['term_id']);
                            }

                            // If term exists, translate it
                            if ($related_category instanceof WP_Term) {

                                $translated_term_id = apply_filters(
                                    'wpml_object_id',
                                    $related_category->term_id,
                                    $related_category->taxonomy,
                                    true
                                );

                                $related_category = get_term($translated_term_id);
                            }
                        }

                        if ($related_type) {

                            // CASE 1: ACF returns TERM ID
                            if (is_numeric($related_type)) {
                                $related_type = get_term($related_type);
                            }

                            // CASE 2: ACF returns array
                            if (is_array($related_type) && isset($related_type['term_id'])) {
                                $related_type = get_term($related_type['term_id']);
                            }

                            // WPML translation
                            if ($related_type instanceof WP_Term) {

                                $translated_type_id = apply_filters(
                                    'wpml_object_id',
                                    $related_type->term_id,
                                    $related_type->taxonomy,
                                    true
                                );

                                $related_type = get_term($translated_type_id);
                            }
                        }



                        // ICON LOGIC
                        $is_pdf = false;
                        $is_video = false;
                        $is_external = false;
                        $is_internal = false;
                        $is_doc = false;
                        $is_google_doc = false;


                        if ($url) {

                            // PDF check
                            if (preg_match('/\.pdf(\?.*)?$/i', $url)) {
                                $is_pdf = true;
                            }

                            // VIDEO EXTENSIONS
                            if (preg_match('/\.(mp4|mov|avi|wmv|mkv|webm|flv)(\?.*)?$/i', $url)) {
                                $is_video = true;
                            }

                            // YOUTUBE links
                            if (preg_match('/(youtube\.com|youtu\.be)/i', $url)) {
                                $is_video = true;
                            }

                            // DOCUMENT FILES
                            if (preg_match('/\.(doc|docx|ppt|pptx|xls|xlsx|txt|rtf)(\?.*)?$/i', $url)) {
                                $is_doc = true;
                            }
                            // GOOGLE DOCS / SHEETS / SLIDES / DRIVE

                            if (preg_match('/docs\.google\.com|drive\.google\.com/i', $url)) {
                                $is_google_doc = true;
                            }

                            // INTERNAL or EXTERNAL link check
                            if (strpos($url, $home_url) === 0) {
                                $is_internal = true;
                            } else {
                                $is_external = true;
                            }
                        }

                        // ICON SELECTION PRIORITY:
                        // 1. Video
                        // 2. PDF (if needed)
                        // 3. External link
                        // 4. Internal link
                        if ($is_pdf) {
                            $icon_html =  '<i class="fa-regular fa-file-pdf ms-auto fa-lg"></i>';
                        } elseif ($is_youtube) {
                            $icon_html = '	<i class="fa-brands fa-youtube ms-auto fa-lg"></i>';
                        } elseif ($is_google_doc) {
                            $icon_html =  '<i class="fa-regular ms-auto fa-file fa-lg"></i>';
                        } elseif ($is_internal) {
                            $icon_html = '<i class="fa-regular ms-auto fa-newspaper fa-lg"></i>';
                        } else {
                            $icon_html = '<i class="fa-regular ms-auto fa-play fa-lg"></i>';
                        }
                    ?>

                        <?php if ($url): ?>
                            <a href="<?php echo esc_url($url); ?>"
                                target="_blank"
                                rel="noopener"
                                class="d-flex  d-flex flex-column flex-md-row align-items-md-center pb-2 w-100 line-gradint">

                                <span class="fs-6 related_link_title"> <?php echo esc_html($title); ?></span>
                                <?php
                                $related_term = $related_category ?? null;
                                $related_type_term = $related_type ?? null;

                                $category_class = '';
                                if ($related_type_term) {

                                    $type_field_value = get_field(
                                        'category__class',
                                        $related_type_term->taxonomy . '_' . $related_type_term->term_id
                                    );

                                    if (!empty($type_field_value)) {
                                        $type_class = 'cat-' . sanitize_html_class($type_field_value);
                                    }

                                    $translated_type_name = apply_filters(
                                        'wpml_translate_single_string',
                                        $related_type_term->name,
                                        'taxonomy',
                                        $related_type_term->taxonomy . '_term_' . $related_type_term->term_id
                                    );
                                }
                                if ($related_term) {

                                    // Get the ACF field 'category__class' from the term
                                    $field_value = get_field('category__class', $related_term->taxonomy . '_' . $related_term->term_id);

                                    if (!empty($field_value)) {
                                        $category_class = 'cat-' . sanitize_html_class($field_value);
                                    }

                                    // WPML translate category name
                                    $translated_category_name = apply_filters(
                                        'wpml_translate_single_string',
                                        $related_term->name,
                                        'taxonomy',
                                        $related_term->taxonomy . '_term_' . $related_term->term_id
                                    );
                                }
                                ?>

                                <div class="related-link-meta">
                                    <?php if ($related_term) : ?>
                                        <span class="related-category d-none <?php echo esc_attr($category_class); ?>">
                                            <?php echo esc_html($translated_category_name); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($related_type_term) : ?>
                                        <span class="related-category related-type  text-uppercase <?php echo esc_attr($type_class); ?>">
                                            <?php echo esc_html($translated_type_name); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php echo $icon_html; ?>
                                </div>
                            </a>

                        <?php else: ?>
                            <span class="fw-semibold text-muted d-block py-2">
                                <?php echo esc_html($title); ?>
                            </span>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>

            </div>
        </div>

    </div>
<?php endif; ?>