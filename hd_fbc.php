<?php
/*
Plugin Name: HD Facebook Connect
Plugin URI:
Description: Tailored facebook connect integration
Author: Horia Dragomir
Version: 1
Author URI: http://hdragomir.com
License: GPL2

    Copyright 2011  Horia Dragomir (email : horia@hdragomir.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

register_activation_hook(__FILE__, 'hd_fbc::activation_check');


add_action('admin_init', 'hd_fbc::admin_init');
add_action('admin_menu', 'hd_fbc::add_admin_page');
add_action('init', 'hd_fbc::_facebook');

class hd_fbc{

    public static function activation_check(){
        if (version_compare(PHP_VERSION, '5', '<')) {
            deactivate_plugins(basename(__FILE__)); // Deactivate ourself
            wp_die(printf('We need PHP5 or later and we have %s. No good.', PHP_VERSION));
        }
    }

    public static function admin_init(){
        $options = get_option('hd_fbc_options');
        if (empty($options['api_key']) || empty($options['app_secret']) || empty($options['appid'])) {
            add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf(__('You need to have a look at the FB Connect <a href="%s">settings</a> page.'), admin_url('options-general.php?page=hd_fbc'))."</p></div>';" ) );
        }

        register_setting( 'hd_fbc_options', 'hd_fbc_options', 'hd_fbc::sanitize_options_input' );

        add_settings_section('hd_fbc_main', '', create_function('', ';'), 'hd_fbc');

        add_settings_field('hd_fbc_api_key', __('Facebook API Key'), 'hd_fbc::input__setting_api_key', 'hd_fbc', 'hd_fbc_main');
        add_settings_field('hd_fbc_app_secret', __('Facebook Application Secret'), 'hd_fbc::input__setting_app_secret', 'hd_fbc', 'hd_fbc_main');
        add_settings_field('hd_fbc_appid', __('Facebook Application ID'), 'hd_fbc::input__setting_appid', 'hd_fbc', 'hd_fbc_main');
        add_settings_field('hd_fbc_fanpage', __('Facebook Fan Page'), 'hd_fbc::input__setting_fanpage', 'hd_fbc', 'hd_fbc_main');
    }

    public static function add_admin_page(){
        global $options_page;
        $options_page = add_options_page(__('Facebook Connect'), __('Facebook Connect'), 'manage_options', 'hd_fbc', 'hd_fbc::options_page');
    }

    public static function options_page() {
    ?>
        <div class="wrap">
            <h2><?php _e('Facebook Connect Settings'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('hd_fbc_options'); ?>

                <?php do_settings_sections('hd_fbc'); ?>

                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
                </p>
            </form>
        </div>

    <?php
    }

    public static function sanitize_options_input(array $input){
        return $input;
    }

    public static function input__setting_api_key() {
        $options = get_option('hd_fbc_options');
        echo "<input type='text' id='fbapikey' name='hd_fbc_options[api_key]' value='{$options['api_key']}' size='40' /> ";
        _e('(required)');
    }
    public static function input__setting_app_secret() {
        $options = get_option('hd_fbc_options');
        echo "<input type='text' id='fbappsecret' name='hd_fbc_options[app_secret]' value='{$options['app_secret']}' size='40' /> ";
        _e('(required)');
    }
    public static function input__setting_appid() {
        $options = get_option('hd_fbc_options');
        echo "<input type='text' id='fbappid' name='hd_fbc_options[appid]' value='{$options['appid']}' size='40' /> ";
        _e('(required)');
        if (!empty($options['appid'])) printf(__('<p>Here is a <a href=\'http://www.facebook.com/apps/application.php?id=%s&amp;v=wall\'>link to your applications wall</a>.</p>'), $options['appid']);
    }
    public static function input__setting_fanpage() {
        $options = get_option('hd_fbc_options');
        echo "<input type='text' id='fbfanpage' name='hd_fbc_options[fanpage]' value='{$options['fanpage']}' size='40' />";
    }

    public static function _facebook(){
        $options = get_option('hd_fbc_options');
        if(! class_exists('Facebook', false))
            require_once 'facebook-graph/facebook.php';

        $facebook = new Facebook(array('appId' => $options['appid'], 'secret' => $options['app_secret'], 'cookie' => true));
        if(method_exists($facebook, 'getSession'))
            $facebook->getSession();
        return $facebook;
    }


    public static function user(){
        $user = null;
        $facebook = self::_facebook();
        try {
            $user = $facebook->api('/me');
        } catch (FacebookApiException $e) {}

        return $user;
    }
}

