/* --------------------------------------------------------------
 filter.js 2016-09-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module(
	'filter',

	['form', 'xhr'],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			$preloader = null,
			$contentWrapper = null,
			errorTimer = null,
			updateTimer = null,
			filterAjax = null,
			productsAjax = null,
			historyAvailable = false,
			reset = false,
			historyPopstateEventBinded = false,
			defaults = {
				// The url the ajax request execute against
				requestUrl: 'shop.php?do=Filter',
				// If autoUpdate is false, and this is true the product listing filter will be set to default 
				// on page reload
				resetProductlistingFilter: false,
				// If true, the product list gets updated dynamically
				autoUpdate: true,
				// The delay after a change event before an ajax gets executed
				updateDelay: 200,
				// The maximum number of retries after failures
				retries: 2,
				// After which delay the nex try will be done
				retryDelay: 500,
				
				selectorMapping: {
					filterForm: '.filter-box-form-wrapper',
					productsContainer: '.product-filter-target',
					filterSelectionContainer: '.filter-selection-container',
					listingPagination: '.productlisting-filter-container .panel-pagination',
					filterHiddenContainer: '.productlisting-filter-container .productlisting-filter-hiddens',
					paginationInfo: '.pagination-info'
				}
			},
			options = $.extend(true, {}, defaults, data),
			module = {};


		/*
		 var v_selected_values_group = new Array();
		 $("#menubox_body_shadow").find("span").live("click", function()
		 {		
		 $("#menubox_body_shadow").removeClass("error").html("");

		 get_selected_values();
		 get_available_values(0);
		 });

		 $("#menubox_filter .filter_features_link.link_list").live("click", function(){
		 var t_feature_value_id = $(this).attr("rel");
		 $( "#"+t_feature_value_id ).trigger("click");
		 return false;
		 */

// ########## HELPER FUNCTIONS ##########

		/**
		 * Helper function that updates the product list
		 * and the pagination for the filter.
		 * @param filterResult
		 * @private
		 */
		var _updateProducts = function(historyChange) {
			var resetParam = '';
			
			if (productsAjax) {
				productsAjax.abort();
			}

			if (reset) {
				resetParam = '&reset=true';
			}
			
			// Call the request ajax and fill the page with the delivered data
			productsAjax = $.ajax({
				                      url: options.requestUrl + '/GetListing&' + $this.serialize() + resetParam,
				                      method: 'GET',
				                      dataType: 'json'
			                      }).done(function(result) {
				
			    // redirect if filter has been reset              	
				if (typeof result.redirect !== 'undefined') {
					location.href = result.redirect;
					return;
				}
				
				// bind _historyHandler function on popstate event not earlier than first paged content change to 
				// prevent endless popstate event triggering bug on mobile devices
				if (!historyPopstateEventBinded && options.autoUpdate) {
					$(window).on('popstate', _historyHandler);
					historyPopstateEventBinded = true;
				}
				
				jse.libs.template.helpers.fill(result.content, $contentWrapper, options.selectorMapping);
				
				var $productsContainer = $(options.selectorMapping.productsContainer);
				
				$productsContainer.attr('data-gambio-widget', 'cart_handler');
				gambio.widgets.init($productsContainer);
				
				if (historyAvailable && historyChange) {
					var urlParameter = decodeURIComponent($this.serialize());

					history.pushState({}, 'filter', location.origin + location.pathname + '?' + urlParameter
					                  + location.hash);
					$this.trigger('pushstate', []);
				} else {
					$this.trigger('pushstate_no_history', []);
				}
			});
		};

		/**
		 * Helper function that transforms the filter
		 * settings to a format that is readable by
		 * the backend
		 * @param       {object}        dataset             The formdata that contains the filter settings
		 * @return     {*}                                 The transformed form data
		 * @private
		 */
		var _transform = function(dataset, join) {
			var result = [];
			$.each(dataset.filter_fv_id, function(key, value) {
				if (value !== undefined && value !== false) {

					if (typeof value === 'object') {
						var valid = [];
						$.each(value, function(k, v) {
							if (v !== false) {
								valid.push(v);
							}
						});
						if (join) {
							result.push(key + ':' + valid.join('|'));
						} else {
							result[key] = result[key] || [];
							result[key] = valid;
						}
					} else {
						result.push(key + ':' + value);
					}
				}
			});

			dataset.filter_fv_id = (join) ? result.join('&') : result;

			return dataset;
		};

		/**
		 * Helper function that calls the update
		 * ajax and replaces the filter box with
		 * the new form
		 * @param {integer}  tryCount        The count how often the ajax has failed
		 * @param {object}   formdata        The ready to use data from the form
		 * @param {boolean}  historyChange   If true, the history will be updted after the list update (if possible)
		 * @private
		 */
		var _update = function(tryCount, formdata, historyChange) {

			$preloader
				.removeClass('error')
				.show();

			if (filterAjax) {
				filterAjax.abort();
			}

			filterAjax = jse.libs.xhr.ajax({
				                               url: options.requestUrl,
				                               data: formdata
			                               }, true).done(function(result) {
				// Update the filterbox and check if the products need to be updated automatically.
				// The elements will need to be converted again to checkbox widgets, so we will first
				// store them in a hidden div, convert them and then append them to the filter box 
				// (dirty fix because it is not otherwise possible without major refactoring ...)
				var checkboxes = $(result.content.filter.selector)
					.find('input:checkbox')
						.length,
					$targets = $(result.content.filter.selector);

				if (checkboxes) {

					var $hiddenContainer = $('<div/>').appendTo('body').hide();
					// Copy the elements but leave a clone to the filter box element.
					$this.children().appendTo($hiddenContainer).clone().appendTo($this);
					
					jse.libs.template.helpers.fill(result.content, $hiddenContainer, options.selectorMapping);
					gambio.widgets.init($hiddenContainer);

					var intv = setInterval(function() {
						if ($hiddenContainer.find('.single-checkbox').length > 0) {
							$this.children().remove();
							$hiddenContainer.children().appendTo($this);
							$hiddenContainer.remove();

							$preloader.hide();
							if (options.autoUpdate) {
								_updateProducts(historyChange);
							}

							clearInterval(intv);
						}

					}, 300);

				} else {
					jse.libs.template.helpers.fill(result.content, $body, options.selectorMapping);
					gambio.widgets.init($targets);
					$preloader.hide();

					if (options.autoUpdate) {
						_updateProducts(historyChange);
					}
				}
				
				// reinitialize widgets in updated DOM
				window.gambio.widgets.init($this);
				
			}).fail(function() {
				if (tryCount < options.retries) {
					// Restart the update process if the
					// tryCount hasn't reached the maximum
					errorTimer = setTimeout(function() {
						_update(tryCount + 1, formdata, historyChange);
					}, options.retryDelay);
				} else {
					$preloader.addClass('error');
				}
			});

		};

		/**
		 * Helper function that starts the filter
		 * and page update process
		 * @private
		 */
		var _updateStart = function(historyChange) {
			var dataset = jse.libs.form.getData($this);

			historyChange = (historyChange !== undefined) ? !!historyChange : true;

			_update(0, _transform(dataset, true), historyChange);
		};


