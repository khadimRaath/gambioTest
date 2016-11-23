'use strict';

/* --------------------------------------------------------------
 content_manager_overview_controller.js 2016-08-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.compatibility.module('content_manager_overview_controller', ['datatable'], function (data) {

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

	var _createTopBar = function _createTopBar($table) {
		var $grid = $('<div class="grid" />');

		var $search = $('<div class="span4 quick-search" />'); // Input searching  

		$search.append('<form class="control-group remove-padding">' + '<label for="search-keyword">' + jse.core.lang.translate('search', 'admin_labels') + '</label>' + '<input type="text" class="search-keyword" />' + '</form>');

		var $filter = $('<div class="span8 filter" />'); // Alphabetical filtering 
		var alphabet = [];

		$table.DataTable().data().each(function (row) {
			var letter = $(row[options.filterColumnIndex]).text().trim().substring(0, 1).toUpperCase();

			if (alphabet.indexOf(letter) === -1 && letter !== '') {
				alphabet.push(letter);
			}
		});

		alphabet.sort().forEach(function (letter) {
			$filter.append('<button class="btn btn-small">' + letter + '</button>');
		});

		$grid.append([$search, $filter]);

		$table.parent().prepend($grid);
	};

	var _createBottomBar = function _createBottomBar($table) {
		var $paginator = $('<div class="paginator grid" />');
		var $datatableComponents = $('<div class="span8 datatable-components remove-padding" />');

		var $pageLength = $('<select class="page-length" />');
		$pageLength.append(new Option('20 ' + jse.core.lang.translate('PER_PAGE', 'admin_general'), 20, true, true)).append(new Option('30 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 30).append(new Option('50 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 50).append(new Option('100 ' + jse.core.lang.translate('PER_PAGE', 'admin_general')), 100).css('float', 'left').appendTo($datatableComponents);

		$table.siblings('.dataTables_info').appendTo($datatableComponents).css('clear', 'none');

		$table.siblings('.dataTables_paginate').appendTo($datatableComponents).css('clear', 'none');

		$paginator.append('<div class="span4">&nbsp;</div>').append($datatableComponents);

		$table.parent().append($paginator);
	};

	var _onQuickSearchSubmit = function _onQuickSearchSubmit(event) {
		event.preventDefault();

		var $table = $(this).parent().siblings('table.content_manager');
		var keyword = $(this).find('.search-keyword').val();

		$table.DataTable().search(keyword, true, false).draw();
	};

	var _onFilterButtonClick = function _onFilterButtonClick() {
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

	var _onPageLengthChange = function _onPageLengthChange() {
		var $table = $(this).parents('.paginator').siblings('table.content_manager');
		$table.DataTable().page.len($(this).val()).draw();
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		var $tables = $this.find('table.content_manager');

		// Combine ".paginator" with the DataTable HTML output in order to create a unique pagination
		// frame at the bottom of the table (executed after table initialization).
		$tables.on('init.dt', function (event, settings, json) {
			var $table = $(this);
			_createTopBar($table);
			_createBottomBar($table);
		});

		jse.libs.datatable.create($tables, {
			autoWidth: false,
			dom: 'rtip',
			pageLength: 20,
			language: jse.libs.datatable.getTranslations(jse.core.config.get('languageCode')),
			createdRow: function createdRow(row, data, dataIndex) {
				$(row).find('td').each(function () {
					$(this).html($(this).html().trim().replace(/(\r\n|\n|\r)/gm, ''));
				});
			}
		});

		$this.on('submit', '.quick-search', _onQuickSearchSubmit).on('click', '.filter .btn', _onFilterButtonClick).on('change', '.page-length', _onPageLengthChange);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbnRlbnRfbWFuYWdlci9jb250ZW50X21hbmFnZXJfb3ZlcnZpZXdfY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJmaWx0ZXJDb2x1bW5JbmRleCIsImZpbHRlclJlZ2V4UHJlZml4Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9jcmVhdGVUb3BCYXIiLCIkdGFibGUiLCIkZ3JpZCIsIiRzZWFyY2giLCJhcHBlbmQiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsIiRmaWx0ZXIiLCJhbHBoYWJldCIsIkRhdGFUYWJsZSIsImVhY2giLCJyb3ciLCJsZXR0ZXIiLCJ0ZXh0IiwidHJpbSIsInN1YnN0cmluZyIsInRvVXBwZXJDYXNlIiwiaW5kZXhPZiIsInB1c2giLCJzb3J0IiwiZm9yRWFjaCIsInBhcmVudCIsInByZXBlbmQiLCJfY3JlYXRlQm90dG9tQmFyIiwiJHBhZ2luYXRvciIsIiRkYXRhdGFibGVDb21wb25lbnRzIiwiJHBhZ2VMZW5ndGgiLCJPcHRpb24iLCJjc3MiLCJhcHBlbmRUbyIsInNpYmxpbmdzIiwiX29uUXVpY2tTZWFyY2hTdWJtaXQiLCJldmVudCIsInByZXZlbnREZWZhdWx0Iiwia2V5d29yZCIsImZpbmQiLCJ2YWwiLCJzZWFyY2giLCJkcmF3IiwiX29uRmlsdGVyQnV0dG9uQ2xpY2siLCJwYXJlbnRzIiwiZXEiLCJoYXNDbGFzcyIsInJlbW92ZUNsYXNzIiwiY29sdW1uIiwiYWRkQ2xhc3MiLCJyZWdleCIsIl9vblBhZ2VMZW5ndGhDaGFuZ2UiLCJwYWdlIiwibGVuIiwiaW5pdCIsImRvbmUiLCIkdGFibGVzIiwib24iLCJzZXR0aW5ncyIsImpzb24iLCJsaWJzIiwiZGF0YXRhYmxlIiwiY3JlYXRlIiwiYXV0b1dpZHRoIiwiZG9tIiwicGFnZUxlbmd0aCIsImxhbmd1YWdlIiwiZ2V0VHJhbnNsYXRpb25zIiwiY29u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bnRfbWFuYWdlcl9vdmVydmlld19jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGNvbnRlbnRfbWFuYWdlcl9vdmVydmlld19jb250cm9sbGVyLmpzIDIwMTYtMDgtMjVcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcclxuXHQnY29udGVudF9tYW5hZ2VyX292ZXJ2aWV3X2NvbnRyb2xsZXInLCBcclxuXHRcclxuXHRbJ2RhdGF0YWJsZSddLFxyXG5cdFxyXG5cdGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcdFxyXG5cdFx0J3VzZSBzdHJpY3QnO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFxyXG5cdFx0dmFyXHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdGRlZmF1bHRzID0ge1xyXG5cdFx0XHRcdGZpbHRlckNvbHVtbkluZGV4OiAwLFxyXG5cdFx0XHRcdGZpbHRlclJlZ2V4UHJlZml4OiAnXidcclxuXHRcdFx0fSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XHJcblx0XHRcdCAqL1xyXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XHJcblx0XHRcdCAqL1xyXG5cdFx0XHRtb2R1bGUgPSB7fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBGVU5DVElPTlNcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHR2YXIgX2NyZWF0ZVRvcEJhciA9IGZ1bmN0aW9uKCR0YWJsZSkge1xyXG5cdFx0XHR2YXIgJGdyaWQgPSAkKCc8ZGl2IGNsYXNzPVwiZ3JpZFwiIC8+Jyk7IFxyXG5cdFx0XHRcclxuXHRcdFx0dmFyICRzZWFyY2ggPSAkKCc8ZGl2IGNsYXNzPVwic3BhbjQgcXVpY2stc2VhcmNoXCIgLz4nKTsgLy8gSW5wdXQgc2VhcmNoaW5nICBcclxuXHRcdFx0XHJcblx0XHRcdCRzZWFyY2guYXBwZW5kKFxyXG5cdFx0XHRcdCc8Zm9ybSBjbGFzcz1cImNvbnRyb2wtZ3JvdXAgcmVtb3ZlLXBhZGRpbmdcIj4nICtcclxuXHRcdFx0XHRcdCc8bGFiZWwgZm9yPVwic2VhcmNoLWtleXdvcmRcIj4nICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3NlYXJjaCcsICdhZG1pbl9sYWJlbHMnKSArICc8L2xhYmVsPicgKyBcclxuXHRcdFx0XHRcdCc8aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cInNlYXJjaC1rZXl3b3JkXCIgLz4nICsgXHJcblx0XHRcdFx0JzwvZm9ybT4nXHJcblx0XHRcdCk7XHJcblx0XHRcdFxyXG5cdFx0XHR2YXIgJGZpbHRlciA9ICQoJzxkaXYgY2xhc3M9XCJzcGFuOCBmaWx0ZXJcIiAvPicpOyAvLyBBbHBoYWJldGljYWwgZmlsdGVyaW5nIFxyXG5cdFx0XHR2YXIgYWxwaGFiZXQgPSBbXTsgXHJcblx0XHRcdFxyXG5cdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuZGF0YSgpLmVhY2goZnVuY3Rpb24ocm93KSB7XHJcblx0XHRcdFx0dmFyIGxldHRlciA9ICQocm93W29wdGlvbnMuZmlsdGVyQ29sdW1uSW5kZXhdKS50ZXh0KCkudHJpbSgpLnN1YnN0cmluZygwLCAxKS50b1VwcGVyQ2FzZSgpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmIChhbHBoYWJldC5pbmRleE9mKGxldHRlcikgPT09IC0xICYmIGxldHRlciAhPT0gJycpIHtcclxuXHRcdFx0XHRcdGFscGhhYmV0LnB1c2gobGV0dGVyKTsgXHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9KTsgXHJcblx0XHRcdFxyXG5cdFx0XHRhbHBoYWJldC5zb3J0KCkuZm9yRWFjaChmdW5jdGlvbihsZXR0ZXIpIHtcclxuXHRcdFx0XHQkZmlsdGVyLmFwcGVuZCgnPGJ1dHRvbiBjbGFzcz1cImJ0biBidG4tc21hbGxcIj4nICsgbGV0dGVyICsgJzwvYnV0dG9uPicpOyBcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQkZ3JpZC5hcHBlbmQoWyRzZWFyY2gsICRmaWx0ZXJdKTtcclxuXHRcdFx0XHJcblx0XHRcdCR0YWJsZS5wYXJlbnQoKS5wcmVwZW5kKCRncmlkKTtcclxuXHRcdH07IFxyXG5cdFx0XHJcblx0XHR2YXIgX2NyZWF0ZUJvdHRvbUJhciA9IGZ1bmN0aW9uKCR0YWJsZSkge1xyXG5cdFx0XHR2YXIgJHBhZ2luYXRvciA9ICQoJzxkaXYgY2xhc3M9XCJwYWdpbmF0b3IgZ3JpZFwiIC8+Jyk7XHJcblx0XHRcdHZhciAkZGF0YXRhYmxlQ29tcG9uZW50cyA9ICQoJzxkaXYgY2xhc3M9XCJzcGFuOCBkYXRhdGFibGUtY29tcG9uZW50cyByZW1vdmUtcGFkZGluZ1wiIC8+Jyk7XHJcblx0XHRcdFxyXG5cdFx0XHR2YXIgJHBhZ2VMZW5ndGggPSAkKCc8c2VsZWN0IGNsYXNzPVwicGFnZS1sZW5ndGhcIiAvPicpO1xyXG5cdFx0XHQkcGFnZUxlbmd0aFxyXG5cdFx0XHRcdC5hcHBlbmQobmV3IE9wdGlvbignMjAgJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdQRVJfUEFHRScsICdhZG1pbl9nZW5lcmFsJyksIDIwLCB0cnVlLCB0cnVlKSlcclxuXHRcdFx0XHQuYXBwZW5kKG5ldyBPcHRpb24oJzMwICcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnUEVSX1BBR0UnLCAnYWRtaW5fZ2VuZXJhbCcpKSwgMzApXHJcblx0XHRcdFx0LmFwcGVuZChuZXcgT3B0aW9uKCc1MCAnICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1BFUl9QQUdFJywgJ2FkbWluX2dlbmVyYWwnKSksIDUwKVxyXG5cdFx0XHRcdC5hcHBlbmQobmV3IE9wdGlvbignMTAwICcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnUEVSX1BBR0UnLCAnYWRtaW5fZ2VuZXJhbCcpKSwgMTAwKVxyXG5cdFx0XHRcdC5jc3MoJ2Zsb2F0JywgJ2xlZnQnKVxyXG5cdFx0XHRcdC5hcHBlbmRUbygkZGF0YXRhYmxlQ29tcG9uZW50cyk7IFxyXG5cdFx0XHRcclxuXHRcdFx0JHRhYmxlLnNpYmxpbmdzKCcuZGF0YVRhYmxlc19pbmZvJylcclxuXHRcdFx0XHQuYXBwZW5kVG8oJGRhdGF0YWJsZUNvbXBvbmVudHMpXHJcblx0XHRcdFx0LmNzcygnY2xlYXInLCAnbm9uZScpO1xyXG5cdFx0XHRcclxuXHRcdFx0JHRhYmxlLnNpYmxpbmdzKCcuZGF0YVRhYmxlc19wYWdpbmF0ZScpXHJcblx0XHRcdFx0LmFwcGVuZFRvKCRkYXRhdGFibGVDb21wb25lbnRzKVxyXG5cdFx0XHRcdC5jc3MoJ2NsZWFyJywgJ25vbmUnKTtcclxuXHRcdFx0XHJcblx0XHRcdCRwYWdpbmF0b3JcclxuXHRcdFx0XHQuYXBwZW5kKCc8ZGl2IGNsYXNzPVwic3BhbjRcIj4mbmJzcDs8L2Rpdj4nKVxyXG5cdFx0XHRcdC5hcHBlbmQoJGRhdGF0YWJsZUNvbXBvbmVudHMpOyBcclxuXHRcdFx0XHJcblx0XHRcdCR0YWJsZS5wYXJlbnQoKS5hcHBlbmQoJHBhZ2luYXRvcik7IFxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0dmFyIF9vblF1aWNrU2VhcmNoU3VibWl0ID0gZnVuY3Rpb24oZXZlbnQpIHtcclxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0XHJcblx0XHRcdHZhciAkdGFibGUgPSAkKHRoaXMpLnBhcmVudCgpLnNpYmxpbmdzKCd0YWJsZS5jb250ZW50X21hbmFnZXInKTtcclxuXHRcdFx0dmFyIGtleXdvcmQgPSAkKHRoaXMpLmZpbmQoJy5zZWFyY2gta2V5d29yZCcpLnZhbCgpO1xyXG5cdFx0XHRcclxuXHRcdFx0JHRhYmxlLkRhdGFUYWJsZSgpLnNlYXJjaChrZXl3b3JkLCB0cnVlLCBmYWxzZSkuZHJhdygpO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0dmFyIF9vbkZpbHRlckJ1dHRvbkNsaWNrID0gZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciAkdGFibGUgPSAkKHRoaXMpLnBhcmVudHMoKS5lcSgxKS5zaWJsaW5ncygndGFibGUuY29udGVudF9tYW5hZ2VyJyk7XHJcblx0XHRcdFxyXG5cdFx0XHRpZiAoJCh0aGlzKS5oYXNDbGFzcygnYnRuLXByaW1hcnknKSkge1xyXG5cdFx0XHRcdCQodGhpcykucmVtb3ZlQ2xhc3MoJ2J0bi1wcmltYXJ5Jyk7XHJcblx0XHRcdFx0JHRhYmxlLkRhdGFUYWJsZSgpLmNvbHVtbihvcHRpb25zLmZpbHRlckNvbHVtbkluZGV4KS5zZWFyY2goJycpLmRyYXcoKTtcclxuXHRcdFx0XHRyZXR1cm47IFxyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQkKHRoaXMpLnNpYmxpbmdzKCcuYnRuLXByaW1hcnknKS5yZW1vdmVDbGFzcygnYnRuLXByaW1hcnknKTsgXHJcblx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ2J0bi1wcmltYXJ5Jyk7IFxyXG5cdFx0XHRcclxuXHRcdFx0dmFyIHJlZ2V4ID0gb3B0aW9ucy5maWx0ZXJSZWdleFByZWZpeCArICQodGhpcykudGV4dCgpICsgJy4qJCc7IFxyXG5cdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuY29sdW1uKG9wdGlvbnMuZmlsdGVyQ29sdW1uSW5kZXgpLnNlYXJjaChyZWdleCwgdHJ1ZSwgZmFsc2UpLmRyYXcoKTtcclxuXHRcdH07XHJcblx0XHRcclxuXHRcdHZhciBfb25QYWdlTGVuZ3RoQ2hhbmdlID0gZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciAkdGFibGUgPSAkKHRoaXMpLnBhcmVudHMoJy5wYWdpbmF0b3InKS5zaWJsaW5ncygndGFibGUuY29udGVudF9tYW5hZ2VyJyk7IFxyXG5cdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkucGFnZS5sZW4oJCh0aGlzKS52YWwoKSkuZHJhdygpO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0XHR2YXIgJHRhYmxlcyA9ICR0aGlzLmZpbmQoJ3RhYmxlLmNvbnRlbnRfbWFuYWdlcicpO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gQ29tYmluZSBcIi5wYWdpbmF0b3JcIiB3aXRoIHRoZSBEYXRhVGFibGUgSFRNTCBvdXRwdXQgaW4gb3JkZXIgdG8gY3JlYXRlIGEgdW5pcXVlIHBhZ2luYXRpb25cclxuXHRcdFx0Ly8gZnJhbWUgYXQgdGhlIGJvdHRvbSBvZiB0aGUgdGFibGUgKGV4ZWN1dGVkIGFmdGVyIHRhYmxlIGluaXRpYWxpemF0aW9uKS5cclxuXHRcdFx0JHRhYmxlcy5vbignaW5pdC5kdCcsIGZ1bmN0aW9uKGV2ZW50LCBzZXR0aW5ncywganNvbikge1xyXG5cdFx0XHRcdHZhciAkdGFibGUgPSAkKHRoaXMpO1xyXG5cdFx0XHRcdF9jcmVhdGVUb3BCYXIoJHRhYmxlKTtcclxuXHRcdFx0XHRfY3JlYXRlQm90dG9tQmFyKCR0YWJsZSk7XHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0anNlLmxpYnMuZGF0YXRhYmxlLmNyZWF0ZSgkdGFibGVzLCB7XHJcblx0XHRcdFx0YXV0b1dpZHRoOiBmYWxzZSxcclxuXHRcdFx0XHRkb206ICdydGlwJyxcclxuXHRcdFx0XHRwYWdlTGVuZ3RoOiAyMCxcclxuXHRcdFx0XHRsYW5ndWFnZToganNlLmxpYnMuZGF0YXRhYmxlLmdldFRyYW5zbGF0aW9ucyhqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSksXHJcblx0XHRcdFx0Y3JlYXRlZFJvdzogZnVuY3Rpb24ocm93LCBkYXRhLCBkYXRhSW5kZXgpIHtcclxuXHRcdFx0XHRcdCQocm93KS5maW5kKCd0ZCcpLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0XHRcdCQodGhpcykuaHRtbCgkKHRoaXMpLmh0bWwoKS50cmltKCkucmVwbGFjZSgvKFxcclxcbnxcXG58XFxyKS9nbSwnJykpO1xyXG5cdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzXHJcblx0XHRcdFx0Lm9uKCdzdWJtaXQnLCAnLnF1aWNrLXNlYXJjaCcsIF9vblF1aWNrU2VhcmNoU3VibWl0KVxyXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLmZpbHRlciAuYnRuJywgX29uRmlsdGVyQnV0dG9uQ2xpY2spXHJcblx0XHRcdFx0Lm9uKCdjaGFuZ2UnLCAnLnBhZ2UtbGVuZ3RoJywgX29uUGFnZUxlbmd0aENoYW5nZSk7XHJcblx0XHRcdFxyXG5cdFx0XHRkb25lKCk7XHJcblx0XHR9OyBcclxuXHRcdFx0XHJcblx0XHRyZXR1cm4gbW9kdWxlOyBcclxuXHR9KTsiXX0=
