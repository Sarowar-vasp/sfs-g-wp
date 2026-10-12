<?php
/**
 * Plugin Name: School Academic Calendar
 * Description: Displays academic year calendar (April-March) with events popup from API
 * Version: 1.0
 * Author: Vasp Technologies - Badal Sutradhar
 */

if (!defined('ABSPATH')) exit;


if (!class_exists('School_Academic_Calendar')) {

class School_Academic_Calendar {

    public static function init() {
        add_shortcode('academic_calendar', [__CLASS__, 'render_calendar']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_ajax_get_month_events', [__CLASS__, 'ajax_get_month_events']);
        add_action('wp_ajax_nopriv_get_month_events', [__CLASS__, 'ajax_get_month_events']);
    }

    public static function enqueue_assets() {
        if (!is_admin()) {
            wp_enqueue_style('academic-calendar-css', plugins_url('style.css', __FILE__));
            wp_enqueue_script('academic-calendar-js', plugins_url('script.js', __FILE__), ['jquery'], '1.0', true);
            wp_localize_script('academic-calendar-js', 'ac_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('ac_nonce')
            ]);
        }
    }

    // Get current, previous & next academic sessions
    public static function get_sessions() {
        $current_year = date('Y');
        $current_month = date('n');

        // If current month is Jan-Mar ? current session started last year
        if ($current_month <= 3) {
            $current_session = ($current_year - 1) . '-' . $current_year;
            $previous_session = ($current_year - 2) . '-' . ($current_year - 1);
            $next_session = $current_year . '-' . ($current_year + 1);
        } else {
            $current_session = $current_year . '-' . ($current_year + 1);
            $previous_session = ($current_year - 1) . '-' . $current_year;
            $next_session = ($current_year + 1) . '-' . ($current_year + 2);
        }

        return [
            'current'  => $current_session,
            'previous' => $previous_session,
            'next'     => $next_session
        ];
    }

    // Generate April to March month list for a session
    public static function get_months($session) {
        list($from, $to) = explode('-', $session);
        $months = [];

        // April to December of from year
        for ($m = 4; $m <= 12; $m++) {
            $months[] = [
                'id'   => $m,
                'name' => date('F Y', strtotime("$from-$m-01")),
                'year' => $from,
                'month'=> $m
            ];
        }

        // January to March of to year
        for ($m = 1; $m <= 3; $m++) {
            $months[] = [
                'id'   => $m,
                'name' => date('F Y', strtotime("$to-$m-01")),
                'year' => $to,
                'month'=> $m
            ];
        }

        return $months;
    }

    public static function render_calendar() {
        $sessions = self::get_sessions();
        $current_session = $sessions['current'];
        $months = self::get_months($current_session);

        // Default: current month in Indian academic year
        $today = date('Y-m-d');
        $default_year = $current_session;
        $default_month = (date('n') >= 4) ? date('n') : date('n'); // Jan-Mar still belongs to previous session

        ob_start();
        ?>
        <div class="academic-calendar-wrapper">
            <!-- Session Switcher -->
            <div class="session-switcher">
                <button class="session-btn" data-session="<?= $sessions['previous'] ?>"><?= $sessions['previous'] ?></button>
                <button class="session-btn active" data-session="<?= $current_session ?>"><?= $current_session ?></button>
                <button class="session-btn" data-session="<?= $sessions['next'] ?>"><?= $sessions['next'] ?></button>
            </div>

            <!-- Month Tabs -->
            <div class="month-tabs" id="monthTabs">
                <?php foreach ($months as $m): ?>
                    <button class="month-tab <?= ($m['year'].'-'.str_pad($m['month'],2,'0',STR_PAD_LEFT)) === date('Y-m', strtotime($today)) ? 'active' : '' ?>"
                            data-year="<?= $m['year'] ?>" data-month="<?= $m['month'] ?>">
                        <?= substr($m['name'], 0, 3) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid" id="calendarGrid">
                <div class="loading">Loading events...</div>
            </div>

            <!-- Event Popup Modal -->
            <div id="eventModal" class="modal">
                <div class="modal-content">
                    <span class="close">?</span>
                    <h3 id="modalDate"></h3>
                    <div id="modalEvents"></div>
                </div>
            </div>
            
            <div class="events-section">
                <div class="events-header">
                    <h3>Events</h3>
                    
                </div>
                <div id="eventsList" class="events-list">
                    <!-- Events will appear here -->
                </div>
                <div align="right" style="margin-top: 20px;">
                    <button id="showAllEventsBtn" class="btn-all-events">Show All Events</button>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    // AJAX Handler - fully WordPress-compliant
    public static function ajax_get_month_events() {
        // Read input from $_POST or raw JSON (e.g. from curl or fetch with application/json)
        $raw_input  = file_get_contents('php://input');
        $json_input = json_decode($raw_input, true) ?: [];

        // Check nonce if present, but allow requests if nonce expired due to page caching
        $nonce = $_POST['nonce'] ?? $json_input['nonce'] ?? '';
        if (!empty($nonce)) {
            check_ajax_referer('ac_nonce', 'nonce', false);
        }

        $fromyear = sanitize_text_field($_POST['fromyear'] ?? $json_input['fromyear'] ?? '');
        $toyear   = sanitize_text_field($_POST['toyear'] ?? $json_input['toyear'] ?? '');
        $monthid  = sanitize_text_field($_POST['monthid'] ?? $json_input['monthid'] ?? '');

        if (empty($fromyear) || empty($toyear)) {
            $sessions = self::get_sessions();
            list($fromyear, $toyear) = explode('-', $sessions['current']);
        }

        // Ensure monthid is valid 2-digit format (API crashes with HTTP 500 if monthid is empty)
        if (empty($monthid)) {
            $monthid = date('m');
        } else {
            $monthid = str_pad(ltrim($monthid, '0'), 2, '0', STR_PAD_LEFT);
        }

        $api_url = "https://websiteapi.sfsguwahati.ac.in/api/home/GetWebsiteCalendar";

        $payload = [
            "sl"       => "",
            "sid"      => 0,
            "regno"    => "",
            "fromyear" => $fromyear,
            "toyear"   => $toyear,
            "monthid"  => $monthid
        ];

        // 1. Primary method: WordPress HTTP API (wp_remote_post)
        $wp_response = wp_remote_post($api_url, [
            'headers'    => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-API-KEY'    => 'G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
                'Referer'      => 'https://sfsguwahati.ac.in',
            ],
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'body'       => wp_json_encode($payload),
            'timeout'    => 30,
            'sslverify'  => false,
        ]);

        $body      = '';
        $http_code = 0;

        if (!is_wp_error($wp_response)) {
            $http_code = wp_remote_retrieve_response_code($wp_response);
            $body      = wp_remote_retrieve_body($wp_response);
        }

        // 2. Secondary fallback: direct cURL if WordPress HTTP transport was blocked or empty
        if ((empty($body) || $http_code !== 200) && function_exists('curl_init')) {
            $ch = curl_init($api_url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => [
                    'Accept: application/json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Referer: https://sfsguwahati.ac.in',
                    'X-API-KEY: G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
                    'Content-Type: application/json'
                ],
                CURLOPT_TIMEOUT        => 30
            ]);
            $curl_body = curl_exec($ch);
            if (!curl_errno($ch)) {
                $body      = $curl_body;
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }
            curl_close($ch);
        }

//         $data = json_decode($body, true);

//         if (!empty($data['statusCode']) && $data['statusCode'] === 'Success') {
//             wp_send_json_success($data['data']);
//         } else {
//             $err = 'API Error (HTTP ' . $http_code . ')';
//             if (!empty($data['message'])) {
//                 $err .= ': ' . $data['message'];
//             } elseif (!empty($body)) {
//                 $err .= ': ' . substr(strip_tags($body), 0, 100);
//             }
//             wp_send_json_error($err);
//         }
    
    	$data = json_decode($body, true);

        // Normalize status code, message, and data (handles both camelCase and PascalCase)
        $statusCode = $data['statusCode'] ?? $data['StatusCode'] ?? $data['status'] ?? null;
        $message    = $data['message'] ?? $data['Message'] ?? '';
        $eventsData = $data['data'] ?? $data['Data'] ?? $data;

        $is_success = ($http_code === 200) && (
            $statusCode == 200 || 
            strcasecmp((string)$statusCode, 'success') === 0 || 
            strcasecmp((string)$message, 'success') === 0
        );

        if ($is_success && $eventsData !== null) {
            wp_send_json_success($eventsData);
        } else {
            $err = 'API Error (HTTP ' . $http_code . ')';
            if (!empty($message)) {
                $err .= ': ' . $message;
            } elseif (!empty($body)) {
                $err .= ': ' . substr(strip_tags($body), 0, 100);
            }
            wp_send_json_error($err);
        }
    }

}

School_Academic_Calendar::init();
}

