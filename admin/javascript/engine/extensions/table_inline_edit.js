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
gx.extensions.module(
	'table_inline_edit',
	
	['form', 'xhr', 'fallback'],
	
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
		var _switchState = function(mode, $element, addClass) {
			
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
			$element
				.find('.table_inlineedit_alt')
				.remove();
			
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
					$others
						.prop('disabled', true)
						.each(function() {
							// Check if there is an alt text given for the input field
							var $self = $(this),
								dataset = jse.libs.fallback._data($self, 'table_inline_edit');
							
							// Replace some kind of form fields with span tags
							if ($self.attr('type') && $self.attr('type').toLowerCase() === 'checkbox' && dataset.alt) {
								var values = dataset.alt.split('_'),
									checked = $self.prop('checked');
								$self.after('<span class="table_inlineedit_alt">' + (checked ? values[0] : values[1]) +
									'</span>');
							} else if ($self.prop('tagName').toLowerCase() === 'select') {
								var waitUntilValues = function() {
									$edit.hide();
									if (!$self.children().length) {
										setTimeout(function() {
											waitUntilValues();
										}, 200);
									} else {
										$self.children('[value="' + $self.val() + '"]').text();
										$self.after(
											'<span class="table_inlineedit_alt">' +
											$self.children('[value="' + $self.val() + '"]').text() +
											'</span>'
										);
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
				$element
					.removeClass('edit add default')
					.addClass(mode);
			}
		};
		
		/**
		 * Create New Line
		 *
		 * Creates a new "add"-line by cloning the footer template.
		 */
		var _createNewLine = function() {
			var $newLine = $template.clone();
			
			$newLine
				.find('[name]')
				.each(function() {
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
		var _abortHandler = function() {
			var $tr = $(this).closest('tr'),
				cache = JSON.stringify($tr.data('formcache')),
				current = JSON.stringify(jse.libs.form.getData($tr, undefined, true)),
				deferred = $.Deferred();
			
			/**
			 * Helper function to reset a line state
			 *
			 * @private
			 */
			var _resetLine = function(e) {
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
				var
					href = 'lightbox_confirm.html?section=shop_offline&amp;' +
						'message=dicard_changes_hint&amp;buttons=cancel-discard',
					linkHtml = '<a href="' + href + '"></a>',
					lightboxLink = $(linkHtml),
					lightboxId = lightboxLink.lightbox_plugin({
						'lightbox_width': '360px'
					});
				
				$('#lightbox_package_' + lightboxId)
					.one('click', '.discard', {
						'reject': false,
						'id': lightboxId
					}, _resetLine)
					.one('click', '.cancel', {
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
		var _editHandler = function() {
			var $tr = $(this).closest('tr'),
				$edited = $this.find('tr.edit'),
				promises = [];
			
			if (!options.multiEdit && $edited.length) {
				// If multiEdit is disabled and other lines are in edit mode, wait for confirmation
				// of the abort event on the other lines.
				$edited
					.each(function() {
						promises.push(_abortHandler.call($(this).find('.row_abort').first()));
					});
			}
			
			$.when.apply(undefined, promises).promise().done(function() {
				// Store the current data of the line in cache
				$tr.data('formcache', jse.libs.form.getData($tr, undefined, true));
				_switchState('edit', $tr, true);
			});
		};
		
		/**
		 * Handler for the "save"-button
		 */
		var _saveHandler = function() {
			var $self = $(this),
				$tr = $self.closest('tr'),
				dataset = jse.libs.form.getData($tr, undefined, true),
				url = $self.data().url,
				deferred = $.Deferred();
			
			// Done callback on validation success
			deferred.done(function() {
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
			$tr.trigger('validator.validate', [
				{
					'deferred': deferred
				}
			]);
			
		};
		
		/**
		 * Handler for the "delete"-button
		 */
		var _deleteHandler = function() {
			var $self = $(this),
				$tr = $self.closest('tr'),
				dataset = {
					id: $tr.data('id')
				},
				url = $self.data().url,
				html = '<a href="lightbox_confirm.html?section=shop_offline&amp;message=delete_job'
					+ '&amp;buttons=cancel-delete"></a>',
				lightboxLink = $(html),
				lightboxId = lightboxLink.lightbox_plugin({
					'lightbox_width': '360px'
				});
			
			$('#lightbox_package_' + lightboxId).one('click', '.delete', function() {
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
		var _addHandler = function() {
			var $self = $(this),
				url = $self.data().url,
				$tr = $self.closest('tr'),
				dataset = jse.libs.form.getData($tr, undefined, true),
				deferred = $.Deferred();
			
			// Done callback on validation success
			deferred.done(function() {
				var _finalize = function() {
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
					}).done(function(result) {
						var id = result.id,
							$targets = $tr.find('input:not(:button), textarea, select');
						
						$targets.each(function() {
							var $elem = $(this),
								name = $elem
									.attr('name')
									.replace('[0]', '[' + id + ']');
							
							if ($elem.data().lightboxHref) {
								$elem.data().lightboxHref = $elem.data().lightboxHref.replace('id=0', 'id=' + id);
							}
							$elem.attr('name', name);
						});
						
						$tr.find('[data-lightbox-href]').each(function() {
							var newLink = $(this).attr('data-lightbox-href').replace('id=0', 'id=' + id);
							$(this)
								.attr('data-lightbox-href', newLink)
								.data().lightboxHref = newLink;
						});
						
						_finalize();
					});
				} else {
					_finalize();
				}
			});
			
			// Get validation state of the line. On success goto deferred.done callback
			$tr.trigger('validator.validate', [
				{
					'deferred': deferred
				}
			]);
			
		};
		
		/**
		 * Handler to update the table state, if an widget inside the table gets initialized
		 * (needed to disable the datepicker buttons).
		 *
		 * @param {object} e    jQuery event-object
		 */
		var _initialiedHandler = function(e) {
			var inside = $this
				.filter($(e.target))
				.add($this.find($(e.target)))
				.length;
			
			if (!inside) {
				var $tr = $(e.target).closest('tr'),
					type = ($tr.hasClass('edit')) ? 'edit' :
					       ($tr.hasClass('add')) ? 'add' :
					       'default';
				
				_switchState(type, $tr, true);
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function(done) {
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
			$this
				.on('click', '.row_edit', _editHandler)
				.on('click', '.row_delete', _deleteHandler)
				.on('click', '.row_save', _saveHandler)
				.on('click', '.row_add', _addHandler)
				.on('click', '.row_abort', _abortHandler)
				.on('widget.initialized', _initialiedHandler);
			
			$('body')
				.on('validator.validate', function (e, d) {
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
