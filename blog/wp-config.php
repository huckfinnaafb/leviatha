<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'leviatha_blog');

/** MySQL database username */
define('DB_USER', 'huckfinnaafb');

/** MySQL database password */
define('DB_PASSWORD', '714b627c92dec493');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'l{#6+VEdI3 prQ+,f$gMXLOd>+-bxoCncnV.^z{|:#W>iQ{y|pJRGZk*e|nyJ5|9');
define('SECURE_AUTH_KEY',  'wmuUpcFm*8O!od`39EwA0On)y=7H(203IDVgS =Nt~+NO.[}=H+4jS!s{O3u+XUT');
define('LOGGED_IN_KEY',    '9*{.mMxkxwpY6vXj#8r+Gd0&BNR.4L~9pMPyAk+GkaAi0d5HRejt8AstRNTP]ycu');
define('NONCE_KEY',        'SC+KC4*B|WAx8^)(.i0sX5-X8Z$x`-1}Hz]Xu:+o6-i2/GcP=/h)_SC=oV}i^+:U');
define('AUTH_SALT',        'eM f!wKZkPpV+fms/yRl%ERhME*I|fzi<}U-hpJ?+XLDv#V<@Ya?o$CaC=HGt#y/');
define('SECURE_AUTH_SALT', 'vZ#1--Oj)<0%in;P_0,{Qc3?Z;sO$8i39* 7QDWObXA~A)wn$X v|IGIax5+ke.s');
define('LOGGED_IN_SALT',   'yeN@%|~M&QYm;+,gkJyPO$!Yzsv =z{SDtcHxGa$-=5%8YR04XGiu/,1|hSf{&Ip');
define('NONCE_SALT',       'zFKJ6@O4<+9q:{2lH_~Q8Qbu+{9}|vIG%>)ksc^ea;-C}&Q%p29-Fp0yY[zSs;=p');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
