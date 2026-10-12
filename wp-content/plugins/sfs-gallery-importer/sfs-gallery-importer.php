<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/**
 * Plugin Name: SFS Gallery Importer (Stable Batch Import)
 * Description: Imports SFS galleries safely in batches
 * Version: 2.0
 */

if (!defined('ABSPATH')) exit;

define('SFS_BATCH_SIZE', 4);

add_filter('cron_schedules', function ($schedules) {
    $schedules['five_minutes'] = [
        'interval' => 300,
        'display'  => 'Every 5 Minutes'
    ];
    return $schedules;
});


/* =========================
   POST TYPES
========================= */
add_action('init', function () {

    register_post_type('sfs_gallery', [
        'label' => 'SFS Galleries',
        'public' => true,
        'supports' => ['title', 'thumbnail'],
    ]);

    register_post_type('sfs_gallery_image', [
        'label' => 'SFS Gallery Images',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title','thumbnail'],
    ]);
});



function sfs_import_image($url, $post_id) {

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Clean URL
    $clean_url = strtok($url, '?');

    // Download file
    $tmp = download_url($clean_url, 360);
    if (is_wp_error($tmp)) {
        error_log('Download failed: ' . $tmp->get_error_message());
        return;
    }

    // Detect image type
    $image_info = @getimagesize($tmp);
    if (!$image_info) {
        @unlink($tmp);
        error_log('Invalid image file');
        return;
    }

    $mime = $image_info['mime'];

    // Convert JPG/JPEG → PNG
    if ($mime === 'image/jpeg' || $mime === 'image/jpg') {

        $img = imagecreatefromjpeg($tmp);
        if (!$img) {
            @unlink($tmp);
            return;
        }

        $png_tmp = $tmp . '.png';

        imagepng($img, $png_tmp, 9);
        imagedestroy($img);

        @unlink($tmp);
        $tmp = $png_tmp;
        $filename = uniqid('sfs_', true) . '.png';

    } elseif ($mime === 'image/png' || $mime === 'image/webp') {

        $filename = uniqid('sfs_', true) . '.png';

        if ($mime === 'image/webp') {
            $img = imagecreatefromwebp($tmp);
            $png_tmp = $tmp . '.png';
            imagepng($img, $png_tmp, 9);
            imagedestroy($img);
            @unlink($tmp);
            $tmp = $png_tmp;
        }

    } else {
        @unlink($tmp);
        return;
    }

    // Prepare for WP upload
    $file = [
        'name'     => $filename,
        'tmp_name'=> $tmp,
    ];

    // Upload to Media Library
    $id = media_handle_sideload($file, $post_id);

    // Cleanup temp file
    if (file_exists($tmp)) {
        @unlink($tmp);
    }

    if (is_wp_error($id)) {
        error_log('Media sideload failed: ' . $id->get_error_message());
        return;
    }

    set_post_thumbnail($post_id, $id);
}



