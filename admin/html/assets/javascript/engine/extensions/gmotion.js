'use strict';

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
[], function (data) {

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
	var _refreshInputValues = function _refreshInputValues(inputField, leftPosition, topPosition) {
		// Elements shortcut.
		var $startDragger = $this.find(selectors.startDragger),
		    $startInput = $this.find(selectors.startInput),
		    $endInput = $this.find(selectors.endInput);

		var $input = $startDragger.is(inputField) ? $startInput : $endInput;

		var value = [leftPosition + '%', topPosition + '%'].join(' ');

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
	var _updateDraggerFromInput = function _updateDraggerFromInput(inputElement) {
		// Elements shortcut.
		var $startDragger = $this.find(selectors.startDragger),
		    $endDragger = $this.find(selectors.endDragger),
		    $startInput = $this.find(selectors.startInput);

		// Input value.
		var inputValue = $(inputElement).val();

		// Input value.
		var extractedValues = _extractValues(inputValue);

		// Return immediately on abnormal values.
		var falseValues =
		// No values extracted.
		extractedValues === null ||

		// Left values exceeds maximum.
		extractedValues[0] > 100 ||

		// Top value exceed
		extractedValues[1] > 100;

		if (falseValues) {
			return;
		}

		// Position container with values in pixel.
		var positionInPixel = _convertPercentToPixel(extractedValues[0], extractedValues[1]);

		// Assign appropriate dragger element.
		var $draggerToMove = $startInput.is(inputElement) ? $startDragger : $endDragger;

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
	var _initializeDraggers = function _initializeDraggers() {
		// Element shortcuts.
		var $image = $this.find(selectors.playgroundImage),
		    $startDragger = $this.find(selectors.startDragger),
		    $endDragger = $this.find(selectors.endDragger);

		var options = {
			containment: $image,
			drag: function drag() {
				var percentage = _convertPixelToPercent($(this).css('left').replace('px', ''), $(this).css('top').replace('px', ''));
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
	var _setDraggerPosition = function _setDraggerPosition(element, position) {
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
	var _convertPixelToPercent = function _convertPixelToPercent(leftPosition, topPosition) {
		// Element shortcuts.
		var $image = $this.find(selectors.playgroundImage);

		// Result object, which will be returned.
		var result = {
			left: null,
			top: null
		};

		// Calculate left position.
		var realWidth = $image.width() - defaultValues.draggerWidth;
		var leftPercentage = leftPosition / realWidth * 100;
		result.left = Math.round(leftPercentage);

		// Calculate top position.
		var realHeight = $image.height() - defaultValues.draggerHeight;
		var topPercentage = topPosition / realHeight * 100;
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
	var _convertPercentToPixel = function _convertPercentToPixel(leftPosition, topPosition) {
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
	var _extractValues = function _extractValues(value) {
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
			result = [extractedValues[0], extractedValues[1]];
		}

		return result;
	};

	/**
  * Shows/hides checkbox for display G-Motion control panel.
  *
  * @param {boolean} doShow Determines whether to show/hide.
  * @private
  */
	var _toggleCheckbox = function _toggleCheckbox(doShow) {
		// Element shortcuts.
		var $settingsContainer = $this.find(selectors.settingsContainer),
		    $settingRows = $this.find(selectors.settingRows);

		if (doShow) {
			$settingRows.not($settingsContainer).removeClass('hidden');
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
	var _toggleControlPanel = function _toggleControlPanel(doShow) {
		// Element shortcuts.
		var $settingsContainer = $this.find(selectors.settingsContainer),
		    $settingRows = $this.find(selectors.settingRows);

		if (doShow) {
			$settingsContainer.removeClass('hidden').css({
				opacity: 0.1
			});

			setTimeout(function () {
				_initializeDraggers();
				_initializeValues();

				$settingsContainer.animate({
					opacity: 1
				});
			}, 1000);
		} else {
			$settingsContainer.addClass('hidden');
		}

		$settingsContainer.find('input, select').prop('disabled', !doShow);
	};

	/**
  * Initializes event handlers.
  *
  * @private
  */
	var _initializeEventHandlers = function _initializeEventHandlers() {
		// Element shortcuts.
		var $activator = $(selectors.activator),
		    $checkbox = $this.find(selectors.useCheckbox),
		    $startInput = $this.find(selectors.startInput),
		    $endInput = $this.find(selectors.endInput);

		// Handle checkboxes.
		// ==================

		// (De-)activates G-Motion option checkboxes in images settings.
		$activator.parent().on('click', function () {
			_toggleCheckbox($activator.is(':checked'));
			_toggleControlPanel($checkbox.is(':checked') && $activator.is(':checked'));
		});

		// Shows/Hides G-Motion control panel on checkbox click.
		$checkbox.parent().on('click', function () {
			_toggleControlPanel($checkbox.is(':checked'));
		});

		// Handle input fields.
		// ====================

		// Update start dragger position.
		$startInput.on('keyup', function () {
			_updateDraggerFromInput(this);
		});

		// Update end dragger position.
		$endInput.on('keyup', function () {
			_updateDraggerFromInput(this);
		});
	};

	/**
  * Set values.
  *
  * @private
  */
	var _initializeValues = function _initializeValues() {
		// Element shortcuts.
		var $image = $this.find(selectors.playgroundImage),
		    $startInput = $this.find(selectors.startInput),
		    $endInput = $this.find(selectors.endInput),
		    $zoomStartInput = $this.find(selectors.zoomFromInput),
		    $zoomEndInput = $this.find(selectors.zoomToInput),
		    $durationInput = $this.find(selectors.durationInput);

		// Position start value
		// ====================
		if (data.positionFrom) {
			$startInput.val(data.positionFrom);
		} else {
			_refreshInputValues($startInput, defaultValues.animationStartLeft, defaultValues.animationStartTop);
		}
		_updateDraggerFromInput($startInput);

		// Position end value
		// ==================
		if (data.positionTo) {
			$endInput.val(data.positionTo);
		} else {
			_refreshInputValues($endInput, defaultValues.animationEndLeft, defaultValues.animationEndTop);
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
		var durationValue = data.duration ? data.duration : defaultValues.animationDuration;
		$durationInput.val(durationValue);
	};

	var _handleInitialState = function _handleInitialState() {
		// Element shortcuts.
		var $activator = $(selectors.activator),
		    $useCheckbox = $this.find(selectors.useCheckbox);

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
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImdtb3Rpb24uanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImlzUHJpbWFyeUltYWdlIiwiQm9vbGVhbiIsInNlbGVjdG9ycyIsImFjdGl2YXRvciIsInVzZUNoZWNrYm94Iiwic2V0dGluZ3NDb250YWluZXIiLCJzdGFydERyYWdnZXIiLCJlbmREcmFnZ2VyIiwicGxheWdyb3VuZEltYWdlIiwic3RhcnRJbnB1dCIsImVuZElucHV0Iiwiem9vbUZyb21JbnB1dCIsInpvb21Ub0lucHV0IiwiZHVyYXRpb25JbnB1dCIsInNvcnRJbnB1dCIsInNldHRpbmdSb3dzIiwiZGVmYXVsdFZhbHVlcyIsImFuaW1hdGlvblN0YXJ0TGVmdCIsImFuaW1hdGlvblN0YXJ0VG9wIiwiYW5pbWF0aW9uRW5kTGVmdCIsImFuaW1hdGlvbkVuZFRvcCIsInpvb21TdGFydCIsInpvb21FbmQiLCJhbmltYXRpb25EdXJhdGlvbiIsImRyYWdnZXJXaWR0aCIsImRyYWdnZXJIZWlnaHQiLCJfcmVmcmVzaElucHV0VmFsdWVzIiwiaW5wdXRGaWVsZCIsImxlZnRQb3NpdGlvbiIsInRvcFBvc2l0aW9uIiwiJHN0YXJ0RHJhZ2dlciIsImZpbmQiLCIkc3RhcnRJbnB1dCIsIiRlbmRJbnB1dCIsIiRpbnB1dCIsImlzIiwidmFsdWUiLCJqb2luIiwidmFsIiwiX3VwZGF0ZURyYWdnZXJGcm9tSW5wdXQiLCJpbnB1dEVsZW1lbnQiLCIkZW5kRHJhZ2dlciIsImlucHV0VmFsdWUiLCJleHRyYWN0ZWRWYWx1ZXMiLCJfZXh0cmFjdFZhbHVlcyIsImZhbHNlVmFsdWVzIiwicG9zaXRpb25JblBpeGVsIiwiX2NvbnZlcnRQZXJjZW50VG9QaXhlbCIsIiRkcmFnZ2VyVG9Nb3ZlIiwiX3NldERyYWdnZXJQb3NpdGlvbiIsIl9pbml0aWFsaXplRHJhZ2dlcnMiLCIkaW1hZ2UiLCJvcHRpb25zIiwiY29udGFpbm1lbnQiLCJkcmFnIiwicGVyY2VudGFnZSIsIl9jb252ZXJ0UGl4ZWxUb1BlcmNlbnQiLCJjc3MiLCJyZXBsYWNlIiwibGVmdCIsInRvcCIsImRyYWdnYWJsZSIsImVsZW1lbnQiLCJwb3NpdGlvbiIsInJlc3VsdCIsInJlYWxXaWR0aCIsIndpZHRoIiwibGVmdFBlcmNlbnRhZ2UiLCJNYXRoIiwicm91bmQiLCJyZWFsSGVpZ2h0IiwiaGVpZ2h0IiwidG9wUGVyY2VudGFnZSIsInJlZ2V4IiwibWF0Y2giLCJsZW5ndGgiLCJfdG9nZ2xlQ2hlY2tib3giLCJkb1Nob3ciLCIkc2V0dGluZ3NDb250YWluZXIiLCIkc2V0dGluZ1Jvd3MiLCJub3QiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX3RvZ2dsZUNvbnRyb2xQYW5lbCIsIm9wYWNpdHkiLCJzZXRUaW1lb3V0IiwiX2luaXRpYWxpemVWYWx1ZXMiLCJhbmltYXRlIiwicHJvcCIsIl9pbml0aWFsaXplRXZlbnRIYW5kbGVycyIsIiRhY3RpdmF0b3IiLCIkY2hlY2tib3giLCJwYXJlbnQiLCJvbiIsIiR6b29tU3RhcnRJbnB1dCIsIiR6b29tRW5kSW5wdXQiLCIkZHVyYXRpb25JbnB1dCIsInBvc2l0aW9uRnJvbSIsInBvc2l0aW9uVG8iLCJ6b29tU3RhcnRWYWx1ZSIsInpvb21Gcm9tIiwiem9vbUVuZFZhbHVlIiwiem9vbVRvIiwiZHVyYXRpb25WYWx1ZSIsImR1cmF0aW9uIiwiX2hhbmRsZUluaXRpYWxTdGF0ZSIsIiR1c2VDaGVja2JveCIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7OztBQVlBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQ7QUFDQztBQUNBLFNBRkQ7O0FBSUM7QUFDQSxFQUxELEVBT0MsVUFBVUMsSUFBVixFQUFnQjs7QUFFZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBQ0EsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7O0FBRUE7QUFDQSxLQUFJQyxpQkFBaUJDLFFBQVFKLEtBQUtHLGNBQWIs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eEQsR0FBaEIsQ0FBb0I2RCxjQUFwQjs7QUFFQTtBQUNBO0FBQ0EsTUFBSUUsZUFBZXhHLEtBQUt5RyxNQUFMLEdBQWN6RyxLQUFLeUcsTUFBbkIsR0FBNEJ0RixjQUFjTSxPQUE3RDtBQUNBeUUsZ0JBQWN6RCxHQUFkLENBQWtCK0QsWUFBbEI7O0FBRUE7QUFDQTtBQUNBLE1BQUlFLGdCQUFnQjFHLEtBQUsyRyxRQUFMLEdBQ0EzRyxLQUFLMkcsUUFETCxHQUVBeEYsY0FBY08saUJBRmxDO0FBR0F5RSxpQkFBZTFELEdBQWYsQ0FBbUJpRSxhQUFuQjtBQUNBLEVBbkREOztBQXFEQSxLQUFJRSxzQkFBc0IsU0FBdEJBLG1CQUFzQixHQUFZO0FBQ3JDO0FBQ0EsTUFBSWYsYUFBZTNGLEVBQUVHLFVBQVVDLFNBQVosQ0FBbkI7QUFBQSxNQUNDdUcsZUFBZ0I1RyxNQUFNaUMsSUFBTixDQUFXN0IsVUFBVUUsV0FBckIsQ0FEakI7O0FBR0E7QUFDQSxNQUFJc0YsV0FBV3ZELEVBQVgsQ0FBYyxVQUFkLENBQUosRUFBK0I7QUFDOUJ5QyxtQkFBZ0IsSUFBaEI7QUFDQSxPQUFJOEIsYUFBYXZFLEVBQWIsQ0FBZ0IsVUFBaEIsQ0FBSixFQUFpQztBQUNoQ2dELHdCQUFvQixJQUFwQjtBQUNBO0FBQ0Q7QUFDRCxFQVpEOztBQWNBO0FBQ0E7QUFDQTs7QUFFQXZGLFFBQU8rRyxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUM3QjtBQUNBSDs7QUFFQTtBQUNBaEI7O0FBRUE7QUFDQW1CO0FBQ0EsRUFURDs7QUFXQSxRQUFPaEgsTUFBUDtBQUNBLENBMWZGIiwiZmlsZSI6Imdtb3Rpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGdtb3Rpb24uanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRy1Nb3Rpb24gQ29udHJvbCBFeHRlbnNpb25cbiAqXG4gKiBUaGlzIGV4dGVuc2lvbiBhbGxvd3MgeW91IHRvIG1ha2UgdXNlIG9mIEctTW90aW9uIGNvbnRyb2xzIGZvciBhIHByb2R1Y3QgaW1hZ2UuXG4gKlxuICogRWFjaCBwcm9kdWN0IHBpY3R1cmUgaGFzIGEgRy1Nb3Rpb24gY29udHJvbCBzZWN0aW9uLCB3aGVyZSB0aGUgdXNlciBpcyBhYmxlIHRvIGNoYW5nZSBHLU1vdGlvbiBcbiAqIHNldHRpbmdzIGZvciB0aGUgcmVzcGVjdGl2ZSBwaWN0dXJlLiBUaGlzIGV4dGVuc2lvbiBpcyByZXNwb25zaWJsZSBmb3Igc2hvd2luZyB0aGUgRy1Nb3Rpb24gb3B0aW9ucyBcbiAqIHVuZGVyIGVhY2ggcGljdHVyZSBhbmQgdG8gY2hhbmdlIHRoZSB2YWx1ZXMgb2YgcG9zaXRpb24gY29vcmRpbmF0ZXMuXG4gKlxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2dtb3Rpb25cbiAqIEBpZ25vcmVcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdC8vIE1vZHVsZSBuYW1lLlxuXHQnZ21vdGlvbicsXG5cblx0Ly8gTW9kdWxlIGRlcGVuZGVuY2llcy5cblx0W10sXG5cblx0ZnVuY3Rpb24gKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvLyBTaG9ydGN1dCB0byBtb2R1bGUgZWxlbWVudC5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpO1xuXG5cdFx0Ly8gSXMgdGhpcyBpbWFnZSBjb250YWluZXIgZm9yIGEgcHJpbWFyeSBpbWFnZT9cblx0XHR2YXIgaXNQcmltYXJ5SW1hZ2UgPSBCb29sZWFuKGRhdGEuaXNQcmltYXJ5SW1hZ2UpO1xuXG5cdFx0Ly8gRWxlbWVudHMgc2VsZWN0b3Igb2JqZWN0LlxuXHRcdHZhciBzZWxlY3RvcnMgPSB7XG5cdFx0XHQvLyBHZW5lcmFsIHByb2R1Y3Qtc3BlY2lmaWMgRy1Nb3Rpb24gYWN0aXZhdGlvbiBjaGVja2JveC5cblx0XHRcdGFjdGl2YXRvcjogJ1tkYXRhLWdtb3Rpb24tYWN0aXZhdG9yXScsXG5cblx0XHRcdC8vIFRoZSAnVXNlIGFzIEctTW90aW9uIGltYWdlJyBjaGVja2JveC5cblx0XHRcdHVzZUNoZWNrYm94OiAnW2RhdGEtZ21vdGlvbi1jaGVja2JveF0nLFxuXG5cdFx0XHQvLyBHLU1vdGlvbiBvbnRyb2wgcGFuZWwgY29udGFpbmVyLlxuXHRcdFx0c2V0dGluZ3NDb250YWluZXI6ICdbZGF0YS1nbW90aW9uLXNldHRpbmdzLWNvbnRhaW5lcl0nLFxuXG5cdFx0XHQvLyBTdGFydCBwb3NpdGlvbiBkcmFnZ2VyLlxuXHRcdFx0c3RhcnREcmFnZ2VyOiAnW2RhdGEtZ21vdGlvbi1zdGFydC1kcmFnZ2VyXScsXG5cblx0XHRcdC8vIEVuZCBwb3NpdGlvbiBkcmFnZ2VyLlxuXHRcdFx0ZW5kRHJhZ2dlcjogJ1tkYXRhLWdtb3Rpb24tZW5kLWRyYWdnZXJdJyxcblxuXHRcdFx0Ly8gUGxheWdyb3VuZCBpbWFnZS5cblx0XHRcdHBsYXlncm91bmRJbWFnZTogJ1tkYXRhLWdtb3Rpb24taW1hZ2VdJyxcblxuXHRcdFx0Ly8gU3RhcnQgaW5wdXQgZmllbGQuXG5cdFx0XHRzdGFydElucHV0OiAnW2RhdGEtZ21vdGlvbi1zdGFydC1pbnB1dF0nLFxuXG5cdFx0XHQvLyBFbmQgaW5wdXQgZmllbGQuXG5cdFx0XHRlbmRJbnB1dDogJ1tkYXRhLWdtb3Rpb24tZW5kLWlucHV0XScsXG5cblx0XHRcdC8vIFpvb20gZmFjdG9yIGZyb20gaW5wdXQgZmllbGQuXG5cdFx0XHR6b29tRnJvbUlucHV0OiAnW2RhdGEtZ21vdGlvbi16b29tc3RhcnQtaW5wdXRdJyxcblxuXHRcdFx0Ly8gWm9vbSBmYWN0b3IgdG8gaW5wdXQgZmllbGQuXG5cdFx0XHR6b29tVG9JbnB1dDogJ1tkYXRhLWdtb3Rpb24tem9vbXRvLWlucHV0XScsXG5cblx0XHRcdC8vIER1cmF0aW9uIGlucHV0IGZpZWxkLlxuXHRcdFx0ZHVyYXRpb25JbnB1dDogJ1tkYXRhLWdtb3Rpb24tZHVyYXRpb24taW5wdXRdJyxcblxuXHRcdFx0Ly8gU29ydCBpbnB1dCBmaWVsZC5cblx0XHRcdHNvcnRJbnB1dDogJ1tkYXRhLWdtb3Rpb24tc29ydC1pbnB1dF0nLFxuXG5cdFx0XHQvLyBHLU1vdGlvbiByb3dzLlxuXHRcdFx0c2V0dGluZ1Jvd3M6ICcuZ21vdGlvbi1zZXR0aW5nJ1xuXHRcdH07XG5cblx0XHQvLyBEZWZhdWx0IHZhbHVlcyBvYmplY3QuXG5cdFx0dmFyIGRlZmF1bHRWYWx1ZXMgPSB7XG5cdFx0XHQvLyBTdGFydCBob3Jpem9udGFsIHN3aW5nIGFuaW1hdGlvbiBwb3NpdGlvbiBkZWZhdWx0IHZhbHVlLlxuXHRcdFx0YW5pbWF0aW9uU3RhcnRMZWZ0OiAwLFxuXG5cdFx0XHQvLyBTdGFydCB2ZXJ0aWNhbCBzd2luZyBhbmltYXRpb24gcG9zaXRpb24gZGVmYXVsdCB2YWx1ZS5cblx0XHRcdGFuaW1hdGlvblN0YXJ0VG9wOiA1MCxcblxuXHRcdFx0Ly8gRW5kIGhvcml6b250YWwgc3dpbmcgYW5pbWF0aW9uIHBvc2l0aW9uIGRlZmF1bHQgdmFsdWUuXG5cdFx0XHRhbmltYXRpb25FbmRMZWZ0OiAxMDAsXG5cblx0XHRcdC8vIEVuZCB2ZXJ0aWNhbCBzd2luZyBhbmltYXRpb24gcG9zaXRpb24gZGVmYXVsdCB2YWx1ZS5cblx0XHRcdGFuaW1hdGlvbkVuZFRvcDogNTAsXG5cblx0XHRcdC8vIFpvb20gZmFjdG9yIHN0YXJ0IGRlZmF1bHQgdmFsdWUuXG5cdFx0XHR6b29tU3RhcnQ6IDEsXG5cblx0XHRcdC8vIFpvb20gZmFjdG9yIGVuZCBkZWZhdWx0IHZhbHVlLlxuXHRcdFx0em9vbUVuZDogMSxcblxuXHRcdFx0Ly8gQW5pbWF0aW9uIGR1cmF0aW9uIGRlZmF1bHQgdmFsdWUuXG5cdFx0XHRhbmltYXRpb25EdXJhdGlvbjogMTAsXG5cblx0XHRcdC8vIERyYWdnZXIgaWNvbiB3aWR0aC5cblx0XHRcdGRyYWdnZXJXaWR0aDogMTIsXG5cblx0XHRcdC8vIERyYWdnZXIgaWNvbiBoZWlnaHQuXG5cdFx0XHRkcmFnZ2VySGVpZ2h0OiAxNFxuXHRcdH07XG5cblx0XHQvLyBNb2R1bGUgb2JqZWN0LlxuXHRcdHZhciBtb2R1bGUgPSB7fTtcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdFx0LyoqXG5cdFx0ICogUmVmcmVzaGVzIHZhbHVlcyBpbiB0aGUgYXBwcm9wcmlhdGUgaW5wdXQgZmllbGQuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge2pRdWVyeXxIVE1MRWxlbWVudH0gaW5wdXRGaWVsZCBUaGUgZmllbGQgd2hpY2ggc2hvdWxkIGJlIHVwZGF0ZWQuXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IGxlZnRQb3NpdGlvbiBIb3Jpem9udGFsIHBvc2l0aW9uIHBlcmNlbnRhZ2UuXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IHRvcFBvc2l0aW9uIFZlcnRpY2FsIHBvc2l0aW9uIHBlcmNlbnRhZ2UuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcmVmcmVzaElucHV0VmFsdWVzID0gZnVuY3Rpb24gKGlucHV0RmllbGQsIGxlZnRQb3NpdGlvbiwgdG9wUG9zaXRpb24pIHtcblx0XHRcdC8vIEVsZW1lbnRzIHNob3J0Y3V0LlxuXHRcdFx0dmFyICRzdGFydERyYWdnZXIgPSAkdGhpcy5maW5kKHNlbGVjdG9ycy5zdGFydERyYWdnZXIpLFxuXHRcdFx0XHRcdCRzdGFydElucHV0IFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy5zdGFydElucHV0KSxcblx0XHRcdFx0XHQkZW5kSW5wdXQgXHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuZW5kSW5wdXQpO1xuXG5cdFx0XHR2YXIgJGlucHV0ID0gJHN0YXJ0RHJhZ2dlci5pcyhpbnB1dEZpZWxkKSA/ICRzdGFydElucHV0IDogJGVuZElucHV0O1xuXG5cdFx0XHR2YXIgdmFsdWUgPSBbXG5cdFx0XHRcdGxlZnRQb3NpdGlvbiArICclJyxcblx0XHRcdFx0dG9wUG9zaXRpb24gKyAnJSdcblx0XHRcdF0uam9pbignICcpO1xuXG5cdFx0XHQkaW5wdXQudmFsKHZhbHVlKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRmV0Y2hlcyBwZXJjZW50IHZhbHVlcyBmcm9tIGlucHV0IGZpZWxkIGFuZCB1cGRhdGVzXG5cdFx0ICogdGhlIHBvc2l0aW9uIG9mIHRoZSByZXNwZWN0aXZlIGRyYWdnZXIgZWxlbWVudC5cblx0XHQgKiBBYm9ydHMgb24gYWJub3JtYWwgdmFsdWVzLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtqUXVlcnl8SFRNTEVsZW1lbnR9IGlucHV0RWxlbWVudCBJbnB1dCBlbGVtZW50LlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVEcmFnZ2VyRnJvbUlucHV0ID0gZnVuY3Rpb24gKGlucHV0RWxlbWVudCkge1xuXHRcdFx0Ly8gRWxlbWVudHMgc2hvcnRjdXQuXG5cdFx0XHR2YXIgJHN0YXJ0RHJhZ2dlciA9ICR0aGlzLmZpbmQoc2VsZWN0b3JzLnN0YXJ0RHJhZ2dlciksXG5cdFx0XHRcdFx0JGVuZERyYWdnZXIgXHQ9ICR0aGlzLmZpbmQoc2VsZWN0b3JzLmVuZERyYWdnZXIpLFxuXHRcdFx0XHRcdCRzdGFydElucHV0IFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy5zdGFydElucHV0KTtcblxuXHRcdFx0Ly8gSW5wdXQgdmFsdWUuXG5cdFx0XHR2YXIgaW5wdXRWYWx1ZSA9ICQoaW5wdXRFbGVtZW50KS52YWwoKTtcblxuXHRcdFx0Ly8gSW5wdXQgdmFsdWUuXG5cdFx0XHR2YXIgZXh0cmFjdGVkVmFsdWVzID0gX2V4dHJhY3RWYWx1ZXMoaW5wdXRWYWx1ZSk7XG5cblx0XHRcdC8vIFJldHVybiBpbW1lZGlhdGVseSBvbiBhYm5vcm1hbCB2YWx1ZXMuXG5cdFx0XHR2YXIgZmFsc2VWYWx1ZXMgPSAoXG5cdFx0XHRcdC8vIE5vIHZhbHVlcyBleHRyYWN0ZWQuXG5cdFx0XHRcdGV4dHJhY3RlZFZhbHVlcyA9PT0gbnVsbCB8fFxuXG5cdFx0XHRcdC8vIExlZnQgdmFsdWVzIGV4Y2VlZHMgbWF4aW11bS5cblx0XHRcdFx0ZXh0cmFjdGVkVmFsdWVzWzBdID4gMTAwIHx8XG5cblx0XHRcdFx0Ly8gVG9wIHZhbHVlIGV4Y2VlZFxuXHRcdFx0XHRleHRyYWN0ZWRWYWx1ZXNbMV0gPiAxMDBcblx0XHRcdCk7XG5cblx0XHRcdGlmIChmYWxzZVZhbHVlcykge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8vIFBvc2l0aW9uIGNvbnRhaW5lciB3aXRoIHZhbHVlcyBpbiBwaXhlbC5cblx0XHRcdHZhciBwb3NpdGlvbkluUGl4ZWwgPSBfY29udmVydFBlcmNlbnRUb1BpeGVsKGV4dHJhY3RlZFZhbHVlc1swXSwgZXh0cmFjdGVkVmFsdWVzWzFdKTtcblxuXHRcdFx0Ly8gQXNzaWduIGFwcHJvcHJpYXRlIGRyYWdnZXIgZWxlbWVudC5cblx0XHRcdHZhciAkZHJhZ2dlclRvTW92ZSA9ICRzdGFydElucHV0LmlzKGlucHV0RWxlbWVudCkgP1xuXHRcdFx0ICAgICAgICAgICAgICAgICAgICAgJHN0YXJ0RHJhZ2dlciA6ICRlbmREcmFnZ2VyO1xuXG5cdFx0XHQvLyBSZXBvc2l0aW9uIGRyYWdnZXIgZWxlbWVudCB0byBuZXcgcG9zaXRpb24gdmFsdWVzLlxuXHRcdFx0X3NldERyYWdnZXJQb3NpdGlvbigkZHJhZ2dlclRvTW92ZSwgcG9zaXRpb25JblBpeGVsKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRHJhd3MgdGhlIGRyYWdnYWJsZSBoYW5kbGVyIGZvciBjb29yZGluYXRpbmdcblx0XHQgKiBzd2luZyBzdGFydCBhbmQgZW5kIHBvc2l0aW9ucy4gVXNlcyBqUXVlcnlVSSB0byBoYW5kbGUgZHJhZ2dpbmcuXG5cdFx0ICogSWYgbm8gdmFsdWVzIGFyZSBzZXQsIHRoZSBkZWZhdWx0IHBvc2l0aW9uIHZhbHVlcyB3aWxsIGJlIHNldC5cblx0XHQgKlxuXHRcdCAqIEBzZWUgalF1ZXJ5VUkgJ2RyYWdnYWJsZScgQVBJIGRvY3VtZW50YXRpb24uXG5cdFx0ICogQHJlcXVpcmVzIGpRdWVyeVVJXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2luaXRpYWxpemVEcmFnZ2VycyA9IGZ1bmN0aW9uICgpIHtcblx0XHRcdC8vIEVsZW1lbnQgc2hvcnRjdXRzLlxuXHRcdFx0dmFyICRpbWFnZSBcdFx0XHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMucGxheWdyb3VuZEltYWdlKSxcblx0XHRcdFx0XHQkc3RhcnREcmFnZ2VyID0gJHRoaXMuZmluZChzZWxlY3RvcnMuc3RhcnREcmFnZ2VyKSxcblx0XHRcdFx0XHQkZW5kRHJhZ2dlciBcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuZW5kRHJhZ2dlcik7XG5cblx0XHRcdHZhciBvcHRpb25zID0ge1xuXHRcdFx0XHRjb250YWlubWVudDogJGltYWdlLFxuXHRcdFx0XHRkcmFnOiBmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdFx0dmFyIHBlcmNlbnRhZ2UgPSBfY29udmVydFBpeGVsVG9QZXJjZW50KFxuXHRcdFx0XHRcdFx0JCh0aGlzKS5jc3MoJ2xlZnQnKS5yZXBsYWNlKCdweCcsICcnKSxcblx0XHRcdFx0XHRcdCQodGhpcykuY3NzKCd0b3AnKS5yZXBsYWNlKCdweCcsICcnKVxuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdFx0X3JlZnJlc2hJbnB1dFZhbHVlcyh0aGlzLCBwZXJjZW50YWdlLmxlZnQsIHBlcmNlbnRhZ2UudG9wKTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblxuXHRcdFx0JHN0YXJ0RHJhZ2dlci5kcmFnZ2FibGUob3B0aW9ucyk7XG5cdFx0XHQkZW5kRHJhZ2dlci5kcmFnZ2FibGUob3B0aW9ucyk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFNldHMgdGhlIHBvc2l0aW9uIG9mIGEgZHJhZ2dlci5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5fEhUTUxFbGVtZW50fSBlbGVtZW50IERyYWdnZXIgZWxlbWVudC5cblx0XHQgKiBAcGFyYW0ge29iamVjdH0gcG9zaXRpb24gUG9zaXRpb25zIHRvIHNldC5cblx0XHQgKiBAcGFyYW0ge251bWJlcn0gcG9zaXRpb24ubGVmdCBIb3Jpem9udGFsIHBvc2l0aW9uLlxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSBwb3NpdGlvbi50b3AgVmVydGljYWwgcG9zaXRpb24uXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0RHJhZ2dlclBvc2l0aW9uID0gZnVuY3Rpb24gKGVsZW1lbnQsIHBvc2l0aW9uKSB7XG5cdFx0XHQkKGVsZW1lbnQpLmNzcyhwb3NpdGlvbik7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIENvbnZlcnRzIHBpeGVsIHZhbHVlcyB0byB0aGUgcmVsYXRpdmUgcGVyY2VudCB2YWx1ZXMuXG5cdFx0ICogTm90ZTogRGltZW5zaW9ucyBvZiAkc3RhcnREcmFnZ2VyIGlzIHVzZWQgZm9yIGNhbGN1bGF0aW9uLFxuXHRcdCAqIHdoaWNoIGRvZXMgbm90IGFmZmVjdCB0aGUgZW5kIHJlc3VsdCxcblx0XHQgKiBhcyBib3RoIGRyYWdnZXJzIGhhdmUgdGhlIHNhbWUgZGltZW5zaW9ucy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSBsZWZ0UG9zaXRpb25cblx0XHQgKiBAcGFyYW0ge251bWJlcn0gdG9wUG9zaXRpb25cblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtvYmplY3R9XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NvbnZlcnRQaXhlbFRvUGVyY2VudCA9IGZ1bmN0aW9uIChsZWZ0UG9zaXRpb24sIHRvcFBvc2l0aW9uKSB7XG5cdFx0XHQvLyBFbGVtZW50IHNob3J0Y3V0cy5cblx0XHRcdHZhciAkaW1hZ2UgPSAkdGhpcy5maW5kKHNlbGVjdG9ycy5wbGF5Z3JvdW5kSW1hZ2UpO1xuXG5cdFx0XHQvLyBSZXN1bHQgb2JqZWN0LCB3aGljaCB3aWxsIGJlIHJldHVybmVkLlxuXHRcdFx0dmFyIHJlc3VsdCA9IHtcblx0XHRcdFx0bGVmdDogbnVsbCxcblx0XHRcdFx0dG9wOiBudWxsXG5cdFx0XHR9O1xuXG5cdFx0XHQvLyBDYWxjdWxhdGUgbGVmdCBwb3NpdGlvbi5cblx0XHRcdHZhciByZWFsV2lkdGggPSAkaW1hZ2Uud2lkdGgoKSAtIGRlZmF1bHRWYWx1ZXMuZHJhZ2dlcldpZHRoO1xuXHRcdFx0dmFyIGxlZnRQZXJjZW50YWdlID0gKGxlZnRQb3NpdGlvbiAvIHJlYWxXaWR0aCkgKiAxMDA7XG5cdFx0XHRyZXN1bHQubGVmdCA9IE1hdGgucm91bmQobGVmdFBlcmNlbnRhZ2UpO1xuXG5cdFx0XHQvLyBDYWxjdWxhdGUgdG9wIHBvc2l0aW9uLlxuXHRcdFx0dmFyIHJlYWxIZWlnaHQgPSAkaW1hZ2UuaGVpZ2h0KCkgLSBkZWZhdWx0VmFsdWVzLmRyYWdnZXJIZWlnaHQ7XG5cdFx0XHR2YXIgdG9wUGVyY2VudGFnZSA9ICh0b3BQb3NpdGlvbiAvIHJlYWxIZWlnaHQpICogMTAwO1xuXHRcdFx0cmVzdWx0LnRvcCA9IE1hdGgucm91bmQodG9wUGVyY2VudGFnZSk7XG5cblx0XHRcdHJldHVybiByZXN1bHQ7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIENvbnZlcnRzIHBlcmNlbnQgdmFsdWVzIHRvIHRoZSByZXNwZWN0aXZlIHBpeGVsIHZhbHVlcy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSBsZWZ0UG9zaXRpb25cblx0XHQgKiBAcGFyYW0ge251bWJlcn0gdG9wUG9zaXRpb25cblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtvYmplY3R9XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NvbnZlcnRQZXJjZW50VG9QaXhlbCA9IGZ1bmN0aW9uIChsZWZ0UG9zaXRpb24sIHRvcFBvc2l0aW9uKSB7XG5cdFx0XHQvLyBFbGVtZW50IHNob3J0Y3V0cy5cblx0XHRcdHZhciAkaW1hZ2UgPSAkdGhpcy5maW5kKHNlbGVjdG9ycy5wbGF5Z3JvdW5kSW1hZ2UpO1xuXG5cdFx0XHQvLyBSZXN1bHQgb2JqZWN0LCB3aGljaCB3aWxsIGJlIHJldHVybmVkLlxuXHRcdFx0dmFyIHJlc3VsdCA9IHtcblx0XHRcdFx0bGVmdDogbnVsbCxcblx0XHRcdFx0dG9wOiBudWxsXG5cdFx0XHR9O1xuXG5cdFx0XHQvLyBDYWxjdWxhdGUgbGVmdCBwb3NpdGlvbi5cblx0XHRcdHZhciByZWFsV2lkdGggPSAkaW1hZ2Uud2lkdGgoKSAtIGRlZmF1bHRWYWx1ZXMuZHJhZ2dlcldpZHRoO1xuXHRcdFx0cmVzdWx0LmxlZnQgPSByZWFsV2lkdGggLyAxMDAgKiBsZWZ0UG9zaXRpb247XG5cblx0XHRcdC8vIENhbGN1bGF0ZSB0b3AgcG9zaXRpb24uXG5cdFx0XHR2YXIgcmVhbEhlaWdodCA9ICRpbWFnZS5oZWlnaHQoKSAtIGRlZmF1bHRWYWx1ZXMuZHJhZ2dlckhlaWdodDtcblx0XHRcdHJlc3VsdC50b3AgPSByZWFsSGVpZ2h0IC8gMTAwICogdG9wUG9zaXRpb247XG5cblx0XHRcdHJldHVybiByZXN1bHQ7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV4dHJhY3RzIG51bWVyaWMgdmFsdWVzIGZyb20gc3RyaW5nIGFuZFxuXHRcdCAqIHJldHVybnMgdGhlIGZpcnN0IHR3byB2YWx1ZXMgaW4gYW4gYXJyYXkuXG5cdFx0ICogSGFzIHRvIHJldHVybiBhdCBsZWFzdCB0d28gZXh0cmFjdGVkIHZhbHVlcyxcblx0XHQgKiBvdGhlcndpc2UgaXQgd2lsbCByZXR1cm4gbnVsbC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZVxuXHRcdCAqIEByZXR1cm5zIHtBcnJheXxudWxsfVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9leHRyYWN0VmFsdWVzID0gZnVuY3Rpb24gKHZhbHVlKSB7XG5cdFx0XHQvLyBSZXN1bHQgd2hpY2ggd2lsbCBiZSByZXR1cm5lZC5cblx0XHRcdHZhciByZXN1bHQ7XG5cblx0XHRcdC8vIFJlZ2V4IHRvIGV4dHJhY3QgbnVtZXJpYyB2YWx1ZXMuXG5cdFx0XHR2YXIgcmVnZXggPSAvKFtcXGRdKykvZztcblxuXHRcdFx0Ly8gRXh0cmFjdGVkIHZhbHVlcyBmcm9tIGFycmF5LlxuXHRcdFx0dmFyIGV4dHJhY3RlZFZhbHVlcyA9IHZhbHVlLm1hdGNoKHJlZ2V4KTtcblxuXHRcdFx0Ly8gQ2hlY2sgaWYgYXQgbGVhc3QgdHdvIHZhbHVlcyBoYXZlIGJlZW4gZXh0cmFjdGVkXG5cdFx0XHQvLyBhbmQgYXNzaWduIHRoZW0gdG8gcmVzdWx0IHZhcmlhYmxlLFxuXHRcdFx0Ly8gb3RoZXJ3aXNlIG51bGwgd2lsbCBiZSBhc3NpZ25lZC5cblx0XHRcdGlmIChleHRyYWN0ZWRWYWx1ZXMgPT09IG51bGwgfHwgZXh0cmFjdGVkVmFsdWVzLmxlbmd0aCA8IDIpIHtcblx0XHRcdFx0cmVzdWx0ID0gbnVsbDtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdHJlc3VsdCA9IFtcblx0XHRcdFx0XHRleHRyYWN0ZWRWYWx1ZXNbMF0sXG5cdFx0XHRcdFx0ZXh0cmFjdGVkVmFsdWVzWzFdXG5cdFx0XHRcdF07XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiByZXN1bHQ7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFNob3dzL2hpZGVzIGNoZWNrYm94IGZvciBkaXNwbGF5IEctTW90aW9uIGNvbnRyb2wgcGFuZWwuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IGRvU2hvdyBEZXRlcm1pbmVzIHdoZXRoZXIgdG8gc2hvdy9oaWRlLlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF90b2dnbGVDaGVja2JveCA9IGZ1bmN0aW9uIChkb1Nob3cpIHtcblx0XHRcdC8vIEVsZW1lbnQgc2hvcnRjdXRzLlxuXHRcdFx0dmFyICRzZXR0aW5nc0NvbnRhaW5lciBcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuc2V0dGluZ3NDb250YWluZXIpLFxuXHRcdFx0XHRcdCRzZXR0aW5nUm93cyBcdFx0XHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuc2V0dGluZ1Jvd3MpO1xuXG5cdFx0XHRpZiAoZG9TaG93KSB7XG5cdFx0XHRcdCRzZXR0aW5nUm93c1xuXHRcdFx0XHRcdC5ub3QoJHNldHRpbmdzQ29udGFpbmVyKVxuXHRcdFx0XHRcdC5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkc2V0dGluZ1Jvd3MuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBTaG93cy9oaWRlcyBHLU1vdGlvbiBhbmltYXRpb24gY29udHJvbCBwYW5lbC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gZG9TaG93IERldGVybWluZXMgd2hldGhlciB0byBzaG93L2hpZGUuXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvZ2dsZUNvbnRyb2xQYW5lbCA9IGZ1bmN0aW9uIChkb1Nob3cpIHtcblx0XHRcdC8vIEVsZW1lbnQgc2hvcnRjdXRzLlxuXHRcdFx0dmFyICRzZXR0aW5nc0NvbnRhaW5lciBcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuc2V0dGluZ3NDb250YWluZXIpLFxuXHRcdFx0XHRcdCRzZXR0aW5nUm93cyBcdFx0XHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuc2V0dGluZ1Jvd3MpO1xuXG5cdFx0XHRpZiAoZG9TaG93KSB7XG5cdFx0XHRcdCRzZXR0aW5nc0NvbnRhaW5lclxuXHRcdFx0XHRcdC5yZW1vdmVDbGFzcygnaGlkZGVuJylcblx0XHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHRcdG9wYWNpdHk6IDAuMVxuXHRcdFx0XHRcdH0pO1xuXG5cdFx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdF9pbml0aWFsaXplRHJhZ2dlcnMoKTtcblx0XHRcdFx0XHRfaW5pdGlhbGl6ZVZhbHVlcygpO1xuXG5cdFx0XHRcdFx0JHNldHRpbmdzQ29udGFpbmVyXG5cdFx0XHRcdFx0XHQuYW5pbWF0ZSh7XG5cdFx0XHRcdFx0XHQgICAgIG9wYWNpdHk6IDFcblx0XHRcdFx0XHQgICAgIH0pO1xuXHRcdFx0XHR9LCAxMDAwKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdCRzZXR0aW5nc0NvbnRhaW5lci5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHR9XG5cblx0XHRcdCRzZXR0aW5nc0NvbnRhaW5lclxuXHRcdFx0XHQuZmluZCgnaW5wdXQsIHNlbGVjdCcpXG5cdFx0XHRcdC5wcm9wKCdkaXNhYmxlZCcsICFkb1Nob3cpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplcyBldmVudCBoYW5kbGVycy5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9pbml0aWFsaXplRXZlbnRIYW5kbGVycyA9IGZ1bmN0aW9uICgpIHtcblx0XHRcdC8vIEVsZW1lbnQgc2hvcnRjdXRzLlxuXHRcdFx0dmFyICRhY3RpdmF0b3IgXHRcdD0gJChzZWxlY3RvcnMuYWN0aXZhdG9yKSxcblx0XHRcdFx0XHQkY2hlY2tib3ggXHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMudXNlQ2hlY2tib3gpLFxuXHRcdFx0XHRcdCRzdGFydElucHV0IFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy5zdGFydElucHV0KSxcblx0XHRcdFx0XHQkZW5kSW5wdXQgXHRcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMuZW5kSW5wdXQpO1xuXG5cdFx0XHQvLyBIYW5kbGUgY2hlY2tib3hlcy5cblx0XHRcdC8vID09PT09PT09PT09PT09PT09PVxuXG5cdFx0XHQvLyAoRGUtKWFjdGl2YXRlcyBHLU1vdGlvbiBvcHRpb24gY2hlY2tib3hlcyBpbiBpbWFnZXMgc2V0dGluZ3MuXG5cdFx0XHQkYWN0aXZhdG9yXG5cdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdF90b2dnbGVDaGVja2JveCgkYWN0aXZhdG9yLmlzKCc6Y2hlY2tlZCcpKTtcblx0XHRcdFx0XHRfdG9nZ2xlQ29udHJvbFBhbmVsKCgkY2hlY2tib3guaXMoJzpjaGVja2VkJykgJiYgJGFjdGl2YXRvci5pcygnOmNoZWNrZWQnKSkpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0Ly8gU2hvd3MvSGlkZXMgRy1Nb3Rpb24gY29udHJvbCBwYW5lbCBvbiBjaGVja2JveCBjbGljay5cblx0XHRcdCRjaGVja2JveFxuXHRcdFx0XHQucGFyZW50KClcblx0XHRcdFx0Lm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHRfdG9nZ2xlQ29udHJvbFBhbmVsKCRjaGVja2JveC5pcygnOmNoZWNrZWQnKSk7XG5cdFx0XHRcdH0pO1xuXG5cdFx0XHQvLyBIYW5kbGUgaW5wdXQgZmllbGRzLlxuXHRcdFx0Ly8gPT09PT09PT09PT09PT09PT09PT1cblxuXHRcdFx0Ly8gVXBkYXRlIHN0YXJ0IGRyYWdnZXIgcG9zaXRpb24uXG5cdFx0XHQkc3RhcnRJbnB1dFxuXHRcdFx0XHQub24oJ2tleXVwJywgZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdF91cGRhdGVEcmFnZ2VyRnJvbUlucHV0KHRoaXMpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0Ly8gVXBkYXRlIGVuZCBkcmFnZ2VyIHBvc2l0aW9uLlxuXHRcdFx0JGVuZElucHV0XG5cdFx0XHRcdC5vbigna2V5dXAnLCBmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdFx0X3VwZGF0ZURyYWdnZXJGcm9tSW5wdXQodGhpcyk7XG5cdFx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBTZXQgdmFsdWVzLlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2luaXRpYWxpemVWYWx1ZXMgPSBmdW5jdGlvbiAoKSB7XG5cdFx0XHQvLyBFbGVtZW50IHNob3J0Y3V0cy5cblx0XHRcdHZhciAkaW1hZ2UgXHRcdFx0XHRcdFx0XHQ9ICR0aGlzLmZpbmQoc2VsZWN0b3JzLnBsYXlncm91bmRJbWFnZSksXG5cdFx0XHRcdFx0JHN0YXJ0SW5wdXQgXHRcdFx0XHQ9ICR0aGlzLmZpbmQoc2VsZWN0b3JzLnN0YXJ0SW5wdXQpLFxuXHRcdFx0XHRcdCRlbmRJbnB1dCBcdFx0XHRcdFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy5lbmRJbnB1dCksXG5cdFx0XHRcdFx0JHpvb21TdGFydElucHV0IFx0XHQ9ICR0aGlzLmZpbmQoc2VsZWN0b3JzLnpvb21Gcm9tSW5wdXQpLFxuXHRcdFx0XHRcdCR6b29tRW5kSW5wdXQgXHRcdFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy56b29tVG9JbnB1dCksXG5cdFx0XHRcdFx0JGR1cmF0aW9uSW5wdXQgXHRcdFx0PSAkdGhpcy5maW5kKHNlbGVjdG9ycy5kdXJhdGlvbklucHV0KTtcblxuXHRcdFx0Ly8gUG9zaXRpb24gc3RhcnQgdmFsdWVcblx0XHRcdC8vID09PT09PT09PT09PT09PT09PT09XG5cdFx0XHRpZiAoZGF0YS5wb3NpdGlvbkZyb20pIHtcblx0XHRcdFx0JHN0YXJ0SW5wdXQudmFsKGRhdGEucG9zaXRpb25Gcm9tKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdF9yZWZyZXNoSW5wdXRWYWx1ZXMoXG5cdFx0XHRcdFx0JHN0YXJ0SW5wdXQsXG5cdFx0XHRcdFx0ZGVmYXVsdFZhbHVlcy5hbmltYXRpb25TdGFydExlZnQsXG5cdFx0XHRcdFx0ZGVmYXVsdFZhbHVlcy5hbmltYXRpb25TdGFydFRvcFxuXHRcdFx0XHQpO1xuXHRcdFx0fVxuXHRcdFx0X3VwZGF0ZURyYWdnZXJGcm9tSW5wdXQoJHN0YXJ0SW5wdXQpO1xuXG5cdFx0XHQvLyBQb3NpdGlvbiBlbmQgdmFsdWVcblx0XHRcdC8vID09PT09PT09PT09PT09PT09PVxuXHRcdFx0aWYgKGRhdGEucG9zaXRpb25Ubykge1xuXHRcdFx0XHQkZW5kSW5wdXQudmFsKGRhdGEucG9zaXRpb25Ubyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRfcmVmcmVzaElucHV0VmFsdWVzKFxuXHRcdFx0XHRcdCRlbmRJbnB1dCxcblx0XHRcdFx0XHRkZWZhdWx0VmFsdWVzLmFuaW1hdGlvbkVuZExlZnQsXG5cdFx0XHRcdFx0ZGVmYXVsdFZhbHVlcy5hbmltYXRpb25FbmRUb3Bcblx0XHRcdFx0KTtcblx0XHRcdH1cblx0XHRcdF91cGRhdGVEcmFnZ2VyRnJvbUlucHV0KCRlbmRJbnB1dCk7XG5cblx0XHRcdC8vIFpvb20gc3RhcnQgdmFsdWVcblx0XHRcdC8vID09PT09PT09PT09PT09PT1cblx0XHRcdHZhciB6b29tU3RhcnRWYWx1ZSA9IGRhdGEuem9vbUZyb20gPyBkYXRhLnpvb21Gcm9tIDogZGVmYXVsdFZhbHVlcy56b29tU3RhcnQ7XG5cdFx0XHQkem9vbVN0YXJ0SW5wdXQudmFsKHpvb21TdGFydFZhbHVlKTtcblxuXHRcdFx0Ly8gWm9vbSBlbmQgdmFsdWVcblx0XHRcdC8vID09PT09PT09PT09PT09XG5cdFx0XHR2YXIgem9vbUVuZFZhbHVlID0gZGF0YS56b29tVG8gPyBkYXRhLnpvb21UbyA6IGRlZmF1bHRWYWx1ZXMuem9vbUVuZDtcblx0XHRcdCR6b29tRW5kSW5wdXQudmFsKHpvb21FbmRWYWx1ZSk7XG5cblx0XHRcdC8vIEFuaW1hdGlvbiBkdXJhdGlvblxuXHRcdFx0Ly8gPT09PT09PT09PT09PT09PT09XG5cdFx0XHR2YXIgZHVyYXRpb25WYWx1ZSA9IGRhdGEuZHVyYXRpb24gP1xuXHRcdFx0ICAgICAgICAgICAgICAgICAgICBkYXRhLmR1cmF0aW9uIDpcblx0XHRcdCAgICAgICAgICAgICAgICAgICAgZGVmYXVsdFZhbHVlcy5hbmltYXRpb25EdXJhdGlvbjtcblx0XHRcdCRkdXJhdGlvbklucHV0LnZhbChkdXJhdGlvblZhbHVlKTtcblx0XHR9O1xuXG5cdFx0dmFyIF9oYW5kbGVJbml0aWFsU3RhdGUgPSBmdW5jdGlvbiAoKSB7XG5cdFx0XHQvLyBFbGVtZW50IHNob3J0Y3V0cy5cblx0XHRcdHZhciAkYWN0aXZhdG9yIFx0XHQ9ICQoc2VsZWN0b3JzLmFjdGl2YXRvciksXG5cdFx0XHRcdCR1c2VDaGVja2JveCBcdD0gJHRoaXMuZmluZChzZWxlY3RvcnMudXNlQ2hlY2tib3gpO1xuXG5cdFx0XHQvLyBUb2dnbGUgY2hlY2tib3ggYW5kIHBhbmVsIGRlcGVuZGluZyBvbiBjaGVja2JveCBhY3RpdmF0aW9uIHN0YXRlcy5cblx0XHRcdGlmICgkYWN0aXZhdG9yLmlzKCc6Y2hlY2tlZCcpKSB7XG5cdFx0XHRcdF90b2dnbGVDaGVja2JveCh0cnVlKTtcblx0XHRcdFx0aWYgKCR1c2VDaGVja2JveC5pcygnOmNoZWNrZWQnKSkge1xuXHRcdFx0XHRcdF90b2dnbGVDb250cm9sUGFuZWwodHJ1ZSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cdFx0XHQvLyBIYW5kbGUgaW5pdGlhbCB2aXNpYmlsaXR5IHN0YXRlIG9mIEctTW90aW9uIGNvbnRyb2xzLlxuXHRcdFx0X2hhbmRsZUluaXRpYWxTdGF0ZSgpO1xuXG5cdFx0XHQvLyBTZXQgdXAgZXZlbnQgbGlzdGVuZXJzXG5cdFx0XHRfaW5pdGlhbGl6ZUV2ZW50SGFuZGxlcnMoKTtcblxuXHRcdFx0Ly8gUmVnaXN0ZXIgYXMgZmluaXNoZWQuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH1cbik7XG4iXX0=
