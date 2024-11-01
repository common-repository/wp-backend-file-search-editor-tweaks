<?php

// Exit if accessed directly
if ( ! defined('ABSPATH')) exit;

function wpbs_get_editor_themes() {
	return apply_filters(
		'wp_backend_search_editor_themes',
		array(
			'ambiance' => 'Ambiance',
			'chaos' => 'Chaos',
			'chrome' => 'Chrome',
			// 'cloud' => 'Cloud',
			'clouds_midnight' => 'Clouds Midnight',
			'cobalt' => 'Cobalt',
			'crimson_editor' => 'Crimson',
			'dawn' => 'Dawn',
			'dreamweaver' => 'Dreamweaver',
			'eclipse' => 'Eclipse',
			'github' => 'Github',
			'idle_fingers' => 'Idle Fingers',
			'iplastic' => 'iPlastic',
			'katzenmilch' => 'Katzenmilch',
			'kr_theme' => 'KR Theme',
			'kuroir' => 'Kuroir',
			'merbivore' => 'Merbivore',
			'merbivore_soft' => 'Merbivore Soft',
			'mono_industrial' => 'Mono Industrial',
			'monokai' => 'Monokai',
			'pastel_on_dark' => 'Pastel on Dark',
			'solarized_dark' => 'Solarized Dark',
			'solarized_light' => 'Solarized Light',
			'sqlserver' => 'SQLServer',
			'terminal' => 'Terminal',
			'textmate' => 'Textmate',
			'tomorrow' => 'Tomorrow',
			'tomorrow_night' => 'Tomorrow Night',
			'tomorrow_night_blue' => 'Tomorrow Night (Blue)',
			'tomorrow_night_bright' => 'Tomorrow Night (Bright)',
			'tomorrow_night_eighties' => 'Tomorrow Night (Eighties)',
			'twilight' => 'Twilight', 
			'vibrant_ink' => 'Vibrant Ink',
			'xcode' => 'XCode'
		)
	);
}

function wpbs_get_settings() {
	return get_option('wp_backend_search_settings', array());
}

function wpbs_get_setting($setting, $default = '') {
	$settings = wpbs_get_settings();
	return (isset($settings[$setting])) ? $settings[$setting] : $default;
}