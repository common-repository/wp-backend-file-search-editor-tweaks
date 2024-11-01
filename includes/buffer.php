<?php

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;


class WP_Backend_Search_Buffer {

	public static function init() {

		if ( ! is_admin()) {
			return;
		}

		add_action('after_setup_theme', array(__CLASS__, 'buffer_start'));
		add_action('shutdown', array(__CLASS__, 'buffer_end'));
	}

	public static function buffer_start() {
		ob_start(array(__CLASS__, 'buffer_contents'));
	}

	public static function buffer_contents($contents) {
		$html = apply_filters('wp_backend_search_html', '');
		$search = '<div id="templateside">';
		$replace = $html . "\n" . $search;
		$contents = str_replace($search, $replace, $contents);

		return $contents;
	}

	public static function buffer_end() {
		if (ob_get_contents()) {
			ob_end_clean();
		}
	}
}

WP_Backend_Search_Buffer::init();