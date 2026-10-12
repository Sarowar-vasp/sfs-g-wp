<?php
/**
 * Plugin Name: Playlist Player for YouTube
 * Plugin URI: https://getbutterfly.com/wordpress-plugins/
 * Description: Display a YouTube player (with an optional playlist) on any post or page using a simple shortcode.
 * Version: 4.8.3
 * Author: Ciprian Popescu
 * Author URI: https://getbutterfly.com/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: youtube-playlist-player
 *
 * Playlist Player for YouTube
 * Copyright (C) 2013-2026 Ciprian Popescu (getbutterfly@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require 'includes/functions.php';
require 'includes/settings.php';

/**
 * Register/enqueue plugin scripts and styles (front-end)
 */
function ytpp_pss() {
    wp_register_style( 'ytpp', plugins_url( 'css/style.min.css', __FILE__ ), [], '4.8.3' );

    wp_register_script( 'ytpp', plugins_url( 'js/ytpp-main.min.js', __FILE__ ), [], '4.8.3', true );

    if ( (int) get_option( 'ytpp_iframe_fix' ) === 1 ) {
        wp_register_script( 'ytpp-fluid-vids', plugins_url( 'js/ytpp-fluid-vids.min.js', __FILE__ ), [], '4.8.3', true );
    }
}

/**
 * Install/uninstall plugin
 */
register_activation_hook( __FILE__, 'ytpp_install' );
register_uninstall_hook( __FILE__, 'ytpp_uninstall' );

/**
 * Initialise plugin
 */
add_action( 'admin_menu', 'ytpp_admin' );
add_action( 'wp_enqueue_scripts', 'ytpp_pss' );

/**
 * Add plugin shortcodes
 */
add_shortcode( 'yt_playlist', 'ytpp_player_show' );
add_shortcode( 'yt_playlist_v3', 'ytpp_apiplayer_show' );
add_shortcode( 'yt_feed', 'ytpp_feed_youtube' );
