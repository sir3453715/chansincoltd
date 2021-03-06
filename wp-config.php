<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'i7174157_wp1' );

/** MySQL database username */
define( 'DB_USER', 'i7174157_wp1' );

/** MySQL database password */
define( 'DB_PASSWORD', 'M.byAErFI4ySn8xPI1U95' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'cLz9xNcrlvteWVsS2zZqywLKBJIzrMegaDtB3exsdbvScwBWry1vWNnr8j3qcP8a');
define('SECURE_AUTH_KEY',  'VSFBTkxvVEu1GkMo3qOVrKBUzaM5rsGNBI9Q9VCFcioZC0npNNEziHfKhC5wMerd');
define('LOGGED_IN_KEY',    'f8xQku0pp8iN8GnY0tWh3qm5TIHjMe8J32Ogf9sD1iBC5ffD1yVoI9BtAqFpbRbl');
define('NONCE_KEY',        'PnTWW3UrYmKZ3W8fO1YMaty5IID9za5oouzyW5NluYnSLmMnqhiMSIp6GQDbofBK');
define('AUTH_SALT',        'tbmUQcpsSoqBTAbkSs4cbDm4DehWG4VMyKTxnqrmEsocT4XTE1StCQ3d8c0sz2cx');
define('SECURE_AUTH_SALT', 'EBVoXwMHZ5OTKDG5eJEWjdshr8lmzZYMlyUsrxQQ2IBJ6iyeTBPjoRibfBMqf21v');
define('LOGGED_IN_SALT',   'JCD37RiTWwFWVKVdNCN6PQhbarvPEFAMld6cJBjSM2KiuI3BuRQvijQkRichw37H');
define('NONCE_SALT',       'nKKi008b9MBxiLsvp21zFOUVWr7nGWPt00eVswcwpkjRovDCWxhmbpIY2W0QFfIm');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed externally by Installatron.
 * If you remove this define() to re-enable WordPress's automatic background updating
 * then it's advised to disable auto-updating in Installatron.
 */
define('AUTOMATIC_UPDATER_DISABLED', false);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */

/**
 * 開發人員用： WordPress 偵錯模式。
 */
/** 開啟WordPress偵錯功能 */
define('WP_DEBUG', true);
/** 產生錯誤記錄檔，產生於wp-content/debug.log */
define('WP_DEBUG_LOG', true );
/** 顯示錯誤訊息於html上 */
define('WP_DEBUG_DISPLAY', false );
/** 腳本偵錯功能，如設為true，則載入非minified的js */
define('SCRIPT_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
