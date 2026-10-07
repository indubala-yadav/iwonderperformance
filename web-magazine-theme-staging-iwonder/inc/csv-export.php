<?php
/**
 * CSV Export Tool Logic.
 */

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

add_action('admin_menu', function () {
    add_management_page('Download CSV', 'Download CSV', 'manage_options', 'download-csv', 'render_download_csv_page');
});

class Posts_CSV_Table extends WP_List_Table {
    function get_columns() {
        return ['ID' => 'ID', 'title' => 'Title', 'type' => 'Post Type', 'url' => 'URL', 'date' => 'Date'];
    }
    function column_default($item, $column) { return $item[$column] ?? ''; }
    function column_title($item) { return '<strong>' . esc_html($item['title']) . '</strong>'; }
    function column_url($item) { return '<a href="' . esc_url($item['url']) . '" target="_blank">View</a>'; }
    function prepare_items() {
        $per_page = 10;
        $paged  = max(1, absint($_GET['paged'] ?? 1));
        $search = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';
        $filter = isset($_GET['post_type_filter']) ? sanitize_key($_GET['post_type_filter']) : '';
        $args = ['post_type' => $filter ?: ['post', 'resource'], 'posts_per_page' => $per_page, 'paged' => $paged, 's' => $search, 'post_status' => 'publish'];
        $query = new WP_Query($args);
        $data = [];
        foreach ($query->posts as $p) {
            $data[] = ['ID' => $p->ID, 'title' => $p->post_title, 'type' => $p->post_type, 'url' => get_permalink($p->ID), 'date' => $p->post_date];
        }
        $this->items = $data;
        $this->_column_headers = [$this->get_columns(), [], []];
        $this->set_pagination_args(['total_items' => $query->found_posts, 'per_page' => $per_page, 'total_pages' => ceil($query->found_posts / $per_page)]);
    }
}

function render_download_csv_page() {
    $table = new Posts_CSV_Table();
    $table->prepare_items();
    ?>
    <div class="wrap">
        <h1>Downlod CSV</h1>
        <form method="get">
            <input type="hidden" name="page" value="download-csv" />
            <?php wp_nonce_field('download_csv_action', 'download_csv_nonce'); ?>
            <?php $table->search_box('Search Posts', 'search_id'); ?>
            <a href="<?php echo esc_url( wp_nonce_url( admin_url('admin-post.php?action=download_all_posts_csv'), 'download_csv_action' ) ); ?>" class="button button-primary">Download All CSV</a>
        </form>
        <?php $table->display(); ?>
    </div>
    <?php
}

add_action('admin_post_download_all_posts_csv', function () {
    if (!current_user_can('manage_options')) wp_die('No access');
    check_admin_referer('download_csv_action');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=iW-export.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Title', 'Type', 'URL', 'Date']);
    $query = new WP_Query(['post_type' => ['post', 'resource'], 'posts_per_page' => -1, 'post_status' => 'publish']);
    foreach ($query->posts as $p) {
        fputcsv($out, [$p->ID, $p->post_title, $p->post_type, get_permalink($p->ID), $p->post_date]);
    }
    fclose($out);
    exit;
});
