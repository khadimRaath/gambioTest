'use strict';

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
gx.extensions.module('language_switcher', ['form', 'fallback'], function (data) {

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
	var _generateTransferObject = function _generateTransferObject(langActive, toHidden) {

		var currentData = {},
		    fullData = jse.libs.fallback.getData($this);

		$.each(names, function (i, v) {

			var keySplit = v.match(/\[([^\]]+)\]/gi),
			    baseKey = v.split('[')[0],
			    srcKey = baseKey,
			    destKey = baseKey,
			    valid = false;

			// Only execute if name schema matches
			if (keySplit) {
				// Generate key names
				$.each(keySplit, function (i, v) {
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
	var _storeDataToHidden = function _storeDataToHidden($activeButton) {
		var langActive = $activeButton.attr('href').slice(1);

		// Update textarea fields with data from CKEditor.
		$this.find('textarea').each(function () {
			var $self = $(this),
			    name = $self.attr('name'),
			    editor = window.CKEDITOR ? CKEDITOR.instances[name] : null;

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
	var _getDataFromHidden = function _getDataFromHidden($activeButton) {
		var langActive = $activeButton.attr('href').slice(1);

		// Restore data to input fields
		jse.libs.form.prefillForm($this, _generateTransferObject(langActive, false), false);

		// Update the ckeditors with the new
		// data from textareas
		$this.find('textarea').not('[data-language_switcher-ignore]').each(function () {
			var $self = $(this),
			    name = $self.attr('name'),
			    value = $self.text(),
			    editor = window.CKEDITOR ? CKEDITOR.instances[name] : null;

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
	var _updateCKeditors = function _updateCKeditors() {
		if (window.CKEDITOR) {
			$this.find('textarea').each(function () {
				var name = $(this).attr('name');
				if (CKEDITOR.instances[name] && $.inArray(name, ckeditors) === -1) {
					ckeditors.push(name);
					CKEDITOR.instances[name].on('blur', function () {
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
	var _clickHandler = function _clickHandler(event) {
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
	var _updateField = function _updateField(event) {
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
	var _getLanguage = function _getLanguage(event, deferred) {
		if (deferred && deferred.deferred) {
			var lang = $buttons.filter('.active').first().attr('href').slice(1);

			deferred.deferred.resolve(lang);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the extension, called by the engine.
  */
	module.init = function (done) {

		$buttons = $this.find('.buttonbar a'); // @todo Make the selector dynamic through an option.

		/**
   * Bind event listener to the form fields, and store the names of the field in
   * cache. To prevent empty CKEditors (because of already loaded CKEditors on
   * init of this script update them with the correct value.
   * 
   * @todo Move method outside the initialize method (avoid function nesting without specific reason). 
   */
		var _addEventHandler = function _addEventHandler() {
			names = [];

			// Get all needed selectors.
			var $formFields = $this.find('input:not(:button):not(:submit), select, textarea').not('[data-language_switcher-ignore]');

			$formFields.each(function () {

				var $self = $(this),
				    type = jse.libs.form.getFieldType($self),
				    event = $.inArray(type, ['text', 'textarea']) > -1 ? 'blur' : 'change',
				    name = $self.attr('name');

				names.push(name);

				$self.on(event, function () {
					_storeDataToHidden($buttons.filter('.active'));
				});
			});

			_updateCKeditors();
		};

		_addEventHandler();

		// Bind event handler to the flags buttons.
		$buttons.on('click', _clickHandler).filter('[href="#' + options.initLang + '"]').trigger('click');

		// Bind additional event listener to $this.
		$('body').on('JSENGINE_INIT_FINISHED', function () {
			_updateCKeditors();
		});

		$this.on('layerClose', function () {
			// Workaround to update the hidden fields on layer close.
			_storeDataToHidden($buttons.filter('.active'));
		}).on('language_switcher.update', _addEventHandler).on('language_switcher.updateField', _updateField).on('language_switcher.getLang', _getLanguage);

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxhbmd1YWdlX3N3aXRjaGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImpzX29wdGlvbnMiLCJnbG9iYWwiLCJsYW5ndWFnZV9pZCIsIm9wdGlvbnMiLCJleHRlbmQiLCJuYW1lcyIsIiRidXR0b25zIiwiY2tlZGl0b3JzIiwiX2dlbmVyYXRlVHJhbnNmZXJPYmplY3QiLCJsYW5nQWN0aXZlIiwidG9IaWRkZW4iLCJjdXJyZW50RGF0YSIsImZ1bGxEYXRhIiwianNlIiwibGlicyIsImZhbGxiYWNrIiwiZ2V0RGF0YSIsImVhY2giLCJpIiwidiIsImtleVNwbGl0IiwibWF0Y2giLCJiYXNlS2V5Iiwic3BsaXQiLCJzcmNLZXkiLCJkZXN0S2V5IiwidmFsaWQiLCJwb3NpdGlvbiIsInVuZGVmaW5lZCIsIl9zdG9yZURhdGFUb0hpZGRlbiIsIiRhY3RpdmVCdXR0b24iLCJhdHRyIiwic2xpY2UiLCJmaW5kIiwiJHNlbGYiLCJuYW1lIiwiZWRpdG9yIiwid2luZG93IiwiQ0tFRElUT1IiLCJpbnN0YW5jZXMiLCJ2YWwiLCJmb3JtIiwicHJlZmlsbEZvcm0iLCJfZ2V0RGF0YUZyb21IaWRkZW4iLCJub3QiLCJ2YWx1ZSIsInRleHQiLCJzZXREYXRhIiwiX3VwZGF0ZUNLZWRpdG9ycyIsImluQXJyYXkiLCJwdXNoIiwib24iLCJmaWx0ZXIiLCJfY2xpY2tIYW5kbGVyIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImxlbmd0aCIsIl91cGRhdGVGaWVsZCIsIl9nZXRMYW5ndWFnZSIsImRlZmVycmVkIiwibGFuZyIsImZpcnN0IiwicmVzb2x2ZSIsImluaXQiLCJkb25lIiwiX2FkZEV2ZW50SGFuZGxlciIsIiRmb3JtRmllbGRzIiwidHlwZSIsImdldEZpZWxkVHlwZSIsImluaXRMYW5nIiwidHJpZ2dlciJdLCJt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bkI7QUFDQSxLQUhGO0FBSUEsSUFiRDs7QUFlQUo7QUFDQSxHQXZCRDs7QUF5QkFvQjs7QUFFQTtBQUNBOUQsV0FDRTZDLEVBREYsQ0FDSyxPQURMLEVBQ2NFLGFBRGQsRUFFRUQsTUFGRixDQUVTLGFBQWFqRCxRQUFRcUUsUUFBckIsR0FBZ0MsSUFGekMsRUFHRUMsT0FIRixDQUdVLE9BSFY7O0FBS0E7QUFDQTNFLElBQUUsTUFBRixFQUFVcUQsRUFBVixDQUFhLHdCQUFiLEVBQXVDLFlBQVc7QUFDakRIO0FBQ0EsR0FGRDs7QUFJQW5ELFFBQ0VzRCxFQURGLENBQ0ssWUFETCxFQUNtQixZQUFXO0FBQzVCO0FBQ0F0QixzQkFBbUJ2QixTQUFTOEMsTUFBVCxDQUFnQixTQUFoQixDQUFuQjtBQUNBLEdBSkYsRUFLRUQsRUFMRixDQUtLLDBCQUxMLEVBS2lDaUIsZ0JBTGpDLEVBTUVqQixFQU5GLENBTUssK0JBTkwsRUFNc0NTLFlBTnRDLEVBT0VULEVBUEYsQ0FPSywyQkFQTCxFQU9rQ1UsWUFQbEM7O0FBU0FNO0FBQ0EsRUEzREQ7O0FBNkRBO0FBQ0EsUUFBT3hFLE1BQVA7QUFDQSxDQS9VRiIsImZpbGUiOiJsYW5ndWFnZV9zd2l0Y2hlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbGFuZ3VhZ2Vfc3dpdGNoZXIuanMgMjAxNi0wNi0zMCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgTGFuZ3VhZ2UgU3dpdGNoZXIgRXh0ZW5zaW9uXG4gKlxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2xhbmd1YWdlX3N3aXRjaGVyXG4gKiBAaWdub3JlXG4gKi9cbmd4LmV4dGVuc2lvbnMubW9kdWxlKFxuXHQnbGFuZ3VhZ2Vfc3dpdGNoZXInLFxuXHRcblx0Wydmb3JtJywgJ2ZhbGxiYWNrJ10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKlxuXHRcdFx0ICogQHRvZG8gUmVzb2x2ZSBleHRlcm5hbCBkZXBlbmRlbmN5IChqc19vcHRpb25zKS5cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdCdwb3NpdGlvbic6IDEsIC8vIFBvc2l0aW9uIG9mIHRoZSBsYW5ndWFnZSBpZCBpbiB0aGUgZmllbGQgbmFtZSAoemVybyBpbmRleGVkKVxuXHRcdFx0XHQnaW5pdExhbmcnOiBqc19vcHRpb25zLmdsb2JhbC5sYW5ndWFnZV9pZCAvLyBDdXJyZW50IGxhbmd1YWdlIG9uIGluaXRcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTGFuZ3VhZ2UgTmFtZXNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7QXJyYXl9XG5cdFx0XHQgKi9cblx0XHRcdG5hbWVzID0gW10sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQnV0dG9ucyBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRidXR0b25zID0gbnVsbCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDS0VkaXRvciBJbnN0YW5jZXNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7QXJyYXl9XG5cdFx0XHQgKi9cblx0XHRcdGNrZWRpdG9ycyA9IFtdO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIE1BSU4gRlVOQ1RJT05BTElUWVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEdlbmVyYXRlIFRyYW5zZmVyIE9iamVjdFxuXHRcdCAqXG5cdFx0ICogR2VuZXJhdGVzIGEgSlNPTiB0cmFuc2ZlciBvYmplY3QgdG8gZ2V0IGRhdGEgZnJvbSBmaWVsZHMgbmFtZWQgPFg+IHRvIGJlIHN0b3JlZCBpblxuXHRcdCAqIGZpZWxkcyB3aXRoIG5hbWUgPFk+LiBUaGVyZWZvcmUgdGhlIG5hbWVzIGdldHRpbmcgdHJhbnNmb3JtZWQgdGhlIHJpZ2h0IHdheSB0byBiZVxuXHRcdCAqIGFibGUgdG8gdXNlIFwianNlLmxpYnMuZm9ybS5wcmVmaWxsRm9ybVwiXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gbGFuZ0FjdGl2ZSBTdHJpbmcgd2l0aCB0aGUgY3VycmVudCBsYW5nIGlkLlxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gdG9IaWRkZW4gSWYgdHJ1ZSwgdGhlIGRlc3RpbmF0aW9uIGFyZSB0aGUgaGlkZGVuIGZpZWxkcyAoZWxzZSB0aGUgaW5wdXQgZmllbGRzKS5cblx0XHQgKi9cblx0XHR2YXIgX2dlbmVyYXRlVHJhbnNmZXJPYmplY3QgPSBmdW5jdGlvbihsYW5nQWN0aXZlLCB0b0hpZGRlbikge1xuXHRcdFx0XG5cdFx0XHR2YXIgY3VycmVudERhdGEgPSB7fSxcblx0XHRcdFx0ZnVsbERhdGEgPSBqc2UubGlicy5mYWxsYmFjay5nZXREYXRhKCR0aGlzKTtcblx0XHRcdFxuXHRcdFx0JC5lYWNoKG5hbWVzLCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIga2V5U3BsaXQgPSB2Lm1hdGNoKC9cXFsoW15cXF1dKylcXF0vZ2kpLFxuXHRcdFx0XHRcdGJhc2VLZXkgPSB2LnNwbGl0KCdbJylbMF0sXG5cdFx0XHRcdFx0c3JjS2V5ID0gYmFzZUtleSxcblx0XHRcdFx0XHRkZXN0S2V5ID0gYmFzZUtleSxcblx0XHRcdFx0XHR2YWxpZCA9IGZhbHNlO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gT25seSBleGVjdXRlIGlmIG5hbWUgc2NoZW1hIG1hdGNoZXNcblx0XHRcdFx0aWYgKGtleVNwbGl0KSB7XG5cdFx0XHRcdFx0Ly8gR2VuZXJhdGUga2V5IG5hbWVzXG5cdFx0XHRcdFx0JC5lYWNoKGtleVNwbGl0LCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdFx0XHRpZiAob3B0aW9ucy5wb3NpdGlvbiAhPT0gaSkge1xuXHRcdFx0XHRcdFx0XHRkZXN0S2V5ICs9IHY7XG5cdFx0XHRcdFx0XHRcdHNyY0tleSArPSB2O1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0aWYgKHRvSGlkZGVuKSB7XG5cdFx0XHRcdFx0XHRcdFx0ZGVzdEtleSArPSAnWycgKyBsYW5nQWN0aXZlICsgJ10nO1xuXHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdHNyY0tleSArPSAnWycgKyBsYW5nQWN0aXZlICsgJ10nO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdHZhbGlkID0gdHJ1ZTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBQdXNoIGRhdGEgdG8gdGhlIHJlc3VsdCBvYmplY3Rcblx0XHRcdFx0XHRpZiAodmFsaWQgJiYgZnVsbERhdGFbc3JjS2V5XSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0XHRjdXJyZW50RGF0YVtkZXN0S2V5XSA9IGZ1bGxEYXRhW3NyY0tleV07XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIGN1cnJlbnREYXRhO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU3RvcmUgRGF0YSBUbyBIaWRkZW5cblx0XHQgKlxuXHRcdCAqIEZ1bmN0aW9uIHRvIHN0b3JlIGlucHV0IGZpZWxkIGRhdGEgdG8gaGlkZGVuIGZpZWxkcy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAkYWN0aXZlQnV0dG9uIGpRdWVyeSBzZWxlY3RvciBvYmplY3Qgd2l0aCB0aGUgYWN0aXZlIGxhbmd1YWdlIGlkLlxuXHRcdCAqL1xuXHRcdHZhciBfc3RvcmVEYXRhVG9IaWRkZW4gPSBmdW5jdGlvbigkYWN0aXZlQnV0dG9uKSB7XG5cdFx0XHR2YXIgbGFuZ0FjdGl2ZSA9ICRhY3RpdmVCdXR0b24uYXR0cignaHJlZicpLnNsaWNlKDEpO1xuXHRcdFx0XG5cdFx0XHQvLyBVcGRhdGUgdGV4dGFyZWEgZmllbGRzIHdpdGggZGF0YSBmcm9tIENLRWRpdG9yLlxuXHRcdFx0JHRoaXNcblx0XHRcdFx0LmZpbmQoJ3RleHRhcmVhJylcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyksXG5cdFx0XHRcdFx0XHRlZGl0b3IgPSAod2luZG93LkNLRURJVE9SKSA/IENLRURJVE9SLmluc3RhbmNlc1tuYW1lXSA6IG51bGw7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0aWYgKGVkaXRvcikge1xuXHRcdFx0XHRcdFx0JHNlbGYudmFsKGVkaXRvci5nZXREYXRhKCkpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIFN0b3JlIGRhdGEgdG8gaGlkZGVuIGZpZWxkcy5cblx0XHRcdGpzZS5saWJzLmZvcm0ucHJlZmlsbEZvcm0oJHRoaXMsIF9nZW5lcmF0ZVRyYW5zZmVyT2JqZWN0KGxhbmdBY3RpdmUsIHRydWUpLCBmYWxzZSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgRnJvbSBIaWRkZW5cblx0XHQgKlxuXHRcdCAqIEZ1bmN0aW9uIHRvIHJlc3RvcmUgaW5wdXQgZmllbGQgZGF0YSBmcm9tIGhpZGRlbiBmaWVsZHNcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAkYWN0aXZlQnV0dG9uIGpRdWVyeSBzZWxlY3RvciBvYmplY3Qgd2l0aCB0aGUgYWN0aXZlIGxhbmd1YWdlIGlkLlxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0RGF0YUZyb21IaWRkZW4gPSBmdW5jdGlvbigkYWN0aXZlQnV0dG9uKSB7XG5cdFx0XHR2YXIgbGFuZ0FjdGl2ZSA9ICRhY3RpdmVCdXR0b24uYXR0cignaHJlZicpLnNsaWNlKDEpO1xuXHRcdFx0XG5cdFx0XHQvLyBSZXN0b3JlIGRhdGEgdG8gaW5wdXQgZmllbGRzXG5cdFx0XHRqc2UubGlicy5mb3JtLnByZWZpbGxGb3JtKCR0aGlzLCBfZ2VuZXJhdGVUcmFuc2Zlck9iamVjdChsYW5nQWN0aXZlLCBmYWxzZSksIGZhbHNlKTtcblx0XHRcdFxuXHRcdFx0Ly8gVXBkYXRlIHRoZSBja2VkaXRvcnMgd2l0aCB0aGUgbmV3XG5cdFx0XHQvLyBkYXRhIGZyb20gdGV4dGFyZWFzXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQuZmluZCgndGV4dGFyZWEnKVxuXHRcdFx0XHQubm90KCdbZGF0YS1sYW5ndWFnZV9zd2l0Y2hlci1pZ25vcmVdJylcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyksXG5cdFx0XHRcdFx0XHR2YWx1ZSA9ICRzZWxmLnRleHQoKSxcblx0XHRcdFx0XHRcdGVkaXRvciA9ICh3aW5kb3cuQ0tFRElUT1IpID8gQ0tFRElUT1IuaW5zdGFuY2VzW25hbWVdIDogbnVsbDtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoZWRpdG9yKSB7XG5cdFx0XHRcdFx0XHRlZGl0b3Iuc2V0RGF0YSh2YWx1ZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFVwZGF0ZSBDS0VkaXRvcnNcblx0XHQgKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBhZGQgYSBibHVyIGV2ZW50IG9uIGV2ZXJ5IGNrZWRpdG9yIHRoYXQgaXMgbG9hZGVkIGluc2lkZVxuXHRcdCAqIG9mICR0aGlzLiBUbyBwcmV2ZW50IG11bHRpcGxlIGJsdXIgZXZlbnRzIG9uIG9uZSBja2VkaXRvciwgYWxsIG5hbWVzIG9mIHRoZVxuXHRcdCAqIHRhZ3MgdGhhdCBhbHJlYWR5IGdvdCBhbiBibHVyIGV2ZW50IGFyZSBzYXZlZC5cblx0XHQgKi9cblx0XHR2YXIgX3VwZGF0ZUNLZWRpdG9ycyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKHdpbmRvdy5DS0VESVRPUikge1xuXHRcdFx0XHQkdGhpc1xuXHRcdFx0XHRcdC5maW5kKCd0ZXh0YXJlYScpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgbmFtZSA9ICQodGhpcykuYXR0cignbmFtZScpO1xuXHRcdFx0XHRcdFx0aWYgKENLRURJVE9SLmluc3RhbmNlc1tuYW1lXSAmJiAkLmluQXJyYXkobmFtZSwgY2tlZGl0b3JzKSA9PT0gLTEpIHtcblx0XHRcdFx0XHRcdFx0Y2tlZGl0b3JzLnB1c2gobmFtZSk7XG5cdFx0XHRcdFx0XHRcdENLRURJVE9SLmluc3RhbmNlc1tuYW1lXS5vbignYmx1cicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdF9zdG9yZURhdGFUb0hpZGRlbigkYnV0dG9ucy5maWx0ZXIoJy5hY3RpdmUnKSk7XG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE9uIENsaWNrIEV2ZW50IEhhbmRsZXJcblx0XHQgKlxuXHRcdCAqIEV2ZW50IGxpc3RlbmVyIHRvIHN0b3JlIGN1cnJlbnQgZGF0YSB0byBoaWRkZW4gZmllbGRzIGFuZCByZXN0b3JlIGhpZGRlblxuXHRcdCAqIGRhdGEgdG8gdGV4dCBmaWVsZHMgaWYgYSBmbGFnIGJ1dHRvbiBnZXRzIGNsaWNrZWRcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyBpbmZvcm1hdGlvbiBhYm91dCB0aGUgZXZlbnQuXG5cdFx0ICovXG5cdFx0dmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFxuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKTtcblx0XHRcdFxuXHRcdFx0aWYgKCEkc2VsZi5oYXNDbGFzcygnYWN0aXZlJykpIHtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciAkYWN0aXZlQnV0dG9uID0gJGJ1dHRvbnMuZmlsdGVyKCcuYWN0aXZlJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkYnV0dG9ucy5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdCRzZWxmLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmICgkYWN0aXZlQnV0dG9uLmxlbmd0aCkge1xuXHRcdFx0XHRcdF9zdG9yZURhdGFUb0hpZGRlbigkYWN0aXZlQnV0dG9uKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0X2dldERhdGFGcm9tSGlkZGVuKCRzZWxmKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFVwZGF0ZSBGaWVsZCBFdmVudCBIYW5kbGVyXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgaW5mb3JtYXRpb24gYWJvdXQgdGhlIGV2ZW50LlxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlRmllbGQgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdHZhciAkYWN0aXZlQnV0dG9uID0gJGJ1dHRvbnMuZmlsdGVyKCcuYWN0aXZlJyk7XG5cdFx0XHRfZ2V0RGF0YUZyb21IaWRkZW4oJGFjdGl2ZUJ1dHRvbik7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgTGFuZ3VhZ2Vcblx0XHQgKlxuXHRcdCAqIEZ1bmN0aW9uIHRvIHJldHVybiB0aGUgY3VycmVudCBsYW5ndWFnZSBpZCB2aWEgYW4gZGVmZXJyZWQgb2JqZWN0LlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QuXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGRlZmVycmVkIERhdGEgb2JqZWN0IHRoYXQgY29udGFpbnMgdGhlIGRlZmVycmVkIG9iamVjdC5cblx0XHQgKi9cblx0XHR2YXIgX2dldExhbmd1YWdlID0gZnVuY3Rpb24oZXZlbnQsIGRlZmVycmVkKSB7XG5cdFx0XHRpZiAoZGVmZXJyZWQgJiYgZGVmZXJyZWQuZGVmZXJyZWQpIHtcblx0XHRcdFx0dmFyIGxhbmcgPSAkYnV0dG9uc1xuXHRcdFx0XHRcdC5maWx0ZXIoJy5hY3RpdmUnKVxuXHRcdFx0XHRcdC5maXJzdCgpXG5cdFx0XHRcdFx0LmF0dHIoJ2hyZWYnKVxuXHRcdFx0XHRcdC5zbGljZSgxKTtcblx0XHRcdFx0XG5cdFx0XHRcdGRlZmVycmVkLmRlZmVycmVkLnJlc29sdmUobGFuZyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdCRidXR0b25zID0gJHRoaXMuZmluZCgnLmJ1dHRvbmJhciBhJyk7IC8vIEB0b2RvIE1ha2UgdGhlIHNlbGVjdG9yIGR5bmFtaWMgdGhyb3VnaCBhbiBvcHRpb24uXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQmluZCBldmVudCBsaXN0ZW5lciB0byB0aGUgZm9ybSBmaWVsZHMsIGFuZCBzdG9yZSB0aGUgbmFtZXMgb2YgdGhlIGZpZWxkIGluXG5cdFx0XHQgKiBjYWNoZS4gVG8gcHJldmVudCBlbXB0eSBDS0VkaXRvcnMgKGJlY2F1c2Ugb2YgYWxyZWFkeSBsb2FkZWQgQ0tFZGl0b3JzIG9uXG5cdFx0XHQgKiBpbml0IG9mIHRoaXMgc2NyaXB0IHVwZGF0ZSB0aGVtIHdpdGggdGhlIGNvcnJlY3QgdmFsdWUuXG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0b2RvIE1vdmUgbWV0aG9kIG91dHNpZGUgdGhlIGluaXRpYWxpemUgbWV0aG9kIChhdm9pZCBmdW5jdGlvbiBuZXN0aW5nIHdpdGhvdXQgc3BlY2lmaWMgcmVhc29uKS4gXG5cdFx0XHQgKi9cblx0XHRcdHZhciBfYWRkRXZlbnRIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG5hbWVzID0gW107XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBHZXQgYWxsIG5lZWRlZCBzZWxlY3RvcnMuXG5cdFx0XHRcdHZhciAkZm9ybUZpZWxkcyA9ICR0aGlzLmZpbmQoJ2lucHV0Om5vdCg6YnV0dG9uKTpub3QoOnN1Ym1pdCksIHNlbGVjdCwgdGV4dGFyZWEnKVxuXHRcdFx0XHRcdC5ub3QoJ1tkYXRhLWxhbmd1YWdlX3N3aXRjaGVyLWlnbm9yZV0nKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRmb3JtRmllbGRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdHR5cGUgPSBqc2UubGlicy5mb3JtLmdldEZpZWxkVHlwZSgkc2VsZiksXG5cdFx0XHRcdFx0XHRldmVudCA9ICgkLmluQXJyYXkodHlwZSwgWyd0ZXh0JywgJ3RleHRhcmVhJ10pID4gLTEpID8gJ2JsdXInIDogJ2NoYW5nZScsXG5cdFx0XHRcdFx0XHRuYW1lID0gJHNlbGYuYXR0cignbmFtZScpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdG5hbWVzLnB1c2gobmFtZSk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHRcdC5vbihldmVudCwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdF9zdG9yZURhdGFUb0hpZGRlbigkYnV0dG9ucy5maWx0ZXIoJy5hY3RpdmUnKSk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRfdXBkYXRlQ0tlZGl0b3JzKCk7XG5cdFx0XHR9O1xuXHRcdFx0XG5cdFx0XHRfYWRkRXZlbnRIYW5kbGVyKCk7XG5cdFx0XHRcblx0XHRcdC8vIEJpbmQgZXZlbnQgaGFuZGxlciB0byB0aGUgZmxhZ3MgYnV0dG9ucy5cblx0XHRcdCRidXR0b25zXG5cdFx0XHRcdC5vbignY2xpY2snLCBfY2xpY2tIYW5kbGVyKVxuXHRcdFx0XHQuZmlsdGVyKCdbaHJlZj1cIiMnICsgb3B0aW9ucy5pbml0TGFuZyArICdcIl0nKVxuXHRcdFx0XHQudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFxuXHRcdFx0Ly8gQmluZCBhZGRpdGlvbmFsIGV2ZW50IGxpc3RlbmVyIHRvICR0aGlzLlxuXHRcdFx0JCgnYm9keScpLm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdF91cGRhdGVDS2VkaXRvcnMoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2xheWVyQ2xvc2UnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQvLyBXb3JrYXJvdW5kIHRvIHVwZGF0ZSB0aGUgaGlkZGVuIGZpZWxkcyBvbiBsYXllciBjbG9zZS5cblx0XHRcdFx0XHRfc3RvcmVEYXRhVG9IaWRkZW4oJGJ1dHRvbnMuZmlsdGVyKCcuYWN0aXZlJykpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQub24oJ2xhbmd1YWdlX3N3aXRjaGVyLnVwZGF0ZScsIF9hZGRFdmVudEhhbmRsZXIpXG5cdFx0XHRcdC5vbignbGFuZ3VhZ2Vfc3dpdGNoZXIudXBkYXRlRmllbGQnLCBfdXBkYXRlRmllbGQpXG5cdFx0XHRcdC5vbignbGFuZ3VhZ2Vfc3dpdGNoZXIuZ2V0TGFuZycsIF9nZXRMYW5ndWFnZSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
