'use strict';

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
	var _generateRandomId = function _generateRandomId(charLength) {
		// Check default parameter.
		charLength = charLength || 8;

		// Generate random string.
		var randomString = Math.random().toString(36).substring(charLength);

		// Return generated random string.
		return randomString;
	};

	/**
  * Renders template with provided data and returns an new jQuery element.
  * @param  {Object} [data = {}] Template data.
  * @return {jQuery}
  */
	var _renderTemplate = function _renderTemplate(data) {
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
	var _addImage = function _addImage(event) {
		// Reference to (new) product image list.
		var $list = $(selectors.newImagesList);

		// Create a new element with rendered product image container template.
		var $newContainer = _renderTemplate({ randomId: _generateRandomId() });

		// Apppend new element to product image list.
		$list.append($newContainer);

		// Hide and make fade animation.
		$newContainer.hide().fadeIn(ANIMATION_DURATION);

		// Initialize JSEngine modules.
		gx.widgets.init($newContainer);
		gx.compatibility.init($newContainer);
	};

	/**
  * Handles click events.
  * @param {jQuery.Event} event Fired event.
  */
	var _onClick = function _onClick(event) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3RzL25ld19pbWFnZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwic2VsZWN0b3JzIiwiYWRkSW1hZ2VCdXR0b24iLCJjb250YWluZXJUZW1wbGF0ZSIsIm5ld0ltYWdlc0xpc3QiLCJBTklNQVRJT05fRFVSQVRJT04iLCJfZ2VuZXJhdGVSYW5kb21JZCIsImNoYXJMZW5ndGgiLCJyYW5kb21TdHJpbmciLCJNYXRoIiwicmFuZG9tIiwidG9TdHJpbmciLCJzdWJzdHJpbmciLCJfcmVuZGVyVGVtcGxhdGUiLCIkdGVtcGxhdGUiLCJyZW5kZXJlZCIsIk11c3RhY2hlIiwicmVuZGVyIiwiaHRtbCIsIl9hZGRJbWFnZSIsImV2ZW50IiwiJGxpc3QiLCIkbmV3Q29udGFpbmVyIiwicmFuZG9tSWQiLCJhcHBlbmQiLCJoaWRlIiwiZmFkZUluIiwid2lkZ2V0cyIsImluaXQiLCJfb25DbGljayIsIiRjbGlja2VkRWxlbWVudCIsInRhcmdldCIsIiRhZGRCdXR0b24iLCJpcyIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCO0FBQ0M7QUFDQSxXQUZEOztBQUlDO0FBQ0EsRUFMRDs7QUFPQzs7QUFFQSxVQUFVQyxJQUFWLEVBQWdCOztBQUVmOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7QUFDQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjs7QUFFQTtBQUNBLEtBQUlDLFlBQVk7QUFDZkMsa0JBQWdCLHdCQUREO0FBRWZDLHFCQUFtQiwyQkFGSjtBQUdmQyxpQkFBZTtBQUhBLEVBQWhCOztBQU1BO0FBQ0EsS0FBSUMscUJBQXFCLEdBQXpCOztBQUVBO0FBQ0EsS0FBSVIsU0FBUyxFQUFiOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxLQUFJUyxvQkFBb0IsU0FBcEJBLGlCQUFvQixDQUFVQyxVQUFWLEVBQXNCO0FBQzdDO0FBQ0FBLGVBQWFBLGNBQWMsQ0FBM0I7O0FBRUE7QUFDQSxNQUFJQyxlQUFlQyxLQUFLQyxNQUFMLEdBQ2pCQyxRQURpQixDQUNSLEVBRFEsRUFFakJDLFNBRmlCLENBRVBMLFVBRk8sQ0FBbkI7O0FBS0E7QUFDQSxTQUFPQyxZQUFQO0FBQ0EsRUFaRDs7QUFjQTs7Ozs7QUFLQSxLQUFJSyxrQkFBa0IsU0FBbEJBLGVBQWtCLENBQVNmLElBQVQsRUFBZTtBQUNwQztBQUNBQSxTQUFPQSxRQUFRLEVBQWY7O0FBRUE7QUFDQSxNQUFJZ0IsWUFBWWQsRUFBRUMsVUFBVUUsaUJBQVosQ0FBaEI7O0FBRUE7QUFDQSxNQUFJWSxXQUFXQyxTQUFTQyxNQUFULENBQWdCSCxVQUFVSSxJQUFWLEVBQWhCLEVBQWtDcEIsSUFBbEMsQ0FBZjs7QUFFQTtBQUNBLFNBQU9FLEVBQUVlLFFBQUYsQ0FBUDtBQUNBLEVBWkQ7O0FBY0E7OztBQUdBLEtBQUlJLFlBQVksU0FBWkEsU0FBWSxDQUFVQyxLQUFWLEVBQWlCO0FBQ2hDO0FBQ0EsTUFBSUMsUUFBUXJCLEVBQUVDLFVBQVVHLGFBQVosQ0FBWjs7QUFFQTtBQUNBLE1BQUlrQixnQkFBZ0JULGdCQUFnQixFQUFFVSxVQUFVakIsbUJBQVosRUFBaEIsQ0FBcEI7O0FBRUE7QUFDQWUsUUFBTUcsTUFBTixDQUFhRixhQUFiOztBQUVBO0FBQ0FBLGdCQUNFRyxJQURGLEdBRUVDLE1BRkYsQ0FFU3JCLGtCQUZUOztBQUlBO0FBQ0FWLEtBQUdnQyxPQUFILENBQVdDLElBQVgsQ0FBZ0JOLGFBQWhCO0FBQ0EzQixLQUFHQyxhQUFILENBQWlCZ0MsSUFBakIsQ0FBc0JOLGFBQXRCO0FBQ0EsRUFsQkQ7O0FBb0JBOzs7O0FBSUEsS0FBSU8sV0FBVyxTQUFYQSxRQUFXLENBQVVULEtBQVYsRUFBaUI7QUFDL0I7QUFDQSxNQUFJVSxrQkFBa0I5QixFQUFFb0IsTUFBTVcsTUFBUixDQUF0Qjs7QUFFQTtBQUNBLE1BQUlDLGFBQWFoQyxFQUFFQyxVQUFVQyxjQUFaLENBQWpCOztBQUVBO0FBQ0EsTUFBSTRCLGdCQUFnQkcsRUFBaEIsQ0FBbUJELFVBQW5CLENBQUosRUFBb0M7QUFDbkNiLGFBQVVDLEtBQVY7QUFDQTtBQUNELEVBWEQ7O0FBYUF2QixRQUFPK0IsSUFBUCxHQUFjLFVBQVVNLElBQVYsRUFBZ0I7QUFDN0I7QUFDQW5DLFFBQU1vQyxFQUFOLENBQVMsT0FBVCxFQUFrQk4sUUFBbEI7O0FBRUE7QUFDQUs7QUFDQSxFQU5EOztBQVFBLFFBQU9yQyxNQUFQO0FBQ0EsQ0E1SEYiLCJmaWxlIjoicHJvZHVjdHMvbmV3X2ltYWdlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBuZXdfaW1hZ2UuanMgMjAxNi0wMi0wMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgUHJvZHVjdHMgTmV3IEltYWdlIE1vZHVsZVxuICpcbiAqIFRoaXMgbW9kdWxlIGlzIHJlcG9uc2libGUgZm9yIGhhbmRsaW5nIG5ldyBpbWFnZXMgYWRkZWQuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L25ld19pbWFnZVxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0Ly8gTW9kdWxlIG5hbWVcblx0J25ld19pbWFnZScsXG5cblx0Ly8gTW9kdWxlIGRlcGVuZGVuY2llc1xuXHRbXSxcblxuXHQvKiogQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L25ld19pbWFnZSAqL1xuXG5cdGZ1bmN0aW9uIChkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdFx0Ly8gU2hvcnRjdXQgdG8gbW9kdWxlIGVsZW1lbnQuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKTtcblxuXHRcdC8vIEVsZW1lbnRzIHNlbGVjdG9yIG9iamVjdC5cblx0XHR2YXIgc2VsZWN0b3JzID0ge1xuXHRcdFx0YWRkSW1hZ2VCdXR0b246ICdbZGF0YS1hZGRpbWFnZS1idXR0b25dJyxcblx0XHRcdGNvbnRhaW5lclRlbXBsYXRlOiAnI2ltYWdlLWNvbnRhaW5lci10ZW1wbGF0ZScsXG5cdFx0XHRuZXdJbWFnZXNMaXN0OiAnW2RhdGEtbmV3aW1hZ2VzLWxpc3RdJ1xuXHRcdH07XG5cblx0XHQvLyBBbmltYXRpb24gZHVyYXRpb24gKGluIG1zKS5cblx0XHR2YXIgQU5JTUFUSU9OX0RVUkFUSU9OID0gMjUwO1xuXG5cdFx0Ly8gTW9kdWxlIG9iamVjdC5cblx0XHR2YXIgbW9kdWxlID0ge307XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdFx0LyoqXG5cdFx0ICogR2VuZXJhdGVzIGEgcmFuZG9tIHN0cmluZy4gVXNlZCBmb3IgY3JlYXRpbmcgdW5pcXVlIGVsZW1lbnQgSURzLlxuXHRcdCAqIEBwYXJhbSB7TnVtYmVyfSBbY2hhckxlbmd0aCA9IDhdIE1heGltdW0gY2hhcmFjdGVyIGxlbmd0aCBvZiBnZW5lcmF0ZWQgc3RyaW5nLlxuXHRcdCAqIEByZXR1cm4ge1N0cmluZ31cblx0XHQgKi9cblx0XHR2YXIgX2dlbmVyYXRlUmFuZG9tSWQgPSBmdW5jdGlvbiAoY2hhckxlbmd0aCkge1xuXHRcdFx0Ly8gQ2hlY2sgZGVmYXVsdCBwYXJhbWV0ZXIuXG5cdFx0XHRjaGFyTGVuZ3RoID0gY2hhckxlbmd0aCB8fCA4O1xuXG5cdFx0XHQvLyBHZW5lcmF0ZSByYW5kb20gc3RyaW5nLlxuXHRcdFx0dmFyIHJhbmRvbVN0cmluZyA9IE1hdGgucmFuZG9tKClcblx0XHRcdFx0LnRvU3RyaW5nKDM2KVxuXHRcdFx0XHQuc3Vic3RyaW5nKGNoYXJMZW5ndGgpO1xuXG5cblx0XHRcdC8vIFJldHVybiBnZW5lcmF0ZWQgcmFuZG9tIHN0cmluZy5cblx0XHRcdHJldHVybiByYW5kb21TdHJpbmc7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFJlbmRlcnMgdGVtcGxhdGUgd2l0aCBwcm92aWRlZCBkYXRhIGFuZCByZXR1cm5zIGFuIG5ldyBqUXVlcnkgZWxlbWVudC5cblx0XHQgKiBAcGFyYW0gIHtPYmplY3R9IFtkYXRhID0ge31dIFRlbXBsYXRlIGRhdGEuXG5cdFx0ICogQHJldHVybiB7alF1ZXJ5fVxuXHRcdCAqL1xuXHRcdHZhciBfcmVuZGVyVGVtcGxhdGUgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHQvLyBDaGVjayBkYXRhIHBhcmFtZXRlci5cblx0XHRcdGRhdGEgPSBkYXRhIHx8IHt9O1xuXG5cdFx0XHQvLyBUZW1wbGF0ZSBlbGVtZW50LlxuXHRcdFx0dmFyICR0ZW1wbGF0ZSA9ICQoc2VsZWN0b3JzLmNvbnRhaW5lclRlbXBsYXRlKTtcblxuXHRcdFx0Ly8gUmVuZGVyZWQgSFRNTCBmcm9tIG11c3RhY2hlIHRlbXBhbHRlIHdpdGggcHJvdmlkZWQgZGF0YS5cblx0XHRcdHZhciByZW5kZXJlZCA9IE11c3RhY2hlLnJlbmRlcigkdGVtcGxhdGUuaHRtbCgpLCBkYXRhKTtcblxuXHRcdFx0Ly8gUmV0dXJuIGpRdWVyeS13cmFwcGVkIGVsZW1lbnQgd2l0aCByZW5kZXJlZCBIVE1MLlxuXHRcdFx0cmV0dXJuICQocmVuZGVyZWQpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBBZGRzIGEgbmV3IGltYWdlIGNvbnRhaW5lciB0byB0aGUgcHJvZHVjdCBpbWFnZSBsaXN0LlxuXHRcdCAqL1xuXHRcdHZhciBfYWRkSW1hZ2UgPSBmdW5jdGlvbiAoZXZlbnQpIHtcblx0XHRcdC8vIFJlZmVyZW5jZSB0byAobmV3KSBwcm9kdWN0IGltYWdlIGxpc3QuXG5cdFx0XHR2YXIgJGxpc3QgPSAkKHNlbGVjdG9ycy5uZXdJbWFnZXNMaXN0KTtcblxuXHRcdFx0Ly8gQ3JlYXRlIGEgbmV3IGVsZW1lbnQgd2l0aCByZW5kZXJlZCBwcm9kdWN0IGltYWdlIGNvbnRhaW5lciB0ZW1wbGF0ZS5cblx0XHRcdHZhciAkbmV3Q29udGFpbmVyID0gX3JlbmRlclRlbXBsYXRlKHsgcmFuZG9tSWQ6IF9nZW5lcmF0ZVJhbmRvbUlkKCkgfSk7XG5cblx0XHRcdC8vIEFwcHBlbmQgbmV3IGVsZW1lbnQgdG8gcHJvZHVjdCBpbWFnZSBsaXN0LlxuXHRcdFx0JGxpc3QuYXBwZW5kKCRuZXdDb250YWluZXIpO1xuXG5cdFx0XHQvLyBIaWRlIGFuZCBtYWtlIGZhZGUgYW5pbWF0aW9uLlxuXHRcdFx0JG5ld0NvbnRhaW5lclxuXHRcdFx0XHQuaGlkZSgpXG5cdFx0XHRcdC5mYWRlSW4oQU5JTUFUSU9OX0RVUkFUSU9OKTtcblxuXHRcdFx0Ly8gSW5pdGlhbGl6ZSBKU0VuZ2luZSBtb2R1bGVzLlxuXHRcdFx0Z3gud2lkZ2V0cy5pbml0KCRuZXdDb250YWluZXIpO1xuXHRcdFx0Z3guY29tcGF0aWJpbGl0eS5pbml0KCRuZXdDb250YWluZXIpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVzIGNsaWNrIGV2ZW50cy5cblx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXG5cdFx0ICovXG5cdFx0dmFyIF9vbkNsaWNrID0gZnVuY3Rpb24gKGV2ZW50KSB7XG5cdFx0XHQvLyBSZWZlcmVuY2UgdG8gY2xpY2tlZCBlbGVtZW50LlxuXHRcdFx0dmFyICRjbGlja2VkRWxlbWVudCA9ICQoZXZlbnQudGFyZ2V0KTtcblxuXHRcdFx0Ly8gUmVmZXJlbmNlIHRvIGFkZCBuZXcgaW1hZ2UgYnV0dG9uLlxuXHRcdFx0dmFyICRhZGRCdXR0b24gPSAkKHNlbGVjdG9ycy5hZGRJbWFnZUJ1dHRvbik7XG5cblx0XHRcdC8vIENoZWNrIGlmIHRoZSBhZGQgaW1hZ2UgYnV0dG9uIGhhcyBiZWVuIGNsaWNrZWQuXG5cdFx0XHRpZiAoJGNsaWNrZWRFbGVtZW50LmlzKCRhZGRCdXR0b24pKSB7XG5cdFx0XHRcdF9hZGRJbWFnZShldmVudCk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblx0XHRcdC8vIEhhbmRsZSBjbGljayBldmVudFxuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29uQ2xpY2spO1xuXG5cdFx0XHQvLyBSZWdpc3RlciBhcyBmaW5pc2hlZFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
