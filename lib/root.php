<?php

/**
 *
 * Basis of all framework.
 * Begins with the statement of constants.
 *
 */

define( 'ROOT_VERSION', '1.0' );

if ( !defined( 'THEME_VERSION' ) )
    define( 'THEME_VERSION', ROOT_VERSION );

// Paths

define( 'THEME_PATH',   STYLESHEETPATH . '/' );
define( 'LIB_PATH',     THEME_PATH . '/lib/' );

// Relevant Informations

define( 'ROOT_URL',     get_home_url() . '/' );
define( 'THEME_URL',    get_stylesheet_directory_uri() . '/' );

define( 'SITE_NAME',    get_bloginfo( 'name' ) );


// i18n

define( 'TEXT_DOMAIN',  'root' );

load_theme_textdomain( TEXT_DOMAIN, LIB_PATH . 'lang' );

/**
 *
 * Loads pluggable functions of the system
 * Runs autoload, loading the classes only when necessary
 *
 */
require_once LIB_PATH . 'core.php';
require_once LIB_PATH . 'hooks.php';
require_once LIB_PATH . 'toolkit.php';
require_once LIB_PATH . 'autoload.php';

add_action( 'after_setup_theme', 'root_setup', 11 );

/**
 *
 * Basic configuration of a Theme
 *
 */
function root_setup()
{
    Main::init();

    do_action( 'root_setup' );

    if ( is_admin() ) {
        do_action( 'root_admin_setup' );

        root_admin_setup();
    } else {
        do_action( 'root_public_setup' );

        root_public_setup();
    }
}

function root_admin_setup()
{
    add_action( 'admin_enqueue_scripts', array( 'Hooks', 'admin_scripts' ) );
}

function root_public_setup()
{
}

?>