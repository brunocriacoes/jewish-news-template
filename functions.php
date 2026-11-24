<?php
declare(strict_types=1);
function jnt_setup(): void { add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('html5', ['search-form','style','script']); register_nav_menus(['primary'=>__('Main navigation','jewish-news-template'),'footer_sections'=>__('Footer: sections','jewish-news-template'),'footer_contact'=>__('Footer: contact','jewish-news-template')]); }
add_action('after_setup_theme','jnt_setup');
function jnt_assets(): void { wp_enqueue_style('jnt-style',get_stylesheet_uri(),[], (string) filemtime(get_stylesheet_directory() . '/style.css')); wp_enqueue_style('jnt-fontawesome','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',[], '6.7.2'); wp_enqueue_script('jnt-theme',get_theme_file_uri('/assets/js/theme.js'),[], (string) filemtime(get_theme_file_path('/assets/js/theme.js')),true); if (is_single()) { wp_enqueue_script('jnt-share',get_theme_file_uri('/assets/js/share.js'),[], (string) filemtime(get_theme_file_path('/assets/js/share.js')),true); } }
add_action('wp_enqueue_scripts','jnt_assets');
function jnt_category_posts_per_page(WP_Query $query): void {
    if (!is_admin() && $query->is_main_query() && $query->is_category()) {
        if ($query->is_category('communities')) {
            $featured = get_page_by_path('the-jewish-communities-of-portugal', OBJECT, 'post');

            // 49 cards + o destaque fixo mantêm 50 posts visíveis por página.
            $query->set('posts_per_page', 49);
            if ($featured instanceof WP_Post) {
                $query->set('post__not_in', array($featured->ID));
            }

            return;
        }

        $query->set('posts_per_page', 7);
    }
}
add_action('pre_get_posts', 'jnt_category_posts_per_page');
function jnt_fallback_menu(): void { echo '<ul class="menu">'; wp_list_pages(['title_li'=>'']); echo '</ul>'; }
function jnt_category(): string { $categories=get_the_category(); return $categories ? $categories[0]->name : __('News','jewish-news-template'); }
function jnt_post_date(?int $post_id = null): string { $timestamp = get_post_timestamp($post_id); return $timestamp ? gmdate('F j, Y', $timestamp) : ''; }
function jnt_author_nickname(): string { $nickname = get_the_author_meta('nickname'); return $nickname ?: get_the_author(); }
function jnt_story_image(string $size = 'large', string $fallback = ''): void { if (has_post_thumbnail()) { the_post_thumbnail($size, ['loading' => 'eager']); return; } if ($fallback) { printf('<img src="%s" alt="" loading="eager">', esc_url($fallback)); } }
function jnt_excerpt_chars(string $text, int $length): string { $text = trim(wp_strip_all_tags($text)); return mb_strlen($text) > $length ? rtrim(mb_substr($text, 0, $length)) . '...' : $text; }

/** Newsletter subscribers live in their own table so they never become public content or WordPress accounts. */
function jnt_subscribers_table(): string { global $wpdb; return $wpdb->prefix . 'jnt_subscribers'; }
function jnt_install_subscribers_table(): void {
    global $wpdb;
    $table = jnt_subscribers_table();
    $charset = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta("CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        email varchar(190) NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        token varchar(64) NOT NULL,
        subscribed_at datetime NOT NULL,
        confirmed_at datetime NULL,
        unsubscribed_at datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email),
        UNIQUE KEY token (token),
        KEY status (status)
    ) {$charset};");
    update_option('jnt_subscribers_schema_version', '1');
}
add_action('after_switch_theme', 'jnt_install_subscribers_table');
function jnt_maybe_install_subscribers_table(): void {
    if (get_option('jnt_subscribers_schema_version') !== '1') { jnt_install_subscribers_table(); }
}
add_action('init', 'jnt_maybe_install_subscribers_table');

