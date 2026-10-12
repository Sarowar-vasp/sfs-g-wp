<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

add_filter( 'document_title_separator', function( $sep ) {
    return '|';
});

add_action('template_redirect', function() {
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    // Only exact 'admission' (case-insensitive)
    if (strcasecmp($path, 'admission') == 0) {
        $query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        wp_redirect("https://admission.sfsguwahati.ac.in/admission$query");
        exit;
    }

	if (strcasecmp($path, 'sfs-mun-apply') == 0) {

        $query = !empty($_SERVER['QUERY_STRING'])
            ? '?' . $_SERVER['QUERY_STRING']
            : '';

        wp_redirect("https://old.sfsguwahati.ac.in/sfs-mun-apply.php{$query}", 301);
        exit;
    }
});

add_action('admin_menu', 'restrict_sfs_blog_admin_menu', 999);

function restrict_sfs_blog_admin_menu() {

    $current_user = wp_get_current_user();

    if ($current_user->user_login !== 'sfs_blog') {
        return;
    }

    global $menu;

    $allowed_menus = array(
        'edit.php',          // Posts
    	'post-new.php',      // Add Posts
        'upload.php',        // Media
        'media-new.php',     // media-new
        'index.php',         // Dashboard
        'profile.php'        // Profile
    );

    foreach ($menu as $key => $item) {
        if (!in_array($item[2], $allowed_menus)) {
            remove_menu_page($item[2]);
        }
    }
}


// TEMPORARY FIX: Clear the old, stale hook
wp_clear_scheduled_hook('sfs_importer_cron_hook');
wp_clear_scheduled_hook('sfs_blog_import_cron');
defined( 'ABSPATH' ) || exit;


add_filter('upload_mimes', function($mimes) {
    $mimes['json'] = 'application/json';
    $mimes['svg']  = 'image/svg+xml';
    $mimes['jpg']  = 'image/jpeg';
    $mimes['jpeg'] = 'image/jpeg';
    $mimes['png']  = 'image/png';
    return $mimes;
});



/**
 * Enqueue child scripts
 */
if ( ! function_exists( 'unicamp_child_enqueue_scripts' ) ) {
	function unicamp_child_enqueue_scripts() {
		wp_enqueue_style( 'unicamp-child-style', get_stylesheet_directory_uri() . '/style.css' );
	}
}

add_action('wp_enqueue_scripts', function () {

    if (is_front_page()) {

        wp_enqueue_script(
            'homepage-events-js',
            get_stylesheet_directory_uri() . '/assets/js/homepage-events.js',
            ['jquery'],
            '1.0',
            true
        );

        wp_localize_script('homepage-events-js', 'homeEvents', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }
});


add_action('wp_head', function() {
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-11455612705" type="256bf73b849261324ae9b06b-text/javascript"></script>
<script type="256bf73b849261324ae9b06b-text/javascript">
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-11455612705');
</script>
<?php
});


/* =========================================================
   HOMEPAGE UPCOMING EVENTS SHORTCODE
========================================================= */

add_shortcode('homepage_upcoming_events', 'render_homepage_events');

function render_homepage_events() {
    ob_start();
    ?>
    <div class="home-upcoming-events" style="padding: 20px;">

        <div class="home-events-header">
            <h4>Calendar</h4>
            <a href="/school-calendar" class="view-all">View all →</a>
        </div>

        <div id="homepageEventsList">
            <p>Loading events...</p>
        </div>
        <div class="ue-footer">
            <a href="/school-calendar"><div class="ue-chevron"></div></a>
        </div>

    </div>
    <?php
    return ob_get_clean();
}


/* =========================================================
   AJAX: GET UPCOMING EVENTS
========================================================= */

add_action('wp_ajax_get_homepage_events', 'get_homepage_events');
add_action('wp_ajax_nopriv_get_homepage_events', 'get_homepage_events');

add_action('wp_ajax_get_homepage_events', 'get_homepage_events');
add_action('wp_ajax_nopriv_get_homepage_events', 'get_homepage_events');

function get_homepage_events() {

    $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

    $year  = (int) $now->format('Y');
    $month = (int) $now->format('n');

    // Academic session logic (April–March)
    if ($month >= 4) {
        $fromyear = $year;
        $toyear   = $year + 1;
    } else {
        $fromyear = $year - 1;
        $toyear   = $year;
    }

    // Current + next month
    $months = [$month];

    if ($month == 12) {
        $months[] = 1; // January
    } else {
        $months[] = $month + 1;
    }

    $api_url = 'https://api.sfsguwahati.in/api/GetWebsiteCalendar';
    $all_events = [];

    foreach ($months as $m) {

        $response = wp_remote_post($api_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                "sl"       => "",
                "sid"      => 0,
                "regno"    => "",
                "fromyear" => (string) $fromyear,
                "toyear"   => (string) $toyear,
                "monthid"  => str_pad($m, 2, '0', STR_PAD_LEFT)
            ])
        ]);

        if (is_wp_error($response)) continue;

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($data['data'])) {
            $all_events = array_merge($all_events, $data['data']);
        }
    }

    if (empty($all_events)) {
        wp_send_json_error('No events found');
    }

    wp_send_json_success($all_events);
}

/* -------------------------------
   AJAX: Load Academic Year
-------------------------------- */

add_action('wp_ajax_load_years', 'sfs_load_years');
add_action('wp_ajax_nopriv_load_years', 'sfs_load_years');

function sfs_load_years() {

    $url = 'https://websiteapi.sfsguwahati.ac.in/api/home/activesession';
    $token = "G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf";

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
            . '(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Referer: https://sfsguwahati.ac.in',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
            'X-API-KEY: ' . $token
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        wp_send_json_error('API Error: ' . curl_error($ch));
    }

    curl_close($ch);

    wp_send_json(json_decode($response, true));
}



/* -------------------------------
   AJAX: Load Classes By Year
-------------------------------- */
add_action('wp_ajax_load_classes', 'sfs_load_classes');
add_action('wp_ajax_nopriv_load_classes', 'sfs_load_classes');

function sfs_load_classes() {

    $fromyear = sanitize_text_field($_POST['fromyear']);
    $sessyear = sanitize_text_field($_POST['sessyear']);

    $url = "https://websiteapi.sfsguwahati.ac.in/api/home/classbyyear?fromyear=" . urlencode($fromyear) . "&sessyear=" . urlencode($sessyear);

    $token = "G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf";

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
            . '(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Referer: https://sfsguwahati.ac.in',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
            'X-API-KEY: ' . $token
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        wp_send_json_error('API Error: ' . curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    wp_send_json($data);
}


function sfs_events_cards_shortcode() {
    $default_term = 'information';

    $args = [
        'post_type'      => 'tp_event',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'tp_event_category',
                'field'    => 'slug',
                'terms'    => $default_term,
            ],
        ],
    ];

    $events = get_posts($args);
    if (!$events) {
        return "<p>No events found.</p>";
    }

    // Colors for non-image cards
    $colors = [
        "#10386b",
        "#b11226",
        "#a41034",
        "#c62828",
        "#1e3c72",
        "#a41034",
        "#223f8f",
        "#10386b"
    ];

    // Which card indexes need background images? (0-based index)
    $image_cards = [1, 3, 4, 6]; // card 2, 4, 5, 7

    ob_start();
    ?>

    <style>
        .sfs-card {
            padding: 20px;
            color: #fff;
            min-height: 320px;
            margin-bottom: 20px;
            background-size: cover !important;
            background-position: center !important;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .sfs-card::before {
            content: "";
            position: absolute;
            inset: 0;
            /*background: rgba(0,0,0,0.45);*/
        }
        
        .sfs-card-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            flex-grow: 1;     /* ⬅ important */
            height: 100%;     /* ⬅ important */
        }
        
        .sfs-card-title {
            margin-bottom: 8px;
            color: #fff;
            font-weight: bold;
        }
        
        .sfs-card-desc {
            margin-top: 25px;
            margin-bottom: 10px;
            color: #fff;
        }
        
        .sfs-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-top: auto;   /* ⬅ FORCE FOOTER TO BOTTOM */
        }
        
        .event-date {
            /* Style for the date, if needed */
            color: #fff;
        }
        
        .download-icon {
            text-decoration: none; /* Remove default link styles */
            color: #fff; /* Set icon color */
            font-size: 16px; /* Adjust the icon size */
        }
        
        .download-icon:hover {
            color: #007bff; /* Change color on hover, if you want */
        }

        .announcement-btn {
            /*color: #ae162e;*/
        }
    </style>

    <div class="container">
        <div class="row">

            <?php
            $i = 0;
            foreach ($events as $event):
                // featured image
                $image = get_the_post_thumbnail_url($event->ID, 'medium');
                
                // card bg — image OR color
                
                $desc = !empty($event->post_excerpt)
                        ? $event->post_excerpt
                        : wp_trim_words($event->post_content, 20);
                        
                $tp_event_description = get_post_meta($event->ID, 'tp_event_description', true);

                        
                if (in_array($i, $image_cards) && $image) {
                    // Array of image URLs
                    $image_urls = [
                        '/wp-content/uploads/2025/12/image-14.webp',
                        '/wp-content/uploads/2025/12/image-15.webp',
                        '/wp-content/uploads/2025/12/image-16.webp',
                        '/wp-content/uploads/2025/12/image-17.webp',
                    ];
                    
                    // Randomly select an image from the array
                    $random_image = $image_urls[array_rand($image_urls)];
                    
                    // Generate the background style
           
                    if($i==1){ $bg_style = 'background-image: url(' . esc_url($image_urls[0]) . ');'; }
                    elseif($i==3){ $bg_style = 'background-image: url(' . esc_url($image_urls[1]) . ');';}
                    elseif($i==4){$bg_style = 'background-image: url(' . esc_url($image_urls[2]) . ');';}
                    elseif($i==6){$bg_style = 'background-image: url(' . esc_url($image_urls[3]) . ');';}
                           
                   
                    
                ?>
                
                    <div class="col-md-3 col-sm-6">
                       <div class="sfs-card" style="<?= $bg_style; ?>">
                          <div class="sfs-card-content">
                             <div class="sfs-card-title"><?= esc_html($event->post_title); ?></div>
                             <div class="sfs-card-desc mt-5"><?= $tp_event_description; ?></div>
                             <div class="sfs-card-footer mt-5">
                                <span class="event-date"><?= esc_html(get_the_date("F j, Y", $event->ID)); ?></span>
                                <a href="<?= esc_html($desc); ?>" class="download-icon">
                                <i class="fa fa-paperclip"></i>
                                </a>
                             </div>
                          </div>
                       </div>
                    </div>

                
                <?php
                } else {
                    $bg_style = 'background-color: ' . esc_attr($colors[$i % count($colors)]) . ';';
                ?>
                    <div class="col-md-3 col-sm-6">
                       <div class="sfs-card" style="<?= $bg_style; ?>">
                          <div class="sfs-card-content">
                             <div class="sfs-card-title"><?= esc_html($event->post_title); ?></div>
                             <div class="sfs-card-desc mt-5"><?= $tp_event_description; ?></div>
                             <div class="sfs-card-footer mt-5">
                                <span class="event-date"><?= esc_html(get_the_date("F j, Y", $event->ID)); ?></span>
                                <a href="<?= esc_html($desc); ?>" class="download-icon">
                                <i class="fa fa-paperclip"></i>
                                </a>
                             </div>
                          </div>
                       </div>
                    </div>
                <?php } ?>
                <?php
                $i++;
            endforeach;
            ?>

        </div>

        <div class="row" align="center">
            <div class="col-md-12">
                <a class="btn btn-default announcement-btn" href="/events/">
                    View all <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode("sfs_events_cards", "sfs_events_cards_shortcode");


function sfs_allow_calendar_query_vars( $vars ) {
    $vars[] = 'cy';
    $vars[] = 'cm';
    $vars[] = 'cd';
    $vars[] = 'showall';
    return $vars;
}
add_filter( 'query_vars', 'sfs_allow_calendar_query_vars' );


function sfs_disable_unicamp_404_on_query_vars( $request ) {
    if ( isset($_GET['cy']) || isset($_GET['cm']) || isset($_GET['cd']) ) {
        $request['error'] = false;
    }
    return $request;
}
add_filter( 'request', 'sfs_disable_unicamp_404_on_query_vars' );


