<?php
global $wp;

// Determine current post type
$current_post_type = get_post_type();
$is_default_post_archive = is_post_type_archive('post') || is_home();
$is_resource_archive = is_post_type_archive('resource');
$is_magazine_issue_archive = is_post_type_archive('magazine_issue');
$is_authors_bio_archive = is_post_type_archive('authors_bio');
$is_category_archive = is_category();
$is_resource_category_archive = is_tax(['resource_type', 'resource_categories']);
$is_all_articles = is_page('all-articles');

// Setup selected categories
$selected_categories = [];
if (isset($_GET['category']) && trim($_GET['category']) !== '') {
    $selected_categories = array_map(
    'sanitize_title',
    explode(',', wp_unslash($_GET['category']))
);

}

// Get categories/taxonomies based on current post type
$categories = [];
$category_taxonomy = '';

if ($is_default_post_archive) {

    $default_lang = apply_filters('wpml_default_language', null);
    $current_lang = apply_filters('wpml_current_language', null);

    do_action('wpml_switch_language', $default_lang);

    $default_categories = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'meta_key'   => 'cat_order',
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
    ]);

    do_action('wpml_switch_language', $current_lang);

    $categories = [];

    foreach ($default_categories as $default_cat) {

        if ((int) $default_cat->term_id === (int) get_option('default_category')) {
            continue;
        }

        $translated_id = apply_filters(
            'wpml_object_id',
            $default_cat->term_id,
            'category',
            false,
            $current_lang
        );

        $term = $translated_id ? get_term($translated_id, 'category') : $default_cat;

        if (!is_wp_error($term)) {
            $categories[] = $term;
        }
    }
}



elseif ($is_resource_archive || is_tax(['resource_type', 'resource_categories'])) {

    // Detect current taxonomy automatically
    if (is_tax('resource_categories')) {
        $category_taxonomy = 'resource_categories';
    } else {
        $category_taxonomy = 'resource_type';
    }

    // Get terms based on detected taxonomy
    $categories = get_terms([
        'taxonomy'   => $category_taxonomy,
        'hide_empty' => false
    ]);
} elseif ($is_magazine_issue_archive) {
    $categories = get_terms(['taxonomy' => 'magazine_issue_category', 'hide_empty' => true]);
    $category_taxonomy = 'magazine_issue_category';
} elseif ($is_authors_bio_archive) {
    $categories = get_terms(['taxonomy' => 'authors_bio_category', 'hide_empty' => false]);
    $category_taxonomy = 'authors_bio_category';
}

// Determine if filters should show
$show_filters = ($is_default_post_archive || $is_resource_archive) && !$is_magazine_issue_archive && !$is_authors_bio_archive && !$is_all_articles;
?>
<?php if ($show_filters) : ?>

<div class="d-flex d-md-flex flex-column flex-md-row gap-4 apum-search-filter mb-5">
    

    <div class="flex-fill position-relative">
        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="d-flex search-form" role="search" method="get">
            
            <label for="search" class="visually-hidden">
                Search articles
            </label>

            <input 
                type="search" 
                id="search" 
                class="form-control search-input search-global" 
                name="search_query"
                placeholder="<?php echo esc_attr(t('iw_search_for')); ?>" 
                value="<?php echo isset($_GET['search_query']) ? esc_attr(wp_unslash($_GET['search_query'])) : ''; ?>" 
            />

            <button type="button" class="clear-search-btn me-3" aria-label="<?php echo esc_attr( t('iw_clear_search') ); ?>" style="display: <?php echo isset($_GET['search_query']) && $_GET['search_query'] !== '' ? 'block' : 'none'; ?>;">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <?php if (isset($_REQUEST['from'])) : ?>
                <input type="hidden" name="from" value="<?php echo esc_attr($_REQUEST['from']); ?>">
            <?php endif; ?>

            <input type="hidden" name="category" id="selected-categories-hidden" value="<?php echo esc_attr(implode(',', $selected_categories)); ?>">

            <span class="search-icon"></span>

            <button type="submit" class="search-btn" aria-label="<?php echo esc_attr(t('iw_submit_search')); ?>">
                <img src="/wp-content/uploads/2025/08/searchicon-2.svg" alt="<?php echo esc_attr(t('iw_search')); ?>">
            </button>

        </form>
  
