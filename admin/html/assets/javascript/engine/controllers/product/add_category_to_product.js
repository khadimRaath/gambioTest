'use strict';

/* --------------------------------------------------------------
 add_category_to_product.js 2015-10-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Adds a category dropdown to the categories box by clicking on the add button
 *
 * @module Controllers/add_category_to_product
 */
gx.controllers.module('add_category_to_product', [],

/** @lends module:Controllers/add_category_to_product */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * add category dropdown when clicking add button
  *
  * @private
  */
	var _addCategory = function _addCategory() {
		var $newCategory = $this.find('.category-template').clone().removeClass('category-template').addClass('category-link-wrapper').removeClass('hidden');

		$this.find('.category-link-wrapper:last').removeClass('remove-border').after($newCategory);

		$newCategory.find('select').prop('disabled', false).on('change', _changeCategory);
	};

	/**
  * update displayed category path on dropdown change event
  *
  * @private
  */
	var _changeCategory = function _changeCategory() {
		var level = ($(this).find('option:selected').html().match(/&nbsp;/g) || []).length;
		var categories = [];

		if (level > 0) {
			categories.unshift($(this).find('option:selected').html().replace(/&nbsp;/g, ''));
		}

		if (level > 3) {
			$(this).find('option:selected').prevAll().each(function () {
				if (($(this).html().match(/&nbsp;/g) || []).length === level - 3 && level > 3) {
					level -= 3;
					categories.unshift($(this).html().replace(/&nbsp;/g, ''));
				}
			});
		}

		$(this).parents('.category-link-wrapper').find('.category-path').html(categories.join(' > '));
	};

	/**
  * Update displayed categories list for multi select on change event.
  *
  * @private
  */
	var _changeCategoryMultiSelect = function _changeCategoryMultiSelect() {
		var level,
		    processedLevel,
		    categories = [],
		    categoryPathArray = [],
		    selected = $(this).find('option:selected'),
		    $multiSelectContainer = $('.multi-select-container').parent();

		$.each(selected, function () {
			level = ($(this).html().match(/&nbsp;/g) || []).length;
			processedLevel = level;
			if (level > 0) {
				categoryPathArray = [];
				categoryPathArray.unshift($(this).html().replace(/&nbsp;/g, ''));

				$(this).prevAll().each(function () {
					if (($(this).html().match(/&nbsp;/g) || []).length === processedLevel - 3 && processedLevel > 3) {

						processedLevel -= 3;
						categoryPathArray.unshift($(this).html().replace(/&nbsp;/g, ''));
					}
				});
				categories.push(categoryPathArray);
			}
		});

		$multiSelectContainer.empty();
		if (categories.length > 0) {
			$.each(categories, function () {
				$multiSelectContainer.append('<div class="span12 multi-select-container">' + '<label class="category-path">' + this.join(' > ') + '</label></div>');
			});
		} else {
			$multiSelectContainer.append('<div class="span12 multi-select-container"></div>');
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the widget
  */
	module.init = function (done) {
		var select = $this.find('select');
		$this.find('.add-category').on('click', _addCategory);

		if (select.prop('multiple')) {
			select.on('change', _changeCategoryMultiSelect);
			//select.on('change', _changeCategory);
		} else {
			select.on('change', _changeCategory);
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3QvYWRkX2NhdGVnb3J5X3RvX3Byb2R1Y3QuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfYWRkQ2F0ZWdvcnkiLCIkbmV3Q2F0ZWdvcnkiLCJmaW5kIiwiY2xvbmUiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiYWZ0ZXIiLCJwcm9wIiwib24iLCJfY2hhbmdlQ2F0ZWdvcnkiLCJsZXZlbCIsImh0bWwiLCJtYXRjaCIsImxlbmd0aCIsImNhdGVnb3JpZXMiLCJ1bnNoaWZ0IiwicmVwbGFjZSIsInByZXZBbGwiLCJlYWNoIiwicGFyZW50cyIsImpvaW4iLCJfY2hhbmdlQ2F0ZWdvcnlNdWx0aVNlbGVjdCIsInByb2Nlc3NlZExldmVsIiwiY2F0ZWdvcnlQYXRoQXJyYXkiLCJzZWxlY3RlZCIsIiRtdWx0aVNlbGVjdENvbnRhaW5lciIsInBhcmVudCIsInB1c2giLCJlbXB0eSIsImFwcGVuZCIsImluaXQiLCJkb25lIiwic2VsZWN0Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLHlCQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUQsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLEtBQUlPLGVBQWUsU0FBZkEsWUFBZSxHQUFXO0FBQzdCLE1B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QUNBLENBSkQsRUFJSTs7QUFFSEEsd0JBQWtCLENBQWxCO0FBQ0FDLHdCQUFrQlIsT0FBbEIsQ0FBMEJuQixFQUFFLElBQUYsRUFBUWUsSUFBUixHQUFlSyxPQUFmLENBQXVCLFNBQXZCLEVBQWtDLEVBQWxDLENBQTFCO0FBQ0E7QUFDRCxLQVZEO0FBV0FGLGVBQVdhLElBQVgsQ0FBZ0JKLGlCQUFoQjtBQUNBO0FBQ0QsR0FwQkQ7O0FBc0JBRSx3QkFBc0JHLEtBQXRCO0FBQ0EsTUFBSWQsV0FBV0QsTUFBWCxHQUFvQixDQUF4QixFQUEyQjtBQUMxQmpCLEtBQUVzQixJQUFGLENBQU9KLFVBQVAsRUFBbUIsWUFBVztBQUM3QlcsMEJBQXNCSSxNQUF0QixDQUE2QixnREFDMUIsK0JBRDBCLEdBQ1EsS0FBS1QsSUFBTCxDQUFVLEtBQVYsQ0FEUixHQUMyQixnQkFEeEQ7QUFFQSxJQUhEO0FBSUEsR0FMRCxNQUtPO0FBQ05LLHlCQUFzQkksTUFBdEIsQ0FBNkIsbURBQTdCO0FBQ0E7QUFDRCxFQXZDRDs7QUF5Q0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQXBDLFFBQU9xQyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCLE1BQUlDLFNBQVNyQyxNQUFNTyxJQUFOLENBQVcsUUFBWCxDQUFiO0FBQ0FQLFFBQU1PLElBQU4sQ0FBVyxlQUFYLEVBQTRCTSxFQUE1QixDQUErQixPQUEvQixFQUF3Q1IsWUFBeEM7O0FBRUEsTUFBSWdDLE9BQU96QixJQUFQLENBQVksVUFBWixDQUFKLEVBQTZCO0FBQzVCeUIsVUFBT3hCLEVBQVAsQ0FBVSxRQUFWLEVBQW9CYSwwQkFBcEI7QUFDQTtBQUNBLEdBSEQsTUFHTztBQUNOVyxVQUFPeEIsRUFBUCxDQUFVLFFBQVYsRUFBb0JDLGVBQXBCO0FBQ0E7O0FBRURzQjtBQUNBLEVBWkQ7O0FBY0E7QUFDQSxRQUFPdEMsTUFBUDtBQUNBLENBcEtGIiwiZmlsZSI6InByb2R1Y3QvYWRkX2NhdGVnb3J5X3RvX3Byb2R1Y3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGFkZF9jYXRlZ29yeV90b19wcm9kdWN0LmpzIDIwMTUtMTAtMDFcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEFkZHMgYSBjYXRlZ29yeSBkcm9wZG93biB0byB0aGUgY2F0ZWdvcmllcyBib3ggYnkgY2xpY2tpbmcgb24gdGhlIGFkZCBidXR0b25cbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL2FkZF9jYXRlZ29yeV90b19wcm9kdWN0XG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J2FkZF9jYXRlZ29yeV90b19wcm9kdWN0Jyxcblx0XG5cdFtdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvYWRkX2NhdGVnb3J5X3RvX3Byb2R1Y3QgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIGFkZCBjYXRlZ29yeSBkcm9wZG93biB3aGVuIGNsaWNraW5nIGFkZCBidXR0b25cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9hZGRDYXRlZ29yeSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRuZXdDYXRlZ29yeSA9ICR0aGlzLmZpbmQoJy5jYXRlZ29yeS10ZW1wbGF0ZScpXG5cdFx0XHRcdC5jbG9uZSgpXG5cdFx0XHRcdC5yZW1vdmVDbGFzcygnY2F0ZWdvcnktdGVtcGxhdGUnKVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ2NhdGVnb3J5LWxpbmstd3JhcHBlcicpXG5cdFx0XHRcdC5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLmZpbmQoJy5jYXRlZ29yeS1saW5rLXdyYXBwZXI6bGFzdCcpXG5cdFx0XHRcdC5yZW1vdmVDbGFzcygncmVtb3ZlLWJvcmRlcicpXG5cdFx0XHRcdC5hZnRlcigkbmV3Q2F0ZWdvcnkpO1xuXHRcdFx0XG5cdFx0XHQkbmV3Q2F0ZWdvcnkuZmluZCgnc2VsZWN0Jylcblx0XHRcdFx0LnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpXG5cdFx0XHRcdC5vbignY2hhbmdlJywgX2NoYW5nZUNhdGVnb3J5KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIHVwZGF0ZSBkaXNwbGF5ZWQgY2F0ZWdvcnkgcGF0aCBvbiBkcm9wZG93biBjaGFuZ2UgZXZlbnRcblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jaGFuZ2VDYXRlZ29yeSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGxldmVsID0gKCQodGhpcykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJykuaHRtbCgpLm1hdGNoKC8mbmJzcDsvZykgfHwgW10pLmxlbmd0aDtcblx0XHRcdHZhciBjYXRlZ29yaWVzID0gW107XG5cdFx0XHRcblx0XHRcdGlmIChsZXZlbCA+IDApIHtcblx0XHRcdFx0Y2F0ZWdvcmllcy51bnNoaWZ0KCQodGhpcykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJykuaHRtbCgpLnJlcGxhY2UoLyZuYnNwOy9nLCAnJykpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAobGV2ZWwgPiAzKSB7XG5cdFx0XHRcdCQodGhpcykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJykucHJldkFsbCgpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0aWYgKCgkKHRoaXMpLmh0bWwoKS5tYXRjaCgvJm5ic3A7L2cpIHx8IFtdKS5sZW5ndGggPT09IGxldmVsIC0gMyAmJiBsZXZlbCA+IDMpIHtcblx0XHRcdFx0XHRcdGxldmVsIC09IDM7XG5cdFx0XHRcdFx0XHRjYXRlZ29yaWVzLnVuc2hpZnQoJCh0aGlzKS5odG1sKCkucmVwbGFjZSgvJm5ic3A7L2csICcnKSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JCh0aGlzKS5wYXJlbnRzKCcuY2F0ZWdvcnktbGluay13cmFwcGVyJykuZmluZCgnLmNhdGVnb3J5LXBhdGgnKS5odG1sKGNhdGVnb3JpZXMuam9pbignID4gJykpO1xuXHRcdH07XG5cdFx0XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVXBkYXRlIGRpc3BsYXllZCBjYXRlZ29yaWVzIGxpc3QgZm9yIG11bHRpIHNlbGVjdCBvbiBjaGFuZ2UgZXZlbnQuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hhbmdlQ2F0ZWdvcnlNdWx0aVNlbGVjdCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGxldmVsLFxuXHRcdFx0XHRwcm9jZXNzZWRMZXZlbCxcblx0XHRcdFx0Y2F0ZWdvcmllcyA9IFtdLFxuXHRcdFx0XHRjYXRlZ29yeVBhdGhBcnJheSA9IFtdLFxuXHRcdFx0XHRzZWxlY3RlZCA9ICQodGhpcykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJyksXG5cdFx0XHRcdCRtdWx0aVNlbGVjdENvbnRhaW5lciA9ICQoJy5tdWx0aS1zZWxlY3QtY29udGFpbmVyJykucGFyZW50KCk7XG5cdFx0XHRcblx0XHRcdCQuZWFjaChzZWxlY3RlZCwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGxldmVsID0gKCQodGhpcykuaHRtbCgpLm1hdGNoKC8mbmJzcDsvZykgfHwgW10pLmxlbmd0aDtcblx0XHRcdFx0cHJvY2Vzc2VkTGV2ZWwgPSBsZXZlbDtcblx0XHRcdFx0aWYgKGxldmVsID4gMCkge1xuXHRcdFx0XHRcdGNhdGVnb3J5UGF0aEFycmF5ID0gW107XG5cdFx0XHRcdFx0Y2F0ZWdvcnlQYXRoQXJyYXkudW5zaGlmdCgkKHRoaXMpLmh0bWwoKS5yZXBsYWNlKC8mbmJzcDsvZywgJycpKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKHRoaXMpLnByZXZBbGwoKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0aWYgKCgkKHRoaXMpLmh0bWwoKS5tYXRjaCgvJm5ic3A7L2cpIHx8IFtdKS5sZW5ndGggPT09XG5cdFx0XHRcdFx0XHRcdHByb2Nlc3NlZExldmVsIC1cblx0XHRcdFx0XHRcdFx0MyAmJlxuXHRcdFx0XHRcdFx0XHRwcm9jZXNzZWRMZXZlbCA+XG5cdFx0XHRcdFx0XHRcdDMpIHtcblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdHByb2Nlc3NlZExldmVsIC09IDM7XG5cdFx0XHRcdFx0XHRcdGNhdGVnb3J5UGF0aEFycmF5LnVuc2hpZnQoJCh0aGlzKS5odG1sKCkucmVwbGFjZSgvJm5ic3A7L2csICcnKSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0Y2F0ZWdvcmllcy5wdXNoKGNhdGVnb3J5UGF0aEFycmF5KTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCRtdWx0aVNlbGVjdENvbnRhaW5lci5lbXB0eSgpO1xuXHRcdFx0aWYgKGNhdGVnb3JpZXMubGVuZ3RoID4gMCkge1xuXHRcdFx0XHQkLmVhY2goY2F0ZWdvcmllcywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JG11bHRpU2VsZWN0Q29udGFpbmVyLmFwcGVuZCgnPGRpdiBjbGFzcz1cInNwYW4xMiBtdWx0aS1zZWxlY3QtY29udGFpbmVyXCI+J1xuXHRcdFx0XHRcdFx0KyAnPGxhYmVsIGNsYXNzPVwiY2F0ZWdvcnktcGF0aFwiPicgKyB0aGlzLmpvaW4oJyA+ICcpICsgJzwvbGFiZWw+PC9kaXY+Jyk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JG11bHRpU2VsZWN0Q29udGFpbmVyLmFwcGVuZCgnPGRpdiBjbGFzcz1cInNwYW4xMiBtdWx0aS1zZWxlY3QtY29udGFpbmVyXCI+PC9kaXY+Jyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0dmFyIHNlbGVjdCA9ICR0aGlzLmZpbmQoJ3NlbGVjdCcpO1xuXHRcdFx0JHRoaXMuZmluZCgnLmFkZC1jYXRlZ29yeScpLm9uKCdjbGljaycsIF9hZGRDYXRlZ29yeSk7XG5cdFx0XHRcblx0XHRcdGlmIChzZWxlY3QucHJvcCgnbXVsdGlwbGUnKSkge1xuXHRcdFx0XHRzZWxlY3Qub24oJ2NoYW5nZScsIF9jaGFuZ2VDYXRlZ29yeU11bHRpU2VsZWN0KTtcblx0XHRcdFx0Ly9zZWxlY3Qub24oJ2NoYW5nZScsIF9jaGFuZ2VDYXRlZ29yeSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRzZWxlY3Qub24oJ2NoYW5nZScsIF9jaGFuZ2VDYXRlZ29yeSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
