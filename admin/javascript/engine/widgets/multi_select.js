/* --------------------------------------------------------------
 multi_select.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Multi Select Widget
 *
 * This module serves as a wrapper of SumoSelect, a jQuery plugin that provides enhanced select-box functionality.
 * Bind this widget to a parent container and mark each child select-box element with the `data-multi_select-instance`
 * attribute.
 *
 * After the initialization of the widget all the marked elements will be converted into SumoSelect instances.
 *
 *
 * ### Options
 *
 * **Options Source | `data-multi_select-source` | String | Optional**
 *
 * Provide a URL that will be used to fetch the options of the select box. The widget will perform a GET request to
 * the provided destination and expects a JSON array with the options:
 *
 * [
 *   {
 *     "value": "1", 
 *     "text": "Option #1"
 *   },
 *   {
 *     "value": "2", 
 *     "text": "Option #2"
 *   }
 * ]
 *
 * You can also pass other configuration directly in the parent element which will be used for every child instance.
 *
 *
 * ### Methods
 *
 * **Reload Options [AJAX]**
 *
 * You can use this method to refresh the options from the already provided data-multi_select-source or by providing
 * a new URL which will also be set as the data-source of the element. If the multi select has no URL then it will just
 * sync its values with the select element.
 *
 * * ```js
 * $('#my-multi-select').multi_select('reload', 'http://shop.de/options/source/url');
 * ```
 *
 * **Refresh Options**
 *
 * Update the multi-select widget with the state of the original select element. This method is useful after performing
 * changes in the original element and need to display them in the multi-select widget.
 *
 * ```js
 * $('#my-multi-select').multi_select('refresh');
 * ```
 *
 * ### Events
 * ```javascript
 * // Triggered when the multi-select widget has performed a "reload" method (after the AJAX call).
 * $('#my-multi-select').on('reload', function(event) {});
 * ```
 *
 * ### Example
 *
 * ```html
 * <form data-gx-widget="multi_select">
 *   <select data-multi_select-instance data-multi_select-source="http://shop.de/options-source-url"></select>
 * </form>
 * ```
 *
 * {@link http://hemantnegi.github.io/jquery.sumoselect}
 *
 * @module Admin/Widgets/multi_select
 * @requires jQuery-SumoSelect
 */
gx.widgets.module('multi_select', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Default Options
	 *
	 * @type {Object}
	 */
	const defaults = {
		selectAll: true,
		csvDispCount: 2,
		captionFormat: `{0} ${jse.core.lang.translate('selected', 'admin_labels')}`,
		locale: [
			'OK',
			jse.core.lang.translate('CANCEL', 'general'),
			jse.core.lang.translate('SELECT_ALL', 'general')
		]
	};
	
	/**
	 * Final Options
	 *
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Add the "multi_select" method to the jQuery prototype.
	 */
	function _addPublicMethod() {
		if ($.fn.multi_select) {
			return;
		}
		
		$.fn.extend({
			multi_select: function(action, ...args) {
				if (!$(this).is('select')) {
					throw new Error('Called the "multi_select" method on an invalid object (select box expected).');
				}
				
				$.each(this, function() {
					switch (action) {
						case 'reload':
							_reload($(this), ...args);
							break;
						
						case 'refresh':
							_refresh(this);
					}
				});
			}
		});
	}
	
	/**
	 * Fill a select box with the provided options.
	 *
	 * @param {jQuery} $select The select box to be filled.
	 * @param {Object} options Array with { value: "...", "text": "..." } entries.
	 */
	function _fillSelect($select, options) {
		$select.empty();
		$.each(options, (index, option) => {
			$select.append(new Option(option.text, option.value));
		});
	}
	
	/**
	 * Reload the options from the source (data property) or the provided URL,
	 *
	 * @param {string} url Optional, if provided it will be used as the source of the data and will also update the
	 * data-source property of the element.
	 */
	function _reload($select, url) {
		url = url || $select.data('source');
		
		if (!url) {
			throw new Error('Multi Select Reload: Neither URL nor data-source contain a URL value.');
		}
		
		$select.data('source', url);
		
		$.getJSON(url)
			.done(function(response) {
				_fillSelect($select, response);
				$select[0].sumo.reload();
				$select.trigger('reload');
			})
			.fail(function(jqxhr, textStatus, errorThrown) {
				jse.core.debug.error('Multi Select AJAX Error: ', jqxhr, textStatus, errorThrown);
			});
	}
	
	/**
	 * Refresh the multi select instance depending the state of the original select element.
	 *
	 * @param {Node} select The DOM element to be refreshed.
	 */
	function _refresh(select) {
		if (select.sumo === undefined) {
			throw new Error('Multi Select Refresh: The provided select element is not an instance of SumoSelect.', 
				select);
		}
		
		select.sumo.reload();
		
		// Update the caption by simulating a click in an ".opt" element.  
		_overrideSelectAllCaption.apply($(select.parentNode).find('.opt')[0]);
	}
	
	/**
	 * Override the multi select caption when all elements are selected.
	 *
	 * This callback will override the caption because SumoSelect does not provide a setting for this text.
	 */
	function _overrideSelectAllCaption() {
		const $optWrapper = $(this).parents('.optWrapper');
		const allCheckboxesChecked = $optWrapper.find('.opt.selected').length === $optWrapper.find('.opt').length;
		const atLeastOneCheckboxChecked = $optWrapper.find('.opt.selected').length > 0;
		const $selectAllCheckbox = $optWrapper.find('.select-all');
		
		$selectAllCheckbox.removeClass('partial-select');
		
		if (allCheckboxesChecked) {
			$optWrapper
				.siblings('.CaptionCont')
				.children('span')
				.text(jse.core.lang.translate('all_selected', 'admin_labels'));
		} else if (atLeastOneCheckboxChecked) {
			$selectAllCheckbox.addClass('partial-select');
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		// Add public module method.  
		_addPublicMethod();
		
		// Initialize the elements. 
		$this.find('[data-multi_select-instance]').each(function() {
			const $select = $(this);
			
			$select.removeAttr('data-multi_select-instance');
			
			// Instantiate the widget without an AJAX request.
			$select.SumoSelect(options);
			
			if ($select.data('multi_selectSource') !== undefined) {
				// Remove the data attribute and store the value internally with the 'source' key. 
				$select.data('source', $select.data('multi_selectSource'));
				$select.removeAttr('data-multi_select-source');
				
				// Fetch the options with an AJAX request.
				$.getJSON($select.data('multi_selectSource'))
					.done(function(response) {
						_fillSelect($select, response);
						$select[0].sumo.unload();
						$select.SumoSelect(options);
						$select.trigger('reload');
					})
					.fail(function(jqxhr, textStatus, errorThrown) {
						jse.core.debug.error('Multi Select AJAX Error: ', jqxhr, textStatus, errorThrown);
					});
			}
		});
		
		done();
	};
	
	// When the user clicks on the "Select All" option update the text with a custom translations. This has to 
	// be done manually because there is no option for this text in SumoSelect. 
	$this.on('click', '.select-all, .opt', _overrideSelectAllCaption);
	
	return module;
	
}); 