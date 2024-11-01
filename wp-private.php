<?php
/*
Plugin Name: Wp-Private
Plugin URI: https://www.smartlogix.co.in/
Description: Privatize parts of posts from unauthorized users. Begin protected content with [protected] and end hidden content with [/protected].
Version: 1.6.1
Author: namithjawahar
Author URI: https://www.smartlogix.co.in/

Surround the text to be privatized with [protected] and [/protected]
*/

/*  Copyright 2020  NAMITH JAWAHAR  (website : https://www.smartlogix.co.in)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once (dirname(__FILE__) . '/includes/controls.php');
require_once (dirname(__FILE__) . '/includes/settings.php');
require_once (dirname(__FILE__) . '/includes/shortcodes.php');

register_activation_hook(__FILE__, function() {
	if(!get_option('wp_private_replacement_type')) {
		add_option("wp_private_replacement_type", 'form', '', 'yes');
	}
	if(!get_option('wp_private_linkback_enable')) {
		add_option("wp_private_linkback_enable", '1', '', 'yes');
	}
	if(!get_option('wp_private_before_html')) {
		add_option("wp_private_before_html", '<br/><div id="wp-private-box"><b>This is protected content. ', '', 'yes');
	}
	if(!get_option('wp_private_after_html')) {
		add_option("wp_private_after_html", '</b></div><br/>', '', 'yes');
	}
	if(!get_option('wp_private_not_authorized_text')) {
		add_option("wp_private_after_html", '<b>You are not permitted to view Premium Content</b>', '', 'yes');
	}
});
?>