<?php
add_action('admin_menu', function() {
	register_setting('wp_private_settings', 'wp_private_settings');
	add_options_page('Premium Content', 'Premium Content', 'manage_options', 'wp-private', 'wp_private_settings_page');
}, 100);

add_action('admin_enqueue_scripts', function($hook) {
    if($hook == 'settings_page_wp-private') {
        wp_enqueue_script('common');
        wp_enqueue_script('wp-lists');
        wp_enqueue_script('postbox');
    }
});

function wp_private_settings_page() {
	do_action('add_meta_boxes', 'wp-private', 10);
	echo '<div class="wrap">';
        echo '<h2>WP-PRIVATE : Premium Content Manager</h2>';
        settings_errors(); 
        echo '<div class="wp_private_settings_wrap">';
            echo '<form id="wp_private_form" method="post" action="options.php">';
				settings_fields('wp_private_settings');
                wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
                wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
                echo '<div id="poststuff">';
                     echo '<div id="post-body" class="metabox-holder columns-'.((1 == get_current_screen()->get_columns())?'1':'2').'">';
                        echo '<div id="postbox-container-1" class="postbox-container">';
							echo '<p style="margin-top: 0;">';
								echo '<input style="display: block;width: 100%;padding: 10px 10px;font-size: 22px;font-weight: 600;text-transform: uppercase;letter-spacing: 1px;" type="submit" class="button button-primary" value="Save" />';
							echo '</p>';
							echo '<div style="font-size: 16px; font-weight: 600; background: #FFFFAA; display: block; padding: 10px; text-align: justify; border: 1px dashed #FF0000; margin: 10px 0 0; border-radius: 5px;">';
								echo '<p style="margin-top: 0;">Thanks for using Wp-Private.  Positive <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/wp-private#postform">reviews</a> are a great motivation for us to add new features, fix bugs and spend more time working on my Plugins.</p>';
								echo '<p>Also checkout our Ad Management Plugin : <a target="_blank" href="https://wordpress.org/plugins/wp-insert">Wp-Insert</a> for your Ad Management needs</p>';
								echo '<p>If you need a unique feature developed for your Wordpress site, feel free to <a target="_blank" href="https://smartlogix.co.in/contact-us/">reach out</a> for a No Commitment Quote.</p>';
								echo '<p style="margin-bottom: 0;">Looking forward to your continued support and patronage - <i>Namith Jawahar</i>.</p>';
							echo '</div>';
                        echo '</div>';
                        echo '<div id="postbox-container-2" class="postbox-container">';
							do_meta_boxes('wp-private', 'normal', null);
                        echo '</div>';
						echo '<br class="clear">';
                    echo '</div>';
                    echo '<br class="clear">';
                echo '</div>';
            echo '</form>';
        echo '</div>';
    echo '</div>';
	echo '<script type="text/javascript">';
	echo 'jQuery(document).ready(function($) {';
		echo 'jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");';
		echo 'postboxes.add_postbox_toggles("wp-private");';
	echo '});';
	echo '</script>';
}

add_action('add_meta_boxes', function() {
	$settings = get_option('wp_private_settings');
	if(isset($settings) && !is_array($settings)) {
		$settings = array(
			'type' => ((get_option('wp_private_replacement_type'))?get_option('wp_private_replacement_type'):'form'),
			'before_html' => ((get_option('wp_private_before_html'))?get_option('wp_private_before_html'):'<br/><div id="wp-private-box"><b>This is protected content. '),
			'after_html' => ((get_option('wp_private_after_html'))?get_option('wp_private_after_html'):'</b></div><br/>'),
			'authorized_text' => ((get_option('wp_private_not_authorized_text'))?get_option('wp_private_not_authorized_text'):'<b>You are not permitted to view Premium Content</b>'),
			'blocked_users' => ((get_option('wp_private_selected_users'))?implode(',', get_option('wp_private_selected_users')):array()),
			'custom_login_page_url' => ((get_option('wp_private_custom_login_page_url'))?get_option('wp_private_custom_login_page_url'):'')
		);		
	}
	add_meta_box('wp_private_how_to_block_instructions', 'How To Block Content', 'wp_private_how_to_block_instructions_content', 'wp-private', 'normal', 'default', $settings);
	add_meta_box('wp_private_how_to_block', 'How To Replace Blocked Content', 'wp_private_how_to_block_content', 'wp-private', 'normal', 'default', $settings);
	add_meta_box('wp_private_text_to_replace', 'Text To Replace Blocked Content', 'wp_private_text_to_replace_content', 'wp-private', 'normal', 'default', $settings);
	add_meta_box('wp_private_text_not_authorized', 'Text For Logged-in But Not Authorized Users', 'wp_private_text_not_authorized_content', 'wp-private', 'normal', 'default', $settings);
	add_meta_box('wp_private_who_cant_see', 'Users Who Can\'t See Blocked Content', 'wp_private_who_cant_see_content', 'wp-private', 'normal', 'default', $settings);
	add_meta_box('wp_private_custom_login_page', 'Custom Login Page', 'wp_private_custom_login_page_content', 'wp-private', 'normal', 'default', $settings);
});

