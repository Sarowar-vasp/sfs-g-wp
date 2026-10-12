<?php
get_header();

while (have_posts()) : the_post();

/* ===============================
   PAGINATION SETUP
================================ */
$paged = max(1, get_query_var('paged'));

$query = new WP_Query([
    'post_type'      => 'sfs_gallery_image',
    'posts_per_page' => 16, // ✅ load limited images
    'paged'          => $paged,
    'meta_key'       => 'parent_gallery_id',
    'meta_value'     => get_the_ID(),
    'orderby'        => 'ID', // insertion order
    'order'          => 'ASC'
]);
?>

<style>
/* ====== WRAPPER ====== */
.sfs-gallery-wrapper{
    max-width:1110px;
    margin:40px auto;
    padding:0 15px;
}

/* ====== TITLE ====== */
.sfs-gallery-title{
    text-align:center;
    margin-bottom:25px;
}

/* ====== FEATURED IMAGE ====== */
.sfs-gallery-featured img{
    width:100%;
    height:420px;
    object-fit:cover;
}

/* ====== GRID ====== */
.sfs-gallery-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-top:30px;
}

.sfs-gallery-grid img{
    width:100%;
    height:260px;
    object-fit:cover;
    cursor:pointer;
    transition:.3s ease;
}

.sfs-gallery-grid img:hover{
    transform:scale(1.05);
}

/* ====== PAGINATION ====== */
.sfs-pagination{
    margin:40px 0;
    text-align:center;
}

.sfs-pagination .page-numbers{
    display:inline-block;
    padding:8px 14px;
    margin:0 4px;
    background:#eee;
    text-decoration:none;
    color:#333;
}

.sfs-pagination .current{
    background:#9d2235;
    color:#fff;
}

/* ====== LIGHTBOX ====== */
.sfs-lightbox{
    display:none;
    position:fixed;
    inset:0;
    z-index:9999;
    background:rgba(0,0,0,.85);
    align-items:center;
    justify-content:center;
}

.sfs-lightbox img{
    max-width:90%;
    max-height:90%;
}

.sfs-lightbox-close{
    position:absolute;
    top:20px;
    right:30px;
    font-size:32px;
    color:#fff;
    cursor:pointer;
}

/* ====== RESPONSIVE ====== */
@media(max-width:1024px){
    .sfs-gallery-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:600px){
    .sfs-gallery-grid{grid-template-columns:1fr;}
    .sfs-gallery-featured img{height:260px;}
}
</style>

<div class="sfs-gallery-wrapper">

    <!-- TITLE -->
    <h1 class="sfs-gallery-title"><?php the_title(); ?></h1>

    <!-- FEATURED IMAGE -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="sfs-gallery-featured">
            <?php the_post_thumbnail('medium_large'); ?>
        </div>
    <?php endif; ?>

    <!-- IMAGES -->
    <div class="sfs-gallery-grid">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
                $full  = get_the_post_thumbnail_url(get_the_ID(), 'full');
                ?>
                <img
                    src="<?php echo esc_url($thumb); ?>"
                    data-full="<?php echo esc_url($full); ?>"
                    loading="lazy"
                    onclick="openLightbox(this.dataset.full)"
                >
            <?php endwhile; ?>
        <?php else : ?>
            <p>No images found.</p>
        <?php endif; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($query->max_num_pages > 1) : ?>
        <div class="sfs-pagination">
            <?php
            echo paginate_links([
                'total'   => $query->max_num_pages,
                'current' => $paged,
            ]);
            ?>
        </div>
    <?php endif; ?>

</div>

<!-- LIGHTBOX -->
<div class="sfs-lightbox" id="sfsLightbox" onclick="closeLightbox()">
    <span class="sfs-lightbox-close">&times;</span>
    <img id="sfsLightboxImg">
</div>

<script>
function openLightbox(src){
    document.getElementById('sfsLightboxImg').src = src;
    document.getElementById('sfsLightbox').style.display = 'flex';
}
function closeLightbox(){
    document.getElementById('sfsLightbox').style.display = 'none';
}
</script>

<?php
wp_reset_postdata();
endwhile;
get_footer();
