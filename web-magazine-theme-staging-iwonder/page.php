<?php
get_header(); 
?>
<div class="container-fluid p-0 position-relative apum-innerpage-section"> 
    <div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>
    <main id="primary" class="apum-section position-relative section-pad pt-5 pt-lg-4 pb-200">

        <?php
        // Start the Loop.
        while ( have_posts() ) :
            the_post();
        ?>
        <div class="container apum-innerpage-body bg-white pt-80 pb-0 rounded">
            <div class="maxw-720 content-area">
                <div class="title-area add-motif mb-4">
                <?php custom_breadcrumb(); ?>
                    <h1 class="mb-4"><?php the_title(); ?></h1>
                </div>
                
                
                <?php 
                // Get the 'Short Note' field value
                $short_note = get_field('page_short_note');
                // Check if the field has a value before displaying
                if ($short_note) : ?>
                    <div class="short-note mb-5">
                        <?php echo wp_kses_post($short_note); ?>
                    </div>
                <?php endif; ?>
            </div>
        
        
                <!-- featured image -->
            <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium', ['class' => 'img-fluid w-100', 'alt' => get_the_title()]); ?>
            <?php endif; ?>
            <!-- featured image -->

    <div class="page-content content-area 
<?php 
$privacy_page = get_page_by_path('privacy-policy');
$legal_page   = get_page_by_path('legal-and-usage-information');

if ($privacy_page && function_exists('icl_object_id')) {
    $privacy_page_id = icl_object_id($privacy_page->ID, 'page', true);

    if (get_the_ID() == $privacy_page_id) {
        echo 'privacy-policy';
    }
}

if ($legal_page && function_exists('icl_object_id')) {
    $legal_page_id = icl_object_id($legal_page->ID, 'page', true);

    if (get_the_ID() == $legal_page_id) {
        echo 'legal-and-usage-information';
    }
}
?> maxw-720 mt-5" >
            
         

            <?php the_content(); ?>

					<?php
			$about_page = get_page_by_path('about-us');
			$about_us_id = $about_page ? $about_page->ID : 0;

			// If WPML is active, translate the ID
			if (function_exists('icl_object_id') && $about_us_id) {
				$about_us_id = icl_object_id($about_us_id, 'page', true);
			}

			if ($about_us_id && is_page($about_us_id)) {
				get_template_part('template-parts/section', 'editorial-team');
			}
			?>

        </div>

            <?php endwhile; // End of the loop. ?>
        </div>
    </main><!-- #primary -->
 </div>
 


<?php
$english_page_id = 350;
$current_lang_page_id = $english_page_id;

// If WPML is active
if (function_exists('icl_object_id') && defined('ICL_LANGUAGE_CODE')) {
    $current_lang_page_id = apply_filters('wpml_object_id', $english_page_id, 'page', false, ICL_LANGUAGE_CODE);
}

if (get_queried_object_id() === $current_lang_page_id):
    $tags = get_tags(['hide_empty' => false]);
    $tag_names = array_map(fn($tag) => $tag->name, $tags);
?>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('#keywords');
        if (input && typeof Tagify !== 'undefined') {
            const tagify = new Tagify(input, {
                enforceWhitelist: false,
                whitelist: <?php echo json_encode($tag_names); ?>,
                keepInvalidTags: true,
                delimiters: ",",
				originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '),
                callbacks: {
                    add: onTagAdd,
                    remove: console.log
                }
            });
			 // Convert Tagify data to plain comma-separated value before Contact Form 7 submission
				 const form = input.closest('form');
			if (form) {
				form.addEventListener('submit', function () {
					const plainValues = tagify.value.map(tag => tag.value);
					input.value = plainValues.join(', ');
				});
			}


            const junkKeywords = ['asdf', 'qwerty', 'test123', 'lorem', 'xxx'];
            const junkPattern = /^([a-z]{1,2}|[a-z]{6,}|[0-9]{3,})$/i;

            function onTagAdd(e) {
                const tagData = e.detail.data;
                const tagValue = (tagData?.value || '').trim().toLowerCase();

                setTimeout(() => {
                    if (junkKeywords.includes(tagValue) || junkPattern.test(tagValue)) {
                        tagify.removeTag(tagData);
                    }
                }, 100);
            }
        }

        function countWords(text) {
            return text.trim() === '' ? 0 : text.trim().replace(/[\r\n]/g, ' ')
                .replace(/\s+/g, ' ')
                .split(' ').length;
        }

        function enforceAbsoluteLimit(textarea) {
            const text = textarea.value;
            const wordCount = countWords(text);

            if (wordCount > 100) {
                const words = text.replace(/\s+/g, ' ').split(' ');
                let position = 0;
                for (let i = 0; i < 100; i++) {
                    position += words[i].length + (i < 99 ? 1 : 0);
                }
                textarea.value = text.substring(0, position);

                const counter = document.querySelector('.word-count[data-for="' + textarea.id + '"]');
                if (counter) {
                    counter.textContent = '100';
                    counter.style.color = 'red';
                }
                return false;
            }
            return true;
        }

        document.querySelectorAll('#authors-bio, #short-summary').forEach(textarea => {
            enforceAbsoluteLimit(textarea);

            textarea.addEventListener('input', function () {
                enforceAbsoluteLimit(this);
                const wordCount = countWords(this.value);
                const counter = document.querySelector('.word-count[data-for="' + this.id + '"]');
                if (counter) {
                    counter.textContent = wordCount;
                    counter.style.color = wordCount >= 100 ? 'red' : '';
                }
            });

            ['paste', 'drop'].forEach(evt => {
                textarea.addEventListener(evt, function () {
                    setTimeout(() => enforceAbsoluteLimit(this), 10);
                });
            });

            textarea.addEventListener('keydown', function (e) {
                if (countWords(this.value) >= 100 &&
                    !(e.key.length > 1 || e.ctrlKey || e.metaKey || e.altKey)) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
<?php endif; ?>

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>


<?php get_footer(); ?>