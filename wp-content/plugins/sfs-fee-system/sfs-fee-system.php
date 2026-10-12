<?php
/*
Plugin Name: SFS Fee System
Description: Dynamic fee structure by academic session & class
Version: 1.0
Author: Badal Sutradhar - Vasp Technologies
*/

if (!defined('ABSPATH')) exit;


add_action('init', function () {

    register_post_type('sfs_fee', [
        'labels' => [
            'name' => 'SFS Fee Structures',
            'singular_name' => 'SFS Fee Structure',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-money-alt',
        'supports' => ['title', 'page-attributes'],
    ]);
});

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'sfs-fee-css',
        plugin_dir_url(__FILE__) . 'assets/css/fee-style.css'
    );

    wp_enqueue_script(
        'sfs-fee-js',
        plugin_dir_url(__FILE__) . 'assets/js/fee.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('sfs-fee-js', 'SFS_ADMIN', [
        'ajax' => admin_url('admin-ajax.php'),
        'year' => '',
        'class' => ''
    ]);
});

add_action('admin_enqueue_scripts', function ($hook) {

    if ($hook !== 'post.php' && $hook !== 'post-new.php') return;

    global $post;
    if (!$post || $post->post_type !== 'sfs_fee') return;

    wp_enqueue_script(
        'sfs-fee-admin-js',
        plugin_dir_url(__FILE__) . 'assets/js/fee-admin.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('sfs-fee-admin-js', 'SFS_ADMIN', [
        'ajax'  => admin_url('admin-ajax.php'),
        'year'  => get_post_meta($post->ID, '_fee_year', true),
        'class' => get_post_meta($post->ID, '_fee_class', true),
    ]);
});

add_action('edit_form_top', 'sfs_add_back_button');

function sfs_add_back_button($post) {

    // Change this to your post type
    if ($post->post_type !== 'sfs_fee') {
        return;
    }

    $list_url = admin_url('edit.php?post_type=sfs_fee');
    ?>

    <div style="margin-bottom:10px;">
        <a href="<?php echo esc_url($list_url); ?>"
           class="button button-secondary">
            ← Back to Fee Structures List
        </a>
    </div>

    <?php
}

add_action('add_meta_boxes', function () {
add_meta_box(
    'sfs_fee_meta',
    'Fee Details',
    'sfs_fee_meta_callback',
    'sfs_fee',
    'advanced', // 👈 change from normal
    'high'
);

});


function sfs_fee_meta_callback($post) {

    wp_nonce_field('sfs_fee_save', 'sfs_fee_nonce');

    $year  = get_post_meta($post->ID, '_fee_year', true);
    $class = get_post_meta($post->ID, '_fee_class', true);
    $fees  = get_post_meta($post->ID, '_fee_items', true);

    $fees = is_array($fees) && count($fees) ? $fees : [
        ['label' => '', 'amount' => '', 'add_on_fee' => '']
    ];

    ?>

    <p>
        <label><b>Academic Session</b></label>
        <select id="sfs_fee_year" name="fee_year" style="width:100%;" data-ext="badal">
            <option value="">Loading sessions...</option>
        </select>
    </p>

    <p>
        <label><b>Class</b></label>
        <select id="sfs_fee_class" name="fee_class" style="width:100%;">
            <option value="">Select session first</option>
        </select>
    </p>

    <hr>

    <h3>Fee Items</h3>

    <div id="feeRepeater">
        <?php foreach ($fees as $i => $row): ?>
            <div class="fee-row">
        
                <input type="text"
                       name="fee_items[<?= $i ?>][admission_fee]"
                       placeholder="New Admission Fee"
                       value="<?= esc_attr($row['admission_fee'] ?? ''); ?>">
        
                <input type="text"
                       name="fee_items[<?= $i ?>][q1_fee]"
                       placeholder="Quarter-1 Fee"
                       value="<?= esc_attr($row['q1_fee'] ?? ''); ?>">
        
                <input type="text"
                       name="fee_items[<?= $i ?>][add_on_fee]"
                       placeholder="Add-on Fee"
                       value="<?= esc_attr($row['add_on_fee'] ?? ''); ?>">
        
                <button type="button" class="remove-fee">✕</button>
            </div>
        <?php endforeach; ?>

    </div>

    <button type="button" id="addFeeRow">+ Add Fee</button>

    <!-- store selected values for JS -->
    <input type="hidden" id="SFS_SELECTED_YEAR" value="<?= esc_attr($year); ?>">
    <input type="hidden" id="SFS_SELECTED_CLASS" value="<?= esc_attr($class); ?>">

<?php }


