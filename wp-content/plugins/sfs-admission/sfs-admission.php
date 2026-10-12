<?php
/*
Plugin Name: SFS Admission System
Description: Admission system with API dropdowns and register modal.
Version: 1.2
Author: Badal Sutradhar - Vasp Technologies
*/

if (!defined('ABSPATH')) exit;

add_action('restrict_manage_posts', function () {

    global $typenow;

    if ($typenow !== 'sfs_registration') return;

    echo '<a href="' . admin_url('admin-post.php?action=sfs_export_registration') . '" 
            class="button button-primary"
            style="margin-left:10px;">
            Export Registrations (Excel)
          </a>';
});

add_action('admin_post_sfs_export_registration', 'sfs_export_registration');


function sfs_export_registration() {

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=sfs-registrations.csv');

    $output = fopen('php://output', 'w');

    /* ------------------------------
       Excel Headers
    ------------------------------ */
    fputcsv($output, [
        'form_id',
        'ses',
        'sname',
        'dob',
        'class_name',
        'lastclass',
        'lastSchool',
        'fname',
        'mname',
        'Email',
        'phoneno',
        'Address',
        'Entrydate'
    ]);

    /* ------------------------------
       Query Admission Posts
    ------------------------------ */
    $q = new WP_Query([
        'post_type'      => 'sfs_registration',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    ]);

    while ($q->have_posts()) {

        $q->the_post();

        fputcsv($output, [
            0,
            get_post_meta(get_the_ID(), '_sfs_session', true),
            get_post_meta(get_the_ID(), '_sfs_student_name', true),
            get_post_meta(get_the_ID(), '_sfs_dob', true),
            get_post_meta(get_the_ID(), '_sfs_class', true),
            get_post_meta(get_the_ID(), '_sfs_last_class', true),
            get_post_meta(get_the_ID(), '_sfs_school', true),
            get_post_meta(get_the_ID(), '_sfs_father', true),
            get_post_meta(get_the_ID(), '_sfs_mother', true),
            get_post_meta(get_the_ID(), '_sfs_email', true),
            get_post_meta(get_the_ID(), '_sfs_mobile', true),
            get_post_meta(get_the_ID(), '_sfs_remarks', true),
            get_the_date('Y-m-d H:i:s')
        ]);
    }

    wp_reset_postdata();
    fclose($output);
    exit;
}



add_action('wp_ajax_sfs_submit_registration', 'sfs_submit_registration');
add_action('wp_ajax_nopriv_sfs_submit_registration', 'sfs_submit_registration');

