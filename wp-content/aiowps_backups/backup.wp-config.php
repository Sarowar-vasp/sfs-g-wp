<?php

if (isset($_SERVER['HTTP_HOST'])) {
    define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST']);
}
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '300');

// define('FS_METHOD', 'direct');
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sfsguwahati_09022026' );

/** Database username */
define( 'DB_USER', 'wp_user' );

/** Database password */
define( 'DB_PASSWORD', 'SFS@2026Guwahati!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '0iQ3:B^LQ-Vp.=I:E/<i8{<q/l-.&#swAB/}Ss]TlZ0N}~B4b$S8~MkZ{N<M(P_C' );
define( 'SECURE_AUTH_KEY',  '(>z{vtU5cQJ4|?I~fs!rOcx;X-?Qv9AD<1Hn#~g3RMBW;y65Y$HUnb[5j]G1n/NS' );
define( 'LOGGED_IN_KEY',    '0nE^El;aWq(K]U_SoG @uWQ,@Oa1W=p.U]oN&E]YTsp)(ln|cdKS>cUA#yuEK9n ' );
define( 'NONCE_KEY',        '*rPysuwn-8>8[3^c3d=VElOtE?0*6b^UKJ5=_JFX)33G>,c -r.*hs%B6N>r}z k' );
define( 'AUTH_SALT',        '7d+@iqf%qG in/Z`+_Gi%Lcwfk$O*>/ynr$ez~[PLba!K$?vN:^8YZcz~6WK:~^I' );
define( 'SECURE_AUTH_SALT', 'Z7G(8eMl7]j.qBZ4#Rn{7hL2]nCZpf>O_k,Jr?a==@7:1`?=d1za5saS[9-Adb>0' );
define( 'LOGGED_IN_SALT',   'L|s/Quq~ZE^ B#Rz;k8hBWVl/a@UtF5@ge2FIwrR|88(AigB,D1!<jU[&}R0g4CT' );
define( 'NONCE_SALT',       '!M k1H58h^9f|-x;D^BthW{5cB02gUjNbY[f4F3<k0f>Rpw?eS$*?sDob-f4A2gY' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define('DISABLE_WP_CRON', true);
define('ALLOW_UNFILTERED_UPLOADS', true);
define('DISALLOW_UNFILTERED_HTML', false);

// define('WP_DEBUG', true);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
