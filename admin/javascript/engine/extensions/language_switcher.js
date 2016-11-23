/* --------------------------------------------------------------
 language_switcher.js 2016-06-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Language Switcher Extension
 *
 * @module Admin/Extensions/language_switcher
 * @ignore
 */
gx.extensions.module(
	'language_switcher',
	
	['form', 'fallback'],
	
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
			
			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 *
			 * @todo Resolve external dependency (js_options).
			 */
			defaults = {
				'position': 1, // Position of the language id in the field name (zero indexed)
				'initLang': js_options.global.language_id // Current language on init
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
			 * Language Names
			 *
			 * @type {Array}
			 */
			names = [],
			
			/**
			 * Buttons Selector
			 *
			 * @type {object}
			 */
			$buttons = null,
			
			/**
			 * CKEditor Instances
			 *
			 * @type {Array}
			 */
			ckeditors = [];
		
		// ------------------------------------------------------------------------
		// MAIN FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Generate Transfer Object
		 *
		 * Generates a JSON transfer object to get data from fields named <X> to be stored in
		 * fields with name <Y>. Therefore the names getting transformed the right way to be
		 * able to use "jse.libs.form.prefillForm"
		 *
		 * @param {string} langActive String with the current lang id.
		 * @param {boolean} toHidden If true, the destination are the hidden fields (else the input fields).
		 */
		var _generateTransferObject = function(langActive, toHidden) {
			
			var currentData = {},
				fullData = jse.libs.fallback.getData($this);
			
			$.each(names, function(i, v) {
				
				var keySplit = v.match(/\[([^\]]+)\]/gi),
					baseKey = v.split('[')[0],
					srcKey = baseKey,
					destKey = baseKey,
					valid = false;
				
				// Only execute if name schema matches
				if (keySplit) {
					// Generate key names
					$.each(keySplit, function(i, v) {
						if (options.position !== i) {
							destKey += v;
							srcKey += v;
						} else {
							if (toHidden) {
								destKey += '[' + langActive + ']';
							} else {
								srcKey += '[' + langActive + ']';
							}
							valid = true;
						}
					});
					
					// Push data to the result object
					if (valid && fullData[srcKey] !== undefined) {
						currentData[destKey] = fullData[srcKey];
					}
				}
			});
			
			return currentData;
		};
		
		/**
		 * Store Data To Hidden
		 *
		 * Function to store input field data to hidden fields.
		 *
		 * @param {object} $activeButton jQuery selector object with the active language id.
		 */
		var _storeDataToHidden = function($activeButton) {
			var langActive = $activeButton.attr('href').slice(1);
			
			// Update textarea fields with data from CKEditor.
			$this
				.find('textarea')
				.each(function() {
					var $self = $(this),
						name = $self.attr('name'),
						editor = (window.CKEDITOR) ? CKEDITOR.instances[name] : null;
					
					if (editor) {
						$self.val(editor.getData());
					}
				});
			
			// Store data to hidden fields.
			jse.libs.form.prefillForm($this, _generateTransferObject(langActive, true), false);
		};
		
		/**
		 * Get From Hidden
		 *
		 * Function to restore input field data from hidden fields
		 *
		 * @param {object} $activeButton jQuery selector object with the active language id.
		 */
		var _getDataFromHidden = function($activeButton) {
			var langActive = $activeButton.attr('href').slice(1);
			
			// Restore data to input fields
			jse.libs.form.prefillForm($this, _generateTransferObject(langActive, false), false);
			
			// Update the ckeditors with the new
			// data from textareas
			$this
				.find('textarea')
				.not('[data-language_switcher-ignore]')
				.each(function() {
					var $self = $(this),
						name = $self.attr('name'),
						value = $self.text(),
						editor = (window.CKEDITOR) ? CKEDITOR.instances[name] : null;
					
					if (editor) {
						editor.setData(value);
					}
				});
		};
		
		/**
		 * Update CKEditors
		 *
		 * Helper function to add a blur event on every ckeditor that is loaded inside
		 * of $this. To prevent multiple blur events on one ckeditor, all names of the
		 * tags that already got an blur event are saved.
		 */
		var _updateCKeditors = function() {
			if (window.CKEDITOR) {
				$this
					.find('textarea')
					.each(function() {
						var name = $(this).attr('name');
						if (CKEDITOR.instances[name] && $.inArray(name, ckeditors) === -1) {
							ckeditors.push(name);
							CKEDITOR.instances[name].on('blur', function() {
								_storeDataToHidden($buttons.filter('.active'));
							});
						}
					});
			}
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------
		
		/**
		 * On Click Event Handler
		 *
		 * Event listener to store current data to hidden fields and restore hidden
		 * data to text fields if a flag button gets clicked
		 *
		 * @param {object} event Contains information about the event.
		 */
		var _clickHandler = function(event) {
			event.preventDefault();
			
			var $self = $(this);
			
			if (!$self.hasClass('active')) {
				
				var $activeButton = $buttons.filter('.active');
				
				$buttons.removeClass('active');
				$self.addClass('active');
				
				if ($activeButton.length) {
					_storeDataToHidden($activeButton);
				}
				
				_getDataFromHidden($self);
			}
		};
		
		/**
		 * Update Field Event Handler
		 *
		 * @param {object} event Contains information about the event.
		 */
		var _updateField = function(event) {
			event.preventDefault();
			var $activeButton = $buttons.filter('.active');
			_getDataFromHidden($activeButton);
		};
		
		/**
		 * Get Language
		 *
		 * Function to return the current language id via an deferred object.
		 *
		 * @param {object} event jQuery event object.
		 * @param {object} deferred Data object that contains the deferred object.
		 */
		var _getLanguage = function(event, deferred) {
			if (deferred && deferred.deferred) {
				var lang = $buttons
					.filter('.active')
					.first()
					.attr('href')
					.slice(1);
				
				deferred.deferred.resolve(lang);
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Init function of the extension, called by the engine.
		 */
		module.init = function(done) {
			
			$buttons = $this.find('.buttonbar a'); // @todo Make the selector dynamic through an option.
			
			/**
			 * Bind event listener to the form fields, and store the names of the field in
			 * cache. To prevent empty CKEditors (because of already loaded CKEditors on
			 * init of this script update them with the correct value.
			 * 
			 * @todo Move method outside the initialize method (avoid function nesting without specific reason). 
			 */
			var _addEventHandler = function() {
				names = [];
				
				// Get all needed selectors.
				var $formFields = $this.find('input:not(:button):not(:submit), select, textarea')
					.not('[data-language_switcher-ignore]');
				
				$formFields.each(function() {
					
					var $self = $(this),
						type = jse.libs.form.getFieldType($self),
						event = ($.inArray(type, ['text', 'textarea']) > -1) ? 'blur' : 'change',
						name = $self.attr('name');
					
					names.push(name);
					
					$self
						.on(event, function() {
							_storeDataToHidden($buttons.filter('.active'));
						});
				});
				
				_updateCKeditors();
			};
			
			_addEventHandler();
			
			// Bind event handler to the flags buttons.
			$buttons
				.on('click', _clickHandler)
				.filter('[href="#' + options.initLang + '"]')
				.trigger('click');
			
			// Bind additional event listener to $this.
			$('body').on('JSENGINE_INIT_FINISHED', function() {
				_updateCKeditors();
			});
			
			$this
				.on('layerClose', function() {
					// Workaround to update the hidden fields on layer close.
					_storeDataToHidden($buttons.filter('.active'));
				})
				.on('language_switcher.update', _addEventHandler)
				.on('language_switcher.updateField', _updateField)
				.on('language_switcher.getLang', _getLanguage);
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
