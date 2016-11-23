'use strict';

/* --------------------------------------------------------------
 iframe_resizer.js 2015-11-12 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## iFrame resizer
 *
 * Widget that resizes the iframes on isolated modules page
 *
 * @module Compatibility/iframe_resizer
 */
gx.compatibility.module('iframe_resizer', [],

/**  @lends module:Compatibility/iframe_resizer */

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

	var _resize = function _resize() {
		var $iframe = $this.contents(),
		    $body = $iframe.find('body'),
		    height = $body.outerHeight(),
		    width = $('.boxCenter').width() - 70;

		$this.css({ 'height': height + 'px', 'width': width + 'px' });
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_resize();
		$this.one('load', _resize);
		setInterval(_resize, 100);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImlmcmFtZV9yZXNpemVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfcmVzaXplIiwiJGlmcmFtZSIsImNvbnRlbnRzIiwiJGJvZHkiLCJmaW5kIiwiaGVpZ2h0Iiwib3V0ZXJIZWlnaHQiLCJ3aWR0aCIsImNzcyIsImluaXQiLCJkb25lIiwib25lIiwic2V0SW50ZXJ2YWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGdCQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUQsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBLEtBQUlPLFVBQVUsU0FBVkEsT0FBVSxHQUFXO0FBQ3hCLE1BQUlDLFVBQVVOLE1BQU1PLFFBQU4sRUFBZDtBQUFBLE1BQ0NDLFFBQVFGLFFBQVFHLElBQVIsQ0FBYSxNQUFiLENBRFQ7QUFBQSxNQUVDQyxTQUFTRixNQUFNRyxXQUFOLEVBRlY7QUFBQSxNQUdDQyxRQUFRWCxFQUFFLFlBQUYsRUFBZ0JXLEtBQWhCLEtBQTBCLEVBSG5DOztBQUtBWixRQUFNYSxHQUFOLENBQVUsRUFBQyxVQUFVSCxTQUFTLElBQXBCLEVBQTBCLFNBQVNFLFFBQVEsSUFBM0MsRUFBVjtBQUNBLEVBUEQ7O0FBVUE7QUFDQTtBQUNBOztBQUVBZCxRQUFPZ0IsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QlY7QUFDQUwsUUFBTWdCLEdBQU4sQ0FBVSxNQUFWLEVBQWtCWCxPQUFsQjtBQUNBWSxjQUFZWixPQUFaLEVBQXFCLEdBQXJCO0FBQ0FVO0FBQ0EsRUFMRDs7QUFPQSxRQUFPakIsTUFBUDtBQUNBLENBdEVGIiwiZmlsZSI6ImlmcmFtZV9yZXNpemVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpZnJhbWVfcmVzaXplci5qcyAyMDE1LTExLTEyIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBpRnJhbWUgcmVzaXplclxuICpcbiAqIFdpZGdldCB0aGF0IHJlc2l6ZXMgdGhlIGlmcmFtZXMgb24gaXNvbGF0ZWQgbW9kdWxlcyBwYWdlXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2lmcmFtZV9yZXNpemVyXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnaWZyYW1lX3Jlc2l6ZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9pZnJhbWVfcmVzaXplciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfcmVzaXplID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGlmcmFtZSA9ICR0aGlzLmNvbnRlbnRzKCksXG5cdFx0XHRcdCRib2R5ID0gJGlmcmFtZS5maW5kKCdib2R5JyksXG5cdFx0XHRcdGhlaWdodCA9ICRib2R5Lm91dGVySGVpZ2h0KCksXG5cdFx0XHRcdHdpZHRoID0gJCgnLmJveENlbnRlcicpLndpZHRoKCkgLSA3MDtcblx0XHRcdFxuXHRcdFx0JHRoaXMuY3NzKHsnaGVpZ2h0JzogaGVpZ2h0ICsgJ3B4JywgJ3dpZHRoJzogd2lkdGggKyAncHgnfSk7XG5cdFx0fTtcblx0XHRcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0X3Jlc2l6ZSgpO1xuXHRcdFx0JHRoaXMub25lKCdsb2FkJywgX3Jlc2l6ZSk7XG5cdFx0XHRzZXRJbnRlcnZhbChfcmVzaXplLCAxMDApO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