function sfs_submit_registration() {

    if (empty($_POST['mobile']) || empty($_POST['student_name'])) {
        wp_send_json_error('Required fields missing');
    }

    $mobile = sanitize_text_field($_POST['mobile']);

    // if (!get_transient('verified_'.$mobile)) {
    //     wp_send_json_error('Mobile number not verified');
    // }

    $post_id = wp_insert_post([
        'post_type'   => 'sfs_registration',
        'post_title'  => sanitize_text_field($_POST['student_name']).' - '.$mobile,
        'post_status' => 'publish',
    ]);

    if (!$post_id) {
        wp_send_json_error('Unable to save');
    }

    // Save fields
    $fields = [
        'mobile','email','student_name','dob','class','session',
        'last_class','school','father','mother','remarks',
    ];

    foreach ($fields as $field) {
        update_post_meta($post_id, '_sfs_'.$field,
            sanitize_text_field($_POST[$field] ?? '')
        );
    }

    // FILE UPLOAD
    $attachments = [];
    $allowed_types = ['image/jpeg','image/png','application/pdf'];

    if (!empty($_FILES)) {

        require_once ABSPATH.'wp-admin/includes/file.php';

        foreach ($_FILES as $key => $file) {

            if (!empty($file['name'])) {

                if (!in_array($file['type'], $allowed_types)) {
                    wp_send_json_error('Invalid file type');
                }

                if ($file['size'] > 2 * 1024 * 1024) {
                    wp_send_json_error('File too large');
                }

                $upload = wp_handle_upload($file, ['test_form'=>false]);

                if (!isset($upload['error'])) {

                    update_post_meta($post_id, '_sfs_'.$key,
                        esc_url_raw($upload['url'])
                    );

                    // Save path for email attachment
                    $attachments[] = $upload['file'];
                }
            }
        }
    }

    delete_transient('verified_'.$mobile);

    /*
    =====================================
    SEND DATA TO EXTERNAL API
    =====================================
    */

    $api_url = "https://websiteapi.sfsguwahati.ac.in/api/home/registration";

    $body = [
        '_mobno'         => $mobile,
        '_email'         => sanitize_email($_POST['email']),
        '_studentname'   => sanitize_text_field($_POST['student_name']),
        '_dob'           => sanitize_text_field($_POST['dob']),
        '_cls'           => sanitize_text_field($_POST['class']),
        '_admissionyar'  => sanitize_text_field($_POST['session']),
        '_lastclass'     => sanitize_text_field($_POST['last_class']),
        '_currentschool' => sanitize_text_field($_POST['school']),
        '_father'        => sanitize_text_field($_POST['father']),
        '_mother'        => sanitize_text_field($_POST['mother']),
        '_remarks'       => sanitize_textarea_field($_POST['remarks']),
        '_status'        => 'new'
    ];

    // Attach files to API request
    foreach (['file1','file2','file3'] as $file_key) {

        if (!empty($_FILES[$file_key]['tmp_name'])) {

            $body['files[]'] = curl_file_create(
                $_FILES[$file_key]['tmp_name'],
                $_FILES[$file_key]['type'],
                $_FILES[$file_key]['name']
            );
        }
    }

    $args = [
        'method'  => 'POST',
        'timeout' => 30,
        'headers' => [
            'X-API-KEY' => 'G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
            'Accept'    => 'application/json',
        ],
        'body' => $body,
    ];

    // $response = wp_remote_post($api_url, $args);

    // if (is_wp_error($response)) {
    //     error_log('API Error: '.$response->get_error_message());
    // } else {
    //     error_log('API Response: '.wp_remote_retrieve_body($response));
    // }

    /*
    =====================================
    SEND EMAIL WITH ATTACHMENTS
    =====================================
    */

    $to = "admission@sfsguwahati.ac.in";
    $subject = "New Admission Registration";

    $message = "
    <h3>New Admission Registration</h3>
    <p><b>Student:</b> ".$_POST['student_name']."</p>
    <p><b>Mobile:</b> ".$mobile."</p>
    <p><b>Email:</b> ".$_POST['email']."</p>
    <p><b>Class:</b> ".$_POST['class']."</p>
    <p><b>Session:</b> ".$_POST['session']."</p>
    <p><b>Father:</b> ".$_POST['father']."</p>
    <p><b>Mother:</b> ".$_POST['mother']."</p>
    ";

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($to, $subject, $message, $headers, $attachments);

    wp_send_json_success('Registration submitted successfully');
}


