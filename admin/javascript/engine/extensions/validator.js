/* --------------------------------------------------------------
 validator.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Validator Extension
 * 
 * Validate form elements for common rules such as required fields, email addresses and other useful
 * premade types. You can add new validation types by appending the list in the end of this file.
 *
 * #### Methods
 * ```javascript
 * $parent.trigger('validator.validate'); // Trigger validation manually.
 * $parent.trigger('validator.reset'); // Reset validator state.
 * ```
 *
 * #### Example Usage
 *
 * ```html
 * <!--
 *      HTML
 *      The following element will be validated as a required field and the value
 *      must be a valid email address (two validation rules).
 * -->
 * <div id="parent" data-gx-extension="validator">
 *     <input type="email" class="validate" data-validator-validate="required email" />
 * </div>
 *
 * <!--
 *      JavaScript
 *      The following script demonstrates how to check if there are currently invalid
 *      elements in your form.
 * -->
 * <script>
 *     // Trigger validation manually:
 *     $('#parent').trigger('validator.validate');
 *
 *     // Check for invalid field values.
 *     if ($('#parent .error').length > 0) {
 *          // Invalid elements have the ".error" class.
 *     } else {
 *          // Valid input elements have the ".valid" class.
 *     }
 * </script>
 * ```
 *
 * @module Admin/Extensions/validator
 * @ignore
 * 
 * @deprecated Since v1.4, will be removed in v1.6. Use the extension from JSE/Extensions namespace.
 */
gx.extensions.module(
	'validator',
	
	['fallback'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Extension Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			
			perform = {
				
				/**
				 * Validate required fields.
				 */
				required: function($element, value, type, opt) {
					switch (type) {
						case 'select':
							return (parseInt(value, 10) === -1) ? false : true;
						case 'checkbox':
							return (parseInt(value, 10) === -1) ? false : true;
						case 'radio':
							return false;
						default:
							return (value) ? true : false;
					}
				},
				
				/**
				 * Validate email addresses (you should also validate emails at server side before storing).
				 */
				email: function($element, value, type, opt) {
					if (value === '' && opt.validate.indexOf('required') === -1) {
						$element.removeClass('error valid');
						return null; // Do not validate empty strings (that are not required).
					}
					
					// @link http://stackoverflow.com/questions/2507030/email-validation-using-jquery
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(value);
				},
				
				/**
				 * Use this type along with the "required" to check if a CKEditor element is
				 * empty or not. In case that it has the ".error" class you must find you own
				 * way to display that the field is invalid because you cannot display a red
				 * border directly to the validated textarea (CKEditor adds many HTML elements
				 * to the page).
				 */
				ckeditor: function($element, value, type, opt) {
					var id = $element.attr('id');
					
					if (id === undefined) {
						throw 'Cannot validate CKEditor for element without id attribute.';
					}
					
					return (CKEDITOR.instances[id].getData() !== '') ? true : false;
				}
			},
			
			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {},
			
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
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Set State
		 *
		 * @param {object} $element Validated element selector.
		 * @param {string} state Describes current state ("valid", "error").
		 */
		var _setState = function($element, state) {
			switch (state) {
				case 'valid':
					$element
						.removeClass('error')
						.addClass('valid');
					break;
				case 'error':
					$element
						.removeClass('valid')
						.addClass('error');
					break;
				default:
					$element.removeClass('valid error');
					break;
			}
		};
		
		/**
		 * Validate Item
		 *
		 * @return {boolean} Returns the validation result.
		 */
		var _validateItem = function() {
			var $self = $(this),
				settings = jse.libs.fallback._data($self, 'validator'),
				validate = (settings.validate) ? settings.validate.split(' ') : [],
				type = $self.prop('tagName').toLowerCase(),
				result = true;
			
			type = (type !== 'input') ? type : $self.attr('type').toLowerCase();
			
			$.each(validate, function(index, validationType) {
				var isValid = perform[validationType]($self, $self.val(), type, settings);
				if (isValid !== null) {
					_setState($self, (isValid) ? 'valid' : 'error');
					result = (!result) ? false : isValid;
				}
			});
			
			return result;
		};
		
		/**
		 * Validate Multiple Items
		 *
		 * @param {object} event Contains the event information.
		 * @param {object} deferred Defines the deferred object.
		 */
		var _validateItems = function(event, deferred) {
			event.preventDefault();
			event.stopPropagation();
			
			var $self = $(event.target),
				valid = true;
			
			$self
				.filter('.validate')
				.add($self.find('.validate'))
				.each(function() {
					var current = _validateItem.call($(this));
					valid = (!valid) ? false : current;
				});
			
			if (deferred && deferred.deferred) {
				if (valid) {
					deferred.deferred.resolve();
				} else {
					deferred.deferred.reject();
				}
			}
		};
		
		/**
		 * Reset Validator Elements
		 */
		var _resetValidator = function() {
			$this
				.filter('.validate')
				.add($this.find('.validate'))
				.each(function() {
					_setState($(this), 'reset');
				});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Init function of the extension, called by the engine.
		 */
		module.init = function(done) {
			$this
				.on('change', '.validate:text:visible', _validateItem)
				.on('validator.validate', _validateItems)
				.on('validator.reset', _resetValidator)
				.on('submit', function(event) {
					event.preventDefault();
				});
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
