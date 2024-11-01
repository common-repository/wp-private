<?php
add_shortcode('protected', function($atts, $content = null) {
	$settings = get_option('wp_private_settings');
	if(isset($settings) && is_array($settings)) {
		if(is_user_logged_in()) {		
			if(isset($settings['blocked_users']) && is_array($settings['blocked_users'])) {
				$currentUser = wp_get_current_user();
				if(in_array($currentUser->ID, $settings['blocked_users'])) {
					return ((isset($settings['authorized_text']) && ($settings['authorized_text'] != ''))?$settings['authorized_text']:'');
				} else {
					return do_shortcode($content);				
				}
			} else {
				return do_shortcode($content);
			}
		} else {	
			if(isset($settings['type'])) {
				$output = ((isset($settings['before_html']))?$settings['before_html']:'');
				switch($settings['type']) {
					case 'form':
						$output .= wp_login_form(array('echo' => true, 'redirect' => $_SERVER['REQUEST_URI']));
						break;
					case 'link':
						if(isset($settings['custom_login_page_url']) && ($settings['custom_login_page_url'] != '')) {
							$loginURL = $settings['custom_login_page_url'].((strpos($settings['custom_login_page_url'], '?'))?'&':'?').'redirect='.urlencode($_SERVER['REQUEST_URI']);
						} else {
							$loginURL =  wp_login_url(urlencode($_SERVER['REQUEST_URI']));
						}
						$output .= 'Please <a href="'.$loginURL.'">Login</a> or <a href="'.wp_registration_url().'">Register</a> for access.';
						break;
				}
				$output .= ((isset($settings['after_html']))?$settings['after_html']:'');
				return $output;
			}
		}
	}
	return '';
});

add_shortcode('loginform', function($atts, $content = null) {
	if(is_user_logged_in()) {
		$currentUser = wp_get_current_user();
		return 'Hello, '.esc_html($currentUser->user_login).'. <a href="'.wp_logout_url($_SERVER['REQUEST_URI']).'" title="Logout">Logout</a>';
	} else {
		return wp_login_form(array('echo' => true, 'redirect' => ((isset($_GET['redirect']) && ($_GET['redirect'] != ''))?urldecode($_GET['redirect']):$_SERVER['REQUEST_URI'])));
	}
});
?>