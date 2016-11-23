'use strict';

/* --------------------------------------------------------------
 table_inline_edit.js 2015-10-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Extension for making tables editable.
 *
 * @module Admin/Extensions/table_inline_edit
 * @ignore
 */
gx.extensions.module('table_inline_edit', ['form', 'xhr', 'fallback'], function (data) {

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
  * Template Selector
  *
  * @type {object}
  */
	$template = null,


	/**
  * Table Body Selector
  *
  * @type {object}
  */
	$table_body = null,


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {
		'multiEdit': false
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
	module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Switch State
  *
  * Function that enables / disables, depending on the mode, all input fields inside
  * the $element and shows / hides the corresponding buttons.
  *
  * @param {string} mode Set the given mode. Possible values: 'edit', 'add', 'default'
  * @param {object} $element The element jQuery selection that gets modified
  * @param {boolean} addClass If true, the state class gets added to the element
  */
	var _switchState = function _switchState(mode, $element, addClass) {

		var $targets = $element.find('input, textarea, select, button, i'),
		    $edit = $targets.filter('.editmode'),
		    $add = $targets.filter('.addmode'),
		    $default = $targets.filter('.defaultmode'),
		    $others = $targets.filter(':not(.editmode):not(.addmode):not(.defaultmode)');

		// Hide all buttons
		$edit.hide();
		$add.hide();
		$default.hide();

		// Remove alt-text if available
		$element.find('.table_inlineedit_alt').remove();

		switch (mode) {
			case 'edit':
				// Switch to edit mode
				$edit.show();
				$others.prop('disabled', false);
				break;
			case 'add':
				// Switch to add mode
				$add.show();
				$others.prop('disabled', false);
				break;
			default:
				// Switch to default-mode
				$default.show();
				$others.prop('disabled', true).each(function () {
					// Check if there is an alt text given for the input field
					var $self = $(this),
					    dataset = jse.libs.fallback._data($self, 'table_inline_edit');

					// Replace some kind of form fields with span tags
					if ($self.attr('type') && $self.attr('type').toLowerCase() === 'checkbox' && dataset.alt) {
						var values = dataset.alt.split('_'),
						    checked = $self.prop('checked');
						$self.after('<span class="table_inlineedit_alt">' + (checked ? values[0] : values[1]) + '</span>');
					} else if ($self.prop('tagName').toLowerCase() === 'select') {
						var waitUntilValues = function waitUntilValues() {
							$edit.hide();
							if (!$self.children().length) {
								setTimeout(function () {
									waitUntilValues();
								}, 200);
							} else {
								$self.children('[value="' + $self.val() + '"]').text();
								$self.after('<span class="table_inlineedit_alt">' + $self.children('[value="' + $self.val() + '"]').text() + '</span>');
								return;
							}
						};

						waitUntilValues();
					}
				});
				break;
		}

		$this.trigger('FORM_UPDATE', []);

		// Add the mode class
		if (addClass) {
			$element.removeClass('edit add default').addClass(mode);
		}
	};

	/**
  * Create New Line
  *
  * Creates a new "add"-line by cloning the footer template.
  */
	var _createNewLine = function _createNewLine() {
		var $newLine = $template.clone();

		$newLine.find('[name]').each(function () {
			var $self = $(this);

			$self.attr('name', $self.attr('name').replace('[]', '[0]'));
		});

		_switchState('add', $newLine, true);
		// Rename the temporarily widget data attributes
		jse.libs.fallback.setupWidgetAttr($newLine);
		$table_body.append($newLine);
		// Start the widgets
		gx.widgets.init($table_body.find('tr').last());
		gx.extensions.init($table_body.find('tr').last());
		gx.controllers.init($table_body.find('tr').last());
		gx.compatibility.init($table_body.find('tr').last());
		jse.widgets.init($table_body.find('tr').last());
		jse.extensions.init($table_body.find('tr').last());
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Handler for the "abort"-button
  *
  * @returns {boolean} If function gets called directly, the return value is the state of the abort.
  */
	var _abortHandler = function _abortHandler() {
		var $tr = $(this).closest('tr'),
		    cache = JSON.stringify($tr.data('formcache')),
		    current = JSON.stringify(jse.libs.form.getData($tr, undefined, true)),
		    deferred = $.Deferred();

		/**
   * Helper function to reset a line state
   *
   * @private
   */
		var _resetLine = function _resetLine(e) {
			if (e) {
				$('#lightbox_package_' + e.data.id + 'admin_button').off();
				$('#lightbox_package_' + e.data.id);
				$.lightbox_plugin('close', e.data.id);
			}

			if (e && e.data.reject) {
				deferred.reject();
			} else {
				// Reset the validation state
				$tr.trigger('validator.reset', []);
				// Reset the form data
				jse.libs.form.prefillForm($tr, $tr.data('formcache'), true);
				_switchState('default', $tr, true);
				deferred.resolve();
			}
		};

		// Compare the old with the new data. If changes were made, confirm the abort
		if (cache !== current) {
			var href = 'lightbox_confirm.html?section=shop_offline&amp;' + 'message=dicard_changes_hint&amp;buttons=cancel-discard',
			    linkHtml = '<a href="' + href + '"></a>',
			    lightboxLink = $(linkHtml),
			    lightboxId = lightboxLink.lightbox_plugin({
				'lightbox_width': '360px'
			});

			$('#lightbox_package_' + lightboxId).one('click', '.discard', {
				'reject': false,
				'id': lightboxId
			}, _resetLine).one('click', '.cancel', {
				'reject': true,
				'id': lightboxId
			}, _resetLine);
		} else {
			_resetLine();
		}

		return deferred.promise();
	};

	/**
  * Handler for the "edit"-button
  */
	var _editHandler = function _editHandler() {
		var $tr = $(this).closest('tr'),
		    $edited = $this.find('tr.edit'),
		    promises = [];

		if (!options.multiEdit && $edited.length) {
			// If multiEdit is disabled and other lines are in edit mode, wait for confirmation
			// of the abort event on the other lines.
			$edited.each(function () {
				promises.push(_abortHandler.call($(this).find('.row_abort').first()));
			});
		}

		$.when.apply(undefined, promises).promise().done(function () {
			// Store the current data of the line in cache
			$tr.data('formcache', jse.libs.form.getData($tr, undefined, true));
			_switchState('edit', $tr, true);
		});
	};

	/**
  * Handler for the "save"-button
  */
	var _saveHandler = function _saveHandler() {
		var $self = $(this),
		    $tr = $self.closest('tr'),
		    dataset = jse.libs.form.getData($tr, undefined, true),
		    url = $self.data().url,
		    deferred = $.Deferred();

		// Done callback on validation success
		deferred.done(function () {
			if (url) {
				// If a url is given, post the data against the server
				jse.core.debug.info('Sending data:', dataset);
				jse.libs.xhr.ajax({
					'url': url,
					'data': dataset
				});
			}

			$this.trigger('row_saved', [dataset]);
			_switchState('default', $tr, true);
		});

		// Get validation state of the line. On success goto deferred.done callback
		$tr.trigger('validator.validate', [{
			'deferred': deferred
		}]);
	};

	/**
  * Handler for the "delete"-button
  */
	var _deleteHandler = function _deleteHandler() {
		var $self = $(this),
		    $tr = $self.closest('tr'),
		    dataset = {
			id: $tr.data('id')
		},
		    url = $self.data().url,
		    html = '<a href="lightbox_confirm.html?section=shop_offline&amp;message=delete_job' + '&amp;buttons=cancel-delete"></a>',
		    lightboxLink = $(html),
		    lightboxId = lightboxLink.lightbox_plugin({
			'lightbox_width': '360px'
		});

		$('#lightbox_package_' + lightboxId).one('click', '.delete', function () {
			$.lightbox_plugin('close', lightboxId);

			if (url) {
				// If a url is given, post the data against the server
				jse.libs.xhr.ajax({
					'url': url,
					'data': dataset
				});
			}

			$this.trigger('row_deleted', [dataset]);
			$tr.remove();
		});
	};

	/**
  * Handler for the 'add'-button
  */
	var _addHandler = function _addHandler() {
		var $self = $(this),
		    url = $self.data().url,
		    $tr = $self.closest('tr'),
		    dataset = jse.libs.form.getData($tr, undefined, true),
		    deferred = $.Deferred();

		// Done callback on validation success
		deferred.done(function () {
			var _finalize = function _finalize() {
				// Switch the state of the line and
				// create a new 'add'-line
				$this.trigger('row_added', [dataset]);
				_switchState('default', $tr, true);
				_createNewLine();
			};

			if (url) {
				// If a url is given, post the data against the server
				// The respone of the server contains an id, which will be
				// injected into the field names
				jse.core.debug.info('Sending data:', dataset);
				jse.libs.xhr.ajax({
					'url': url,
					'data': dataset
				}).done(function (result) {
					var id = result.id,
					    $targets = $tr.find('input:not(:button), textarea, select');

					$targets.each(function () {
						var $elem = $(this),
						    name = $elem.attr('name').replace('[0]', '[' + id + ']');

						if ($elem.data().lightboxHref) {
							$elem.data().lightboxHref = $elem.data().lightboxHref.replace('id=0', 'id=' + id);
						}
						$elem.attr('name', name);
					});

					$tr.find('[data-lightbox-href]').each(function () {
						var newLink = $(this).attr('data-lightbox-href').replace('id=0', 'id=' + id);
						$(this).attr('data-lightbox-href', newLink).data().lightboxHref = newLink;
					});

					_finalize();
				});
			} else {
				_finalize();
			}
		});

		// Get validation state of the line. On success goto deferred.done callback
		$tr.trigger('validator.validate', [{
			'deferred': deferred
		}]);
	};

	/**
  * Handler to update the table state, if an widget inside the table gets initialized
  * (needed to disable the datepicker buttons).
  *
  * @param {object} e    jQuery event-object
  */
	var _initialiedHandler = function _initialiedHandler(e) {
		var inside = $this.filter($(e.target)).add($this.find($(e.target))).length;

		if (!inside) {
			var $tr = $(e.target).closest('tr'),
			    type = $tr.hasClass('edit') ? 'edit' : $tr.hasClass('add') ? 'add' : 'default';

			_switchState(type, $tr, true);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {
		$template = $this.find('tfoot > tr');
		$table_body = $this.children('tbody');

		// Add a special class to the table, to style
		// disabled input boxes
		$this.addClass('table_inlineedit');

		// Set the default state for all tr
		_switchState('default', $table_body);
		// Add the "Add"-line to the table
		_createNewLine();

		// Add event listeners for all buttons and
		// a listener for the widget initialized event
		// from widgets inside the table
		$this.on('click', '.row_edit', _editHandler).on('click', '.row_delete', _deleteHandler).on('click', '.row_save', _saveHandler).on('click', '.row_add', _addHandler).on('click', '.row_abort', _abortHandler).on('widget.initialized', _initialiedHandler);

		$('body').on('validator.validate', function (e, d) {
			if (d && d.deferred) {
				// Event listener that performs on every validate trigger that isn't handled by the validator.
				d.deferred.resolve();
			}
		});
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRhYmxlX2lubGluZV9lZGl0LmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkdGVtcGxhdGUiLCIkdGFibGVfYm9keSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zd2l0Y2hTdGF0ZSIsIm1vZGUiLCIkZWxlbWVudCIsImFkZENsYXNzIiwiJHRhcmdldHMiLCJmaW5kIiwiJGVkaXQiLCJmaWx0ZXIiLCIkYWRkIiwiJGRlZmF1bHQiLCIkb3RoZXJzIiwiaGlkZSIsInJlbW92ZSIsInNob3ciLCJwcm9wIiwiZWFjaCIsIiRzZWxmIiwiZGF0YXNldCIsImpzZSIsImxpYnMiLCJmYWxsYmFjayIsIl9kYXRhIiwiYXR0ciIsInRvTG93ZXJDYXNlIiwiYWx0IiwidmFsdWVzIiwic3BsaXQiLCJjaGVja2VkIiwiYWZ0ZXIiLCJ3YWl0VW50aWxWYWx1ZXMiLCJjaGlsZHJlbiIsImxlbmd0aCIsInNldFRpbWVvdXQiLCJ2YWwiLCJ0ZXh0IiwidHJpZ2dlciIsInJlbW92ZUNsYXNzIiwiX2NyZWF0ZU5ld0xpbmUiLCIkbmV3TGluZSIsImNsb25lIiwicmVwbGFjZSIsInNldHVwV2lkZ2V0QXR0ciIsImFwcGVuZCIsIndpZGdldHMiLCJpbml0IiwibGFzdCIsImNvbnRyb2xsZXJzIiwiY29tcGF0aWJpbGl0eSIsIl9hYm9ydEhhbmRsZXIiLCIkdHIiLCJjbG9zZXN0IiwiY2FjaGUiLCJKU09OIiwic3RyaW5naWZ5IiwiY3VycmVudCIsImZvcm0iLCJnZXREYXRhIiwidW5kZWZpbmVkIiwiZGVmZXJyZWQiLCJEZWZlcnJlZCIsIl9yZXNldExpbmUiLCJlIiwiaWQiLCJvZmYiLCJsaWdodGJveF9wbHVnaW4iLCJyZWplY3QiLCJwcmVmaWxsRm9ybSIsInJlc29sdmUiLCJocmVmIiwibGlua0h0bWwiLCJsaWdodGJveExpbmsiLCJsaWdodGJveElkIiwib25lIiwicHJvbWlzZSIsIl9lZGl0SGFuZGxlciIsIiRlZGl0ZWQiLCJwcm9taXNlcyIsIm11bHRpRWRpdCIsInB1c2giLCJjYWxsIiwiZmlyc3QiLCJ3aGVuIiwiYXBwbHkiLCJkb25lIiwiX3NhdmVIYW5kbGVyIiwidXJsIiwiY29yZSIsImRlYnVnIiwiaW5mbyIsInhociIsImFqYXgiLCJfZGVsZXRlSGFuZGxlciIsImh0bWwiLCJfYWRkSGFuZGxlciIsIl9maW5hbGl6ZSIsInJlc3VsdCIsIiRlbGVtIiwibmFtZSIsImxpZ2h0Ym94SHJlZiIsIm5ld0xpbmsiLCJfaW5pdGlhbGllZEhhbmRsZXIiLCJpbnNpZGUiLCJ0YXJnZXQiLCJhZGQiLCJ0eXBlIiwiaGFzQ2xhc3MiLCJvbiIsImQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7O0FBTUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLG1CQURELEVBR0MsQ0FBQyxNQUFELEVBQVMsS0FBVCxFQUFnQixVQUFoQixDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLGFBQVksSUFiYjs7O0FBZUM7Ozs7O0FBS0FDLGVBQWMsSUFwQmY7OztBQXNCQzs7Ozs7QUFLQUMsWUFBVztBQUNWLGVBQWE7QUFESCxFQTNCWjs7O0FBK0JDOzs7OztBQUtBQyxXQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCTCxJQUE3QixDQXBDWDs7O0FBc0NDOzs7OztBQUtBRCxVQUFTLEVBM0NWOztBQTZDQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7Ozs7Ozs7QUFVQSxLQUFJUyxlQUFlLFNBQWZBLFlBQWUsQ0FBU0MsSUFBVCxFQUFlQyxRQUFmLEVBQXlCQyxRQUF6QixFQUFtQzs7QUFFckQsTUFBSUMsV0FBV0YsU0FBU0csSUFBVCxDQUFjLG9DQUFkLENBQWY7QUFBQSxNQUNDQyxRQUFRRixTQUFTRyxN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QyxRQUFOLENBQWUsT0FBZixDQUFkOztBQUVBO0FBQ0E7QUFDQXJDLFFBQU1VLFFBQU4sQ0FBZSxrQkFBZjs7QUFFQTtBQUNBSCxlQUFhLFNBQWIsRUFBd0JKLFdBQXhCO0FBQ0E7QUFDQXlDOztBQUVBO0FBQ0E7QUFDQTtBQUNBNUMsUUFDRWlILEVBREYsQ0FDSyxPQURMLEVBQ2MsV0FEZCxFQUMyQmhDLFlBRDNCLEVBRUVnQyxFQUZGLENBRUssT0FGTCxFQUVjLGFBRmQsRUFFNkJmLGNBRjdCLEVBR0VlLEVBSEYsQ0FHSyxPQUhMLEVBR2MsV0FIZCxFQUcyQnRCLFlBSDNCLEVBSUVzQixFQUpGLENBSUssT0FKTCxFQUljLFVBSmQsRUFJMEJiLFdBSjFCLEVBS0VhLEVBTEYsQ0FLSyxPQUxMLEVBS2MsWUFMZCxFQUs0QjFELGFBTDVCLEVBTUUwRCxFQU5GLENBTUssb0JBTkwsRUFNMkJOLGtCQU4zQjs7QUFRQTFHLElBQUUsTUFBRixFQUNFZ0gsRUFERixDQUNLLG9CQURMLEVBQzJCLFVBQVU3QyxDQUFWLEVBQWE4QyxDQUFiLEVBQWdCO0FBQ3pDLE9BQUlBLEtBQUtBLEVBQUVqRCxRQUFYLEVBQXFCO0FBQ3BCO0FBQ0FpRCxNQUFFakQsUUFBRixDQUFXUyxPQUFYO0FBQ0E7QUFDRCxHQU5GO0FBT0FnQjtBQUNBLEVBaENEOztBQWtDQTtBQUNBLFFBQU81RixNQUFQO0FBQ0EsQ0F4ZEYiLCJmaWxlIjoidGFibGVfaW5saW5lX2VkaXQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRhYmxlX2lubGluZV9lZGl0LmpzIDIwMTUtMTAtMTYgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEV4dGVuc2lvbiBmb3IgbWFraW5nIHRhYmxlcyBlZGl0YWJsZS5cbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdGFibGVfaW5saW5lX2VkaXRcbiAqIEBpZ25vcmVcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCd0YWJsZV9pbmxpbmVfZWRpdCcsXG5cdFxuXHRbJ2Zvcm0nLCAneGhyJywgJ2ZhbGxiYWNrJ10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBUZW1wbGF0ZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0ZW1wbGF0ZSA9IG51bGwsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGFibGUgQm9keSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0YWJsZV9ib2R5ID0gbnVsbCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQnbXVsdGlFZGl0JzogZmFsc2Vcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRlVOQ1RJT05BTElUWVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFN3aXRjaCBTdGF0ZVxuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBlbmFibGVzIC8gZGlzYWJsZXMsIGRlcGVuZGluZyBvbiB0aGUgbW9kZSwgYWxsIGlucHV0IGZpZWxkcyBpbnNpZGVcblx0XHQgKiB0aGUgJGVsZW1lbnQgYW5kIHNob3dzIC8gaGlkZXMgdGhlIGNvcnJlc3BvbmRpbmcgYnV0dG9ucy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBtb2RlIFNldCB0aGUgZ2l2ZW4gbW9kZS4gUG9zc2libGUgdmFsdWVzOiAnZWRpdCcsICdhZGQnLCAnZGVmYXVsdCdcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gJGVsZW1lbnQgVGhlIGVsZW1lbnQgalF1ZXJ5IHNlbGVjdGlvbiB0aGF0IGdldHMgbW9kaWZpZWRcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IGFkZENsYXNzIElmIHRydWUsIHRoZSBzdGF0ZSBjbGFzcyBnZXRzIGFkZGVkIHRvIHRoZSBlbGVtZW50XG5cdFx0ICovXG5cdFx0dmFyIF9zd2l0Y2hTdGF0ZSA9IGZ1bmN0aW9uKG1vZGUsICRlbGVtZW50LCBhZGRDbGFzcykge1xuXHRcdFx0XG5cdFx0XHR2YXIgJHRhcmdldHMgPSAkZWxlbWVudC5maW5kKCdpbnB1dCwgdGV4dGFyZWEsIHNlbGVjdCwgYnV0dG9uLCBpJyksXG5cdFx0XHRcdCRlZGl0ID0gJHRhcmdldHMuZmlsdGVyKCcuZWRpdG1vZGUnKSxcblx0XHRcdFx0JGFkZCA9ICR0YXJnZXRzLmZpbHRlcignLmFkZG1vZGUnKSxcblx0XHRcdFx0JGRlZmF1bHQgPSAkdGFyZ2V0cy5maWx0ZXIoJy5kZWZhdWx0bW9kZScpLFxuXHRcdFx0XHQkb3RoZXJzID0gJHRhcmdldHMuZmlsdGVyKCc6bm90KC5lZGl0bW9kZSk6bm90KC5hZGRtb2RlKTpub3QoLmRlZmF1bHRtb2RlKScpO1xuXHRcdFx0XG5cdFx0XHQvLyBIaWRlIGFsbCBidXR0b25zXG5cdFx0XHQkZWRpdC5oaWRlKCk7XG5cdFx0XHQkYWRkLmhpZGUoKTtcblx0XHRcdCRkZWZhdWx0LmhpZGUoKTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVtb3ZlIGFsdC10ZXh0IGlmIGF2YWlsYWJsZVxuXHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0LmZpbmQoJy50YWJsZV9pbmxpbmVlZGl0X2FsdCcpXG5cdFx0XHRcdC5yZW1vdmUoKTtcblx0XHRcdFxuXHRcdFx0c3dpdGNoIChtb2RlKSB7XG5cdFx0XHRcdGNhc2UgJ2VkaXQnOlxuXHRcdFx0XHRcdC8vIFN3aXRjaCB0byBlZGl0IG1vZGVcblx0XHRcdFx0XHQkZWRpdC5zaG93KCk7XG5cdFx0XHRcdFx0JG90aGVycy5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnYWRkJzpcblx0XHRcdFx0XHQvLyBTd2l0Y2ggdG8gYWRkIG1vZGVcblx0XHRcdFx0XHQkYWRkLnNob3coKTtcblx0XHRcdFx0XHQkb3RoZXJzLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdC8vIFN3aXRjaCB0byBkZWZhdWx0LW1vZGVcblx0XHRcdFx0XHQkZGVmYXVsdC5zaG93KCk7XG5cdFx0XHRcdFx0JG90aGVyc1xuXHRcdFx0XHRcdFx0LnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSlcblx0XHRcdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQvLyBDaGVjayBpZiB0aGVyZSBpcyBhbiBhbHQgdGV4dCBnaXZlbiBmb3IgdGhlIGlucHV0IGZpZWxkXG5cdFx0XHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0XHRcdFx0ZGF0YXNldCA9IGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCRzZWxmLCAndGFibGVfaW5saW5lX2VkaXQnKTtcblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdC8vIFJlcGxhY2Ugc29tZSBraW5kIG9mIGZvcm0gZmllbGRzIHdpdGggc3BhbiB0YWdzXG5cdFx0XHRcdFx0XHRcdGlmICgkc2VsZi5hdHRyKCd0eXBlJykgJiYgJHNlbGYuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCkgPT09ICdjaGVja2JveCcgJiYgZGF0YXNldC5hbHQpIHtcblx0XHRcdFx0XHRcdFx0XHR2YXIgdmFsdWVzID0gZGF0YXNldC5hbHQuc3BsaXQoJ18nKSxcblx0XHRcdFx0XHRcdFx0XHRcdGNoZWNrZWQgPSAkc2VsZi5wcm9wKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0XHRcdFx0JHNlbGYuYWZ0ZXIoJzxzcGFuIGNsYXNzPVwidGFibGVfaW5saW5lZWRpdF9hbHRcIj4nICsgKGNoZWNrZWQgPyB2YWx1ZXNbMF0gOiB2YWx1ZXNbMV0pICtcblx0XHRcdFx0XHRcdFx0XHRcdCc8L3NwYW4+Jyk7XG5cdFx0XHRcdFx0XHRcdH0gZWxzZSBpZiAoJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCkgPT09ICdzZWxlY3QnKSB7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHdhaXRVbnRpbFZhbHVlcyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JGVkaXQuaGlkZSgpO1xuXHRcdFx0XHRcdFx0XHRcdFx0aWYgKCEkc2VsZi5jaGlsZHJlbigpLmxlbmd0aCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdHdhaXRVbnRpbFZhbHVlcygpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR9LCAyMDApO1xuXHRcdFx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0JHNlbGYuY2hpbGRyZW4oJ1t2YWx1ZT1cIicgKyAkc2VsZi52YWwoKSArICdcIl0nKS50ZXh0KCk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdCRzZWxmLmFmdGVyKFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8c3BhbiBjbGFzcz1cInRhYmxlX2lubGluZWVkaXRfYWx0XCI+JyArXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHNlbGYuY2hpbGRyZW4oJ1t2YWx1ZT1cIicgKyAkc2VsZi52YWwoKSArICdcIl0nKS50ZXh0KCkgK1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8L3NwYW4+J1xuXHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0fTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHR3YWl0VW50aWxWYWx1ZXMoKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCR0aGlzLnRyaWdnZXIoJ0ZPUk1fVVBEQVRFJywgW10pO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgdGhlIG1vZGUgY2xhc3Ncblx0XHRcdGlmIChhZGRDbGFzcykge1xuXHRcdFx0XHQkZWxlbWVudFxuXHRcdFx0XHRcdC5yZW1vdmVDbGFzcygnZWRpdCBhZGQgZGVmYXVsdCcpXG5cdFx0XHRcdFx0LmFkZENsYXNzKG1vZGUpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ3JlYXRlIE5ldyBMaW5lXG5cdFx0ICpcblx0XHQgKiBDcmVhdGVzIGEgbmV3IFwiYWRkXCItbGluZSBieSBjbG9uaW5nIHRoZSBmb290ZXIgdGVtcGxhdGUuXG5cdFx0ICovXG5cdFx0dmFyIF9jcmVhdGVOZXdMaW5lID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJG5ld0xpbmUgPSAkdGVtcGxhdGUuY2xvbmUoKTtcblx0XHRcdFxuXHRcdFx0JG5ld0xpbmVcblx0XHRcdFx0LmZpbmQoJ1tuYW1lXScpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JHNlbGYuYXR0cignbmFtZScsICRzZWxmLmF0dHIoJ25hbWUnKS5yZXBsYWNlKCdbXScsICdbMF0nKSk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRfc3dpdGNoU3RhdGUoJ2FkZCcsICRuZXdMaW5lLCB0cnVlKTtcblx0XHRcdC8vIFJlbmFtZSB0aGUgdGVtcG9yYXJpbHkgd2lkZ2V0IGRhdGEgYXR0cmlidXRlc1xuXHRcdFx0anNlLmxpYnMuZmFsbGJhY2suc2V0dXBXaWRnZXRBdHRyKCRuZXdMaW5lKTtcblx0XHRcdCR0YWJsZV9ib2R5LmFwcGVuZCgkbmV3TGluZSk7XG5cdFx0XHQvLyBTdGFydCB0aGUgd2lkZ2V0c1xuXHRcdFx0Z3gud2lkZ2V0cy5pbml0KCR0YWJsZV9ib2R5LmZpbmQoJ3RyJykubGFzdCgpKTtcblx0XHRcdGd4LmV4dGVuc2lvbnMuaW5pdCgkdGFibGVfYm9keS5maW5kKCd0cicpLmxhc3QoKSk7XG5cdFx0XHRneC5jb250cm9sbGVycy5pbml0KCR0YWJsZV9ib2R5LmZpbmQoJ3RyJykubGFzdCgpKTtcblx0XHRcdGd4LmNvbXBhdGliaWxpdHkuaW5pdCgkdGFibGVfYm9keS5maW5kKCd0cicpLmxhc3QoKSk7XG5cdFx0XHRqc2Uud2lkZ2V0cy5pbml0KCR0YWJsZV9ib2R5LmZpbmQoJ3RyJykubGFzdCgpKTtcblx0XHRcdGpzZS5leHRlbnNpb25zLmluaXQoJHRhYmxlX2JvZHkuZmluZCgndHInKS5sYXN0KCkpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgXCJhYm9ydFwiLWJ1dHRvblxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59IElmIGZ1bmN0aW9uIGdldHMgY2FsbGVkIGRpcmVjdGx5LCB0aGUgcmV0dXJuIHZhbHVlIGlzIHRoZSBzdGF0ZSBvZiB0aGUgYWJvcnQuXG5cdFx0ICovXG5cdFx0dmFyIF9hYm9ydEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkdHIgPSAkKHRoaXMpLmNsb3Nlc3QoJ3RyJyksXG5cdFx0XHRcdGNhY2hlID0gSlNPTi5zdHJpbmdpZnkoJHRyLmRhdGEoJ2Zvcm1jYWNoZScpKSxcblx0XHRcdFx0Y3VycmVudCA9IEpTT04uc3RyaW5naWZ5KGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdHIsIHVuZGVmaW5lZCwgdHJ1ZSkpLFxuXHRcdFx0XHRkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gcmVzZXQgYSBsaW5lIHN0YXRlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0dmFyIF9yZXNldExpbmUgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdGlmIChlKSB7XG5cdFx0XHRcdFx0JCgnI2xpZ2h0Ym94X3BhY2thZ2VfJyArIGUuZGF0YS5pZCArICdhZG1pbl9idXR0b24nKS5vZmYoKTtcblx0XHRcdFx0XHQkKCcjbGlnaHRib3hfcGFja2FnZV8nICsgZS5kYXRhLmlkKTtcblx0XHRcdFx0XHQkLmxpZ2h0Ym94X3BsdWdpbignY2xvc2UnLCBlLmRhdGEuaWQpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoZSAmJiBlLmRhdGEucmVqZWN0KSB7XG5cdFx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0Ly8gUmVzZXQgdGhlIHZhbGlkYXRpb24gc3RhdGVcblx0XHRcdFx0XHQkdHIudHJpZ2dlcigndmFsaWRhdG9yLnJlc2V0JywgW10pO1xuXHRcdFx0XHRcdC8vIFJlc2V0IHRoZSBmb3JtIGRhdGFcblx0XHRcdFx0XHRqc2UubGlicy5mb3JtLnByZWZpbGxGb3JtKCR0ciwgJHRyLmRhdGEoJ2Zvcm1jYWNoZScpLCB0cnVlKTtcblx0XHRcdFx0XHRfc3dpdGNoU3RhdGUoJ2RlZmF1bHQnLCAkdHIsIHRydWUpO1xuXHRcdFx0XHRcdGRlZmVycmVkLnJlc29sdmUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblx0XHRcdFxuXHRcdFx0Ly8gQ29tcGFyZSB0aGUgb2xkIHdpdGggdGhlIG5ldyBkYXRhLiBJZiBjaGFuZ2VzIHdlcmUgbWFkZSwgY29uZmlybSB0aGUgYWJvcnRcblx0XHRcdGlmIChjYWNoZSAhPT0gY3VycmVudCkge1xuXHRcdFx0XHR2YXJcblx0XHRcdFx0XHRocmVmID0gJ2xpZ2h0Ym94X2NvbmZpcm0uaHRtbD9zZWN0aW9uPXNob3Bfb2ZmbGluZSZhbXA7JyArXG5cdFx0XHRcdFx0XHQnbWVzc2FnZT1kaWNhcmRfY2hhbmdlc19oaW50JmFtcDtidXR0b25zPWNhbmNlbC1kaXNjYXJkJyxcblx0XHRcdFx0XHRsaW5rSHRtbCA9ICc8YSBocmVmPVwiJyArIGhyZWYgKyAnXCI+PC9hPicsXG5cdFx0XHRcdFx0bGlnaHRib3hMaW5rID0gJChsaW5rSHRtbCksXG5cdFx0XHRcdFx0bGlnaHRib3hJZCA9IGxpZ2h0Ym94TGluay5saWdodGJveF9wbHVnaW4oe1xuXHRcdFx0XHRcdFx0J2xpZ2h0Ym94X3dpZHRoJzogJzM2MHB4J1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0JCgnI2xpZ2h0Ym94X3BhY2thZ2VfJyArIGxpZ2h0Ym94SWQpXG5cdFx0XHRcdFx0Lm9uZSgnY2xpY2snLCAnLmRpc2NhcmQnLCB7XG5cdFx0XHRcdFx0XHQncmVqZWN0JzogZmFsc2UsXG5cdFx0XHRcdFx0XHQnaWQnOiBsaWdodGJveElkXG5cdFx0XHRcdFx0fSwgX3Jlc2V0TGluZSlcblx0XHRcdFx0XHQub25lKCdjbGljaycsICcuY2FuY2VsJywge1xuXHRcdFx0XHRcdFx0J3JlamVjdCc6IHRydWUsXG5cdFx0XHRcdFx0XHQnaWQnOiBsaWdodGJveElkXG5cdFx0XHRcdFx0fSwgX3Jlc2V0TGluZSk7XG5cdFx0XHRcdFxuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0X3Jlc2V0TGluZSgpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRyZXR1cm4gZGVmZXJyZWQucHJvbWlzZSgpO1xuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgXCJlZGl0XCItYnV0dG9uXG5cdFx0ICovXG5cdFx0dmFyIF9lZGl0SGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICR0ciA9ICQodGhpcykuY2xvc2VzdCgndHInKSxcblx0XHRcdFx0JGVkaXRlZCA9ICR0aGlzLmZpbmQoJ3RyLmVkaXQnKSxcblx0XHRcdFx0cHJvbWlzZXMgPSBbXTtcblx0XHRcdFxuXHRcdFx0aWYgKCFvcHRpb25zLm11bHRpRWRpdCAmJiAkZWRpdGVkLmxlbmd0aCkge1xuXHRcdFx0XHQvLyBJZiBtdWx0aUVkaXQgaXMgZGlzYWJsZWQgYW5kIG90aGVyIGxpbmVzIGFyZSBpbiBlZGl0IG1vZGUsIHdhaXQgZm9yIGNvbmZpcm1hdGlvblxuXHRcdFx0XHQvLyBvZiB0aGUgYWJvcnQgZXZlbnQgb24gdGhlIG90aGVyIGxpbmVzLlxuXHRcdFx0XHQkZWRpdGVkXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRwcm9taXNlcy5wdXNoKF9hYm9ydEhhbmRsZXIuY2FsbCgkKHRoaXMpLmZpbmQoJy5yb3dfYWJvcnQnKS5maXJzdCgpKSk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCQud2hlbi5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKS5wcm9taXNlKCkuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0Ly8gU3RvcmUgdGhlIGN1cnJlbnQgZGF0YSBvZiB0aGUgbGluZSBpbiBjYWNoZVxuXHRcdFx0XHQkdHIuZGF0YSgnZm9ybWNhY2hlJywganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0ciwgdW5kZWZpbmVkLCB0cnVlKSk7XG5cdFx0XHRcdF9zd2l0Y2hTdGF0ZSgnZWRpdCcsICR0ciwgdHJ1ZSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBcInNhdmVcIi1idXR0b25cblx0XHQgKi9cblx0XHR2YXIgX3NhdmVIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkdHIgPSAkc2VsZi5jbG9zZXN0KCd0cicpLFxuXHRcdFx0XHRkYXRhc2V0ID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0ciwgdW5kZWZpbmVkLCB0cnVlKSxcblx0XHRcdFx0dXJsID0gJHNlbGYuZGF0YSgpLnVybCxcblx0XHRcdFx0ZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcblx0XHRcdC8vIERvbmUgY2FsbGJhY2sgb24gdmFsaWRhdGlvbiBzdWNjZXNzXG5cdFx0XHRkZWZlcnJlZC5kb25lKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRpZiAodXJsKSB7XG5cdFx0XHRcdFx0Ly8gSWYgYSB1cmwgaXMgZ2l2ZW4sIHBvc3QgdGhlIGRhdGEgYWdhaW5zdCB0aGUgc2VydmVyXG5cdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuaW5mbygnU2VuZGluZyBkYXRhOicsIGRhdGFzZXQpO1xuXHRcdFx0XHRcdGpzZS5saWJzLnhoci5hamF4KHtcblx0XHRcdFx0XHRcdCd1cmwnOiB1cmwsXG5cdFx0XHRcdFx0XHQnZGF0YSc6IGRhdGFzZXRcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0JHRoaXMudHJpZ2dlcigncm93X3NhdmVkJywgW2RhdGFzZXRdKTtcblx0XHRcdFx0X3N3aXRjaFN0YXRlKCdkZWZhdWx0JywgJHRyLCB0cnVlKTtcblx0XHRcdFx0XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gR2V0IHZhbGlkYXRpb24gc3RhdGUgb2YgdGhlIGxpbmUuIE9uIHN1Y2Nlc3MgZ290byBkZWZlcnJlZC5kb25lIGNhbGxiYWNrXG5cdFx0XHQkdHIudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJywgW1xuXHRcdFx0XHR7XG5cdFx0XHRcdFx0J2RlZmVycmVkJzogZGVmZXJyZWRcblx0XHRcdFx0fVxuXHRcdFx0XSk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBcImRlbGV0ZVwiLWJ1dHRvblxuXHRcdCAqL1xuXHRcdHZhciBfZGVsZXRlSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0JHRyID0gJHNlbGYuY2xvc2VzdCgndHInKSxcblx0XHRcdFx0ZGF0YXNldCA9IHtcblx0XHRcdFx0XHRpZDogJHRyLmRhdGEoJ2lkJylcblx0XHRcdFx0fSxcblx0XHRcdFx0dXJsID0gJHNlbGYuZGF0YSgpLnVybCxcblx0XHRcdFx0aHRtbCA9ICc8YSBocmVmPVwibGlnaHRib3hfY29uZmlybS5odG1sP3NlY3Rpb249c2hvcF9vZmZsaW5lJmFtcDttZXNzYWdlPWRlbGV0ZV9qb2InXG5cdFx0XHRcdFx0KyAnJmFtcDtidXR0b25zPWNhbmNlbC1kZWxldGVcIj48L2E+Jyxcblx0XHRcdFx0bGlnaHRib3hMaW5rID0gJChodG1sKSxcblx0XHRcdFx0bGlnaHRib3hJZCA9IGxpZ2h0Ym94TGluay5saWdodGJveF9wbHVnaW4oe1xuXHRcdFx0XHRcdCdsaWdodGJveF93aWR0aCc6ICczNjBweCdcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoJyNsaWdodGJveF9wYWNrYWdlXycgKyBsaWdodGJveElkKS5vbmUoJ2NsaWNrJywgJy5kZWxldGUnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JC5saWdodGJveF9wbHVnaW4oJ2Nsb3NlJywgbGlnaHRib3hJZCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAodXJsKSB7XG5cdFx0XHRcdFx0Ly8gSWYgYSB1cmwgaXMgZ2l2ZW4sIHBvc3QgdGhlIGRhdGEgYWdhaW5zdCB0aGUgc2VydmVyXG5cdFx0XHRcdFx0anNlLmxpYnMueGhyLmFqYXgoe1xuXHRcdFx0XHRcdFx0J3VybCc6IHVybCxcblx0XHRcdFx0XHRcdCdkYXRhJzogZGF0YXNldFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdyb3dfZGVsZXRlZCcsIFtkYXRhc2V0XSk7XG5cdFx0XHRcdCR0ci5yZW1vdmUoKTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlciBmb3IgdGhlICdhZGQnLWJ1dHRvblxuXHRcdCAqL1xuXHRcdHZhciBfYWRkSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0dXJsID0gJHNlbGYuZGF0YSgpLnVybCxcblx0XHRcdFx0JHRyID0gJHNlbGYuY2xvc2VzdCgndHInKSxcblx0XHRcdFx0ZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdHIsIHVuZGVmaW5lZCwgdHJ1ZSksXG5cdFx0XHRcdGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFx0XG5cdFx0XHQvLyBEb25lIGNhbGxiYWNrIG9uIHZhbGlkYXRpb24gc3VjY2Vzc1xuXHRcdFx0ZGVmZXJyZWQuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIF9maW5hbGl6ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdC8vIFN3aXRjaCB0aGUgc3RhdGUgb2YgdGhlIGxpbmUgYW5kXG5cdFx0XHRcdFx0Ly8gY3JlYXRlIGEgbmV3ICdhZGQnLWxpbmVcblx0XHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdyb3dfYWRkZWQnLCBbZGF0YXNldF0pO1xuXHRcdFx0XHRcdF9zd2l0Y2hTdGF0ZSgnZGVmYXVsdCcsICR0ciwgdHJ1ZSk7XG5cdFx0XHRcdFx0X2NyZWF0ZU5ld0xpbmUoKTtcblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmICh1cmwpIHtcblx0XHRcdFx0XHQvLyBJZiBhIHVybCBpcyBnaXZlbiwgcG9zdCB0aGUgZGF0YSBhZ2FpbnN0IHRoZSBzZXJ2ZXJcblx0XHRcdFx0XHQvLyBUaGUgcmVzcG9uZSBvZiB0aGUgc2VydmVyIGNvbnRhaW5zIGFuIGlkLCB3aGljaCB3aWxsIGJlXG5cdFx0XHRcdFx0Ly8gaW5qZWN0ZWQgaW50byB0aGUgZmllbGQgbmFtZXNcblx0XHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy5pbmZvKCdTZW5kaW5nIGRhdGE6JywgZGF0YXNldCk7XG5cdFx0XHRcdFx0anNlLmxpYnMueGhyLmFqYXgoe1xuXHRcdFx0XHRcdFx0J3VybCc6IHVybCxcblx0XHRcdFx0XHRcdCdkYXRhJzogZGF0YXNldFxuXHRcdFx0XHRcdH0pLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFx0XHR2YXIgaWQgPSByZXN1bHQuaWQsXG5cdFx0XHRcdFx0XHRcdCR0YXJnZXRzID0gJHRyLmZpbmQoJ2lucHV0Om5vdCg6YnV0dG9uKSwgdGV4dGFyZWEsIHNlbGVjdCcpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkdGFyZ2V0cy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHR2YXIgJGVsZW0gPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRcdG5hbWUgPSAkZWxlbVxuXHRcdFx0XHRcdFx0XHRcdFx0LmF0dHIoJ25hbWUnKVxuXHRcdFx0XHRcdFx0XHRcdFx0LnJlcGxhY2UoJ1swXScsICdbJyArIGlkICsgJ10nKTtcblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdGlmICgkZWxlbS5kYXRhKCkubGlnaHRib3hIcmVmKSB7XG5cdFx0XHRcdFx0XHRcdFx0JGVsZW0uZGF0YSgpLmxpZ2h0Ym94SHJlZiA9ICRlbGVtLmRhdGEoKS5saWdodGJveEhyZWYucmVwbGFjZSgnaWQ9MCcsICdpZD0nICsgaWQpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdCRlbGVtLmF0dHIoJ25hbWUnLCBuYW1lKTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkdHIuZmluZCgnW2RhdGEtbGlnaHRib3gtaHJlZl0nKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHR2YXIgbmV3TGluayA9ICQodGhpcykuYXR0cignZGF0YS1saWdodGJveC1ocmVmJykucmVwbGFjZSgnaWQ9MCcsICdpZD0nICsgaWQpO1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpXG5cdFx0XHRcdFx0XHRcdFx0LmF0dHIoJ2RhdGEtbGlnaHRib3gtaHJlZicsIG5ld0xpbmspXG5cdFx0XHRcdFx0XHRcdFx0LmRhdGEoKS5saWdodGJveEhyZWYgPSBuZXdMaW5rO1xuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdF9maW5hbGl6ZSgpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdF9maW5hbGl6ZSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gR2V0IHZhbGlkYXRpb24gc3RhdGUgb2YgdGhlIGxpbmUuIE9uIHN1Y2Nlc3MgZ290byBkZWZlcnJlZC5kb25lIGNhbGxiYWNrXG5cdFx0XHQkdHIudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJywgW1xuXHRcdFx0XHR7XG5cdFx0XHRcdFx0J2RlZmVycmVkJzogZGVmZXJyZWRcblx0XHRcdFx0fVxuXHRcdFx0XSk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgdG8gdXBkYXRlIHRoZSB0YWJsZSBzdGF0ZSwgaWYgYW4gd2lkZ2V0IGluc2lkZSB0aGUgdGFibGUgZ2V0cyBpbml0aWFsaXplZFxuXHRcdCAqIChuZWVkZWQgdG8gZGlzYWJsZSB0aGUgZGF0ZXBpY2tlciBidXR0b25zKS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBlICAgIGpRdWVyeSBldmVudC1vYmplY3Rcblx0XHQgKi9cblx0XHR2YXIgX2luaXRpYWxpZWRIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0dmFyIGluc2lkZSA9ICR0aGlzXG5cdFx0XHRcdC5maWx0ZXIoJChlLnRhcmdldCkpXG5cdFx0XHRcdC5hZGQoJHRoaXMuZmluZCgkKGUudGFyZ2V0KSkpXG5cdFx0XHRcdC5sZW5ndGg7XG5cdFx0XHRcblx0XHRcdGlmICghaW5zaWRlKSB7XG5cdFx0XHRcdHZhciAkdHIgPSAkKGUudGFyZ2V0KS5jbG9zZXN0KCd0cicpLFxuXHRcdFx0XHRcdHR5cGUgPSAoJHRyLmhhc0NsYXNzKCdlZGl0JykpID8gJ2VkaXQnIDpcblx0XHRcdFx0XHQgICAgICAgKCR0ci5oYXNDbGFzcygnYWRkJykpID8gJ2FkZCcgOlxuXHRcdFx0XHRcdCAgICAgICAnZGVmYXVsdCc7XG5cdFx0XHRcdFxuXHRcdFx0XHRfc3dpdGNoU3RhdGUodHlwZSwgJHRyLCB0cnVlKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGVtcGxhdGUgPSAkdGhpcy5maW5kKCd0Zm9vdCA+IHRyJyk7XG5cdFx0XHQkdGFibGVfYm9keSA9ICR0aGlzLmNoaWxkcmVuKCd0Ym9keScpO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgYSBzcGVjaWFsIGNsYXNzIHRvIHRoZSB0YWJsZSwgdG8gc3R5bGVcblx0XHRcdC8vIGRpc2FibGVkIGlucHV0IGJveGVzXG5cdFx0XHQkdGhpcy5hZGRDbGFzcygndGFibGVfaW5saW5lZWRpdCcpO1xuXHRcdFx0XG5cdFx0XHQvLyBTZXQgdGhlIGRlZmF1bHQgc3RhdGUgZm9yIGFsbCB0clxuXHRcdFx0X3N3aXRjaFN0YXRlKCdkZWZhdWx0JywgJHRhYmxlX2JvZHkpO1xuXHRcdFx0Ly8gQWRkIHRoZSBcIkFkZFwiLWxpbmUgdG8gdGhlIHRhYmxlXG5cdFx0XHRfY3JlYXRlTmV3TGluZSgpO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgZXZlbnQgbGlzdGVuZXJzIGZvciBhbGwgYnV0dG9ucyBhbmRcblx0XHRcdC8vIGEgbGlzdGVuZXIgZm9yIHRoZSB3aWRnZXQgaW5pdGlhbGl6ZWQgZXZlbnRcblx0XHRcdC8vIGZyb20gd2lkZ2V0cyBpbnNpZGUgdGhlIHRhYmxlXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5yb3dfZWRpdCcsIF9lZGl0SGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljaycsICcucm93X2RlbGV0ZScsIF9kZWxldGVIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5yb3dfc2F2ZScsIF9zYXZlSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljaycsICcucm93X2FkZCcsIF9hZGRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5yb3dfYWJvcnQnLCBfYWJvcnRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ3dpZGdldC5pbml0aWFsaXplZCcsIF9pbml0aWFsaWVkSGFuZGxlcik7XG5cdFx0XHRcblx0XHRcdCQoJ2JvZHknKVxuXHRcdFx0XHQub24oJ3ZhbGlkYXRvci52YWxpZGF0ZScsIGZ1bmN0aW9uIChlLCBkKSB7XG5cdFx0XHRcdFx0aWYgKGQgJiYgZC5kZWZlcnJlZCkge1xuXHRcdFx0XHRcdFx0Ly8gRXZlbnQgbGlzdGVuZXIgdGhhdCBwZXJmb3JtcyBvbiBldmVyeSB2YWxpZGF0ZSB0cmlnZ2VyIHRoYXQgaXNuJ3QgaGFuZGxlZCBieSB0aGUgdmFsaWRhdG9yLlxuXHRcdFx0XHRcdFx0ZC5kZWZlcnJlZC5yZXNvbHZlKCk7IFxuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