add_action('wp_ajax_sfs_load_fee', 'sfs_load_fee');
add_action('wp_ajax_nopriv_sfs_load_fee', 'sfs_load_fee');

function sfs_load_fee() {

    $q = new WP_Query([
        'post_type'      => 'sfs_fee',
        'posts_per_page' => -1,
        'orderby'        => [
            'menu_order' => 'ASC',
            'title'      => 'ASC'
        ]
    ]);

    if (!$q->have_posts()) {
        wp_send_json_error();
    }

    $rows = [];

    while ($q->have_posts()) {
        $q->the_post();

        $fees = get_post_meta(get_the_ID(), '_fee_items', true);
        if (empty($fees)) continue;

        $rows[] = [
            'class'   => get_the_title(),
            'session' => get_post_meta(get_the_ID(), '_fee_year', true),
            'fees'    => $fees
        ];
    }

    wp_reset_postdata();

    wp_send_json_success([
        'rows' => $rows
    ]);
}


add_shortcode('sfs_fee_structure', function () {

    ob_start(); ?>
    <div class="sfs-fee-container" style="padding: 10px; margin-top: 10px;">

        <div class="sfs-fee-body">
            <div class="sfs-fee-content table-responsive">
                <table class="table" id="feeTable"></table>
            </div>
        </div>

    </div>

    <?php return ob_get_clean();
});


add_action('save_post_sfs_fee', 'sfs_fee_save_meta', 10, 3);

function sfs_fee_save_meta($post_id) {

    /* ---------------------------------
       1. POST TYPE CHECK
    --------------------------------- */
    if (get_post_type($post_id) !== 'sfs_fee') {
        return;
    }

    /* ---------------------------------
       2. NONCE CHECK
    --------------------------------- */
    if (
        !isset($_POST['sfs_fee_nonce']) ||
        !wp_verify_nonce($_POST['sfs_fee_nonce'], 'sfs_fee_save')
    ) {
        return;
    }

    /* ---------------------------------
       3. AUTOSAVE / REVISION CHECK
    --------------------------------- */
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    /* ---------------------------------
       4. SAVE SESSION
    --------------------------------- */
    if (isset($_POST['fee_year'])) {
        update_post_meta(
            $post_id,
            '_fee_year',
            sanitize_text_field($_POST['fee_year'])
        );
    }

    /* ---------------------------------
       5. SAVE CLASS
    --------------------------------- */
    if (isset($_POST['fee_class'])) {
        update_post_meta(
            $post_id,
            '_fee_class',
            sanitize_text_field($_POST['fee_class'])
        );
    }

    /* ---------------------------------
       6. SAVE FEE ITEMS (REPEATER)
    --------------------------------- */


    if (isset($_POST['fee_items']) && is_array($_POST['fee_items'])) {

    $items = [];

    foreach ($_POST['fee_items'] as $row) {

        if (
            $row['admission_fee'] !== '' ||
            $row['q1_fee'] !== '' ||
            $row['add_on_fee'] !== ''
        ) {
            $items[] = [
                'admission_fee' => sanitize_text_field($row['admission_fee'] ?? ''),
                'q1_fee'        => sanitize_text_field($row['q1_fee'] ?? ''),
                'add_on_fee'    => sanitize_text_field($row['add_on_fee'] ?? ''),
            ];
        }
    }

    update_post_meta($post_id, '_fee_items', $items);
    

    } else {
        // If user removed all rows, clear meta
        delete_post_meta($post_id, '_fee_items');
    }
}

add_filter('post_row_actions', 'sfs_fee_add_copy_link', 10, 2);
function sfs_fee_add_copy_link($actions, $post) {

    if ($post->post_type !== 'sfs_fee') {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url('admin.php?action=sfs_fee_copy&post=' . $post->ID),
        'sfs_fee_copy_' . $post->ID
    );

    $actions['sfs_fee_copy'] = '<a href="' . esc_url($url) . '">Copy</a>';

    return $actions;
}

add_action('admin_action_sfs_fee_copy', 'sfs_fee_copy_post');
function sfs_fee_copy_post() {

    if (!isset($_GET['post'])) {
        wp_die('No post selected.');
    }

    $post_id = intval($_GET['post']);

    if (!wp_verify_nonce($_GET['_wpnonce'], 'sfs_fee_copy_' . $post_id)) {
        wp_die('Security check failed.');
    }

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'sfs_fee') {
        wp_die('Invalid post type.');
    }

    $new_post_id = wp_insert_post([
        'post_title'  => $post->post_title . ' (Copy)',
        'post_status' => 'draft',
        'post_type'   => 'sfs_fee',
    ]);

    $meta = get_post_meta($post_id);
    foreach ($meta as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }

    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit;
}

