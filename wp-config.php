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
define( 'DB_NAME', getenv('DATABASE_NAME') );

/** MySQL database username */
define( 'DB_USER', getenv('DATABASE_USER') );

/** MySQL database password */
define( 'DB_PASSWORD', getenv('DATABASE_PASSWORD') );

/** MySQL hostname */
define( 'DB_HOST',  getenv(strtoupper(str_replace('-', '_', getenv('DATABASE_SERVICE_NAME'))).'_SERVICE_HOST') );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ' 5 ri.u:kNG~0T)nSHL)IjegkOI!i;puL-94-j:g`G1FyFOk2 ^qy[?md{3}$V(>');
define('SECURE_AUTH_KEY',  '$mSDhQ%v;c7wZ+L7|Yu:t&YNvS>^4@wT,<{^5^@QFk4kk6SW~ yks+w(!=4[]cK4');
define('LOGGED_IN_KEY',    'WX9F<Q&4+k;;O=k`myNDvuOg_PJQ}|Hh!1B|;2F?6MbzTN:MDwjg!tb)lz>v`-oN');
define('NONCE_KEY',        'G3e?_j|Z WU%Mv![55R$Lo7a-5lGZBBdi=RuWV;Ks-U94e.EI5!*m%r< yDR.-dR');
define('AUTH_SALT',        '^&k0aj5B|JG57rKv<v)~O)|U / bSTE29|X9^IAxjcIKn|v2? 5^wZE|+uL=E7H7');
define('SECURE_AUTH_SALT', '/|)9o;w7gP:m;KA!n.b%ogEs=YTHc`dbMx<@:m<3Izs8YaM3-r.}RNt(QUsfphz)');
define('LOGGED_IN_SALT',   ';-lK&,[@(^c>pQtM.lmAC+3]Q>`%*| Ih@$ik(|=Ach#1NhO9=+hXC#.|G-S)Kd;');
define('NONCE_SALT',       'CD9p:X!e.+c&AI5VqUzh;AcrTHw4%B+{h@!t.$0uDz;A0W|o*zasdixT;&4y?0eG');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