add_action('init', function () {

    register_post_type('sfs_admission', [
        'labels' => [
            'name' => 'SFS Take Admission',
            'singular_name' => 'SFS Take Admission',
        ],
        'public' => false,
        'show_ui' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'rewrite' => false,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => ['title', 'editor'],
    ]);
  
    register_post_type('sfs_registration', [
        'labels' => [
            'name' => 'Registrations',
            'singular_name' => 'Registration',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-id',
        'supports' => ['title'],
        'show_in_menu' => true,
    ]);
});

add_action('add_meta_boxes_sfs_registration', function () {
    add_meta_box(
        'sfs_registration_details',
        'Registration Details',
        'sfs_registration_meta_box',
        'sfs_registration',
        'normal',
        'high'
    );
});

function sfs_registration_meta_box($post) {

    $fields = [
        'mobile'       => 'Mobile',
        'email'        => 'Email',
        'student_name' => 'Student Name',
        'dob'          => 'DOB',
        'class'        => 'Class',
        'session'      => 'Session',
        'last_class'   => 'Last Class',
        'school'       => 'School',
        'father'       => 'Father Name',
        'mother'       => 'Mother Name',
        'remarks'      => 'Remarks',
    ];

    echo "<table class='widefat striped'>";

    foreach ($fields as $key => $label) {

        $value = get_post_meta($post->ID, '_sfs_'.$key, true);

        echo "<tr>
                <td style='width:200px'><strong>$label</strong></td>
                <td>".esc_html($value)."</td>
              </tr>";
    }

    // Uploaded files
    foreach (['file1','file2','file3'] as $file) {

        $url = get_post_meta($post->ID, '_sfs_'.$file, true);

        echo "<tr>
                <td><strong>".strtoupper($file)."</strong></td>
                <td>";

        if ($url) {
            echo "<a href='".esc_url($url)."' target='_blank'>View File</a>";
        } else {
            echo "Not uploaded";
        }

        echo "</td></tr>";
    }

    echo "</table>";
}


add_action('edit_form_top', 'sfs_add_back_button_admission');

function sfs_add_back_button_admission($post) {

    // Change this to your post type
    if ($post->post_type !== 'sfs_admission') {
        return;
    }

    $list_url = admin_url('edit.php?post_type=sfs_admission');
    ?>

    <div style="margin-bottom:10px;">
        <a href="<?php echo esc_url($list_url); ?>"
           class="button button-secondary">
            ? Back to Admission List
        </a>
    </div>

    <?php
}

/*==================================================
  2. META BOX: YEAR, CLASS, STATUS
==================================================*/
add_action('add_meta_boxes', function () {
    add_meta_box(
        'sfs_admission_meta',
        'Admission Settings',
        'sfs_meta_callback',
        'sfs_admission',
        'normal',
        'high'
    );
});

function sfs_meta_callback($post) {

    $year   = get_post_meta($post->ID, '_sfs_year', true);
    $class  = get_post_meta($post->ID, '_sfs_class', true);
    $status = get_post_meta($post->ID, '_sfs_status', true);

    wp_nonce_field('sfs_save_meta', 'sfs_nonce');
    ?>

    <p>
        <label><b>Academic Year</b></label>
        <select name="sfs_year" id="sfs_year" style="width:100%;">
            <option value="">Loading...</option>
        </select>
    </p>
    
    <p>
        <label><b>Class</b></label>
        <select name="sfs_class" id="sfs_class" style="width:100%;">
            <option value="">Select Year First</option>
        </select>
    </p>


    <p>
        <label><b>Admission Status</b></label>
        <select name="sfs_status" style="width:100%;">
            <option value="open" <?php selected($status, 'open'); ?>>Open</option>
            <option value="closed" <?php selected($status, 'closed'); ?>>Closed</option>
        </select>
    </p>
    <?php
}

/*==================================================
  3. SAVE META
==================================================*/
add_action('save_post', function ($post_id) {

    if (!isset($_POST['sfs_nonce']) || !wp_verify_nonce($_POST['sfs_nonce'], 'sfs_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    update_post_meta($post_id, '_sfs_year', sanitize_text_field($_POST['sfs_year'] ?? ''));
    update_post_meta($post_id, '_sfs_class', sanitize_text_field($_POST['sfs_class'] ?? ''));
    update_post_meta($post_id, '_sfs_status', sanitize_text_field($_POST['sfs_status'] ?? 'open'));
});

/*==================================================
  4. SHORTCODE
==================================================*/
add_shortcode('sfs_admission_form', function () {
    ob_start(); ?>
    <div id="sfsAdmissionForm" class="row">
        <div class="col-lg-6 col-md-6 col-sm-12" style="margin-top: 10px;">
            <select id="admissionYear" class="form-select">
                <option>Loading...</option>
            </select>            
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12" style="margin-top: 10px;">
            <select id="admissionClass" class="form-select">
                <option>Select Class</option>
            </select>
        </div>
        <div id="ajaxLoader" style="display:none;">
          Loading...
        </div>
    </div>
    <br><br>
    <div id="classStatusMsg"></div>
    <?php return ob_get_clean();
});

/*==================================================
  5. SCRIPTS
==================================================*/
add_action('wp_enqueue_scripts', function () {
    
    // CSS
    wp_enqueue_style(
        'sfs-admission-css',
        plugin_dir_url(__FILE__) . 'assets/css/admission.css',
        [],
        time()
    );

    // CSS
    wp_enqueue_style(
        'sfs-admission-css-flatpickr',
        'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
        [],
        time()
    );
    
    wp_enqueue_script(
        'sfs-admission-js',
        plugin_dir_url(__FILE__) . 'assets/js/admission.js?v=2',
        ['jquery'],
        time(),
        true
    );

    wp_enqueue_script(
        'sfs-admission-js-flatpickr',
        'https://cdn.jsdelivr.net/npm/flatpickr',
        ['jquery'],
        time(),
        true
    );

    wp_localize_script('sfs-admission-js', 'SFS', [
        'ajax' => admin_url('admin-ajax.php')
    ]);

  
});

add_action('admin_enqueue_scripts', function ($hook) {

    // Only for sfs_admission post type
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'sfs_admission') {
        return;
    }

    // Load on list + edit + add new
    if (!in_array($hook, ['edit.php', 'post.php', 'post-new.php'], true)) {
        return;
    }

    wp_enqueue_script(
        'sfs-admin-js',
        plugin_dir_url(__FILE__) . 'assets/js/admin-admission.js',
        ['jquery'],
        time(),
        true
    );

    // Only pass year/class on EDIT screen
    $year  = '';
    $class = '';

    if ($hook === 'post.php' && isset($_GET['post'])) {
        $post_id = intval($_GET['post']);
        $year  = get_post_meta($post_id, '_sfs_year', true);
        $class = get_post_meta($post_id, '_sfs_class', true);
    }

    wp_localize_script('sfs-admin-js', 'SFS_ADMIN', [
        'ajax'  => admin_url('admin-ajax.php'),
        'year'  => $year,
        'class' => $class,
    ]);
});

/*==================================================
  5.1 AJAX: LOAD ACADEMIC YEARS & CLASSES
==================================================*/
add_action('wp_ajax_load_years', 'sfs_admission_load_years', 1);
add_action('wp_ajax_nopriv_load_years', 'sfs_admission_load_years', 1);

if (!function_exists('sfs_admission_load_years')) {
    function sfs_admission_load_years() {
        // $url = 'https://websiteapi.sfsguwahati.ac.in/api/home/activesession';
    	$url = 'https://websiteapi.sfsguwahati.ac.in/api/home/academic-years';
        $token = "G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf";

        $response = null;

        // Attempt 1: cURL with SSL bypass
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Referer: https://sfsguwahati.ac.in',
                    'Accept-Language: en-US,en;q=0.9',
                    'X-API-KEY: ' . $token
                ],
                CURLOPT_TIMEOUT => 15
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        // Attempt 2: WordPress native wp_remote_get
        if (empty($response) || !is_string($response)) {
            $wp_res = wp_remote_get($url, [
                'headers' => [
                    'Accept'    => 'application/json',
                    'X-API-KEY' => $token,
                    'Referer'   => 'https://sfsguwahati.ac.in'
                ],
                'sslverify' => false,
                'timeout'   => 15
            ]);
            if (!is_wp_error($wp_res)) {
                $response = wp_remote_retrieve_body($wp_res);
            }
        }

        $data = !empty($response) ? json_decode($response, true) : null;

        // If API returned a valid array of sessions, send it
        if (is_array($data) && !empty($data)) {
            wp_send_json($data);
        }

        // Reliable fallback sessions if API is unreachable or returns non-JSON
        $curr_year = (int) date('Y');
        $curr_month = (int) date('n');
        $session_start = ($curr_month >= 4) ? $curr_year : ($curr_year - 1);

        $fallback = [
            [
                'id'        => $session_start . ($session_start + 1),
                'name'      => $session_start . '-' . ($session_start + 1),
                'isCurrent' => '1'
            ],
            [
                'id'        => ($session_start + 1) . ($session_start + 2),
                'name'      => ($session_start + 1) . '-' . ($session_start + 2),
                'isCurrent' => '1'
            ]
        ];

        wp_send_json($fallback);
    }
}

add_action('wp_ajax_load_classes', 'sfs_admission_load_classes', 1);
add_action('wp_ajax_nopriv_load_classes', 'sfs_admission_load_classes', 1);

if (!function_exists('sfs_admission_load_classes')) {
    function sfs_admission_load_classes() {
        $fromyear = sanitize_text_field($_POST['fromyear'] ?? '');
        $sessyear = sanitize_text_field($_POST['sessyear'] ?? '');

        $url = "https://websiteapi.sfsguwahati.ac.in/api/home/classes-by-year?fromyear=" . urlencode($fromyear) . "&sessyear=" . urlencode($sessyear);
        $token = "G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf";

        $response = null;

        // Attempt 1: cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Referer: https://sfsguwahati.ac.in',
                    'Accept-Language: en-US,en;q=0.9',
                    'X-API-KEY: ' . $token
                ],
                CURLOPT_TIMEOUT => 15
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        // Attempt 2: WordPress native wp_remote_get
        if (empty($response) || !is_string($response)) {
            $wp_res = wp_remote_get($url, [
                'headers' => [
                    'Accept'    => 'application/json',
                    'X-API-KEY' => $token,
                    'Referer'   => 'https://sfsguwahati.ac.in'
                ],
                'sslverify' => false,
                'timeout'   => 15
            ]);
            if (!is_wp_error($wp_res)) {
                $response = wp_remote_retrieve_body($wp_res);
            }
        }

        $data = !empty($response) ? json_decode($response, true) : null;

        if (is_array($data) && !empty($data)) {
            wp_send_json($data);
        }

        // Fallback classes if API is unreachable
        $fallback_classes = [
            ["displayNo" => "0", "classId" => "17", "openStatus" => "1", "clsDisp" => "NURSERY", "stream" => ""],
            ["displayNo" => "1", "classId" => "14", "openStatus" => "1", "clsDisp" => "LKG", "stream" => ""],
            ["displayNo" => "2", "classId" => "15", "openStatus" => "1", "clsDisp" => "UKG", "stream" => ""],
            ["displayNo" => "3", "classId" => "1",  "openStatus" => "1", "clsDisp" => "1", "stream" => ""],
            ["displayNo" => "4", "classId" => "2",  "openStatus" => "0", "clsDisp" => "2", "stream" => ""],
            ["displayNo" => "5", "classId" => "3",  "openStatus" => "0", "clsDisp" => "3", "stream" => ""],
            ["displayNo" => "6", "classId" => "4",  "openStatus" => "0", "clsDisp" => "4", "stream" => ""],
            ["displayNo" => "7", "classId" => "5",  "openStatus" => "0", "clsDisp" => "5", "stream" => ""],
            ["displayNo" => "8", "classId" => "6",  "openStatus" => "0", "clsDisp" => "6", "stream" => ""],
            ["displayNo" => "9", "classId" => "7",  "openStatus" => "0", "clsDisp" => "7", "stream" => ""],
            ["displayNo" => "10", "classId" => "8", "openStatus" => "0", "clsDisp" => "8", "stream" => ""],
            ["displayNo" => "11", "classId" => "9", "openStatus" => "0", "clsDisp" => "9", "stream" => ""],
            ["displayNo" => "12", "classId" => "10", "openStatus" => "0", "clsDisp" => "10", "stream" => ""],
            ["displayNo" => "13", "classId" => "11", "openStatus" => "0", "clsDisp" => "11", "stream" => ""],
            ["displayNo" => "14", "classId" => "12", "openStatus" => "0", "clsDisp" => "12", "stream" => ""]
        ];

        wp_send_json($fallback_classes);
    }
}

