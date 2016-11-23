/* --------------------------------------------------------------
 tablednd.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Table Dnd Extension
 *
 * Sorts lines in connected tables.
 *
 * @module Admin/Extensions/tablednd
 * @ignore
 */
gx.extensions.module(
	'tablednd',
	
	[],
	
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
			 * Table Body Selector
			 *
			 * @type {object}
			 */
			$tbody = null,
			
			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {
				'addclass': 'clsDnd', // classname added to body
				'disabledclass': 'sort-disabled', // classname added to body
				'handle': false // handler which enables the sortable
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
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Setup Dummies
		 */
		var setupDummies = function() {
			// On drag stop, update dummy line visibility
			$tbody.each(function() {
				var $self = $(this),
					$sortDisabled = $self.find('.' + options.disabledclass);
				
				if ($self.children().length > 1) {
					$sortDisabled.hide();
				} else {
					$sortDisabled.show();
				}
				
				var rowHidden = $sortDisabled.clone();
				$sortDisabled.remove();
				$self.prepend(rowHidden);
				
			});
		};
		
		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function(done) {
			$tbody = $this.find('tbody');
			var strTimestamp = parseInt(new Date().getTime() * Math.random(), 10),
				strClsDnd = options.addclass + '_' + strTimestamp,
				config = {
					'handle': options.handle,
					'connectWith': '.' + strClsDnd,
					'containment': $this,
					'sort': function(event, ui) {
						$(event.target).each(function() {
							var $self = $(this),
								$sortDisabled = $self.find('.' + options.disabledclass);
							
							if ($self.children().length > 2) {
								$sortDisabled.hide();
							} else {
								$sortDisabled.show();
								var rowHidden = $sortDisabled.clone();
								$sortDisabled.remove();
								$self.append(rowHidden);
							}
						});
						
					},
					'stop': function(event, ui) {
						setupDummies();
						// Trigger an update event on table
						$this.trigger('tablednd.update', []);
					}
				};
			
			// Add a special class and start the sortable plugin.
			$tbody
				.addClass(strClsDnd)
				.sortable(config);
			
			setupDummies();
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
