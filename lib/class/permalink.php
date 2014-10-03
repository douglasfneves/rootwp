<?php

class Permalink
{

    private static $vars;
    private static $rules;
    private static $templates;
    
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
    
    public static function add_rule( $slug, $query_var, $template=false, $value='1' )
    {
        self::setup();
        
        array_push( self::$vars, $query_var );
        
        self::$rules[ $slug . '/?$' ] = 'index.php?' . $query_var . '=' . $value;
        
        if ( !$template )
            $template = apply_filters( 'root_custom_template', 'custom-' . $slug );
        
        self::$templates[ $query_var ] = $template;
    }
    
    public static function rewrite_rules( $rules )
    {
        return array_merge( self::$rules, $rules );
    }
    
    public static function query_vars( $qv )
    {
        return array_merge( self::$vars, $qv );
    }
    
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
    
    public static function update_rules()
    {
        run_once( 'root_rewrite_rules', array( 'Permalink', 'flush' ), THEME_VERSION );
    }
    
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