function wp_private_how_to_block_instructions_content($post, $metabox) {
	echo '<p>You can block any content anywhere in your POSTS/PAGES by Surrounding the text to be privatized with <code>[protected]</code> and <code>[/protected]</code>.</p>';
	echo '<p>Gutenberg users can use the Classic Editor to enter the Protected Content and surround it with the Shortcode.</p>';
}

function wp_private_how_to_block_content($post, $metabox) {
	echo wp_private_get_control('radio', 'Replace Blocked Content with A Login Form.', 'wp_private_settings_type_form', 'wp_private_settings[type]', ((isset($metabox['args']['type']))?$metabox['args']['type']:''), 'form', '', 'input widefat tooltip');
	echo wp_private_get_control('radio', 'Replace Blocked Content with Login and Register Link.', 'wp_private_settings_type_link', 'wp_private_settings[type]', ((isset($metabox['args']['type']))?$metabox['args']['type']:''), 'link', '', 'input widefat tooltip');
	echo wp_private_get_control('radio', 'Just Remove Blocked Content.', 'wp_private_settings_type_custom', 'wp_private_settings[type]', ((isset($metabox['args']['type']))?$metabox['args']['type']:''), 'custom', '', 'input widefat tooltip');
}

function wp_private_text_to_replace_content($post, $metabox) {
	echo wp_private_get_control('textarea', 'HTML to Insert Before protected Area (For Non Logged in users)', 'wp_private_settings_before_html', 'wp_private_settings[before_html]', ((isset($metabox['args']['before_html']))?$metabox['args']['before_html']:''), null, 'eg : This is protected Content. Please Login to read');
	echo wp_private_get_control('textarea', 'HTML to Insert After protected Area (For Non Logged in users)', 'wp_private_settings_after_html', 'wp_private_settings[after_html]', ((isset($metabox['args']['after_html']))?$metabox['args']['after_html']:''), null, 'eg : Enter your user name and password above to access premium Content');
}

function wp_private_text_not_authorized_content($post, $metabox) {
	echo wp_private_get_control('textarea', '', 'wp_private_settings_authorized_text', 'wp_private_settings[authorized_text]', ((isset($metabox['args']['authorized_text']))?$metabox['args']['authorized_text']:''));
}

function wp_private_who_cant_see_content($post, $metabox) {
	class UserListTable extends WP_List_Table {
		private $settings;
		
		function __construct($settings) {
			$this->settings = $settings;
			parent::__construct(array(
				'singular' => 'user',
				'plural' => 'users',
				'ajax' => false
			));
		}
		
		function display_tablenav($which) {
			if($which == 'bottom') {
				echo '<span style="font-size: small;">Select the Users who dont have the privilage to see the premium content</span>';
			}
		}
		
		function get_columns() {
			return $columns= array(
				'cb' => '<input type="checkbox" />',
				'username' => 'Username',
				'name' => 'Name'
			);
		}
		
		function column_default($item, $columnName) {
			if(($columnName == 'username') || ($columnName == 'name')) {
				return $item[$columnName];
			}
		}
		
		function column_cb($item) {
			if(isset($this->settings['args']['blocked_users']) && is_array($this->settings['args']['blocked_users']) && in_array($item['user_id'], $this->settings['args']['blocked_users'])) {
				return '<input type="checkbox" name="wp_private_settings[blocked_users][]" value="'.$item['user_id'].'" checked="checked" />'; 
			} else {
				return '<input type="checkbox" name="wp_private_settings[blocked_users][]" value="'.$item['user_id'].'" />'; 
			}			   
		}
		
		function prepare_items() {
			$this->_column_headers = array($this->get_columns(), array(), array());
			$processedUsersData = array();
			$users = get_users(array(
				'number' => 100
			));
			if(isset($users) && is_array($users)) {
				foreach($users as $user) {
					$processedUsersData[] = array(
						'user_id' => $user->ID,
						'username' => $user->user_login,
						'name' => $user->first_name.' '.$user->last_name
					);
				}
			}			
			$this->items = $processedUsersData;
		}
	}
	$userListTable = new UserListTable($metabox);
	$userListTable->prepare_items();
	$userListTable->display();
}

function wp_private_custom_login_page_content($post, $metabox) {
	echo '<p>You can show the custom Login Form anywhere in your POSTS/PAGES by using the shortcode [loginform].</p>';
	echo wp_private_get_control('text', 'Custom Login Page URL', 'wp_private_settings_custom_login_page_url', 'wp_private_settings[custom_login_page_url]', ((isset($metabox['args']['custom_login_page_url']))?$metabox['args']['custom_login_page_url']:''), null, 'This is URL to the page where you have pasted in the shortcode [loginform]');
}
?>