/* =========================
   API CALL
========================= */
function sfs_api_call() {

    $ch = curl_init('https://old.sfsguwahati.ac.in/WebSrv/srvPhotoGallery.asmx/LoadPhotos');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'url1' => 'undefined',
            'url2' => 'undefined',
            'url3' => 'undefined'
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 60,
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

/* =========================
   FETCH UNIQUE CATEGORIES
========================= */
function sfs_get_all_categories() {

    $cached = get_option('sfs_cached_categories');
    if ($cached !== false) {
        return $cached;
    }
    

    $res = sfs_api_call();
    if (empty($res['d'][0]['_data'])) {
        return [];
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadHTML('<?xml encoding="UTF-8">' . $res['d'][0]['_data']);
    libxml_clear_errors();

    $xp = new DOMXPath($dom);
    $links = $xp->query("//a[contains(@href,'photo-gallery.php?sz=')]");

    $unique = [];

    foreach ($links as $a) {

        $href = html_entity_decode($a->getAttribute('href'));

        preg_match('/sz=([^&]+)/', $href, $sz_match);
        preg_match('/vz=([^&]+)/', $href, $vz_match);

        if (empty($sz_match[1]) || empty($vz_match[1])) {
            continue;
        }

        $sz = urldecode($sz_match[1]);
        $vz = urldecode($vz_match[1]);

        $key = $sz . '|' . $vz;
        if (isset($unique[$key])) {
            continue;
        }

        // 🔥 Extract image + title ONCE
        $imgNode = $xp->query(".//img", $a)->item(0);

        $title = 'Gallery';
        $img   = '';

        if ($imgNode) {
            $title = trim($imgNode->getAttribute('alt')) ?: 'Gallery';
            $img   = trim($imgNode->getAttribute('src'));

            if ($img && str_starts_with($img, '/')) {
                $img = 'https://n.sfsguwahati.ac.in' . $img;
            }
        }

        $unique[$key] = [
            'sz'    => $sz,
            'vz'    => $vz,
            'title' => $title,
            'img'   => $img,
        ];
    }

    unset($dom, $xp);
    gc_collect_cycles();

    // 🔒 Cache permanently
    update_option('sfs_cached_categories', array_values($unique), false);

    return array_values($unique);
}


/* =========================
   BATCH IMPORT
========================= */
function sfs_import_batch() {

    $all = sfs_get_all_categories();
    $total = count($all);
    

    
    update_option('sfs_import_total', $total);

    $offset = (int) get_option('sfs_import_offset', 0);
    $end = min($offset + SFS_BATCH_SIZE, $total);

    if ($offset >= $total) {
        return ['done' => true, 'imported' => 0];
    }

    $imported = 0;

    for ($i = $offset; $i < $end; $i++) {

        $item = $all[$i];
        $sz    = $item['sz'];
        $vz    = $item['vz'];
        $title = $item['title'];
        $img_url = $item['img'];
        
        
        if (!$sz || !$vz) {
            continue;
        }

        $key = $sz . '|' . $vz;
        $hash = md5($key);

        if (get_posts([
            'post_type' => 'sfs_gallery',
            'meta_key'  => 'gallery_hash',
            'meta_value'=> $hash,
            'fields'    => 'ids',
            'numberposts' => 1
        ])) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'sfs_gallery',
            'post_title' => wp_strip_all_tags($title),
            'post_status' => 'publish'
        ]);

        update_post_meta($post_id, 'sz', $sz);
        update_post_meta($post_id, 'vz', $vz);
        update_post_meta($post_id, 'gallery_hash', $hash);
        

    
        if(empty($img_url)) {
            $img_url = "https://sn.laksvrddhi.com/wp-content/uploads/2026/01/default-gallery.webp";    
        }
        

        
        if (!empty($img_url)) {
            // echo "--- ".$img_url;
            sfs_import_image($img_url, $post_id);
        }
        
       

        
        $imported++;
    }
    
    // exit;
    update_option('sfs_import_offset', $end);
    
    return ['done' => false, 'imported' => $imported];
}

function sfs_import_gallery_images($gallery_id) {
    @set_time_limit(0);
    @ini_set('memory_limit', '1024M');
    
    $sz = get_post_meta($gallery_id, 'sz', true);
    // $vz = get_post_meta($gallery_id, 'vz', true);
    
    $vz = rtrim(get_post_meta($gallery_id, 'vz', true), '=');

    if (!$sz || !$vz) return;

    $ch = curl_init('https://old.sfsguwahati.ac.in/WebSrv/srvPhotoGallery.asmx/LoadPhotos');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'url1' => $sz,
            'url2' => $vz,
            'url3' => 'undefined'
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 60,
    ]);

    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    

    if (empty($res['d'][0]['_data'])) return;

    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0','UTF-8');
    $dom->loadHTML('<?xml encoding="UTF-8">'.$res['d'][0]['_data']);
    libxml_clear_errors();

    $xp = new DOMXPath($dom);

    foreach ($xp->query("//img[contains(@src,'.jpg') or contains(@src,'.png')]") as $img) {

    $url = trim($img->getAttribute('src'));
    if (!$url) continue;

    if (str_starts_with($url, '/')) {
        $url = 'https://n.sfsguwahati.ac.in' . $url;
    }
    
    if(empty($url)) {
        $url = "https://sn.laksvrddhi.com/wp-content/uploads/2026/01/default-gallery.webp";    
    }
    
    $hash = md5($url);

    if (get_posts([
        'post_type' => 'sfs_gallery_image',
        'meta_key' => 'img_hash',
        'meta_value' => $hash,
        'fields' => 'ids',
        'numberposts' => 1
    ])) continue;

    $img_post = wp_insert_post([
        'post_type' => 'sfs_gallery_image',
        'post_title' => basename(parse_url($url, PHP_URL_PATH)),
        'post_status' => 'publish'
    ]);

    update_post_meta($img_post, 'parent_gallery_id', $gallery_id);
    update_post_meta($img_post, 'img_hash', $hash);

    sfs_import_image($url, $img_post);
}

}

