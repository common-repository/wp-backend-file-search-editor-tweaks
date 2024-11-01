<?php

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;

class WP_Backend_Search_Admin {

	public function __construct() {
		$this->version = WP_BACKEND_SEARCH_VERSION;
		$this->dir_url = WP_BACKEND_SEARCH_DIR_URL;

		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'localize_scripts'));

		add_action('wp_ajax_wp_backend_search_save_settings', array($this, 'save_settings'));
	}

	public function enqueue_styles() {

		//notice
		wp_enqueue_script('wp-backend-search-notice', $this->dir_url . 'assets/js/notice.js', array(), $this->version, true);

		if ( ! in_array(get_current_screen()->id, array('plugin-editor', 'theme-editor'))) {
			return;
		}

		// ACE
		wp_enqueue_script('ace', $this->dir_url . 'assets/libraries/ace/ace.js', array(), '1.2.3', true);
		
		// Plugin
		wp_enqueue_script('wp-backend-search', $this->dir_url . 'assets/js/admin.js', array('ace'), $this->version, true);
	}

	public function enqueue_scripts() {

		wp_enqueue_style('wp-backend-search-notice', $this->dir_url . 'assets/css/notice.css', array(), $this->version, 'all');

		if ( ! in_array(get_current_screen()->id, array('plugin-editor', 'theme-editor'))) {
			return;
		}

		// Plugin
		wp_enqueue_style('wp-backend-search', $this->dir_url . 'assets/css/admin.css', array(), $this->version, 'all');

	}

	public function localize_scripts() {

		if ( ! in_array(get_current_screen()->id, array('plugin-editor', 'theme-editor'))) {
			return;
		}

		$settings = array(
			'theme' => 'chrome',
			'mode' => 'php',
			'show_gutter' => false,
			'wrap' => false,
			'themes' => wpbs_get_editor_themes(),
			'line' => 1,
			'current' => 1
		);

		if (isset($_GET['line']) && $_GET['line'] > 0) {
			$settings['line'] = absint($_GET['line']);
		}

		if (isset($_GET['current']) && $_GET['current'] > 0) {
			$settings['current'] = absint($_GET['current']);
		}

		$settings = wp_parse_args(get_option('wp_backend_search_settings', array()), $settings);

		wp_localize_script('wp-backend-search', 'wp_backend_search', apply_filters('wp_backend_search_settings', $settings));
	}

	public function save_settings() {
		$settings = isset($_POST['settings']) ? $_POST['settings'] : array();
		$current_settings = get_option('wp_backend_search_settings', array());

		if (empty($settings)) {
			wp_send_json_error(array());
			exit();
		}

		if (isset($settings['theme'])) {
			$current_settings['theme'] = $settings['theme'];
		}
		
		$current_settings['show_gutter'] = $settings['show_gutter'] == 'false' ? false : true;
		$current_settings['wrap'] = $settings['wrap'] == 'false' ? false : true;

		update_option('wp_backend_search_settings', $current_settings);

		wp_send_json_success(array());

		exit();
	}
}

new WP_Backend_Search_Admin();