function jnt_newsletter_redirect(string $state): void {
    wp_safe_redirect(add_query_arg('newsletter', $state, wp_get_referer() ?: home_url('/')));
    exit;
}
function jnt_handle_newsletter_signup(): void {
    if (!isset($_POST['jnt_newsletter_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['jnt_newsletter_nonce'])), 'jnt_newsletter_signup')) { jnt_newsletter_redirect('invalid'); }
    if (!empty($_POST['website'])) { jnt_newsletter_redirect('success'); } // Honeypot.
    if (empty($_POST['newsletter_consent'])) { jnt_newsletter_redirect('consent'); }
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    if (!is_email($email)) { jnt_newsletter_redirect('invalid'); }

    global $wpdb;
    $table = jnt_subscribers_table();
    $existing = $wpdb->get_row($wpdb->prepare("SELECT id, status FROM {$table} WHERE email = %s", $email));
    if ($existing && $existing->status === 'active') { jnt_newsletter_redirect('exists'); }

    $token = wp_generate_password(48, false, false);
    $now = current_time('mysql', true);
    if ($existing) {
        $wpdb->update($table, ['status' => 'pending', 'token' => $token, 'subscribed_at' => $now, 'confirmed_at' => null, 'unsubscribed_at' => null], ['id' => $existing->id]);
    } else {
        $wpdb->insert($table, ['email' => $email, 'status' => 'pending', 'token' => $token, 'subscribed_at' => $now]);
    }

    $confirm_url = add_query_arg(['jnt_newsletter_confirm' => rawurlencode($token)], home_url('/'));
    $unsubscribe_url = add_query_arg(['jnt_newsletter_unsubscribe' => rawurlencode($token)], home_url('/'));
    $subject = sprintf(__('Confirm your subscription to %s', 'jewish-news-template'), get_bloginfo('name'));
    $message = sprintf("Hello,\n\nPlease confirm your newsletter subscription by opening this link:\n%s\n\nIf you did not request this, you can safely ignore this email.\n\nTo cancel this request or unsubscribe later, use this link:\n%s", $confirm_url, $unsubscribe_url);
    wp_mail($email, $subject, $message);
    jnt_newsletter_redirect('check-email');
}
add_action('admin_post_nopriv_jnt_newsletter_signup', 'jnt_handle_newsletter_signup');
add_action('admin_post_jnt_newsletter_signup', 'jnt_handle_newsletter_signup');

function jnt_handle_newsletter_confirmation(): void {
    if (empty($_GET['jnt_newsletter_confirm'])) { return; }
    $token = sanitize_text_field(wp_unslash($_GET['jnt_newsletter_confirm']));
    global $wpdb;
    $table = jnt_subscribers_table();
    $subscriber = $wpdb->get_row($wpdb->prepare("SELECT id, status FROM {$table} WHERE token = %s", $token));
    if (!$subscriber) { wp_safe_redirect(add_query_arg('newsletter', 'invalid-confirmation', home_url('/'))); exit; }
    if ($subscriber->status !== 'active') { $wpdb->update($table, ['status' => 'active', 'confirmed_at' => current_time('mysql', true)], ['id' => $subscriber->id]); }
    wp_safe_redirect(add_query_arg('newsletter', 'confirmed', home_url('/')));
    exit;
}
add_action('template_redirect', 'jnt_handle_newsletter_confirmation');
function jnt_handle_newsletter_unsubscribe(): void {
    if (empty($_GET['jnt_newsletter_unsubscribe'])) { return; }
    $token = sanitize_text_field(wp_unslash($_GET['jnt_newsletter_unsubscribe']));
    global $wpdb;
    $updated = $wpdb->update(jnt_subscribers_table(), ['status' => 'unsubscribed', 'unsubscribed_at' => current_time('mysql', true)], ['token' => $token]);
    wp_safe_redirect(add_query_arg('newsletter', $updated !== false ? 'unsubscribed' : 'invalid-confirmation', home_url('/')));
    exit;
}
add_action('template_redirect', 'jnt_handle_newsletter_unsubscribe');

function jnt_register_subscribers_admin_page(): void {
    add_menu_page(__('Newsletter subscribers', 'jewish-news-template'), __('Newsletter', 'jewish-news-template'), 'manage_options', 'jnt-subscribers', 'jnt_render_subscribers_admin_page', 'dashicons-email-alt2', 26);
}
add_action('admin_menu', 'jnt_register_subscribers_admin_page');
function jnt_render_subscribers_admin_page(): void {
    if (!current_user_can('manage_options')) { return; }
    global $wpdb;
    $table = jnt_subscribers_table();
    if (isset($_GET['jnt_export']) && check_admin_referer('jnt_export_subscribers')) {
        $rows = $wpdb->get_results("SELECT email, status, subscribed_at, confirmed_at, unsubscribed_at FROM {$table} ORDER BY subscribed_at DESC", ARRAY_A);
        nocache_headers(); header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=newsletter-subscribers.csv');
        $output = fopen('php://output', 'w'); fputcsv($output, ['Email', 'Status', 'Subscribed at', 'Confirmed at', 'Unsubscribed at']); foreach ($rows as $row) { fputcsv($output, $row); } fclose($output); exit;
    }
    $search = isset($_GET['s']) ? sanitize_email(wp_unslash($_GET['s'])) : '';
    $page = max(1, absint($_GET['paged'] ?? 1)); $per_page = 30; $offset = ($page - 1) * $per_page;
    $where = $search ? $wpdb->prepare('WHERE email LIKE %s', '%' . $wpdb->esc_like($search) . '%') : '';
    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} {$where}");
    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} {$where} ORDER BY subscribed_at DESC LIMIT %d OFFSET %d", $per_page, $offset));
    $export_url = wp_nonce_url(admin_url('admin.php?page=jnt-subscribers&jnt_export=1'), 'jnt_export_subscribers');
    echo '<div class="wrap"><h1>Newsletter subscribers</h1><p>Only administrators can access this list.</p><form method="get"><input type="hidden" name="page" value="jnt-subscribers"><p class="search-box"><label class="screen-reader-text" for="subscriber-search-input">Search email</label><input type="search" id="subscriber-search-input" name="s" value="' . esc_attr($search) . '"><input type="submit" class="button" value="Search"></p></form><p><a class="button button-primary" href="' . esc_url($export_url) . '">Export CSV</a></p><table class="widefat fixed striped"><thead><tr><th>Email</th><th>Status</th><th>Subscribed</th><th>Confirmed</th></tr></thead><tbody>';
    if ($rows) { foreach ($rows as $row) { echo '<tr><td>' . esc_html($row->email) . '</td><td>' . esc_html(ucfirst($row->status)) . '</td><td>' . esc_html($row->subscribed_at) . '</td><td>' . esc_html($row->confirmed_at ?: '—') . '</td></tr>'; } } else { echo '<tr><td colspan="4">No subscribers found.</td></tr>'; }
    echo '</tbody></table>' . paginate_links(['base' => add_query_arg('paged', '%#%'), 'format' => '', 'current' => $page, 'total' => max(1, (int) ceil($total / $per_page))]) . '</div>';
}

