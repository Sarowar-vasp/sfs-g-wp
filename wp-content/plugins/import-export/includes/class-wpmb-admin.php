<?php

if (! defined('ABSPATH')) {
	exit;
}

class WPMB_Admin
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_menu_page'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
		add_action('wp_ajax_wpmb_export', array($this, 'handle_export'));
		add_action('admin_post_wpmb_import', array($this, 'handle_import'));

		// Progressive AJAX Import
		add_action('wp_ajax_wpmb_get_import_count', array($this, 'ajax_get_import_count'));
		add_action('wp_ajax_wpmb_process_import_item', array($this, 'ajax_process_import_item'));
	}

	public function add_menu_page()
	{
		add_menu_page(
			'WP Post Bundle',
			'Post Bundle',
			'manage_options',
			'wp-post-bundle',
			array($this, 'render_admin_page'),
			'dashicons-archive',
			30
		);
	}

	public function enqueue_assets($hook)
	{
		if ('toplevel_page_wp-post-bundle' !== $hook) {
			return;
		}

		wp_enqueue_style('wpmb-admin-style', WPMB_URL . 'assets/admin.css', array(), '1.0.0');
		wp_enqueue_script('wpmb-admin-js', WPMB_URL . 'assets/admin.js', array('jquery'), '1.0.0', true);

		wp_localize_script('wpmb-admin-js', 'wpmb_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('wpmb_nonce'),
		));
	}

	public function render_admin_page()
	{
?>
		<div class="wrap wpmb-dashboard">
			<div class="wpmb-header">
				<h1>Post & Media Bundle</h1>
				<p>High-performance post migration for WordPress.</p>
			</div>

			<div class="wpmb-main-layout">
				<!-- Full Width Local Migration -->
				<div class="wpmb-card local-card hero-style">
					<div class="hero-header">
						<div class="badge">Performance Import</div>
						<h2>Smart Local Migration</h2>
						<p>Safely handle 90MB+ bundles directly from the <code>/imports/</code> folder.</p>
					</div>

					<div class="hero-body">
						<div class="hero-left">
							<div class="info-group">
								<label>Source Folder</label>
								<code class="path-display">/wp-content/plugins/import-export/imports/</code>
							</div>
							
							<div class="local-files-container">
								<label>Detected Bundles</label>
								<div class="local-files-list">
									<?php
									$import_dir = WPMB_PATH . 'imports/';
									if ( ! is_dir( $import_dir ) ) wp_mkdir_p( $import_dir );
									$files = glob( $import_dir . '*.zip' );
									if ( $files ) :
										foreach ( $files as $file ) :
											$filename = basename( $file );
											echo '<div class="local-file-item"><span class="dashicons dashicons-archive"></span> ' . esc_html( $filename ) . '</div>';
										endforeach;
									else :
										echo '<p class="no-files">No ZIP files found.</p>';
									endif;
									?>
								</div>
							</div>
						</div>

						<div class="hero-right">
							<div class="action-panel">
								<button type="button" id="wpmb-local-import-btn" class="button button-primary button-hero" <?php echo empty($files) ? 'disabled' : ''; ?>>
									Start Smart Import
								</button>
								
								<div id="local-import-progress" class="progress-box">
									<div class="progress-status">
										<span class="status-text">Ready</span>
										<span class="status-percent">0%</span>
									</div>
									<div class="progress-track">
										<div class="progress-bar" style="width: 0%"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Side by Side Export and Import -->
				<div class="wpmb-secondary-grid">
					<div class="wpmb-card export-card">
						<div class="card-icon">
							<span class="dashicons dashicons-upload"></span>
						</div>
						<h2>Export Posts</h2>
						<p>Package your posts, metadata, and images into a single ZIP file.</p>
						
						<div class="export-options">
							<div class="input-row">
								<div class="input-group">
									<label>Number of posts:</label>
									<input type="number" id="post-count" value="10" min="1">
								</div>
								<div class="input-group">
									<label>Offset:</label>
									<input type="number" id="post-offset" value="0" min="0">
								</div>
							</div>
							<div class="checkbox-group">
								<label><input type="checkbox" id="include-meta" checked> Include Metadata & Taxonomies</label>
							</div>
						</div>
						<button id="wpmb-export-btn" class="button button-primary button-hero">Generate Export Bundle</button>
						<div id="export-message"></div>
					</div>

					<div class="wpmb-card import-card">
						<div class="card-icon">
							<span class="dashicons dashicons-download"></span>
						</div>
						<h2>Import Bundle</h2>
						<p>Upload a ZIP file (subject to server size limits).</p>
						
						<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="wpmb_import"> 
							<?php wp_nonce_field( 'wpmb_import_nonce' ); ?>
							<div class="file-upload">
								<input type="file" name="wpmb_bundle" id="wpmb-bundle-file" accept=".zip" required>
							</div>
							<button type="submit" class="button button-secondary button-hero">Upload and Import</button>
						</form>
					</div>
				</div>
			</div>

			<div id="wpmb-loader" class="wpmb-hidden">
				<div class="loader-content">
					<div class="spinner-modern"></div>
					<p>Processing bundle... Please wait.</p>
				</div>
			</div>
		</div>
<?php
	}

	public function handle_export()
	{
		check_ajax_referer('wpmb_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error('Unauthorized');
		}

		$post_count = isset($_POST['post_count']) ? intval($_POST['post_count']) : 10;
		$post_offset = isset($_POST['post_offset']) ? intval($_POST['post_offset']) : 0;
		$include_meta = isset($_POST['include_meta']) && $_POST['include_meta'] === 'true';

		$exporter = new WPMB_Exporter();
		$result = $exporter->generate_bundle($post_count, $post_offset, $include_meta);

		if (is_wp_error($result)) {
			wp_send_json_error($result->get_error_message());
		}

		wp_send_json_success($result);
	}

	public function handle_import()
	{
		if (! current_user_can('manage_options')) {
			wp_die('Unauthorized');
		}

		check_admin_referer('wpmb_import_nonce');

		if (empty($_FILES['wpmb_bundle']['tmp_name'])) {
			wp_redirect(admin_url('admin.php?page=wp-post-bundle&error=no_file'));
			exit;
		}

		$importer = new WPMB_Importer();
		$result = $importer->import_bundle($_FILES['wpmb_bundle']['tmp_name']);

		if (is_wp_error($result)) {
			wp_die($result->get_error_message());
		}

		wp_redirect(admin_url('admin.php?page=wp-post-bundle&success=imported'));
		exit;
	}

	public function ajax_get_import_count()
	{
		check_ajax_referer('wpmb_nonce', 'nonce');

		$import_dir = WPMB_PATH . 'imports/';
		$files = glob($import_dir . '*.zip');

		if (empty($files)) {
			wp_send_json_error('No ZIP files found in imports folder.');
		}

		$importer = new WPMB_Importer();
		$bundle = $importer->get_posts_from_bundle($files[0]);

		if (is_wp_error($bundle)) {
			wp_send_json_error($bundle->get_error_message());
		}

		// Store data in a transient for the next steps (valid for 1 hour)
		$session_id = uniqid();
		set_transient('wpmb_import_' . $session_id, $bundle, HOUR_IN_SECONDS);

		wp_send_json_success(array(
			'count'      => $bundle['count'],
			'session_id' => $session_id,
			'filename'   => basename($files[0])
		));
	}

	public function ajax_process_import_item()
	{
		check_ajax_referer('wpmb_nonce', 'nonce');

		$session_id = $_POST['session_id'];
		$index = intval($_POST['index']);
		$bundle = get_transient('wpmb_import_' . $session_id);

		if (! $bundle) {
			wp_send_json_error('Session expired.');
		}

		if (! isset($bundle['posts'][$index])) {
			wp_send_json_error('Index out of bounds.');
		}

		$importer = new WPMB_Importer();
		$result = $importer->import_single_item($bundle['posts'][$index], $bundle['temp_dir']);

		// If this was the last item, cleanup
		if ($index === $bundle['count'] - 1) {
			$importer->cleanup_temp($bundle['temp_dir']);
			delete_transient('wpmb_import_' . $session_id);
		}

		if ($result) {
			wp_send_json_success('Item imported.');
		} else {
			wp_send_json_error('Failed to import item.');
		}
	}
}