/*==================================================
  6. AJAX LOAD CONTENT
==================================================*/
add_action('wp_ajax_sfs_load_content', 'sfs_load_content');
add_action('wp_ajax_nopriv_sfs_load_content', 'sfs_load_content');

function sfs_load_content() {

    $year   = sanitize_text_field($_POST['year']);
    $class  = sanitize_text_field($_POST['class']);
    $status = sanitize_text_field($_POST['status'] ?? '');

    $q = new WP_Query([
        'post_type'      => 'sfs_admission',
        'posts_per_page' => 1,
        'meta_query'     => [
            [
                'key'   => '_sfs_year',
                'value' => $year
            ],
            [
                'key'   => '_sfs_class',
                'value' => $class
            ]
        ]
    ]);

    if ($q->have_posts()) {

        $q->the_post();
        $post_id = get_the_ID();

        /* -----------------------
           UPDATE STATUS IF SENT
        ----------------------- */

        if ($status === '1') {
            $status = 'open';
        } elseif ($status === '0') {
            $status = 'closed';
        }

        if ($status && in_array($status, ['open','closed'])) {
            update_post_meta($post_id, '_sfs_status', $status);
        }

        /* -----------------------
           GET STATUS + CONTENT
        ----------------------- */

        $current_status = get_post_meta($post_id, '_sfs_status', true);

        wp_send_json_success([
            'status'  => $current_status,
            'content' => apply_filters('the_content', get_the_content())
        ]);
    }

    wp_send_json_error('No admission found');
}