function sfs_import_gallery_images_batch() {

    $galleries = get_posts([
        'post_type'      => 'sfs_gallery',
        'posts_per_page' => 1,
        'offset'         => (int) get_option('sfs_image_offset', 0),
        'fields'         => 'ids'
    ]);

    if (!$galleries) {
        return ['done' => true];
    }

    $gallery_id = $galleries[0];
    
    sfs_import_gallery_images($gallery_id);

    update_option(
        'sfs_image_offset',
        (int) get_option('sfs_image_offset', 0) + 1
    );

    return ['done' => false];
}

/* =========================
   ADMIN PAGE
========================= */
add_action('admin_menu', function () {
    add_menu_page(
        'SFS Gallery Import',
        'SFS Gallery Import',
        'manage_options',
        'sfs-gallery-import',
        'sfs_admin_page'
    );
});

function sfs_admin_page() {
    @set_time_limit(0);
    @ini_set('memory_limit', '1024M');

    if (isset($_POST['run_categories'])) {
        $res = sfs_import_batch();
        echo '<div class="updated"><p>';
        echo $res['done']
            ? '✅ Category import completed!'
            : '⏳ Category batch imported. Click again.';
        echo '</p></div>';
    }

    if (isset($_POST['run_images'])) {
        $res = sfs_import_gallery_images_batch();
        echo '<div class="updated"><p>';
        echo $res['done']
            ? '✅ All gallery images imported!'
            : '⏳ Gallery images batch imported. Click again.';
        echo '</p></div>';
    }

    if (isset($_POST['reset'])) {
        delete_option('sfs_import_offset');
        delete_option('sfs_import_total');
        delete_option('sfs_image_offset');
        delete_option('sfs_cached_categories');
        wp_clear_scheduled_hook('sfs_import_gallery_images_cron');
        wp_schedule_event(
            time() + 60,
            'five_minutes',
            'sfs_import_gallery_images_cron'
        );

        echo '<div class="updated"><p>Progress reset.</p></div>';
    }

    $cat_done  = (int) get_option('sfs_import_offset', 0);
    $cat_total = (int) get_option('sfs_import_total', 0);
    $img_done  = (int) get_option('sfs_image_offset', 0);

    ?>
    <div class="wrap">
        <h2>SFS Gallery Import</h2>

        <p><strong>Category Progress:</strong> <?= $cat_done ?> / <?= $cat_total ?></p>
        <p><strong>Gallery Image Progress:</strong> <?= $img_done ?></p>

        <form method="post">
            <p>
                <button name="run_categories" class="button button-primary">
                    Import Categories (Batch)
                </button>
            </p>

            <p>
                <button name="run_images" class="button button-secondary">
                    Import Gallery Images (Batch)
                </button>
            </p>

            <p>
                <button name="reset" class="button">
                    Reset Progress
                </button>
            </p>
        </form>
    </div>
    <?php
}

add_action('wp_ajax_sfs_gallery_list', 'sfs_gallery_list_ajax');
add_action('wp_ajax_nopriv_sfs_gallery_list', 'sfs_gallery_list_ajax');

