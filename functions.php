<?php

require_once STYLESHEETPATH . '/lib/root.php';

// Apply actions & filters to admin and public area
add_action( 'root_setup', 'custom_setup' );

// Activities only in admin side
add_action( 'root_admin_setup', 'admin_setup' );

// Public interactions
add_action( 'root_public_setup', 'public_setup' );

function custom_setup()
{

}

function custom_admin_setup()
{

}

function custom_public_setup()
{

}

?>