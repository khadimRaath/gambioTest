/* --------------------------------------------------------------
 content_manager_overview_controller.js 2016-08-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.compatibility.module(
	'content_manager_overview_controller', 
	
	['datatable'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				filterColumnIndex: 0,
				filterRegexPrefix: '^'
			},
			
			/**
			 * Final Options
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
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		var _createTopBar = function($table) {
			var $grid = $('<div class="grid" />'); 
			
			var $search = $('<div class="span4 quick-search" />'); // Input searching  
			
			$search.append(
				'<form class="control-group remove-padding">' +
					'<label for="search-keyword">' + jse.core.lang.translate('search', 'admin_labels') + '</label>' + 
					'<input type="text" class="search-keyword" />' + 
				'</form>'
			);
			
			var $filter = $('<div class="span8 filter" />'); // Alphabetical filtering 
			var alphabet = []; 
			
			$table.DataTable().data().each(function(row) {
				var letter = $(row[options.filterColumnIndex]).text().trim().substring(0, 1).toUpperCase();
				
				if (alphabet.indexOf(letter) === -1 && letter !== '') {
					alphabet.push(letter); 
				}
			}); 
			
			alphabet.sort().forEach(function(letter) {
				$filter.append('<button class="btn btn-small">' + letter + '</button>'); 
			});
			
			$grid.append([$search, $filter]);
			
			$table.parent().prepend($grid);
		}; 
		
		var _createBottomBar = function($table) {
			var $paginator = $('<div class="paginator grid" />');
			var $datatableComponents = $('<div class="span8 datatable-components remove-padding" />');
			
			var $pageLength = $('<select class="page-length" />');
			$pageLength
				.append(new Option('20 ' + jse.core.lang.translate('PER_PAGE', 'admin_general'), 20, true, true))
				.append(new Option('30 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 30)
				.append(new Option('50 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 50)
				.append(new Option('100 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 100)
				.css('float', 'left')
				.appendTo($datatableComponents); 
			
			$table.siblings('.dataTables_info')
				.appendTo($datatableComponents)
				.css('clear', 'none');
			
			$table.siblings('.dataTables_paginate')
				.appendTo($datatableComponents)
				.css('clear', 'none');
			
			$paginator
				.append('<div class="span4">&nbsp;</div>')
				.append($datatableComponents); 
			
			$table.parent().append($paginator); 
		};
		
		var _onQuickSearchSubmit = function(event) {
			event.preventDefault();
			
			var $table = $(this).parent().siblings('table.content_manager');
			var keyword = $(this).find('.search-keyword').val();
			
			$table.DataTable().search(keyword, true, false).draw();
		};
		
		var _onFilterButtonClick = function() {
			var $table = $(this).parents().eq(1).siblings('table.content_manager');
			
			if ($(this).hasClass('btn-primary')) {
				$(this).removeClass('btn-primary');
				$table.DataTable().column(options.filterColumnIndex).search('').draw();
				return; 
			}
			
			$(this).siblings('.btn-primary').removeClass('btn-primary'); 
			$(this).addClass('btn-primary'); 
			
			var regex = options.filterRegexPrefix + $(this).text() + '.*$'; 
			$table.DataTable().column(options.filterColumnIndex).search(regex, true, false).draw();
		};
		
		var _onPageLengthChange = function() {
			var $table = $(this).parents('.paginator').siblings('table.content_manager'); 
			$table.DataTable().page.len($(this).val()).draw();
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			var $tables = $this.find('table.content_manager');
			
			// Combine ".paginator" with the DataTable HTML output in order to create a unique pagination
			// frame at the bottom of the table (executed after table initialization).
			$tables.on('init.dt', function(event, settings, json) {
				var $table = $(this);
				_createTopBar($table);
				_createBottomBar($table);
			});
			
			jse.libs.datatable.create($tables, {
				autoWidth: false,
				dom: 'rtip',
				pageLength: 20,
				language: jse.libs.datatable.getTranslations(jse.core.config.get('languageCode')),
				createdRow: function(row, data, dataIndex) {
					$(row).find('td').each(function() {
						$(this).html($(this).html().trim().replace(/(\r\n|\n|\r)/gm,''));
					});
				}
			});
			
			$this
				.on('submit', '.quick-search', _onQuickSearchSubmit)
				.on('click', '.filter .btn', _onFilterButtonClick)
				.on('change', '.page-length', _onPageLengthChange);
			
			done();
		}; 
			
		return module; 
	});