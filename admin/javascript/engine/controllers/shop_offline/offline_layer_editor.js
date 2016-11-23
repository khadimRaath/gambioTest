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
gx.controllers.module(
	'offline_layer_editor',
	
	['form', 'fallback'],
	
	/** @lends module:Controllers/offline_layer_editor */
	
	function(data) {
		
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
		
		var _alterNames = function(revert) {
			$fields.each(function() {
				var $self = $(this),
					name = $self.attr('name');
				
				name = (revert) ? (name.replace(appendName, '')) : (name + appendName);
				$self.attr('name', name);
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// -----------------------------------------------------------------------
		
		/**
		 * Init function of the widget
		 */
		module.init = function(done) {
			
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
			
			$layer.on('click', '.ok', function() {
				$form
					.find('textarea')
					.each(function() {
						var $self = $(this),
							name = $self.attr('name'),
							editor = (window.CKEDITOR) ? window.CKEDITOR.instances[name] : null;
						
						if (editor) {
							$self.val(editor.getData());
						}
					});
				
				$layer
					.find('form')
					.trigger('layerClose');
				
				_alterNames(true);
				jse.libs.form.prefillForm($parentForm, jse.libs.fallback.getData($form), false);
				$.lightbox_plugin('close', lightbox_parameters.identifier);
			});
			
			$layer.on('click', '.close', function() {
				_alterNames(true);
			});
			
			$(window).on('JSENGINE_INIT_FINISHED', function(e, d) {
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
