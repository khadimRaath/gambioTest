/* --------------------------------------------------------------
 gmotion.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## G-Motion Control Extension
 *
 * This extension allows you to make use of G-Motion controls for a product image.
 *
 * Each product picture has a G-Motion control section, where the user is able to change G-Motion 
 * settings for the respective picture. This extension is responsible for showing the G-Motion options 
 * under each picture and to change the values of position coordinates.
 *
 * @module Admin/Extensions/gmotion
 * @ignore
 */
gx.extensions.module(
	// Module name.
	'gmotion',

	// Module dependencies.
	[],

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		// Shortcut to module element.
		var $this = $(this);

		// Is this image container for a primary image?
		var isPrimaryImage = Boolean(data.isPrimaryImage);

		// Elements selector object.
		var selectors = {
			// General product-specific G-Motion activation checkbox.
			activator: '[data-gmotion-activator]',

			// The 'Use as G-Motion image' checkbox.
			useCheckbox: '[data-gmotion-checkbox]',

			// G-Motion ontrol panel container.
			settingsContainer: '[data-gmotion-settings-container]',

			// Start position dragger.
			startDragger: '[data-gmotion-start-dragger]',

			// End position dragger.
			endDragger: '[data-gmotion-end-dragger]',

			// Playground image.
			playgroundImage: '[data-gmotion-image]',

			// Start input field.
			startInput: '[data-gmotion-start-input]',

			// End input field.
			endInput: '[data-gmotion-end-input]',

			// Zoom factor from input field.
			zoomFromInput: '[data-gmotion-zoomstart-input]',

			// Zoom factor to input field.
			zoomToInput: '[data-gmotion-zoomto-input]',

			// Duration input field.
			durationInput: '[data-gmotion-duration-input]',

			// Sort input field.
			sortInput: '[data-gmotion-sort-input]',

			// G-Motion rows.
			settingRows: '.gmotion-setting'
		};

		// Default values object.
		var defaultValues = {
			// Start horizontal swing animation position default value.
			animationStartLeft: 0,

			// Start vertical swing animation position default value.
			animationStartTop: 50,

			// End horizontal swing animation position default value.
			animationEndLeft: 100,

			// End vertical swing animation position default value.
			animationEndTop: 50,

			// Zoom factor start default value.
			zoomStart: 1,

			// Zoom factor end default value.
			zoomEnd: 1,

			// Animation duration default value.
			animationDuration: 10,

			// Dragger icon width.
			draggerWidth: 12,

			// Dragger icon height.
			draggerHeight: 14
		};

		// Module object.
		var module = {};

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Refreshes values in the appropriate input field.
		 *
		 * @param {jQuery|HTMLElement} inputField The field which should be updated.
		 * @param {number} leftPosition Horizontal position percentage.
		 * @param {number} topPosition Vertical position percentage.
		 *
		 * @private
		 */
		var _refreshInputValues = function (inputField, leftPosition, topPosition) {
			// Elements shortcut.
			var $startDragger = $this.find(selectors.startDragger),
					$startInput 	= $this.find(selectors.startInput),
					$endInput 		= $this.find(selectors.endInput);

			var $input = $startDragger.is(inputField) ? $startInput : $endInput;

			var value = [
				leftPosition + '%',
				topPosition + '%'
			].join(' ');

			$input.val(value);
		};

		/**
		 * Fetches percent values from input field and updates
		 * the position of the respective dragger element.
		 * Aborts on abnormal values.
		 *
		 * @param {jQuery|HTMLElement} inputElement Input element.
		 * @private
		 */
		var _updateDraggerFromInput = function (inputElement) {
			// Elements shortcut.
			var $startDragger = $this.find(selectors.startDragger),
					$endDragger 	= $this.find(selectors.endDragger),
					$startInput 	= $this.find(selectors.startInput);

			// Input value.
			var inputValue = $(inputElement).val();

			// Input value.
			var extractedValues = _extractValues(inputValue);

			// Return immediately on abnormal values.
			var falseValues = (
				// No values extracted.
				extractedValues === null ||

				// Left values exceeds maximum.
				extractedValues[0] > 100 ||

				// Top value exceed
				extractedValues[1] > 100
			);

			if (falseValues) {
				return;
			}

			// Position container with values in pixel.
			var positionInPixel = _convertPercentToPixel(extractedValues[0], extractedValues[1]);

			// Assign appropriate dragger element.
			var $draggerToMove = $startInput.is(inputElement) ?
			                     $startDragger : $endDragger;

			// Reposition dragger element to new position values.
			_setDraggerPosition($draggerToMove, positionInPixel);
		};

		/**
		 * Draws the draggable handler for coordinating
		 * swing start and end positions. Uses jQueryUI to handle dragging.
		 * If no values are set, the default position values will be set.
		 *
		 * @see jQueryUI 'draggable' API documentation.
		 * @requires jQueryUI
		 * @private
		 */
		var _initializeDraggers = function () {
			// Element shortcuts.
			var $image 				= $this.find(selectors.playgroundImage),
					$startDragger = $this.find(selectors.startDragger),
					$endDragger 	= $this.find(selectors.endDragger);

			var options = {
				containment: $image,
				drag: function () {
					var percentage = _convertPixelToPercent(
						$(this).css('left').replace('px', ''),
						$(this).css('top').replace('px', '')
					);
					_refreshInputValues(this, percentage.left, percentage.top);
				}
			};

			$startDragger.draggable(options);
			$endDragger.draggable(options);
		};

		/**
		 * Sets the position of a dragger.
		 *
		 * @param {jQuery|HTMLElement} element Dragger element.
		 * @param {object} position Positions to set.
		 * @param {number} position.left Horizontal position.
		 * @param {number} position.top Vertical position.
		 *
		 * @private
		 */
		var _setDraggerPosition = function (element, position) {
			$(element).css(position);
		};

		/**
		 * Converts pixel values to the relative percent values.
		 * Note: Dimensions of $startDragger is used for calculation,
		 * which does not affect the end result,
		 * as both draggers have the same dimensions.
		 *
		 * @param {number} leftPosition
		 * @param {number} topPosition
		 *
		 * @returns {object}
		 * @private
		 */
		var _convertPixelToPercent = function (leftPosition, topPosition) {
			// Element shortcuts.
			var $image = $this.find(selectors.playgroundImage);

			// Result object, which will be returned.
			var result = {
				left: null,
				top: null
			};

			// Calculate left position.
			var realWidth = $image.width() - defaultValues.draggerWidth;
			var leftPercentage = (leftPosition / realWidth) * 100;
			result.left = Math.round(leftPercentage);

			// Calculate top position.
			var realHeight = $image.height() - defaultValues.draggerHeight;
			var topPercentage = (topPosition / realHeight) * 100;
			result.top = Math.round(topPercentage);

			return result;
		};

		/**
		 * Converts percent values to the respective pixel values.
		 *
		 * @param {number} leftPosition
		 * @param {number} topPosition
		 *
		 * @returns {object}
		 * @private
		 */
		var _convertPercentToPixel = function (leftPosition, topPosition) {
			// Element shortcuts.
			var $image = $this.find(selectors.playgroundImage);

			// Result object, which will be returned.
			var result = {
				left: null,
				top: null
			};

			// Calculate left position.
			var realWidth = $image.width() - defaultValues.draggerWidth;
			result.left = realWidth / 100 * leftPosition;

			// Calculate top position.
			var realHeight = $image.height() - defaultValues.draggerHeight;
			result.top = realHeight / 100 * topPosition;

			return result;
		};

		/**
		 * Extracts numeric values from string and
		 * returns the first two values in an array.
		 * Has to return at least two extracted values,
		 * otherwise it will return null.
		 *
		 * @param {string} value
		 * @returns {Array|null}
		 * @private
		 */
		var _extractValues = function (value) {
			// Result which will be returned.
			var result;

			// Regex to extract numeric values.
			var regex = /([\d]+)/g;

			// Extracted values from array.
			var extractedValues = value.match(regex);

			// Check if at least two values have been extracted
			// and assign them to result variable,
			// otherwise null will be assigned.
			if (extractedValues === null || extractedValues.length < 2) {
				result = null;
			} else {
				result = [
					extractedValues[0],
					extractedValues[1]
				];
			}

			return result;
		};

		/**
		 * Shows/hides checkbox for display G-Motion control panel.
		 *
		 * @param {boolean} doShow Determines whether to show/hide.
		 * @private
		 */
		var _toggleCheckbox = function (doShow) {
			// Element shortcuts.
			var $settingsContainer 	= $this.find(selectors.settingsContainer),
					$settingRows 				= $this.find(selectors.settingRows);

			if (doShow) {
				$settingRows
					.not($settingsContainer)
					.removeClass('hidden');
			} else {
				$settingRows.addClass('hidden');
			}
		};

		/**
		 * Shows/hides G-Motion animation control panel.
		 *
		 * @param {boolean} doShow Determines whether to show/hide.
		 * @private
		 */
		var _toggleControlPanel = function (doShow) {
			// Element shortcuts.
			var $settingsContainer 	= $this.find(selectors.settingsContainer),
					$settingRows 				= $this.find(selectors.settingRows);

			if (doShow) {
				$settingsContainer
					.removeClass('hidden')
					.css({
						opacity: 0.1
					});

				setTimeout(function () {
					_initializeDraggers();
					_initializeValues();

					$settingsContainer
						.animate({
						     opacity: 1
					     });
				}, 1000);
			} else {
				$settingsContainer.addClass('hidden');
			}

			$settingsContainer
				.find('input, select')
				.prop('disabled', !doShow);
		};

		/**
		 * Initializes event handlers.
		 *
		 * @private
		 */
		var _initializeEventHandlers = function () {
			// Element shortcuts.
			var $activator 		= $(selectors.activator),
					$checkbox 		= $this.find(selectors.useCheckbox),
					$startInput 	= $this.find(selectors.startInput),
					$endInput 		= $this.find(selectors.endInput);

			// Handle checkboxes.
			// ==================

			// (De-)activates G-Motion option checkboxes in images settings.
			$activator
				.parent()
				.on('click', function () {
					_toggleCheckbox($activator.is(':checked'));
					_toggleControlPanel(($checkbox.is(':checked') && $activator.is(':checked')));
				});

			// Shows/Hides G-Motion control panel on checkbox click.
			$checkbox
				.parent()
				.on('click', function () {
					_toggleControlPanel($checkbox.is(':checked'));
				});

			// Handle input fields.
			// ====================

			// Update start dragger position.
			$startInput
				.on('keyup', function () {
					_updateDraggerFromInput(this);
				});

			// Update end dragger position.
			$endInput
				.on('keyup', function () {
					_updateDraggerFromInput(this);
				});
		};

		/**
		 * Set values.
		 *
		 * @private
		 */
		var _initializeValues = function () {
			// Element shortcuts.
			var $image 							= $this.find(selectors.playgroundImage),
					$startInput 				= $this.find(selectors.startInput),
					$endInput 					= $this.find(selectors.endInput),
					$zoomStartInput 		= $this.find(selectors.zoomFromInput),
					$zoomEndInput 			= $this.find(selectors.zoomToInput),
					$durationInput 			= $this.find(selectors.durationInput);

			// Position start value
			// ====================
			if (data.positionFrom) {
				$startInput.val(data.positionFrom);
			} else {
				_refreshInputValues(
					$startInput,
					defaultValues.animationStartLeft,
					defaultValues.animationStartTop
				);
			}
			_updateDraggerFromInput($startInput);

			// Position end value
			// ==================
			if (data.positionTo) {
				$endInput.val(data.positionTo);
			} else {
				_refreshInputValues(
					$endInput,
					defaultValues.animationEndLeft,
					defaultValues.animationEndTop
				);
			}
			_updateDraggerFromInput($endInput);

			// Zoom start value
			// ================
			var zoomStartValue = data.zoomFrom ? data.zoomFrom : defaultValues.zoomStart;
			$zoomStartInput.val(zoomStartValue);

			// Zoom end value
			// ==============
			var zoomEndValue = data.zoomTo ? data.zoomTo : defaultValues.zoomEnd;
			$zoomEndInput.val(zoomEndValue);

			// Animation duration
			// ==================
			var durationValue = data.duration ?
			                    data.duration :
			                    defaultValues.animationDuration;
			$durationInput.val(durationValue);
		};

		var _handleInitialState = function () {
			// Element shortcuts.
			var $activator 		= $(selectors.activator),
				$useCheckbox 	= $this.find(selectors.useCheckbox);

			// Toggle checkbox and panel depending on checkbox activation states.
			if ($activator.is(':checked')) {
				_toggleCheckbox(true);
				if ($useCheckbox.is(':checked')) {
					_toggleControlPanel(true);
				}
			}
		};

		// --------------------------------------------------------------------
		// INITIALIZATION
		// --------------------------------------------------------------------

		module.init = function (done) {
			// Handle initial visibility state of G-Motion controls.
			_handleInitialState();

			// Set up event listeners
			_initializeEventHandlers();

			// Register as finished.
			done();
		};

		return module;
	}
);
