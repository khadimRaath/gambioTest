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
gambio.widgets.module(
	'product_listing_filter',

	[
		'url_arguments',
		gambio.source + '/libs/events'
	],

	function(data) {

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
				target: null        // The target the classes getting added (e.g. the product list)
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
		var _switchView = function(config) {

			// Get all "add" classes from the other buttons
			// to remove them in the next step
			var removeClasses = '';
			$viewmode
				.find('a')
				.each(function() {
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
			$target
				.off(jse.libs.template.events.TRANSITION_FINISHED())
				.one(jse.libs.template.events.TRANSITION_FINISHED(), function() {
					transition.open = true;
					$target
						.removeClass(removeClasses)
						.addClass(config.add)
						.trigger(jse.libs.template.events.TRANSITION(), transition);
				})
				.trigger(jse.libs.template.events.TRANSITION(), transition);

		};

		/**
		 * Sets the pagination URLs on viewmode
		 * change, so that the parameter "view_mode"
		 * is set correctly in the URL
		 * @param       {string}        mode        The value of the view_mode-parameter
		 * @private
		 */
		var _setPaginationURLs = function(mode) {
			$pagination
				.find('a')
				.each(function() {
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
		var _viewChangeHandler = function(e) {
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
					
					history.pushState({state: viewMode}, viewMode, url);

					// Trigger a pushstate event to notify other widgets
					// about the url change
					$this.trigger('pushstate', {state: viewMode});
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
		var _historyHandler = function(e, d) {
			var eventData = d || (e.originalEvent ? e.originalEvent : {state: ''}),
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
		var _changeHandler = function() {
			$form.submit();
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

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
				var 
					viewMode = jse.libs.url_arguments.getUrlParameters().view_mode,
					state = history.state || {},
					url = jse.libs.url_arguments.replaceParameterValue(location.href, 'view_mode', viewMode);
				
				state.state = viewMode;
				history.replaceState(state, viewMode, url);
			}

			// Bind listener for user input
			$this
				.on('change', 'select.jsReload', _changeHandler)
				.on('click', '.jsPanelViewmode a', {history: true}, _viewChangeHandler);

			// Bind event listener to check
			// if the history entry has changed
			$body.on('pushstate pushstate_no_history', _historyHandler);
			$(window).on('popstate', _historyHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});