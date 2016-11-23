'use strict';

/* --------------------------------------------------------------
 offline_layer_editor.js 2016-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Site online/offline Layer Editor Controller
 *
 * @module Controllers/offline_layer_editor
 */
gx.controllers.module('offline_layer_editor', ['form', 'fallback'],

/** @lends module:Controllers/offline_layer_editor */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    lightbox_parameters = $this.data().lightboxParams,
	    module = {},
	    $fields = null,
	    appendName = '';

	// ------------------------------------------------------------------------
	// MAIN FUNCTIONALITY
	// ------------------------------------------------------------------------

	var _alterNames = function _alterNames(revert) {
		$fields.each(function () {
			var $self = $(this),
			    name = $self.attr('name');

			name = revert ? name.replace(appendName, '') : name + appendName;
			$self.attr('name', name);
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// -----------------------------------------------------------------------

	/**
  * Init function of the widget
  */
	module.init = function (done) {

		var $layer = $('#lightbox_package_' + lightbox_parameters.identifier),
		    $form = $this.find('.lightbox_content_container form'),
		    $parentForm = $(lightbox_parameters.element).closest('tr'),
		    dataset = jse.libs.fallback.getData($parentForm);

		$fields = $parentForm.find('[name]');
		appendName = '_tmp_' + parseInt(Math.random() * new Date().getTime());

		_alterNames();
		jse.libs.form.prefillForm($form, dataset, false);
		jse.libs.fallback.setupWidgetAttr($this);

		gx.extensions.init($this);
		gx.controllers.init($this);
		gx.widgets.init($this);
		gx.compatibility.init($this);

		$layer.on('click', '.ok', function () {
			$form.find('textarea').each(function () {
				var $self = $(this),
				    name = $self.attr('name'),
				    editor = window.CKEDITOR ? window.CKEDITOR.instances[name] : null;

				if (editor) {
					$self.val(editor.getData());
				}
			});

			$layer.find('form').trigger('layerClose');

			_alterNames(true);
			jse.libs.form.prefillForm($parentForm, jse.libs.fallback.getData($form), false);
			$.lightbox_plugin('close', lightbox_parameters.identifier);
		});

		$layer.on('click', '.close', function () {
			_alterNames(true);
		});

		$(window).on('JSENGINE_INIT_FINISHED', function (e, d) {
			if (d.widget === 'ckeditor') {
				$(e.target).trigger('ckeditor.update');
			}
		});

		$this.find('form').trigger('language_switcher.updateField', []);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNob3Bfb2ZmbGluZS9vZmZsaW5lX2xheWVyX2VkaXRvci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImxpZ2h0Ym94X3BhcmFtZXRlcnMiLCJsaWdodGJveFBhcmFtcyIsIiRmaWVsZHMiLCJhcHBlbmROYW1lIiwiX2FsdGVyTmFtZXMiLCJyZXZlcnQiLCJlYWNoIiwiJHNlbGYiLCJuYW1lIiwiYXR0ciIsInJlcGxhY2UiLCJpbml0IiwiZG9uZSIsIiRsYXllciIsImlkZW50aWZpZXIiLCIkZm9ybSIsImZpbmQiLCIkcGFyZW50Rm9ybSIsImVsZW1lbnQiLCJjbG9zZXN0IiwiZGF0YXNldCIsImpzZSIsImxpYnMiLCJmYWxsYmFjayIsImdldERhdGEiLCJwYXJzZUludCIsIk1hdGgiLCJyYW5kb20iLCJEYXRlIiwiZ2V0VGltZSIsImZvcm0iLCJwcmVmaWxsRm9ybSIsInNldHVwV2lkZ2V0QXR0ciIsImV4dGVuc2lvbnMiLCJ3aWRnZXRzIiwiY29tcGF0aWJpbGl0eSIsIm9uIiwiZWRpdG9yIiwid2luZG93IiwiQ0tFRElUT1IiLCJpbnN0YW5jZXMiLCJ2YWwiLCJ0cmlnZ2VyIiwibGlnaHRib3hfcGx1Z2luIiwiZSIsImQiLCJ3aWRnZXQiLCJ0YXJnZXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0Msc0JBREQsRUFHQyxDQUFDLE1BQUQsRUFBUyxVQUFULENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFEWjtBQUFBLEtBRUNDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFB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VSxPQUFWLEVBQW1CLFFBQW5CLEVBQTZCLFlBQVc7QUFDdkNoQyxlQUFZLElBQVo7QUFDQSxHQUZEOztBQUlBUixJQUFFMEMsTUFBRixFQUFVRixFQUFWLENBQWEsd0JBQWIsRUFBdUMsVUFBU1EsQ0FBVCxFQUFZQyxDQUFaLEVBQWU7QUFDckQsT0FBSUEsRUFBRUMsTUFBRixLQUFhLFVBQWpCLEVBQTZCO0FBQzVCbEQsTUFBRWdELEVBQUVHLE1BQUosRUFBWUwsT0FBWixDQUFvQixpQkFBcEI7QUFDQTtBQUNELEdBSkQ7O0FBTUEvQyxRQUFNcUIsSUFBTixDQUFXLE1BQVgsRUFBbUIwQixPQUFuQixDQUEyQiwrQkFBM0IsRUFBNEQsRUFBNUQ7O0FBRUE5QjtBQUNBLEVBdEREOztBQXdEQTtBQUNBLFFBQU9uQixNQUFQO0FBQ0EsQ0F0R0YiLCJmaWxlIjoic2hvcF9vZmZsaW5lL29mZmxpbmVfbGF5ZXJfZWRpdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvZmZsaW5lX2xheWVyX2VkaXRvci5qcyAyMDE2LTA2LTAxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBTaXRlIG9ubGluZS9vZmZsaW5lIExheWVyIEVkaXRvciBDb250cm9sbGVyXG4gKlxuICogQG1vZHVsZSBDb250cm9sbGVycy9vZmZsaW5lX2xheWVyX2VkaXRvclxuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoXG5cdCdvZmZsaW5lX2xheWVyX2VkaXRvcicsXG5cdFxuXHRbJ2Zvcm0nLCAnZmFsbGJhY2snXSxcblx0XG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL29mZmxpbmVfbGF5ZXJfZWRpdG9yICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bGlnaHRib3hfcGFyYW1ldGVycyA9ICR0aGlzLmRhdGEoKS5saWdodGJveFBhcmFtcyxcblx0XHRcdG1vZHVsZSA9IHt9LFxuXHRcdFx0JGZpZWxkcyA9IG51bGwsXG5cdFx0XHRhcHBlbmROYW1lID0gJyc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gTUFJTiBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9hbHRlck5hbWVzID0gZnVuY3Rpb24ocmV2ZXJ0KSB7XG5cdFx0XHQkZmllbGRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0bmFtZSA9ICRzZWxmLmF0dHIoJ25hbWUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdG5hbWUgPSAocmV2ZXJ0KSA/IChuYW1lLnJlcGxhY2UoYXBwZW5kTmFtZSwgJycpKSA6IChuYW1lICsgYXBwZW5kTmFtZSk7XG5cdFx0XHRcdCRzZWxmLmF0dHIoJ25hbWUnLCBuYW1lKTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHR2YXIgJGxheWVyID0gJCgnI2xpZ2h0Ym94X3BhY2thZ2VfJyArIGxpZ2h0Ym94X3BhcmFtZXRlcnMuaWRlbnRpZmllciksXG5cdFx0XHRcdCRmb3JtID0gJHRoaXMuZmluZCgnLmxpZ2h0Ym94X2NvbnRlbnRfY29udGFpbmVyIGZvcm0nKSxcblx0XHRcdFx0JHBhcmVudEZvcm0gPSAkKGxpZ2h0Ym94X3BhcmFtZXRlcnMuZWxlbWVudCkuY2xvc2VzdCgndHInKSxcblx0XHRcdFx0ZGF0YXNldCA9IGpzZS5saWJzLmZhbGxiYWNrLmdldERhdGEoJHBhcmVudEZvcm0pO1xuXHRcdFx0XG5cdFx0XHQkZmllbGRzID0gJHBhcmVudEZvcm0uZmluZCgnW25hbWVdJyk7XG5cdFx0XHRhcHBlbmROYW1lID0gJ190bXBfJyArIHBhcnNlSW50KE1hdGgucmFuZG9tKCkgKiBuZXcgRGF0ZSgpLmdldFRpbWUoKSk7XG5cdFx0XHRcblx0XHRcdF9hbHRlck5hbWVzKCk7XG5cdFx0XHRqc2UubGlicy5mb3JtLnByZWZpbGxGb3JtKCRmb3JtLCBkYXRhc2V0LCBmYWxzZSk7XG5cdFx0XHRqc2UubGlicy5mYWxsYmFjay5zZXR1cFdpZGdldEF0dHIoJHRoaXMpO1xuXHRcdFx0XG5cdFx0XHRneC5leHRlbnNpb25zLmluaXQoJHRoaXMpO1xuXHRcdFx0Z3guY29udHJvbGxlcnMuaW5pdCgkdGhpcyk7XG5cdFx0XHRneC53aWRnZXRzLmluaXQoJHRoaXMpO1xuXHRcdFx0Z3guY29tcGF0aWJpbGl0eS5pbml0KCR0aGlzKTtcblx0XHRcdFxuXHRcdFx0JGxheWVyLm9uKCdjbGljaycsICcub2snLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JGZvcm1cblx0XHRcdFx0XHQuZmluZCgndGV4dGFyZWEnKVxuXHRcdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdFx0bmFtZSA9ICRzZWxmLmF0dHIoJ25hbWUnKSxcblx0XHRcdFx0XHRcdFx0ZWRpdG9yID0gKHdpbmRvdy5DS0VESVRPUikgPyB3aW5kb3cuQ0tFRElUT1IuaW5zdGFuY2VzW25hbWVdIDogbnVsbDtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aWYgKGVkaXRvcikge1xuXHRcdFx0XHRcdFx0XHQkc2VsZi52YWwoZWRpdG9yLmdldERhdGEoKSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkbGF5ZXJcblx0XHRcdFx0XHQuZmluZCgnZm9ybScpXG5cdFx0XHRcdFx0LnRyaWdnZXIoJ2xheWVyQ2xvc2UnKTtcblx0XHRcdFx0XG5cdFx0XHRcdF9hbHRlck5hbWVzKHRydWUpO1xuXHRcdFx0XHRqc2UubGlicy5mb3JtLnByZWZpbGxGb3JtKCRwYXJlbnRGb3JtLCBqc2UubGlicy5mYWxsYmFjay5nZXREYXRhKCRmb3JtKSwgZmFsc2UpO1xuXHRcdFx0XHQkLmxpZ2h0Ym94X3BsdWdpbignY2xvc2UnLCBsaWdodGJveF9wYXJhbWV0ZXJzLmlkZW50aWZpZXIpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCRsYXllci5vbignY2xpY2snLCAnLmNsb3NlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdF9hbHRlck5hbWVzKHRydWUpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQod2luZG93KS5vbignSlNFTkdJTkVfSU5JVF9GSU5JU0hFRCcsIGZ1bmN0aW9uKGUsIGQpIHtcblx0XHRcdFx0aWYgKGQud2lkZ2V0ID09PSAnY2tlZGl0b3InKSB7XG5cdFx0XHRcdFx0JChlLnRhcmdldCkudHJpZ2dlcignY2tlZGl0b3IudXBkYXRlJyk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5maW5kKCdmb3JtJykudHJpZ2dlcignbGFuZ3VhZ2Vfc3dpdGNoZXIudXBkYXRlRmllbGQnLCBbXSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