</div>

<?php endif; ?>

    <?php if ($show_filters && !$is_category_archive && !$is_resource_category_archive) : ?>
        <div class="dropdown">
            <button id="select-category-btn" class="form-control" onclick="toggleDropdown()">
                <span><?php echo esc_html(t('iw_select_category')); ?></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="dropdown-content" id="category-dropdown" tabindex="0">
                <?php foreach ($categories as $category) : ?>
                    <?php
                    $category_name = esc_html($category->name);
                    $category_slug = esc_attr($category->slug);
                    $is_selected = in_array($category_slug, $selected_categories);
                    ?>
                    <div onclick="selectCategory('<?php echo $category_slug; ?>', '<?php echo esc_js($category_name); ?>')" class="<?php echo $is_selected ? 'selected' : ''; ?>">
                        <input type="checkbox" <?php echo $is_selected ? 'checked' : ''; ?> onclick="event.stopPropagation();" data-slug="<?php echo $category_slug; ?>" data-name="<?php echo esc_attr($category_name); ?>">
                        <span><?php echo $category_name; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($show_filters && !$is_resource_category_archive) : ?>

    <div class="d-flex align-items-center gap-2">


<?php if ( isset($_GET['search_query']) && ! empty($_GET['search_query']) ) : ?>
<div class="d-flex gap-2 align-items-center mb-4">
    <div class="searched-keyword-box d-flex align-items-center gap-2 flex-wrap">
        <span class="searched-keyword-label">
            <?php echo esc_html(t('iw_results_for')); ?>: 
            <?php
                // Get cleaned query string
                $query_args = $_GET;
                unset($query_args['search_query'], $query_args['paged']);
                if (empty($query_args['category'])) {
                    unset($query_args['category']);
                }

                $current_post_type = get_post_type();
                $current_url = home_url( add_query_arg( $_GET, $wp->request ) );
                $current_lang = apply_filters('wpml_current_language', null);
if ($current_lang) {
    $current_url = add_query_arg('lang', $current_lang, $current_url);
}

                if (is_post_type_archive()) {
                    $clear_url = get_post_type_archive_link($current_post_type);
                } elseif (is_page()) {
                    $clear_url = get_permalink();
                } else {
                    $clear_url = $current_url;
                }

                if (!empty($query_args)) {
                    $clear_url = add_query_arg($query_args, $clear_url);
                }
            ?>
            <a href="<?php echo esc_url($clear_url); ?>" id="clear-search-chip" class="clear-search-keyword category-tag ms-1" title="<?php echo esc_attr(t('iw_clear_search')); ?>">
                <span><?php echo esc_html( wp_unslash( $_GET['search_query'] ) ); ?></span>
                <i class="fa-solid fa-xmark"></i>
            </a>
        </span>
    </div>
</div>
<?php endif; ?>
<div class="selected-categories-container mb-4">
    <div class="selected-categories" id="selected-categories"></div>
    <?php if (!empty($selected_categories)) : ?>
        <div class="clear-all" id="clear-all" onclick="clearAllCategories()" tabindex="0">
            <span><?php echo esc_html(t('iw_clear_all')); ?></span>
        </div>
    <?php endif; ?>
</div>