function sfs_gallery_list_ajax() {

    $paged  = max(1, intval($_POST['page'] ?? 1));
    $search = sanitize_text_field($_POST['search'] ?? '');

    $q = new WP_Query([
        'post_type'      => 'sfs_gallery',
        'posts_per_page' => 12,
        'paged'          => $paged,
        's'              => $search,
        'post_status'    => 'publish',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ]);


    ob_start();

    if ($q->have_posts()) :
        while ($q->have_posts()) : $q->the_post(); ?>
            <div class="sfs-card col-md-4 col-sm-12 col-lg-4" style="width: 100% !important; min-width: 100% !important;">
                <a href="<?php the_permalink(); ?>" style="color: black;">
                    <?php the_post_thumbnail('full'); ?>
                    <p><?php the_title(); ?></p>
                </a>
            </div>
        <?php endwhile;
    else :
        echo '<p style="grid-column:1/-1;text-align:center;">No galleries found.</p>';
    endif;

    wp_reset_postdata();

    wp_send_json([
        'html'  => ob_get_clean(),
        'pages' => $q->max_num_pages,
        'page'  => $paged
    ]);
}

add_shortcode('sfs_gallery_list', function () {

    ob_start(); ?>
    <div class="custom-mrg">
    <input type="text" id="sfsGallerySearch" placeholder="Search galleries...">

    <div class="sfs-gallery-grid row" id="sfsGalleryGrid"></div>
    <div class="sfs-pagination" id="sfsGalleryPagination"></div>
    </div>
    <style>
    .custom-mrg{
        margin: 0 110px;
    }
    
    /* GRID */
    .sfs-gallery-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:24px;
        margin-top:20px;
    }
    
    /* CARD */
    .sfs-card{
        /*border-radius:10px;*/
        overflow:hidden;
        background:#fff;
        transition:.3s ease;
    }
    
    .sfs-card:hover{
        box-shadow:0 10px 30px rgba(0,0,0,.15);
    }
    
    /* IMAGE ZOOM EFFECT */
    .sfs-card img{
        width:100%;
        height:220px;
        object-fit:cover;
        /*border-radius:6px;*/
        transition:transform .4s ease;
    }
    
    .sfs-card:hover img{
        transform:scale(1.08);
    }
    
    /* TITLE */
    .sfs-card p{
        text-align:center;
        margin:12px 0 0;
        font-weight:600;
    }
    
    /* PAGINATION */
    .sfs-pagination{
        margin:40px 0;
        text-align:center;
    }
    
    .sfs-pagination a{
        display:inline-block;
        padding:8px 14px;
        margin:0 4px;
        background:#eee;
        color:#333;
        text-decoration:none;
        /*border-radius:6px;*/
        font-weight:500;
    }
    
    .sfs-pagination a:hover{
        background:#a41034;
        color:#fff;
    }
    
    .sfs-pagination .current{
        background:#9d2235;
        color:#fff;
    }
    </style>


    <script>
    let galleryPage = 1;
    
    function loadGallery(page = 1) {
        galleryPage = page;
    
        const fd = new FormData();
        fd.append('action', 'sfs_gallery_list');
        fd.append('search', document.getElementById('sfsGallerySearch').value);
        fd.append('page', page);
    
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(res => {
            document.getElementById('sfsGalleryGrid').innerHTML = res.html;
            renderGalleryPagination(res.pages, res.page);
        });
    }
    
    function renderGalleryPagination(pages, current) {
        let html = '';
    
        if (current > 1) {
            html += `<a href="#" onclick="event.preventDefault();loadGallery(${current - 1})">« Prev</a>`;
        }
    
        for (let i = 1; i <= pages; i++) {
            html += `<a href="#" class="${i === current ? 'current' : ''}"
                     onclick="event.preventDefault();loadGallery(${i})">${i}</a>`;
        }
    
        if (current < pages) {
            html += `<a href="#" onclick="event.preventDefault();loadGallery(${current + 1})">Next »</a>`;
        }
    
        document.getElementById('sfsGalleryPagination').innerHTML = html;
    }
    
    document.getElementById('sfsGallerySearch')
        .addEventListener('keyup', () => loadGallery(1));
    
    loadGallery();
    </script>


    <?php
    return ob_get_clean();
});