// function sfs_load_content() {

//     $args = [
//         'post_type'      => 'sfs_admission',
//         'posts_per_page' => -1
//     ];

//     $q = new WP_Query($args);

//     $data = [];

//     while ($q->have_posts()) {
//         $q->the_post();

//         $data[] = [
//             'id'     => get_the_ID(),
//             'title'  => get_the_title(),
//             'year'   => get_post_meta(get_the_ID(), '_sfs_year', true),
//             'class'  => get_post_meta(get_the_ID(), '_sfs_class', true),
//             'status' => get_post_meta(get_the_ID(), '_sfs_status', true),
//         ];
//     }

//     wp_send_json($data);
// }

add_filter('manage_sfs_admission_posts_columns', function ($columns) {

    $new = [];

    foreach ($columns as $key => $value) {
        $new[$key] = $value;

        if ($key === 'title') {
            $new['sfs_status'] = 'Admission Status';
            $new['sfs_action'] = 'Update';
        }
    }

    return $new;
});


add_action('manage_sfs_admission_posts_custom_column', function ($column, $post_id) {

    if ($column === 'sfs_status') {
        $status = get_post_meta($post_id, '_sfs_status', true) ?: 'closed';

        echo $status === 'open'
            ? '<span style="color:green;font-weight:bold;">OPEN</span>'
            : '<span style="color:red;font-weight:bold;">CLOSED</span>';
    }

    if ($column === 'sfs_action') {
        $status = get_post_meta($post_id, '_sfs_status', true) ?: 'closed';
        $next   = $status === 'open' ? 'closed' : 'open';

        echo '<button 
                class="button sfs-toggle-status"
                data-id="'.$post_id.'"
                data-status="'.$next.'">
                '.ucfirst($next).'
              </button>';
    }

}, 10, 2);


