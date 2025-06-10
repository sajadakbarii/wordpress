<?php
define( 'WP_CACHE', true );

 // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "shoptamland_db" );

/** Database username */
define( 'DB_USER', "shoptamland_user" );

/** Database password */
define( 'DB_PASSWORD', "xisxrS_td+I3" );

/** Database hostname */
define( 'DB_HOST', "localhost" );

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
define( 'AUTH_KEY',         'A22DawCYPEubqD2W9NuPsjT5lXuVDcvPdCCJVge36zJgX3TmQAw5nsP2dS9hudRy' );
define( 'SECURE_AUTH_KEY',  'TZyshH29jXj4CsXnSkTVkFPzsjpxT8V8aB4BcMMHrOfAZeyvpsviFBWBGy8spham' );
define( 'LOGGED_IN_KEY',    'ICUlonYos8i8Yzi452pU9XXtrcWXY2M7bZDg1DWC99gCBMuM4NdtlCvRf4GEtJI2' );
define( 'NONCE_KEY',        'OkYWsIrPhOVSqR575JDfF0BnIYm4y1Svs7a30DjiyiNe464V5TqzrQPiQy0AxSFx' );
define( 'AUTH_SALT',        'RPgr4MeaoQCwVTOLgTGzxLYi8FalCSryBrmbGd5WkDt3oPqg7IWAcSbGnUpscejA' );
define( 'SECURE_AUTH_SALT', 'gcXZPtcuC7t49mu0wBk8tO1A4HsEdq0BmOnzPqQex5ew1mD7tLnQSUhESdArfc36' );
define( 'LOGGED_IN_SALT',   'IuQ6xdYvk3Rip4m65LWHVEivPBuAh9eMYJDvGHanTJP0HCNJN3WYOw0EkC4NTaTp' );
define( 'NONCE_SALT',       'b502RUBZoC56OXYGEjpOPplUrhkzizDuLAArgRvMbjh4t0ME3YqnuTFFaCiDhIKf' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', true );
/* Add any custom values between this line and the "stop editing" line. */

//define( 'WP_MEMORY_LIMIT', '8192M' );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

