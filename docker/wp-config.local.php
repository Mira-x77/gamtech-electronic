<?php
/**
 * LOCAL DEVELOPMENT wp-config.php — Docker environment
 * This file is used only inside the Docker container.
 * It is NOT the production config and does NOT contain live credentials.
 */

// ** Database settings — matches docker-compose.yml ** //
define( 'DB_NAME',     'gamtech_local' );
define( 'DB_USER',     'gamtech' );
define( 'DB_PASSWORD', 'gamtech' );
define( 'DB_HOST',     'db:3306' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// ** Authentication keys and salts ** //
// These are local-only — safe to commit
define( 'AUTH_KEY',         'local-auth-key-change-if-needed' );
define( 'SECURE_AUTH_KEY',  'local-secure-auth-key' );
define( 'LOGGED_IN_KEY',    'local-logged-in-key' );
define( 'NONCE_KEY',        'local-nonce-key' );
define( 'AUTH_SALT',        'local-auth-salt' );
define( 'SECURE_AUTH_SALT', 'local-secure-auth-salt' );
define( 'LOGGED_IN_SALT',   'local-logged-in-salt' );
define( 'NONCE_SALT',       'local-nonce-salt' );

$table_prefix = 'wp_';

// ** Local development settings ** //
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG',     true );

// Point WordPress to localhost instead of the live domain
define( 'WP_HOME',    'http://localhost:8080' );
define( 'WP_SITEURL', 'http://localhost:8080' );

// Disable automatic updates in local env
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy publishing. */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
