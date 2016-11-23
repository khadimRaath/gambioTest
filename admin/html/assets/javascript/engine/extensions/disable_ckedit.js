'use strict';

/* --------------------------------------------------------------
 disable_ckedit.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Disable CKEdit
 *
 * Extension to enable or disable (readonly) CKEditors corresponding to a checkbox value.
 *
 * @module Admin/Extensions/disable_ckedit
 * @ignore
 */
gx.extensions.module('disable_ckedit', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE INITIALIZATION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {
		'invert': false // if true, the checkbox has to be deselected to enable the ckeditor
	},


	/**
  * Final Extension Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {},


	/**
  * Interval
  *
  * @type {number}
  */
	interval = null;

	// ------------------------------------------------------------------------
	// EVENT HANDLER
	// ------------------------------------------------------------------------

	/**
  * Switch CKEdit
  *
  * Function to detect if a CKEdit is bound to the target text field. If so,
  * set the readonly state of the box corresponding to the checkbox value.
  */
	var _switchCkEdit = function _switchCkEdit() {
		if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[options.target]) {

			if (interval) {
				clearInterval(interval);
			}

			var checked = $this.prop('checked');
			checked = options.invert ? !checked : checked;
			try {
				CKEDITOR.instances[options.target].setReadOnly(!checked);
			} catch (err) {
				interval = setInterval(function () {
					CKEDITOR.instances[options.target].setReadOnly(!checked);
					clearInterval(interval);
				}, 100);
			}
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize function of the extension, called by the engine.
  */
	module.init = function (done) {
		$this.on('change', _switchCkEdit);
		_switchCkEdit();
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRpc2FibGVfY2tlZGl0LmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbnRlcnZhbCIsIl9zd2l0Y2hDa0VkaXQiLCJ3aW5kb3ciLCJDS0VESVRPUiIsImluc3RhbmNlcyIsInRhcmdldCIsImNsZWFySW50ZXJ2YWwiLCJjaGVja2VkIiwicHJvcCIsImludmVydCIsInNldFJlYWRPbmx5IiwiZXJyIiwic2V0SW50ZXJ2YWwiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLGdCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1YsWUFBVSxLQURBLENBQ007QUFETixFQWJaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBdEJYOzs7QUF3QkM7Ozs7O0FBS0FELFVBQVMsRUE3QlY7OztBQStCQzs7Ozs7QUFLQU8sWUFBVyxJQXBDWjs7QUFzQ0E7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFNQSxLQUFJQyxnQkFBZ0IsU0FBaEJBLGFBQWdCLEdBQVc7QUFDOUIsTUFBSUMsT0FBT0MsUUFBUCxJQUFtQkEsU0FBU0MsU0FBNUIsSUFBeUNELFNBQVNDLFNBQVQsQ0FBbUJOLFFBQVFPLE1BQTNCLENBQTdDLEVBQWlGOztBQUVoRixPQUFJTCxRQUFKLEVBQWM7QUFDYk0sa0JBQWNOLFFBQWQ7QUFDQTs7QUFFRCxPQUFJTyxVQUFVWixNQUFNYSxJQUFOLENBQVcsU0FBWCxDQUFkO0FBQ0FELGFBQVdULFFBQVFXLE1BQVQsR0FBbUIsQ0FBQ0YsT0FBcEIsR0FBOEJBLE9BQXhDO0FBQ0EsT0FBSTtBQUNISixhQUFTQyxTQUFULENBQW1CTixRQUFRTyxNQUEzQixFQUFtQ0ssV0FBbkMsQ0FBK0MsQ0FBQ0gsT0FBaEQ7QUFDQSxJQUZELENBRUUsT0FBT0ksR0FBUCxFQUFZO0FBQ2JYLGVBQVdZLFlBQVksWUFBVztBQUNqQ1QsY0FBU0MsU0FBVCxDQUFtQk4sUUFBUU8sTUFBM0IsRUFBbUNLLFdBQW5DLENBQStDLENBQUNILE9BQWhEO0FBQ0FELG1CQUFjTixRQUFkO0FBQ0EsS0FIVSxFQUdSLEdBSFEsQ0FBWDtBQUlBO0FBRUQ7QUFDRCxFQW5CRDs7QUFxQkE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQVAsUUFBT29CLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJuQixRQUFNb0IsRUFBTixDQUFTLFFBQVQsRUFBbUJkLGFBQW5CO0FBQ0FBO0FBQ0FhO0FBQ0EsRUFKRDs7QUFNQTtBQUNBLFFBQU9yQixNQUFQO0FBRUEsQ0FsR0YiLCJmaWxlIjoiZGlzYWJsZV9ja2VkaXQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRpc2FibGVfY2tlZGl0LmpzIDIwMTUtMDktMTcgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIERpc2FibGUgQ0tFZGl0XG4gKlxuICogRXh0ZW5zaW9uIHRvIGVuYWJsZSBvciBkaXNhYmxlIChyZWFkb25seSkgQ0tFZGl0b3JzIGNvcnJlc3BvbmRpbmcgdG8gYSBjaGVja2JveCB2YWx1ZS5cbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvZGlzYWJsZV9ja2VkaXRcbiAqIEBpZ25vcmVcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCdkaXNhYmxlX2NrZWRpdCcsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQnaW52ZXJ0JzogZmFsc2UgLy8gaWYgdHJ1ZSwgdGhlIGNoZWNrYm94IGhhcyB0byBiZSBkZXNlbGVjdGVkIHRvIGVuYWJsZSB0aGUgY2tlZGl0b3Jcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogSW50ZXJ2YWxcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7bnVtYmVyfVxuXHRcdFx0ICovXG5cdFx0XHRpbnRlcnZhbCA9IG51bGw7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFN3aXRjaCBDS0VkaXRcblx0XHQgKlxuXHRcdCAqIEZ1bmN0aW9uIHRvIGRldGVjdCBpZiBhIENLRWRpdCBpcyBib3VuZCB0byB0aGUgdGFyZ2V0IHRleHQgZmllbGQuIElmIHNvLFxuXHRcdCAqIHNldCB0aGUgcmVhZG9ubHkgc3RhdGUgb2YgdGhlIGJveCBjb3JyZXNwb25kaW5nIHRvIHRoZSBjaGVja2JveCB2YWx1ZS5cblx0XHQgKi9cblx0XHR2YXIgX3N3aXRjaENrRWRpdCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKHdpbmRvdy5DS0VESVRPUiAmJiBDS0VESVRPUi5pbnN0YW5jZXMgJiYgQ0tFRElUT1IuaW5zdGFuY2VzW29wdGlvbnMudGFyZ2V0XSkge1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGludGVydmFsKSB7XG5cdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdHZhciBjaGVja2VkID0gJHRoaXMucHJvcCgnY2hlY2tlZCcpO1xuXHRcdFx0XHRjaGVja2VkID0gKG9wdGlvbnMuaW52ZXJ0KSA/ICFjaGVja2VkIDogY2hlY2tlZDtcblx0XHRcdFx0dHJ5IHtcblx0XHRcdFx0XHRDS0VESVRPUi5pbnN0YW5jZXNbb3B0aW9ucy50YXJnZXRdLnNldFJlYWRPbmx5KCFjaGVja2VkKTtcblx0XHRcdFx0fSBjYXRjaCAoZXJyKSB7XG5cdFx0XHRcdFx0aW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdENLRURJVE9SLmluc3RhbmNlc1tvcHRpb25zLnRhcmdldF0uc2V0UmVhZE9ubHkoIWNoZWNrZWQpO1xuXHRcdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdFx0fSwgMTAwKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBmdW5jdGlvbiBvZiB0aGUgZXh0ZW5zaW9uLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzLm9uKCdjaGFuZ2UnLCBfc3dpdGNoQ2tFZGl0KTtcblx0XHRcdF9zd2l0Y2hDa0VkaXQoKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0XHRcblx0fSk7XG4iXX0=
