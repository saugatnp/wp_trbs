<?php
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
define( 'DB_NAME', 'db_techreadyblocks' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'JFi>uZ,,uv3n:<A_@/WN|5DsyuCpOr~.uCTe:(@_zd!/)6OHIw7w:>L;<1-^<l v' );
define( 'SECURE_AUTH_KEY',  'o,#M!$$aO+[%}9TG?^w)LF=O:a;wXEpQ.q{HEH{O$37[t%nB1toZ:uxT{)}-p3z[' );
define( 'LOGGED_IN_KEY',    'W!<e)37p*f$(ePJPSDKo^n,C6I|FWt}DN@b5Es5WIs1b6ow9~uh3s>L>&wJJ^o=8' );
define( 'NONCE_KEY',        'c9N.niPX)-X$a=~c_OS8-c}+~@dKCCEDPVglRcnh~X-3:#6=,EQbo:gZ-:Z~Y0=7' );
define( 'AUTH_SALT',        'u}>qjIqxFSsN7/Za<qI8k$Q_o/76i@R ={i KI$v6j#)NHzFH@0Wj1~8GC<a(F6 ' );
define( 'SECURE_AUTH_SALT', 'P6GG)@!~=&7-K5+4x#F$G@_-3]:!%Ajet?yT.@-|>)$vRXy_3XzGP>-<mK%oR501' );
define( 'LOGGED_IN_SALT',   '}#),<n$i,%FR&mAacy>lR C;]pg7nbyw[w|.d-73-Rs26P: ~I4I;a3uM[e7.pcb' );
define( 'NONCE_SALT',       '3bj>7dG(uza8J_E2Y]DYmCF1Cd$W0mbCt}n^vt;1M]etNx;G!mq.C/PTq^RNrG;y' );

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
