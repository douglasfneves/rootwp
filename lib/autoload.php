<?php

/*
 *
 * Function for automatic charging of the framework classes
 * @param string $class_name Receives the name of the requested class
 *
 */
function __autoload( $class_name )
{
    // Alphabetically
    $components = array(
        'code',
        'cpt',
        'dashboard',
        'form',
        'main',
        'metabox',
        'option',
        'permalink',
        'term',
        'theme',
        'user'
    );
    $file_name = strtolower( $class_name );
    if ( in_array( $file_name, $components ) ) {
        $file = LIB_PATH . 'class/' . $file_name . '.php';
        if ( file_exists( $file ) )
            require_once $file;
    }
}

?>