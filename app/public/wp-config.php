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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '{G# /8X{;ifP?6Ho8VM@3#:MH0CKKx(Ht{:U] 4NLM}bjawCd*;QGl#f3nehO$;V' );
define( 'SECURE_AUTH_KEY',   '.wD=sCt#@EF$Ft{zJdM|y4^Jf.mHhv/vu JVo8?!ofNE)YLdpXa,rKq.UGGf$ITY' );
define( 'LOGGED_IN_KEY',     '7vr_P 5^26Q1ZV:Q+b7n!.l!)B]v-zJG`Y!b:V2Y+Eo;76q~>x:qJV%)n=HV%Z)F' );
define( 'NONCE_KEY',         'P)mpWyegm)A|:b{kvb=(W4&T]il7F|4],!Gm _P5p=JO8Jq_ Qn~d/Y%1[jl$ 2u' );
define( 'AUTH_SALT',         '@PEyM|&I$~mI>Bs_~buf;t*mA}p@nv#9fcx228}SaJb2nJmGMe4_]6ao{DrUS_F3' );
define( 'SECURE_AUTH_SALT',  ')A@[O+&!Ct&bywpYri:KC:;kvB.;cn#7dlJiMm_b9_m$&fuy{I*iA.a&05;=kW$&' );
define( 'LOGGED_IN_SALT',    '~z2i4f}N>v5Pe0r0g6}Ocxz]Q4|awqX(88AcnP#C[bS)-3Lf[2rwaAd4m[_`lETZ' );
define( 'NONCE_SALT',        '=pS+&_-j*EOPL~VF]B ~0mVVoTBW^/:aD1se]RYehaO[|#I6!}^)Vh,D+29bXHkS' );
define( 'WP_CACHE_KEY_SALT', 'm%ztoXPR8!l#cr3#a1lfy?Dtnv a#+m|0%,5#Oz}JQ9gq2s|u:EpAY:~4j}s1^Ru' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_nt7iqvroqx_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