function sfs_admission_form_shortcode() {
        ob_start();
        ?>

        <div class="row" id="sfsAdmissionForm">
               <div class="col-md-12"  align="center">

               <div class="form-group">
                   <label class="col-sm-4 col-form-label" style="text-align: left;"><b>Academic Year</b></label>
                   <div class="col-sm-8">
                       <select class="form-control" id="admissionYear">
                           <option value="0">Loading...</option>
                       </select>
                   </div>
               </div>

                <div class="form-group">
                    <label class="col-sm-4 col-form-label"  style="text-align: left;"><b>Select Class</b></label>
                    <div class="col-sm-8">
                        <select class="form-control" id="admissionclass">
                            <option value="0_0">Select Class</option>
                        </select>
                    </div>
                </div>
                
                </div>

        </div>

        <div id="classStatusMsg" style="margin: 18px;" class="col-lg-12 col-md-12 col-sm-12" align="center"></div>

        <style>
            .congo { color:#a40; font-size:20px; font-weight:bold; text-align:center; }
            .instr { font-weight:bolder; color:seagreen; text-align:center; }

            .btn { border:1px solid transparent !important; border-radius:.25rem !important; font-size:.875rem !important; }
            .btn-danger { background:#dc3545 !important; color:#fff !important; }
            .btn-info { background:#17a2b8 !important; color:#fff !important; }
            select { color:unset !important; }
            /* BACKDROP */
            .custom-modal {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.65);
                z-index: 99999;
                overflow-y: auto;
                padding: 40px 0;
            }

            /* WHEN VISIBLE */
            .custom-modal.show {
                display: block;
            }

            /* PROPER CENTERED BOX */
            .custom-modal .modal-dialog {
                width: 90%;
                max-width: 850px;
                margin: auto;
            }

            /* CONTENT BOX */
            .custom-modal .modal-content {
                background: white;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 0 20px rgba(0,0,0,0.3);
            }

            /* HEADER */
            .custom-modal .modal-header {
                padding: 12px 20px;
                background: #a41034;
                color: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            /* CLOSE BUTTON */
            .custom-modal .close-btn {
                background: none;
                border: none;
                font-size: 28px;
                color: white;
                cursor: pointer;
                line-height: 1;
            }

            /* BODY */
            .custom-modal .modal-body {
                padding: 20px 25px;
            }

            /* FOOTER */
            .custom-modal .modal-footer {
                padding: 15px 20px;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                border-top: 1px solid #ddd;
            }

            /* INPUT STYLING FIX */
            .custom-modal .form-control {
                width: 100%;
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 8px;
            }

            /* GAP BETWEEN ROWS */
            .modal-row {
                display: flex;
                gap: 15px;
                margin-top: 12px;
            }

            /* Fix column width */
            .modal-col {
                flex: 1;
            }

            .procedure-rectangle {
              border: 1px solid #a41034;
              border-radius: 1em;
              width: 100%;
              background-color: #a41034;
            }

            .bold-part {
                font-weight: bold;
            }

            table, tr, td, th {
                border: 1px solid #ae152d;
            }
        </style>

        <script>
            jQuery(document).ready(function($){
                
                function openRegistrationModal(className, session) {
                    className = (className + "").trim();
                    $("#registrationModal").remove(); // remove old modal
                    let modalHTML = `
                        <div id="registrationModal" class="custom-modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                    
                                    <!-- HEADER -->
                                    <div class="modal-header">
                                        <h5 style="color: white;">${className} Registration</h5>
                                        <button class="close-btn" id="closeRegModal">&times;</button>
                                    </div>
                    
                                    <!-- BODY -->
                                    <div class="modal-body">
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Mobile No</label>
                                                <input type="text" id="txtmob" maxlength="10" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>Email Address</label>
                                                <input type="text" id="txtmail" class="form-control">
                                            </div>
                                        </div>
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Student's Name</label>
                                                <input type="text" id="txtstudentname" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>DOB</label>
                                                <input type="text" id="txtdob" class="form-control" readonly>
                                            </div>
                                            <div class="modal-col">
                                                <label>Class</label>
                                                <input type="text" id="ddladmclass" value="${className}" class="form-control" readonly>
                                            </div>
                                        </div>
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Admission Year</label>
                                                <input type="text" id="txtses" value="${session}" class="form-control" readonly>
                                            </div>
                                            <div class="modal-col">
                                                <label>Last Class Studied</label>
                                                <input type="text" id="txtlastclass" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>Current School Name</label>
                                                <input type="text" id="txtcurrentsch" class="form-control">
                                            </div>
                                        </div>
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Father's Name</label>
                                                <input type="text" id="txtfather" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>Mother's Name</label>
                                                <input type="text" id="txtmother" class="form-control">
                                            </div>
                                        </div>
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Any Remarks</label>
                                                <textarea id="txtregrem" rows="2" class="form-control"></textarea>
                                            </div>
                                        </div>
                    
                                        <div class="modal-row">
                                            <div class="modal-col">
                                                <label>Marksheet 1 *</label>
                                                <input type="file" id="file1" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>Marksheet 2 *</label>
                                                <input type="file" id="file2" class="form-control">
                                            </div>
                                            <div class="modal-col">
                                                <label>Marksheet 3</label>
                                                <input type="file" id="file3" class="form-control">
                                            </div>
                                        </div>
                    
                                    </div>
                    
                                    <!-- FOOTER -->
                                    <div class="modal-footer">
                                        <button class="btn btn-success">Submit</button>
                                        <button class="btn btn-secondary" id="closeRegModal2">Close</button>
                                    </div>
                    
                                </div>
                            </div>
                        </div>
                        `;
                    
                        $("body").append(modalHTML);
                    
                        document.getElementById("registrationModal").classList.add("show");
                    
                        document.getElementById("closeRegModal").onclick = closeRegistrationModal;
                        document.getElementById("closeRegModal2").onclick = closeRegistrationModal;
                }
            
                function closeRegistrationModal() {
                    document.getElementById("registrationModal").classList.remove("show");
                }

                
                function closeRegistrationModal() {
                    document.getElementById("registrationModal").classList.remove("show");
                }


                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: "load_years"
                }, function(res){
                    let yearSelect = $("#admissionYear");
                    yearSelect.html('<option value="0">Select Year</option>');
                    $.each(res.d, function(i, item){
                        yearSelect.append(`<option value="${item._name}" data-sess="${item._id}">${item._name}</option>`);
                    });
                });

                $("#admissionYear").on("change", function(){
                    let selectedYear = $(this).val();
                    let sessyear = $(this).find(':selected').data("sess");

                    if(selectedYear == "0") {
                        $("#admissionclass").html('<option value="0_0">Select Class</option>');
                        return;
                    }

                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: "load_classes",
                        fromyear: selectedYear,
                        sessyear: sessyear
                    }, function(res){
                        let classSelect = $("#admissionclass");
                        classSelect.html('<option value="0_0">Select Class</option>');
                        $.each(res.d, function(i, item){
                            classSelect.append(
                                `<option value="${item._classid}_${item._clsDisp}" data-open="${item._openstatus}">
                                    ${item._clsDisp}
                                </option>`
                            );
                        });
                    });
                });

                $("#admissionclass").on("change", function(){

                    let openStatus = $(this).find(":selected").data("open");
                    let className = $("#admissionclass option:selected").text().trim();
                    let session = $("#admissionYear").val();

                    let box = $("#classStatusMsg");
                    
                    if(openStatus == "1") {
                        switch (className) {
                            case "NURSERY":
                                box.html(`<span style="color:green;">✔ Nursery Admission Open</span>`);
                                break;
                    
                            case "LKG":
                                box.html(`<span style="color:green;">✔ LKG Admission Open</span>`);
                                break;
                    
                            case "UKG":
                                box.html(`<span style="color:green;">✔ UKG Admission Open</span>`);
                                break;
                    
                            case "1":
                                box.html(`<span style="color:green;">✔ Class 1 Admission Open</span>`);
                                break;
                    
                            case "9":
                                box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                       <div class="section-show">
                                          <div align="center">
                                          <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                          </div>
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment shall be conducted for the student. Click here to view the syllabus <a href="/admission-syllabus/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-syllabus/</a></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class IX and IX Foundation, students must have appeared the Class VIII qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class VIII &amp; Term I Examination Report Card of Class IX from the previous school. The report card of the Final Examination of Class IX should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            case "10":
                                box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                                 <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                                 <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                                 <b>
                                                    <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                    <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment shall be conducted for the student. Click here to view the syllabus <a href="/admission-syllabus/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-syllabus/</a></p>
                                                    <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                    </p>
                                                    <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li> For Class X, students must have appeared the Class IX qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                       <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                    <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                    <ol class="ml-3">
                                                       <li> Copy of Birth Certificate.</li>
                                                       <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                       <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                       <li> Copy of caste certificate if other than General Caste.</li>
                                                       <li> PEN number from the previous school.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                       verification.
                                                    </p>
                                                    <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                       you.
                                                    </p>
                                                 </b>
                                              </div>
                                              <b>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </b>
                                           </div>
                                           <b>
                                           </b>
                                        </div>
                                        `);
                                break;
                    
                            // CLASS 11 STREAMS
                            case "11-COMMERCE":
                                box.html(`<div class="class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;"">
                                       <div class="section-show">
                                          <div align="center">
                                             <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR ${className} SESSION ${session}</u></h2>
                                          </div>
                                    
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.
                                                </p>
                                                <table class="table table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <th>SL NO.</th>
                                                         <th>BASELINE ASSESSMENT</th>
                                                         <th>PERSONAL INTERACTION</th>
                                                      </tr>
                                                      <tr>
                                                         <td>1</td>
                                                         <td>SFS Students</td>
                                                         <td>Not Applicable</td>
                                                      </tr>
                                                      <tr>
                                                         <td>2</td>
                                                         <td>Non SFS Students</td>
                                                         <td>Applicable</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class XI, students must have appeared the Class X qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">BASELINE ASSESSMENT SYLLABUS:</p>
                                                <table class="table table-bordered table-sm">
                                                   <thead>
                                                      <tr>
                                                         <th style="text-align:center;">SL NO.</th>
                                                         <th style="text-align:center;">STREAM</th>
                                                         <th style="text-align:center;">ENGLISH</th>
                                                         <th style="text-align:center;">BASIC MATHS</th>
                                                         <th style="text-align:center;">STANDARD MATHS</th>
                                                         <th style="text-align:center;">SCIENCE</th>
                                                         <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                         <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                         <th style="text-align:center;">TOTAL MARKS</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td style="text-align:center;">1</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - NEET</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">2</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - JEE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">3</td>
                                                         <td style="text-align:center;">SCIENCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">4</td>
                                                         <td style="text-align:center;">COMMERCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">5</td>
                                                         <td style="text-align:center;">HUMANITIES</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">50</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            case "11-HUMANITIES":
                                box.html(`<div class="class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;"">
                                       <div class="section-show">
                                          <div align="center">
                                             <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR ${className} SESSION ${session}</u></h2>
                                          </div>
                                    
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.
                                                </p>
                                                <table class="table table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <th>SL NO.</th>
                                                         <th>BASELINE ASSESSMENT</th>
                                                         <th>PERSONAL INTERACTION</th>
                                                      </tr>
                                                      <tr>
                                                         <td>1</td>
                                                         <td>SFS Students</td>
                                                         <td>Not Applicable</td>
                                                      </tr>
                                                      <tr>
                                                         <td>2</td>
                                                         <td>Non SFS Students</td>
                                                         <td>Applicable</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class XI, students must have appeared the Class X qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">BASELINE ASSESSMENT SYLLABUS:</p>
                                                <table class="table table-bordered table-sm">
                                                   <thead>
                                                      <tr>
                                                         <th style="text-align:center;">SL NO.</th>
                                                         <th style="text-align:center;">STREAM</th>
                                                         <th style="text-align:center;">ENGLISH</th>
                                                         <th style="text-align:center;">BASIC MATHS</th>
                                                         <th style="text-align:center;">STANDARD MATHS</th>
                                                         <th style="text-align:center;">SCIENCE</th>
                                                         <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                         <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                         <th style="text-align:center;">TOTAL MARKS</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td style="text-align:center;">1</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - NEET</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">2</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - JEE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">3</td>
                                                         <td style="text-align:center;">SCIENCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">4</td>
                                                         <td style="text-align:center;">COMMERCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">5</td>
                                                         <td style="text-align:center;">HUMANITIES</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">50</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            case "11-IC JEE":
                                box.html(`<div class="class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;"">
                                       <div class="section-show">
                                          <div align="center">
                                             <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR ${className} SESSION ${session}</u></h2>
                                          </div>
                                    
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.
                                                </p>
                                                <table class="table table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <th>SL NO.</th>
                                                         <th>BASELINE ASSESSMENT</th>
                                                         <th>PERSONAL INTERACTION</th>
                                                      </tr>
                                                      <tr>
                                                         <td>1</td>
                                                         <td>SFS Students</td>
                                                         <td>Not Applicable</td>
                                                      </tr>
                                                      <tr>
                                                         <td>2</td>
                                                         <td>Non SFS Students</td>
                                                         <td>Applicable</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class XI, students must have appeared the Class X qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">BASELINE ASSESSMENT SYLLABUS:</p>
                                                <table class="table table-bordered table-sm">
                                                   <thead>
                                                      <tr>
                                                         <th style="text-align:center;">SL NO.</th>
                                                         <th style="text-align:center;">STREAM</th>
                                                         <th style="text-align:center;">ENGLISH</th>
                                                         <th style="text-align:center;">BASIC MATHS</th>
                                                         <th style="text-align:center;">STANDARD MATHS</th>
                                                         <th style="text-align:center;">SCIENCE</th>
                                                         <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                         <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                         <th style="text-align:center;">TOTAL MARKS</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td style="text-align:center;">1</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - NEET</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">2</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - JEE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">3</td>
                                                         <td style="text-align:center;">SCIENCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">4</td>
                                                         <td style="text-align:center;">COMMERCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">5</td>
                                                         <td style="text-align:center;">HUMANITIES</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">50</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            case "11-IC NEET":
                                box.html(`<div class="class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;"">
                                       <div class="section-show">
                                          <div align="center">
                                             <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR ${className} SESSION ${session}</u></h2>
                                          </div>
                                    
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.
                                                </p>
                                                <table class="table table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <th>SL NO.</th>
                                                         <th>BASELINE ASSESSMENT</th>
                                                         <th>PERSONAL INTERACTION</th>
                                                      </tr>
                                                      <tr>
                                                         <td>1</td>
                                                         <td>SFS Students</td>
                                                         <td>Not Applicable</td>
                                                      </tr>
                                                      <tr>
                                                         <td>2</td>
                                                         <td>Non SFS Students</td>
                                                         <td>Applicable</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class XI, students must have appeared the Class X qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">BASELINE ASSESSMENT SYLLABUS:</p>
                                                <table class="table table-bordered table-sm">
                                                   <thead>
                                                      <tr>
                                                         <th style="text-align:center;">SL NO.</th>
                                                         <th style="text-align:center;">STREAM</th>
                                                         <th style="text-align:center;">ENGLISH</th>
                                                         <th style="text-align:center;">BASIC MATHS</th>
                                                         <th style="text-align:center;">STANDARD MATHS</th>
                                                         <th style="text-align:center;">SCIENCE</th>
                                                         <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                         <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                         <th style="text-align:center;">TOTAL MARKS</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td style="text-align:center;">1</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - NEET</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">2</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - JEE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">3</td>
                                                         <td style="text-align:center;">SCIENCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">4</td>
                                                         <td style="text-align:center;">COMMERCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">5</td>
                                                         <td style="text-align:center;">HUMANITIES</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">50</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            case "11-SCIENCE":
                                box.html(`<div class="class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;"">
                                       <div class="section-show">
                                          <div align="center">
                                             <h2 class="dark-maroon text-center"><u>ADMISSION PROCEDURE FOR ${className} SESSION ${session}</u></h2>
                                          </div>
                                    
                                          <div class="procedure-detail">
                                             <p>St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                             <p>The online admission forms for the <span class="bold-part">Academic Session of ${session} for Class ${className}</span> is available below..</p>
                                             <p class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</p>
                                             <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                             <p><b>Step 1: Fill the online application form: Click on the link <a href="https://admission.sfsguwahati.ac.in/admission" style="color: #ae152d;"> https://admission.sfsguwahati.ac.in/admission</a></b></p>
                                             <b>
                                                <p><b>Step 2: Date &amp; time of baseline assessment and personal interaction.</b> The applicant shall receive a call &amp; message on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of Baseline assessment and Personal Interaction.</p>
                                                <p><b>Step 3: Baseline assessment &amp; Personal interaction. </b> To get a better insight into the student's application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.
                                                </p>
                                                <table class="table table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <th>SL NO.</th>
                                                         <th>BASELINE ASSESSMENT</th>
                                                         <th>PERSONAL INTERACTION</th>
                                                      </tr>
                                                      <tr>
                                                         <td>1</td>
                                                         <td>SFS Students</td>
                                                         <td>Not Applicable</td>
                                                      </tr>
                                                      <tr>
                                                         <td>2</td>
                                                         <td>Non SFS Students</td>
                                                         <td>Applicable</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p></p>
                                                <p><b>Step 4: Online declaration of admission result: </b>The selected student can confirm their admission by making the payment on the school website at <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a> . Simply enter the Form ID number of the Application Form to proceed.
                                                </p>
                                                <p><b>Step 5: Confirmation of Admission: </b>Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</p>
                                                <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                <ol class="ml-3">
                                                   <li> For Class XI, students must have appeared the Class X qualifying examination from a recognized school affiliated to CBSE / ICSE / Other board or recognized board.</li>
                                                   <li> Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                </ol>
                                                <p class="bold-part">BASELINE ASSESSMENT SYLLABUS:</p>
                                                <table class="table table-bordered table-sm">
                                                   <thead>
                                                      <tr>
                                                         <th style="text-align:center;">SL NO.</th>
                                                         <th style="text-align:center;">STREAM</th>
                                                         <th style="text-align:center;">ENGLISH</th>
                                                         <th style="text-align:center;">BASIC MATHS</th>
                                                         <th style="text-align:center;">STANDARD MATHS</th>
                                                         <th style="text-align:center;">SCIENCE</th>
                                                         <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                         <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                         <th style="text-align:center;">TOTAL MARKS</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td style="text-align:center;">1</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - NEET</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">2</td>
                                                         <td style="text-align:center;">INTEGRATED COACHING - JEE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">45</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">15</td>
                                                         <td style="text-align:center;">100</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">3</td>
                                                         <td style="text-align:center;">SCIENCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">4</td>
                                                         <td style="text-align:center;">COMMERCE</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">30</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="text-align:center;">5</td>
                                                         <td style="text-align:center;">HUMANITIES</td>
                                                         <td style="text-align:center;">20</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">50</td>
                                                         <td style="text-align:center;">-</td>
                                                         <td style="text-align:center;">70</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                                <p class="bold-part">DOCUMENTS REQUIRED:</p>
                                                <p class="bold-part">(To be submitted at the time of Documentation after Admission)</p>
                                                <ol class="ml-3">
                                                   <li> Copy of Birth Certificate.</li>
                                                   <li> Copy of the Final Examination Report Card of Class IX &amp; Term I Examination Report Card of Class X from the previous school. The report card of the Final Examination of Class X should be submitted later.</li>
                                                   <li> Transfer / Bonafide Certificate from the previous school.</li>
                                                   <li> Copy of caste certificate if other than General Caste.</li>
                                                   <li> PEN number from the previous school.</li>
                                                </ol>
                                                <br>
                                                <p class="bold-part">NOTE: The school management shall call for the original documents for the purpose of
                                                   verification.
                                                </p>
                                                <p>School selection is one of the most important decisions for your child. We look forward to supporting
                                                   you.
                                                </p>
                                             </b>
                                          </div>
                                          <b>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                   <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="/wp-content/uploads/2025/11/Admission_Brochure.pdf" target="_blank">
                                                   <div class="download-document" style="color: white;">
                                                      Download the Brochure
                                                   </div>
                                                </a>
                                             </div>
                                             <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                   <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                </a>
                                             </div>
                                          </b>
                                       </div>
                                       <b>
                                       </b>
                                    </div>
                                    `);
                                break;
                    
                            // CLASS 12 STREAMS
                            case "12-COMMERCE":
                                box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                              <h2><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p class="bold-part1">St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p class="bold-part1">The online admission forms for the Academic Session of ${session} for Class ${className} shall be available from 1st December 2023.</p>
                                                 <p><span class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</span></p>
                                                 <br>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <ol class="ml-3">
                                                    <li> <b>Step 1: Fill the online application form:</b> Click on the link <a href="/take-admission-here/" style="color: #ae152d;">https://sfsguwahati.ac.in/take-admission-here/</a> </li>
                                                    <li> <b>Step 2: Date &amp; time of baseline assessment &amp; personal interaction:</b> The applicant shall receive a notification on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of parent-student interaction.</li>
                                                    <li> <b>Step 3:Baseline assessment &amp; Personal interaction:</b> To get a better insight into the students' application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.</li>
                                                    <li> <b>Step 4: Online declaration of admission result:</b> The admission selection list shall be uploaded in the school website <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a><a></a></li>
                                                    <a>
                                                    </a>
                                                    <li><a> <b>Step 5: Confirmation of Admission:</b> Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</a><a href="#" style="color: #ae152d;"></a><a></a></li>
                                                    <a>
                                                    </a>
                                                 </ol>
                                                 <a>
                                                    <br>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li>Admission is confirmed only after getting the permission by CBSE.</li>
                                                       <li>For Class XII, students must have appeared the Class XI qualifying examination from school affiliated to CBSE board.</li>
                                                       <li>Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part"> BASELINE ASSESSMENT SYLLABUS:</p>
                                                    <table class="table table-bordered table-sm">
                                                       <thead>
                                                          <tr>
                                                             <th style="text-align:center;">SL NO.</th>
                                                             <th style="text-align:center;">STREAM</th>
                                                             <th style="text-align:center;">ENGLISH</th>
                                                             <th style="text-align:center;">BASIC MATHS</th>
                                                             <th style="text-align:center;">STANDARD MATHS</th>
                                                             <th style="text-align:center;">SCIENCE</th>
                                                             <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                             <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                             <th style="text-align:center;">TOTAL MARKS</th>
                                                          </tr>
                                                       </thead>
                                                       <tbody>
                                                          <tr>
                                                             <td style="text-align:center;">1</td>
                                                             <td style="text-align:center;">INTEGRATED COACHING (NEET &amp; JEE)</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">45</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">15</td>
                                                             <td style="text-align:center;">100</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">3</td>
                                                             <td style="text-align:center;">SCIENCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">4</td>
                                                             <td style="text-align:center;">COMMERCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">5</td>
                                                             <td style="text-align:center;">HUMANITIES</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">50</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                       </tbody>
                                                    </table>
                                                    <div class="document-section" style="margin: 2rem 0;">
                                                       <h2>DOCUMENTS REQUIRED:</h2>
                                                       <p>(To be submitted at the time of Documentation after Admission)</p>
                                                       <ol class="ml-3">
                                                          <li>Copy of Birth Certificate.</li>
                                                          <li>Copy of the Final Examination Report card of Class X &amp; Term I Examination Report Card of Class XI from the previous school. The report card of the Final Exam of Class XI should be submitted later.</li>
                                                          <li>Transfer Certificate / Bonafide certificate from the previous school.</li>
                                                          <li>Copy of the caste certificate if other than General Caste.</li>
                                                          <li>Registration detail of Class XI.</li>
                                                          <li>Documents as per the CBSE norms shall be submitted.</li>
                                                       </ol>
                                                    </div>
                                                    <p class="bold-part"> NOTE: The school management may ask for the original certificates anytime if required.</p>
                                                 </a>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        `);
                                break;
                    
                            case "12-HUMANITIES":
                                 box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                              <h2><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p class="bold-part1">St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p class="bold-part1">The online admission forms for the Academic Session of ${session} for Class ${className} shall be available from 1st December 2023.</p>
                                                 <p><span class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</span></p>
                                                 <br>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <ol class="ml-3">
                                                    <li> <b>Step 1: Fill the online application form:</b> Click on the link <a href="/take-admission-here/" style="color: #ae152d;">https://sfsguwahati.ac.in/take-admission-here/</a> </li>
                                                    <li> <b>Step 2: Date &amp; time of baseline assessment &amp; personal interaction:</b> The applicant shall receive a notification on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of parent-student interaction.</li>
                                                    <li> <b>Step 3:Baseline assessment &amp; Personal interaction:</b> To get a better insight into the students' application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.</li>
                                                    <li> <b>Step 4: Online declaration of admission result:</b> The admission selection list shall be uploaded in the school website <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a><a></a></li>
                                                    <a>
                                                    </a>
                                                    <li><a> <b>Step 5: Confirmation of Admission:</b> Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</a><a href="#" style="color: #ae152d;"></a><a></a></li>
                                                    <a>
                                                    </a>
                                                 </ol>
                                                 <a>
                                                    <br>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li>Admission is confirmed only after getting the permission by CBSE.</li>
                                                       <li>For Class XII, students must have appeared the Class XI qualifying examination from school affiliated to CBSE board.</li>
                                                       <li>Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part"> BASELINE ASSESSMENT SYLLABUS:</p>
                                                    <table class="table table-bordered table-sm">
                                                       <thead>
                                                          <tr>
                                                             <th style="text-align:center;">SL NO.</th>
                                                             <th style="text-align:center;">STREAM</th>
                                                             <th style="text-align:center;">ENGLISH</th>
                                                             <th style="text-align:center;">BASIC MATHS</th>
                                                             <th style="text-align:center;">STANDARD MATHS</th>
                                                             <th style="text-align:center;">SCIENCE</th>
                                                             <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                             <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                             <th style="text-align:center;">TOTAL MARKS</th>
                                                          </tr>
                                                       </thead>
                                                       <tbody>
                                                          <tr>
                                                             <td style="text-align:center;">1</td>
                                                             <td style="text-align:center;">INTEGRATED COACHING (NEET &amp; JEE)</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">45</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">15</td>
                                                             <td style="text-align:center;">100</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">3</td>
                                                             <td style="text-align:center;">SCIENCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">4</td>
                                                             <td style="text-align:center;">COMMERCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">5</td>
                                                             <td style="text-align:center;">HUMANITIES</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">50</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                       </tbody>
                                                    </table>
                                                    <div class="document-section" style="margin: 2rem 0;">
                                                       <h2>DOCUMENTS REQUIRED:</h2>
                                                       <p>(To be submitted at the time of Documentation after Admission)</p>
                                                       <ol class="ml-3">
                                                          <li>Copy of Birth Certificate.</li>
                                                          <li>Copy of the Final Examination Report card of Class X &amp; Term I Examination Report Card of Class XI from the previous school. The report card of the Final Exam of Class XI should be submitted later.</li>
                                                          <li>Transfer Certificate / Bonafide certificate from the previous school.</li>
                                                          <li>Copy of the caste certificate if other than General Caste.</li>
                                                          <li>Registration detail of Class XI.</li>
                                                          <li>Documents as per the CBSE norms shall be submitted.</li>
                                                       </ol>
                                                    </div>
                                                    <p class="bold-part"> NOTE: The school management may ask for the original certificates anytime if required.</p>
                                                 </a>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        `);
                                break;
                    
                            case "12-IC JEE":
                                 box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                              <h2><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p class="bold-part1">St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p class="bold-part1">The online admission forms for the Academic Session of ${session} for Class ${className} shall be available from 1st December 2023.</p>
                                                 <p><span class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</span></p>
                                                 <br>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <ol class="ml-3">
                                                    <li> <b>Step 1: Fill the online application form:</b> Click on the link <a href="/take-admission-here/" style="color: #ae152d;">https://sfsguwahati.ac.in/take-admission-here/</a> </li>
                                                    <li> <b>Step 2: Date &amp; time of baseline assessment &amp; personal interaction:</b> The applicant shall receive a notification on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of parent-student interaction.</li>
                                                    <li> <b>Step 3:Baseline assessment &amp; Personal interaction:</b> To get a better insight into the students' application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.</li>
                                                    <li> <b>Step 4: Online declaration of admission result:</b> The admission selection list shall be uploaded in the school website <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a><a></a></li>
                                                    <a>
                                                    </a>
                                                    <li><a> <b>Step 5: Confirmation of Admission:</b> Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</a><a href="#" style="color: #ae152d;"></a><a></a></li>
                                                    <a>
                                                    </a>
                                                 </ol>
                                                 <a>
                                                    <br>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li>Admission is confirmed only after getting the permission by CBSE.</li>
                                                       <li>For Class XII, students must have appeared the Class XI qualifying examination from school affiliated to CBSE board.</li>
                                                       <li>Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part"> BASELINE ASSESSMENT SYLLABUS:</p>
                                                    <table class="table table-bordered table-sm">
                                                       <thead>
                                                          <tr>
                                                             <th style="text-align:center;">SL NO.</th>
                                                             <th style="text-align:center;">STREAM</th>
                                                             <th style="text-align:center;">ENGLISH</th>
                                                             <th style="text-align:center;">BASIC MATHS</th>
                                                             <th style="text-align:center;">STANDARD MATHS</th>
                                                             <th style="text-align:center;">SCIENCE</th>
                                                             <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                             <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                             <th style="text-align:center;">TOTAL MARKS</th>
                                                          </tr>
                                                       </thead>
                                                       <tbody>
                                                          <tr>
                                                             <td style="text-align:center;">1</td>
                                                             <td style="text-align:center;">INTEGRATED COACHING (NEET &amp; JEE)</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">45</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">15</td>
                                                             <td style="text-align:center;">100</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">3</td>
                                                             <td style="text-align:center;">SCIENCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">4</td>
                                                             <td style="text-align:center;">COMMERCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">5</td>
                                                             <td style="text-align:center;">HUMANITIES</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">50</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                       </tbody>
                                                    </table>
                                                    <div class="document-section" style="margin: 2rem 0;">
                                                       <h2>DOCUMENTS REQUIRED:</h2>
                                                       <p>(To be submitted at the time of Documentation after Admission)</p>
                                                       <ol class="ml-3">
                                                          <li>Copy of Birth Certificate.</li>
                                                          <li>Copy of the Final Examination Report card of Class X &amp; Term I Examination Report Card of Class XI from the previous school. The report card of the Final Exam of Class XI should be submitted later.</li>
                                                          <li>Transfer Certificate / Bonafide certificate from the previous school.</li>
                                                          <li>Copy of the caste certificate if other than General Caste.</li>
                                                          <li>Registration detail of Class XI.</li>
                                                          <li>Documents as per the CBSE norms shall be submitted.</li>
                                                       </ol>
                                                    </div>
                                                    <p class="bold-part"> NOTE: The school management may ask for the original certificates anytime if required.</p>
                                                 </a>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        `);
                                break;
                    
                            case "12-IC NEET":
                                 box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                              <h2><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p class="bold-part1">St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p class="bold-part1">The online admission forms for the Academic Session of ${session} for Class ${className} shall be available from 1st December 2023.</p>
                                                 <p><span class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</span></p>
                                                 <br>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <ol class="ml-3">
                                                    <li> <b>Step 1: Fill the online application form:</b> Click on the link <a href="/take-admission-here/" style="color: #ae152d;">https://sfsguwahati.ac.in/take-admission-here/</a> </li>
                                                    <li> <b>Step 2: Date &amp; time of baseline assessment &amp; personal interaction:</b> The applicant shall receive a notification on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of parent-student interaction.</li>
                                                    <li> <b>Step 3:Baseline assessment &amp; Personal interaction:</b> To get a better insight into the students' application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.</li>
                                                    <li> <b>Step 4: Online declaration of admission result:</b> The admission selection list shall be uploaded in the school website <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a><a></a></li>
                                                    <a>
                                                    </a>
                                                    <li><a> <b>Step 5: Confirmation of Admission:</b> Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</a><a href="#" style="color: #ae152d;"></a><a></a></li>
                                                    <a>
                                                    </a>
                                                 </ol>
                                                 <a>
                                                    <br>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li>Admission is confirmed only after getting the permission by CBSE.</li>
                                                       <li>For Class XII, students must have appeared the Class XI qualifying examination from school affiliated to CBSE board.</li>
                                                       <li>Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part"> BASELINE ASSESSMENT SYLLABUS:</p>
                                                    <table class="table table-bordered table-sm">
                                                       <thead>
                                                          <tr>
                                                             <th style="text-align:center;">SL NO.</th>
                                                             <th style="text-align:center;">STREAM</th>
                                                             <th style="text-align:center;">ENGLISH</th>
                                                             <th style="text-align:center;">BASIC MATHS</th>
                                                             <th style="text-align:center;">STANDARD MATHS</th>
                                                             <th style="text-align:center;">SCIENCE</th>
                                                             <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                             <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                             <th style="text-align:center;">TOTAL MARKS</th>
                                                          </tr>
                                                       </thead>
                                                       <tbody>
                                                          <tr>
                                                             <td style="text-align:center;">1</td>
                                                             <td style="text-align:center;">INTEGRATED COACHING (NEET &amp; JEE)</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">45</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">15</td>
                                                             <td style="text-align:center;">100</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">3</td>
                                                             <td style="text-align:center;">SCIENCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">4</td>
                                                             <td style="text-align:center;">COMMERCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">5</td>
                                                             <td style="text-align:center;">HUMANITIES</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">50</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                       </tbody>
                                                    </table>
                                                    <div class="document-section" style="margin: 2rem 0;">
                                                       <h2>DOCUMENTS REQUIRED:</h2>
                                                       <p>(To be submitted at the time of Documentation after Admission)</p>
                                                       <ol class="ml-3">
                                                          <li>Copy of Birth Certificate.</li>
                                                          <li>Copy of the Final Examination Report card of Class X &amp; Term I Examination Report Card of Class XI from the previous school. The report card of the Final Exam of Class XI should be submitted later.</li>
                                                          <li>Transfer Certificate / Bonafide certificate from the previous school.</li>
                                                          <li>Copy of the caste certificate if other than General Caste.</li>
                                                          <li>Registration detail of Class XI.</li>
                                                          <li>Documents as per the CBSE norms shall be submitted.</li>
                                                       </ol>
                                                    </div>
                                                    <p class="bold-part"> NOTE: The school management may ask for the original certificates anytime if required.</p>
                                                 </a>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        `);
                                break;
                    
                            case "12-SCIENCE":
                                 box.html(`<div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px; text-align: left;">
                                           <div class="section-show">
                                              <div align="center">
                                              <h2><u>ADMISSION PROCEDURE FOR CLASS ${className} SESSION ${session}</u></h2>
                                              </div>
                                              <div class="procedure-detail">
                                                 <p class="bold-part1">St. Francis De Sales School, Guwahati, aims to maintain a smooth admission process, which is convenient for all the candidates.</p>
                                                 <p class="bold-part1">The online admission forms for the Academic Session of ${session} for Class ${className} shall be available from 1st December 2023.</p>
                                                 <p><span class="bold-part">NOTE: Limited seats, based on CBSE guidelines.</span></p>
                                                 <br>
                                                 <p class="bold-part">THE FOLLOWING ARE THE STEPS TO BE FOLLOWED FOR THE ADMISSION PROCESS:</p>
                                                 <ol class="ml-3">
                                                    <li> <b>Step 1: Fill the online application form:</b> Click on the link <a href="/take-admission-here/" style="color: #ae152d;">https://sfsguwahati.ac.in/take-admission-here/</a> </li>
                                                    <li> <b>Step 2: Date &amp; time of baseline assessment &amp; personal interaction:</b> The applicant shall receive a notification on their primary contact number for the date and time of interaction. Applicant shall take a printout of the admission form and submit it on the date of parent-student interaction.</li>
                                                    <li> <b>Step 3:Baseline assessment &amp; Personal interaction:</b> To get a better insight into the students application of knowledge and assess their conceptual clarity, baseline assessment and personal interaction is conducted with the student.</li>
                                                    <li> <b>Step 4: Online declaration of admission result:</b> The admission selection list shall be uploaded in the school website <a href="/admission-results/" style="color: #ae152d;">https://sfsguwahati.ac.in/admission-results/</a><a></a></li>
                                                    <a>
                                                    </a>
                                                    <li><a> <b>Step 5: Confirmation of Admission:</b> Selected applicants shall complete the payment formalities within 5 working days to confirm the admission.</a><a href="#" style="color: #ae152d;"></a><a></a></li>
                                                    <a>
                                                    </a>
                                                 </ol>
                                                 <a>
                                                    <br>
                                                    <p class="bold-part">ELIGIBILITY CRITERIA:</p>
                                                    <ol class="ml-3">
                                                       <li>Admission is confirmed only after getting the permission by CBSE.</li>
                                                       <li>For Class XII, students must have appeared the Class XI qualifying examination from school affiliated to CBSE board.</li>
                                                       <li>Admission is considered provisional till result declaration. Submission of all valid documents are mandatory.</li>
                                                    </ol>
                                                    <br>
                                                    <p class="bold-part"> BASELINE ASSESSMENT SYLLABUS:</p>
                                                    <table class="table table-bordered table-sm">
                                                       <thead>
                                                          <tr>
                                                             <th style="text-align:center;">SL NO.</th>
                                                             <th style="text-align:center;">STREAM</th>
                                                             <th style="text-align:center;">ENGLISH</th>
                                                             <th style="text-align:center;">BASIC MATHS</th>
                                                             <th style="text-align:center;">STANDARD MATHS</th>
                                                             <th style="text-align:center;">SCIENCE</th>
                                                             <th style="text-align:center;">SOCIAL SCIENCE</th>
                                                             <th style="text-align:center;">LOGIC &amp; REASONING</th>
                                                             <th style="text-align:center;">TOTAL MARKS</th>
                                                          </tr>
                                                       </thead>
                                                       <tbody>
                                                          <tr>
                                                             <td style="text-align:center;">1</td>
                                                             <td style="text-align:center;">INTEGRATED COACHING (NEET &amp; JEE)</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">45</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">15</td>
                                                             <td style="text-align:center;">100</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">3</td>
                                                             <td style="text-align:center;">SCIENCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">4</td>
                                                             <td style="text-align:center;">COMMERCE</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">30</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                          <tr>
                                                             <td style="text-align:center;">5</td>
                                                             <td style="text-align:center;">HUMANITIES</td>
                                                             <td style="text-align:center;">20</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">50</td>
                                                             <td style="text-align:center;">-</td>
                                                             <td style="text-align:center;">70</td>
                                                          </tr>
                                                       </tbody>
                                                    </table>
                                                    <div class="document-section" style="margin: 2rem 0;">
                                                       <h2>DOCUMENTS REQUIRED:</h2>
                                                       <p>(To be submitted at the time of Documentation after Admission)</p>
                                                       <ol class="ml-3">
                                                          <li>Copy of Birth Certificate.</li>
                                                          <li>Copy of the Final Examination Report card of Class X &amp; Term I Examination Report Card of Class XI from the previous school. The report card of the Final Exam of Class XI should be submitted later.</li>
                                                          <li>Transfer Certificate / Bonafide certificate from the previous school.</li>
                                                          <li>Copy of the caste certificate if other than General Caste.</li>
                                                          <li>Registration detail of Class XI.</li>
                                                          <li>Documents as per the CBSE norms shall be submitted.</li>
                                                       </ol>
                                                    </div>
                                                    <p class="bold-part"> NOTE: The school management may ask for the original certificates anytime if required.</p>
                                                 </a>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="/wp-content/uploads/2025/11/Prospectus.pdf"  target="_blank">
                                                       <div class="download-document" style="color: white;"> Download the E-Prospectus </div>
                                                    </a>
                                                 </div>
                                                 <div class="procedure-rectangle mt-4" align="center" style="margin-top: 5px;">
                                                    <a href="https://admission.sfsguwahati.ac.in/admission?p=${session}&c=${className}" target="_blank" class="proceed-to-admission">
                                                       <div class="download-document" style="color: white;"> Click here to fill up the Online Application form </div>
                                                    </a>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                        `);
                                break;
                    
                            default:
                                box.html(`<span style="color:green;">Admission is Closed</span>`);
                        }
                    } 
                    else {
                        box.html(`
                        <div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px;">
                            <div class="sorry">
                                <img src="/wp-content/uploads/2025/11/sorry.png">
                            </div>
                            <div class="congo">Admission is closed</div>
                            <div class="instr">Do you want Register?</div>
                            <center>
                                <button class="btn btn-sm btn-danger mr-2 yes-btn">Yes</button>
                                <button class="btn btn-sm btn-info">No</button>
                            </center>
                        </div>`);
                    }
                });

                // YES CLICK → SHOW REGISTER HERE
                $(document).on("click", ".yes-btn", function(){

                    let className = $("#admissionclass option:selected").text();
                    let session = $("#admissionYear").val();

                    $("#classStatusMsg").html(`
                        <div class="col-md-12" style="border:1px solid #ae152d; border-radius:4px; padding:20px;">

                            <h2 class="dark-maroon text-center"><u>Admission for Session ${session}</u></h2>

                            <p class="text-justify">
                                Limited seats, based on CBSE guidelines.<br>
                                Sorry, no seats available for Session ${session}.<br>
                                Please register to get notifications in case seats become available.
                            </p>

                            <center>
                                <button class="btn btn-redir-reg btn-primary"
                                        data-class="${className}" 
                                        data-session="${session}">
                                    Register Here
                                </button>
                            </center>

                        </div>
                    `);
                });

                // REGISTER HERE → OPEN MODAL
                $(document).on("click", ".btn-redir-reg", function(){
                    let className = $(this).data("class");
                    let session = $(this).data("session");
                    openRegistrationModal(className, session);
                });

            });
        </script>

        <?php
        return ob_get_clean();
}
add_shortcode('admission_form', 'sfs_admission_form_shortcode');


function sfs_admission_detail_form() {

    // If form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $formid = sanitize_text_field($_POST['formid']);
        header("Location: https://old.sfsguwahati.ac.in/admission-process.php?vz=$formid");
        exit;
        $url = "https://old.sfsguwahati.ac.in/WebSrv/srvAdmissionOnline.asmx/Loadcheckdatabyformidyear";
         // <-- UPDATE

        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                "formid" => $formid
            ])
        ]);

        if (is_wp_error($response)) {
            return "<p>Error connecting to server. Please try again.</p>";
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($json['d'])) {
            return "<p>No data found for provided Form ID.</p>";
        }

        $d = $json['d'];

        // Display form + result
        ob_start();
        ?>

        <!-- FORM again -->
        <style>
            .result-form-wrapper {
                max-width: 500px;
                margin: 0 auto;
                text-align: center;
            }
            .result-label {
                font-weight: 600;
                padding-right: 10px;
                white-space: nowrap;
                font-size: 15px;
                margin-top: 8px;
            }
            .result-input {
                height: 50px;
                font-size: 16px;
                border: 2px solid #b51c30;
                border-radius: 3px;
            }
            .result-btn {
                height: 50px;
                border-radius: 3px;
                background: #b51c30;
                color: #fff;
                padding: 0 35px;
                border: none;
                font-weight: 600;
            }
            .result-btn:hover {
                background: #8a1223;
            }

            /* ======================
               MOBILE RESPONSIVE
               ====================== */
            @media (max-width: 768px) {

              /* FORM WRAPPER */
              .result-form-wrapper {
                max-width: 100%;
                padding: 10px;
              }

              /* STACK FORM ELEMENTS */
              .result-form-wrapper .row {
                flex-direction: column;
                align-items: stretch !important;
              }

              .result-label {
                text-align: left;
                margin-bottom: 5px;
                font-size: 14px;
              }

              .result-input {
                width: 100%;
                height: 45px;
                font-size: 14px;
              }

              .result-btn {
                width: 100%;
                margin-top: 10px;
                height: 45px;
              }

              /* RESULT TABLE */
              .admission-process table {
                width: 100% !important;
                display: block;
                overflow-x: auto;
              }

              .admission-process th,
              .admission-process td {
                white-space: nowrap;
                font-size: 14px;
              }
            }


        </style>
            <div class="result-form-wrapper">
                <form method="POST" action="">
                    <div class="row g-2 justify-content-center align-items-center" style="margin: 5px !important;">
                        <div class="col-md-12 result-label">
                            FORM ID
                        </div>
                        <div class="col-md-12 col-10">
                            <input type="text" name="formid" class="form-control result-input" required>
                        </div>
                        <div class="col-md-12">
                            <button class="result-btn" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
                <div class="admission-process" id="divfirst" style="display:block; margin: 10px;">
                    <table class="table table-bordered table-stripped" style="width: 85%;">
                        <tbody>
                            <tr>
                                <th>Form Id</th>
                                <td><?php echo isset($_POST['formid']) ?? $_POST['formid']; ?></td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td><?php echo isset($d['Name']) ?? $d['Name']; ?></td>
                            </tr>
                            <tr>
                                <th>Reg No.</th>
                                <td><?php echo isset($d['RegNo']) ?? $d['RegNo']; ?></td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?php echo isset($d['Gender']) ?? $d['Gender']; ?></td>
                            </tr>
                            <tr>
                                <th>Father Name</th>
                                <td><?php echo isset($d['FatherName']) ?? $d['FatherName']; ?></td>
                            </tr>
                            <tr>
                                <th>Mother Name</th>
                                <td><?php echo isset($d['MotherName']) ?? $d['MotherName']; ?></td>
                            </tr>
                            <tr>
                                <th>Phone No.</th>
                                <td><?php echo isset($d['Phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <td><?php echo isset($d['Class']) ?? $d['Class']; ?></td>
                            </tr>
                            <tr>
                                <th>Section/Stream</th>
                                <td><?php echo isset($d['Section']) ?? $d['Section']; ?></td>
                            </tr>
                            <tr>
                                <th>Year</th>
                                <td><?php echo isset($d['FromYear']) ?? $d['FromYear']; ?> - <?php echo isset($d['ToYear']) ?? $d['ToYear']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        return ob_get_clean();
    }

    // FIRST LOAD — Only show form
    ob_start();
    ?>
        <style>
            .result-form-wrapper {
                max-width: 500px;
                margin: 0 auto;
                text-align: center;
            }
            .result-label {
                font-weight: 600;
                padding-right: 10px;
                white-space: nowrap;
                font-size: 15px;
                margin-top: 8px;
            }
            .result-input {
                height: 50px;
                font-size: 16px;
                border: 2px solid #b51c30;
                border-radius: 3px;
            }
            .result-btn {
                height: 50px;
                border-radius: 3px;
                background: #b51c30;
                color: #fff;
                padding: 0 35px;
                border: none;
                font-weight: 600;
            }
            .result-btn:hover {
                background: #8a1223;
            }
        </style>
        <div class="result-form-wrapper">
            <form method="POST" action="">
                <div class="row g-2 justify-content-center align-items-center" style="margin:5px;">
        
                    <div class="col-auto result-label">
                        FORM ID
                    </div>
        
                    <div class="col-md-6 col-sm-12" style="padding-left:0px !important;">
                        <input type="text" name="formid" class="form-control result-input" required >
                    </div>
        
                    <div class="col-auto">
                        <button class="result-btn" type="submit">Submit</button>
                    </div>
        
                </div>
            </form>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode('admission_detail_form', 'sfs_admission_detail_form');


function sfs_transfer_certificate_form() {

    // Shared datepicker scripts (always load)
    $datepicker_js = '
    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr("#dob", {
            dateFormat: "d/m/Y",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            maxDate: "today"
        });
    });
    </script>
    ';

    // If form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $registration_no = sanitize_text_field($_POST['registration_no']);
        $dob = sanitize_text_field($_POST['dob']);

        $url = "https://old.sfsguwahati.ac.in/WebSrv/srvTransferCertificate.asmx/LoadTransferCertificate";
        
        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                "obj" => [
                    "str1" => $registration_no,
                    "str2" => $dob
                ]
            ])
        ]);

        if (is_wp_error($response)) {
            return "<p>Error fetching data. Please try again.</p>";
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($json['d'][0]['str1'])) {
            return "<p>No data found for the provided details.</p>";
        }

        $html = $json['d'][0]['str1'];

        preg_match_all('/<div class=\'panel.*?<\/div>\s*<\/div>/is', $html, $matches);

        if (empty($matches[0])) {
            $tc_error = "<p>No TC sections found.</p>";  // save error message
        } else {
            $tc_error = "";  // no error
        }

        // Show form again + result
        ob_start();
        echo $datepicker_js;
        ?>

        <form method="POST" action="">
            <div class="row" style="padding:45px;">
                <div class="col-md-6 mb-3">
                    <label for="registration_no" class="form-label">Registration No:</label>
                    <input type="text" id="registration_no" name="registration_no" class="form-control" required />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="dob" class="form-label">Date of Birth:</label>
                    <input type="text" id="dob" name="dob" class="form-control" required />
                </div>
            </div>

            <div class="row" style="padding:0 45px 45px;" align="center">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-danger btn-block">Submit</button>
                </div>
            </div>
        </form>
        
        <?php
        echo "<div align='center'>$tc_error</div>";
        // Output TC sections
        if (empty($tc_error)) {
            foreach ($matches[0] as $panel) {
                echo $panel;
            }
        }

        return ob_get_clean();
    }

    // Initial (first) form load — this ALSO needs datepicker script
    ob_start();
    echo $datepicker_js;
    ?>
    

    <form method="POST" action="">
        <div class="row" style="padding:45px;">
            <div class="col-md-6 mb-3">
                <label for="registration_no" class="form-label">Registration No:</label>
                <input type="text" id="registration_no" name="registration_no" class="form-control" required />
            </div>

            <div class="col-md-6 mb-3">
                <label for="dob" class="form-label">Date of Birth:</label>
                <input type="text" id="dob" name="dob" class="form-control" required />
            </div>
        </div>

        <div class="row" style="padding:0 45px 45px;" align="center">
            <div class="col-md-12">
                <button type="submit" class="btn btn-danger btn-block">Submit</button>
            </div>
        </div>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('transfer_certificate_form', 'sfs_transfer_certificate_form');


function sfs_transfer_certificate_shortcode() {

    $baseUrl = "https://n.sfsguwahati.ac.in/"; // base URL for TCFiles

    $url = "https://old.sfsguwahati.ac.in/WebSrv/srvTransferCertificate.asmx/LoadCertAccr";

    $response = wp_remote_post($url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode(["d" => []])
    ]);

    if (is_wp_error($response)) {
        return '<p>Error fetching data.</p>';
    }

    $json = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($json['d'][0]['str1'])) {
        return "<p>No data found.</p>";
    }

    $html = $json['d'][0]['str1'];

    // FIXED: Extract each full panel
    preg_match_all('/<div class=\'panel panel-default\'>.*?<\\/div>\\s*<\\/div>/is', $html, $matches);

    if (empty($matches[0])) {
        return "<p>No TC sections found.</p>";
    }

    ob_start();
    ?>

    <style>
        .tc-panel { background:#ab0f33; color:#fff; padding:12px; margin-bottom:8px;
            border-radius:6px; cursor:pointer; display:flex; align-items:center; }
        .tc-icon { margin-right:10px; font-weight:bold; }
        .tc-content { display:none; padding:12px; border:1px solid #ccc; border-radius:6px; margin-bottom:15px; }
        table { width:100%; border-collapse:collapse; }
        table th, table td { border:1px solid #aaa; padding:6px; }
        /* Popup Modal */
        .tc-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .tc-modal-content {
            background: #fff;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;      /* total modal max height */
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .tc-modal-header {
            padding: 10px;
            background: #ae152d;
            color: #fff;
            border-radius: 8px 8px 0 0;
        }
        
        .tc-modal-body {
            padding: 10px;
            max-height: calc(90vh - 60px); /* remaining space after header */
            overflow-y: auto;              /* ENABLE VERTICAL SCROLL */
        }
        
        .tc-modal-body img {
            width: 100%;
            height: auto;
        }

    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function(){

        // Accordion
        document.querySelectorAll(".tc-panel").forEach(panel => {
            panel.addEventListener("click", function(){
                let content = this.nextElementSibling;
                content.style.display = content.style.display === "block" ? "none" : "block";
            });
        });

        // Image Popup
        document.querySelectorAll(".tc-file-icon").forEach(icon => {
            icon.addEventListener("click", function(e){
                e.stopPropagation(); // prevent collapsing

                let studentId = this.getAttribute("data-id");
                let span = document.getElementById("fl_" + studentId);
                let filePath = span ? span.innerText.trim() : "";

                if (filePath === "") {
                    alert("No file found!");
                    return;
                }

                // Build full URL
                let fullUrl = "<?php echo $baseUrl; ?>" + filePath.replace("\\", "/");

                let modal = document.getElementById("tcModal");
                let modalImg = document.getElementById("tcModalImg");

                modalImg.src = fullUrl;
                modal.style.display = "flex";
            });
        });

        // Close modal
        document.getElementById("tcModal").addEventListener("click", function(){
            this.style.display = "none";
        });

    });
    
    document.addEventListener("DOMContentLoaded", function () {
        // Remove fa-download icons NOT wrapped inside <a> tag
        document.querySelectorAll("i.fa-download").forEach(function(icon) {
            if (!icon.closest("a")) {
                icon.remove();
            }
        });
    });
    </script>

    <div class="tc-wrapper">

        <?php foreach ($matches[0] as $block):

            preg_match('/Transfer Certificate[^<]*/i', $block, $titleMatch);
            $title = $titleMatch[0] ?? "Transfer Certificate";

            // Extract table
            preg_match('/<table.*?<\/table>/is', $block, $tableMatch);
            $tableHTML = $tableMatch[0] ?? "";
            
            // Replace original download icons with our custom ones
            $tableHTML = preg_replace_callback(
                '/<a[^>]*id=([0-9]+)[^>]*class=\'tc-file-icon\'[^>]*>/i',
                function ($m) {
                    $id = $m[1];
                    return "<a class='tc-file-icon' data-id='{$id}' style='color:green;cursor:pointer'><i class='fa fa-download'></i></a>";
                },
                $tableHTML
            );

        ?>

            <div class="tc-panel"><span class="tc-icon">+</span> <?php echo esc_html($title); ?></div>

            <div class="tc-content">
                <?php echo $tableHTML; ?>
                <?php 
                // Output hidden spans (file path containers)
                preg_match_all('/<span id=\'fl_(.*?)\'.*?>(.*?)<\/span>/is', $block, $spans, PREG_SET_ORDER);
                foreach ($spans as $sp) {
                    echo "<span id='fl_{$sp[1]}' style='display:none;'>{$sp[2]}</span>";
                }
                ?>
            </div>

        <?php endforeach; ?>

    </div>

    
    <div id="tcModal" class="tc-modal">
        <div class="tc-modal-content" align="center">
            <div class="tc-modal-header" align="right">
                <button class="tc-close-btn" id="tcCloseBtn">&times;</button>
            </div>
            <div class="tc-modal-body">
                <img id="tcModalImg" src="">
            </div>
        </div>
    </div>



    <?php
    return ob_get_clean();
}
add_shortcode('transfer_certificate', 'sfs_transfer_certificate_shortcode');
add_filter('the_content', 'do_shortcode');
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');


function staff_members_shortcode() {
    $url = "https://old.sfsguwahati.ac.in/WebSrv/srvHome.asmx/Loadloadteachbody";

//     $response = wp_remote_post($url, [
//         'headers' => ['Content-Type' => 'application/json']
//     ]);

//     if (is_wp_error($response)) {
//         return '<p>Error loading data.</p>';
//     }
	$response = wp_remote_post($url, [
    'headers' => [
        'Content-Type' => 'application/json; charset=utf-8',
        'Accept'       => 'application/json',
    ],
    'body'    => '{}',
    'timeout' => 20,
]);

if (is_wp_error($response)) {
    return '<p>Error loading data.</p>';
}

if (wp_remote_retrieve_response_code($response) !== 200) {
    return '<p>Server error: ' . intval(wp_remote_retrieve_response_code($response)) . '</p>';
}

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['d'][0])) {
        return '<p>No staff data found.</p>';
    }

    $item = $data['d'][0];

    $sections = [
        'Teaching Staff'       => $item['_datas']  ?? '',
        'Administrative Staff' => $item['_datas2'] ?? '',
        'Supporting Staff'     => $item['_datas3'] ?? '',
        'Bus Staff'            => $item['_datas4'] ?? '',
    ];

    ob_start();
    ?>
    <style>
        .sfs-accordion { margin-left: 120px; margin-right: 120px; }
        .sfs-accordion .acc-item { margin-bottom: 15px; }
        .sfs-accordion .acc-header {
            background: #861929;
            color: #fff;
            padding: 18px 60px 18px 60px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            border-radius: 5px;
            user-select: none;
        }
        .sfs-accordion .acc-header::before {
            content: "+";
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 32px;
            font-weight: bold;
        }
        .sfs-accordion .acc-header.active::before {
            content: "−";
        }
        .sfs-accordion .acc-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.5s ease;
            background: #fff;
            border: 1px solid #eee;
            border-top: none;
            padding: 0 20px;
        }
        .sfs-accordion .acc-body.open {
            max-height: max-content;
            padding: 30px 20px;
        }
        .pe-box1 {
          border-radius: 5px;
          background-color: #A41034;            /* maroon */
          box-shadow: 10px 11px 15px -8px rgba(0,0,0,0.75);
          margin: 13px 0px;
          padding-top: 15px;
          padding-bottom: 15px;
          height: 370px;
          text-align: -webkit-center;
        }
        .pe-hea-box1 {
          background: #fff;
          padding: 20px 9px;
          border-radius: 5px;
          margin-top: -70px;
          margin-left: 20px;
          margin-right: 20px;
          height: 230px;
        }
        .pe-hed-image img {
          border-radius: 50%;
          width: 180px;
          height: 180px;
        }
        .pe-body {
          padding-top: 60px;
          text-align: center;
        }
        .pe-body h4 {
          color: #000000;
          font-size: 16px;
          font-weight: bold;
        }
        .pe-body h5 {
          color: #000000;
          font-size: 15px;
          line-height: 18px;
          font-weight: 300;
        }
    </style>

    <div class="sfs-accordion">
        <?php $i = 0; foreach ($sections as $title => $content): $i++;
            if (empty(trim(strip_tags($content)))) continue;
            $is_open = ($i === 1) ? 'open' : '';
            $is_active = ($i === 1) ? 'active' : '';
        ?>
            <div class="acc-item">
                <div class="acc-header <?php echo $is_active; ?>" onclick="toggleSfsAccordion(this)">
                    <?php echo esc_html($title); ?>
                </div>
                <div class="acc-body <?php echo $is_open; ?>">
                    <?php echo $content; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function toggleSfsAccordion(header) {
            const body = header.nextElementSibling;
            const isCurrentlyOpen = body.classList.contains('open');

            // Close all others
            document.querySelectorAll('.sfs-accordion .acc-body').forEach(b => {
                b.classList.remove('open');
            });
            document.querySelectorAll('.sfs-accordion .acc-header').forEach(h => {
                h.classList.remove('active');
            });

            // Open clicked one (if it wasn't already open)
            if (!isCurrentlyOpen) {
                body.classList.add('open');
                header.classList.add('active');
            }
        }

        // Optional: open first one on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstHeader = document.querySelector('.sfs-accordion .acc-header');
            if (firstHeader && !firstHeader.classList.contains('active')) {
                toggleSfsAccordion(firstHeader);
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('staff_members', 'staff_members_shortcode');



function sfs_custom_common_script() {
    // Enqueue custom common.js after all other scripts
    wp_enqueue_script(
        'common-js',
        get_stylesheet_directory_uri() . '/assets/js/common.js',
        array('jquery'),
        '2.0.0',
        true
    );
}
// add_action('wp_enqueue_scripts', 'sfs_custom_common_script', 99);


function sfs_custom_common_css() {
    wp_enqueue_style( 'common-css-02', 
    get_stylesheet_directory_uri() . '/assets/css/common.css', 
    array(), 
    '3.0.1' );
}
add_action( 'wp_enqueue_scripts', 'sfs_custom_common_css' );

function sfs_custom_media_css() {
    wp_enqueue_style( 'custom-media-css-02', 
    get_stylesheet_directory_uri() . '/assets/css/custom-media.css', 
    array(), 
    '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'sfs_custom_media_css' );


function sfs_custom_min_css() {
    wp_enqueue_style( 'custom-min-css', 
    get_stylesheet_directory_uri() . '/assets/css/custom.min.css', 
    array(), 
    '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'sfs_custom_min_css' );


function unicamp_child_enqueue_scripts_new() {
	wp_enqueue_style( 'unicamp-child-style-new', get_stylesheet_directory_uri() . '/assets/css/w3.css' );
}
add_action( 'wp_enqueue_scripts', 'unicamp_child_enqueue_scripts_new', 16 );

/* SFS All Events and Information Importers - FINAL */

// Required for media_sideload_image() and related functions
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');


// =========================================================
// 1. EVENT IMPORTER LOGIC (sfs_import_all_events)
// =========================================================

// Define the option key for persistent storage
const SFS_IMPORTER_PAGE_KEY = 'sfs_importer_current_page';
const SFS_DEFAULT_INFO_IMAGE_ID = 17070;
    
function sfs_import_all_events() {
    
    $current_page = get_option(SFS_IMPORTER_PAGE_KEY, '1');
    
    $args = [
        "url1" => "undefined",
        "url2" => "undefined",
        "url3" => "undefined",
        "url4" => (string)$current_page, 
        "url" => "https://old.sfsguwahati.ac.in/WebSrv/srvHome.asmx/LoadHappeningOne"
    ];

    $curl_call = curl_call($args);
    $data = json_decode($curl_call);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->d) || !isset($data->d[0]->_data)) {
        error_log("SFS Cron: JSON/Structure Error in EVENT list fetch.");
        return;
    }

    $html_string = $data->d[0]->_data;
    if (empty($html_string)) {
        error_log("SFS Cron: EVENT Page " . $current_page . " returned empty content. Resetting page to 0.");
        update_option(SFS_IMPORTER_PAGE_KEY, '0');
        return;
    }

    $base_url = 'https://old.sfsguwahati.ac.in/';
    $doc = new DOMDocument();
    $unescaped_html = json_decode('"' . $html_string . '"');
    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $unescaped_html);
    $xpath = new DOMXPath($doc);
    $items = $xpath->query("//div[contains(@class, 'wpb_column') and contains(@class, 'col-md-4')]");

    if (!$items || $items->length === 0) {
        error_log("SFS Cron: EVENT Page " . $current_page . " returned structured content but no events. Resetting page to 0.");
        update_option(SFS_IMPORTER_PAGE_KEY, '0');
        return;
    }

    $imported_count = 0;
    $nodes_to_process = [];
    foreach ($items as $item) {
        $nodes_to_process[] = $item;
    }

    foreach (array_reverse($nodes_to_process) as $item) {
        
        $title_node = $xpath->query(".//h6[contains(@class, 'breakww')]", $item)->item(0);
        $image_node = $xpath->query(".//img[contains(@class, 'recentimg')]", $item)->item(0);
        $date_node = $xpath->query(".//div[contains(@class, 'col-md-3')]/a/span", $item)->item(0);
        $anchor_node = $xpath->query(".//a[contains(@href, 'recent-happening.php')]", $item)->item(0);

        if (!$title_node || !$image_node || !$date_node || !$anchor_node) {
            continue;
        }

        $title = trim($title_node->nodeValue);
        $image_url = trim($image_node->getAttribute('src'));
        $date_string = trim($date_node->nodeValue);
        $formatted_date = null;

        if ($date_string) {
            $date_obj = DateTime::createFromFormat('dM, y', $date_string);
            if ($date_obj) {
                $formatted_date = $date_obj->format('Y-m-d');
            }
        }
        $relative_url = trim($anchor_node->getAttribute('href'));

        parse_str(parse_url($relative_url, PHP_URL_QUERY), $query_params);
        
        $source_id = trim($query_params['sz'] ?? null);
        $sz = $source_id;
        $vz2 = $query_params['vz'] ?? null;
        $vz1 = str_replace(" ", "+", $vz2);
        $vz = str_replace("==", "", $vz1);

        $source_url = $base_url . $relative_url;
        
        if (!$source_id) {
            continue;
        }

        $item_data = [
            'title' => $title,
            'image_url' => $image_url,
            'date_string' => $formatted_date,
            'source_id' => $source_id,
            'sz' => $sz,
            'vz' => $vz,
            'source_url' => $source_url,
            'imported_items' => (int)$current_page 
        ];

        if (sfs_import_single_events($item_data)) {
            $imported_count++;
        }
    }
    
    if ($imported_count > 0) {
        $next_page = (int)$current_page + 1;
        update_option(SFS_IMPORTER_PAGE_KEY, (string)$next_page);
        error_log("SFS Cron: Successfully imported " . $imported_count . " EVENT items from page " . $current_page . ". Next run will use page " . $next_page . ".");
    } else {
        error_log("SFS Cron: No new EVENT items processed from page " . $current_page . ". Assuming end of feed and resetting page to 0.");
        update_option(SFS_IMPORTER_PAGE_KEY, '0');
    }
    
    // wp_die('Import finished');
}

// --- 2. SINGLE EVENT FETCHER (Fetches detailed post content) ---
function sfs_import_single_events($item_data) {
    
    $curl_args = [
        "url1" => $item_data['sz'],
        "url2" => $item_data['vz'],
        "url3" => "undefined",
        "url4" => $item_data['imported_items'],
        "url" => "https://old.sfsguwahati.ac.in/WebSrv/srvHome.asmx/LoadHappeningOne"
    ];

    $curl_call = curl_call($curl_args);
    $data = json_decode($curl_call);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->d) || !isset($data->d[0]->_data)) {
        error_log("SFS Cron: Single Event JSON/Structure Error.");
        return 0;
    }

    $raw_html_string = $data->d[0]->_data;

    if (!empty($raw_html_string)) {
        $decoded_content = json_decode('"' . $raw_html_string . '"'); 
        $full_raw_html = ($decoded_content !== null) ? $decoded_content : stripslashes($raw_html_string);
        
        // Use the DEDICATED Event content cleaner
        $clean_data = sfs_clean_and_extract_event_content($full_raw_html);

        $item_data['title']       = $clean_data['title'];
        $item_data['date_string'] = $clean_data['date'];
        $item_data['post_content'] = $clean_data['content'];
    } else {
        $item_data['post_content'] = '';
    }
error_log('badal: ' . wp_json_encode($item_data, JSON_PRETTY_PRINT));

    if (sfs_create_post_from_item($item_data)) {
        return 1;
    } else {
        return 0;
    }
}

// --- 3. POST CREATION FUNCTION for Events (Unchanged) ---
function sfs_create_post_from_item($item) {
    // ... (Existing post creation logic for tp_event, NO category assignment needed here, 
    // assuming it defaults to 'Event' or is handled elsewhere) ...
    $existing_post = get_posts([
        'post_type' => 'tp_event', 
        'post_status' => 'publish',
        'title' => $item['title'],
        'posts_per_page' => 1,
    ]);

    if (!empty($existing_post)) {
        return false; 
    }

    $date_obj = $item['date_string'];
    $post_date = $date_obj ? $date_obj : gmdate('Y-m-d');
    $post_content = $item['post_content'];

    $post_data = [
        'post_title' => strtoupper(wp_strip_all_tags($item['title'])),
        'post_content' => $post_content,
        'post_status' => 'publish',
        'post_type' => 'tp_event',
        'post_date' => $post_date,
        'post_date_gmt' => get_gmt_from_date($post_date),
    ];

    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        error_log('SFS Importer: Failed to insert EVENT post. Error: ' . $post_id->get_error_message());
        return false;
    }

    // =======================================================
    // ðŸŒŸ CATEGORY ASSIGNMENT LOGIC FOR EVENT ðŸŒŸ
    // =======================================================
    
    $event_category_name = 'Event'; // <--- ðŸš¨ VERIFY THIS CATEGORY NAME
    $taxonomy_slug = 'tp_event_category';       // <--- ðŸš¨ VERIFY THIS TAXONOMY
    
    $term = get_term_by('name', $event_category_name, $taxonomy_slug);

    if ($term && !is_wp_error($term)) {
        // Set the category (using 'false' for append to replace existing, though usually there are none)
        wp_set_object_terms(
            $post_id, 
            (int)$term->term_id, 
            $taxonomy_slug, 
            false
        );
    } else {
        error_log('SFS Importer: EVENT category "' . $event_category_name . '" not found. Skipping category assignment.');
    }
    // --- Post Meta Data (Full set is required) ---
    add_post_meta($post_id, 'tp_event_status', 'happening', true);
    add_post_meta($post_id, '_sfs_source_id', $item['source_id'], true);
    add_post_meta($post_id, '_sfs_source_url', $item['source_url'], true);
    add_post_meta($post_id, '_elementor_page_assets', '', true);
    add_post_meta($post_id, 'views', 0, true);
    add_post_meta($post_id, '_edit_lock', 1, true);
    add_post_meta($post_id, '_edit_last', 0, true);
    add_post_meta($post_id, 'tp_event_qty', 0, true);
    add_post_meta($post_id, 'tp_event_price', 0, true);
    add_post_meta($post_id, 'tp_event_date_start', $post_date, true);
    add_post_meta($post_id, 'tp_event_time_start', '', true);
    add_post_meta($post_id, 'tp_event_date_end', $post_date, true);
    add_post_meta($post_id, 'tp_event_time_end', '', true);
    add_post_meta($post_id, 'tp_event_registration_end_date', $post_date, true);
    add_post_meta($post_id, 'tp_event_registration_end_time', '', true);
    add_post_meta($post_id, 'tp_event_location', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_iframe', '', true);
    add_post_meta($post_id, 'tp_event_short_location', 'SFS', true);
    add_post_meta($post_id, 'tp_event_place', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_organiser', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_phone_number', '18003453581', true);
    add_post_meta($post_id, 'tp_event_email', 'infocenter@sfsguwahati.ac.in', true);
    add_post_meta($post_id, 'tp_event_website', 'https://sfsguwahati.ac.in', true);
    add_post_meta($post_id, '_yoast_wpseo_primary_tp_event_category', '', true);
    add_post_meta($post_id, '_yoast_wpseo_primary_tp_event_type', '', true);
    add_post_meta($post_id, '_yoast_wpseo_content_score', 90, true);
    add_post_meta($post_id, '_yoast_wpseo_estimated-reading-time-minutes', 1, true);

    error_log('badal2: ' . wp_json_encode($item, JSON_PRETTY_PRINT));
            
    if (!empty($item['image_url'])) {
    
        error_log('[SFS IMAGE] URL: ' . $item['image_url']);
    
        $attachment_id = sfs_safe_media_sideload_image(
            $item['image_url'],
            $post_id,
            $item['title']
        );
    
        error_log('[SFS IMAGE] RESULT: ' . print_r($attachment_id, true));
    
        if ($attachment_id && !is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
            update_post_meta($post_id, '_thumbnail_id', $attachment_id);
        }
    }

    
    return true;
}

function sfs_safe_media_sideload_image($url, $post_id, $title = '') {

    error_log('[SFS IMAGE] START');

    $tmp = download_url($url, 30);

    if (is_wp_error($tmp)) {
        error_log('[SFS IMAGE] download_url ERROR: ' . $tmp->get_error_message());
        return false;
    }

    error_log('[SFS IMAGE] downloaded to: ' . $tmp);

    $file = [
        'name'     => basename(parse_url($url, PHP_URL_PATH)),
        'type'     => mime_content_type($tmp),
        'tmp_name' => $tmp,
        'error'    => 0,
        'size'     => filesize($tmp),
    ];

    error_log('[SFS IMAGE] file array: ' . print_r($file, true));

    $sideload = wp_handle_sideload(
        $file,
        ['test_form' => false]
    );

    if (isset($sideload['error'])) {
        error_log('[SFS IMAGE] wp_handle_sideload ERROR: ' . $sideload['error']);
        @unlink($tmp);
        return false;
    }

    error_log('[SFS IMAGE] sideload path: ' . $sideload['file']);

    $attachment_id = wp_insert_attachment(
        [
            'post_mime_type' => $sideload['type'],
            'post_title'     => sanitize_file_name($title),
            'post_status'    => 'inherit',
        ],
        $sideload['file'],
        $post_id
    );

    if (is_wp_error($attachment_id)) {
        error_log('[SFS IMAGE] wp_insert_attachment ERROR: ' . $attachment_id->get_error_message());
        return false;
    }

    error_log('[SFS IMAGE] attachment_id: ' . $attachment_id);

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $meta = wp_generate_attachment_metadata($attachment_id, $sideload['file']);
    if (empty($meta) || is_wp_error($meta)) {
        error_log('[SFS IMAGE] metadata generation FAILED for JPG');
    
        // 🔥 still mark attachment usable
        update_post_meta($attachment_id, '_wp_attached_file', basename($sideload['file']));
    } else {
        wp_update_attachment_metadata($attachment_id, $meta);
    }

    error_log('[SFS IMAGE] DONE');

    return $attachment_id;
}

// =========================================================
// 2. INFORMATION IMPORTER LOGIC (sfs_import_all_info)
// =========================================================

const SFS_IMPORTER_INFO_PAGE_KEY = 'sfs_importer_info_current_page';

function sfs_import_all_info() {
    
    $current_page = get_option(SFS_IMPORTER_INFO_PAGE_KEY, '1');
    
    $args = [
        "url1" => "undefined",
        "url2" => "undefined",
        "url3" => "undefined",
        "url4" => (string)$current_page, 
        // ðŸš¨ NEW URL for list of notices
        "url" => "https://old.sfsguwahati.ac.in/WebSrv/srvHome.asmx/LoadlastestAll" 
    ];

    $curl_call = curl_call($args);
    $data = json_decode($curl_call);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->d) || !isset($data->d[0]->_data)) {
        error_log("SFS Cron: JSON/Structure Error in INFO list fetch.");
        return;
    }

    $html_string = $data->d[0]->_data;
    if (empty($html_string)) {
        error_log("SFS Cron: INFO Page " . $current_page . " returned empty content. Resetting page to 0.");
        update_option(SFS_IMPORTER_INFO_PAGE_KEY, '0');
        return;
    }

    $base_url = 'https://old.sfsguwahati.ac.in/';
    $doc = new DOMDocument();
    $unescaped_html = json_decode('"' . $html_string . '"');
    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $unescaped_html);
    $xpath = new DOMXPath($doc);
    $items = $xpath->query("//div[contains(@class, 'wpb_column') and contains(@class, 'col-md-4')]");

    if (!$items || $items->length === 0) {
        error_log("SFS Cron: INFO Page " . $current_page . " returned structured content but no notices. Resetting page to 0.");
        update_option(SFS_IMPORTER_INFO_PAGE_KEY, '0');
        return;
    }

    $imported_count = 0;
    $nodes_to_process = [];
    foreach ($items as $item) {
        $nodes_to_process[] = $item;
    }

    foreach (array_reverse($nodes_to_process) as $item) {
        
        $title_node = $xpath->query(".//h6[contains(@class, 'breakww')]", $item)->item(0);
        
        $object_node = $xpath->query(".//object", $item)->item(0);
        $image_node = $xpath->query(".//img[contains(@class, 'recentimg')]", $item)->item(0);
        
        $date_node = $xpath->query(".//div[contains(@class, 'col-md-3')]/a/span", $item)->item(0);
        $anchor_node = $xpath->query(".//a[contains(@href, 'updates.php')]", $item)->item(0);

        if (!$title_node || (!$object_node && !$image_node) || !$date_node || !$anchor_node) {
            continue;
        }

        $title = trim($title_node->nodeValue);
        
        if ($object_node) {
            $image_url = trim($object_node->getAttribute('data'));
        } elseif ($image_node) {
            $image_url = trim($image_node->getAttribute('src'));
        } else {
             $image_url = null;
        }
        
        $date_string = trim($date_node->nodeValue);
        $formatted_date = null;

        if ($date_string) {
            $date_obj = DateTime::createFromFormat('jM, y', $date_string);
            if ($date_obj) {
                $formatted_date = $date_obj->format('Y-m-d');
            }
        }
        $relative_url = trim($anchor_node->getAttribute('href'));

        parse_str(parse_url($relative_url, PHP_URL_QUERY), $query_params);
        
        $source_id = trim($query_params['sz'] ?? null);
        $sz = $source_id;
        $vz2 = $query_params['vz'] ?? null;
        $vz1 = str_replace(" ", "+", $vz2);
        $vz = str_replace("==", "", $vz1);

        $source_url = $base_url . $relative_url;
        
        if (!$source_id) {
            continue;
        }

        $item_data = [
            'title' => $title,
            'image_url' => $image_url,
            'date_string' => $formatted_date,
            'source_id' => $source_id,
            'sz' => $sz,
            'vz' => $vz,
            'source_url' => $source_url,
            'imported_items' => (int)$current_page 
        ];

        if (sfs_import_single_info($item_data)) {
            $imported_count++;
        }
    }
    
    if ($imported_count > 0) {
        $next_page = (int)$current_page + 1;
        update_option(SFS_IMPORTER_INFO_PAGE_KEY, (string)$next_page);
        error_log("SFS Cron: Successfully imported " . $imported_count . " INFO items from page " . $current_page . ". Next run will use page " . $next_page . ".");
    } else {
        error_log("SFS Cron: No new INFO items processed from page " . $current_page . ". Assuming end of feed and resetting page to 0.");
        update_option(SFS_IMPORTER_INFO_PAGE_KEY, '0');
    }
}

// --- NEW 2. SINGLE INFORMATION FETCHER (Fetches detailed post content) ---
function sfs_import_single_info($item_data) {
    
    $curl_args = [
        "url1" => $item_data['sz'],
        "url2" => $item_data['vz'],
        "url3" => "undefined",
        "url4" => $item_data['imported_items'],
        // ðŸš¨ NEW URL for single notice
        "url" => "https://old.sfsguwahati.ac.in/WebSrv/srvHome.asmx/LoadlastestAll" 
    ];

    $curl_call = curl_call($curl_args);
    $data = json_decode($curl_call);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->d) || !isset($data->d[0]->_data)) {
        error_log("SFS Cron: Single INFO JSON/Structure Error.");
        return 0;
    }
    
    $raw_html_string = $data->d[0]->_data;

    if (strpos($raw_html_string, 'update-content') !== false) {
        $info_items = sfs_clean_and_extract_notice_content($raw_html_string);
        foreach ($info_items as $item_data) {
            // insert into DB or wp_posts, as you do now
        }
    } else {
        $clean_data = sfs_clean_and_extract_info_content($raw_html_string);
        $item_data['title']        = $clean_data['title'];
        $item_data['date_string']  = $clean_data['date'];
        $item_data['post_content'] = $clean_data['file_url'];
        $item_data['description'] = $clean_data['description'];
        $item_data['just_text'] = $clean_data['just_text'];
    }

    if (sfs_create_info_post_from_item($item_data)) {
        return 1;
    } else {
        return 0;
    }
}

// --- NEW 3. POST CREATION FUNCTION for Information ---
function sfs_create_info_post_from_item($item) {
    
    $existing_post = get_posts([
        'post_type' => 'tp_event', 
        'post_status' => 'publish',
        'title' => $item['title'],
        'posts_per_page' => 1,
    ]);

    if (!empty($existing_post)) {
        return false;
    }

    $date_obj = $item['date_string'];
    $post_date = $date_obj ? $date_obj : gmdate('Y-m-d');
    $post_content = $item['post_content'];

    $post_data = [
        'post_title' => strtoupper(wp_strip_all_tags($item['title'])),
        'post_content' => $post_content,
        'post_status' => 'publish',
        'post_type' => 'tp_event', 
        'post_date' => $post_date,
        'post_date_gmt' => get_gmt_from_date($post_date),
    ];

    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        error_log('SFS Importer: Failed to insert INFO post. Error: ' . $post_id->get_error_message());
        return false;
    }
    
    // ðŸŒŸ CATEGORY ASSIGNMENT FOR INFORMATION/NOTICE ðŸŒŸ
    $info_category_name = 'Information'; // <--- VERIFY THIS CATEGORY NAME
    $taxonomy_slug = 'tp_event_category';       // <--- VERIFY THIS TAXONOMY
    
    $term = get_term_by('name', $info_category_name, $taxonomy_slug);

    if ($term && !is_wp_error($term)) {
        wp_set_object_terms($post_id, (int)$term->term_id, $taxonomy_slug, true); // Append=true or false as required
    } else {
        error_log('SFS Importer: INFO category "' . $info_category_name . '" not found. Skipping category assignment.');
    }
    
    // --- Post Meta Data (Full set is required) ---
    add_post_meta($post_id, 'tp_event_status', 'happening', true);
    add_post_meta($post_id, 'tp_event_description', $item['description'], true);
    add_post_meta($post_id, '_sfs_source_id', $item['source_id'], true);
    add_post_meta($post_id, '_sfs_source_url', $item['source_url'], true);
    add_post_meta($post_id, '_elementor_page_assets', '', true);
    add_post_meta($post_id, 'views', 0, true);
    add_post_meta($post_id, '_edit_lock', 1, true);
    add_post_meta($post_id, '_edit_last', 0, true);
    add_post_meta($post_id, 'tp_event_qty', 0, true);
    add_post_meta($post_id, 'tp_event_price', 0, true);
    add_post_meta($post_id, 'tp_event_date_start', $post_date, true);
    add_post_meta($post_id, 'tp_event_time_start', '', true);
    add_post_meta($post_id, 'tp_event_date_end', $post_date, true);
    add_post_meta($post_id, 'tp_event_time_end', '', true);
    add_post_meta($post_id, 'tp_event_registration_end_date', $post_date, true);
    add_post_meta($post_id, 'tp_event_registration_end_time', '', true);
    add_post_meta($post_id, 'tp_event_location', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_iframe', '', true);
    add_post_meta($post_id, 'tp_event_short_location', 'SFS', true);
    add_post_meta($post_id, 'tp_event_place', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_organiser', 'SFS Guwahati', true);
    add_post_meta($post_id, 'tp_event_phone_number', '18003453581', true);
    add_post_meta($post_id, 'tp_event_email', 'infocenter@sfsguwahati.ac.in', true);
    add_post_meta($post_id, 'tp_event_website', 'https://sfsguwahati.ac.in', true);
    add_post_meta($post_id, '_yoast_wpseo_primary_tp_event_category', '', true);
    add_post_meta($post_id, '_yoast_wpseo_primary_tp_event_type', '', true);
    add_post_meta($post_id, '_yoast_wpseo_content_score', 90, true);
    add_post_meta($post_id, '_yoast_wpseo_estimated-reading-time-minutes', 1, true);


    if (!empty($item['image_url'])) {
        $attachment_id = media_sideload_image($item['image_url'], $post_id, $item['title'], 'id');
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, SFS_DEFAULT_INFO_IMAGE_ID);
        } else {
            error_log('SFS Importer: Failed to sideload INFO image: ' . $attachment_id->get_error_message());
            set_post_thumbnail($post_id, SFS_DEFAULT_INFO_IMAGE_ID);
        }
    } else {
        set_post_thumbnail($post_id, SFS_DEFAULT_INFO_IMAGE_ID);
    }
    
    return true;
}


// =========================================================
// 3. COMMON & UTILITY FUNCTIONS
// =========================================================

// --- Dedicated Content Cleaner for Events (Original, but renamed) ---
function sfs_clean_and_extract_event_content($raw_html) {
    
    // 1. RAW HTML CLEANUP (Pre-DOM Parsing)
    $raw_html = preg_replace('/<p[^>]*>(\s|&nbsp;|<\s*!--\s*-->)*<\/p>/i', '', $raw_html);

    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $raw_html);
    $xpath = new DOMXPath($doc);

    // --- 2. EXTRACT TITLE AND DATE (Events) ---
    $title_node = $xpath->query("//div[contains(@class, 'blog-section')]/h6")->item(0);
    $raw_title = $title_node ? trim($title_node->nodeValue) : 'Untitled Event';
    $title = ucfirst(strtolower($raw_title));

    $date_node = $xpath->query("//ul[contains(@class, 'list-inline')]/li/span/i")->item(0);
    $date_string = $date_node ? trim($date_node->nodeValue) : '';

    $formatted_date = null;
    if ($date_string) {
        $date_obj = DateTime::createFromFormat('M d Y', $date_string);
        if ($date_obj) {
            $formatted_date = $date_obj->format('Y-m-d');
        }
    }

    // --- 3. EXTRACT CORE DESCRIPTION CONTENT ---
    $content_nodes = $xpath->query("//div[contains(@style, 'text-align:justify')]");
    $content_html = '';

    foreach ($content_nodes as $node) {
        $inner_html = '';
        foreach ($node->childNodes as $child) {
            $inner_html .= $doc->saveHTML($child);
        }
        $clean_html = preg_replace('/[[:cntrl:]\x{00A0}]+/u', ' ', $inner_html);
        $clean_html = preg_replace('/\s+/', ' ', $clean_html);
        $trimmed_html = trim($clean_html);
        
        if (!empty($trimmed_html)) {
            $content_html .= '<p>' . $trimmed_html . '</p>';
        }
    }

    // --- 4. EXTRACT IMAGE GALLERY (Clickable, Skip Featured) ---
    $all_image_nodes = $xpath->query("//div[@class='row']//img");
    $image_html = '';
    
    if ($all_image_nodes->length > 1) { 
        $image_html .= '<div class="row pt-4 pb-4">';
    }

    for ($i = 1; $i < $all_image_nodes->length; $i++) {
        $img = $all_image_nodes->item($i);
        $src = $img->getAttribute('src');
        
        if ($src) {
            $image_html .= '<div class="col-md-4 mb-3">';
            $image_html .= '<a href="' . $src . '" target="_blank" class="d-block">'; 
            $image_html .= '<figure class="wp-block-image">';
            $image_html .= '<img src="' . $src . '" alt="' . $title . ' photo" class="img-fluid" />'; 
            $image_html .= '</figure>';
            $image_html .= '</a>';
            $image_html .= '</div>';
        }
    }

    if ($all_image_nodes->length > 1) {
        $image_html .= '</div>';
    }
    
    $final_post_content = $content_html . $image_html;

    return [
        'title'   => $title,
        'date'    => $formatted_date,
        'content' => $final_post_content
    ];
}


// --- Dedicated Content Cleaner for Information (Updated for PDF Handling) ---
function sfs_clean_and_extract_info_content($raw_html) {
    if (empty($raw_html)) return [];

    // --- 1. Prepare upload folder ---
    $upload_dir = wp_upload_dir();
    $save_dir   = trailingslashit($upload_dir['basedir']) . 'sfs_files/';
    $save_url   = trailingslashit($upload_dir['baseurl']) . 'sfs_files/';
    if (!file_exists($save_dir)) wp_mkdir_p($save_dir);

    // --- 2. Load HTML safely ---
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $raw_html);
    $xpath = new DOMXPath($doc);
    libxml_clear_errors();

    // --- 3. Extract title ---
    $title_node = $xpath->query("//div[contains(@class,'blog-section')]/h6")->item(0);
    $title = $title_node ? trim($title_node->nodeValue) : '';

    // --- 4. Extract date ---
    // Correct path:
    // <span class="ml-3 dark-head">
    //      Posted: <span class="text-dark"><i>Apr 1 2025</i></span>
    // </span>
    $date_node = $xpath->query("//span[contains(@class, 'dark-head')]//i")->item(0);
    $date_string = $date_node ? trim($date_node->nodeValue) : '';

    $formatted_date = '';
    if ($date_string) {
        $date_obj = DateTime::createFromFormat('M d Y', $date_string);
        if ($date_obj) {
            $formatted_date = $date_obj->format('Y-m-d');
        }
    }

    // --- 5. Extract description ONLY from text-align:justify ---
    $desc_node = $xpath->query("//div[contains(@style,'text-align:justify')]")->item(0);
    if (!$desc_node) {
        $desc_node = $xpath->query("//div[@class='blog-section']/div[last()]")->item(0);
    }
    $description = $desc_node ? trim($desc_node->nodeValue) : '';


    // --- 6. Detect file (PDF or image) ---
    $file_url = '';
    $file_type = '';
    $remote_file = '';

    // Check <object> PDF
    $pdf_node = $xpath->query("//object[contains(@data,'.pdf')]")->item(0);
    if ($pdf_node) {
        $remote_file = $pdf_node->getAttribute('data');
        $file_type = 'pdf';
    }

    // Check <img> image
    if (!$remote_file) {
        $img_node = $xpath->query("//img[contains(@src,'.jpg') or contains(@src,'.jpeg') or contains(@src,'.png')]")->item(0);
        if ($img_node) {
            $remote_file = $img_node->getAttribute('src');
            $file_type = 'image';
        }
    }

    // Fallback <a href>
    if (!$remote_file) {
        $a_node = $xpath->query("//a[contains(@href,'.pdf') or contains(@href,'.jpg') or contains(@href,'.png')]")->item(0);
        if ($a_node) {
            $remote_file = $a_node->getAttribute('href');
            $file_type = (strpos($remote_file, '.pdf') !== false) ? 'pdf' : 'image';
        }
    }

    // --- 7. Download and store locally ---
    if (!empty($remote_file)) {
        $filename = basename(parse_url($remote_file, PHP_URL_PATH));
        $local_path = $save_dir . $filename;

        if (!file_exists($local_path)) {
            $response = wp_remote_get($remote_file, ['timeout' => 20]);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                file_put_contents($local_path, wp_remote_retrieve_body($response));
            }
        }

        $file_url = $save_url . $filename;
    }

    // --- 8. Return formatted output ---
    return [
        'title'       => $title ? $title : "Latest Information",
        'date'        => $formatted_date,
        'description' => $description,
        'file_type'   => $file_type,
        'file_url'    => esc_url($file_url),
    ];
}


// --- CURL CALL FUNCTION (Corrected and Completed) ---
function curl_call($args) {
    $url = $args['url'];
    $data = [
        "url1" => $args['url1'],
        "url2" => $args['url2'],
        "url3" => $args['url3'],
        "url4" => $args['url4']
    ];

    $jsonData = json_encode($data);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_ENCODING => '',
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0',
            'Accept: application/json, text/javascript, */*; q=0.01',
            'Accept-Language: en-US,en;q=0.5',
            'Content-Type: application/json; charset=UTF-8', // CRUCIAL for JSON POST
            'Content-Length: ' . strlen($jsonData)
        ]
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        return false;
    }
    curl_close($ch);
    return $response;
}


// Define the 'four_hours' interval
add_filter('cron_schedules', 'sfs_add_cron_interval');

function sfs_add_cron_interval($schedules) {
    $schedules['four_hours'] = [
        'interval' => 14400, // 4 hours in seconds
        'display' => esc_html__('Every Four Hours'),
    ];
    return $schedules;
}

// Event hooks (Linking the hook to the functions)
add_action('sfs_event_importer_cron_hook', 'sfs_import_all_events');

// Information hooks (Linking the hook to the functions)
add_action('sfs_info_importer_cron_hook', 'sfs_import_all_info');


add_action('sfs_import_blog_cron_new', 'sfs_import_blog_posts');
 
// Schedule both hooks on theme switch
add_action('init', 'sfs_schedule_importer_on_theme_switch');
add_action('after_switch_theme', 'sfs_schedule_importer_on_theme_switch');
function sfs_schedule_importer_on_theme_switch() {
    // Event Importer Hook
    if (!wp_next_scheduled('sfs_event_importer_cron_hook')) {
        wp_schedule_event(time(), 'four_hours', 'sfs_event_importer_cron_hook');
    }
    // Information Importer Hook
    if (!wp_next_scheduled('sfs_info_importer_cron_hook')) {
        wp_schedule_event(time(), 'four_hours', 'sfs_info_importer_cron_hook');
    }
    // 🔹 CRON SCHEDULER
    if (!wp_next_scheduled('sfs_import_blog_cron_new')) {
        wp_schedule_event(time(), 'daily', 'sfs_import_blog_cron_new');
    }
   
}

// Clear both hooks on theme deactivation
add_action('switch_theme', 'sfs_importer_deactivate');
function sfs_importer_deactivate() {
    wp_clear_scheduled_hook('sfs_event_importer_cron_hook');
    wp_clear_scheduled_hook('sfs_info_importer_cron_hook');
    wp_clear_scheduled_hook('sfs_import_blog_cron_new');
}


// Child Theme: functions.php (Revised Enqueue Function)

function custom_enqueue_child_scripts() {
    wp_enqueue_script( 
        'custom-script', 
        get_stylesheet_directory_uri() . '/assets/js/custom-script.js', 
        array( 'jquery' ), 
        '1.0', 
        true 
    );

    // Localize data (AJAX URL and Nonce remain the same)
    wp_localize_script( 'custom-script', 'ajax_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'view_post_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'custom_enqueue_child_scripts' );

// (Keep the custom_get_post_details_ajax function from the previous response)


/**
 * 1. AJAX Handler to retrieve post data
 */
function custom_get_post_details_ajax() {
    // 1. Security Check: Always verify the nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'view_post_nonce' ) ) {
        wp_send_json_error( 'Nonce verification failed.' );
    }

    // 2. Get Post ID
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

    if ( $post_id <= 0 ) {
        wp_send_json_error( 'Invalid Post ID.' );
    }

    // 3. Retrieve Post Data
    $post = get_post( $post_id );

    if ( ! $post ) {
        wp_send_json_error( 'Post not found.' );
    }

    // Example of data to return
    $data = array(
        'title'   => get_the_title( $post_id ),
        'content' => $post->post_content,
        'author'  => get_the_author_meta( 'display_name', $post->post_author ),
        'date'    => get_the_date( '', $post_id ),
    );

    // 4. Send the JSON response and exit
    wp_send_json_success( $data );
}
// Hook for logged-in users
add_action( 'wp_ajax_get_post_details', 'custom_get_post_details_ajax' );
// Hook for non-logged-in users
add_action( 'wp_ajax_nopriv_get_post_details', 'custom_get_post_details_ajax' );


// 🔹 MAIN IMPORT FUNCTION
function sfs_import_blog_posts() {
    $page = (int) get_option('sfs_import_page', 1);
    $api_url = "https://blog.sfsguwahati.ac.in/wp-json/wp/v2/posts?_fields=id,title,excerpt,link,_links,date,modified&per_page=12&page={$page}";
    
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        error_log('SFS Import: API request failed.');
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $posts = json_decode($body);

    // Stop pagination if empty
    if (empty($posts) || !is_array($posts)) {
        error_log("SFS Import: No posts found on page {$page}. Resetting page count.");
        update_option('sfs_import_page', 1);
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $imported_count = 0;
    $default_image = '/wp-content/uploads/2021/01/logoN.png';

    foreach ($posts as $item) {
        $title = wp_strip_all_tags($item->title->rendered ?? '');
        $content = $item->excerpt->rendered ?? '';
        $date = $item->date ?? current_time('mysql');
        $slug = sanitize_title($title);
        $original_link = esc_url($item->link ?? '');

        if (empty($title)) continue;

        // Skip duplicates
        $existing = get_page_by_path($slug, OBJECT, 'post');
        if ($existing) continue;

        // === Featured Image Logic ===
        $image_url = '';
        if (!empty($item->_links->{'wp:featuredmedia'}[0]->href)) {
            $media_api = $item->_links->{'wp:featuredmedia'}[0]->href;
            $media_response = wp_remote_get($media_api);
            if (!is_wp_error($media_response)) {
                $media_data = json_decode(wp_remote_retrieve_body($media_response));
                if (!empty($media_data->source_url) && filter_var($media_data->source_url, FILTER_VALIDATE_URL)) {
                    $image_url = esc_url_raw($media_data->source_url);
                }
            }
        }

        // ✅ Ensure fallback image is always used if none valid
        if (empty($image_url)) {
            $image_url = $default_image;
        }

        // === Download image locally ===
        $local_image_id = '';
        if (!empty($image_url)) {
            $tmp = download_url($image_url);

            if (!is_wp_error($tmp)) {
                $file_array = [
                    'name' => basename(parse_url($image_url, PHP_URL_PATH)),
                    'tmp_name' => $tmp,
                ];

                $local_image_id = media_handle_sideload($file_array, 0);

                if (is_wp_error($local_image_id)) {
                    @unlink($tmp);
                    $local_image_id = '';
                }
            } else {
                // fallback download failed too → use default
                $local_image_id = '';
            }
        }

        // === Add "Read Full Post" button ===
        $content .= "<div style='margin-top:20px;'>
            <a href='{$original_link}' target='_blank' rel='noopener noreferrer'
               style='background:#9d2235;color:#fff;padding:8px 16px;border-radius:5px;text-decoration:none;display:inline-block;'>
               🔗 Read Full Post on SFS Blog
            </a>
        </div>";

        // === Create local post ===
        $post_data = [
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_date'    => $date,
            'post_type'    => 'post',
        ];
        $post_id = wp_insert_post($post_data);

        // === Set featured image ===
        if ($post_id) {
            if ($local_image_id) {
                set_post_thumbnail($post_id, $local_image_id);
            } else {
                // If even download failed, attach the default image from media library
                $default_id = attachment_url_to_postid($default_image);
                if ($default_id) {
                    set_post_thumbnail($post_id, $default_id);
                }
            }
        }

        $imported_count++;
        sleep(1);
    }

    // ✅ Move to next page only if something imported
    if ($imported_count > 0) {
        update_option('sfs_import_page', $page + 1);
        error_log("SFS Import: Imported {$imported_count} posts from page {$page}. Moving to page " . ($page + 1));
    } else {
        update_option('sfs_import_page', 1);
        error_log("SFS Import: No new posts on page {$page}. Resetting to page 1.");
    }
}

add_filter('wp_mail_from', function() {
    return 'admission@sfsguwahati.ac.in';
});

add_filter('wp_mail_from_name', function() {
    return 'SFS Admission';
});

function delete_duplicate_tp_events() {
    global $wpdb;

    // 1. Delete duplicates (keep latest ID)
    $delete_query = "
        DELETE FROM {$wpdb->posts}
        WHERE ID NOT IN (
            SELECT * FROM (
                SELECT MAX(ID)
                FROM {$wpdb->posts}
                WHERE post_type = 'tp_event'
                GROUP BY post_title
            ) AS keep_ids
        )
        AND post_type = 'tp_event';
    ";

    $wpdb->query($delete_query);

    // 2. Update old domain to new domain in post_content
    $update_query = "
        UPDATE {$wpdb->posts}
        SET post_content = REPLACE(post_content, 'n.old.sfsguwahati.ac.in', 'n.sfsguwahati.ac.in')
        WHERE post_content LIKE '%n.old.sfsguwahati.ac.in%';
    ";

    $wpdb->query($update_query);
}

function schedule_delete_duplicate_events() {
    if (!wp_next_scheduled('delete_duplicate_tp_events_hook')) {
        wp_schedule_event(time(), 'hourly', 'delete_duplicate_tp_events_hook');
    }
}
add_action('wp', 'schedule_delete_duplicate_events');

add_action('delete_duplicate_tp_events_hook', 'delete_duplicate_tp_events');

add_filter('cron_schedules', function($schedules) {
    $schedules['ten_minutes'] = [
        'interval' => 600,
        'display'  => 'Every 10 Minutes'
    ];
    return $schedules;
});

function allow_pdf_uploads($mimes)
{
    $mimes['pdf'] = 'application/pdf';
    return $mimes;
}

add_filter('upload_mimes', 'allow_pdf_uploads');

// Disable Heartbeat in Post Editor to stop admin-ajax.php NS_BINDING_ABORTED
add_action('init', 'disable_editor_heartbeat', 1);
function disable_editor_heartbeat() {
    global $pagenow;
    if (in_array($pagenow, array('post.php', 'post-new.php'))) {
        wp_deregister_script('heartbeat');
    }
}


/**
 * Shortcode: [sfs_achievements type="internal"]
 * Fetches and displays school achievements in an interactive accordion.
 */
add_shortcode('sfs_achievements', 'sfs_achievements_shortcode');

function sfs_achievements_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'internal', // 'internal' or 'external'
    ], $atts, 'sfs_achievements');

    $event_type = sanitize_text_field($atts['type']);
    $transient_key = 'sfs_achievements_' . md5($event_type);

    // Check cached response for 1 hour to optimize performance
    $events = get_transient($transient_key);

    if ($events === false) {
        $api_url = add_query_arg(
            ['eventType' => $event_type],
            'https://websiteapi.sfsguwahati.ac.in/api/home/achievements'
        );

        $response = wp_remote_get($api_url, [
            'timeout' => 15,
            'headers' => [
                'Accept'    => 'application/json',
                'x-api-key' => 'G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf',
            ],
        ]);

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body, true);

            if (!empty($json['success']) && !empty($json['data']) && is_array($json['data'])) {
                $events = $json['data'];
                set_transient($transient_key, $events, HOUR_IN_SECONDS);
            }
        }

        // Fallback demo data if API is temporarily down
        if (empty($events)) {
            $events = [
                [
                    "eventName"   => "DESALITE FIESTA",
                    "organizedby" => "SFS GUWAHATI",
                    "dateOfEvent" => "01/11/2023",
                    "eventDetails" => [
                        ["category" => "DRAWING",      "regNo" => "2490", "stundetName" => "DHARITRI PHUKAN",  "class" => "10 B", "positionName" => "1st Position"],
                        ["category" => "ELOCUTION",    "regNo" => "2954", "stundetName" => "AYUSHI NANG UKOI",  "class" => "8 C",  "positionName" => "1st Position"],
                        ["category" => "QUIZ",         "regNo" => "3029", "stundetName" => "HARSHITA BASAK",   "class" => "8 A",  "positionName" => "2nd Position"],
                        ["category" => "QUIZ",         "regNo" => "5129", "stundetName" => "MRIDUTPOL GOGOI",  "class" => "9 A",  "positionName" => "2nd Position"],
                        ["category" => "SOLO SINGING", "regNo" => "2750", "stundetName" => "UPASANA BARMAN",   "class" => "9 D",  "positionName" => "1st Position"],
                    ]
                ],
                [
                    "eventName"   => "2ND ONLINE TALENT HUNT 2022",
                    "organizedby" => "SFS Guwahati",
                    "dateOfEvent" => "15/08/2022",
                    "eventDetails" => [
                        ["category" => "BUDDING VOCALIST", "regNo" => "1716", "stundetName" => "ANGAN DAS", "class" => "10 C", "positionName" => "2nd Position"]
                    ]
                ]
            ];
        }
    }

    if (empty($events)) {
        return '<p class="sfs-no-achievements">No achievements available at the moment.</p>';
    }

    ob_start();
    ?>
    <style>
        .sfs-achieve-accordion {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-width: 900px;
            margin: 20px auto;
            font-family: inherit;
        }

        .sfs-achieve-item {
            border: 1.5px solid #a41034;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .sfs-achieve-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 22px;
            background: #fff;
            color: #1a1a1a;
            border: none;
            outline: none;
            cursor: pointer;
            text-align: left;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: background 0.25s ease, color 0.25s ease;
        }

        .sfs-achieve-item.active .sfs-achieve-header {
            background: #a41034;
            color: #fff;
        }

        .sfs-achieve-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .sfs-achieve-icon svg {
            width: 16px;
            height: 16px;
            stroke: #a41034;
            transition: stroke 0.25s ease;
        }

        .sfs-achieve-item.active .sfs-achieve-icon svg {
            stroke: #fff;
            transform: rotate(180deg);
        }

        /* Enforce display toggle with !important against theme styles */
        .sfs-achieve-content {
            display: none !important;
            padding: 24px 20px;
            background: #fff;
        }

        .sfs-achieve-item.active .sfs-achieve-content {
            display: block !important;
        }

        .sfs-achieve-box {
            border: 1.5px solid #a41034;
            border-radius: 8px;
            padding: 18px;
            background: #fff;
            box-sizing: border-box;
        }

        .sfs-achieve-meta {
            background: #a41034;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
            padding: 10px 14px;
            border-radius: 2px 2px 0 0;
            letter-spacing: 0.3px;
        }

        .sfs-achieve-table-wrapper {
            overflow-x: auto;
            margin-top: 0;
        }

        .sfs-achieve-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .sfs-achieve-table th,
        .sfs-achieve-table td {
            border: 1px solid #dcdcdc;
            padding: 10px 14px;
            vertical-align: middle;
        }

        .sfs-achieve-table th {
            background-color: #8c0d2c;
            color: #fff;
            font-weight: 700;
            white-space: nowrap;
        }

        .sfs-achieve-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .sfs-achieve-table td:first-child,
        .sfs-achieve-table th:first-child {
            text-align: center;
            width: 50px;
        }

        @media (max-width: 600px) {
            .sfs-achieve-header {
                font-size: 13px;
                padding: 12px 16px;
            }
            .sfs-achieve-box {
                padding: 10px;
            }
            .sfs-achieve-table th,
            .sfs-achieve-table td {
                padding: 8px 10px;
                font-size: 12px;
            }
        }
    </style>

    <div class="sfs-achieve-accordion">
        <?php foreach ($events as $index => $event): 
            $event_name   = !empty($event['eventName']) ? $event['eventName'] : 'Achievement Event';
            $organized_by = !empty($event['organizedby']) ? $event['organizedby'] : 'SFS Guwahati';
            $event_date   = !empty($event['dateOfEvent']) ? $event['dateOfEvent'] : '';
            $details      = !empty($event['eventDetails']) && is_array($event['eventDetails']) ? $event['eventDetails'] : [];
            $is_first     = ($index === 0);
        ?>
            <div class="sfs-achieve-item <?= $is_first ? 'active' : ''; ?>">
                <button type="button" 
                        class="sfs-achieve-header" 
                        onclick="sfsToggleAchievement(this)" 
                        aria-expanded="<?= $is_first ? 'true' : 'false'; ?>">
                    <span><?= esc_html($event_name); ?></span>
                    <span class="sfs-achieve-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </button>

                <div class="sfs-achieve-content">
                    <div class="sfs-achieve-box">
                        <div class="sfs-achieve-meta">
                            Organized By: <?= esc_html($organized_by); ?>
                            <?php if ($event_date): ?>
                                , Date: <?= esc_html($event_date); ?>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($details)): ?>
                            <div class="sfs-achieve-table-wrapper">
                                <table class="sfs-achieve-table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Category</th>
                                            <th>Reg No</th>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($details as $idx => $row): 
                                            $student_name = $row['stundetName'] ?? $row['studentName'] ?? '';
                                        ?>
                                            <tr>
                                                <td><?= $idx + 1; ?></td>
                                                <td><?= esc_html($row['category'] ?? '-'); ?></td>
                                                <td><?= esc_html($row['regNo'] ?? '-'); ?></td>
                                                <td><?= esc_html($student_name ?: '-'); ?></td>
                                                <td><?= esc_html($row['class'] ?? '-'); ?></td>
                                                <td><?= esc_html($row['positionName'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p style="text-align:center; padding: 15px; margin: 0; color: #666;">No participant records available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        if (typeof window.sfsToggleAchievement === 'undefined') {
            window.sfsToggleAchievement = function(btn) {
                var currentItem = btn.closest('.sfs-achieve-item');
                var accordion   = btn.closest('.sfs-achieve-accordion');
                var isOpen      = currentItem.classList.contains('active');

                // Close all other items in this accordion
                if (accordion) {
                    var items = accordion.querySelectorAll('.sfs-achieve-item');
                    items.forEach(function(item) {
                        item.classList.remove('active');
                        var h = item.querySelector('.sfs-achieve-header');
                        if (h) h.setAttribute('aria-expanded', 'false');
                    });
                }

                // If the clicked one was closed, open it
                if (!isOpen) {
                    currentItem.classList.add('active');
                    btn.setAttribute('aria-expanded', 'true');
                }
            };
        }
    </script>
    <?php
    return ob_get_clean();
}