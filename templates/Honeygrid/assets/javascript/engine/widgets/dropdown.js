'use strict';

/* --------------------------------------------------------------
 dropdown.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component to replace the default browser select
 * boxes with a more stylish html / css one
 */
gambio.widgets.module('dropdown', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    transition = {},
	    defaults = {
		// Minimum breakpoint to switch to mobile view
		breakpoint: 40,
		// Container selector for the dropdown markup to look for
		container: '.custom-dropdown',
		// Class that gets added to opened flyouts (@ the container)
		openClass: 'open',
		// If true, the currently selected item gets hidden from the flyout
		hideActive: true,
		// Shortens the text shown in the button. Possible values: Any type of integer, null for do nothing
		shorten: 10,

		// or "fit" for autodetect length depending on the button size (only works with fixed with buttons)

		// Shortens the text inside the button on component init
		shortenOnInit: false,
		// If true the label will get shortened on mobile too
		shortenOnMobile: false,
		// If true, a change of the selectbox by the flyout is receipted trough a change trigger
		triggerChange: true,
		// If true, a change is triggered on no change of the selectbox also
		triggerNoChange: false
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function that hides the currently active
  * element from the dropdown
  * @param       {object}        $container      jQuery selection of the dropdown container
  * @param       {object}        opt             JSON with custom settings for that container
  * @private
  */
	var _hideActive = function _hideActive($container, opt) {
		if (opt.hideActive) {
			var $select = $container.children('select'),
			    value = $select.children(':selected').val();

			$container.find('li').show().children('a[rel="' + value + '"]').parent().hide();
		}
	};

	/**
  * Helper function to add a disabled class to the
  * disabled entries in the custom dropdown. Therefor
  * the original select is scanned for disabled entries
  * @param       {object}        $container      jQuery selection of the dropdown container
  * @private
  */
	var _setDisabled = function _setDisabled($container) {
		var $ul = $container.children(),
		    $select = $container.children('select'),
		    $disabled = $select.children(':disabled');

		// Remove all disabled classes first
		$ul.find('.disabled').removeClass('disabled');

		// Iterate through all entries that needs to
		// be disabled and add a class to them
		$disabled.each(function () {
			var $self = $(this),
			    value = $self.val();

			$ul.find('a[rel="' + value + '"]').parent().addClass('disabled');
		});
	};

	/**
  * Helper function for the _shortenLabel-function.
  * This function shortens the label so that it fits
  * inside the button. Additional available siblings
  * of the text element were getting substracted from
  * the available button size.
  * @param       {object}    $button     jQuery selection of the button
  * @param       {string}    value       The value that should be set as the button text
  * @return     {string}                The shortened string
  * @private
  */
	var _shortenFit = function _shortenFit($button, value) {
		var $siblings = $button.children().not('.dropdown-name'),
		    $textarea = $button.children('.dropdown-name'),
		    width = $button.width(),
		    length = value.length,
		    name = '',
		    shorten = false,
		    i = 0,
		    test = null;

		// Remove the siblings with from the available
		// full width of the button
		$siblings.each(function () {
			width -= $(this).outerWidth();
		});

		// Iterate through the label characters
		// and add one character at time to the button
		// if the textfield size grows larger than
		// the available width of the button cancel
		// the loop and take the last fitting value
		// as result
		for (i; i < length; i += 1) {
			test = value.substring(0, i) + '...';
			$textarea.text(test);

			if ($textarea.width() > width) {
				shorten = true;
				break;
			}

			name = test;
		}

		// If the text was shortened
		// return the shortened name
		// else the full name
		if (shorten) {
			return name;
		}
		return value;
	};

	/**
  * Helper function for the _shortenLabel-function.
  * This function shortens the label to a set number
  * of digets
  * @param       {string}    value       The value that should be set as the button text
  * @param       {object}    opt         JSON with custom settings for that container
  * @return     {string}                The shortened string
  * @private
  */
	var _shortenInt = function _shortenInt(value, opt) {
		var length = value.length,
		    diff = length - opt.shorten;

		if (diff > 0) {
			diff += 3;
			return value.substring(0, length - diff) + '...';
		}

		return value;
	};

	/**
  * Function that chooses the correct shortener
  * subroutine for shortening the button text
  * (if needed) and returns the shortened value
  * to the caller
  * @param       {object}    $button     jQuery selection of the button
  * @param       {string}    value       The value that should be set as the button text
  * @param       {object}    opt         JSON with custom settings for that container
  * @return     {string}                The shortened string
  * @private
  */
	var _shortenLabel = function _shortenLabel($button, value, opt) {
		if (options.breakpoint < jse.libs.template.responsive.breakpoint().id || opt.shortenOnMobile) {
			if (opt.shorten === 'fit') {
				value = _shortenFit($button, value);
			} else if (opt.shorten) {
				value = _shortenInt(value, opt);
			}
		}

		return value;
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler that ist triggered on change
  * of the selectbox to force the dropdown to close
  * (needed on mobile devices, because of it's native
  * support for dropdowns)
  * @private
  */
	var _closeLayer = function _closeLayer() {
		var $self = $(this),
		    $container = $self.closest(options.container),
		    $select = $container.children('select'),
		    dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

		transition.open = false;
		$container.trigger(jse.libs.template.events.TRANSITION(), transition);

		// Trigger the change event if the option is set
		if (dataset.triggerNoChange) {
			$select.trigger('change', []);
		}
	};

	/**
  * Function gets triggered on click on the button.
  * It switches the state of the dropdown visibility
  * @param           {object}    e       jQuery event object
  * @private
  */
	var _openLayer = function _openLayer(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
		    $container = $self.closest(options.container),
		    $select = $container.children('select'),
		    dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

		if ($container.hasClass(options.openClass)) {
			// Remove the open class if the layer is opened
			transition.open = false;
			$container.trigger(jse.libs.template.events.TRANSITION(), transition);

			// Trigger the change event if the option is set
			if (dataset.triggerNoChange) {
				$select.trigger('change', []);
			}
		} else {
			// Add the open class and inform other layers to close
			_hideActive($container, dataset);
			_setDisabled($container);

			transition.open = true;
			$container.trigger(jse.libs.template.events.TRANSITION(), transition);
			$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), [$container]);
		}
	};

	/**
  * Handler that gets used if the user
  * selects a value from the custom dropdown.
  * If the value has changed, the view gets
  * updated and the original select gets set
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _selectEntry = function _selectEntry(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
		    $li = $self.parent();

		// If the item is disabled, do nothing
		if (!$li.hasClass('disabled')) {

			var $container = $self.closest(options.container),
			    $button = $container.children('button'),
			    $select = $container.children('select'),
			    oldValue = $select.children(':selected').val(),
			    newValue = $self.attr('rel'),
			    name = $self.text(),
			    dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

			// Update the dropdown view if the
			// value has changed
			if (oldValue !== newValue) {
				// Set the button text
				var shortened = _shortenLabel($button, name, dataset);
				$button.children('.dropdown-name').text(shortened);

				// Set the "original" select box and
				// notify the browser / other js that the
				// value has changed
				$select.children('[value="' + newValue + '"]').prop('selected', true);

				// Trigger the change event if the option is set
				if (dataset.triggerChange) {
					$select.trigger('change', []);
				}
			} else if (dataset.triggerNoChange) {
				// Trigger the change event if the option is set
				$select.trigger('change', []);
			}

			// Close the layer
			transition.open = false;
			$container.trigger(jse.libs.template.events.TRANSITION(), transition);
		}
	};

	/**
  * Handles the switch between the breakpoint. If the
  * size of the button changes the text will be shortened
  * again to fit. If the view switches to mobile, this
  * behaviour is skipped the full name will be displayed
  * again
  * @private
  */
	var _breakpointHandler = function _breakpointHandler() {
		var $container = $this.find(options.container);

		if (options.breakpoint < jse.libs.template.responsive.breakpoint().id || options.shortenOnMobile) {
			// If still in desktop mode, try to shorten the name
			$container.each(function () {
				var $self = $(this),
				    $button = $self.children('button'),
				    $textarea = $button.children('.dropdown-name'),
				    value = $self.find('select option:selected').text(),
				    dataset = $.extend({}, options, $self.parseModuleData('dropdown')),
				    shortened = _shortenLabel($button, value, dataset);

				$textarea.text(shortened);
			});
		} else {
			// If in mobile mode insert the complete name again
			// and close opened layers
			$container.removeClass(options.openClass).each(function () {
				var $self = $(this),
				    $textarea = $self.find('.dropdown-name'),
				    value = $self.find('select option:selected').text();

				$textarea.text(value);
			});
		}
	};

	/**
  * Handler for closing all dropdown flyouts if
  * somewhere on the page opens an other flyout
  * @param   {object}    e       jQuery event object
  * @param   {object}    d       jQuery selection of the event emitter
  * @private
  */
	var _closeFlyout = function _closeFlyout(e, d) {
		var $containers = $this.find(options.container),
		    $exclude = d || $(e.target).closest(options.openClass);

		$containers = $containers.not($exclude);
		$containers.removeClass(options.openClass);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		transition.classOpen = options.openClass;

		$body.on(jse.libs.template.events.OPEN_FLYOUT() + ' click', _closeFlyout).on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

		$this.on('click', options.container + ' button', _openLayer).on('click', options.container + ' ul a', _selectEntry).on('change', options.container + ' select', _closeLayer);

		if (options.shortenOnInit) {
			_breakpointHandler();
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZHJvcGRvd24uanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsInRyYW5zaXRpb24iLCJkZWZhdWx0cyIsImJyZWFrcG9pbnQiLCJjb250YWluZXIiLCJvcGVuQ2xhc3MiLCJoaWRlQWN0aXZlIiwic2hvcnRlbiIsInNob3J0ZW5PbkluaXQiLCJzaG9ydGVuT25Nb2JpbGUiLCJ0cmlnZ2VyQ2hhbmdlIiwidHJpZ2dlck5vQ2hhbmdlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9oaWRlQWN0aXZlIiwiJGNvbnRhaW5lciIsIm9wdCIsIiRzZWxlY3QiLCJjaGlsZHJlbiIsInZhbHVlIiwidmFsIiwiZmluZCIsInNob3ciLCJwYXJlbnQiLCJoaWRlIiwiX3NldERpc2FibGVkIiwiJHVsIiwiJGRpc2FibGVkIiwicmVtb3ZlQ2xhc3MiLCJlYWNoIiwiJHNlbGYiLCJhZGRDbGFzcyIsIl9zaG9ydGVuRml0IiwiJGJ1dHRvbiIsIiRzaWJsaW5ncyIsIm5vdCIsIiR0ZXh0YXJlYSIsIndpZHRoIiwibGVuZ3RoIiwibmFtZSIsImkiLCJ0ZXN0Iiwib3V0ZXJXaWR0aCIsInN1YnN0cmluZyIsInRleHQiLCJfc2hvcnRlbkludCIsImRpZmYiLCJfc2hvcnRlbkxhYmVsIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwicmVzcG9uc2l2ZSIsImlkIiwiX2Nsb3NlTGF5ZXIiLCJjbG9zZXN0IiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsIm9wZW4iLCJ0cmlnZ2VyIiwiZXZlbnRzIiwiVFJBTlNJVElPTiIsIl9vcGVuTGF5ZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJoYXNDbGFzcyIsIk9QRU5fRkxZT1VUIiwiX3NlbGVjdEVudHJ5IiwiJGxpIiwib2xkVmFsdWUiLCJuZXdWYWx1ZSIsImF0dHIiLCJzaG9ydGVuZWQiLCJwcm9wIiwiX2JyZWFrcG9pbnRIYW5kbGVyIiwiX2Nsb3NlRmx5b3V0IiwiZCIsIiRjb250YWluZXJzIiwiJGV4Y2x1ZGUiLCJ0YXJnZXQiLCJpbml0IiwiZG9uZSIsImNsYXNzT3BlbiIsIm9uIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFVBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLEVBRUNILE9BQU9HLE1BQVAsR0FBZ0Isa0JBRmpCLENBSEQsRUFRQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxRQUFRRCxFQUFFLE1BQUYsQ0FEVDtBQUFBLEtBRUNFLGFBQWEsRUFGZDtBQUFBLEtBR0NDLFdBQVc7QUFDVjtBQUNBQyxjQUFZLEVBRkY7QUFHVjtBQUNBQyxhQUFXLGtCQUpEO0FBS1Y7QUFDQUMsYUFBVyxNQU5EO0FBT1Y7QUFDQUMsY0FBWSxJQVJGO0FBU1Y7QUFDQUMsV0FBUyxFQVZDOztBQVlWOztBQUVBO0FBQ0FDLGlCQUFlLEtBZkw7QUFnQlY7QUFDQUMsbUJBQWlCLEtBakJQO0FBa0JWO0FBQ0FDLGlCQUFlLElBbkJMO0FBb0JWO0FBQ0FDLG1CQUFpQjtBQXJCUCxFQUhaO0FBQUEsS0EwQkNDLFVBQVViLEVBQUVjLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQlgsUUFBbkIsRUFBNkJMLElBQTdCLENBMUJYO0FBQUEsS0EyQkNGLFNBQVMsRUEzQlY7O0FBOEJGOztBQUVFOzs7Ozs7O0FBT0EsS0FBSW1CLGNBQWMsU0FBZEEsV0FBYyxDQUFTQyxVQUFULEVBQXFCQyxHQUFyQixFQUEwQjtBQUMzQyxNQUFJQSxJQUFJVixVQUFSLEVBQW9CO0FBQ25CLE9BQUlXLFVBQVVGLFdBQ1pHLFFBRFksQ0FDSCxRQURHLENBQWQ7QUFBQSxPQUVDQyxRQUFRRixRQUNOQyxRQURNLENBQ0csV0FE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UUFBUVIsU0FBUixHQUFvQixTQUhuQyxFQUc4Q2lELFdBSDlDOztBQUtBLE1BQUl6QyxRQUFRSixhQUFaLEVBQTJCO0FBQzFCa0U7QUFDQTs7QUFFRE87QUFDQSxFQWxCRDs7QUFvQkE7QUFDQSxRQUFPdEYsTUFBUDtBQUNBLENBN1lGIiwiZmlsZSI6IndpZGdldHMvZHJvcGRvd24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRyb3Bkb3duLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIENvbXBvbmVudCB0byByZXBsYWNlIHRoZSBkZWZhdWx0IGJyb3dzZXIgc2VsZWN0XG4gKiBib3hlcyB3aXRoIGEgbW9yZSBzdHlsaXNoIGh0bWwgLyBjc3Mgb25lXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J2Ryb3Bkb3duJyxcblxuXHRbXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvcmVzcG9uc2l2ZSdcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCRib2R5ID0gJCgnYm9keScpLFxuXHRcdFx0dHJhbnNpdGlvbiA9IHt9LFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdC8vIE1pbmltdW0gYnJlYWtwb2ludCB0byBzd2l0Y2ggdG8gbW9iaWxlIHZpZXdcblx0XHRcdFx0YnJlYWtwb2ludDogNDAsXG5cdFx0XHRcdC8vIENvbnRhaW5lciBzZWxlY3RvciBmb3IgdGhlIGRyb3Bkb3duIG1hcmt1cCB0byBsb29rIGZvclxuXHRcdFx0XHRjb250YWluZXI6ICcuY3VzdG9tLWRyb3Bkb3duJyxcblx0XHRcdFx0Ly8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIG9wZW5lZCBmbHlvdXRzIChAIHRoZSBjb250YWluZXIpXG5cdFx0XHRcdG9wZW5DbGFzczogJ29wZW4nLFxuXHRcdFx0XHQvLyBJZiB0cnVlLCB0aGUgY3VycmVudGx5IHNlbGVjdGVkIGl0ZW0gZ2V0cyBoaWRkZW4gZnJvbSB0aGUgZmx5b3V0XG5cdFx0XHRcdGhpZGVBY3RpdmU6IHRydWUsXG5cdFx0XHRcdC8vIFNob3J0ZW5zIHRoZSB0ZXh0IHNob3duIGluIHRoZSBidXR0b24uIFBvc3NpYmxlIHZhbHVlczogQW55IHR5cGUgb2YgaW50ZWdlciwgbnVsbCBmb3IgZG8gbm90aGluZ1xuXHRcdFx0XHRzaG9ydGVuOiAxMCxcblxuXHRcdFx0XHQvLyBvciBcImZpdFwiIGZvciBhdXRvZGV0ZWN0IGxlbmd0aCBkZXBlbmRpbmcgb24gdGhlIGJ1dHRvbiBzaXplIChvbmx5IHdvcmtzIHdpdGggZml4ZWQgd2l0aCBidXR0b25zKVxuXG5cdFx0XHRcdC8vIFNob3J0ZW5zIHRoZSB0ZXh0IGluc2lkZSB0aGUgYnV0dG9uIG9uIGNvbXBvbmVudCBpbml0XG5cdFx0XHRcdHNob3J0ZW5PbkluaXQ6IGZhbHNlLFxuXHRcdFx0XHQvLyBJZiB0cnVlIHRoZSBsYWJlbCB3aWxsIGdldCBzaG9ydGVuZWQgb24gbW9iaWxlIHRvb1xuXHRcdFx0XHRzaG9ydGVuT25Nb2JpbGU6IGZhbHNlLFxuXHRcdFx0XHQvLyBJZiB0cnVlLCBhIGNoYW5nZSBvZiB0aGUgc2VsZWN0Ym94IGJ5IHRoZSBmbHlvdXQgaXMgcmVjZWlwdGVkIHRyb3VnaCBhIGNoYW5nZSB0cmlnZ2VyXG5cdFx0XHRcdHRyaWdnZXJDaGFuZ2U6IHRydWUsXG5cdFx0XHRcdC8vIElmIHRydWUsIGEgY2hhbmdlIGlzIHRyaWdnZXJlZCBvbiBubyBjaGFuZ2Ugb2YgdGhlIHNlbGVjdGJveCBhbHNvXG5cdFx0XHRcdHRyaWdnZXJOb0NoYW5nZTogZmFsc2Vcblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBoaWRlcyB0aGUgY3VycmVudGx5IGFjdGl2ZVxuXHRcdCAqIGVsZW1lbnQgZnJvbSB0aGUgZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgICRjb250YWluZXIgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBkcm9wZG93biBjb250YWluZXJcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIG9wdCAgICAgICAgICAgICBKU09OIHdpdGggY3VzdG9tIHNldHRpbmdzIGZvciB0aGF0IGNvbnRhaW5lclxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9oaWRlQWN0aXZlID0gZnVuY3Rpb24oJGNvbnRhaW5lciwgb3B0KSB7XG5cdFx0XHRpZiAob3B0LmhpZGVBY3RpdmUpIHtcblx0XHRcdFx0dmFyICRzZWxlY3QgPSAkY29udGFpbmVyXG5cdFx0XHRcdFx0LmNoaWxkcmVuKCdzZWxlY3QnKSxcblx0XHRcdFx0XHR2YWx1ZSA9ICRzZWxlY3Rcblx0XHRcdFx0XHRcdC5jaGlsZHJlbignOnNlbGVjdGVkJylcblx0XHRcdFx0XHRcdC52YWwoKTtcblxuXHRcdFx0XHQkY29udGFpbmVyXG5cdFx0XHRcdFx0LmZpbmQoJ2xpJylcblx0XHRcdFx0XHQuc2hvdygpXG5cdFx0XHRcdFx0LmNoaWxkcmVuKCdhW3JlbD1cIicgKyB2YWx1ZSArICdcIl0nKVxuXHRcdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBhZGQgYSBkaXNhYmxlZCBjbGFzcyB0byB0aGVcblx0XHQgKiBkaXNhYmxlZCBlbnRyaWVzIGluIHRoZSBjdXN0b20gZHJvcGRvd24uIFRoZXJlZm9yXG5cdFx0ICogdGhlIG9yaWdpbmFsIHNlbGVjdCBpcyBzY2FubmVkIGZvciBkaXNhYmxlZCBlbnRyaWVzXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICAkY29udGFpbmVyICAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgZHJvcGRvd24gY29udGFpbmVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NldERpc2FibGVkID0gZnVuY3Rpb24oJGNvbnRhaW5lcikge1xuXHRcdFx0dmFyICR1bCA9ICRjb250YWluZXIuY2hpbGRyZW4oKSxcblx0XHRcdFx0JHNlbGVjdCA9ICRjb250YWluZXIuY2hpbGRyZW4oJ3NlbGVjdCcpLFxuXHRcdFx0XHQkZGlzYWJsZWQgPSAkc2VsZWN0LmNoaWxkcmVuKCc6ZGlzYWJsZWQnKTtcblxuXHRcdFx0Ly8gUmVtb3ZlIGFsbCBkaXNhYmxlZCBjbGFzc2VzIGZpcnN0XG5cdFx0XHQkdWxcblx0XHRcdFx0LmZpbmQoJy5kaXNhYmxlZCcpXG5cdFx0XHRcdC5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuXHRcdFx0Ly8gSXRlcmF0ZSB0aHJvdWdoIGFsbCBlbnRyaWVzIHRoYXQgbmVlZHMgdG9cblx0XHRcdC8vIGJlIGRpc2FibGVkIGFuZCBhZGQgYSBjbGFzcyB0byB0aGVtXG5cdFx0XHQkZGlzYWJsZWQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHR2YWx1ZSA9ICRzZWxmLnZhbCgpO1xuXG5cdFx0XHRcdCR1bFxuXHRcdFx0XHRcdC5maW5kKCdhW3JlbD1cIicgKyB2YWx1ZSArICdcIl0nKVxuXHRcdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHRcdC5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gZm9yIHRoZSBfc2hvcnRlbkxhYmVsLWZ1bmN0aW9uLlxuXHRcdCAqIFRoaXMgZnVuY3Rpb24gc2hvcnRlbnMgdGhlIGxhYmVsIHNvIHRoYXQgaXQgZml0c1xuXHRcdCAqIGluc2lkZSB0aGUgYnV0dG9uLiBBZGRpdGlvbmFsIGF2YWlsYWJsZSBzaWJsaW5nc1xuXHRcdCAqIG9mIHRoZSB0ZXh0IGVsZW1lbnQgd2VyZSBnZXR0aW5nIHN1YnN0cmFjdGVkIGZyb21cblx0XHQgKiB0aGUgYXZhaWxhYmxlIGJ1dHRvbiBzaXplLlxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAkYnV0dG9uICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBidXR0b25cblx0XHQgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdmFsdWUgICAgICAgVGhlIHZhbHVlIHRoYXQgc2hvdWxkIGJlIHNldCBhcyB0aGUgYnV0dG9uIHRleHRcblx0XHQgKiBAcmV0dXJuICAgICB7c3RyaW5nfSAgICAgICAgICAgICAgICBUaGUgc2hvcnRlbmVkIHN0cmluZ1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zaG9ydGVuRml0ID0gZnVuY3Rpb24oJGJ1dHRvbiwgdmFsdWUpIHtcblx0XHRcdHZhciAkc2libGluZ3MgPSAkYnV0dG9uLmNoaWxkcmVuKCkubm90KCcuZHJvcGRvd24tbmFtZScpLFxuXHRcdFx0XHQkdGV4dGFyZWEgPSAkYnV0dG9uLmNoaWxkcmVuKCcuZHJvcGRvd24tbmFtZScpLFxuXHRcdFx0XHR3aWR0aCA9ICRidXR0b24ud2lkdGgoKSxcblx0XHRcdFx0bGVuZ3RoID0gdmFsdWUubGVuZ3RoLFxuXHRcdFx0XHRuYW1lID0gJycsXG5cdFx0XHRcdHNob3J0ZW4gPSBmYWxzZSxcblx0XHRcdFx0aSA9IDAsXG5cdFx0XHRcdHRlc3QgPSBudWxsO1xuXG5cdFx0XHQvLyBSZW1vdmUgdGhlIHNpYmxpbmdzIHdpdGggZnJvbSB0aGUgYXZhaWxhYmxlXG5cdFx0XHQvLyBmdWxsIHdpZHRoIG9mIHRoZSBidXR0b25cblx0XHRcdCRzaWJsaW5ncy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR3aWR0aCAtPSAkKHRoaXMpLm91dGVyV2lkdGgoKTtcblx0XHRcdH0pO1xuXG5cdFx0XHQvLyBJdGVyYXRlIHRocm91Z2ggdGhlIGxhYmVsIGNoYXJhY3RlcnNcblx0XHRcdC8vIGFuZCBhZGQgb25lIGNoYXJhY3RlciBhdCB0aW1lIHRvIHRoZSBidXR0b25cblx0XHRcdC8vIGlmIHRoZSB0ZXh0ZmllbGQgc2l6ZSBncm93cyBsYXJnZXIgdGhhblxuXHRcdFx0Ly8gdGhlIGF2YWlsYWJsZSB3aWR0aCBvZiB0aGUgYnV0dG9uIGNhbmNlbFxuXHRcdFx0Ly8gdGhlIGxvb3AgYW5kIHRha2UgdGhlIGxhc3QgZml0dGluZyB2YWx1ZVxuXHRcdFx0Ly8gYXMgcmVzdWx0XG5cdFx0XHRmb3IgKGk7IGkgPCBsZW5ndGg7IGkgKz0gMSkge1xuXHRcdFx0XHR0ZXN0ID0gdmFsdWUuc3Vic3RyaW5nKDAsIGkpICsgJy4uLic7XG5cdFx0XHRcdCR0ZXh0YXJlYS50ZXh0KHRlc3QpO1xuXG5cdFx0XHRcdGlmICgkdGV4dGFyZWEud2lkdGgoKSA+IHdpZHRoKSB7XG5cdFx0XHRcdFx0c2hvcnRlbiA9IHRydWU7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRuYW1lID0gdGVzdDtcblx0XHRcdH1cblxuXHRcdFx0Ly8gSWYgdGhlIHRleHQgd2FzIHNob3J0ZW5lZFxuXHRcdFx0Ly8gcmV0dXJuIHRoZSBzaG9ydGVuZWQgbmFtZVxuXHRcdFx0Ly8gZWxzZSB0aGUgZnVsbCBuYW1lXG5cdFx0XHRpZiAoc2hvcnRlbikge1xuXHRcdFx0XHRyZXR1cm4gbmFtZTtcblx0XHRcdH1cblx0XHRcdHJldHVybiB2YWx1ZTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIGZvciB0aGUgX3Nob3J0ZW5MYWJlbC1mdW5jdGlvbi5cblx0XHQgKiBUaGlzIGZ1bmN0aW9uIHNob3J0ZW5zIHRoZSBsYWJlbCB0byBhIHNldCBudW1iZXJcblx0XHQgKiBvZiBkaWdldHNcblx0XHQgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdmFsdWUgICAgICAgVGhlIHZhbHVlIHRoYXQgc2hvdWxkIGJlIHNldCBhcyB0aGUgYnV0dG9uIHRleHRcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgb3B0ICAgICAgICAgSlNPTiB3aXRoIGN1c3RvbSBzZXR0aW5ncyBmb3IgdGhhdCBjb250YWluZXJcblx0XHQgKiBAcmV0dXJuICAgICB7c3RyaW5nfSAgICAgICAgICAgICAgICBUaGUgc2hvcnRlbmVkIHN0cmluZ1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zaG9ydGVuSW50ID0gZnVuY3Rpb24odmFsdWUsIG9wdCkge1xuXHRcdFx0dmFyIGxlbmd0aCA9IHZhbHVlLmxlbmd0aCxcblx0XHRcdFx0ZGlmZiA9IGxlbmd0aCAtIG9wdC5zaG9ydGVuO1xuXG5cdFx0XHRpZiAoZGlmZiA+IDApIHtcblx0XHRcdFx0ZGlmZiArPSAzO1xuXHRcdFx0XHRyZXR1cm4gdmFsdWUuc3Vic3RyaW5nKDAsIGxlbmd0aCAtIGRpZmYpICsgJy4uLic7XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiB2YWx1ZTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBjaG9vc2VzIHRoZSBjb3JyZWN0IHNob3J0ZW5lclxuXHRcdCAqIHN1YnJvdXRpbmUgZm9yIHNob3J0ZW5pbmcgdGhlIGJ1dHRvbiB0ZXh0XG5cdFx0ICogKGlmIG5lZWRlZCkgYW5kIHJldHVybnMgdGhlIHNob3J0ZW5lZCB2YWx1ZVxuXHRcdCAqIHRvIHRoZSBjYWxsZXJcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgJGJ1dHRvbiAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgYnV0dG9uXG5cdFx0ICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgIHZhbHVlICAgICAgIFRoZSB2YWx1ZSB0aGF0IHNob3VsZCBiZSBzZXQgYXMgdGhlIGJ1dHRvbiB0ZXh0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIG9wdCAgICAgICAgIEpTT04gd2l0aCBjdXN0b20gc2V0dGluZ3MgZm9yIHRoYXQgY29udGFpbmVyXG5cdFx0ICogQHJldHVybiAgICAge3N0cmluZ30gICAgICAgICAgICAgICAgVGhlIHNob3J0ZW5lZCBzdHJpbmdcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2hvcnRlbkxhYmVsID0gZnVuY3Rpb24oJGJ1dHRvbiwgdmFsdWUsIG9wdCkge1xuXHRcdFx0aWYgKG9wdGlvbnMuYnJlYWtwb2ludCA8IGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIHx8IG9wdC5zaG9ydGVuT25Nb2JpbGUpIHtcblx0XHRcdFx0aWYgKG9wdC5zaG9ydGVuID09PSAnZml0Jykge1xuXHRcdFx0XHRcdHZhbHVlID0gX3Nob3J0ZW5GaXQoJGJ1dHRvbiwgdmFsdWUpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKG9wdC5zaG9ydGVuKSB7XG5cdFx0XHRcdFx0dmFsdWUgPSBfc2hvcnRlbkludCh2YWx1ZSwgb3B0KTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXG5cdFx0XHRyZXR1cm4gdmFsdWU7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciB0aGF0IGlzdCB0cmlnZ2VyZWQgb24gY2hhbmdlXG5cdFx0ICogb2YgdGhlIHNlbGVjdGJveCB0byBmb3JjZSB0aGUgZHJvcGRvd24gdG8gY2xvc2Vcblx0XHQgKiAobmVlZGVkIG9uIG1vYmlsZSBkZXZpY2VzLCBiZWNhdXNlIG9mIGl0J3MgbmF0aXZlXG5cdFx0ICogc3VwcG9ydCBmb3IgZHJvcGRvd25zKVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jbG9zZUxheWVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkY29udGFpbmVyID0gJHNlbGYuY2xvc2VzdChvcHRpb25zLmNvbnRhaW5lciksXG5cdFx0XHRcdCRzZWxlY3QgPSAkY29udGFpbmVyLmNoaWxkcmVuKCdzZWxlY3QnKSxcblx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zLCAkY29udGFpbmVyLnBhcnNlTW9kdWxlRGF0YSgnZHJvcGRvd24nKSk7XG5cblx0XHRcdHRyYW5zaXRpb24ub3BlbiA9IGZhbHNlO1xuXHRcdFx0JGNvbnRhaW5lci50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXG5cdFx0XHQvLyBUcmlnZ2VyIHRoZSBjaGFuZ2UgZXZlbnQgaWYgdGhlIG9wdGlvbiBpcyBzZXRcblx0XHRcdGlmIChkYXRhc2V0LnRyaWdnZXJOb0NoYW5nZSkge1xuXHRcdFx0XHQkc2VsZWN0LnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gZ2V0cyB0cmlnZ2VyZWQgb24gY2xpY2sgb24gdGhlIGJ1dHRvbi5cblx0XHQgKiBJdCBzd2l0Y2hlcyB0aGUgc3RhdGUgb2YgdGhlIGRyb3Bkb3duIHZpc2liaWxpdHlcblx0XHQgKiBAcGFyYW0gICAgICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9vcGVuTGF5ZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkY29udGFpbmVyID0gJHNlbGYuY2xvc2VzdChvcHRpb25zLmNvbnRhaW5lciksXG5cdFx0XHRcdCRzZWxlY3QgPSAkY29udGFpbmVyLmNoaWxkcmVuKCdzZWxlY3QnKSxcblx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zLCAkY29udGFpbmVyLnBhcnNlTW9kdWxlRGF0YSgnZHJvcGRvd24nKSk7XG5cblx0XHRcdGlmICgkY29udGFpbmVyLmhhc0NsYXNzKG9wdGlvbnMub3BlbkNsYXNzKSkge1xuXHRcdFx0XHQvLyBSZW1vdmUgdGhlIG9wZW4gY2xhc3MgaWYgdGhlIGxheWVyIGlzIG9wZW5lZFxuXHRcdFx0XHR0cmFuc2l0aW9uLm9wZW4gPSBmYWxzZTtcblx0XHRcdFx0JGNvbnRhaW5lci50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXG5cdFx0XHRcdC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuXHRcdFx0XHRpZiAoZGF0YXNldC50cmlnZ2VyTm9DaGFuZ2UpIHtcblx0XHRcdFx0XHQkc2VsZWN0LnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcblx0XHRcdFx0fVxuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0Ly8gQWRkIHRoZSBvcGVuIGNsYXNzIGFuZCBpbmZvcm0gb3RoZXIgbGF5ZXJzIHRvIGNsb3NlXG5cdFx0XHRcdF9oaWRlQWN0aXZlKCRjb250YWluZXIsIGRhdGFzZXQpO1xuXHRcdFx0XHRfc2V0RGlzYWJsZWQoJGNvbnRhaW5lcik7XG5cblx0XHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gdHJ1ZTtcblx0XHRcdFx0JGNvbnRhaW5lci50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5PUEVOX0ZMWU9VVCgpLCBbJGNvbnRhaW5lcl0pO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIHRoYXQgZ2V0cyB1c2VkIGlmIHRoZSB1c2VyXG5cdFx0ICogc2VsZWN0cyBhIHZhbHVlIGZyb20gdGhlIGN1c3RvbSBkcm9wZG93bi5cblx0XHQgKiBJZiB0aGUgdmFsdWUgaGFzIGNoYW5nZWQsIHRoZSB2aWV3IGdldHNcblx0XHQgKiB1cGRhdGVkIGFuZCB0aGUgb3JpZ2luYWwgc2VsZWN0IGdldHMgc2V0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zZWxlY3RFbnRyeSA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRsaSA9ICRzZWxmLnBhcmVudCgpO1xuXG5cdFx0XHQvLyBJZiB0aGUgaXRlbSBpcyBkaXNhYmxlZCwgZG8gbm90aGluZ1xuXHRcdFx0aWYgKCEkbGkuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblxuXHRcdFx0XHR2YXIgJGNvbnRhaW5lciA9ICRzZWxmLmNsb3Nlc3Qob3B0aW9ucy5jb250YWluZXIpLFxuXHRcdFx0XHRcdCRidXR0b24gPSAkY29udGFpbmVyLmNoaWxkcmVuKCdidXR0b24nKSxcblx0XHRcdFx0XHQkc2VsZWN0ID0gJGNvbnRhaW5lci5jaGlsZHJlbignc2VsZWN0JyksXG5cdFx0XHRcdFx0b2xkVmFsdWUgPSAkc2VsZWN0LmNoaWxkcmVuKCc6c2VsZWN0ZWQnKS52YWwoKSxcblx0XHRcdFx0XHRuZXdWYWx1ZSA9ICRzZWxmLmF0dHIoJ3JlbCcpLFxuXHRcdFx0XHRcdG5hbWUgPSAkc2VsZi50ZXh0KCksXG5cdFx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zLCAkY29udGFpbmVyLnBhcnNlTW9kdWxlRGF0YSgnZHJvcGRvd24nKSk7XG5cblx0XHRcdFx0Ly8gVXBkYXRlIHRoZSBkcm9wZG93biB2aWV3IGlmIHRoZVxuXHRcdFx0XHQvLyB2YWx1ZSBoYXMgY2hhbmdlZFxuXHRcdFx0XHRpZiAob2xkVmFsdWUgIT09IG5ld1ZhbHVlKSB7XG5cdFx0XHRcdFx0Ly8gU2V0IHRoZSBidXR0b24gdGV4dFxuXHRcdFx0XHRcdHZhciBzaG9ydGVuZWQgPSBfc2hvcnRlbkxhYmVsKCRidXR0b24sIG5hbWUsIGRhdGFzZXQpO1xuXHRcdFx0XHRcdCRidXR0b25cblx0XHRcdFx0XHRcdC5jaGlsZHJlbignLmRyb3Bkb3duLW5hbWUnKVxuXHRcdFx0XHRcdFx0LnRleHQoc2hvcnRlbmVkKTtcblxuXHRcdFx0XHRcdC8vIFNldCB0aGUgXCJvcmlnaW5hbFwiIHNlbGVjdCBib3ggYW5kXG5cdFx0XHRcdFx0Ly8gbm90aWZ5IHRoZSBicm93c2VyIC8gb3RoZXIganMgdGhhdCB0aGVcblx0XHRcdFx0XHQvLyB2YWx1ZSBoYXMgY2hhbmdlZFxuXHRcdFx0XHRcdCRzZWxlY3Rcblx0XHRcdFx0XHRcdC5jaGlsZHJlbignW3ZhbHVlPVwiJyArIG5ld1ZhbHVlICsgJ1wiXScpXG5cdFx0XHRcdFx0XHQucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcblxuXHRcdFx0XHRcdC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuXHRcdFx0XHRcdGlmIChkYXRhc2V0LnRyaWdnZXJDaGFuZ2UpIHtcblx0XHRcdFx0XHRcdCRzZWxlY3QudHJpZ2dlcignY2hhbmdlJywgW10pO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSBlbHNlIGlmIChkYXRhc2V0LnRyaWdnZXJOb0NoYW5nZSkge1xuXHRcdFx0XHRcdC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuXHRcdFx0XHRcdCRzZWxlY3QudHJpZ2dlcignY2hhbmdlJywgW10pO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Ly8gQ2xvc2UgdGhlIGxheWVyXG5cdFx0XHRcdHRyYW5zaXRpb24ub3BlbiA9IGZhbHNlO1xuXHRcdFx0XHQkY29udGFpbmVyLnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXMgdGhlIHN3aXRjaCBiZXR3ZWVuIHRoZSBicmVha3BvaW50LiBJZiB0aGVcblx0XHQgKiBzaXplIG9mIHRoZSBidXR0b24gY2hhbmdlcyB0aGUgdGV4dCB3aWxsIGJlIHNob3J0ZW5lZFxuXHRcdCAqIGFnYWluIHRvIGZpdC4gSWYgdGhlIHZpZXcgc3dpdGNoZXMgdG8gbW9iaWxlLCB0aGlzXG5cdFx0ICogYmVoYXZpb3VyIGlzIHNraXBwZWQgdGhlIGZ1bGwgbmFtZSB3aWxsIGJlIGRpc3BsYXllZFxuXHRcdCAqIGFnYWluXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2JyZWFrcG9pbnRIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGNvbnRhaW5lciA9ICR0aGlzLmZpbmQob3B0aW9ucy5jb250YWluZXIpO1xuXG5cdFx0XHRpZiAob3B0aW9ucy5icmVha3BvaW50IDwganNlLmxpYnMudGVtcGxhdGUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkuaWQgfHwgb3B0aW9ucy5zaG9ydGVuT25Nb2JpbGUpIHtcblx0XHRcdFx0Ly8gSWYgc3RpbGwgaW4gZGVza3RvcCBtb2RlLCB0cnkgdG8gc2hvcnRlbiB0aGUgbmFtZVxuXHRcdFx0XHQkY29udGFpbmVyLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdCRidXR0b24gPSAkc2VsZi5jaGlsZHJlbignYnV0dG9uJyksXG5cdFx0XHRcdFx0XHQkdGV4dGFyZWEgPSAkYnV0dG9uLmNoaWxkcmVuKCcuZHJvcGRvd24tbmFtZScpLFxuXHRcdFx0XHRcdFx0dmFsdWUgPSAkc2VsZi5maW5kKCdzZWxlY3Qgb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpLFxuXHRcdFx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zLCAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ2Ryb3Bkb3duJykpLFxuXHRcdFx0XHRcdFx0c2hvcnRlbmVkID0gX3Nob3J0ZW5MYWJlbCgkYnV0dG9uLCB2YWx1ZSwgZGF0YXNldCk7XG5cblx0XHRcdFx0XHQkdGV4dGFyZWEudGV4dChzaG9ydGVuZWQpO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdC8vIElmIGluIG1vYmlsZSBtb2RlIGluc2VydCB0aGUgY29tcGxldGUgbmFtZSBhZ2FpblxuXHRcdFx0XHQvLyBhbmQgY2xvc2Ugb3BlbmVkIGxheWVyc1xuXHRcdFx0XHQkY29udGFpbmVyXG5cdFx0XHRcdFx0LnJlbW92ZUNsYXNzKG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdFx0JHRleHRhcmVhID0gJHNlbGYuZmluZCgnLmRyb3Bkb3duLW5hbWUnKSxcblx0XHRcdFx0XHRcdFx0dmFsdWUgPSAkc2VsZi5maW5kKCdzZWxlY3Qgb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpO1xuXG5cdFx0XHRcdFx0XHQkdGV4dGFyZWEudGV4dCh2YWx1ZSk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIGNsb3NpbmcgYWxsIGRyb3Bkb3duIGZseW91dHMgaWZcblx0XHQgKiBzb21ld2hlcmUgb24gdGhlIHBhZ2Ugb3BlbnMgYW4gb3RoZXIgZmx5b3V0XG5cdFx0ICogQHBhcmFtICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAge29iamVjdH0gICAgZCAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBldmVudCBlbWl0dGVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2Nsb3NlRmx5b3V0ID0gZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0dmFyICRjb250YWluZXJzID0gJHRoaXMuZmluZChvcHRpb25zLmNvbnRhaW5lciksXG5cdFx0XHRcdCRleGNsdWRlID0gZCB8fCAkKGUudGFyZ2V0KS5jbG9zZXN0KG9wdGlvbnMub3BlbkNsYXNzKTtcblxuXHRcdFx0JGNvbnRhaW5lcnMgPSAkY29udGFpbmVycy5ub3QoJGV4Y2x1ZGUpO1xuXHRcdFx0JGNvbnRhaW5lcnMucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdFx0dHJhbnNpdGlvbi5jbGFzc09wZW4gPSBvcHRpb25zLm9wZW5DbGFzcztcblxuXHRcdFx0JGJvZHlcblx0XHRcdFx0Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5PUEVOX0ZMWU9VVCgpICsgJyBjbGljaycsIF9jbG9zZUZseW91dClcblx0XHRcdFx0Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5CUkVBS1BPSU5UKCksIF9icmVha3BvaW50SGFuZGxlcik7XG5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignY2xpY2snLCBvcHRpb25zLmNvbnRhaW5lciArICcgYnV0dG9uJywgX29wZW5MYXllcilcblx0XHRcdFx0Lm9uKCdjbGljaycsIG9wdGlvbnMuY29udGFpbmVyICsgJyB1bCBhJywgX3NlbGVjdEVudHJ5KVxuXHRcdFx0XHQub24oJ2NoYW5nZScsIG9wdGlvbnMuY29udGFpbmVyICsgJyBzZWxlY3QnLCBfY2xvc2VMYXllcik7XG5cblx0XHRcdGlmIChvcHRpb25zLnNob3J0ZW5PbkluaXQpIHtcblx0XHRcdFx0X2JyZWFrcG9pbnRIYW5kbGVyKCk7XG5cdFx0XHR9XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
