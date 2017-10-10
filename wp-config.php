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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'R0tNbp6GAXfqpQ512ssWpUPoQDysLlM53RtRQNA1RCKLkg3qf0Vxtj36FrS7skY3QM5VXNsA7l/D5bfzld5Gfw==');
define('SECURE_AUTH_KEY',  'Cx61YoOdLY83lgjdIK+EGTS3bA6Vm4Z/Rv2RHYRSDHLn5GJrURkkBfwEbF08KJQMYhzm5ARrbc9/cUy4Zvt1Cg==');
define('LOGGED_IN_KEY',    'a3R/P8fbk0F93VoGgmDGX5g2QDt5ONYS0nvPfokFS31ZcO/HrzCLp2CkSlhc9SqVi13dbFeid/8kBiCFb1voZQ==');
define('NONCE_KEY',        'Dmeom6pzuaBzRVbx/HF4qHo7OptlsB/9zqB/0PvfmRsmbS5xUlHyRsmL+UQL4uqhm3pbCcVmyYa8yCITI4HI4g==');
define('AUTH_SALT',        'RbeDNcZ7Oc1qPHfmLdhqWfNVEXqEO8zlTP7V1DSBQlDVx5n9A+iHEewifKwqTg9GPcyvZUJoKHUj/GuGoJ4V9Q==');
define('SECURE_AUTH_SALT', 'hlqMuow+PFWWvRq6gs5/4XNOFHFGlI3IM1HCy0beQ8rkq2gJd+efMjO+OsgfkPxIuFxv5Yaexcq36vTEf2VALA==');
define('LOGGED_IN_SALT',   'G5MVMoYP1AoToPH5ctuumXJEtRu2GkaICc5WvFEMn1ewYUXj7Wb1eLEKjWxn01VBYx17diP8o3t4GJGc8tf2LA==');
define('NONCE_SALT',       'wbc+u9Hid5UAPHCinc7uCIX4VL10ofxd37oA1G3/xMbA8lzOqlGq3CtnSfmg39kSjRQJyzjodYsA4+PuPH/yJQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
