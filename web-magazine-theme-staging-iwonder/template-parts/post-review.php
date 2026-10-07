<?php

/**
 * ============================================================================
 * ARA Reviewer Dashboard (functions.php) — WPML enabled
 * - Pending/Draft tabs, sortable columns
 * - Copyable preview links
 * - Email column (mailto: with to/cc/subject/body) using per-language reviewer lists
 * - Reviewer email lists + global CC are stored as options (textarea, one per line)
 * ============================================================================
 */




if (!defined('ABSPATH')) exit;

/* ==============================
 * 1️⃣ Secret key
 * ============================== */
function ara_get_secret_key()
{
    // Use a secret from wp-config.php if available; otherwise fall back to WP salts
    if (defined('ARA_PREVIEW_SECRET') && ARA_PREVIEW_SECRET) {
        return ARA_PREVIEW_SECRET;
    }

    // Derive from auth salt to avoid committing secrets to the repo
    return wp_salt('auth');
}

/* ==============================
 * 2️⃣ Generate preview token
 * ============================== */
function ara_generate_preview_token($post_id)
{
    $secret_key = ara_get_secret_key();
    return hash('sha256', $post_id . '|' . $secret_key);
}

/* ==============================
 * 3️⃣ Generate preview link
 * ============================== */
function ara_get_preview_link($post_id)
{
    $token = ara_generate_preview_token($post_id);
    return add_query_arg([
        'p'     => $post_id,
        'token' => $token,
    ], get_permalink($post_id));
}

/* ==============================
 * 4️⃣ Enable public preview for WPML translations
 * ============================== */
add_action('pre_get_posts', function ($query) {

    if (!is_admin() && $query->is_main_query() && isset($_GET['token'], $_GET['p'])) {

        $post_id = intval($_GET['p']);
        $provided_token = sanitize_text_field($_GET['token']);
        $expected_token = ara_generate_preview_token($post_id);

        if (!hash_equals($expected_token, $provided_token)) return;

        // Allow public preview
        add_filter('user_has_cap', function ($caps) {
            $caps['read_post'] = true;
            $caps['read_private_posts'] = true;
            return $caps;
        });

        // WPML language
        $current_lang = apply_filters('wpml_current_language', null) ?: 'en';

        // Get translated post ID
        $translated_id = apply_filters(
            'wpml_object_id',
            $post_id,
            get_post_type($post_id),
            true,
            $current_lang
        ) ?: $post_id;

        // Force query
        $query->set('p', $translated_id);
        $query->set('post_status', ['publish', 'pending', 'draft']);
    }
}, 0);

/* ==============================
 * 5️⃣ Admin menu
 * ============================== */
add_action('admin_menu', function () {
    add_menu_page(
        'Reviewer Dashboard',
        'Reviewer Dashboard',
        'edit_posts',
        'ara-reviewer-dashboard',
        'ara_reviewer_dashboard_page',
        'dashicons-visibility',
        25
    );
});

/* ==============================
 * 6️⃣ Helper functions
 * ============================== */
function ara_parse_emails_textarea($text)
{
    $out = [];
    foreach (preg_split("/\r\n|\n|\r/", (string)$text) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $out[] = $line;
    }
    return $out;
}

function ara_get_reviewer_emails($lang_code)
{
    $key = 'ara_reviewer_emails_' . $lang_code;
    $raw = get_option($key, '');
    return ara_parse_emails_textarea($raw);
}

function ara_build_mailto_for_post($post)
{
    $lang = apply_filters(
        'wpml_element_language_code',
        null,
        [
            'element_id'   => $post->ID,
            'element_type' => 'post_' . get_post_type($post->ID),
        ]
    );

    $lang = strtolower($lang ?: 'en');
    $reviewers = ara_get_reviewer_emails($lang);
    $global_cc = ara_parse_emails_textarea(get_option('ara_reviewer_global_cc', ''));

    $to = $reviewers ? [trim($reviewers[0])] : [];
    $cc = $reviewers && count($reviewers) > 1 ? array_slice($reviewers, 1) : [];
    $cc = array_merge($cc, $global_cc);

    // Remove duplicates
    $normalize = fn($email) => strtolower(trim(preg_match('/<(.+?)>/', $email, $m) ? $m[1] : $email));
    $all_seen = [];
    $to = array_filter($to, fn($e) => !isset($all_seen[$n = $normalize($e)]) ? ($all_seen[$n] = true) : false);
    $cc = array_filter($cc, fn($e) => !isset($all_seen[$n = $normalize($e)]) ? ($all_seen[$n] = true) : false);

    $title = get_the_title($post->ID) ?: '(no title)';
    $preview = ara_get_preview_link($post->ID);
    $encoded_preview = rawurlencode($preview);

    $body = "Dear Reviewer,\n\nA new article is ready for your review.\n\nTitle: {$title}\nPreview Link: {$preview}\n\nKindly review and share your changes.\n\nThank you,\nWeb Publish Team";
    $body = str_replace($preview, $encoded_preview, $body);
    $body = str_replace("\n", "%0A", $body);

    $subject = rawurlencode('Article "' . $title . '" for review');
    $to_str = rawurlencode(implode(',', $to));
    $cc_str = rawurlencode(implode(',', $cc));

    $mailto = "mailto:{$to_str}?subject={$subject}&body={$body}";
    if ($cc) $mailto .= "&cc={$cc_str}";
    return $mailto;
}

function ara_fetch_posts_for_dashboard($status = 'pending')
{
    return get_posts([
        'post_type'   => ['post', 'resource', 'editorial_team'],
        'post_status' => $status,
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ]);
}

