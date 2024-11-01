<?php /** @noinspection ALL */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once SPA_PLUGIN_PATH.'public/SPA_Public.php';

if ( !class_exists( 'SPA_Admin' ) ) {
    /**
     * @property mixed|void options
     */
    class SPA_Admin
    {
        function __construct()
        {
            add_action('admin_init', array($this, 'spa_sub_menu_page_create'));
            add_action('admin_menu', array($this, 'spa_create_menu'));
            add_action('admin_enqueue_scripts', array($this, 'spa_enqueue_style_admin'));
            add_action('init',array($this,'spa_register_block_type'));
            add_action( 'enqueue_block_editor_assets',array($this,'spa_block_enqueue'));
        }

        public function spa_register_block_type(){
            register_block_type(
                'spa/spa-render',
                array(
                    'attributes'      => array(
                        'spa_block_css'    => array(
                            'type'      => 'string',
                            'default'   => '',
                        ),
                        'headerrating' => array(
                            'type'      => 'boolean',
                            'default'   => "1",
                        ),
                        'headertitle' => array(
                            'type'      => 'string',
                            'default'   => '{overallrating} Overall Satisfaction Rating',
                        ),
                        'headersubtitle' => array(
                            'type'      => 'string',
                            'default'   =>  'Based on {numberofcustomer} Customer Ratings Ratings from Actual Customers',
                        ),
                    ),
                    'render_callback' => array($this,'spa_render_block_callback'),
                )
            );
        }

        public function spa_render_block_callback($attributes){
            $public = new SPA_Public();
            
            $public->spa_block_css = $attributes['spa_block_css'];
            $public->headertitle = $attributes['headertitle'];
            $public->headersubtitle = $attributes['headersubtitle'];
        
            return $public->spa_load_content();
        }

        public function spa_enqueue_style_admin() {
            wp_enqueue_script('jquery');//Include jQuery for shortcode. @since 1.1
            wp_enqueue_style('font-awesome-min','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
            wp_enqueue_script('spa_javascript',SPA_PLUGIN_DIR.'public/js/spa_javascript.js', array( 'jquery' ));
            wp_localize_script( 'spa_javascript', 'spa_javascript_obj',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'image_path' => SPA_PLUGIN_DIR.'images'
                )
            );
            wp_enqueue_style('style',SPA_PLUGIN_DIR.'public/css/style.css');
            wp_enqueue_style('spa-admin-style', SPA_PLUGIN_DIR.'admin/css/spa_admin.css');
        }

        public function spa_block_enqueue()
        {
            wp_enqueue_script(
                'spa-admin-block-script', // Unique handle.
                SPA_PLUGIN_DIR.'admin/js/spa-block-build.js', // block.js: We register the block here.
                array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ) // Dependencies, defined above.
            );
        }
        /**
         * Create menu
         * @since 1.0
         */
        public function spa_create_menu()
        {
            add_options_page(
                __('SPA settings', 'textdomain'),
                'SPA settings',
                'manage_options',
                'spa-page-content',
                array($this, 'spa_page_content'),
                'dashicons-editor-insertmore',
                6
            );
        }

        /**
         * Menu page content
         * @since 1.0
         */
        public function spa_page_content()
        {
            $this->options = get_option('spa_setting_options');

            wp_enqueue_media();

            echo '<div class="wrap sap_form_container">';

            printf('<h1>%s</h1>', __('SPA Options', 'shopperapproved'));

            echo '<form method="post" action="options.php">';

            settings_fields('spa_setting');

            do_settings_sections('spa-setting-page');

            submit_button();

            echo '</form></div><div class="sap_api_doc">Please refer document for ganrate siteid and token.<a href="https://help.fomo.com/integration-guides/shopper-approved/where-do-i-find-my-shopper-approved-api-token" target="_blank">click here</a><h3>Method-1</h3><b>Step1 :-</b>Use <code>[spa_shortcode]</code> shortcode in your page<h3>Method-2 (For WordPress Version >= 5.0)</h3><b>Step1 :-</b><img class="spa_image_size" src="'.SPA_PLUGIN_DIR.'images/screenshot-1.png" /><b>Step2 :-</b><img class="spa_image_size" src="'.SPA_PLUGIN_DIR.'images/screenshot-2.png" /></div>';
        }

        /**
         * Add setting section
         * @since 1.0
         */
        public function spa_sub_menu_page_create()
        {
            register_setting(
                'spa_setting', // Option group
                'spa_setting_options', // Option name
                array($this, 'spa_sanitize') // Sanitize
            );
            add_settings_section(
                'sap_site_id', // ID
                __('Site id', 'shopperapproved'), // Title
                array($this, 'sap_site_id_callback'), // Callback
                'spa-setting-page' // Page
            );
            add_settings_section(
                'sap_token', // ID
                __('Token', 'shopperapproved'), // Title
                array($this, 'sap_token_callback'), // Callback
                'spa-setting-page' // Page
            );
        }

        public function spa_sanitize($input)
        {
            $new_input = array();

            if (isset($input['sap_site_id']))
                $new_input['sap_site_id'] = sanitize_text_field($input['sap_site_id']);

            if (isset($input['sap_token']))
                $new_input['sap_token'] = sanitize_text_field($input['sap_token']);

            return $new_input;
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function sap_site_id_callback()
        {
            printf(
                '<input type="text" id="sap_site_id" name="spa_setting_options[sap_site_id]" value="%s" required/>',
                isset($this->options['sap_site_id']) ? esc_attr($this->options['sap_site_id']) : ''
            );
        }

        public function sap_token_callback()
        {
            printf(
                '<input type="text" id="sap_token" name="spa_setting_options[sap_token]" value="%s" required/>',
                isset($this->options['sap_token']) ? esc_attr($this->options['sap_token']) : ''
            );
        }
    }
}