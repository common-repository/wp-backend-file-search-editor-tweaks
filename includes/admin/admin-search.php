<?php

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;

class WP_Backend_Search_Admin_Search {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter('admin_body_class', array($this, 'admin_body_class'));
		add_filter('wp_backend_search_html', array($this, 'display_form'));
	}

	public function admin_body_class($body_classes) {

		if ($this->is_searching()) {
			$body_classes .= ' has-search-results ';
		}

		return $body_classes;
	}

	public function display_form($html) {
		return $this->prepare_form() . $html;
	}

	public function prepare_form() {
		$form = '<div class="wp-backend-search">';
			$form .= '<div class="wp-backend-search-search alignleft">';
				$form .= '<form class="wp-backend-search-search-form" method="get">';

					if ($this->is_results()) {
						$form .= sprintf('<a class="button back" href="%s">&laquo; %s</a>', remove_query_arg(array('results')), __('Back', 'wp-backend-search'));
					}

					$form .= sprintf(
						'<input class="search-field" type="text" name="search" placeholder="%s" spellcheck="false" value="%s" />', 
						__('Enter Search Term(s)', 'wp-backend-search'),
						''
					);

					if ( ! $this->is_results()) {
						$form .= '<button class="button next dashicons dashicons-arrow-down-alt2" type="button"></button>';
						$form .= '<button class="button prev dashicons dashicons-arrow-up-alt2" type="button"></button>';
					}

					if (isset($_GET['theme']) || $this->is_theme_editor()) {
						$form .= sprintf('<input type="hidden" name="theme" value="%s" />', isset($_GET['theme']) ? $_GET['theme'] : '');
					} else {
						$form .= sprintf('<input type="hidden" name="plugin" value="%s" />', isset($_GET['plugin']) ? $_GET['plugin'] : '');
					}
					
					$form .= '<span class="wp-backend-search-search-result-count">';
						
						if ( ! $this->is_results()) {
								$form .= sprintf('<span class="screen-reader-text">%s</span>', __('Results Found', 'wp-backend-search'));
								$form .= ' <span class="current">0</span>';
								$form .= sprintf(' %s ', __('of', 'wp-backend-search'));
								$form .= ' <span class="total">0</span>';
							$form .= '</span>';
						}

					$form .= '</span>';

				$form .= '</form>';

				if ( ! $this->is_results()) {
					$form .= '<label>';
						$form .= '<input type="radio" name="wp_backend_search[proximity]" value="current" checked="checked" />';
						$form .= __('Search Current File', 'wp-backend-search');
					$form .= '</label>';
					
					$form .= '<label>';
						$form .= '<input type="radio" name="wp_backend_search[proximity]" value="all" disabled="disabled" />';
						$form .= __('Search All Files', 'wp-backend-search');

						$form .= sprintf(
							' <a href="%s" target="_blank" class="wp-backend-search-premium">(%s)</a>',
							'//www.wpbackendfilesearch.com/pro',
							__('Pro Version Only', 'wp-backend-search')
						);
						
					$form .= '</label>';
				}

			$form .= '</div>'; // .wp-backend-search-search

			$form .= '<div class="alignright">';

				if ( ! $this->is_results()) {
					$form .= '<div class="wp-backend-search-editor-options">';
						$form .= '<div>';
							$form .= '<label>';
								$form .= __('Editor Theme', 'wp-backend-search');
								$form .= '<select name="wp_backend_search[editor][theme]">';
									if ($themes = wpbs_get_editor_themes()) {

										foreach ($themes as $theme => $theme_label) {
											$form .= sprintf(
												'<option value="%s" %s>%s</option>',
												$theme,
												selected(wpbs_get_setting('theme', 'chrome'), $theme),
												$theme_label
											);
										}
									}

								$form .= '</select>';
							$form .= '</label>';
						$form .= '</div>';

						$form .= '<div>';
							$form .= '<label>';
								$form .= __('Show Line Numbers', 'wp-backend-search');
								$form .= sprintf(
									'<input type="checkbox" name="wp_backend_search[editor][show_gutter]" value="1" %s />',
									checked(wpbs_get_setting('show_gutter', false), true)
								);
							$form .= '</label>';
						$form .= '</div>';

						$form .= '<div>';
							$form .= '<label>';
								$form .= __('Wrap Lines', 'wp-backend-search');
								$form .= sprintf(
									'<input type="checkbox" name="wp_backend_search[editor][wrap]" value="1" %s />',
									checked(wpbs_get_setting('wrap', false), true)
								);
							$form .= '</label>';
						$form .= '</div>';
					$form .= '</div>';

				}

			$form .= '</div>'; // .alignright
		$form .= '</div>'; // .wp-backend-search

		return $form;
	}

	public function is_searching() {

		if ( ! isset($_GET['search']) || empty($_GET['search'])) {
			return false;
		}

		if ( ! $this->is_results()) {
			return false;
		}

		return true;
	}

	public function is_results() {
		return (isset($_GET['results']) && $_GET['results'] == 'true');
	}

	public function is_plugin_editor() {
		return strpos(admin_url('plugin-editor.php'), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) !== false;
	}

	public function is_theme_editor() {
		return strpos(admin_url('theme-editor.php'), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) !== false;
	}

	public function get_search_term() {
		return isset($_GET['search']) ? esc_attr($_GET['search']) : null;
	}
}

new WP_Backend_Search_Admin_Search();