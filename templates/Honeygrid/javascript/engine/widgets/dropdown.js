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
gambio.widgets.module(
	'dropdown',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

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
		var _hideActive = function($container, opt) {
			if (opt.hideActive) {
				var $select = $container
					.children('select'),
					value = $select
						.children(':selected')
						.val();

				$container
					.find('li')
					.show()
					.children('a[rel="' + value + '"]')
					.parent()
					.hide();
			}
		};

		/**
		 * Helper function to add a disabled class to the
		 * disabled entries in the custom dropdown. Therefor
		 * the original select is scanned for disabled entries
		 * @param       {object}        $container      jQuery selection of the dropdown container
		 * @private
		 */
		var _setDisabled = function($container) {
			var $ul = $container.children(),
				$select = $container.children('select'),
				$disabled = $select.children(':disabled');

			// Remove all disabled classes first
			$ul
				.find('.disabled')
				.removeClass('disabled');

			// Iterate through all entries that needs to
			// be disabled and add a class to them
			$disabled.each(function() {
				var $self = $(this),
					value = $self.val();

				$ul
					.find('a[rel="' + value + '"]')
					.parent()
					.addClass('disabled');
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
		var _shortenFit = function($button, value) {
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
			$siblings.each(function() {
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
		var _shortenInt = function(value, opt) {
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
		var _shortenLabel = function($button, value, opt) {
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
		var _closeLayer = function() {
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
		var _openLayer = function(e) {
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
		var _selectEntry = function(e) {
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
					$button
						.children('.dropdown-name')
						.text(shortened);

					// Set the "original" select box and
					// notify the browser / other js that the
					// value has changed
					$select
						.children('[value="' + newValue + '"]')
						.prop('selected', true);

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
		var _breakpointHandler = function() {
			var $container = $this.find(options.container);

			if (options.breakpoint < jse.libs.template.responsive.breakpoint().id || options.shortenOnMobile) {
				// If still in desktop mode, try to shorten the name
				$container.each(function() {
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
				$container
					.removeClass(options.openClass)
					.each(function() {
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
		var _closeFlyout = function(e, d) {
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
		module.init = function(done) {

			transition.classOpen = options.openClass;

			$body
				.on(jse.libs.template.events.OPEN_FLYOUT() + ' click', _closeFlyout)
				.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

			$this
				.on('click', options.container + ' button', _openLayer)
				.on('click', options.container + ' ul a', _selectEntry)
				.on('change', options.container + ' select', _closeLayer);

			if (options.shortenOnInit) {
				_breakpointHandler();
			}

			done();
		};

		// Return data to widget engine
		return module;
	});
