<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPMB_Exporter {

	public function generate_bundle( $count, $offset, $include_meta ) {
		$this->log( "--- Starting Export (Count: $count, Offset: $offset) ---" );
		try {
			if ( ! class_exists( 'ZipArchive' ) ) {
				$this->log( "Error: ZipArchive class missing" );
				return new WP_Error( 'zip_missing', 'ZipArchive class is not enabled on this server.' );
			}

			$posts = get_posts( array(
				'numberposts' => $count,
				'offset'      => $offset,
				'post_status' => 'publish',
				'post_type'   => 'post',
				'orderby'     => 'date',
				'order'       => 'DESC',
			) );

			$this->log( "Found " . count( $posts ) . " posts" );
			if ( ! empty( $posts ) ) {
				$this->log( "First post in this batch: " . $posts[0]->post_title );
			}

			if ( empty( $posts ) ) {
				return new WP_Error( 'no_posts', 'No posts found to export.' );
			}

			$upload_dir = wp_upload_dir();
			$temp_dir = wp_normalize_path( $upload_dir['basedir'] . '/wpmb-temp-' . time() );
			
			$this->log( "Creating temp dir: " . $temp_dir );
			if ( ! wp_mkdir_p( $temp_dir ) ) {
				$this->log( "Failed to create temp dir" );
				return new WP_Error( 'dir_error', 'Could not create temporary directory.' );
			}

			$images_dir = wp_normalize_path( $temp_dir . '/images' );
			wp_mkdir_p( $images_dir );

			$bundle_data = array();

			foreach ( $posts as $post ) {
				$this->log( "Processing post ID: " . $post->ID );
				$author_data = get_userdata( $post->post_author );
				$post_data = array( 
					'ID'           => $post->ID,
					'post_title'   => $post->post_title,
					'post_content' => $post->post_content,
					'post_excerpt' => $post->post_excerpt,
					'post_name'    => $post->post_name,
					'post_type'    => $post->post_type,
					'post_status'  => $post->post_status,
					'post_date'    => $post->post_date,
					'post_date_gmt' => $post->post_date_gmt,
					'author_name'  => ( $author_data ) ? $author_data->user_login : 'admin',
				);

				$thumb_id = get_post_thumbnail_id( $post->ID );
				if ( $thumb_id ) {
					$image_path = wp_normalize_path( get_attached_file( $thumb_id ) );
					if ( $image_path && file_exists( $image_path ) ) {
						$filename = basename( $image_path );
						$this->log( "Copying image: " . $filename );
						copy( $image_path, $images_dir . '/' . $filename );
						$post_data['featured_image'] = $filename;
					}
				}

				if ( $include_meta ) {
					$post_data['meta'] = get_post_meta( $post->ID );
					$post_data['categories'] = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
					$post_data['tags'] = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );
				}

				$bundle_data[] = $post_data;
			}

			$this->log( "Saving JSON data" );
			file_put_contents( $temp_dir . '/data.json', json_encode( $bundle_data ) );

			$zip_name = 'post-bundle-' . date( 'Y-m-d-H-i-s' ) . '.zip';
			$zip_path = wp_normalize_path( $upload_dir['basedir'] . '/' . $zip_name );
			$this->log( "Creating ZIP at: " . $zip_path );
			
			$zip = new ZipArchive();
			if ( $zip->open( $zip_path, ZipArchive::CREATE ) === TRUE ) {
				$zip->addFile( $temp_dir . '/data.json', 'data.json' );
				
				$this->log( "Adding images to ZIP" );
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator( $images_dir ),
					RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ( $files as $name => $file ) {
					if ( ! $file->isDir() ) {
						$filePath = wp_normalize_path( $file->getRealPath() );
						$relativePath = 'images/' . basename( $filePath );
						$zip->addFile( $filePath, $relativePath );
					}
				}
				$zip->close();
				$this->log( "ZIP closed successfully" );
			} else {
				$this->log( "Failed to open ZIP" );
				return new WP_Error( 'zip_error', 'Could not create ZIP archive.' );
			}

			$this->log( "Cleaning up temp dir" );
			$this->recursive_rmdir( $temp_dir );

			$this->log( "Export Complete" );
			return array(
				'download_url' => $upload_dir['baseurl'] . '/' . $zip_name,
				'filename'     => $zip_name,
			);
		} catch ( Exception $e ) {
			$this->log( "Exception: " . $e->getMessage() );
			return new WP_Error( 'exception', $e->getMessage() );
		}
	}

	private function log( $message ) {
		$log_file = WP_CONTENT_DIR . '/wpmb-debug.log';
		$timestamp = date( "Y-m-d H:i:s" );
		file_put_contents( $log_file, "[$timestamp] $message\n", FILE_APPEND );
	}

	private function recursive_rmdir( $dir ) {
		if ( is_dir( $dir ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( $object != "." && $object != ".." ) {
					if ( is_dir( $dir . DIRECTORY_SEPARATOR . $object ) && ! is_link( $dir . "/" . $object ) ) {
						$this->recursive_rmdir( $dir . DIRECTORY_SEPARATOR . $object );
					} else {
						unlink( $dir . DIRECTORY_SEPARATOR . $object );
					}
				}
			}
			rmdir( $dir );
		}
	}
}
