'use strict';

/* --------------------------------------------------------------
 anchor.js 2015-10-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('anchor', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		offset: 80, // Offset in px from top (to prevent the header is hiding an element)
		duration: 300 // Scroll duration in ms
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Handler for the click on an anchor
  * link. It calculates the position of
  * the target and scroll @ that position
  * @param       {object}        e           jQuery event object
  * @private
  */
	var _anchorHandler = function _anchorHandler(e) {
		var $self = $(this),
		    $target = null,
		    link = $self.attr('href'),
		    position = null;

		// Only react if the link is an anchor
		if (link && link.indexOf('#') === 0) {
			e.preventDefault();
			e.stopPropagation();

			$target = $(link);

			if ($target.length) {
				position = $target.offset().top;

				$('html, body').animate({ scrollTop: position - options.offset }, options.duration);
			}
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$this.on('click', 'a:not(.js-open-modal)', _anchorHandler);
		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW5jaG9yLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvZmZzZXQiLCJkdXJhdGlvbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJfYW5jaG9ySGFuZGxlciIsImUiLCIkc2VsZiIsIiR0YXJnZXQiLCJsaW5rIiwiYXR0ciIsInBvc2l0aW9uIiwiaW5kZXhPZiIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwibGVuZ3RoIiwidG9wIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLFFBQXRCLEVBQWdDLEVBQWhDLEVBQW9DLFVBQVNDLElBQVQsRUFBZTs7QUFFbEQ7O0FBRUQ7O0FBRUMsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxXQUFXO0FBQ1ZDLFVBQVEsRUFERSxFQUNNO0FBQ2hCQyxZQUFVLEdBRkEsQ0FFUTtBQUZSLEVBRFo7QUFBQSxLQUtDQyxVQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJKLFFBQW5CLEVBQTZCSCxJQUE3QixDQUxYO0FBQUEsS0FNQ0QsU0FBUyxFQU5WOztBQVFEOztBQUVDOzs7Ozs7O0FBT0EsS0FBSVMsaUJBQWlCLFNBQWpCQSxjQUFpQixDQUFTQyxDQUFULEVBQVk7QUFDaEMsTUFBSUMsUUFBUVIsRUFBRSxJQUFGLENBQVo7QUFBQSxNQUNDUyxVQUFVLElBRFg7QUFBQSxNQUVDQyxPQUFPRixNQUFNRyxJQUFOLENBQVcsTUFBWCxDQUZSO0FBQUEsTUFHQ0MsV0FBVyxJQUhaOztBQUtBO0FBQ0EsTUFBSUYsUUFBUUEsS0FBS0csT0FBTCxDQUFhLEdBQWIsTUFBc0IsQ0FBbEMsRUFBcUM7QUFDcENOLEtBQUVPLGNBQUY7QUFDQVAsS0FBRVEsZUFBRjs7QUFFQU4sYUFBVVQsRUFBRVUsSUFBRixDQUFWOztBQUVBLE9BQUlELFFBQVFPLE1BQVosRUFBb0I7QUFDbkJKLGVBQVdILFFBQ1RQLE1BRFMsR0FFVGUsR0FGRjs7QUFJQWpCLE1BQUUsWUFBRixFQUFnQmtCLE9BQWhCLENBQXdCLEVBQUNDLFdBQVdQLFdBQVdSLFFBQVFGLE1BQS9CLEVBQXhCLEVBQWdFRSxRQUFRRCxRQUF4RTtBQUNBO0FBQ0Q7QUFDRCxFQXJCRDs7QUF1QkQ7O0FBRUM7Ozs7QUFJQU4sUUFBT3VCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJ0QixRQUFNdUIsRUFBTixDQUFTLE9BQVQsRUFBa0IsdUJBQWxCLEVBQTJDaEIsY0FBM0M7QUFDQWU7QUFDQSxFQUhEOztBQUtBO0FBQ0EsUUFBT3hCLE1BQVA7QUFDQSxDQTNERCIsImZpbGUiOiJ3aWRnZXRzL2FuY2hvci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBhbmNob3IuanMgMjAxNS0xMC0yOCBnbVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnYW5jaG9yJywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXHJcblxyXG5cdHZhciAkdGhpcyA9ICQodGhpcyksXHJcblx0XHRkZWZhdWx0cyA9IHtcclxuXHRcdFx0b2Zmc2V0OiA4MCwgICAgIC8vIE9mZnNldCBpbiBweCBmcm9tIHRvcCAodG8gcHJldmVudCB0aGUgaGVhZGVyIGlzIGhpZGluZyBhbiBlbGVtZW50KVxyXG5cdFx0XHRkdXJhdGlvbjogMzAwICAgICAvLyBTY3JvbGwgZHVyYXRpb24gaW4gbXNcclxuXHRcdH0sXHJcblx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcclxuXHRcdG1vZHVsZSA9IHt9O1xyXG5cclxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcclxuXHJcblx0LyoqXHJcblx0ICogSGFuZGxlciBmb3IgdGhlIGNsaWNrIG9uIGFuIGFuY2hvclxyXG5cdCAqIGxpbmsuIEl0IGNhbGN1bGF0ZXMgdGhlIHBvc2l0aW9uIG9mXHJcblx0ICogdGhlIHRhcmdldCBhbmQgc2Nyb2xsIEAgdGhhdCBwb3NpdGlvblxyXG5cdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxyXG5cdCAqIEBwcml2YXRlXHJcblx0ICovXHJcblx0dmFyIF9hbmNob3JIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xyXG5cdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcclxuXHRcdFx0JHRhcmdldCA9IG51bGwsXHJcblx0XHRcdGxpbmsgPSAkc2VsZi5hdHRyKCdocmVmJyksXHJcblx0XHRcdHBvc2l0aW9uID0gbnVsbDtcclxuXHJcblx0XHQvLyBPbmx5IHJlYWN0IGlmIHRoZSBsaW5rIGlzIGFuIGFuY2hvclxyXG5cdFx0aWYgKGxpbmsgJiYgbGluay5pbmRleE9mKCcjJykgPT09IDApIHtcclxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG5cclxuXHRcdFx0JHRhcmdldCA9ICQobGluayk7XHJcblxyXG5cdFx0XHRpZiAoJHRhcmdldC5sZW5ndGgpIHtcclxuXHRcdFx0XHRwb3NpdGlvbiA9ICR0YXJnZXRcclxuXHRcdFx0XHRcdC5vZmZzZXQoKVxyXG5cdFx0XHRcdFx0LnRvcDtcclxuXHJcblx0XHRcdFx0JCgnaHRtbCwgYm9keScpLmFuaW1hdGUoe3Njcm9sbFRvcDogcG9zaXRpb24gLSBvcHRpb25zLm9mZnNldH0sIG9wdGlvbnMuZHVyYXRpb24pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fTtcclxuXHJcbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xyXG5cclxuXHQvKipcclxuXHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcclxuXHQgKiBAY29uc3RydWN0b3JcclxuXHQgKi9cclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdCR0aGlzLm9uKCdjbGljaycsICdhOm5vdCguanMtb3Blbi1tb2RhbCknLCBfYW5jaG9ySGFuZGxlcik7XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHJcblx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxyXG5cdHJldHVybiBtb2R1bGU7XHJcbn0pOyJdfQ==
