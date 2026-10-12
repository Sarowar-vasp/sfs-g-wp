<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPMB_Importer {

	public function get_posts_from_bundle( $zip_path ) {
		if ( ! class_exists( 'ZipArchive' ) ) return new WP_Error( 'zip_missing', 'ZipArchive missing.' );

		$temp_dir = wp_upload_dir()['basedir'] . '/wpmb-extract-' . time();
		wp_mkdir_p( $temp_dir );

		$zip = new ZipArchive();
		if ( $zip->open( $zip_path ) === TRUE ) {
			$zip->extractTo( $temp_dir );
			$zip->close();
		} else {
			return new WP_Error( 'zip_open_error', 'Failed to open ZIP.' );
		}

		$json_file = $temp_dir . '/data.json';
		if ( ! file_exists( $json_file ) ) {
			return new WP_Error( 'json_missing', 'data.json not found in bundle.' );
		}

		$posts_data = json_decode( file_get_contents( $json_file ), true );
		
		return array(
			'posts'    => $posts_data,
			'temp_dir' => $temp_dir,
			'count'    => count( $posts_data )
		);
	}

	public function import_single_item( $data, $temp_dir ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// Check if post already exists by title
		$existing_post = get_page_by_title( $data['post_title'], OBJECT, 'post' );
		
		// Match author
		$author_name = isset($data['author_name']) ? $data['author_name'] : 'admin';
		$author = get_user_by( 'login', $author_name );
		$author_id = ( $author ) ? $author->ID : get_current_user_id();

		$post_arr = array(
			'post_title'   => $data['post_title'],
			'post_content' => $data['post_content'],
			'post_excerpt' => $data['post_excerpt'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_date'    => isset($data['post_date']) ? $data['post_date'] : '',
			'post_date_gmt' => isset($data['post_date_gmt']) ? $data['post_date_gmt'] : '',
			'post_author'  => $author_id,
		);

		if ( $existing_post ) {
			$post_arr['ID'] = $existing_post->ID;
			$post_id = wp_update_post( $post_arr );
			$this->log( "Updating existing: " . $data['post_title'] );
		} else {
			$post_id = wp_insert_post( $post_arr );
			$this->log( "Creating new: " . $data['post_title'] );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) return false;

		// Handle Image
		if ( ! empty( $data['featured_image'] ) && ! has_post_thumbnail( $post_id ) ) {
			$img_path = $temp_dir . '/images/' . $data['featured_image'];
			if ( file_exists( $img_path ) ) {
				$file_array = array(
					'name'     => basename( $img_path ),
					'tmp_name' => $this->copy_to_tmp( $img_path ),
				);
				$thumb_id = media_handle_sideload( $file_array, $post_id );
				if ( ! is_wp_error( $thumb_id ) ) {
					set_post_thumbnail( $post_id, $thumb_id );
					$this->log( "Attached image to " . $post_id );
				}
			}
		}

		return true;
	}

	public function cleanup_temp( $dir ) {
		$this->recursive_rmdir( $dir );
	}

	public function import_bundle( $zip_tmp_path ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'zip_missing', 'ZipArchive class not found.' );
		}

		$temp_dir = wp_upload_dir()['basedir'] . '/wpmb-import-' . time();
		wp_mkdir_p( $temp_dir );

		$zip = new ZipArchive();
		if ( $zip->open( $zip_tmp_path ) === TRUE ) {
			$zip->extractTo( $temp_dir ); 
			$zip->close();
		} else {
			return new WP_Error( 'zip_open_error', 'Failed to open the ZIP bundle.' );
		}

		$json_file = $temp_dir . '/data.json';
		if ( ! file_exists( $json_file ) ) {
			return new WP_Error( 'json_missing', 'Invalid bundle: data.json not found.' );
		}

		$posts_data = json_decode( file_get_contents( $json_file ), true );
		if ( empty( $posts_data ) ) {
			return new WP_Error( 'json_empty', 'Bundle contains no post data.' );
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		foreach ( $posts_data as $data ) {
			// Check if post already exists by title
			$existing_post = get_page_by_title( $data['post_title'], OBJECT, 'post' );
			
			// Match author
			$author = get_user_by( 'login', $data['author_name'] );
			$author_id = ( $author ) ? $author->ID : get_current_user_id();

			$post_id = 0;
			$post_arr = array(
				'post_title'   => $data['post_title'],
				'post_content' => $data['post_content'],
				'post_excerpt' => $data['post_excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_date'    => isset($data['post_date']) ? $data['post_date'] : '',
				'post_date_gmt' => isset($data['post_date_gmt']) ? $data['post_date_gmt'] : '',
				'post_author'  => $author_id,
			);

			if ( $existing_post ) {
				// Update existing post
				$post_arr['ID'] = $existing_post->ID;
				$post_id = wp_update_post( $post_arr );
				$this->log( "Updating existing post: " . $data['post_title'] );
			} else {
				// Create new post
				$post_arr['post_author'] = get_current_user_id();
				$post_id = wp_insert_post( $post_arr );
				$this->log( "Creating new post: " . $data['post_title'] );
			}

			if ( is_wp_error( $post_id ) || 0 === $post_id ) {
				continue;
			}

			// Handle Featured Image - Only if post doesn't already have one
			if ( ! empty( $data['featured_image'] ) && ! has_post_thumbnail( $post_id ) ) {
				$img_path = $temp_dir . '/images/' . $data['featured_image'];
				if ( file_exists( $img_path ) ) {
					$file_array = array(
						'name'     => basename( $img_path ),
						'tmp_name' => $this->copy_to_tmp( $img_path ),
					);

					$thumb_id = media_handle_sideload( $file_array, $post_id );
					if ( ! is_wp_error( $thumb_id ) ) {
						set_post_thumbnail( $post_id, $thumb_id );
						$this->log( "Added missing featured image to post ID: " . $post_id );
					}
				}
			}

			// Handle Categories
			if ( ! empty( $data['categories'] ) ) {
				wp_set_object_terms( $post_id, $data['categories'], 'category' );
			}

			// Handle Tags
			if ( ! empty( $data['tags'] ) ) {
				wp_set_object_terms( $post_id, $data['tags'], 'post_tag' );
			}

			// Handle Meta
			if ( ! empty( $data['meta'] ) ) {
				foreach ( $data['meta'] as $key => $values ) {
					// Skip internal WP meta keys if needed, or just import all
					if ( is_array( $values ) ) {
						foreach ( $values as $value ) {
							add_post_meta( $post_id, $key, $value );
						}
					}
				}
			}
		}

		// Cleanup
		$this->recursive_rmdir( $temp_dir );

		return true;
	}

	private function copy_to_tmp( $path ) {
		$tmp_file = wp_tempnam( $path );
		copy( $path, $tmp_file );
		return $tmp_file;
	}

	private function log( $message ) {
		$log_file = WP_CONTENT_DIR . '/wpmb-debug.log';
		$timestamp = date( "Y-m-d H:i:s" );
		file_put_contents( $log_file, "[$timestamp] [Import] $message\n", FILE_APPEND );
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
