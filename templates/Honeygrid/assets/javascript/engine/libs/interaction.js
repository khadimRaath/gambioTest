'use strict';

/* --------------------------------------------------------------
 interaction.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.interaction = jse.libs.template.interaction || {};

/**
 * ## Honeygrid Interaction Library
 * 
 * Handles the template interactions.
 * 
 * @module Honeygrid/Libs/interaction
 * @exports jse.libs.template.interaction
 */
(function (exports) {
	'use strict';

	var $body = $('body'),
	    mousedown = false;

	/**
  * Returns the mousedown state.
  *
  * @param  {object} e Event
  * @return {boolean} True if mousedown is active 
  */
	var _clickHandler = function _clickHandler(e) {
		mousedown = e.data.mousedown;
	};

	$body.on('mousedown', { mousedown: true }, _clickHandler).on('mouseup', { mousedown: false }, _clickHandler);

	/**
  * Returns true if a mouse button is clicked.
  * 
  * @return {Boolean} Is the mouse clicked?
  */
	exports.isMouseDown = function () {
		return mousedown;
	};
})(jse.libs.template.interaction);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvaW50ZXJhY3Rpb24uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRlbXBsYXRlIiwiaW50ZXJhY3Rpb24iLCJleHBvcnRzIiwiJGJvZHkiLCIkIiwibW91c2Vkb3duIiwiX2NsaWNrSGFuZGxlciIsImUiLCJkYXRhIiwib24iLCJpc01vdXNlRG93biJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLFdBQWxCLEdBQWdDSCxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLFdBQWxCLElBQWlDLEVBQWpFOztBQUVBOzs7Ozs7OztBQVFDLFdBQVNDLE9BQVQsRUFBa0I7QUFDbEI7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxNQUFGLENBQVo7QUFBQSxLQUNDQyxZQUFZLEtBRGI7O0FBR0E7Ozs7OztBQU1BLEtBQUlDLGdCQUFnQixTQUFoQkEsYUFBZ0IsQ0FBU0MsQ0FBVCxFQUFZO0FBQy9CRixjQUFZRSxFQUFFQyxJQUFGLENBQU9ILFNBQW5CO0FBQ0EsRUFGRDs7QUFJQUYsT0FDRU0sRUFERixDQUNLLFdBREwsRUFDa0IsRUFBQ0osV0FBVyxJQUFaLEVBRGxCLEVBQ3FDQyxhQURyQyxFQUVFRyxFQUZGLENBRUssU0FGTCxFQUVnQixFQUFDSixXQUFXLEtBQVosRUFGaEIsRUFFb0NDLGFBRnBDOztBQUlBOzs7OztBQUtBSixTQUFRUSxXQUFSLEdBQXNCLFlBQVc7QUFDaEMsU0FBT0wsU0FBUDtBQUNBLEVBRkQ7QUFJQSxDQTdCQSxFQTZCQ1AsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxXQTdCbkIsQ0FBRCIsImZpbGUiOiJsaWJzL2ludGVyYWN0aW9uLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbnRlcmFjdGlvbi5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudGVtcGxhdGUuaW50ZXJhY3Rpb24gPSBqc2UubGlicy50ZW1wbGF0ZS5pbnRlcmFjdGlvbiB8fCB7fTtcblxuLyoqXG4gKiAjIyBIb25leWdyaWQgSW50ZXJhY3Rpb24gTGlicmFyeVxuICogXG4gKiBIYW5kbGVzIHRoZSB0ZW1wbGF0ZSBpbnRlcmFjdGlvbnMuXG4gKiBcbiAqIEBtb2R1bGUgSG9uZXlncmlkL0xpYnMvaW50ZXJhY3Rpb25cbiAqIEBleHBvcnRzIGpzZS5saWJzLnRlbXBsYXRlLmludGVyYWN0aW9uXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cdCd1c2Ugc3RyaWN0JztcblxuXHR2YXIgJGJvZHkgPSAkKCdib2R5JyksXG5cdFx0bW91c2Vkb3duID0gZmFsc2U7XG5cblx0LyoqXG5cdCAqIFJldHVybnMgdGhlIG1vdXNlZG93biBzdGF0ZS5cblx0ICpcblx0ICogQHBhcmFtICB7b2JqZWN0fSBlIEV2ZW50XG5cdCAqIEByZXR1cm4ge2Jvb2xlYW59IFRydWUgaWYgbW91c2Vkb3duIGlzIGFjdGl2ZSBcblx0ICovXG5cdHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdG1vdXNlZG93biA9IGUuZGF0YS5tb3VzZWRvd247XG5cdH07XG5cblx0JGJvZHlcblx0XHQub24oJ21vdXNlZG93bicsIHttb3VzZWRvd246IHRydWV9LCBfY2xpY2tIYW5kbGVyKVxuXHRcdC5vbignbW91c2V1cCcsIHttb3VzZWRvd246IGZhbHNlfSwgX2NsaWNrSGFuZGxlcik7XG5cblx0LyoqXG5cdCAqIFJldHVybnMgdHJ1ZSBpZiBhIG1vdXNlIGJ1dHRvbiBpcyBjbGlja2VkLlxuXHQgKiBcblx0ICogQHJldHVybiB7Qm9vbGVhbn0gSXMgdGhlIG1vdXNlIGNsaWNrZWQ/XG5cdCAqL1xuXHRleHBvcnRzLmlzTW91c2VEb3duID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIG1vdXNlZG93bjtcblx0fTtcblxufShqc2UubGlicy50ZW1wbGF0ZS5pbnRlcmFjdGlvbikpOyJdfQ==
