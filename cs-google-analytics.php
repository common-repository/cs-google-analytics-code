<?php
/**
 * Plugin Name: CS Google Analytics Code
 * Plugin URI: http://catchsquare.com
 * Description: Verifying website in google analytics
 * Version: 1.0.2
 * Author: Catch Square
 * License:     GNU General Public License v2.0 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: wordpress-google-analytics
 *
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( !class_exists( 'casqrAnalyticsInit' ) ) {
    class casqrAnalyticsInit {
        private $options;
        /**
         * Initialize the plugin and register hooks
         */
        public function __construct() {
            define( 'CSA_VERSION', '1.0' );
            define( 'CSA_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
            if(is_admin()): //loading the content for the wp-admin
                add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
                add_action( 'admin_init', array( $this, 'page_init' ) );
            endif;
            // Add the google analytics code in the head section in frontend
            if(!is_admin()) //loading the content for the frontend
                add_action( 'wp_head', array( $this, 'add_google_analytics' ) );
            //require_once( CSA_PLUGIN_DIR . '/CsaSettings.class.php' );
        }
        function show_admin_analytics_setting_page()
        {
            /* $settingsPage = new csaSettings();
              $settingsPage->create_admin_page();*/
            $this->options = get_option( 'csa-google-analytics' );
            ?>
            <div class="wrap">
                <h1>Google Analytics Settings</h1>
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'csa-google-analytics-group' );
                    do_settings_sections( 'csa_google_analytics' );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php
        }

        /**
         * Register and add settings
         */
        public function page_init()
        {
            register_setting(
                'csa-google-analytics-group', // Option group
                'csa-google-analytics', // Option name
                array( $this, 'sanitize' ) // Sanitize
            );

            add_settings_section(
                'csa_settings', // ID
                '', // Title
                array( $this, 'print_section_info' ), // Callback
                'csa_google_analytics' // Page
            );

            add_settings_field(
                'google_analytics_number', // ID
                'CS Google Analytics', // Title
                array( $this, 'google_analytics_number_callback' ), // Callback
                'csa_google_analytics', // Page
                'csa_settings' // Section
            );
        }

        function add_google_analytics()
        {
            $this->options = get_option( 'csa-google-analytics' );
            if($this->options['csa_google_analytics_number']!='')
            {
                echo "<!-- google analytics code added by CAS-google analytics plugins -->
                <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

 ga('create', '".$this->options['csa_google_analytics_number']."', 'auto');
  ga('send', 'pageview');
  </script>
  <!-- google analytics code added by CAS-google analytics plugins -->
  ";
            }

        }

        function add_admin_menu()
        {

            add_menu_page(
                _x( 'Google Analytics', 'Title of admin page that shows analytics', 'wordpress-google-analytics' ),
                _x( 'Google Analytics', 'Title of analytics admin menu item', 'wordpress-google-analytics' ),
                'manage_options',
                'cas-google-analytics',
                array( $this, 'show_admin_analytics_setting_page' ),
                plugins_url('cs-google-analytics-code/images/ga-icon.png'),
                '26.2987'
            );
        }


        /**
         * Print the Section text
         */
        public function print_section_info()
        {
            print 'Enter your Google Analytics UA Code:';
        }
        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize( $input )
        {
            $new_input = array();


            if( isset( $input['csa_google_analytics_number'] ) )
                $new_input['csa_google_analytics_number'] = sanitize_text_field( $input['csa_google_analytics_number'] );

            return $new_input;
        }
        /**
         * Get the settings option array and print one of its values
         */
        public function google_analytics_number_callback()
        {
            printf(
                '<input type="text" class="form-control" id="title" name="csa-google-analytics[csa_google_analytics_number]" value="%s" /> eg : UA-XXXXXXXX-X',
                isset( $this->options['csa_google_analytics_number'] ) ? esc_attr( $this->options['csa_google_analytics_number']) : ''
            );
        }
    }
}
global $csa_controller;
$csa_controller = new casqrAnalyticsInit();
