<?php
/*
Plugin Name: SFS Enrollments
Description: Load enrollment data from API into fixed layout.
Version: 1.1
*/

if(!defined('ABSPATH')) exit;


/* SHORTCODE */
add_shortcode('sfs_enrollments','sfs_enrollment_shortcode');

function sfs_enrollment_shortcode(){

    // Check transient cache to prevent 26-second timeout on live visits
    // $cached = get_transient('sfs_enrollment_tables');
    // if ($cached !== false && is_array($cached) && !empty($cached)) {
    //     $tables = $cached;
    // } else {
        $api = 'https://old.sfsguwahati.ac.in/WebSrv/srvEnrollment.asmx/LoadEnrollment';

        // Use direct cURL with browser headers to avoid HTTP 403 firewall block on WordPress User-Agent
        $ch = curl_init($api);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=utf-8',
                'Accept: application/json, text/javascript, */*',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Referer: https://sfsguwahati.ac.in',
            ],
            CURLOPT_TIMEOUT        => 90
        ]);

        $body      = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            return "API Connection Error: " . esc_html($curl_err);
        }

        if ($http_code !== 200) {
            return "API Error: Server returned HTTP " . esc_html($http_code);
        }

        $json = json_decode($body, true);

        if (empty($json['d'])) {
            return "No data found (API returned empty response: " . esc_html(substr(strip_tags($body), 0, 100)) . ")";
        }

        /* Extract year + clean table */
        $tables = [];

        foreach ($json['d'] as $row) {
            if (empty($row['_data'])) continue;

            $html = html_entity_decode($row['_data']);

            // Find all collapsible year sections
            preg_match_all('/<div id=\'(\d{4})\'.*?<\/table>/is', $html, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $year = $match[1];

                // Extract table inside this year block
                preg_match('/<table.*?<\/table>/is', $match[0], $tbl);
                $table = $tbl[0] ?? '';

                if ($table) {
                    $table = preg_replace('/style="[^"]*"/i', '', $table);
                    $table = preg_replace('/width="[^"]*"/i', '', $table);
                    $table = str_replace('<table', '<table class="sfs-clean-table"', $table);
                    $table = '<div class="sfs-table-wrapper">' . $table . '</div>';
                }

                $tables[$year] = $table;
            }
//         }

//         if (!empty($tables)) {
//             // Cache parsed tables for 12 hours
//             set_transient('sfs_enrollment_tables', $tables, 12 * HOUR_IN_SECONDS);
//         }
    }

    if (empty($tables)) {
        return "No data found";
    }

    ob_start();
?>

<style>
.result-container{
    max-width:100%;
    background:#fff;
    border:1px solid #ddd;
    border-radius:6px;
    overflow:hidden;
}

/* FLEX FIX */
.result-body{
    display:flex;
    align-items:flex-start;
}

/* LEFT NAV */
.cls-nav{
    /*background:#fafafa;*/
    border-right:1px solid #ddd;
}

.cls-nav button{
    display:block;
    width:100%;
    border:none;
    /* background:none; */
    text-align:left;
    /* padding:10px 14px; */
    cursor:pointer;
    font-size:15px;
    border-bottom:1px solid transparent;
}

.cls-nav button:hover{
    background:#fcecec;
    color:maroon;
}

.cls-nav button.active{
    background:#fff0f0;
    color:maroon;
    font-weight:bold;
}

/* RIGHT CONTENT */
.result-content{
    flex:1;
    padding:10px;
    overflow:hidden;
}

.cls-section{display:none;}
.cls-section.active{display:block;}

/* CLEAN TABLE */
.sfs-clean-table th{
    padding:6px;
    /* text-align:center; */
	background-color: #ae152d;
	color: white;
    border:1px solid #ccc;
}

.sfs-clean-table td{
    padding:6px;
    text-align:center;
    border:1px solid #ccc;
}

/* MOBILE */
/* TABLE WRAPPER */
.sfs-table-wrapper{
    width:100%;
    overflow-x:scroll;
    -webkit-overflow-scrolling: touch;
}

/* TABLE BASE */
.sfs-clean-table{
    width:100%;
    min-width:600px; /* prevents squashing */
    border-collapse:collapse;
}

/* FIRST COLUMN LEFT ALIGN */
.sfs-clean-table th:first-child,
.sfs-clean-table td:first-child{
    text-align:left !important;
}

/* MOBILE IMPROVEMENTS */
@media (max-width:768px){

    .sfs-table-wrapper{
        border-radius:6px;
    }

    .sfs-clean-table{
        font-size:14px;
    }

    .sfs-clean-table th,
    .sfs-clean-table td{
        padding:8px 10px;
    }

}

</style>


<div class="result-container">
<div class="result-body">

<!-- LEFT NAV -->
<div class="cls-nav">
<?php $first=true; foreach($tables as $year=>$tbl): ?>
<button class="cls-btn <?= $first?'active':'' ?>"
        data-target="<?= esc_attr($year) ?>">
<?= esc_html($year.'-'.($year+1)) ?>
</button>
<?php $first=false; endforeach; ?>
</div>


<!-- RIGHT CONTENT -->
<div class="result-content">
<?php $first=true; foreach($tables as $year=>$tbl): ?>
<div id="<?= esc_attr($year) ?>"
     class="cls-section <?= $first?'active':'' ?>">
<?= $tbl ?>
</div>
<?php $first=false; endforeach; ?>
</div>

</div>
</div>


<script>
document.querySelectorAll('.cls-btn').forEach(btn=>{
    btn.onclick=function(){
        document.querySelectorAll('.cls-btn')
        .forEach(b=>b.classList.remove('active'));

        document.querySelectorAll('.cls-section')
        .forEach(s=>s.classList.remove('active'));

        this.classList.add('active');
        document.getElementById(this.dataset.target)
        ?.classList.add('active');
    };
});
</script>

<?php
return ob_get_clean();
}
