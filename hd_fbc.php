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



class hd_fbc{
    public static function activation_check(){
        if (version_compare(PHP_VERSION, '5', '<')) {
            deactivate_plugins(basename(__FILE__)); // Deactivate ourself
            wp_die(printf('We need PHP5 or later and we have %s. No good.', PHP_VERSION));
        }
    }



}
