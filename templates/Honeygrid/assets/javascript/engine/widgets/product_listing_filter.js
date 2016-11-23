'use strict';

/* --------------------------------------------------------------
 product_listing_filter.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component for switching the view and submitting
 * the filter settings on change at the product
 * listing page
 */
gambio.widgets.module('product_listing_filter', ['url_arguments', gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $target = null,
	    $form = null,
	    $hidden = null,
	    $viewmode = null,
	    $pagination = null,
	    historyAvailable = false,
	    transition = {},
	    defaults = {
		target: null // The target the classes getting added (e.g. the product list)
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function to switch the view of the
  * list. If an animation is given in the
  * option execute it
  * @param       {object}    config      Contains the "data" values of the clicked element.
  * @private
  */
	var _switchView = function _switchView(config) {

		// Get all "add" classes from the other buttons
		// to remove them in the next step
		var removeClasses = '';
		$viewmode.find('a').each(function () {
			var $self = $(this),
			    dataset = $self.parseModuleData('product_listing_filter');

			if (config.add !== dataset.add) {
				removeClasses += dataset.add + ' ';
				$self.removeClass('active');
			} else {
				$self.addClass('active');
			}
		});

		// Switch the classes after the fadeout transition finished
		// and then start the fadein animation
		transition.open = false;
		$target.off(jse.libs.template.events.TRANSITION_FINISHED()).one(jse.libs.template.events.TRANSITION_FINISHED(), function () {
			transition.open = true;
			$target.removeClass(removeClasses).addClass(config.add).trigger(jse.libs.template.events.TRANSITION(), transition);
		}).trigger(jse.libs.template.events.TRANSITION(), transition);
	};

	/**
  * Sets the pagination URLs on viewmode
  * change, so that the parameter "view_mode"
  * is set correctly in the URL
  * @param       {string}        mode        The value of the view_mode-parameter
  * @private
  */
	var _setPaginationURLs = function _setPaginationURLs(mode) {
		$pagination.find('a').each(function () {
			var url = $(this).attr('href');
			$(this).attr('href', jse.libs.url_arguments.replaceParameterValue(url, 'view_mode', mode));
		});
	};

	// ########## EVENT HANDLER ##########

	/**
  * Function that gets called if a view change
  * is triggered. It checks the current state of
  * the buttons and siwtches the view if the button
  * state has changed. If the history object is
  * available the viewchange gets logged to that
  * object
  * @param         {object}      e           jQuery event object
  * @private
  */
	var _viewChangeHandler = function _viewChangeHandler(e) {
		// Only prevent the default behaviour
		// if the functions gets called by an event
		// handler
		if (e) {
			e.preventDefault();
		}

		// Get the settings for this button
		var $self = $(this),
		    dataset = $self.parseModuleData('product_listing_filter'),
		    viewMode = dataset.urlParam;

		// Only do something if the state isn't already set
		if (!$self.hasClass('active')) {

			// Close all opened layers
			$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), $this);

			// Add / remove classes
			_switchView(dataset);

			// Update the pagination URLs
			_setPaginationURLs(viewMode);

			// Set the hidden value for the viewmode
			// so that the submit will transfer correct
			// values
			$hidden.val(viewMode);

			// If needed, add an history element
			// (the history parameter is set via the user-click event only)
			if (historyAvailable && e && e.data && e.data.history) {
				var url = jse.libs.url_arguments.replaceParameterValue(location.href, 'view_mode', viewMode);

				history.pushState({ state: viewMode }, viewMode, url);

				// Trigger a pushstate event to notify other widgets
				// about the url change
				$this.trigger('pushstate', { state: viewMode });
			}
		}
	};

	/**
  * Event handler to change the view depending
  * on the history state
  * @param       {object}    e       jQuery event object
  * @param       {object}    d       JSON object that contains the state (if e.originalEvent.state isn't set)
  * @private
  */
	var _historyHandler = function _historyHandler(e, d) {
		var eventData = d || (e.originalEvent ? e.originalEvent : { state: '' }),
		    $button = $viewmode.find('[data-product_listing_filter-url-param="' + eventData.state + '"]');

		if ($button.length && !d.noButton) {
			_viewChangeHandler.call($button);
		} else {
			// Get the settings for this button
			var $activeButton = $this.find('.jsPanelViewmode a.active'),
			    dataset = $activeButton.parseModuleData('product_listing_filter');
			_setPaginationURLs(dataset.urlParam);
		}
	};

	/**
  * Event handler for the submit action
  * on change of the selects
  * @private
  */
	var _changeHandler = function _changeHandler() {
		$form.submit();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$target = $(options.target);
		$form = $this.find('form');
		$hidden = $form.find('input[name="view_mode"]');
		$viewmode = $this.find('.jsPanelViewmode');
		$pagination = $this.find('.pagination');
		historyAvailable = jse.core.config.get('history');
		transition.classClose = 'fadeOut';

		// Replace the current history entry with
		// one with a dataset that represent the
		// current state
		if (historyAvailable) {
			var viewMode = jse.libs.url_arguments.getUrlParameters().view_mode,
			    state = history.state || {},
			    url = jse.libs.url_arguments.replaceParameterValue(location.href, 'view_mode', viewMode);

			state.state = viewMode;
			history.replaceState(state, viewMode, url);
		}

		// Bind listener for user input
		$this.on('change', 'select.jsReload', _changeHandler).on('click', '.jsPanelViewmode a', { history: true }, _viewChangeHandler);

		// Bind event listener to check
		// if the history entry has changed
		$body.on('pushstate pushstate_no_history', _historyHandler);
		$(window).on('popstate', _historyHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiJHRhcmdldCIsIiRmb3JtIiwiJGhpZGRlbiIsIiR2aWV3bW9kZSIsIiRwYWdpbmF0aW9uIiwiaGlzdG9yeUF2YWlsYWJsZSIsInRyYW5zaXRpb24iLCJkZWZhdWx0cyIsInRhcmdldCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc3dpdGNoVmlldyIsImNvbmZpZyIsInJlbW92ZUNsYXNzZXMiLCJmaW5kIiwiZWFjaCIsIiRzZWxmIiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsImFkZCIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJvcGVuIiwib2ZmIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwiZXZlbnRzIiwiVFJBTlNJVElPTl9GSU5JU0hFRCIsIm9uZSIsInRyaWdnZXIiLCJUUkFOU0lUSU9OIiwiX3NldFBhZ2luYXRpb25VUkxzIiwibW9kZSIsInVybCIsImF0dHIiLCJ1cmxfYXJndW1lbnRzIiwicmVwbGFjZVBhcmFtZXRlclZhbHVlIiwiX3ZpZXdDaGFuZ2VIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0Iiwidmlld01vZGUiLCJ1cmxQYXJhbSIsImhhc0NsYXNzIiwiT1BFTl9GTFlPVVQiLCJ2YWwiLCJoaXN0b3J5IiwibG9jYXRpb24iLCJocmVmIiwicHVzaFN0YXRlIiwic3RhdGUiLCJfaGlzdG9yeUhhbmRsZXIiLCJkIiwiZXZlbnREYXRhIiwib3JpZ2luYWxFdmVudCIsIiRidXR0b24iLCJsZW5ndGgiLCJub0J1dHRvbiIsImNhbGwiLCIkYWN0aXZlQnV0dG9uIiwiX2NoYW5nZUhhbmRsZXIiLCJzdWJtaXQiLCJpbml0IiwiZG9uZSIsImNvcmUiLCJnZXQiLCJjbGFzc0Nsb3NlIiwiZ2V0VXJsUGFyYW1ldGVycyIsInZpZXdfbW9kZSIs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QUF1QitCLGdCQUF2QixHQUEwQ0MsU0FEdEQ7QUFBQSxPQUVDbEIsUUFBUUosUUFBUUksS0FBUixJQUFpQixFQUYxQjtBQUFBLE9BR0NoQixNQUFNVixJQUFJQyxJQUFKLENBQVNXLGFBQVQsQ0FBdUJDLHFCQUF2QixDQUE2Q1UsU0FBU0MsSUFBdEQsRUFBNEQsV0FBNUQsRUFBeUVQLFFBQXpFLENBSFA7O0FBS0FTLFNBQU1BLEtBQU4sR0FBY1QsUUFBZDtBQUNBSyxXQUFRdUIsWUFBUixDQUFxQm5CLEtBQXJCLEVBQTRCVCxRQUE1QixFQUFzQ1AsR0FBdEM7QUFDQTs7QUFFRDtBQUNBckMsUUFDRXlFLEVBREYsQ0FDSyxRQURMLEVBQ2UsaUJBRGYsRUFDa0NWLGNBRGxDLEVBRUVVLEVBRkYsQ0FFSyxPQUZMLEVBRWMsb0JBRmQsRUFFb0MsRUFBQ3hCLFNBQVMsSUFBVixFQUZwQyxFQUVxRFIsa0JBRnJEOztBQUlBO0FBQ0E7QUFDQXZDLFFBQU11RSxFQUFOLENBQVMsZ0NBQVQsRUFBMkNuQixlQUEzQztBQUNBckQsSUFBRXlFLE1BQUYsRUFBVUQsRUFBVixDQUFhLFVBQWIsRUFBeUJuQixlQUF6Qjs7QUFFQVk7QUFDQSxFQWxDRDs7QUFvQ0E7QUFDQSxRQUFPckUsTUFBUDtBQUNBLENBOU5GIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9saXN0aW5nX2ZpbHRlci5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgZm9yIHN3aXRjaGluZyB0aGUgdmlldyBhbmQgc3VibWl0dGluZ1xuICogdGhlIGZpbHRlciBzZXR0aW5ncyBvbiBjaGFuZ2UgYXQgdGhlIHByb2R1Y3RcbiAqIGxpc3RpbmcgcGFnZVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdwcm9kdWN0X2xpc3RpbmdfZmlsdGVyJyxcblxuXHRbXG5cdFx0J3VybF9hcmd1bWVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkdGFyZ2V0ID0gbnVsbCxcblx0XHRcdCRmb3JtID0gbnVsbCxcblx0XHRcdCRoaWRkZW4gPSBudWxsLFxuXHRcdFx0JHZpZXdtb2RlID0gbnVsbCxcblx0XHRcdCRwYWdpbmF0aW9uID0gbnVsbCxcblx0XHRcdGhpc3RvcnlBdmFpbGFibGUgPSBmYWxzZSxcblx0XHRcdHRyYW5zaXRpb24gPSB7fSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHR0YXJnZXQ6IG51bGwgICAgICAgIC8vIFRoZSB0YXJnZXQgdGhlIGNsYXNzZXMgZ2V0dGluZyBhZGRlZCAoZS5nLiB0aGUgcHJvZHVjdCBsaXN0KVxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBzd2l0Y2ggdGhlIHZpZXcgb2YgdGhlXG5cdFx0ICogbGlzdC4gSWYgYW4gYW5pbWF0aW9uIGlzIGdpdmVuIGluIHRoZVxuXHRcdCAqIG9wdGlvbiBleGVjdXRlIGl0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGNvbmZpZyAgICAgIENvbnRhaW5zIHRoZSBcImRhdGFcIiB2YWx1ZXMgb2YgdGhlIGNsaWNrZWQgZWxlbWVudC5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc3dpdGNoVmlldyA9IGZ1bmN0aW9uKGNvbmZpZykge1xuXG5cdFx0XHQvLyBHZXQgYWxsIFwiYWRkXCIgY2xhc3NlcyBmcm9tIHRoZSBvdGhlciBidXR0b25zXG5cdFx0XHQvLyB0byByZW1vdmUgdGhlbSBpbiB0aGUgbmV4dCBzdGVwXG5cdFx0XHR2YXIgcmVtb3ZlQ2xhc3NlcyA9ICcnO1xuXHRcdFx0JHZpZXdtb2RlXG5cdFx0XHRcdC5maW5kKCdhJylcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInKTtcblxuXHRcdFx0XHRcdGlmIChjb25maWcuYWRkICE9PSBkYXRhc2V0LmFkZCkge1xuXHRcdFx0XHRcdFx0cmVtb3ZlQ2xhc3NlcyArPSBkYXRhc2V0LmFkZCArICcgJztcblx0XHRcdFx0XHRcdCRzZWxmLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0JHNlbGYuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cblx0XHRcdC8vIFN3aXRjaCB0aGUgY2xhc3NlcyBhZnRlciB0aGUgZmFkZW91dCB0cmFuc2l0aW9uIGZpbmlzaGVkXG5cdFx0XHQvLyBhbmQgdGhlbiBzdGFydCB0aGUgZmFkZWluIGFuaW1hdGlvblxuXHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG5cdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdC5vZmYoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSlcblx0XHRcdFx0Lm9uZShqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTl9GSU5JU0hFRCgpLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuXHRcdFx0XHRcdCR0YXJnZXRcblx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcyhyZW1vdmVDbGFzc2VzKVxuXHRcdFx0XHRcdFx0LmFkZENsYXNzKGNvbmZpZy5hZGQpXG5cdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogU2V0cyB0aGUgcGFnaW5hdGlvbiBVUkxzIG9uIHZpZXdtb2RlXG5cdFx0ICogY2hhbmdlLCBzbyB0aGF0IHRoZSBwYXJhbWV0ZXIgXCJ2aWV3X21vZGVcIlxuXHRcdCAqIGlzIHNldCBjb3JyZWN0bHkgaW4gdGhlIFVSTFxuXHRcdCAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICAgICAgbW9kZSAgICAgICAgVGhlIHZhbHVlIG9mIHRoZSB2aWV3X21vZGUtcGFyYW1ldGVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NldFBhZ2luYXRpb25VUkxzID0gZnVuY3Rpb24obW9kZSkge1xuXHRcdFx0JHBhZ2luYXRpb25cblx0XHRcdFx0LmZpbmQoJ2EnKVxuXHRcdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR2YXIgdXJsID0gJCh0aGlzKS5hdHRyKCdocmVmJyk7IFxuXHRcdFx0XHRcdCQodGhpcykuYXR0cignaHJlZicsIGpzZS5saWJzLnVybF9hcmd1bWVudHMucmVwbGFjZVBhcmFtZXRlclZhbHVlKHVybCwgJ3ZpZXdfbW9kZScsIG1vZGUpKTtcblx0XHRcdFx0fSk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBnZXRzIGNhbGxlZCBpZiBhIHZpZXcgY2hhbmdlXG5cdFx0ICogaXMgdHJpZ2dlcmVkLiBJdCBjaGVja3MgdGhlIGN1cnJlbnQgc3RhdGUgb2Zcblx0XHQgKiB0aGUgYnV0dG9ucyBhbmQgc2l3dGNoZXMgdGhlIHZpZXcgaWYgdGhlIGJ1dHRvblxuXHRcdCAqIHN0YXRlIGhhcyBjaGFuZ2VkLiBJZiB0aGUgaGlzdG9yeSBvYmplY3QgaXNcblx0XHQgKiBhdmFpbGFibGUgdGhlIHZpZXdjaGFuZ2UgZ2V0cyBsb2dnZWQgdG8gdGhhdFxuXHRcdCAqIG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICAgIHtvYmplY3R9ICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF92aWV3Q2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdC8vIE9ubHkgcHJldmVudCB0aGUgZGVmYXVsdCBiZWhhdmlvdXJcblx0XHRcdC8vIGlmIHRoZSBmdW5jdGlvbnMgZ2V0cyBjYWxsZWQgYnkgYW4gZXZlbnRcblx0XHRcdC8vIGhhbmRsZXJcblx0XHRcdGlmIChlKSB7XG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdH1cblxuXHRcdFx0Ly8gR2V0IHRoZSBzZXR0aW5ncyBmb3IgdGhpcyBidXR0b25cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ3Byb2R1Y3RfbGlzdGluZ19maWx0ZXInKSxcblx0XHRcdFx0dmlld01vZGUgPSBkYXRhc2V0LnVybFBhcmFtO1xuXG5cdFx0XHQvLyBPbmx5IGRvIHNvbWV0aGluZyBpZiB0aGUgc3RhdGUgaXNuJ3QgYWxyZWFkeSBzZXRcblx0XHRcdGlmICghJHNlbGYuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG5cblx0XHRcdFx0Ly8gQ2xvc2UgYWxsIG9wZW5lZCBsYXllcnNcblx0XHRcdFx0JHRoaXMudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgJHRoaXMpO1xuXG5cdFx0XHRcdC8vIEFkZCAvIHJlbW92ZSBjbGFzc2VzXG5cdFx0XHRcdF9zd2l0Y2hWaWV3KGRhdGFzZXQpO1xuXG5cdFx0XHRcdC8vIFVwZGF0ZSB0aGUgcGFnaW5hdGlvbiBVUkxzXG5cdFx0XHRcdF9zZXRQYWdpbmF0aW9uVVJMcyh2aWV3TW9kZSk7XG5cblx0XHRcdFx0Ly8gU2V0IHRoZSBoaWRkZW4gdmFsdWUgZm9yIHRoZSB2aWV3bW9kZVxuXHRcdFx0XHQvLyBzbyB0aGF0IHRoZSBzdWJtaXQgd2lsbCB0cmFuc2ZlciBjb3JyZWN0XG5cdFx0XHRcdC8vIHZhbHVlc1xuXHRcdFx0XHQkaGlkZGVuLnZhbCh2aWV3TW9kZSk7XG5cblx0XHRcdFx0Ly8gSWYgbmVlZGVkLCBhZGQgYW4gaGlzdG9yeSBlbGVtZW50XG5cdFx0XHRcdC8vICh0aGUgaGlzdG9yeSBwYXJhbWV0ZXIgaXMgc2V0IHZpYSB0aGUgdXNlci1jbGljayBldmVudCBvbmx5KVxuXHRcdFx0XHRpZiAoaGlzdG9yeUF2YWlsYWJsZSAmJiBlICYmIGUuZGF0YSAmJiBlLmRhdGEuaGlzdG9yeSkge1xuXHRcdFx0XHRcdHZhciB1cmwgPSBqc2UubGlicy51cmxfYXJndW1lbnRzLnJlcGxhY2VQYXJhbWV0ZXJWYWx1ZShsb2NhdGlvbi5ocmVmLCAndmlld19tb2RlJywgdmlld01vZGUpOyBcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRoaXN0b3J5LnB1c2hTdGF0ZSh7c3RhdGU6IHZpZXdNb2RlfSwgdmlld01vZGUsIHVybCk7XG5cblx0XHRcdFx0XHQvLyBUcmlnZ2VyIGEgcHVzaHN0YXRlIGV2ZW50IHRvIG5vdGlmeSBvdGhlciB3aWRnZXRzXG5cdFx0XHRcdFx0Ly8gYWJvdXQgdGhlIHVybCBjaGFuZ2Vcblx0XHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdwdXNoc3RhdGUnLCB7c3RhdGU6IHZpZXdNb2RlfSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciB0byBjaGFuZ2UgdGhlIHZpZXcgZGVwZW5kaW5nXG5cdFx0ICogb24gdGhlIGhpc3Rvcnkgc3RhdGVcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGQgICAgICAgSlNPTiBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgc3RhdGUgKGlmIGUub3JpZ2luYWxFdmVudC5zdGF0ZSBpc24ndCBzZXQpXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2hpc3RvcnlIYW5kbGVyID0gZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0dmFyIGV2ZW50RGF0YSA9IGQgfHwgKGUub3JpZ2luYWxFdmVudCA/IGUub3JpZ2luYWxFdmVudCA6IHtzdGF0ZTogJyd9KSxcblx0XHRcdFx0JGJ1dHRvbiA9ICR2aWV3bW9kZS5maW5kKCdbZGF0YS1wcm9kdWN0X2xpc3RpbmdfZmlsdGVyLXVybC1wYXJhbT1cIicgKyBldmVudERhdGEuc3RhdGUgKyAnXCJdJyk7XG5cblx0XHRcdGlmICgkYnV0dG9uLmxlbmd0aCAmJiAhZC5ub0J1dHRvbikge1xuXHRcdFx0XHRfdmlld0NoYW5nZUhhbmRsZXIuY2FsbCgkYnV0dG9uKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdC8vIEdldCB0aGUgc2V0dGluZ3MgZm9yIHRoaXMgYnV0dG9uXG5cdFx0XHRcdHZhciAkYWN0aXZlQnV0dG9uID0gJHRoaXMuZmluZCgnLmpzUGFuZWxWaWV3bW9kZSBhLmFjdGl2ZScpLFxuXHRcdFx0XHRcdGRhdGFzZXQgPSAkYWN0aXZlQnV0dG9uLnBhcnNlTW9kdWxlRGF0YSgncHJvZHVjdF9saXN0aW5nX2ZpbHRlcicpOyBcblx0XHRcdFx0X3NldFBhZ2luYXRpb25VUkxzKGRhdGFzZXQudXJsUGFyYW0pO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgc3VibWl0IGFjdGlvblxuXHRcdCAqIG9uIGNoYW5nZSBvZiB0aGUgc2VsZWN0c1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jaGFuZ2VIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRcdCR0YXJnZXQgPSAkKG9wdGlvbnMudGFyZ2V0KTtcblx0XHRcdCRmb3JtID0gJHRoaXMuZmluZCgnZm9ybScpO1xuXHRcdFx0JGhpZGRlbiA9ICRmb3JtLmZpbmQoJ2lucHV0W25hbWU9XCJ2aWV3X21vZGVcIl0nKTtcblx0XHRcdCR2aWV3bW9kZSA9ICR0aGlzLmZpbmQoJy5qc1BhbmVsVmlld21vZGUnKTtcblx0XHRcdCRwYWdpbmF0aW9uID0gJHRoaXMuZmluZCgnLnBhZ2luYXRpb24nKTtcblx0XHRcdGhpc3RvcnlBdmFpbGFibGUgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdoaXN0b3J5Jyk7XG5cdFx0XHR0cmFuc2l0aW9uLmNsYXNzQ2xvc2UgPSAnZmFkZU91dCc7XG5cblx0XHRcdC8vIFJlcGxhY2UgdGhlIGN1cnJlbnQgaGlzdG9yeSBlbnRyeSB3aXRoXG5cdFx0XHQvLyBvbmUgd2l0aCBhIGRhdGFzZXQgdGhhdCByZXByZXNlbnQgdGhlXG5cdFx0XHQvLyBjdXJyZW50IHN0YXRlXG5cdFx0XHRpZiAoaGlzdG9yeUF2YWlsYWJsZSkge1xuXHRcdFx0XHR2YXIgXG5cdFx0XHRcdFx0dmlld01vZGUgPSBqc2UubGlicy51cmxfYXJndW1lbnRzLmdldFVybFBhcmFtZXRlcnMoKS52aWV3X21vZGUsXG5cdFx0XHRcdFx0c3RhdGUgPSBoaXN0b3J5LnN0YXRlIHx8IHt9LFxuXHRcdFx0XHRcdHVybCA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMucmVwbGFjZVBhcmFtZXRlclZhbHVlKGxvY2F0aW9uLmhyZWYsICd2aWV3X21vZGUnLCB2aWV3TW9kZSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRzdGF0ZS5zdGF0ZSA9IHZpZXdNb2RlO1xuXHRcdFx0XHRoaXN0b3J5LnJlcGxhY2VTdGF0ZShzdGF0ZSwgdmlld01vZGUsIHVybCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIEJpbmQgbGlzdGVuZXIgZm9yIHVzZXIgaW5wdXRcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignY2hhbmdlJywgJ3NlbGVjdC5qc1JlbG9hZCcsIF9jaGFuZ2VIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5qc1BhbmVsVmlld21vZGUgYScsIHtoaXN0b3J5OiB0cnVlfSwgX3ZpZXdDaGFuZ2VIYW5kbGVyKTtcblxuXHRcdFx0Ly8gQmluZCBldmVudCBsaXN0ZW5lciB0byBjaGVja1xuXHRcdFx0Ly8gaWYgdGhlIGhpc3RvcnkgZW50cnkgaGFzIGNoYW5nZWRcblx0XHRcdCRib2R5Lm9uKCdwdXNoc3RhdGUgcHVzaHN0YXRlX25vX2hpc3RvcnknLCBfaGlzdG9yeUhhbmRsZXIpO1xuXHRcdFx0JCh3aW5kb3cpLm9uKCdwb3BzdGF0ZScsIF9oaXN0b3J5SGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pOyJdfQ==
