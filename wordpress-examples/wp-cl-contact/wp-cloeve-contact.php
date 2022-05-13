<?php
/*
Plugin Name: Cloeve Contact
description: Simple contact api.
Version: 1.0
Author: Cloeve
Author URI: http://cloeve.com
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// plugin files
require_once(dirname(__FILE__) . '/model/wp-cloeve-contact-list.php');
require_once(dirname(__FILE__) . '/api/wp-cloeve-contact-api.php');

class WP_Cloeve_Contact{

    // const
    const MENU_TITLE = "Cloeve Contact";
    const ADMIN_PAGE = "cloeve-contact";
    const SCRIPT_HANDLE = "cloeve_contact_js";
    const JS_FILE_PATH = '/js/wp-cloeve-contact.js';
    const CSS_HANDLE = "cloeve_contact_css";
    const CSS_FILE_PATH = '/css/wp-cloeve-contact.css';

    // class instance
    static $instance;

    // table object
    public $table;

    // class constructor
    public function __construct() {
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', [ $this, 'plugin_menu' ] );

        // db tables
        dbDelta( WP_Cloeve_Contact_List::retrieveCreateSQL() );

        // scripts
        add_action('wp_enqueue_scripts', [__CLASS__, 'load_scripts']);

        // add shortcode name & function handler
        add_shortcode( 'cloeve_contact_form', [__CLASS__, 'cloeve_contact_form'] );

        // API
        add_action( 'rest_api_init', ['WP_Cloeve_Contact_API', 'register_endpoints'] );
    }


    /**
     * scripts
     */
    static function load_scripts() {
        wp_enqueue_script( self::SCRIPT_HANDLE, plugins_url( self::JS_FILE_PATH, __FILE__ ), array( 'jquery' ) );


        wp_register_style( self::CSS_HANDLE, plugins_url( self::CSS_FILE_PATH, __FILE__ )  );
        wp_enqueue_style( self::CSS_HANDLE );
    }


    public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    public function plugin_menu() {

        $icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 13.17"><defs><style>.cls-1{fill:#fff;}</style></defs><title>icon_email</title><g id="_7aYNXT.tif" data-name="7aYNXT.tif"><path class="cls-1" d="M0,15.11V4.89A2.23,2.23,0,0,1,2.32,3.41q7.65,0,15.31,0A2.28,2.28,0,0,1,20,4.89V15.11a2.26,2.26,0,0,1-2.37,1.48q-7.65,0-15.31,0A2.22,2.22,0,0,1,0,15.11ZM2.8,4.85a.7.7,0,0,0,.29.4l6.47,6.48c.42.42.46.42.89,0l6.46-6.47c.11-.11.27-.19.28-.4Zm.38,10.27H16.86a.88.88,0,0,0-.09-.16l-3.51-3.52c-.22-.22-.37-.17-.56,0-.48.5-1,1-1.47,1.48a1.64,1.64,0,0,1-2.45,0c-.48-.46-1-.92-1.41-1.41-.27-.29-.45-.29-.72,0-1.08,1.1-2.17,2.18-3.26,3.27C3.3,14.88,3.17,15,3.18,15.12ZM1.43,5.68c-.1.08-.06.19-.06.29v8.29c0,.13-.07.31.09.36s.22-.12.31-.21c1.31-1.31,2.61-2.62,3.93-3.92.25-.25.24-.39,0-.63-1.34-1.32-2.66-2.65-4-4C1.63,5.8,1.56,5.69,1.43,5.68Zm17.14,0-.17.13C17,7.15,15.65,8.54,14.25,9.91c-.24.23-.16.37,0,.57l3.86,3.85c.12.12.22.35.41.27s.07-.29.07-.45c0-2.68,0-5.35,0-8A.85.85,0,0,0,18.57,5.64Z" transform="translate(0 -3.41)"/></g></svg>');

        $hook = add_menu_page(
            self::MENU_TITLE,
            self::MENU_TITLE,
            'manage_options',
            self::ADMIN_PAGE,
            [ $this, 'plugin_settings_page' ],
            $icon
        );

        add_action( "load-$hook", [ $this, 'screen_option' ] );
    }



    /**
     * Plugin settings page
     */
    public function plugin_settings_page() {


        ?>
        <div class="wrap">
            <h2><?php echo self::MENU_TITLE;?></h2>

        <?php

        require_once(dirname(__FILE__) . '/admin-page/contact-page.php');

    }


    /**
     * shortcode_handler
     * @param $attributes
     * @param $content
     * @param $tag
     * @return string
     */
    public static function cloeve_contact_form( $attributes, $content, $tag ){

        // normalize attribute keys, lowercase
        $attributes = array_change_key_case((array)$attributes, CASE_LOWER);

        // start ob to return
        ob_start();

        require_once(dirname(__FILE__) . '/page/wp-contact-form.php');

        // return the html
        return ob_get_clean();
    }

    /**
     * Screen options
     */
    public function screen_option() {

        // tables
        $this->table = new WP_Cloeve_Contact_List();

    }

    /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}

// load plugin
add_action( 'plugins_loaded', function () {
    WP_Cloeve_Contact::get_instance();
} );