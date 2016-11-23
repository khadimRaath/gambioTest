'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 filter.js 2016-09-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('filter', ['form', 'xhr'], function (data) {

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
	var _updateProducts = function _updateProducts(historyChange) {
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
		}).done(function (result) {

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

				history.pushState({}, 'filter', location.origin + location.pathname + '?' + urlParameter + location.hash);
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
	var _transform = function _transform(dataset, join) {
		var result = [];
		$.each(dataset.filter_fv_id, function (key, value) {
			if (value !== undefined && value !== false) {

				if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object') {
					var valid = [];
					$.each(value, function (k, v) {
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

		dataset.filter_fv_id = join ? result.join('&') : result;

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
	var _update = function _update(tryCount, formdata, historyChange) {

		$preloader.removeClass('error').show();

		if (filterAjax) {
			filterAjax.abort();
		}

		filterAjax = jse.libs.xhr.ajax({
			url: options.requestUrl,
			data: formdata
		}, true).done(function (result) {
			// Update the filterbox and check if the products need to be updated automatically.
			// The elements will need to be converted again to checkbox widgets, so we will first
			// store them in a hidden div, convert them and then append them to the filter box 
			// (dirty fix because it is not otherwise possible without major refactoring ...)
			var checkboxes = $(result.content.filter.selector).find('input:checkbox').length,
			    $targets = $(result.content.filter.selector);

			if (checkboxes) {

				var $hiddenContainer = $('<div/>').appendTo('body').hide();
				// Copy the elements but leave a clone to the filter box element.
				$this.children().appendTo($hiddenContainer).clone().appendTo($this);

				jse.libs.template.helpers.fill(result.content, $hiddenContainer, options.selectorMapping);
				gambio.widgets.init($hiddenContainer);

				var intv = setInterval(function () {
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
		}).fail(function () {
			if (tryCount < options.retries) {
				// Restart the update process if the
				// tryCount hasn't reached the maximum
				errorTimer = setTimeout(function () {
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
	var _updateStart = function _updateStart(historyChange) {
		var dataset = jse.libs.form.getData($this);

		historyChange = historyChange !== undefined ? !!historyChange : true;

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
	var _submitHandler = function _submitHandler(e) {
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
	var _changeHandler = function _changeHandler(e) {
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
	var _resetHandler = function _resetHandler(e) {
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
	var _historyHandler = function _historyHandler() {
		jse.libs.form.reset($this);
		jse.libs.form.prefillForm($this, jse.libs.template.helpers.getUrlParams());
		_updateStart(false);
	};

	/**
  * Handler that listens on the click event
  * of a "more" button to show all filter options
  * @private
  */
	var _clickHandler = function _clickHandler() {
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
	var _filterClickHandler = function _filterClickHandler(e) {
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
	module.init = function (done) {
		$preloader = $this.find('.preloader, .preloader-message');
		$contentWrapper = $('.main-inside');
		historyAvailable = jse.core.config.get('history');

		// no auto update on start page
		if ($(options.selectorMapping.productsContainer).length === 0) {
			options.autoUpdate = false;
		}

		$this.on('change', 'select, input[type="checkbox"], input[type="text"]', _changeHandler).on('click', '.btn-link', _filterClickHandler).on('reset', _resetHandler).on('submit', _submitHandler).on('click', '.show-more', _clickHandler);

		$body.addClass('filterbox-enabled');

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZmlsdGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkcHJlbG9hZGVyIiwiJGNvbnRlbnRXcmFwcGVyIiwiZXJyb3JUaW1lciIsInVwZGF0ZVRpbWVyIiwiZmlsdGVyQWpheCIsInByb2R1Y3RzQWpheCIsImhpc3RvcnlBdmFpbGFibGUiLCJyZXNldCIsImhpc3RvcnlQb3BzdGF0ZUV2ZW50QmluZGVkIiwiZGVmYXVsdHMiLCJyZXF1ZXN0VXJsIiwicmVzZXRQcm9kdWN0bGlzdGluZ0ZpbHRlciIsImF1dG9VcGRhdGUiLCJ1cGRhdGVEZWxheSIsInJldHJpZXMiLCJyZXRyeURlbGF5Iiwic2VsZWN0b3JNYXBwaW5nIiwiZmlsdGVyRm9ybSIsInByb2R1Y3RzQ29udGFpbmVyIiwiZmlsdGVyU2VsZWN0aW9uQ29udGFpbmVyIiwibGlzdGluZ1BhZ2luYXRpb24iLCJmaWx0ZXJIaWRkZW5Db250YWluZXIiLCJwYWdpbmF0aW9uSW5mbyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlUHJvZHVjdHMiLCJoaXN0b3J5Q2hhbmdlIiwicmVzZXRQYXJhbSIsImFib3J0IiwiYWpheCIsInVybCIsInNlcmlhbGl6ZSIsIm1ldGhvZCIsImRhdGFUeXBlIiwiZG9uZSIsInJlc3VsdCIsInJlZGlyZWN0IiwibG9jYXRpb24iLCJocmVmIiwid2luZG93Iiwib24iLCJfaGlzdG9yeUhhbmRsZXIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJoZWxwZXJzIiwiZmlsbCIsImNvbnRlbnQiLCIkcHJvZHVjdHNDb250YWluZXIiLCJhdHRyIiwiaW5pdCIsInVybFBhcmFtZXRlciIsImRlY29kZVVSSUNvbXBvbmVudCIsImhpc3RvcnkiLCJwdXNoU3RhdGUiLCJvcmlnaW4iLCJwYXRobmFtZSIsImhhc2giLCJ0cmlnZ2VyIiwiX3RyYW5zZm9ybSIsImRhdGFzZXQiLCJqb2luIiwiZWFjaCIsImZpbHRlcl9mdl9pZCIsImtleSIsInZhbHVlIiwidW5kZWZpbmVkIiwidmFsaWQiLCJrIiwidiIsInB1c2giLCJfdXBkYXRlIiwidHJ5Q291bnQiLCJmb3JtZGF0YSIsInJlbW92ZUNsYXNzIiwic2hvdyIsInhociIsImNoZWNrYm94ZXMiLCJmaWx0ZXIiLCJzZWxlY3RvciIsImZpbmQiLCJsZW5ndGgiLCIkdGFyZ2V0cyIsIiRoaWRkZW5Db250YWluZXIiLCJhcHBlbmRUbyIsImhpZGUiLCJjaGlsZHJlbiIsImNsb25lIiwiaW50diIsInNldEludGVydmFsIiwicmVtb3ZlIiwiY2xlYXJJbnRlcnZhbCIsImZhaWwiLCJzZXRUaW1lb3V0IiwiYWRkQ2xhc3MiLCJfdXBkYXRlU3RhcnQiLCJmb3JtIiwiZ2V0RGF0YSIsIl9zdWJtaXRIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiYWRkSGlkZGVuQnlVcmwiLCJfY2hhbmdlSGFuZGxlciIsImNsZWFyVGltZW91dCIsIl9yZXNldEhhbmRsZXIiLCJwcmVmaWxsRm9ybSIsImdldFVybFBhcmFtcyIsIl9jbGlja0hhbmRsZXIiLCJwYXJlbnQiLCJfZmlsdGVyQ2xpY2tIYW5kbGVyIiwiaWQiLCJwcm9wIiwiY29yZSIsImNvbmZpZyIsImdldCJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBOzs7Ozs7Ozs7O0FBVUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFFBREQsRUFHQyxDQUFDLE1BQUQsRUFBUyxLQUFULENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxRQUFRRCxFQUFFLE1BQUYsQ0FEVDtBQUFBLEtBRUNFLGFBQWEsSUFGZDtBQUFBLEtBR0NDLGtCQUFrQixJQUhuQjtBQUFBLEtBSUNDLGFBQWEsSUFKZDtBQUFBLEtBS0NDLGNBQWMsSUFMZjtBQUFBLEtBTUNDLGFBQWEsSUFOZDtBQUFBLEtBT0ND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QUFSLEdBQXFCLEtBQXJCO0FBQ0E7O0FBRURmLFFBQ0UyQyxFQURGLENBQ0ssUUFETCxFQUNlLG9EQURmLEVBQ3FFK0QsY0FEckUsRUFFRS9ELEVBRkYsQ0FFSyxPQUZMLEVBRWMsV0FGZCxFQUUyQnNFLG1CQUYzQixFQUdFdEUsRUFIRixDQUdLLE9BSEwsRUFHY2lFLGFBSGQsRUFJRWpFLEVBSkYsQ0FJSyxRQUpMLEVBSWUwRCxjQUpmLEVBS0UxRCxFQUxGLENBS0ssT0FMTCxFQUtjLFlBTGQsRUFLNEJvRSxhQUw1Qjs7QUFPQTdHLFFBQU0rRixRQUFOLENBQWUsbUJBQWY7O0FBRUE1RDtBQUNBLEVBcEJEOztBQXNCQTtBQUNBLFFBQU92QyxNQUFQO0FBQ0EsQ0F6WUYiLCJmaWxlIjoid2lkZ2V0cy9maWx0ZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGZpbHRlci5qcyAyMDE2LTA5LTI5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnZmlsdGVyJyxcblxuXHRbJ2Zvcm0nLCAneGhyJ10sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRcdCRwcmVsb2FkZXIgPSBudWxsLFxuXHRcdFx0JGNvbnRlbnRXcmFwcGVyID0gbnVsbCxcblx0XHRcdGVycm9yVGltZXIgPSBudWxsLFxuXHRcdFx0dXBkYXRlVGltZXIgPSBudWxsLFxuXHRcdFx0ZmlsdGVyQWpheCA9IG51bGwsXG5cdFx0XHRwcm9kdWN0c0FqYXggPSBudWxsLFxuXHRcdFx0aGlzdG9yeUF2YWlsYWJsZSA9IGZhbHNlLFxuXHRcdFx0cmVzZXQgPSBmYWxzZSxcblx0XHRcdGhpc3RvcnlQb3BzdGF0ZUV2ZW50QmluZGVkID0gZmFsc2UsXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Ly8gVGhlIHVybCB0aGUgYWpheCByZXF1ZXN0IGV4ZWN1dGUgYWdhaW5zdFxuXHRcdFx0XHRyZXF1ZXN0VXJsOiAnc2hvcC5waHA/ZG89RmlsdGVyJyxcblx0XHRcdFx0Ly8gSWYgYXV0b1VwZGF0ZSBpcyBmYWxzZSwgYW5kIHRoaXMgaXMgdHJ1ZSB0aGUgcHJvZHVjdCBsaXN0aW5nIGZpbHRlciB3aWxsIGJlIHNldCB0byBkZWZhdWx0IFxuXHRcdFx0XHQvLyBvbiBwYWdlIHJlbG9hZFxuXHRcdFx0XHRyZXNldFByb2R1Y3RsaXN0aW5nRmlsdGVyOiBmYWxzZSxcblx0XHRcdFx0Ly8gSWYgdHJ1ZSwgdGhlIHByb2R1Y3QgbGlzdCBnZXRzIHVwZGF0ZWQgZHluYW1pY2FsbHlcblx0XHRcdFx0YXV0b1VwZGF0ZTogdHJ1ZSxcblx0XHRcdFx0Ly8gVGhlIGRlbGF5IGFmdGVyIGEgY2hhbmdlIGV2ZW50IGJlZm9yZSBhbiBhamF4IGdldHMgZXhlY3V0ZWRcblx0XHRcdFx0dXBkYXRlRGVsYXk6IDIwMCxcblx0XHRcdFx0Ly8gVGhlIG1heGltdW0gbnVtYmVyIG9mIHJldHJpZXMgYWZ0ZXIgZmFpbHVyZXNcblx0XHRcdFx0cmV0cmllczogMixcblx0XHRcdFx0Ly8gQWZ0ZXIgd2hpY2ggZGVsYXkgdGhlIG5leCB0cnkgd2lsbCBiZSBkb25lXG5cdFx0XHRcdHJldHJ5RGVsYXk6IDUwMCxcblx0XHRcdFx0XG5cdFx0XHRcdHNlbGVjdG9yTWFwcGluZzoge1xuXHRcdFx0XHRcdGZpbHRlckZvcm06ICcuZmlsdGVyLWJveC1mb3JtLXdyYXBwZXInLFxuXHRcdFx0XHRcdHByb2R1Y3RzQ29udGFpbmVyOiAnLnByb2R1Y3QtZmlsdGVyLXRhcmdldCcsXG5cdFx0XHRcdFx0ZmlsdGVyU2VsZWN0aW9uQ29udGFpbmVyOiAnLmZpbHRlci1zZWxlY3Rpb24tY29udGFpbmVyJyxcblx0XHRcdFx0XHRsaXN0aW5nUGFnaW5hdGlvbjogJy5wcm9kdWN0bGlzdGluZy1maWx0ZXItY29udGFpbmVyIC5wYW5lbC1wYWdpbmF0aW9uJyxcblx0XHRcdFx0XHRmaWx0ZXJIaWRkZW5Db250YWluZXI6ICcucHJvZHVjdGxpc3RpbmctZmlsdGVyLWNvbnRhaW5lciAucHJvZHVjdGxpc3RpbmctZmlsdGVyLWhpZGRlbnMnLFxuXHRcdFx0XHRcdHBhZ2luYXRpb25JbmZvOiAnLnBhZ2luYXRpb24taW5mbydcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblxuXHRcdC8qXG5cdFx0IHZhciB2X3NlbGVjdGVkX3ZhbHVlc19ncm91cCA9IG5ldyBBcnJheSgpO1xuXHRcdCAkKFwiI21lbnVib3hfYm9keV9zaGFkb3dcIikuZmluZChcInNwYW5cIikubGl2ZShcImNsaWNrXCIsIGZ1bmN0aW9uKClcblx0XHQge1x0XHRcblx0XHQgJChcIiNtZW51Ym94X2JvZHlfc2hhZG93XCIpLnJlbW92ZUNsYXNzKFwiZXJyb3JcIikuaHRtbChcIlwiKTtcblxuXHRcdCBnZXRfc2VsZWN0ZWRfdmFsdWVzKCk7XG5cdFx0IGdldF9hdmFpbGFibGVfdmFsdWVzKDApO1xuXHRcdCB9KTtcblxuXHRcdCAkKFwiI21lbnVib3hfZmlsdGVyIC5maWx0ZXJfZmVhdHVyZXNfbGluay5saW5rX2xpc3RcIikubGl2ZShcImNsaWNrXCIsIGZ1bmN0aW9uKCl7XG5cdFx0IHZhciB0X2ZlYXR1cmVfdmFsdWVfaWQgPSAkKHRoaXMpLmF0dHIoXCJyZWxcIik7XG5cdFx0ICQoIFwiI1wiK3RfZmVhdHVyZV92YWx1ZV9pZCApLnRyaWdnZXIoXCJjbGlja1wiKTtcblx0XHQgcmV0dXJuIGZhbHNlO1xuXHRcdCAqL1xuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgdXBkYXRlcyB0aGUgcHJvZHVjdCBsaXN0XG5cdFx0ICogYW5kIHRoZSBwYWdpbmF0aW9uIGZvciB0aGUgZmlsdGVyLlxuXHRcdCAqIEBwYXJhbSBmaWx0ZXJSZXN1bHRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlUHJvZHVjdHMgPSBmdW5jdGlvbihoaXN0b3J5Q2hhbmdlKSB7XG5cdFx0XHR2YXIgcmVzZXRQYXJhbSA9ICcnO1xuXHRcdFx0XG5cdFx0XHRpZiAocHJvZHVjdHNBamF4KSB7XG5cdFx0XHRcdHByb2R1Y3RzQWpheC5hYm9ydCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRpZiAocmVzZXQpIHtcblx0XHRcdFx0cmVzZXRQYXJhbSA9ICcmcmVzZXQ9dHJ1ZSc7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIENhbGwgdGhlIHJlcXVlc3QgYWpheCBhbmQgZmlsbCB0aGUgcGFnZSB3aXRoIHRoZSBkZWxpdmVyZWQgZGF0YVxuXHRcdFx0cHJvZHVjdHNBamF4ID0gJC5hamF4KHtcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgIHVybDogb3B0aW9ucy5yZXF1ZXN0VXJsICsgJy9HZXRMaXN0aW5nJicgKyAkdGhpcy5zZXJpYWxpemUoKSArIHJlc2V0UGFyYW0sXG5cdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJ1xuXHRcdFx0ICAgICAgICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFxuXHRcdFx0ICAgIC8vIHJlZGlyZWN0IGlmIGZpbHRlciBoYXMgYmVlbiByZXNldCAgICAgICAgICAgICAgXHRcblx0XHRcdFx0aWYgKHR5cGVvZiByZXN1bHQucmVkaXJlY3QgIT09ICd1bmRlZmluZWQnKSB7XG5cdFx0XHRcdFx0bG9jYXRpb24uaHJlZiA9IHJlc3VsdC5yZWRpcmVjdDtcblx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIGJpbmQgX2hpc3RvcnlIYW5kbGVyIGZ1bmN0aW9uIG9uIHBvcHN0YXRlIGV2ZW50IG5vdCBlYXJsaWVyIHRoYW4gZmlyc3QgcGFnZWQgY29udGVudCBjaGFuZ2UgdG8gXG5cdFx0XHRcdC8vIHByZXZlbnQgZW5kbGVzcyBwb3BzdGF0ZSBldmVudCB0cmlnZ2VyaW5nIGJ1ZyBvbiBtb2JpbGUgZGV2aWNlc1xuXHRcdFx0XHRpZiAoIWhpc3RvcnlQb3BzdGF0ZUV2ZW50QmluZGVkICYmIG9wdGlvbnMuYXV0b1VwZGF0ZSkge1xuXHRcdFx0XHRcdCQod2luZG93KS5vbigncG9wc3RhdGUnLCBfaGlzdG9yeUhhbmRsZXIpO1xuXHRcdFx0XHRcdGhpc3RvcnlQb3BzdGF0ZUV2ZW50QmluZGVkID0gdHJ1ZTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkY29udGVudFdyYXBwZXIsIG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciAkcHJvZHVjdHNDb250YWluZXIgPSAkKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nLnByb2R1Y3RzQ29udGFpbmVyKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRwcm9kdWN0c0NvbnRhaW5lci5hdHRyKCdkYXRhLWdhbWJpby13aWRnZXQnLCAnY2FydF9oYW5kbGVyJyk7XG5cdFx0XHRcdGdhbWJpby53aWRnZXRzLmluaXQoJHByb2R1Y3RzQ29udGFpbmVyKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChoaXN0b3J5QXZhaWxhYmxlICYmIGhpc3RvcnlDaGFuZ2UpIHtcblx0XHRcdFx0XHR2YXIgdXJsUGFyYW1ldGVyID0gZGVjb2RlVVJJQ29tcG9uZW50KCR0aGlzLnNlcmlhbGl6ZSgpKTtcblxuXHRcdFx0XHRcdGhpc3RvcnkucHVzaFN0YXRlKHt9LCAnZmlsdGVyJywgbG9jYXRpb24ub3JpZ2luICsgbG9jYXRpb24ucGF0aG5hbWUgKyAnPycgKyB1cmxQYXJhbWV0ZXJcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICArIGxvY2F0aW9uLmhhc2gpO1xuXHRcdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ3B1c2hzdGF0ZScsIFtdKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdwdXNoc3RhdGVfbm9faGlzdG9yeScsIFtdKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHRyYW5zZm9ybXMgdGhlIGZpbHRlclxuXHRcdCAqIHNldHRpbmdzIHRvIGEgZm9ybWF0IHRoYXQgaXMgcmVhZGFibGUgYnlcblx0XHQgKiB0aGUgYmFja2VuZFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZGF0YXNldCAgICAgICAgICAgICBUaGUgZm9ybWRhdGEgdGhhdCBjb250YWlucyB0aGUgZmlsdGVyIHNldHRpbmdzXG5cdFx0ICogQHJldHVybiAgICAgeyp9ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgVGhlIHRyYW5zZm9ybWVkIGZvcm0gZGF0YVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF90cmFuc2Zvcm0gPSBmdW5jdGlvbihkYXRhc2V0LCBqb2luKSB7XG5cdFx0XHR2YXIgcmVzdWx0ID0gW107XG5cdFx0XHQkLmVhY2goZGF0YXNldC5maWx0ZXJfZnZfaWQsIGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcblx0XHRcdFx0aWYgKHZhbHVlICE9PSB1bmRlZmluZWQgJiYgdmFsdWUgIT09IGZhbHNlKSB7XG5cblx0XHRcdFx0XHRpZiAodHlwZW9mIHZhbHVlID09PSAnb2JqZWN0Jykge1xuXHRcdFx0XHRcdFx0dmFyIHZhbGlkID0gW107XG5cdFx0XHRcdFx0XHQkLmVhY2godmFsdWUsIGZ1bmN0aW9uKGssIHYpIHtcblx0XHRcdFx0XHRcdFx0aWYgKHYgIT09IGZhbHNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0dmFsaWQucHVzaCh2KTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRpZiAoam9pbikge1xuXHRcdFx0XHRcdFx0XHRyZXN1bHQucHVzaChrZXkgKyAnOicgKyB2YWxpZC5qb2luKCd8JykpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0cmVzdWx0W2tleV0gPSByZXN1bHRba2V5XSB8fCBbXTtcblx0XHRcdFx0XHRcdFx0cmVzdWx0W2tleV0gPSB2YWxpZDtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0cmVzdWx0LnB1c2goa2V5ICsgJzonICsgdmFsdWUpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdGRhdGFzZXQuZmlsdGVyX2Z2X2lkID0gKGpvaW4pID8gcmVzdWx0LmpvaW4oJyYnKSA6IHJlc3VsdDtcblxuXHRcdFx0cmV0dXJuIGRhdGFzZXQ7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNhbGxzIHRoZSB1cGRhdGVcblx0XHQgKiBhamF4IGFuZCByZXBsYWNlcyB0aGUgZmlsdGVyIGJveCB3aXRoXG5cdFx0ICogdGhlIG5ldyBmb3JtXG5cdFx0ICogQHBhcmFtIHtpbnRlZ2VyfSAgdHJ5Q291bnQgICAgICAgIFRoZSBjb3VudCBob3cgb2Z0ZW4gdGhlIGFqYXggaGFzIGZhaWxlZFxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAgIGZvcm1kYXRhICAgICAgICBUaGUgcmVhZHkgdG8gdXNlIGRhdGEgZnJvbSB0aGUgZm9ybVxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gIGhpc3RvcnlDaGFuZ2UgICBJZiB0cnVlLCB0aGUgaGlzdG9yeSB3aWxsIGJlIHVwZHRlZCBhZnRlciB0aGUgbGlzdCB1cGRhdGUgKGlmIHBvc3NpYmxlKVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGUgPSBmdW5jdGlvbih0cnlDb3VudCwgZm9ybWRhdGEsIGhpc3RvcnlDaGFuZ2UpIHtcblxuXHRcdFx0JHByZWxvYWRlclxuXHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2Vycm9yJylcblx0XHRcdFx0LnNob3coKTtcblxuXHRcdFx0aWYgKGZpbHRlckFqYXgpIHtcblx0XHRcdFx0ZmlsdGVyQWpheC5hYm9ydCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRmaWx0ZXJBamF4ID0ganNlLmxpYnMueGhyLmFqYXgoe1xuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsOiBvcHRpb25zLnJlcXVlc3RVcmwsXG5cdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOiBmb3JtZGF0YVxuXHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sIHRydWUpLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdC8vIFVwZGF0ZSB0aGUgZmlsdGVyYm94IGFuZCBjaGVjayBpZiB0aGUgcHJvZHVjdHMgbmVlZCB0byBiZSB1cGRhdGVkIGF1dG9tYXRpY2FsbHkuXG5cdFx0XHRcdC8vIFRoZSBlbGVtZW50cyB3aWxsIG5lZWQgdG8gYmUgY29udmVydGVkIGFnYWluIHRvIGNoZWNrYm94IHdpZGdldHMsIHNvIHdlIHdpbGwgZmlyc3Rcblx0XHRcdFx0Ly8gc3RvcmUgdGhlbSBpbiBhIGhpZGRlbiBkaXYsIGNvbnZlcnQgdGhlbSBhbmQgdGhlbiBhcHBlbmQgdGhlbSB0byB0aGUgZmlsdGVyIGJveCBcblx0XHRcdFx0Ly8gKGRpcnR5IGZpeCBiZWNhdXNlIGl0IGlzIG5vdCBvdGhlcndpc2UgcG9zc2libGUgd2l0aG91dCBtYWpvciByZWZhY3RvcmluZyAuLi4pXG5cdFx0XHRcdHZhciBjaGVja2JveGVzID0gJChyZXN1bHQuY29udGVudC5maWx0ZXIuc2VsZWN0b3IpXG5cdFx0XHRcdFx0LmZpbmQoJ2lucHV0OmNoZWNrYm94Jylcblx0XHRcdFx0XHRcdC5sZW5ndGgsXG5cdFx0XHRcdFx0JHRhcmdldHMgPSAkKHJlc3VsdC5jb250ZW50LmZpbHRlci5zZWxlY3Rvcik7XG5cblx0XHRcdFx0aWYgKGNoZWNrYm94ZXMpIHtcblxuXHRcdFx0XHRcdHZhciAkaGlkZGVuQ29udGFpbmVyID0gJCgnPGRpdi8+JykuYXBwZW5kVG8oJ2JvZHknKS5oaWRlKCk7XG5cdFx0XHRcdFx0Ly8gQ29weSB0aGUgZWxlbWVudHMgYnV0IGxlYXZlIGEgY2xvbmUgdG8gdGhlIGZpbHRlciBib3ggZWxlbWVudC5cblx0XHRcdFx0XHQkdGhpcy5jaGlsZHJlbigpLmFwcGVuZFRvKCRoaWRkZW5Db250YWluZXIpLmNsb25lKCkuYXBwZW5kVG8oJHRoaXMpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGpzZS5saWJzLnRlbXBsYXRlLmhlbHBlcnMuZmlsbChyZXN1bHQuY29udGVudCwgJGhpZGRlbkNvbnRhaW5lciwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuXHRcdFx0XHRcdGdhbWJpby53aWRnZXRzLmluaXQoJGhpZGRlbkNvbnRhaW5lcik7XG5cblx0XHRcdFx0XHR2YXIgaW50diA9IHNldEludGVydmFsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0aWYgKCRoaWRkZW5Db250YWluZXIuZmluZCgnLnNpbmdsZS1jaGVja2JveCcpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0XHRcdFx0JHRoaXMuY2hpbGRyZW4oKS5yZW1vdmUoKTtcblx0XHRcdFx0XHRcdFx0JGhpZGRlbkNvbnRhaW5lci5jaGlsZHJlbigpLmFwcGVuZFRvKCR0aGlzKTtcblx0XHRcdFx0XHRcdFx0JGhpZGRlbkNvbnRhaW5lci5yZW1vdmUoKTtcblxuXHRcdFx0XHRcdFx0XHQkcHJlbG9hZGVyLmhpZGUoKTtcblx0XHRcdFx0XHRcdFx0aWYgKG9wdGlvbnMuYXV0b1VwZGF0ZSkge1xuXHRcdFx0XHRcdFx0XHRcdF91cGRhdGVQcm9kdWN0cyhoaXN0b3J5Q2hhbmdlKTtcblx0XHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50dik7XG5cdFx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHR9LCAzMDApO1xuXG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkYm9keSwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuXHRcdFx0XHRcdGdhbWJpby53aWRnZXRzLmluaXQoJHRhcmdldHMpO1xuXHRcdFx0XHRcdCRwcmVsb2FkZXIuaGlkZSgpO1xuXG5cdFx0XHRcdFx0aWYgKG9wdGlvbnMuYXV0b1VwZGF0ZSkge1xuXHRcdFx0XHRcdFx0X3VwZGF0ZVByb2R1Y3RzKGhpc3RvcnlDaGFuZ2UpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gcmVpbml0aWFsaXplIHdpZGdldHMgaW4gdXBkYXRlZCBET01cblx0XHRcdFx0d2luZG93LmdhbWJpby53aWRnZXRzLmluaXQoJHRoaXMpO1xuXHRcdFx0XHRcblx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICh0cnlDb3VudCA8IG9wdGlvbnMucmV0cmllcykge1xuXHRcdFx0XHRcdC8vIFJlc3RhcnQgdGhlIHVwZGF0ZSBwcm9jZXNzIGlmIHRoZVxuXHRcdFx0XHRcdC8vIHRyeUNvdW50IGhhc24ndCByZWFjaGVkIHRoZSBtYXhpbXVtXG5cdFx0XHRcdFx0ZXJyb3JUaW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRfdXBkYXRlKHRyeUNvdW50ICsgMSwgZm9ybWRhdGEsIGhpc3RvcnlDaGFuZ2UpO1xuXHRcdFx0XHRcdH0sIG9wdGlvbnMucmV0cnlEZWxheSk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JHByZWxvYWRlci5hZGRDbGFzcygnZXJyb3InKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgc3RhcnRzIHRoZSBmaWx0ZXJcblx0XHQgKiBhbmQgcGFnZSB1cGRhdGUgcHJvY2Vzc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVTdGFydCA9IGZ1bmN0aW9uKGhpc3RvcnlDaGFuZ2UpIHtcblx0XHRcdHZhciBkYXRhc2V0ID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0aGlzKTtcblxuXHRcdFx0aGlzdG9yeUNoYW5nZSA9IChoaXN0b3J5Q2hhbmdlICE9PSB1bmRlZmluZWQpID8gISFoaXN0b3J5Q2hhbmdlIDogdHJ1ZTtcblxuXHRcdFx0X3VwZGF0ZSgwLCBfdHJhbnNmb3JtKGRhdGFzZXQsIHRydWUpLCBoaXN0b3J5Q2hhbmdlKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIFRoZSBzdWJtaXQgZXZlbnQgZ2V0cyBhYm9ydGVkXG5cdFx0ICogaWYgdGhlIGxpdmUgdXBkYXRlIGlzIHNldCB0byB0cnVlLiBFbHNlXG5cdFx0ICogaWYgdGhlIHByb2R1Y3RsaXNpdGluZyBmaWx0ZXIgc2hhbGwgYmVcblx0XHQgKiBrZXB0LCBnZXQgdGhlIHBhcmFtZXRlcnMgZnJvbSBpdCBhbmQgc3RvcmVcblx0XHQgKiB0aGVtIGluIGhpZGRlbiBpbnB1dCBmaWVsZHMgYmVmb3JlIHN1Ym1pdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zdWJtaXRIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0cmVzZXQgPSBmYWxzZTtcblx0XHRcdFxuXHRcdFx0aWYgKG9wdGlvbnMuYXV0b1VwZGF0ZSkge1xuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHR9IGVsc2UgaWYgKCFvcHRpb25zLnJlc2V0UHJvZHVjdGxpc3RpbmdGaWx0ZXIpIHtcblx0XHRcdFx0anNlLmxpYnMuZm9ybS5hZGRIaWRkZW5CeVVybCgkdGhpcyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgdGhhdCBnZXRzIHRyaWdnZXJlZFxuXHRcdCAqIG9uIGV2ZXJ5IGNoYW5nZSBvZiBhbiBpbnB1dCBmaWVsZFxuXHRcdCAqIGluc2lkZSB0aGUgZmlsdGVyIGJveC4gSXQgc3RhcnRzIHRoZVxuXHRcdCAqIHVwZGF0ZSBwcm9jZXNzIGFmdGVyIGEgc2hvcnQgZGVsYXlcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdGNsZWFyVGltZW91dCh1cGRhdGVUaW1lcik7XG5cdFx0XHRjbGVhclRpbWVvdXQoZXJyb3JUaW1lcik7XG5cblx0XHRcdHVwZGF0ZVRpbWVyID0gc2V0VGltZW91dChfdXBkYXRlU3RhcnQsIG9wdGlvbnMudXBkYXRlRGVsYXkpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIHRoYXQgcmVhY3RzIG9uIHRoZSByZXNldFxuXHRcdCAqIGJ1dHRvbiAvIGV2ZW50LiBEZXBlbmRpbmcgb24gdGhlIGF1dG9VcGRhdGVcblx0XHQgKiBzZXR0aW5nIHRoZSBwYWdlIGdldHMgcmVsb2FkZWQgb3IgdGhlIGZvcm1cblx0XHQgKiAvIHByb2R1Y3RzIGdldHMgdXBkYXRlZFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9yZXNldEhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHRqc2UubGlicy5mb3JtLnJlc2V0KCR0aGlzKTtcblx0XHRcdGpzZS5saWJzLmZvcm0uYWRkSGlkZGVuQnlVcmwoJHRoaXMpO1xuXG5cdFx0XHRyZXNldCA9IHRydWU7XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmF1dG9VcGRhdGUpIHtcblx0XHRcdFx0X3VwZGF0ZVN0YXJ0KCk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gbG9jYXRpb24ucGF0aG5hbWUgKyAnPycgKyAkdGhpcy5zZXJpYWxpemUoKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlciB0aGF0IGxpc3RlbnMgb24gdGhlIHBvcHN0YXRlIGV2ZW50LlxuXHRcdCAqIEluIGEgY2FzZSBvZiBhIHBvcHN0YXRlLCB0aGUgZmlsdGVyIHdpbGwgY2hhbmdlXG5cdFx0ICogdG8gaXQncyBwcmV2aW91cyBzdGF0ZSBhbmQgd2lsbCB1cGRhdGUgdGhlIHBhZ2Vcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaGlzdG9yeUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdGpzZS5saWJzLmZvcm0ucmVzZXQoJHRoaXMpO1xuXHRcdFx0anNlLmxpYnMuZm9ybS5wcmVmaWxsRm9ybSgkdGhpcywganNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5nZXRVcmxQYXJhbXMoKSk7XG5cdFx0XHRfdXBkYXRlU3RhcnQoZmFsc2UpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIHRoYXQgbGlzdGVucyBvbiB0aGUgY2xpY2sgZXZlbnRcblx0XHQgKiBvZiBhIFwibW9yZVwiIGJ1dHRvbiB0byBzaG93IGFsbCBmaWx0ZXIgb3B0aW9uc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQodGhpcykucGFyZW50KCkucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXHRcdFx0JCh0aGlzKS5oaWRlKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIHRoYXQgbGlzdGVucyBvbiB0aGUgY2xpY2sgZXZlbnRcblx0XHQgKiBvZiBhIGZpbHRlciBvcHRpb24gbGluayB0byB0cmlnZ2VyIHRoZVxuXHRcdCAqIGNoYW5nZSBldmVudCBvZiB0aGUgYmVsb25naW5nIGhpZGRlbiBjaGVja2JveFxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSBlXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2ZpbHRlckNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciBpZCA9ICQodGhpcykuYXR0cigncmVsJyk7XG5cdFx0XHRcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcblx0XHRcdCQoJyMnICsgaWQpLnByb3AoJ2NoZWNrZWQnLCB0cnVlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHR9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkcHJlbG9hZGVyID0gJHRoaXMuZmluZCgnLnByZWxvYWRlciwgLnByZWxvYWRlci1tZXNzYWdlJyk7XG5cdFx0XHQkY29udGVudFdyYXBwZXIgPSAkKCcubWFpbi1pbnNpZGUnKTtcblx0XHRcdGhpc3RvcnlBdmFpbGFibGUgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdoaXN0b3J5Jyk7XG5cblx0XHRcdC8vIG5vIGF1dG8gdXBkYXRlIG9uIHN0YXJ0IHBhZ2Vcblx0XHRcdGlmKCQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcucHJvZHVjdHNDb250YWluZXIpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRvcHRpb25zLmF1dG9VcGRhdGUgPSBmYWxzZTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdjaGFuZ2UnLCAnc2VsZWN0LCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0sIGlucHV0W3R5cGU9XCJ0ZXh0XCJdJywgX2NoYW5nZUhhbmRsZXIpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLmJ0bi1saW5rJywgX2ZpbHRlckNsaWNrSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdyZXNldCcsIF9yZXNldEhhbmRsZXIpXG5cdFx0XHRcdC5vbignc3VibWl0JywgX3N1Ym1pdEhhbmRsZXIpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLnNob3ctbW9yZScsIF9jbGlja0hhbmRsZXIpO1xuXG5cdFx0XHQkYm9keS5hZGRDbGFzcygnZmlsdGVyYm94LWVuYWJsZWQnKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7Il19
