/* --------------------------------------------------------------
 loading_spinner.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.loading_spinner = jse.libs.loading_spinner || {};

/**
 * ## Loading Spinner Library
 *
 * This library provides an easy and simple way to display a loading spinner inside any container
 * element to provide a smooth "loading" experience to the UI. If no container is specified then
 * the whole page will be taken for the display. The loading spinner comes from the Font Awesome
 * "fa-spinner" class. You can load this library as a dependency to existing modules.
 *
 * The following usage example will show you how to display and hide the spinner inside an element.
 *
 * ```javascript
 * // Create a selector variable for the target element.
 * var $targetElement = $('#my-div');
 *
 * // The $targetElement will be overlayed by the spinner.
 * var $spinner = window.jse.libs.loading_spinner.show($targetElement);
 *
 * // Do some stuff ...
 *
 * // Hide the spinner when the job is done.
 * window.jse.loading_spinner.hide($spinner);
 * ```
 *
 * @module JSE/Libs/loading_spinner
 * @exports jse.libs.loading_spinner
 */
(function(exports) {
	
	'use strict';
	
	/**
	 * Contains a list of the active spinners so that they can be validated
	 * before they are destroyed.
	 *
	 * @type {Array}
	 */
	const instances = [];
	
	/**
	 * Show the loading spinner to the target element.
	 *
	 * @param {jQuery} $targetElement (optional) The target element will be overlayed by the spinner. If no
	 * argument is provided then the spinner will overlay the whole page.
	 * @param {String} zIndex Optional ('auto'), give a specific z-index value to the loading spinner. 
	 *
	 * @return {jQuery} Returns the selector of the spinner div element. You can further manipulate the spinner
	 * if required, but you have to provide this selector as a parameter to the "hide" method below.
	 */
	exports.show = function($targetElement, zIndex = 'auto') {
		if ($targetElement !== undefined && typeof $targetElement !== 'object') {
			throw new Error('Invalid argument provided for the "show" method: ' + typeof $targetElement);
		}
		
		if ($targetElement.length === 0) {
			return; // No element matches the provided selector. 
		}
		
		$targetElement = $targetElement || $('body'); // set default value
		
		const $spinner = $('<div class="loading-spinner"></div>');
		const fontSize = 80;
		
		$spinner
			.html('<i class="fa fa-spinner fa-spin"></i>')
			.css({
				width: $targetElement.innerWidth() + 'px',
				height: $targetElement.innerHeight() + 'px',
				boxSizing: 'border-box',
				background: '#FFF',
				opacity: '0.8',
				position: 'absolute',
				top: $targetElement.offset().top,
				left: $targetElement.offset().left,
				fontSize: fontSize + 'px',
				color: '#2196F3', // primary color
				zIndex: zIndex
			})
			.appendTo('body');
		
		$spinner.find('i').css({
			position: 'absolute',
			left: $spinner.width() / 2 - fontSize / 2,
			top: $spinner.height() / 2 - fontSize / 2
		});
		
		instances.push($spinner);
		
		return $spinner;
	};
	
	/**
	 * Hide an existing spinner.
	 *
	 * This method will hide and remove the loading spinner markup from the document entirely.
	 *
	 * @param {jQuery} $spinner Must be the selector provided from the "show" method. If the selector
	 * is invalid or no elements were found then an exception will be thrown.
	 *
	 * @return {jQuery.Promise} Returns a promise object that will be resolved once the spinner is removed.
	 *
	 * @throws Error If the $spinner selector was not found in the spinner instances.
	 */
	exports.hide = function($spinner) {
		const index = instances.indexOf($spinner); 
		const deferred = $.Deferred();
		
		if (index === -1) {
			throw new Error('The provided spinner instance does not exist.');
		}
		
		instances.splice(index, 1);
		
		$spinner.fadeOut(400, function() {
			$spinner.remove();
			deferred.resolve();
		});
		
		return deferred.promise();
	};
	
})(jse.libs.loading_spinner);
