<?php
$editor_post = new WP_Query([
    'post_type'      => 'from_the_editor',
    'posts_per_page' => 1,
]);

if ($editor_post->have_posts()) : ?>
    <div class="mb-4 editorlinebar-img position-relative">

        <img src="/wp-content/uploads/2025/11/iwonder-windmill.svg"
            class="position-absolute windmill-img"
            alt="iwonder-windmill"
            loading="lazy"
            decoding="async"
            width="180"
            height="180">

        <div class="editor-section apum-animation-fadeInUp py-4">

            <?php while ($editor_post->have_posts()) : $editor_post->the_post();

                $title   = get_the_title();
                $content = get_the_content();

                /*
             * Get plain text only for the preview.
             * This removes WordPress automatic [...] from get_the_excerpt().
             */
                $preview_text = wp_strip_all_tags($content);

                /*
             * Split after the first sentence.
             * Supports ., ! and ?
             */
                preg_match(
                    '/^(.+?[.!?])(\s+)(.*)$/us',
                    $preview_text,
                    $matches
                );

                if (!empty($matches)) {
                    $first_line     = trim($matches[1]);
                    $remaining_text = trim($matches[3]);
                } else {
                    $first_line     = trim($preview_text);
                    $remaining_text = '';
                }

            ?>

                <div class="editor-excerpt mb-1">

                    <h2 class="editor-label">
                        <?php echo esc_html(t('iw_from_the_editor')); ?>
                    </h2>

                    <!-- First sentence -->
                    <p class="editor-first-line mb-2 text-start">
                        <?php echo esc_html($first_line); ?>
                    </p>

                    <!-- Remaining text -->
                    <?php if (!empty($remaining_text)) : ?>
                        <p class="text-truncation line-3 mb-0 text-start">
                            <?php echo esc_html($remaining_text); ?>
                        </p>
                    <?php endif; ?>

                </div>

                <div>
                    <button type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#editorModal"
                        class="btn btn-link btn-sm">

                        <?php echo esc_html(t('iw_read_more')); ?>

                    </button>
                </div>

        </div>

        <!-- Modal -->
        <div class="modal fade"
            id="editorModal"
            tabindex="-1"
            aria-labelledby="editorModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

                <div class="modal-content custom-modal">

                    <div class="modal-header border-0 justify-content-between">

                        <div class="d-flex flex-column text-start">

                            <h2 class="editor-label" id="editorModalLabel">
                                <?php echo esc_html(t('iw_from_the_editor')); ?>
                            </h2>

                            <h2 class="editor-title mb-0">
                                <?php echo esc_html($title); ?>
                            </h2>

                        </div>

                        <i data-bs-dismiss="modal"
                            aria-label="Close"
                            class="cursor-pointer fa fa-window-close fs-24 align-self-start">
                        </i>

                    </div>

                    <div class="modal-body pt-0">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>

                </div>

            </div>

        </div>

    <?php endwhile;
            wp_reset_postdata(); ?>

    </div>

<?php endif; ?>