// ########## EVENT HANDLER #########

		/**
		 * The submit event gets aborted
		 * if the live update is set to true. Else
		 * if the productlisiting filter shall be
		 * kept, get the parameters from it and store
		 * them in hidden input fields before submit
		 * @param       {object}        e           jQuery event object
		 * @private
		 */
		var _submitHandler = function(e) {
			reset = false;
			
			if (options.autoUpdate) {
				e.preventDefault();
				e.stopPropagation();
			} else if (!options.resetProductlistingFilter) {
				jse.libs.form.addHiddenByUrl($this);
			}
		};

		/**
		 * Event handler that gets triggered
		 * on every change of an input field
		 * inside the filter box. It starts the
		 * update process after a short delay
		 * @param       {object}        e           jQuery event object
		 * @private
		 */
		var _changeHandler = function(e) {
			e.preventDefault();
			e.stopPropagation();

			clearTimeout(updateTimer);
			clearTimeout(errorTimer);

			updateTimer = setTimeout(_updateStart, options.updateDelay);
		};

		/**
		 * Event handler that reacts on the reset
		 * button / event. Depending on the autoUpdate
		 * setting the page gets reloaded or the form
		 * / products gets updated
		 * @param       {object}        e           jQuery event object
		 * @private
		 */
		var _resetHandler = function(e) {
			e.preventDefault();
			e.stopPropagation();

			jse.libs.form.reset($this);
			jse.libs.form.addHiddenByUrl($this);

			reset = true;
			
			if (options.autoUpdate) {
				_updateStart();
			} else {
				location.href = location.pathname + '?' + $this.serialize();
			}
		};

		/**
		 * Handler that listens on the popstate event.
		 * In a case of a popstate, the filter will change
		 * to it's previous state and will update the page
		 * @private
		 */
		var _historyHandler = function() {
			jse.libs.form.reset($this);
			jse.libs.form.prefillForm($this, jse.libs.template.helpers.getUrlParams());
			_updateStart(false);
		};

		/**
		 * Handler that listens on the click event
		 * of a "more" button to show all filter options
		 * @private
		 */
		var _clickHandler = function() {
			$(this).parent().removeClass('collapsed');
			$(this).hide();
		};
		
		/**
		 * Handler that listens on the click event
		 * of a filter option link to trigger the
		 * change event of the belonging hidden checkbox
		 * 
		 * @param e
		 * @private
		 */
		var _filterClickHandler = function(e) {
			var id = $(this).attr('rel');
			
			e.preventDefault();
			e.stopPropagation();
			
			$('#' + id).prop('checked', true).trigger('change');
		};

// ########## INITIALIZATION ##########


		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			$preloader = $this.find('.preloader, .preloader-message');
			$contentWrapper = $('.main-inside');
			historyAvailable = jse.core.config.get('history');

			// no auto update on start page
			if($(options.selectorMapping.productsContainer).length === 0) {
				options.autoUpdate = false;
			}
			
			$this
				.on('change', 'select, input[type="checkbox"], input[type="text"]', _changeHandler)
				.on('click', '.btn-link', _filterClickHandler)
				.on('reset', _resetHandler)
				.on('submit', _submitHandler)
				.on('click', '.show-more', _clickHandler);

			$body.addClass('filterbox-enabled');

			done();
		};

		// Return data to widget engine
		return module;
	});