<?php

/**
 * 
 * Manipulation of Permalinks and rewrite rules
 * 
 */
class Permalink
{

    /**
     *
     * @var array Global request vars
     * 
     */
    private static $vars;
    
    /**
     *
     * @var array Custom rules 
     * 
     */
    private static $rules;
    
    /**
     *
     * @var array PHP Files related with the created rules 
     * 
     */
    private static $templates;
    
    /**
     * 
     * Starter function
     * 
     */
    private static function setup()
    {
        if ( !is_array( self::$rules ) ) {
            self::$vars = array();
            self::$rules = array();
            self::$templates = array();
            
            add_filter( 'rewrite_rules_array',  array( 'Permalink', 'rewrite_rules' ) );
            add_filter( 'query_vars',           array( 'Permalink', 'query_vars' ) );
            add_action( 'template_redirect',    array( 'Permalink', 'redirect' ), 11 );
            
            add_action( 'wp', array( 'Permalink', 'update_rules' ) );
        }
    }
    
    /**
     * 
     * Inserts a new instruction to WordPress Rules
     * 
     * @param string $slug Navigation URL to open new content
     * @param string $query_var Global variable setted when accessed the informed url
     * @param string $template Which template file is executed to this case, by default opens custom-slug.php
     * @param mixed $value Custom value to global variable
     * 
     */
    public static function add_rule( $slug, $query_var, $template=false, $value='1' )
    {
        self::setup();
        
        array_push( self::$vars, $query_var );
        
        self::$rules[ $slug . '/?$' ] = 'index.php?' . $query_var . '=' . $value;
        
        if ( !$template )
            $template = apply_filters( 'root_custom_template', 'custom-' . $slug );
        
        self::$templates[ $query_var ] = $template;
    }
    
    /**
     * 
     * Updates the rewrite rules of permalinks
     * 
     * @param array $rules WordPress Rules
     * @return array Updated rules
     * 
     */
    public static function rewrite_rules( $rules )
    {
        return array_merge( self::$rules, $rules );
    }
    
    /**
     * 
     * Embeds custom variables to globals
     * 
     * @param array $qv Global vars
     * @return array Updated variables
     * 
     */
    public static function query_vars( $qv )
    {
        return array_merge( self::$vars, $qv );
    }
    
    /**
     * 
     * Redirects site visitors to exhibition of the custom templates
     * 
     * @global object $wp_query Current WordPress Query
     * @return boolean|null If current request is a feed request, nothing happens
     * 
     */
    public static function redirect()
    {
        if ( is_feed() ) 
            return false;
        
        $template = false;

        global $wp_query;
        foreach ( self::$vars as $v ) {
            if ( $wp_query->get( $v ) ) {
                $template = ( isset( self::$templates[ $v ] ) ) ? self::$templates[ $v ] : false;
                break;
            }
        }

        if ( $template ) {
            $template_file = THEME_PATH . $template . '.php';
            if ( file_exists( $template_file ) ) {
                include( $template_file );
                exit;
            }
        }
    }
    
    /**
     * 
     * Executes the update only once according each 'theme version'
     * 
     */
    public static function update_rules()
    {
        run_once( 'root_rewrite_rules', array( 'Permalink', 'flush' ), THEME_VERSION );
    }
    
    /**
     * 
     * Updates the rewrite rules and refreshes the requested page to fix which template should be opened
     * 
     */
    public static function flush()
    {
        flush_rewrite_rules();
        $url = ( in_localhost() ) ? 'http://localhost/' : '';
        $url .= $_SERVER[ 'REQUEST_URI' ];
        wp_redirect( $url );
        exit;
    }
    
} 

?>