add_action('wp_ajax_sfs_toggle_admission_status', function () {

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied');
    }

    $post_id = intval($_POST['post_id']);
    $status  = sanitize_text_field($_POST['status']);

    if (!in_array($status, ['open','closed'], true)) {
        wp_send_json_error('Invalid status');
    }

    update_post_meta($post_id, '_sfs_status', $status);

    wp_send_json_success([
        'new_status' => $status
    ]);
});

add_filter('post_row_actions', 'sfs_add_copy_link', 10, 2);
function sfs_add_copy_link($actions, $post) {

    if ($post->post_type !== 'sfs_admission') {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url('admin.php?action=sfs_copy_admission&post=' . $post->ID),
        'sfs_copy_admission_' . $post->ID
    );

    $actions['sfs_copy'] = '<a href="' . esc_url($url) . '">Copy</a>';

    return $actions;
}

add_action('admin_action_sfs_copy_admission', 'sfs_copy_admission_post');
function sfs_copy_admission_post() {

    if (!isset($_GET['post'])) {
        wp_die('No post to copy.');
    }

    $post_id = intval($_GET['post']);

    if (!wp_verify_nonce($_GET['_wpnonce'], 'sfs_copy_admission_' . $post_id)) {
        wp_die('Security check failed.');
    }

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'sfs_admission') {
        wp_die('Invalid post.');
    }

    // Create new post
    $new_post_id = wp_insert_post([
        'post_title'   => $post->post_title . ' (Copy)',
        'post_content' => $post->post_content,
        'post_status'  => 'draft',
        'post_type'    => 'sfs_admission',
    ]);

    // Copy all meta
    $meta = get_post_meta($post_id);
    foreach ($meta as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }

    // Redirect to edit screen
    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit;
}

add_action('wp_ajax_send_otp_api', 'send_otp_api');
add_action('wp_ajax_nopriv_send_otp_api', 'send_otp_api');

function send_otp_api() {

    $mobile = sanitize_text_field($_POST['mobile']);

    if (!$mobile) {
        wp_send_json_error('Mobile number required');
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store OTP temporarily (5 min)
    set_transient('otp_'.$mobile, $otp, 300);


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://websiteapi.sfsguwahati.ac.in/api/home/registration/get-otp',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "mobNo": "7005802715"
    }',
      CURLOPT_HTTPHEADER => array(
        'X-API-KEY: G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    if($mobile == "7005802715") {
      if (curl_errno($curl)) {
        $curl_error = curl_error($curl);
        wp_send_json_error("My-error: $curl_error");
      } else {
        wp_send_json_success ("My-success: $response");        
      }
    }    

    // TEMP debug
    error_log("OTP for $mobile = $otp");

    wp_send_json_success("OTP Sent $otp");
}



add_action('wp_ajax_verify_otp_api', 'verify_otp_api');
add_action('wp_ajax_nopriv_verify_otp_api', 'verify_otp_api');

