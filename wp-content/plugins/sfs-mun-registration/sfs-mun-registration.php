<?php
/**
 * Plugin Name: SFS MUN Registration
 * Description: SFS Model United Nation form with shortcode, validation, save and admin view.
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

class SFS_MUN_Registration {

    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'create_table']);
        add_shortcode('sfs_mun_form', [$this, 'form_shortcode']);
        add_action('init', [$this, 'handle_submit']);
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
    }

    public function create_table() {
        global $wpdb;

        $table = $wpdb->prefix . 'sfs_mun_registrations';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            school_name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL,
            state VARCHAR(150) NOT NULL,
            school_contact VARCHAR(50) NOT NULL,
            school_email VARCHAR(150) NOT NULL,
            committees TEXT NOT NULL,
            muns_attended INT DEFAULT 0,
            mun_awards INT DEFAULT 0,
            participant_name VARCHAR(255) NOT NULL,
            class_name VARCHAR(100) NOT NULL,
            contact_no VARCHAR(50) NOT NULL,
            whatsapp_no VARCHAR(50) NOT NULL,
            email VARCHAR(150) NOT NULL,
            pickup_point VARCHAR(255) NOT NULL,
            accommodation VARCHAR(20) DEFAULT 'No',
            transport_intra VARCHAR(20) DEFAULT 'No',
            transport_inter VARCHAR(20) DEFAULT 'No',
            delegate_fee VARCHAR(50) DEFAULT '1199',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function assets() {
        wp_register_style('sfs-mun-style', false);
        wp_enqueue_style('sfs-mun-style');

        wp_add_inline_style('sfs-mun-style', '
            .sfs-mun-box {
                max-width: 940px;
                margin: 30px auto;
                border: 2px solid #9b123a;
                padding: 18px;
                font-family: Arial, sans-serif;
                background: #fff;
            }
            .sfs-mun-title {
                text-align: center;
                color: #9b123a;
                font-weight: 700;
                margin-bottom: 20px;
            }
            .sfs-row {
                display: flex;
                gap: 22px;
                margin-bottom: 14px;
                flex-wrap: wrap;
            }
            .sfs-col {
                flex: 1;
                min-width: 230px;
            }
            .sfs-col label {
                display: block;
                font-size: 14px;
                margin-bottom: 5px;
            }
            .sfs-col input,
            .sfs-col select {
                width: 100%;
                height: 36px;
                border: 1px solid #9b123a;
                padding: 6px 10px;
                font-size: 14px;
            }
            .sfs-required {
                color: #9b123a;
            }
            .sfs-section {
                border: 1px solid #9b123a;
                margin: 25px 0;
                padding: 12px;
            }
            .sfs-section h4 {
                text-align: center;
                color: #9b123a;
                margin: 0 0 10px;
                font-size: 18px;
            }
            .sfs-options label {
                margin-right: 12px;
                font-size: 13px;
                white-space: nowrap;
            }
            .sfs-note {
                text-align: center;
                color: #9b123a;
                font-weight: 700;
                font-size: 12px;
                margin-bottom: 10px;
            }
            .sfs-fee {
                border: 1px solid #9b123a;
                padding: 12px;
                font-weight: 700;
                display: flex;
                justify-content: space-between;
                gap: 20px;
            }
            .sfs-submit {
                background: #9b123a;
                color: #fff;
                border: none;
                padding: 10px 28px;
                cursor: pointer;
                font-weight: 700;
            }
            .sfs-submit:hover {
                background: #710b29;
            }
            .sfs-alert-success {
                background: #d4edda;
                color: #155724;
                padding: 12px;
                margin-bottom: 15px;
            }
            .sfs-alert-error {
                background: #f8d7da;
                color: #721c24;
                padding: 12px;
                margin-bottom: 15px;
            }
        ');
    }

    public function handle_submit() {
        if (!isset($_POST['sfs_mun_submit'])) return;

        if (!isset($_POST['sfs_mun_nonce']) || !wp_verify_nonce($_POST['sfs_mun_nonce'], 'sfs_mun_action')) {
            wp_die('Security check failed.');
        }

        $required = [
            'school_name', 'address', 'state', 'school_contact', 'school_email',
            'muns_attended', 'mun_awards', 'participant_name', 'class_name',
            'contact_no', 'whatsapp_no', 'email', 'pickup_point'
        ];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wp_redirect(add_query_arg('sfs_error', '1', wp_get_referer()));
                exit;
            }
        }

        if (empty($_POST['committees']) || count($_POST['committees']) != 3) {
            wp_redirect(add_query_arg('sfs_error', 'committee', wp_get_referer()));
            exit;
        }

        if (empty($_POST['agree'])) {
            wp_redirect(add_query_arg('sfs_error', 'agree', wp_get_referer()));
            exit;
        }

        if (!is_email($_POST['school_email']) || !is_email($_POST['email'])) {
            wp_redirect(add_query_arg('sfs_error', 'email', wp_get_referer()));
            exit;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'sfs_mun_registrations';

        $wpdb->insert($table, [
            'school_name'      => sanitize_text_field($_POST['school_name']),
            'address'          => sanitize_textarea_field($_POST['address']),
            'state'            => sanitize_text_field($_POST['state']),
            'school_contact'   => sanitize_text_field($_POST['school_contact']),
            'school_email'     => sanitize_email($_POST['school_email']),
            'committees'       => implode(', ', array_map('sanitize_text_field', $_POST['committees'])),
            'muns_attended'    => intval($_POST['muns_attended']),
            'mun_awards'       => intval($_POST['mun_awards']),
            'participant_name' => sanitize_text_field($_POST['participant_name']),
            'class_name'       => sanitize_text_field($_POST['class_name']),
            'contact_no'       => sanitize_text_field($_POST['contact_no']),
            'whatsapp_no'      => sanitize_text_field($_POST['whatsapp_no']),
            'email'            => sanitize_email($_POST['email']),
            'pickup_point'     => sanitize_text_field($_POST['pickup_point']),
            'accommodation'    => sanitize_text_field($_POST['accommodation'] ?? 'No'),
            'transport_intra'  => sanitize_text_field($_POST['transport_intra'] ?? 'No'),
            'transport_inter'  => sanitize_text_field($_POST['transport_inter'] ?? 'No'),
            'delegate_fee'     => '1199',
        ]);

        wp_redirect(add_query_arg('sfs_success', '1', wp_get_referer()));
        exit;
    }

    public function form_shortcode() {
        ob_start();

        if (isset($_GET['sfs_success'])) {
            echo '<div class="sfs-alert-success">Registration submitted successfully.</div>';
        }

        if (isset($_GET['sfs_error'])) {
            echo '<div class="sfs-alert-error">Please fill all required fields correctly. Select exactly 3 committees and accept terms.</div>';
        }
        ?>

        <form method="POST" class="sfs-mun-box">
            <?php wp_nonce_field('sfs_mun_action', 'sfs_mun_nonce'); ?>

            <h3 class="sfs-mun-title">SFS MODEL UNITED NATION 2025</h3>

            <div class="sfs-row">
                <div class="sfs-col">
                    <label>School Name <span class="sfs-required">*</span></label>
                    <input type="text" name="school_name" required placeholder="School name">
                </div>

                <div class="sfs-col">
                    <label>Address of Institution <span class="sfs-required">*</span></label>
                    <input type="text" name="address" required placeholder="Address of Institution">
                </div>

                <div class="sfs-col">
                    <label>State <span class="sfs-required">*</span></label>
                    <input type="text" name="state" required placeholder="State of Institution">
                </div>
            </div>

            <div class="sfs-row">
                <div class="sfs-col">
                    <label>Official School Contact <span class="sfs-required">*</span></label>
                    <input type="text" name="school_contact" required>
                </div>

                <div class="sfs-col">
                    <label>Official School Email <span class="sfs-required">*</span></label>
                    <input type="email" name="school_email" required>
                </div>
            </div>

            <div class="sfs-section">
                <h4>Committee Preferences <small>(Select any three option)</small></h4>
                <div class="sfs-note">
                    Committee Allotment is subject to availability. Each committee has a maximum occupancy limit.
                </div>

                <div class="sfs-options">
                    <?php
                    $committees = ['UNSC (Double Del)', 'UNCTAD', 'UNWTO', 'HAIPPM', 'Crisis Committee', 'IPC-J', 'IPC-C', 'IPC-PJ'];
                    foreach ($committees as $committee) {
                        echo '<label><input type="checkbox" name="committees[]" value="' . esc_attr($committee) . '"> ' . esc_html($committee) . '</label>';
                    }
                    ?>
                </div>
            </div>

            <h4 class="sfs-mun-title">Participant Details</h4>

            <div class="sfs-row">
                <div class="sfs-col">
                    <label>Number of MUNs attended <span class="sfs-required">*</span></label>
                    <input type="number" name="muns_attended" min="0" value="0" required>
                </div>

                <div class="sfs-col">
                    <label>Number of MUN Awards <span class="sfs-required">*</span></label>
                    <input type="number" name="mun_awards" min="0" value="0" required>
                </div>
            </div>

            <div class="sfs-row">
                <div class="sfs-col">
                    <label>Name of Participant <span class="sfs-required">*</span></label>
                    <input type="text" name="participant_name" required>
                </div>

                <div class="sfs-col">
                    <label>Class <span class="sfs-required">*</span></label>
                    <select name="class_name" required>
                        <option value="">Select Class</option>
                        <option>VIII</option>
                        <option>IX</option>
                        <option>X</option>
                        <option>XI</option>
                        <option>XII</option>
                    </select>
                </div>

                <div class="sfs-col">
                    <label>Contact No <span class="sfs-required">*</span></label>
                    <input type="text" name="contact_no" required>
                </div>
            </div>

            <div class="sfs-row">
                <div class="sfs-col">
                    <label>WhatsApp No <span class="sfs-required">*</span></label>
                    <input type="text" name="whatsapp_no" required>
                </div>

                <div class="sfs-col">
                    <label>Email Id <span class="sfs-required">*</span></label>
                    <input type="email" name="email" required>
                </div>

                <div class="sfs-col">
                    <label>Pick Up Point (Nearest Bus Stop) <span class="sfs-required">*</span></label>
                    <input type="text" name="pickup_point" required>
                </div>
            </div>

            <p>
                Accommodation Requirement (Chargeable)
                <label><input type="radio" name="accommodation" value="No" checked> No</label>
                <label><input type="radio" name="accommodation" value="Yes"> Yes</label>
            </p>

            <p>
                Transportation(Intra-city)
                <label><input type="radio" name="transport_intra" value="No" checked> No</label>
                <label><input type="radio" name="transport_intra" value="Yes"> Yes</label>

                &nbsp;&nbsp;

                Transportation(Inter-city) <b><i>Chargeable for out-station delegates</i></b>
                <label><input type="radio" name="transport_inter" value="No" checked> No</label>
                <label><input type="radio" name="transport_inter" value="Yes"> Yes</label>
            </p>

            <div class="sfs-section">
                <h4>Delegate Fee Phase 1</h4>
                <div class="sfs-fee">
                    <span>DELEGATE FEE including training materials, delegate kit, entry to all committee sessions, orientations and recreational events</span>
                    <span>₹1199</span>
                </div>
            </div>

            <p>
                <label>
                    <input type="checkbox" name="agree" value="1" required>
                    I agree to abide by the rules and regulations of SFS MODEL UNITED NATION 2025
                </label>
            </p>

            <button type="submit" name="sfs_mun_submit" class="sfs-submit">Submit Registration</button>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="committees[]"]');

            checkboxes.forEach(function (box) {
                box.addEventListener('change', function () {
                    let checked = document.querySelectorAll('input[name="committees[]"]:checked');

                    if (checked.length > 3) {
                        this.checked = false;
                        alert('Please select only three committee preferences.');
                    }
                });
            });
        });
        </script>

        <?php
        return ob_get_clean();
    }

    public function admin_menu() {
        add_menu_page(
            'SFS MUN Registrations',
            'SFS MUN',
            'manage_options',
            'sfs-mun-registrations',
            [$this, 'admin_page'],
            'dashicons-forms',
            26
        );
    }

    public function admin_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'sfs_mun_registrations';
        $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
        ?>

        <div class="wrap">
            <h1>SFS MUN Registrations</h1>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>School</th>
                        <th>Participant</th>
                        <th>Class</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Committees</th>
                        <th>Fee</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rows): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo esc_html($row->id); ?></td>
                                <td><?php echo esc_html($row->school_name); ?></td>
                                <td><?php echo esc_html($row->participant_name); ?></td>
                                <td><?php echo esc_html($row->class_name); ?></td>
                                <td><?php echo esc_html($row->contact_no); ?></td>
                                <td><?php echo esc_html($row->email); ?></td>
                                <td><?php echo esc_html($row->committees); ?></td>
                                <td>₹<?php echo esc_html($row->delegate_fee); ?></td>
                                <td><?php echo esc_html($row->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No registrations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
    }
}

new SFS_MUN_Registration();