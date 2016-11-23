'use strict';

/* --------------------------------------------------------------
 tabs.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the tabs / accordion
 */
gambio.widgets.module('tabs', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $tabs = null,
	    $content = null,
	    $tabList = null,
	    $contentList = null,
	    transition = {
		classOpen: 'active',
		open: false,
		calcHeight: true
	},
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Function that sets the active classes to the
  * tabs and the content headers and shows / hides
  * the content
  * @param       {integer}       index       The index of the clicked element
  * @private
  */
	var _setClasses = function _setClasses(index) {
		// Set the active tab
		$tabList.removeClass('active').eq(index).addClass('active');

		transition.open = false;
		var $hide = $contentList.filter('.active').removeClass('active').children('.tab-body'),
		    $show = $contentList.eq(index);

		$show.addClass('active').find('.tab-body').addClass('active');
	};

	// ########## EVENT HANDLER ##########

	/**
  * Click handler for the tabs. It hides
  * all other tab content except it's own
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _clickHandlerTabs = function _clickHandlerTabs(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
		    index = $self.index();

		if (!$self.hasClass('active')) {
			_setClasses(index);
		}
	};

	/**
  * Click handler for the accordion. It hides
  * all other tab content except it's own
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _clickHandlerAccordion = function _clickHandlerAccordion(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
		    $container = $self.closest('.tab-pane'),
		    index = $container.index();

		if (!$container.hasClass('active')) {
			_setClasses(index);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$tabs = $this.children('.nav-tabs');
		$tabList = $tabs.children('li');
		$content = $this.children('.tab-content');
		$contentList = $content.children('.tab-pane');

		$this.on('click', '.nav-tabs li', _clickHandlerTabs).on('click', '.tab-content .tab-heading', _clickHandlerAccordion);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdGFicy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0YWJzIiwiJGNvbnRlbnQiLCIkdGFiTGlzdCIsIiRjb250ZW50TGlzdCIsInRyYW5zaXRpb24iLCJjbGFzc09wZW4iLCJvcGVuIiwiY2FsY0hlaWdodCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRDbGFzc2VzIiwiaW5kZXgiLCJyZW1vdmVDbGFzcyIsImVxIiwiYWRkQ2xhc3MiLCIkaGlkZSIsImZpbHRlciIsImNoaWxkcmVuIiwiJHNob3ciLCJmaW5kIiwiX2NsaWNrSGFuZGxlclRhYnMiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCIkc2VsZiIsImhhc0NsYXNzIiwiX2NsaWNrSGFuZGxlckFjY29yZGlvbiIsIiRjb250YWluZXIiLCJjbG9zZXN0IiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLE1BQXRCLEVBQThCLEVBQTlCLEVBQWtDLFVBQVNDLElBQVQsRUFBZTs7QUFFaEQ7O0FBRUQ7O0FBRUMsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxRQUFRLElBRFQ7QUFBQSxLQUVDQyxXQUFXLElBRlo7QUFBQSxLQUdDQyxXQUFXLElBSFo7QUFBQSxLQUlDQyxlQUFlLElBSmhCO0FBQUEsS0FLQ0MsYUFBYTtBQUNaQyxhQUFXLFFBREM7QUFFWkMsUUFBTSxLQUZNO0FBR1pDLGNBQVk7QUFIQSxFQUxkO0FBQUEsS0FVQ0MsV0FBVyxFQVZaO0FBQUEsS0FXQ0MsVUFBVVYsRUFBRVcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QlgsSUFBN0IsQ0FYWDtBQUFBLEtBWUNELFNBQVMsRUFaVjs7QUFlRDs7QUFFQzs7Ozs7OztBQU9BLEtBQUllLGNBQWMsU0FBZEEsV0FBYyxDQUFTQyxLQUFULEVBQWdCO0FBQ2pDO0FBQ0FWLFdBQ0VXLFdBREYsQ0FDYyxRQURkLEVBRUVDLEVBRkYsQ0FFS0YsS0FGTCxFQUdFRyxRQUhGLENBR1csUUFIWDs7QUFLQVgsYUFBV0UsSUFBWCxHQUFrQixLQUFsQjtBQUNBLE1BQUlVLFFBQVFiLGFBQ1ZjLE1BRFUsQ0FDSCxTQURHLEVBRVZKLFdBRlUsQ0FFRSxRQUZGLEVBR1ZLLFFBSFUsQ0FHRCxXQUhDLENBQVo7QUFBQSxNQUlDQyxRQUFRaEIsYUFBYVcsRUFBYixDQUFnQkYsS0FBaEIsQ0FKVDs7QUFNQU8sUUFDRUosUUFERixDQUNXLFFBRFgsRUFFRUssSUFGRixDQUVPLFdBRlAsRUFHRUwsUUFIRixDQUdXLFFBSFg7QUFJQSxFQWxCRDs7QUFxQkQ7O0FBRUM7Ozs7OztBQU1BLEtBQUlNLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVNDLENBQVQsRUFBWTtBQUNuQ0EsSUFBRUMsY0FBRjtBQUNBRCxJQUFFRSxlQUFGOztBQUVBLE1BQUlDLFFBQVExQixFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0NhLFFBQVFhLE1BQU1iLEtBQU4sRUFEVDs7QUFHQSxNQUFJLENBQUNhLE1BQU1DLFFBQU4sQ0FBZSxRQUFmLENBQUwsRUFBK0I7QUFDOUJmLGVBQVlDLEtBQVo7QUFDQTtBQUNELEVBVkQ7O0FBWUE7Ozs7OztBQU1BLEtBQUllLHlCQUF5QixTQUF6QkEsc0JBQXlCLENBQVNMLENBQVQsRUFBWTtBQUN4Q0EsSUFBRUMsY0FBRjtBQUNBRCxJQUFFRSxlQUFGOztBQUVBLE1BQUlDLFFBQVExQixFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0M2QixhQUFhSCxNQUFNSSxPQUFOLENBQWMsV0FBZCxDQURkO0FBQUEsTUFFQ2pCLFFBQVFnQixXQUFXaEIsS0FBWCxFQUZUOztBQUlBLE1BQUksQ0FBQ2dCLFdBQVdGLFFBQVgsQ0FBb0IsUUFBcEIsQ0FBTCxFQUFvQztBQUNuQ2YsZUFBWUMsS0FBWjtBQUNBO0FBQ0QsRUFYRDs7QUFjRDs7QUFFQzs7OztBQUlBaEIsUUFBT2tDLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCL0IsVUFBUUYsTUFBTW9CLFFBQU4sQ0FBZSxXQUFmLENBQVI7QUFDQWhCLGFBQVdGLE1BQU1rQixRQUFOLENBQWUsSUFBZixDQUFYO0FBQ0FqQixhQUFXSCxNQUFNb0IsUUFBTixDQUFlLGNBQWYsQ0FBWDtBQUNBZixpQkFBZUYsU0FBU2lCLFFBQVQsQ0FBa0IsV0FBbEIsQ0FBZjs7QUFFQXBCLFFBQ0VrQyxFQURGLENBQ0ssT0FETCxFQUNjLGNBRGQsRUFDOEJYLGlCQUQ5QixFQUVFVyxFQUZGLENBRUssT0FGTCxFQUVjLDJCQUZkLEVBRTJDTCxzQkFGM0M7O0FBSUFJO0FBQ0EsRUFaRDs7QUFjQTtBQUNBLFFBQU9uQyxNQUFQO0FBQ0EsQ0FqSEQiLCJmaWxlIjoid2lkZ2V0cy90YWJzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0YWJzLmpzIDIwMTUtMDktMzAgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGVuYWJsZXMgdGhlIHRhYnMgLyBhY2NvcmRpb25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCd0YWJzJywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0JHRhYnMgPSBudWxsLFxuXHRcdCRjb250ZW50ID0gbnVsbCxcblx0XHQkdGFiTGlzdCA9IG51bGwsXG5cdFx0JGNvbnRlbnRMaXN0ID0gbnVsbCxcblx0XHR0cmFuc2l0aW9uID0ge1xuXHRcdFx0Y2xhc3NPcGVuOiAnYWN0aXZlJyxcblx0XHRcdG9wZW46IGZhbHNlLFxuXHRcdFx0Y2FsY0hlaWdodDogdHJ1ZVxuXHRcdH0sXG5cdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBGdW5jdGlvbiB0aGF0IHNldHMgdGhlIGFjdGl2ZSBjbGFzc2VzIHRvIHRoZVxuXHQgKiB0YWJzIGFuZCB0aGUgY29udGVudCBoZWFkZXJzIGFuZCBzaG93cyAvIGhpZGVzXG5cdCAqIHRoZSBjb250ZW50XG5cdCAqIEBwYXJhbSAgICAgICB7aW50ZWdlcn0gICAgICAgaW5kZXggICAgICAgVGhlIGluZGV4IG9mIHRoZSBjbGlja2VkIGVsZW1lbnRcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfc2V0Q2xhc3NlcyA9IGZ1bmN0aW9uKGluZGV4KSB7XG5cdFx0Ly8gU2V0IHRoZSBhY3RpdmUgdGFiXG5cdFx0JHRhYkxpc3Rcblx0XHRcdC5yZW1vdmVDbGFzcygnYWN0aXZlJylcblx0XHRcdC5lcShpbmRleClcblx0XHRcdC5hZGRDbGFzcygnYWN0aXZlJyk7XG5cblx0XHR0cmFuc2l0aW9uLm9wZW4gPSBmYWxzZTtcblx0XHR2YXIgJGhpZGUgPSAkY29udGVudExpc3Rcblx0XHRcdC5maWx0ZXIoJy5hY3RpdmUnKVxuXHRcdFx0LnJlbW92ZUNsYXNzKCdhY3RpdmUnKVxuXHRcdFx0LmNoaWxkcmVuKCcudGFiLWJvZHknKSxcblx0XHRcdCRzaG93ID0gJGNvbnRlbnRMaXN0LmVxKGluZGV4KTtcblxuXHRcdCRzaG93XG5cdFx0XHQuYWRkQ2xhc3MoJ2FjdGl2ZScpXG5cdFx0XHQuZmluZCgnLnRhYi1ib2R5Jylcblx0XHRcdC5hZGRDbGFzcygnYWN0aXZlJyk7XG5cdH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogQ2xpY2sgaGFuZGxlciBmb3IgdGhlIHRhYnMuIEl0IGhpZGVzXG5cdCAqIGFsbCBvdGhlciB0YWIgY29udGVudCBleGNlcHQgaXQncyBvd25cblx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9jbGlja0hhbmRsZXJUYWJzID0gZnVuY3Rpb24oZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdGluZGV4ID0gJHNlbGYuaW5kZXgoKTtcblxuXHRcdGlmICghJHNlbGYuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG5cdFx0XHRfc2V0Q2xhc3NlcyhpbmRleCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8qKlxuXHQgKiBDbGljayBoYW5kbGVyIGZvciB0aGUgYWNjb3JkaW9uLiBJdCBoaWRlc1xuXHQgKiBhbGwgb3RoZXIgdGFiIGNvbnRlbnQgZXhjZXB0IGl0J3Mgb3duXG5cdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfY2xpY2tIYW5kbGVyQWNjb3JkaW9uID0gZnVuY3Rpb24oZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdCRjb250YWluZXIgPSAkc2VsZi5jbG9zZXN0KCcudGFiLXBhbmUnKSxcblx0XHRcdGluZGV4ID0gJGNvbnRhaW5lci5pbmRleCgpO1xuXG5cdFx0aWYgKCEkY29udGFpbmVyLmhhc0NsYXNzKCdhY3RpdmUnKSkge1xuXHRcdFx0X3NldENsYXNzZXMoaW5kZXgpO1xuXHRcdH1cblx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdCAqIEBjb25zdHJ1Y3RvclxuXHQgKi9cblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHQkdGFicyA9ICR0aGlzLmNoaWxkcmVuKCcubmF2LXRhYnMnKTtcblx0XHQkdGFiTGlzdCA9ICR0YWJzLmNoaWxkcmVuKCdsaScpO1xuXHRcdCRjb250ZW50ID0gJHRoaXMuY2hpbGRyZW4oJy50YWItY29udGVudCcpO1xuXHRcdCRjb250ZW50TGlzdCA9ICRjb250ZW50LmNoaWxkcmVuKCcudGFiLXBhbmUnKTtcblxuXHRcdCR0aGlzXG5cdFx0XHQub24oJ2NsaWNrJywgJy5uYXYtdGFicyBsaScsIF9jbGlja0hhbmRsZXJUYWJzKVxuXHRcdFx0Lm9uKCdjbGljaycsICcudGFiLWNvbnRlbnQgLnRhYi1oZWFkaW5nJywgX2NsaWNrSGFuZGxlckFjY29yZGlvbik7XG5cblx0XHRkb25lKCk7XG5cdH07XG5cblx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRyZXR1cm4gbW9kdWxlO1xufSk7Il19
