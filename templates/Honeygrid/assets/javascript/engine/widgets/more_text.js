'use strict';

/* --------------------------------------------------------------
 more_text.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Enables the 'more' or '...' buttons in long text fields.
 */
gambio.widgets.module('more_text', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    transition = {
		classClose: 'hide',
		open: true,
		calcHeight: true
	},
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the click event on the '...'-more
  * button. It starts the transition to open the full
  * text
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _openText = function _openText(e) {
		e.preventDefault();

		var $self = $(this),
		    $container = $self.closest('.more-text-container'),
		    $fullText = $container.children('.more-text-full');

		$self.hide();
		$fullText.trigger(jse.libs.template.events.TRANSITION(), transition);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on('click', '.more-text-container .more-text-link', _openText);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9yZV90ZXh0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwidHJhbnNpdGlvbiIsImNsYXNzQ2xvc2UiLCJvcGVuIiwiY2FsY0hlaWdodCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vcGVuVGV4dCIsImUiLCJwcmV2ZW50RGVmYXVsdCIsIiRzZWxmIiwiJGNvbnRhaW5lciIsImNsb3Nlc3QiLCIkZnVsbFRleHQiLCJjaGlsZHJlbiIsImhpZGUiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwiZXZlbnRzIiwiVFJBTlNJVElPTiIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFdBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLENBSEQsRUFPQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxhQUFhO0FBQ1pDLGNBQVksTUFEQTtBQUVaQyxRQUFNLElBRk07QUFHWkMsY0FBWTtBQUhBLEVBRGQ7QUFBQSxLQU1DQyxXQUFXLEVBTlo7QUFBQSxLQU9DQyxVQUFVTixFQUFFTyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCUCxJQUE3QixDQVBYO0FBQUEsS0FRQ0YsU0FBUyxFQVJWOztBQVdGOztBQUVFOzs7Ozs7O0FBT0EsS0FBSVksWUFBWSxTQUFaQSxTQUFZLENBQVNDLENBQVQsRUFBWTtBQUMzQkEsSUFBRUMsY0FBRjs7QUFFQSxNQUFJQyxRQUFRWCxFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0NZLGFBQWFELE1BQU1FLE9BQU4sQ0FBYyxzQkFBZCxDQURkO0FBQUEsTUFFQ0MsWUFBWUYsV0FBV0csUUFBWCxDQUFvQixpQkFBcEIsQ0FGYjs7QUFJQUosUUFBTUssSUFBTjtBQUNBRixZQUFVRyxPQUFWLENBQWtCQyxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLE1BQWxCLENBQXlCQyxVQUF6QixFQUFsQixFQUF5RHJCLFVBQXpEO0FBQ0EsRUFURDs7QUFZRjs7QUFFRTs7OztBQUlBTCxRQUFPMkIsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUJ6QixRQUFNMEIsRUFBTixDQUFTLE9BQVQsRUFBa0Isc0NBQWxCLEVBQTBEakIsU0FBMUQ7O0FBRUFnQjtBQUNBLEVBTEQ7O0FBT0E7QUFDQSxRQUFPNUIsTUFBUDtBQUNBLENBNURGIiwiZmlsZSI6IndpZGdldHMvbW9yZV90ZXh0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb3JlX3RleHQuanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRW5hYmxlcyB0aGUgJ21vcmUnIG9yICcuLi4nIGJ1dHRvbnMgaW4gbG9uZyB0ZXh0IGZpZWxkcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnbW9yZV90ZXh0JyxcblxuXHRbXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXG5cdF0sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHR0cmFuc2l0aW9uID0ge1xuXHRcdFx0XHRjbGFzc0Nsb3NlOiAnaGlkZScsXG5cdFx0XHRcdG9wZW46IHRydWUsXG5cdFx0XHRcdGNhbGNIZWlnaHQ6IHRydWVcblx0XHRcdH0sXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGNsaWNrIGV2ZW50IG9uIHRoZSAnLi4uJy1tb3JlXG5cdFx0ICogYnV0dG9uLiBJdCBzdGFydHMgdGhlIHRyYW5zaXRpb24gdG8gb3BlbiB0aGUgZnVsbFxuXHRcdCAqIHRleHRcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX29wZW5UZXh0ID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkY29udGFpbmVyID0gJHNlbGYuY2xvc2VzdCgnLm1vcmUtdGV4dC1jb250YWluZXInKSxcblx0XHRcdFx0JGZ1bGxUZXh0ID0gJGNvbnRhaW5lci5jaGlsZHJlbignLm1vcmUtdGV4dC1mdWxsJyk7XG5cblx0XHRcdCRzZWxmLmhpZGUoKTtcblx0XHRcdCRmdWxsVGV4dC50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgJy5tb3JlLXRleHQtY29udGFpbmVyIC5tb3JlLXRleHQtbGluaycsIF9vcGVuVGV4dCk7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
