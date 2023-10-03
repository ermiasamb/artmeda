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
define( 'DB_NAME', 'artmeda' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         'nmQ1_[;XCqiSH_m4=UG02VS6B8kX`}n+q|Jj[t&`<LK)|aJoCcgLMF9q.@%LK5GW' );
define( 'SECURE_AUTH_KEY',  'r6b//bRZ;|NG!vh2^To|dpZ9PR&1sZ@<Ir;m#9l8Gp8xPJ^MixpeHH.^,4qL1-If' );
define( 'LOGGED_IN_KEY',    'J7/T/Z$eEzl%2BpuU;u3~2^]p}3bnM(I)/6wu*w(9[f{le1q-$bYRPbye6TeE#Qw' );
define( 'NONCE_KEY',        '$`>Y9/!;`k,dS-}z:V)bg^V2j|F>rj|hVh6:D PAxQGzz%i*ya}9s&hS9&p/ 9%N' );
define( 'AUTH_SALT',        'ktkB3c&a{wr4e=*#;6tNG*:W`-4J=QK4fP9o.W_8BxwTZ5$QWq]$nX$IbO,9/m i' );
define( 'SECURE_AUTH_SALT', '=v]?/rGL(fOXYI}<0>H|=}}XR]h6j?c/8v-Hl^pyzYbgVR1tc@wo$Q9*_yxJED`4' );
define( 'LOGGED_IN_SALT',   '~~ruuI0i,7G<}lq=i?dNlDLJsr?JD X&E/^~ur*s+z@OH3osM?2%X{5<UV^}xV1W' );
define( 'NONCE_SALT',       'y/LqNVqkD.)Iv#SFY*fcmHE0|!tE}CI4(@_[j@{jx%-F}~XRTE%Z$hKGu|2i*C>|' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'web_';

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