/* ==============================
 * 7️⃣ Reviewer dashboard page
 * ============================== */
function ara_reviewer_dashboard_page()
{
    if (!current_user_can('edit_posts')) wp_die('Insufficient permissions');

    // Save settings
    if (isset($_POST['ara_reviewer_settings_save'])) {
        check_admin_referer('ara_reviewer_settings_save_action', 'ara_reviewer_settings_nonce');
        update_option('ara_reviewer_emails_en', wp_kses_post($_POST['ara_reviewer_emails_en'] ?? ''));
        update_option('ara_reviewer_emails_hi', wp_kses_post($_POST['ara_reviewer_emails_hi'] ?? ''));
        update_option('ara_reviewer_emails_kn', wp_kses_post($_POST['ara_reviewer_emails_kn'] ?? ''));
        update_option('ara_reviewer_global_cc', wp_kses_post($_POST['ara_reviewer_global_cc'] ?? ''));
        echo '<div class="updated"><p>Reviewer email settings saved.</p></div>';
    }

    $tab = ($_GET['tab'] ?? 'pending') === 'draft' ? 'draft' : 'pending';
    $posts = ara_fetch_posts_for_dashboard($tab);


    function ara_get_post_lang($post_id)
    {
        $lang = apply_filters('wpml_element_language_code', null, [
            'element_id'   => $post_id,
            'element_type' => 'post_' . get_post_type($post_id),
        ]);
        return strtoupper($lang ?: 'EN'); // fallback EN
    }
    $rows = [];
    foreach ($posts as $post) {
        $rows[] = [
            'ID'       => $post->ID,
            'title'    => $post->post_title,
            'lang'     => ara_get_post_lang($post->ID),
            'author'   => get_the_author_meta('display_name', $post->post_author),
            'date'     => $post->post_date,
            'preview'  => ara_get_preview_link($post->ID),
            'mailto'   => ara_build_mailto_for_post($post),
        ];
    }

    // Load settings
    $emails_en = get_option('ara_reviewer_emails_en', '');
    $emails_hi = get_option('ara_reviewer_emails_hi', '');
    $emails_kn = get_option('ara_reviewer_emails_kn', '');
    $global_cc = get_option('ara_reviewer_global_cc', '');

?>
    <div class="wrap">
        <h1>Reviewer Dashboard</h1>
        <p>Copy preview links or click <strong>Email</strong> to open your mail client with pre-filled To / Cc / Subject / Body.</p>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $tab === 'pending' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=ara-reviewer-dashboard&tab=pending')); ?>">Pending</a>
            <a class="nav-tab <?php echo $tab === 'draft' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=ara-reviewer-dashboard&tab=draft')); ?>">Draft</a>
        </h2>

        <table class="widefat striped" style="margin-top:12px;">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Lang</th>
                    <th>Preview Link</th>
                    <th>Email</th>
                    <th>Author</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6">No posts found (<?php echo esc_html($tab); ?>).</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><a href="<?php echo esc_url(get_edit_post_link($r['ID'])); ?>" target="_blank"><?php echo esc_html($r['title']); ?></a></td>
                            <td><?php echo esc_html($r['lang']); ?></td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <input type="text" class="ara-preview-link" value="<?php echo esc_attr($r['preview']); ?>" readonly style="flex:1;padding:4px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <a class="button" href="<?php echo esc_url($r['preview']); ?>" target="_blank">Open Preview</a>
                                    <button class="button ara-copy-btn" data-url="<?php echo esc_attr($r['preview']); ?>">Copy</button>
                                </div>
                            </td>
                            <td><a class="button" target="_blank" href="<?php echo esc_attr($r['mailto']); ?>">Email</a></td>
                            <td><?php echo esc_html($r['author']); ?></td>
                            <td><?php echo esc_html(date('Y-m-d H:i', strtotime($r['date']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 style="margin-top:28px;">Reviewer Email Settings</h2>
        <form method="post">
            <?php wp_nonce_field('ara_reviewer_settings_save_action', 'ara_reviewer_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>English Reviewer Emails</th>
                    <td><textarea name="ara_reviewer_emails_en" rows="4" class="large-text code"><?php echo esc_textarea($emails_en); ?></textarea></td>
                </tr>
                <tr>
                    <th>Hindi Reviewer Emails</th>
                    <td><textarea name="ara_reviewer_emails_hi" rows="4" class="large-text code"><?php echo esc_textarea($emails_hi); ?></textarea></td>
                </tr>
                <tr>
                    <th>Kannada Reviewer Emails</th>
                    <td><textarea name="ara_reviewer_emails_kn" rows="4" class="large-text code"><?php echo esc_textarea($emails_kn); ?></textarea></td>
                </tr>
                <tr>
                    <th>Global CC</th>
                    <td><textarea name="ara_reviewer_global_cc" rows="2" class="large-text code"><?php echo esc_textarea($global_cc); ?></textarea></td>
                </tr>
            </table>
            <p><input type="submit" name="ara_reviewer_settings_save" class="button button-primary" value="Save Settings"></p>
        </form>
    </div>

    <script>
        document.querySelectorAll('.ara-copy-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = btn.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    const orig = btn.innerText;
                    btn.innerText = 'Copied!';
                    btn.disabled = true;
                    setTimeout(() => {
                        btn.innerText = orig;
                        btn.disabled = false;
                    }, 1400);
                });
            });
        });
    </script>
<?php
}
