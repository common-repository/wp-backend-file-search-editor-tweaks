(function($) {
	"use strict";

	var $settings = wp_backend_search;

	var wp_backend_search_elevator = function() {
		if ( ! $('body').hasClass('has-search-results')) {
			$(document).scrollTop($('.fileedit-sub').offset().top - 32);
		}
	}

	$(document).on('submit', 'form.wp-backend-search-search-form', function(event) {
		event.preventDefault();
	});

	$(document).ready(function() {
		var input = $('#template :input#newcontent');

		var mode = $settings.mode;
		var theme = $settings.theme;
		var container = $('<div>', {
			'height': '500px',
			'width': '97%',
			'position': 'relative'
		}).insertBefore(input);

		// Hide the native textarea
		input.hide();

		// Prepare the ACE editor
		var editor = ace.edit(container[0]);

		// Show editor's gutter
		editor.renderer.setShowGutter(true);

		// Set editor's value
		editor.getSession().setValue(input.val());

		// 
		editor.getSession().on('change', function() {
			input.val(editor.getSession().getValue());
		});

		// Show editor's search form
		editor.execCommand('find');

		// Set editor's mode
		editor.getSession().setMode('ace/mode/' + mode);

		// Set editor's theme
		editor.setTheme('ace/theme/' + theme);

		// Set editor's gutter visibility
		editor.renderer.setOption('showGutter', $settings.show_gutter);

		// Set editor's wrap mode
		editor.getSession().setUseWrapMode($settings.wrap);

		// Set editor's current line view
		if ($settings.line > 0) {
			wp_backend_search_elevator();

			editor.resize(true);
			editor.scrollToLine($settings.line, true, true, function () {});
			editor.gotoLine($settings.line, 10, true);

    		var Range = ace.require("ace/range").Range
    		var marker = editor.session.addMarker(new Range($settings.line - 1, 0, $settings.line - 1, 1000), "ace_active-line", "fullLine");
		
    		$(document).on('click', editor, function() {
				editor.session.removeMarker(marker);
    		});
		}

		$(document).on('change', 'input[name="wp_backend_search[editor][show_gutter]"]', function(e) {
			editor.renderer.setOption('showGutter', $(this).is(':checked'));
		});

		$(document).on('change', 'input[name="wp_backend_search[editor][wrap]"]', function(e) {
			editor.getSession().setUseWrapMode($(this).is(':checked'));
		});

		$(document).on('change', 'select[name="wp_backend_search[editor][theme]"]', function(e) {
			var theme = $(this).val() ? $(this).val() : 'chrome';
			editor.setTheme('ace/theme/' + theme);
		});

		$(document).on('change', 'select[name="wp_backend_search[editor][theme]"], input[name="wp_backend_search[editor][show_gutter]"], input[name="wp_backend_search[editor][wrap]"]', function(e) {
			$.ajax({
				url: ajaxurl,
				method: 'post',
				data: {
					action: 'wp_backend_search_save_settings',
					settings: {
						theme: $('select[name="wp_backend_search[editor][theme]"]').val(),
						show_gutter: $('input[name="wp_backend_search[editor][show_gutter]"]').is(':checked'),
						wrap: $('input[name="wp_backend_search[editor][wrap]"]').is(':checked'),
					}
				},
				success: function(response) {}
			});
		});

		var wp_backend_search_search_init = function(search) {
			var $results = editor.$search.findAll(editor.getSession());
			var $total = $results.length;
				$total = ( ! search) ? 0 : $total;
			var $current = ($total > 0) ? $settings.current : 0;
			var result = $('.wp-backend-search-search-result-count');

			if (search != '') {
				// Initiate the search inside the editor
				editor.findAll(search);
			}

			// Show the results
			result.css('visibility', 'visible');
			result.find('.current').html($current);
			result.find('.total').html($total);
		};

		$(document).on('keyup', '.wp-backend-search-search :input.search-field', function(e) {
			var search = $(this).val();
			var $total = editor.findAll(search);
				$total = ( ! search) ? 0 : $total;
			var $current = ($total > 0) ? 1 : 0;
			var result = $('.wp-backend-search-search-result-count');

			if (search != '') {

				if ($total > 0) {
					// Initiate the search inside the editor
					editor.findAll(search);
				} else {
					editor.setValue(editor.getValue(), 1);
				}
			}

			// Show the results
			result.css('visibility', 'visible');
			result.find('.current').html($current);
			result.find('.total').html($total);
		});

		$('.wp-backend-search-search :input.search-field').trigger('keyup');
		wp_backend_search_search_init($('.wp-backend-search-search :input.search-field').val());

		$(document).on('click', '.wp-backend-search-search .button.next', function(e) {
			var search = $('form.wp-backend-search-search-form .search-field').val();
			var result = $('.wp-backend-search-search-result-count');
			var current = result.find('.current');
			var current_count = parseInt(current.html());
			var total = result.find('.total');
			var total_count = 0;

			if (search != '') {
				total_count = parseInt(total.html());

				if (total_count > 0) {
					wp_backend_search_elevator();
					editor.execCommand('findnext');
					current.html((current_count + 1) <= total_count ? current_count + 1 : 1);
				}
			}
		});

		$(document).on('click', '.wp-backend-search-search .button.prev', function(e) {
			var search = $('form.wp-backend-search-search-form .search-field').val();
			var result = $('.wp-backend-search-search-result-count');
			var current = result.find('.current');
			var current_count = parseInt(current.html());
			var total = result.find('.total');
			var total_count = 0;

			if (search != '') {
				total_count = parseInt(total.html());

				if (total_count > 0) {
					wp_backend_search_elevator();
					editor.execCommand('findprevious');
					current.html((current_count - 1) > 0 ? current_count - 1 : total_count);
				}
			}
		});

		var wp_backend_search_init = function() {
			var input = $('.wp-backend-search-search-form input[name="plugin"]');
				input = (input.length > 0) ? input : $('.wp-backend-search-search-form input[name="theme"]');

			if (input.attr('name') == 'plugin') {
				input.val($('select#plugin').val());
			} else {
				input.val($('select#theme').val());
			}
		};

		wp_backend_search_init();
	});

})(jQuery);