/* --------------------------------------------------------------
 new_image.js 2016-02-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Products New Image Module
 *
 * This module is reponsible for handling new images added.
 *
 * @module Compatibility/new_image
 */
gx.compatibility.module(
	// Module name
	'new_image',

	// Module dependencies
	[],

	/** @lends module:Compatibility/new_image */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		// Shortcut to module element.
		var $this = $(this);

		// Elements selector object.
		var selectors = {
			addImageButton: '[data-addimage-button]',
			containerTemplate: '#image-container-template',
			newImagesList: '[data-newimages-list]'
		};

		// Animation duration (in ms).
		var ANIMATION_DURATION = 250;

		// Module object.
		var module = {};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Generates a random string. Used for creating unique element IDs.
		 * @param {Number} [charLength = 8] Maximum character length of generated string.
		 * @return {String}
		 */
		var _generateRandomId = function (charLength) {
			// Check default parameter.
			charLength = charLength || 8;

			// Generate random string.
			var randomString = Math.random()
				.toString(36)
				.substring(charLength);


			// Return generated random string.
			return randomString;
		};

		/**
		 * Renders template with provided data and returns an new jQuery element.
		 * @param  {Object} [data = {}] Template data.
		 * @return {jQuery}
		 */
		var _renderTemplate = function(data) {
			// Check data parameter.
			data = data || {};

			// Template element.
			var $template = $(selectors.containerTemplate);

			// Rendered HTML from mustache tempalte with provided data.
			var rendered = Mustache.render($template.html(), data);

			// Return jQuery-wrapped element with rendered HTML.
			return $(rendered);
		};

		/**
		 * Adds a new image container to the product image list.
		 */
		var _addImage = function (event) {
			// Reference to (new) product image list.
			var $list = $(selectors.newImagesList);

			// Create a new element with rendered product image container template.
			var $newContainer = _renderTemplate({ randomId: _generateRandomId() });

			// Apppend new element to product image list.
			$list.append($newContainer);

			// Hide and make fade animation.
			$newContainer
				.hide()
				.fadeIn(ANIMATION_DURATION);

			// Initialize JSEngine modules.
			gx.widgets.init($newContainer);
			gx.compatibility.init($newContainer);
		};

		/**
		 * Handles click events.
		 * @param {jQuery.Event} event Fired event.
		 */
		var _onClick = function (event) {
			// Reference to clicked element.
			var $clickedElement = $(event.target);

			// Reference to add new image button.
			var $addButton = $(selectors.addImageButton);

			// Check if the add image button has been clicked.
			if ($clickedElement.is($addButton)) {
				_addImage(event);
			}
		};

		module.init = function (done) {
			// Handle click event
			$this.on('click', _onClick);

			// Register as finished
			done();
		};

		return module;
	});