// Add "Re-fetch Images" link in gallery list
add_filter('post_row_actions', function ($actions, $post) {

    if ($post->post_type !== 'sfs_gallery') {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url('admin.php?action=sfs_refetch_gallery_images&gallery_id=' . $post->ID),
        'sfs_refetch_' . $post->ID
    );

    $actions['sfs_refetch'] =
        '<a href="' . esc_url($url) . '" style="color:#b32d2e;font-weight:600;">Re-fetch Images</a>';

    return $actions;

}, 10, 2);

// Handle re-fetch gallery images action
add_action('admin_action_sfs_refetch_gallery_images', function () {

    if (!isset($_GET['gallery_id'])) {
        wp_die('Missing gallery ID');
    }

    $gallery_id = (int) $_GET['gallery_id'];

    if (!wp_verify_nonce($_GET['_wpnonce'], 'sfs_refetch_' . $gallery_id)) {
        wp_die('Security check failed');
    }

    // 1️⃣ Delete existing images of this gallery
    $images = get_posts([
        'post_type'  => 'sfs_gallery_image',
        'numberposts'=> -1,
        'meta_key'   => 'parent_gallery_id',
        'meta_value' => $gallery_id,
        'fields'     => 'ids'
    ]);

    foreach ($images as $img_id) {
        $thumb = get_post_thumbnail_id($img_id);
        if ($thumb) {
            wp_delete_attachment($thumb, true);
        }
        wp_delete_post($img_id, true);
    }

    // 2️⃣ Reset progress for this gallery
    delete_post_meta($gallery_id, 'img_offset');
    delete_post_meta($gallery_id, 'images_done');

    // 3️⃣ Re-import images
    sfs_import_gallery_images($gallery_id);

    // 4️⃣ Redirect back with success message
    wp_redirect(
        add_query_arg(
            'sfs_refetch_done',
            1,
            admin_url('edit.php?post_type=sfs_gallery')
        )
    );
    exit;
});

add_action('admin_notices', function () {
    if (isset($_GET['sfs_refetch_done'])) {
        echo '<div class="updated notice is-dismissible">
                <p>✅ Gallery images re-fetched successfully.</p>
              </div>';
    }
});


function sfs_get_cached_categories() {

    $cached = get_option('sfs_cached_categories');
    if ($cached && is_array($cached)) {
        return $cached;
    }

    // build once
    $res = sfs_api_call();
    if (empty($res['d'][0]['_data'])) return [];

    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML(mb_convert_encoding($res['d'][0]['_data'], 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xp = new DOMXPath($dom);
    $links = $xp->query("//a[contains(@href,'photo-gallery.php?sz=')]");

    $unique = [];

    foreach ($links as $a) {

        $href = html_entity_decode($a->getAttribute('href'));

        if (!preg_match('/sz=([^&]+)/', $href, $szm)) continue;
        if (!preg_match('/vz=([^&]+)/', $href, $vzm)) continue;

        $sz = urldecode($szm[1]);
        $vz = urldecode($vzm[1]);

        if (!$sz || !$vz) continue;

        $img = (new DOMXPath($a->ownerDocument))->query(".//img", $a)->item(0);

        $unique[md5($sz.'|'.$vz)] = [
            'sz'    => $sz,
            'vz'    => $vz,
            'title' => $img ? trim($img->getAttribute('alt')) : 'Gallery',
            'img'   => $img ? $img->getAttribute('src') : ''
        ];
    }

    unset($dom, $xp);
    gc_collect_cycles();

    update_option('sfs_cached_categories', array_values($unique));

    return array_values($unique);
}


add_action('init', function () {

    if (!wp_next_scheduled('sfs_import_gallery_images_cron')) {
        wp_schedule_event(
            time() + 60,        // start after 1 minute
            'five_minutes',
            'sfs_import_gallery_images_cron'
        );
    }

});


add_action('sfs_import_gallery_images_cron', function () {

    $result = sfs_import_gallery_images_batch();

    // ✅ Stop cron automatically when done
    if (!empty($result['done']) && $result['done'] === true) {
        wp_clear_scheduled_hook('sfs_import_gallery_images_cron');
        error_log('SFS Gallery Import: All gallery images imported. Cron stopped.');
    }

});

