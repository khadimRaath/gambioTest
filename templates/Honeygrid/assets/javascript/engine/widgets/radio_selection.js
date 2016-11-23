'use strict';

/* --------------------------------------------------------------
 radio_selection.js 2016-03-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('radio_selection', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		selection: '.list-group-item',
		className: 'active',
		init: false
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########


	var _changeHandler = function _changeHandler() {
		var $self = $(this);

		$this.find(options.selection).removeClass(options.className);

		$self.closest(options.selection).addClass(options.className);
	};

	var _changeHandlerCheckbox = function _changeHandlerCheckbox() {
		var $self = $(this),
		    $row = $self.closest(options.selection),
		    checked = $self.prop('checked');

		if (checked) {
			$row.addClass(options.className);
		} else {
			$row.removeClass(options.className);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$this.on('change', 'input:radio', _changeHandler).on('change', 'input:checkbox', _changeHandlerCheckbox);

		if (options.init) {
			$this.find('input:checkbox, input:radio:checked').trigger('change', []);
		}

		$this.find('.list-group-item').on('click', function () {
			$(this).find('label input:radio').first().prop('checked', true).trigger('change');
		});

		$this.find('.list-group-item').each(function () {
			if ($(this).find('label input:radio').length > 0) {
				$(this).css({ cursor: 'pointer' });
			}
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcmFkaW9fc2VsZWN0aW9uLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJzZWxlY3Rpb24iLCJjbGFzc05hbWUiLCJpbml0Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9jaGFuZ2VIYW5kbGVyIiwiJHNlbGYiLCJmaW5kIiwicmVtb3ZlQ2xhc3MiLCJjbG9zZXN0IiwiYWRkQ2xhc3MiLCJfY2hhbmdlSGFuZGxlckNoZWNrYm94IiwiJHJvdyIsImNoZWNrZWQiLCJwcm9wIiwiZG9uZSIsIm9uIiwidHJpZ2dlciIsImZpcnN0IiwiZWFjaCIsImxlbmd0aCIsImNzcyIsImN1cnNvciJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsaUJBQXRCLEVBQXlDLEVBQXpDLEVBQTZDLFVBQVNDLElBQVQsRUFBZTs7QUFFM0Q7O0FBRUQ7O0FBRUMsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxXQUFXO0FBQ1ZDLGFBQVcsa0JBREQ7QUFFVkMsYUFBVyxRQUZEO0FBR1ZDLFFBQU07QUFISSxFQURaO0FBQUEsS0FNQ0MsVUFBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CTCxRQUFuQixFQUE2QkgsSUFBN0IsQ0FOWDtBQUFBLEtBT0NELFNBQVMsRUFQVjs7QUFVRDs7O0FBR0MsS0FBSVUsaUJBQWlCLFNBQWpCQSxjQUFpQixHQUFXO0FBQy9CLE1BQUlDLFFBQVFSLEVBQUUsSUFBRixDQUFaOztBQUVBRCxRQUNFVSxJQURGLENBQ09KLFFBQVFILFNBRGYsRUFFRVEsV0FGRixDQUVjTCxRQUFRRixTQUZ0Qjs7QUFJQUssUUFDRUcsT0FERixDQUNVTixRQUFRSCxTQURsQixFQUVFVSxRQUZGLENBRVdQLFFBQVFGLFNBRm5CO0FBR0EsRUFWRDs7QUFZQSxLQUFJVSx5QkFBeUIsU0FBekJBLHNCQUF5QixHQUFXO0FBQ3ZDLE1BQUlMLFFBQVFSLEVBQUUsSUFBRixDQUFaO0FBQUEsTUFDQ2MsT0FBT04sTUFBTUcsT0FBTixDQUFjTixRQUFRSCxTQUF0QixDQURSO0FBQUEsTUFFQ2EsVUFBVVAsTUFBTVEsSUFBTixDQUFXLFNBQVgsQ0FGWDs7QUFLQSxNQUFJRCxPQUFKLEVBQWE7QUFDWkQsUUFBS0YsUUFBTCxDQUFjUCxRQUFRRixTQUF0QjtBQUNBLEdBRkQsTUFFTztBQUNOVyxRQUFLSixXQUFMLENBQWlCTCxRQUFRRixTQUF6QjtBQUNBO0FBQ0QsRUFYRDs7QUFjRDs7QUFFQzs7OztBQUlBTixRQUFPTyxJQUFQLEdBQWMsVUFBU2EsSUFBVCxFQUFlO0FBQzVCbEIsUUFDRW1CLEVBREYsQ0FDSyxRQURMLEVBQ2UsYUFEZixFQUM4QlgsY0FEOUIsRUFFRVcsRUFGRixDQUVLLFFBRkwsRUFFZSxnQkFGZixFQUVpQ0wsc0JBRmpDOztBQUlBLE1BQUlSLFFBQVFELElBQVosRUFBa0I7QUFDakJMLFNBQ0VVLElBREYsQ0FDTyxxQ0FEUCxFQUVFVSxPQUZGLENBRVUsUUFGVixFQUVvQixFQUZwQjtBQUdBOztBQUVEcEIsUUFBTVUsSUFBTixDQUFXLGtCQUFYLEVBQStCUyxFQUEvQixDQUFrQyxPQUFsQyxFQUEyQyxZQUFXO0FBQ3JEbEIsS0FBRSxJQUFGLEVBQVFTLElBQVIsQ0FBYSxtQkFBYixFQUFrQ1csS0FBbEMsR0FBMENKLElBQTFDLENBQStDLFNBQS9DLEVBQTBELElBQTFELEVBQWdFRyxPQUFoRSxDQUF3RSxRQUF4RTtBQUNBLEdBRkQ7O0FBSUFwQixRQUFNVSxJQUFOLENBQVcsa0JBQVgsRUFBK0JZLElBQS9CLENBQW9DLFlBQVc7QUFDOUMsT0FBSXJCLEVBQUUsSUFBRixFQUFRUyxJQUFSLENBQWEsbUJBQWIsRUFBa0NhLE1BQWxDLEdBQTJDLENBQS9DLEVBQWtEO0FBQ2pEdEIsTUFBRSxJQUFGLEVBQVF1QixHQUFSLENBQVksRUFBQ0MsUUFBUSxTQUFULEVBQVo7QUFDQTtBQUNELEdBSkQ7O0FBTUFQO0FBQ0EsRUF0QkQ7O0FBd0JBO0FBQ0EsUUFBT3BCLE1BQVA7QUFDQSxDQTdFRCIsImZpbGUiOiJ3aWRnZXRzL3JhZGlvX3NlbGVjdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcmFkaW9fc2VsZWN0aW9uLmpzIDIwMTYtMDMtMThcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ3JhZGlvX3NlbGVjdGlvbicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0c2VsZWN0aW9uOiAnLmxpc3QtZ3JvdXAtaXRlbScsXG5cdFx0XHRjbGFzc05hbWU6ICdhY3RpdmUnLFxuXHRcdFx0aW5pdDogZmFsc2Vcblx0XHR9LFxuXHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblxuXHR2YXIgX2NoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpO1xuXG5cdFx0JHRoaXNcblx0XHRcdC5maW5kKG9wdGlvbnMuc2VsZWN0aW9uKVxuXHRcdFx0LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NOYW1lKTtcblxuXHRcdCRzZWxmXG5cdFx0XHQuY2xvc2VzdChvcHRpb25zLnNlbGVjdGlvbilcblx0XHRcdC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzTmFtZSk7XG5cdH07XG5cblx0dmFyIF9jaGFuZ2VIYW5kbGVyQ2hlY2tib3ggPSBmdW5jdGlvbigpIHtcblx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0JHJvdyA9ICRzZWxmLmNsb3Nlc3Qob3B0aW9ucy5zZWxlY3Rpb24pLFxuXHRcdFx0Y2hlY2tlZCA9ICRzZWxmLnByb3AoJ2NoZWNrZWQnKTtcblxuXG5cdFx0aWYgKGNoZWNrZWQpIHtcblx0XHRcdCRyb3cuYWRkQ2xhc3Mob3B0aW9ucy5jbGFzc05hbWUpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHQkcm93LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NOYW1lKTtcblx0XHR9XG5cdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0LyoqXG5cdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHQgKiBAY29uc3RydWN0b3Jcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdCR0aGlzXG5cdFx0XHQub24oJ2NoYW5nZScsICdpbnB1dDpyYWRpbycsIF9jaGFuZ2VIYW5kbGVyKVxuXHRcdFx0Lm9uKCdjaGFuZ2UnLCAnaW5wdXQ6Y2hlY2tib3gnLCBfY2hhbmdlSGFuZGxlckNoZWNrYm94KTtcblxuXHRcdGlmIChvcHRpb25zLmluaXQpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maW5kKCdpbnB1dDpjaGVja2JveCwgaW5wdXQ6cmFkaW86Y2hlY2tlZCcpXG5cdFx0XHRcdC50cmlnZ2VyKCdjaGFuZ2UnLCBbXSk7XG5cdFx0fVxuXHRcdFxuXHRcdCR0aGlzLmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0nKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcblx0XHRcdCQodGhpcykuZmluZCgnbGFiZWwgaW5wdXQ6cmFkaW8nKS5maXJzdCgpLnByb3AoJ2NoZWNrZWQnLCB0cnVlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHR9KTtcblx0XHRcblx0XHQkdGhpcy5maW5kKCcubGlzdC1ncm91cC1pdGVtJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdGlmICgkKHRoaXMpLmZpbmQoJ2xhYmVsIGlucHV0OnJhZGlvJykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHQkKHRoaXMpLmNzcyh7Y3Vyc29yOiAncG9pbnRlcid9KTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHRkb25lKCk7XG5cdH07XG5cblx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRyZXR1cm4gbW9kdWxlO1xufSk7Il19