</div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('category-dropdown');
    const button = document.getElementById('select-category-btn');
    const hiddenCategoryInput = document.getElementById('selected-categories-hidden');
    if (!dropdown || !button) return;

    const urlParams = new URLSearchParams(window.location.search);
    const currentCategories = urlParams.get('category') ? urlParams.get('category').split(',') : [];
    const selectedCategories = new Set(currentCategories);
    const selectedCategoryNames = {};

    document.querySelectorAll('#category-dropdown div').forEach(div => {
        const checkbox = div.querySelector('input[type="checkbox"]');
        const slug = checkbox.dataset.slug;
        const name = checkbox.dataset.name;

        if (selectedCategories.has(slug)) {
            selectedCategoryNames[slug] = name;
            checkbox.checked = true;
            div.classList.add('selected');
            addCategoryTag(slug, name);
        }
    });

    if (selectedCategories.size > 0) {
        document.querySelectorAll('.clear-all').forEach(function (el) {
            el.style.display = 'inline';
        });
    }

    window.toggleDropdown = function () {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    };

    window.selectCategory = function (slug, name) {
        const checkbox = document.querySelector(`input[data-slug="${slug}"]`);

        if (selectedCategories.has(slug)) {
            selectedCategories.delete(slug);
            delete selectedCategoryNames[slug];
            checkbox.checked = false;
            checkbox.parentElement.classList.remove('selected');
            removeCategoryTag(slug);
        } else {
            selectedCategories.add(slug);
            selectedCategoryNames[slug] = name;
            checkbox.checked = true;
            checkbox.parentElement.classList.add('selected');
            addCategoryTag(slug, name);
        }

        updateResults();
    };

    function addCategoryTag(slug, name) {
        const container = document.getElementById('selected-categories');
        if (!container || document.querySelector(`.category-tag[data-slug="${slug}"]`)) return;

        const parenttag = document.createElement('a');
        const tag = document.createElement('span');
        parenttag.className = 'category-tag';
        parenttag.dataset.slug = slug;
        tag.innerHTML = `${name} <i class="fa-solid fa-xmark"></i>`;
        tag.onclick = function (e) {
            e.stopPropagation();
            selectCategory(slug, name);
        };
        parenttag.appendChild(tag);
        container.appendChild(parenttag);
    }

    function removeCategoryTag(slug) {
        const tag = document.querySelector(`.category-tag[data-slug="${slug}"]`);
        if (tag) tag.remove();
        if (selectedCategories.size === 0) {
            document.querySelectorAll('.clear-all').forEach(function (el) {
                el.style.display = 'none';
            });
        }
    }

    window.clearAllCategories = function () {
        selectedCategories.clear();
        document.getElementById('selected-categories').innerHTML = '';
        document.querySelectorAll('.clear-all').forEach(function (el) {
            el.style.display = 'none';
        });

        document.querySelectorAll('#category-dropdown input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.parentElement.classList.remove('selected');
        });

        updateResults();
    };

function updateResults() {

    const queryParams = new URLSearchParams();

    // ✅ Preserve WPML language
    const lang = document.documentElement.lang;
    if (lang) {
        queryParams.set('lang', lang);
    }

    // ✅ Preserve search
    const searchInput = document.querySelector('input[name="search_query"]');
    if (searchInput && searchInput.value.trim() !== '') {
        queryParams.set('search_query', searchInput.value.trim());
    }

    // ✅ Categories
    const catStr = Array.from(selectedCategories).join(',');
    if (catStr) {
        queryParams.set('category', catStr);
        hiddenCategoryInput.value = catStr;
    } else {
        hiddenCategoryInput.value = '';
    }

    // remove pagination
    queryParams.delete('paged');

    const newUrl = window.location.pathname + '?' + queryParams.toString();
    window.location.href = newUrl;
}


    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    const clearSearchChip = document.getElementById('clear-search-chip');
    const searchInput = document.querySelector('input[name="search_query"]');

    if (!clearSearchChip || !searchInput) return;

   clearSearchChip.addEventListener('click', function (e) {
    e.preventDefault();

    const searchInput = document.querySelector('input[name="search_query"]');
    if (searchInput) searchInput.value = '';

    const queryParams = new URLSearchParams(window.location.search);

    // ❌ Remove ALL possible search vars
    queryParams.delete('search_query');
    queryParams.delete('s');          // WordPress native search
    queryParams.delete('paged');

    // ✅ Preserve categories
    const hiddenCat = document.getElementById('selected-categories-hidden');
    if (hiddenCat && hiddenCat.value) {
        queryParams.set('category', hiddenCat.value);
    } else {
        queryParams.delete('category');
    }

    // ✅ Preserve WPML language
    const lang = document.documentElement.lang;
    if (lang) {
        queryParams.set('lang', lang);
    }

    const newUrl = window.location.pathname + (queryParams.toString() ? '?' + queryParams.toString() : '');
    window.location.href = newUrl;
});

});

window.addEventListener('pageshow', function(event) {
    const loadingEl = document.getElementById('loading');
    if (!loadingEl) return;

    // Show spinner immediately when coming from bfcache (back/forward)
    loadingEl.style.display = 'block';

    // Hide spinner after page is rendered
    setTimeout(() => {
        loadingEl.style.display = 'none';
    }, 1000); // 100ms delay, adjust if needed
});
</script>