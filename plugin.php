<?php
/**
 * Plugin Name: WP Backend File Search & Editor Tweaks Lite
 * Plugin URI: https://www.wpBackendFileSearch.com/
 * Description: Search backend files faster & easier! You can also tweak the theme & plugin file editor with custom colors, line numbering & wrapped text! Pro version adds multiple file search - so you don't have to open each PHP, JS & CSS file individually to find the code you're looking for.
 * Version: 3.0.1
 * Author: Layman Lab
 * Author URI: http://www.LaymanLab.com/
 * Requires at least: 4.0
 * Tested up to: 5.4.2
 *
 * Text Domain: wp-backend-search
 */

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;

/**
 * Plugin activation
 *
 * @since 1.0.0
 */
function wp_backend_search_lite_activate() {
	do_action('wp_backend_search_lite_activate');

	// Plugin collision prevention
	if ($plugins = get_option('active_plugins', array())) {
		foreach ($plugins as $plugin_key => $plugin) {
			if ($plugin == 'wp-backend-search/plugin.php') {
				deactivate_plugins($plugin);
				break;
			}
		}
	}

	update_option('wp_backend_search_lite_show_activation_tip', true);
}
register_activation_hook(__FILE__, 'wp_backend_search_lite_activate');

/**
 * Plugin deactivation
 *
 * @since 1.0.0
 */
function wp_backend_search_lite_deactivate() {
	do_action('wp_backend_search_lite_deactivate');
}
register_deactivation_hook(__FILE__, 'wp_backend_search_lite_deactivate');

/**
 * Show activation tip
 *
 * @since 1.0.0
 */
function wp_backend_search_lite_show_activation_tip() {
	$show = get_option('wp_backend_search_lite_show_activation_tip', false); 
	
	if ($show != true) return; ?>

	<div class="notice backend-file-search-notice" style="">
                <div class="backend-file-search-notice-logo"><span></span></div>
                <div class="backend-file-search-notice-message wp-backend-file-search-fresh">
                    <strong>Thank you for installing our "WP Backend File Search & Editor Tweaks" plugin.</br></strong>
                    <?php printf(
						"Go to your %s or %s to see it in action!",
						sprintf('<a href="%s">%s</a>', admin_url('theme-editor.php'), __('Theme Editor', 'wp-backend-search')),
						sprintf('<a href="%s">%s</a>', admin_url('plugin-editor.php'), __('Plugin Editor', 'wp-backend-search'))
					); ?>        
					<p>
						<?php printf(
							'<a href="%s" target="_blank">%s</a> Use code NEW50 and search all backend files at once.',
							'https://www.wpbackendfilesearch.com/pro/',
							'Save 50% on Pro version upgrade - now only $6!'
						); ?>
					</p>	  
                </div>
                <div class="backend-file-search-notice-cta">
                    <a href="https://www.wpbackendfilesearch.com/pro/" class="backend-file-search-notice-act button-primary" target="_blank">
                    Upgrade to Pro version
                    </a>
                    <button class="backend-file-search-notice-dismiss backend-file-search-dismiss-welcome" data-msg="Saving">Dismiss</button>
                </div>
     </div>

	<?php
}
add_action('admin_notices', 'wp_backend_search_lite_show_activation_tip');
	
	/**
	 * AJAX - Dismiss notice
	 *
	 * @since 1.0.9
	 */
	function wp_backend_search_lite_dismiss_notice() {
		update_option('wp_backend_search_lite_show_activation_tip', 0);
		exit();
	}
	add_action('wp_ajax_wp_backend_search_lite_dismiss_notice', 'wp_backend_search_lite_dismiss_notice');

	/**
	 * Enqueue admin inline scripts
	 *
	 * @since 1.0.9
	 */
	function wp_backend_search_lite_enqueue_admin_inline_scripts() { ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(document).on('click', '#wpbs-notice .notice-dismiss', function() {
					$.ajax({
						url: ajaxurl,
						method: 'post',
						data: {
							action: 'wp_backend_search_lite_dismiss_notice',
						},
						success: function(response) {}
					});
				});
			});
		</script>
		<?php
	}
	add_action('admin_footer', 'wp_backend_search_lite_enqueue_admin_inline_scripts');


/**
 * Plugin initialization
 *
 * @since 1.0.0
 */
function wp_backend_search_lite_init() {

	if (function_exists('wp_backend_search_init')) {
		return;
	}

	define('WP_BACKEND_SEARCH_DIR_PATH', trailingslashit(plugin_dir_path(__FILE__)));
	define('WP_BACKEND_SEARCH_DIR_URL', trailingslashit(plugin_dir_url(__FILE__)));

	define('WP_BACKEND_SEARCH_VERSION', '3.0.1');

	require_once(WP_BACKEND_SEARCH_DIR_PATH . 'includes/functions.php');
	require_once(WP_BACKEND_SEARCH_DIR_PATH . 'includes/buffer.php');

	if (is_admin()) {
		require_once(WP_BACKEND_SEARCH_DIR_PATH . 'includes/admin/admin.php');
		require_once(WP_BACKEND_SEARCH_DIR_PATH . 'includes/admin/admin-search.php');
	}

	// Check pro version notification during upgrade
	
	$current_db_version = get_option("WP_BACKEND_SEARCH_VERSION");

	if(empty($current_db_version) || (version_compare($current_db_version, WP_BACKEND_SEARCH_VERSION) < 0)) {

		update_option('wp_backend_search_lite_show_activation_tip', 1);
		
		update_option('WP_BACKEND_SEARCH_VERSION', WP_BACKEND_SEARCH_VERSION);

	}
}
add_action('plugins_loaded', 'wp_backend_search_lite_init');

// Add pro link on plugin page
function wp_backend_search_lite_pro_link($links) { 
  $settings_link = '<a href="https://www.wpbackendfilesearch.com/pro/" target="_blank">Upgrade to Pro</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wp_backend_search_lite_pro_link' );