function verify_otp_api() {

    $mobile = sanitize_text_field($_POST['mobile']);
    $otp    = sanitize_text_field($_POST['otp']);

    $stored_otp = get_transient('otp_'.$mobile);

    if (!$stored_otp) {
        wp_send_json_error('OTP expired');
    }

    if ($stored_otp == $otp) {

        delete_transient('otp_'.$mobile);
        set_transient('verified_'.$mobile, true, 600);

        /* ===== FUTURE API CALL (VERIFY OTP LOG) =====
        $api_url = "https://websiteapi.sfsguwahati.ac.in/api/registration/validateotp";

        wp_remote_post($api_url, [
            'method'  => 'POST',
            'timeout' => 20,
            'headers' => [
                'X-API-KEY' => 'G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
                'Accept' => 'application/json',
            ],
            'body' => [
                'str1' => $mobile,
                'str2' => $mobile,
                'str3' => $mobile
            ],
        ]);
        */

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://websiteapi.sfsguwahati.ac.in/api/home/registration/verify-otp',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "str1": "7005802715",
            "str2": "78654",
            "str1": "8_0"
        }',
          CURLOPT_HTTPHEADER => array(
            'X-API-KEY: G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
          $curl_error = curl_error($curl);
          wp_send_json_error("OTP Verified error: $curl_error");
        } else {
          wp_send_json_success ("OTP Verified success: $response");        
        }

    } else {
        wp_send_json_error('Invalid OTP');
    }
}

/* =====================================
   SFS REGISTRATION GET API
===================================== */

add_action('rest_api_init', function () {

    register_rest_route('sfs/v1', '/registrations', [
        'methods'  => 'GET',
        'callback' => 'sfs_get_registrations_api',
        'permission_callback' => 'sfs_api_auth_check'
    ]);

});


function sfs_api_auth_check($request) {

    $api_key = $request->get_header('x-api-key');

    $valid_key = 'SFS_!%HAHAHA$2026_@'; // change this

    if (!$api_key || $api_key !== $valid_key) {

        return new WP_Error(
            'unauthorized',
            'Invalid API Key',
            ['status' => 401]
        );

    }

    return true;
}

function sfs_get_registrations_api($request) {

    // Get entrydate from query or header
    $entrydate = $request->get_param('entrydate');

    if (!$entrydate) {
        $entrydate = $request->get_header('entrydate');
    }

    $entrydate = sanitize_text_field($entrydate);

    $args = [
        'post_type'      => 'sfs_registration',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    ];

    /* =========================
       Entrydate Filter
    ========================= */

    if (!empty($entrydate)) {

        $dateObj = DateTime::createFromFormat('d/m/Y H:i', $entrydate);

if (!$dateObj) {
    $dateObj = DateTime::createFromFormat('d/m/Y', $entrydate);
}

        if ($dateObj) {

            $formattedDate = $dateObj->format('Y-m-d H:i:s');

            $args['date_query'] = [
                [
                    'column'    => 'post_date',
                    'after'     => $formattedDate,
                    'inclusive' => false
                ]
            ];
        }
    }

    $q = new WP_Query($args);

    $data = [];

    while ($q->have_posts()) {

        $q->the_post();
        $id = get_the_ID();
		
        $data[] = [

            'form_id'    => $id,
            'ses'        => get_post_meta($id, '_sfs_session', true),
            'sname'      => get_post_meta($id, '_sfs_student_name', true),
            'dob'        => get_post_meta($id, '_sfs_dob', true),
            'class_name' => get_post_meta($id, '_sfs_class', true),
            'lastclass'  => get_post_meta($id, '_sfs_last_class', true),
            'lastSchool' => get_post_meta($id, '_sfs_school', true),
            'fname'      => get_post_meta($id, '_sfs_father', true),
            'mname'      => get_post_meta($id, '_sfs_mother', true),
            'Email'      => get_post_meta($id, '_sfs_email', true),
            'phoneno'    => get_post_meta($id, '_sfs_mobile', true),
            'Address'    => get_post_meta($id, '_sfs_remarks', true),
            'Entrydate'  => get_post_field('post_date', $id),

            // Attachments
            'fileurl1'   => get_post_meta($id, '_sfs_file1', true),
            'fileurl2'   => get_post_meta($id, '_sfs_file2', true),
            'fileurl3'   => get_post_meta($id, '_sfs_file3', true)

        ];
    }

    wp_reset_postdata();

    return rest_ensure_response($data);
}
