'use strict';

/* --------------------------------------------------------------
 sticky_footer.js 2015-09-14 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Handle footer position for backend.
 *
 * This module will handle the footer position on scrolling or whenever the page window size changes.
 *
 * @module Compatibility/sticky_footer
 */
gx.compatibility.module('sticky_footer', [],

/**  @lends module:Compatibility/sticky_footer */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Copyright Element Selector
  *
  * @var {object}
  */
	$copyright = $('.main-bottom-copyright'),


	/**
  * Footer Offset Top
  *
  * @var {int}
  */
	initialOffsetTop = $this.offset().top,


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _checkOffset = function _checkOffset() {
		if ($(document).scrollTop() + window.innerHeight < $copyright.offset().top) {
			$this.css('position', 'fixed');
		} else if ($this.offset().top + $this.height() >= $copyright.offset().top) {
			$this.css('position', 'absolute');
		}
	};

	var _fixMainContentHeight = function _fixMainContentHeight() {
		if (initialOffsetTop + $this.height() <= window.innerHeight) {
			var newContentHeight = window.innerHeight - $('.main-page-content').offset().top;
			$('.main-page-content').css('min-height', newContentHeight + 'px');
			// First table of the page needs to be also resized.
			$('td.columnLeft2').parents('table:first').css('min-height', newContentHeight + 'px');
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Delay the footer position by some time until so that most elements are rendered
		// properly. Adjust the timeout interval approximately.
		setTimeout(function () {
			_fixMainContentHeight();

			$(window).on('scroll', _checkOffset).on('resize', _checkOffset).on('resize', _fixMainContentHeight);
			_checkOffset();
		}, 300);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN0aWNreV9mb290ZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRjb3B5cmlnaHQiLCJpbml0aWFsT2Zmc2V0VG9wIiwib2Zmc2V0IiwidG9wIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX2NoZWNrT2Zmc2V0IiwiZG9jdW1lbnQiLCJzY3JvbGxUb3AiLCJ3aW5kb3ciLCJpbm5lckhlaWdodCIsImNzcyIsImhlaWdodCIsIl9maXhNYWluQ29udGVudEhlaWdodCIsIm5ld0NvbnRlbnRIZWlnaHQiLCJwYXJlbnRzIiwiaW5pdCIsImRvbmUiLCJzZXRUaW1lb3V0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGVBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxjQUFhRCxFQUFFLHdCQUFGLENBYmQ7OztBQWVDOzs7OztBQUtBRSxvQkFBbUJILE1BQU1JLE1BQU4sR0FBZUMsR0FwQm5DOzs7QUFzQkM7Ozs7O0FBS0FDLFlBQVcsRUEzQlo7OztBQTZCQzs7Ozs7QUFLQUMsV0FBVU4sRUFBRU8sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QlAsSUFBN0IsQ0FsQ1g7OztBQW9DQzs7Ozs7QUFLQUQsVUFBUyxFQXpDVjs7QUEyQ0E7QUFDQTtBQUNBOztBQUVBLEtBQUlXLGVBQWUsU0FBZkEsWUFBZSxHQUFXO0FBQzdCLE1BQUtSLEVBQUVTLFFBQUYsRUFBWUMsU0FBWixLQUEwQkMsT0FBT0MsV0FBbEMsR0FBaURYLFdBQVdFLE1BQVgsR0FBb0JDLEdBQXpFLEVBQThFO0FBQzdFTCxTQUFNYyxHQUFOLENBQVUsVUFBVixFQUFzQixPQUF0QjtBQUNBLEdBRkQsTUFFTyxJQUFJZCxNQUFNSSxNQUFOLEdBQWVDLEdBQWYsR0FBcUJMLE1BQU1lLE1BQU4sRUFBckIsSUFBdUNiLFdBQVdFLE1BQVgsR0FBb0JDLEdBQS9ELEVBQW9FO0FBQzFFTCxTQUFNYyxHQUFOLENBQVUsVUFBVixFQUFzQixVQUF0QjtBQUNBO0FBQ0QsRUFORDs7QUFRQSxLQUFJRSx3QkFBd0IsU0FBeEJBLHFCQUF3QixHQUFXO0FBQ3RDLE1BQUliLG1CQUFtQkgsTUFBTWUsTUFBTixFQUFuQixJQUFxQ0gsT0FBT0MsV0FBaEQsRUFBNkQ7QUFDNUQsT0FBSUksbUJBQW1CTCxPQUFPQyxXQUFQLEdBQXFCWixFQUFFLG9CQUFGLEVBQXdCRyxNQUF4QixHQUFpQ0MsR0FBN0U7QUFDQUosS0FBRSxvQkFBRixFQUF3QmEsR0FBeEIsQ0FBNEIsWUFBNUIsRUFBMENHLG1CQUFtQixJQUE3RDtBQUNBO0FBQ0FoQixLQUFFLGdCQUFGLEVBQW9CaUIsT0FBcEIsQ0FBNEIsYUFBNUIsRUFBMkNKLEdBQTNDLENBQStDLFlBQS9DLEVBQTZERyxtQkFBbUIsSUFBaEY7QUFDQTtBQUNELEVBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBbkIsUUFBT3FCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI7QUFDQTtBQUNBQyxhQUFXLFlBQVc7QUFDckJMOztBQUVBZixLQUFFVyxNQUFGLEVBQ0VVLEVBREYsQ0FDSyxRQURMLEVBQ2ViLFlBRGYsRUFFRWEsRUFGRixDQUVLLFFBRkwsRUFFZWIsWUFGZixFQUdFYSxFQUhGLENBR0ssUUFITCxFQUdlTixxQkFIZjtBQUlBUDtBQUNBLEdBUkQsRUFRRyxHQVJIOztBQVVBVztBQUNBLEVBZEQ7O0FBZ0JBLFFBQU90QixNQUFQO0FBQ0EsQ0FwR0YiLCJmaWxlIjoic3RpY2t5X2Zvb3Rlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc3RpY2t5X2Zvb3Rlci5qcyAyMDE1LTA5LTE0IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBIYW5kbGUgZm9vdGVyIHBvc2l0aW9uIGZvciBiYWNrZW5kLlxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgaGFuZGxlIHRoZSBmb290ZXIgcG9zaXRpb24gb24gc2Nyb2xsaW5nIG9yIHdoZW5ldmVyIHRoZSBwYWdlIHdpbmRvdyBzaXplIGNoYW5nZXMuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L3N0aWNreV9mb290ZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdzdGlja3lfZm9vdGVyJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvc3RpY2t5X2Zvb3RlciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDb3B5cmlnaHQgRWxlbWVudCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JGNvcHlyaWdodCA9ICQoJy5tYWluLWJvdHRvbS1jb3B5cmlnaHQnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGb290ZXIgT2Zmc2V0IFRvcFxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge2ludH1cblx0XHRcdCAqL1xuXHRcdFx0aW5pdGlhbE9mZnNldFRvcCA9ICR0aGlzLm9mZnNldCgpLnRvcCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfY2hlY2tPZmZzZXQgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICgoJChkb2N1bWVudCkuc2Nyb2xsVG9wKCkgKyB3aW5kb3cuaW5uZXJIZWlnaHQpIDwgJGNvcHlyaWdodC5vZmZzZXQoKS50b3ApIHtcblx0XHRcdFx0JHRoaXMuY3NzKCdwb3NpdGlvbicsICdmaXhlZCcpO1xuXHRcdFx0fSBlbHNlIGlmICgkdGhpcy5vZmZzZXQoKS50b3AgKyAkdGhpcy5oZWlnaHQoKSA+PSAkY29weXJpZ2h0Lm9mZnNldCgpLnRvcCkge1xuXHRcdFx0XHQkdGhpcy5jc3MoJ3Bvc2l0aW9uJywgJ2Fic29sdXRlJyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2ZpeE1haW5Db250ZW50SGVpZ2h0ID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoaW5pdGlhbE9mZnNldFRvcCArICR0aGlzLmhlaWdodCgpIDw9IHdpbmRvdy5pbm5lckhlaWdodCkge1xuXHRcdFx0XHR2YXIgbmV3Q29udGVudEhlaWdodCA9IHdpbmRvdy5pbm5lckhlaWdodCAtICQoJy5tYWluLXBhZ2UtY29udGVudCcpLm9mZnNldCgpLnRvcDtcblx0XHRcdFx0JCgnLm1haW4tcGFnZS1jb250ZW50JykuY3NzKCdtaW4taGVpZ2h0JywgbmV3Q29udGVudEhlaWdodCArICdweCcpO1xuXHRcdFx0XHQvLyBGaXJzdCB0YWJsZSBvZiB0aGUgcGFnZSBuZWVkcyB0byBiZSBhbHNvIHJlc2l6ZWQuXG5cdFx0XHRcdCQoJ3RkLmNvbHVtbkxlZnQyJykucGFyZW50cygndGFibGU6Zmlyc3QnKS5jc3MoJ21pbi1oZWlnaHQnLCBuZXdDb250ZW50SGVpZ2h0ICsgJ3B4Jyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gRGVsYXkgdGhlIGZvb3RlciBwb3NpdGlvbiBieSBzb21lIHRpbWUgdW50aWwgc28gdGhhdCBtb3N0IGVsZW1lbnRzIGFyZSByZW5kZXJlZFxuXHRcdFx0Ly8gcHJvcGVybHkuIEFkanVzdCB0aGUgdGltZW91dCBpbnRlcnZhbCBhcHByb3hpbWF0ZWx5LlxuXHRcdFx0c2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0X2ZpeE1haW5Db250ZW50SGVpZ2h0KCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkKHdpbmRvdylcblx0XHRcdFx0XHQub24oJ3Njcm9sbCcsIF9jaGVja09mZnNldClcblx0XHRcdFx0XHQub24oJ3Jlc2l6ZScsIF9jaGVja09mZnNldClcblx0XHRcdFx0XHQub24oJ3Jlc2l6ZScsIF9maXhNYWluQ29udGVudEhlaWdodCk7XG5cdFx0XHRcdF9jaGVja09mZnNldCgpO1xuXHRcdFx0fSwgMzAwKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
