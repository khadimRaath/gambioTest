'use strict';

/* --------------------------------------------------------------
 menu.js 2016-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Main Menu Controller
 *
 * Whenever the user clicks on a menu item the browser must be redirected to the respective location.
 *
 * Middle button clicks are also supported.
 */
gx.controllers.module('menu', ['user_configuration_service'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	/**
  * Menu List Selector
  *
  * @type {Object}
  */
	var $list = $this.children('ul');

	/**
  * Sub-list Selector
  *
  * This object is used for correctly displaying the sub-list <ul> element whenever it goes out
  * of viewport.
  *
  * @type {jQuery}
  */
	var $sublist = null;

	/**
  * Favorites Box
  *
  * @type {Object}
  */
	var $favoritesMenu = $this.find('ul li').first().find('ul');

	/**
  * Draggable Menu Items
  *
  * @type {Object}
  */
	var $draggableMenuItems = $this.find('.fav-drag-item');

	/**
  * Dropzone Box
  *
  * The draggable elements will be placed here.
  *
  * @type {Object}
  */
	var favDropzoneBox = '#fav-dropzone-box';

	/**
  * Drag and drop flag to prevent the default action of a menu item when it is dragged.
  *
  * @type {Boolean} True while am item is dragged.
  */
	var onDragAndDrop = false;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Repositions the sub-list when it goes off the viewport.
  */
	function _onMenuItemMouseEnter() {
		if ($list.hasClass('expand-all')) {
			return; // Do not check for viewport in "expand-all" state.
		}

		$sublist = $(this).children('ul');

		if ($sublist.length === 0) {
			return;
		}

		$sublist.offset = $sublist.offset().top + $sublist.height();

		if ($sublist.offset > window.innerHeight) {
			$sublist.css('margin-top', -1 * ($sublist.offset - window.innerHeight + $('#main-header').outerHeight() + $('#main-footer .info').outerHeight()) + 'px').addClass('stay-within-viewport');
		}
	}

	/**
  * Remove the helper class "stay-within-viewport" and reset the "margin-top" value.
  */
	function _onMenuItemMouseLeave() {
		if ($list.hasClass('expand-all')) {
			return; // Do not check for viewport in "expand-all" state.
		}

		$sublist.css('margin-top', '').removeClass('stay-within-viewport');
	}

	/**
  * Makes all menu items draggable.
  *
  * This function should be executed in the module.init() method.
  *
  * @param {Object} $draggableMenuItems Menu item jQuery selector.
  */
	function _makeMenuItemsDraggable($draggableMenuItems) {
		$draggableMenuItems.draggable({
			helper: 'clone', // Clone the element, don't move the element itself.
			start: function start(event, ui) {
				onDragAndDrop = true;
				ui.helper.addClass('currentlyDragged');
				_createFavoritesDropzone(this);
			},
			stop: function stop(event) {
				onDragAndDrop = false;
				$(favDropzoneBox).remove();
			}
		});
	}

	/**
  * Creates the favorites box, where the draggable items can be dropped on.
  *
  * @param {HTMLElement} draggedElement Dragged menu item.
  */
	function _createFavoritesDropzone(draggedElement) {
		var dropzoneBox = '';
		var action = '';

		if ($(draggedElement).parents('li').find('.fa-heart').length === 0) {
			dropzoneBox = '\n\t\t\t\t<div id="fav-dropzone-box" class="fav-add">\n\t\t\t\t\t<i class="fa fa-heart"></i>\n\t\t\t\t</div>\n\t\t\t';
			action = 'save';
		} else {
			dropzoneBox = '\n\t\t\t\t<div id="fav-dropzone-box" class="fav-delete">\n\t\t\t\t\t<i class="fa fa-trash"></i>\n\t\t\t\t</div>\n\t\t\t';
			action = 'delete';
		}

		_positionDropzoneBox(dropzoneBox, draggedElement);

		$(favDropzoneBox).droppable(_getObjectFromAction(action, draggedElement));
	}

	/**
  * Stores the menu item as a favorite in the database.
  *
  * @param {String} linkKey Unique link key from the menu item.
  * @param {Object} draggedElement Dragged menu item.
  */
	function _saveToFavorites(linkKey, draggedElement) {
		$.ajax({
			url: 'admin.php?do=AdminFavoritesAjax/AddMenuItem&link_key=' + linkKey,
			error: function error(_error) {
				console.error('Could not save the menu item with the link key: ' + linkKey);
			},
			success: function success() {
				if (!_isLinkKeyInFavorites(linkKey)) {
					var $newLink = $(draggedElement).clone().addClass('fav-drag-item');
					var $newListItem = $('<li/>').append($newLink);
					$favoritesMenu.append($newListItem);
					_makeMenuItemsDraggable($newListItem.find('.fav-drag-item'));
				}
			}
		});
	}

	/**
  * Deletes the menu item as a favorite from the database.
  *
  * @param {String} linkKey Unique link key from the menu item.
  * @param {Object} draggedElement Dragged menu item.
  */
	function _deleteFromFavorites(linkKey, draggedElement) {
		$.ajax({
			url: 'admin.php?do=AdminFavoritesAjax/RemoveMenuItem&link_key=' + linkKey,
			error: function error(_error2) {
				console.error('Could not remove the menu item with the link key: ' + linkKey);
			},
			success: function success() {
				$(draggedElement).parent('li').remove();
			}
		});
	}

	/**
  * Checks if a menu item is already stored in the favorites menu.
  *
  * @param {String} linkKey Unique link key of a menu item.
  *
  * @return {Boolean} True if menu item is already stored, else false will be returned.
  */
	function _isLinkKeyInFavorites(linkKey) {
		return $favoritesMenu.find('#' + linkKey).length !== 0;
	}

	/**
  * Get jQueryUI droppable options object
  *
  * @param {String} action Action to execute value=save|delete.
  * @param {Object} draggedElement Dragged meu item.
  *
  * @return {Object} jQueryUI droppable options.
  */
	function _getObjectFromAction(action, draggedElement) {
		var droppableOptions = {
			accept: '.fav-drag-item',
			tolerance: 'pointer',
			// Function when hovering over the favorites box.
			over: function over() {
				$(favDropzoneBox).css('opacity', '1.0');
			},
			// Function when hovering out from the favorites box.
			out: function out() {
				$(favDropzoneBox).css('opacity', '0.9');
			},
			// Function when dropping an element on the favorites box.
			drop: function drop(event, ui) {
				var linkKey = $(ui.draggable).attr('id');

				if (action === 'save') {
					_saveToFavorites(linkKey, draggedElement);
				} else if (action === 'delete') {
					_deleteFromFavorites(linkKey, draggedElement);
				}
			}
		};

		return droppableOptions;
	}

	/**
  * Positions the DropzoneBox at the correct place.
  *
  * @param {String} dropzoneBox DropzoneBox HTML.
  * @param {Object} draggedElement Dragged menu item.
  */
	function _positionDropzoneBox(dropzoneBox, draggedElement) {
		var $dropzoneBox = $(dropzoneBox);

		$(draggedElement).parent('li').prepend($dropzoneBox);

		var dropzoneBoxHeight = $dropzoneBox.outerHeight();

		$dropzoneBox.css({
			top: $(draggedElement).position().top - dropzoneBoxHeight / 2
		});
	}

	/**
  * Open the active menu group.
  *
  * This method will find the menu item that contains the same "do" GET parameter and set the "active"
  * class to its parent.
  */
	function _toggleActiveMenuGroup() {
		var currentUrlParameters = $.deparam(window.location.search.slice(1));

		$list.find('li:gt(1) a').each(function (index, link) {
			var linkUrlParameters = $.deparam($(link).attr('href').replace(/.*(\?)/, '$1').slice(1));

			if (linkUrlParameters.do === currentUrlParameters.do) {
				$(link).parents('li:lt(2)').addClass('active');
				return false;
			}
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$list.children('li').on('mouseenter', _onMenuItemMouseEnter).on('mouseleave', _onMenuItemMouseLeave);

		_makeMenuItemsDraggable($draggableMenuItems);
		_toggleActiveMenuGroup();

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9tZW51L21lbnUuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkbGlzdCIsImNoaWxkcmVuIiwiJHN1Ymxpc3QiLCIkZmF2b3JpdGVzTWVudSIsImZpbmQiLCJmaXJzdCIsIiRkcmFnZ2FibGVNZW51SXRlbXMiLCJmYXZEcm9wem9uZUJveCIsIm9uRHJhZ0FuZERyb3AiLCJfb25NZW51SXRlbU1vdXNlRW50ZXIiLCJoYXNDbGFzcyIsImxlbmd0aCIsIm9mZnNldCIsInRvcCIsImhlaWdodCIsIndpbmRvdyIsImlubmVySGVpZ2h0IiwiY3NzIiwib3V0ZXJIZWlnaHQiLCJhZGRDbGFzcyIsIl9vbk1lbnVJdGVtTW91c2VMZWF2ZSIsInJlbW92ZUNsYXNzIiwiX21ha2VNZW51SXRlbXNEcmFnZ2FibGUiLCJkcmFnZ2FibGUiLCJoZWxwZXIiLCJzdGFydCIsImV2ZW50IiwidWkiLCJfY3JlYXRlRmF2b3JpdGVzRHJvcHpvbmUiLCJzdG9wIiwicmVtb3ZlIiwiZHJhZ2dlZEVsZW1lbnQiLCJkcm9wem9uZUJveCIsImFjdGlvbiIsInBhcmVudHMiLCJfcG9zaXRpb25Ecm9wem9uZUJveCIsImRyb3BwYWJsZSIsIl9nZXRPYmplY3RGcm9tQWN0aW9uIiwiX3NhdmVUb0Zhdm9yaXRlcyIsImxpbmtLZXkiLCJhamF4IiwidXJsIiwiZXJyb3IiLCJjb25zb2xlIiwic3VjY2VzcyIsIl9pc0xpbmtLZXlJbkZhdm9yaXRlcyIsIiRuZXdMaW5rIiwiY2xvbmUiLCIkbmV3TGlzdEl0ZW0iLCJhcHBlbmQiLCJfZGVsZXRlRnJvbUZhdm9yaXRlcyIsInBhcmVudCIsImRyb3BwYWJsZU9wdGlvbnMiLCJhY2NlcHQiLCJ0b2xlcmFuY2UiLCJvdmVyIiwib3V0IiwiZHJvcCIsImF0dHIiLCIkZHJvcHpvbmVCb3giLCJwcmVwZW5kIiwiZHJvcHpvbmVCb3hIZWlnaHQiLCJwb3NpdGlvbiIsIl90b2dnbGVBY3RpdmVNZW51R3JvdXAiLCJjdXJyZW50VXJsUGFyYW1ldGVycyIsImRlcGFyYW0iLCJsb2NhdGlvbiIsInNlYXJjaCIsInNsaWNlIiwiZWFjaCIsImluZGV4IiwibGluayIsImxpbmtVcmxQYXJhbWV0ZXJzIiwicmVwbGFjZSIsImRvIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixNQUF0QixFQUE4QixDQUFDLDRCQUFELENBQTlCLEVBQThELFVBQVNDLElBQVQsRUFBZTs7QUFFNUU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTLEVBQWY7O0FBRUE7Ozs7O0FBS0EsS0FBTUksUUFBUUYsTUFBTUcsUUFBTixDQUFlLElBQWYsQ0FBZDs7QUFFQTs7Ozs7Ozs7QUFRQSxLQUFJQyxXQUFXLElBQWY7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsaUJBQWlCTCxNQUFNTSxJQUFOLENBQVcsT0FBWCxFQUFvQkMsS0FBcEIsR0FBNEJELElBQTVCLENBQWlDLElBQWpDLENBQXZCOztBQUVBOzs7OztBQUtBLEtBQU1FLHNCQUFzQlIsTUFBTU0sSUFBTixDQUFXLGdCQUFYLENBQTVCOztBQUVBOzs7Ozs7O0FBT0EsS0FBTUcsaUJBQWlCLG1CQUF2Qjs7QUFFQTs7Ozs7QUFLQSxLQUFJQyxnQkFBZ0IsS0FBcEI7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQSxVQUFTQyxxQkFBVCxHQUFpQztBQUNoQyxNQUFJVCxNQUFNVSxRQUFOLENBQWUsWUFBZixDQUFKLEVBQWtDO0FBQ2pDLFVBRGlDLENBQ3pCO0FBQ1I7O0FBRURSLGFBQVdILEVBQUUsSUFBRixFQUFRRSxRQUFSLENBQWlCLElBQWpCLENBQVg7O0FBRUEsTUFBSUMsU0FBU1MsTUFBVCxLQUFvQixDQUF4QixFQUEyQjtBQUMxQjtBQUNBOztBQUVEVCxXQUFTVSxNQUFULEdBQWtCVixTQUFTVSxNQUFULEdBQWtCQyxHQUFsQixHQUF3QlgsU0FBU1ksTUFBVCxFQUExQzs7QUFFQSxNQUFJWixTQUFTVSxNQUFULEdBQWtCRyxPQUFPQyxXQUE3QixFQUEwQztBQUN6Q2QsWUFDRWUsR0FERixDQUNNLFlBRE4sRUFDb0IsQ0FBQyxDQUFELElBQU1mLFNBQVNVLE1BQVQsR0FBa0JHLE9BQU9DLFdBQXpCLEdBQXVDakIsRUFBRSxjQUFGLEVBQWtCbUIsV0FBbEIsRUFBdkMsR0FDdEJuQixFQUFFLG9CQUFGLEVBQXdCbUIsV0FBeEIsRUFEZ0IsSUFDeUIsSUFGN0MsRUFHRUMsUUFIRixDQUdXLHNCQUhYO0FBSUE7QUFDRDs7QUFFRDs7O0FBR0EsVUFBU0MscUJBQVQsR0FBaUM7QUFDaEMsTUFBSXBCLE1BQU1VLFFBQU4sQ0FBZSxZQUFmLENBQUosRUFBa0M7QUFDakMsVUFEaUMsQ0FDekI7QUFDUjs7QUFFRFIsV0FDRWUsR0FERixDQUNNLFlBRE4sRUFDb0IsRUFEcEIsRUFFRUksV0FGRixDQUVjLHNCQUZkO0FBR0E7O0FBRUQ7Ozs7Ozs7QUFPQSxVQUFTQyx1QkFBVCxDQUFpQ2hCLG1CQUFqQyxFQUFzRDtBQUNyREEsc0JBQW9CaUIsU0FBcEIsQ0FBOEI7QUFDN0JDLFdBQVEsT0FEcUIsRUFDWjtBQUNqQkMsVUFBTyxlQUFTQyxLQUFULEVBQWdCQyxFQUFoQixFQUFvQjtBQUMxQm5CLG9CQUFnQixJQUFoQjtBQUNBbUIsT0FBR0gsTUFBSCxDQUFVTCxRQUFWLENBQW1CLGtCQUFuQjtBQUNBUyw2QkFBeUIsSUFBekI7QUFDQSxJQU40QjtBQU83QkMsU0FBTSxjQUFTSCxLQUFULEVBQWdCO0FBQ3JCbEIsb0JBQWdCLEtBQWhCO0FBQ0FULE1BQUVRLGNBQUYsRUFBa0J1QixNQUFsQjtBQUNBO0FBVjRCLEdBQTlCO0FBWUE7O0FBRUQ7Ozs7O0FBS0EsVUFBU0Ysd0JBQVQsQ0FBa0NHLGNBQWxDLEVBQWtEO0FBQ2pELE1BQUlDLGNBQWMsRUFBbEI7QUFDQSxNQUFJQyxTQUFTLEVBQWI7O0FBRUEsTUFBSWxDLEVBQUVnQyxjQUFGLEVBQWtCRyxPQUFsQixDQUEwQixJQUExQixFQUFnQzlCLElBQWhDLENBQXFDLFdBQXJDLEVBQWtETyxNQUFsRCxLQUE2RCxDQUFqRSxFQUFvRTtBQUNuRXFCO0FBS0FDLFlBQVMsTUFBVDtBQUNBLEdBUEQsTUFPTztBQUNORDtBQUtBQyxZQUFTLFFBQVQ7QUFDQTs7QUFFREUsdUJBQXFCSCxXQUFyQixFQUFrQ0QsY0FBbEM7O0FBRUFoQyxJQUFFUSxjQUFGLEVBQWtCNkIsU0FBbEIsQ0FBNEJDLHFCQUFxQkosTUFBckIsRUFBNkJGLGNBQTdCLENBQTVCO0FBQ0E7O0FBRUQ7Ozs7OztBQU1BLFVBQVNPLGdCQUFULENBQTBCQyxPQUExQixFQUFtQ1IsY0FBbkMsRUFBbUQ7QUFDbERoQyxJQUFFeUMsSUFBRixDQUFPO0FBQ05DLFFBQUssMERBQTBERixPQUR6RDtBQUVORyxVQUFPLGVBQVNBLE1BQVQsRUFBZ0I7QUFDdEJDLFlBQVFELEtBQVIsQ0FBYyxxREFBcURILE9BQW5FO0FBQ0EsSUFKSztBQUtOSyxZQUFTLG1CQUFXO0FBQ25CLFFBQUksQ0FBQ0Msc0JBQXNCTixPQUF0QixDQUFMLEVBQXFDO0FBQ3BDLFNBQU1PLFdBQVcvQyxFQUFFZ0MsY0FBRixFQUFrQmdCLEtBQWxCLEdBQTBCNUIsUUFBMUIsQ0FBbUMsZUFBbkMsQ0FBakI7QUFDQSxTQUFNNkIsZUFBZWpELEVBQUUsT0FBRixFQUFXa0QsTUFBWCxDQUFrQkgsUUFBbEIsQ0FBckI7QUFDQTNDLG9CQUFlOEMsTUFBZixDQUFzQkQsWUFBdEI7QUFDQTFCLDZCQUF3QjBCLGFBQWE1QyxJQUFiLENBQWtCLGdCQUFsQixDQUF4QjtBQUNBO0FBQ0Q7QUFaSyxHQUFQO0FBY0E7O0FBRUQ7Ozs7OztBQU1BLFVBQVM4QyxvQkFBVCxDQUE4QlgsT0FBOUIsRUFBdUNSLGNBQXZDLEVBQXVEO0FBQ3REaEMsSUFBRXlDLElBQUYsQ0FBTztBQUNOQyxRQUFLLDZEQUE2REYsT0FENUQ7QUFFTkcsVUFBTyxlQUFTQSxPQUFULEVBQWdCO0FBQ3RCQyxZQUFRRCxLQUFSLENBQWMsdURBQXVESCxPQUFyRTtBQUNBLElBSks7QUFLTkssWUFBUyxtQkFBVztBQUNuQjdDLE1BQUVnQyxjQUFGLEVBQWtCb0IsTUFBbEIsQ0FBeUIsSUFBekIsRUFBK0JyQixNQUEvQjtBQUNBO0FBUEssR0FBUDtBQVNBOztBQUVEOzs7Ozs7O0FBT0EsVUFBU2UscUJBQVQsQ0FBK0JOLE9BQS9CLEVBQXdDO0FBQ3ZDLFNBQVFwQyxlQUFlQyxJQUFmLENBQW9CLE1BQU1tQyxPQUExQixFQUFtQzVCLE1BQW5DLEtBQThDLENBQXREO0FBQ0E7O0FBRUQ7Ozs7Ozs7O0FBUUEsVUFBUzBCLG9CQUFULENBQThCSixNQUE5QixFQUFzQ0YsY0FBdEMsRUFBc0Q7QUFDckQsTUFBT3FCLG1CQUFtQjtBQUN6QkMsV0FBUSxnQkFEaUI7QUFFekJDLGNBQVcsU0FGYztBQUd6QjtBQUNBQyxTQUFNLGdCQUFXO0FBQ2hCeEQsTUFBRVEsY0FBRixFQUFrQlUsR0FBbEIsQ0FBc0IsU0FBdEIsRUFBaUMsS0FBakM7QUFDQSxJQU53QjtBQU96QjtBQUNBdUMsUUFBSyxlQUFXO0FBQ2Z6RCxNQUFFUSxjQUFGLEVBQWtCVSxHQUFsQixDQUFzQixTQUF0QixFQUFpQyxLQUFqQztBQUNBLElBVndCO0FBV3pCO0FBQ0F3QyxTQUFNLGNBQVMvQixLQUFULEVBQWdCQyxFQUFoQixFQUFvQjtBQUN6QixRQUFJWSxVQUFVeEMsRUFBRTRCLEdBQUdKLFNBQUwsRUFBZ0JtQyxJQUFoQixDQUFxQixJQUFyQixDQUFkOztBQUVBLFFBQUl6QixXQUFXLE1BQWYsRUFBdUI7QUFDdEJLLHNCQUFpQkMsT0FBakIsRUFBMEJSLGNBQTFCO0FBQ0EsS0FGRCxNQUVPLElBQUlFLFdBQVcsUUFBZixFQUF5QjtBQUMvQmlCLDBCQUFxQlgsT0FBckIsRUFBOEJSLGNBQTlCO0FBQ0E7QUFDRDtBQXBCd0IsR0FBMUI7O0FBdUJBLFNBQU9xQixnQkFBUDtBQUNBOztBQUVEOzs7Ozs7QUFNQSxVQUFTakIsb0JBQVQsQ0FBOEJILFdBQTlCLEVBQTJDRCxjQUEzQyxFQUEyRDtBQUMxRCxNQUFNNEIsZUFBZTVELEVBQUVpQyxXQUFGLENBQXJCOztBQUVBakMsSUFBRWdDLGNBQUYsRUFBa0JvQixNQUFsQixDQUF5QixJQUF6QixFQUErQlMsT0FBL0IsQ0FBdUNELFlBQXZDOztBQUVBLE1BQU1FLG9CQUFvQkYsYUFBYXpDLFdBQWIsRUFBMUI7O0FBRUF5QyxlQUFhMUMsR0FBYixDQUFpQjtBQUNoQkosUUFBS2QsRUFBRWdDLGNBQUYsRUFBa0IrQixRQUFsQixHQUE2QmpELEdBQTdCLEdBQW9DZ0Qsb0JBQW9CO0FBRDdDLEdBQWpCO0FBR0E7O0FBRUQ7Ozs7OztBQU1BLFVBQVNFLHNCQUFULEdBQWtDO0FBQ2pDLE1BQU1DLHVCQUF1QmpFLEVBQUVrRSxPQUFGLENBQVVsRCxPQUFPbUQsUUFBUCxDQUFnQkMsTUFBaEIsQ0FBdUJDLEtBQXZCLENBQTZCLENBQTdCLENBQVYsQ0FBN0I7O0FBRUFwRSxRQUFNSSxJQUFOLENBQVcsWUFBWCxFQUF5QmlFLElBQXpCLENBQThCLFVBQUNDLEtBQUQsRUFBUUMsSUFBUixFQUFpQjtBQUM5QyxPQUFNQyxvQkFBb0J6RSxFQUFFa0UsT0FBRixDQUFVbEUsRUFBRXdFLElBQUYsRUFBUWIsSUFBUixDQUFhLE1BQWIsRUFBcUJlLE9BQXJCLENBQTZCLFFBQTdCLEVBQXVDLElBQXZDLEVBQTZDTCxLQUE3QyxDQUFtRCxDQUFuRCxDQUFWLENBQTFCOztBQUVBLE9BQUlJLGtCQUFrQkUsRUFBbEIsS0FBeUJWLHFCQUFxQlUsRUFBbEQsRUFBc0Q7QUFDckQzRSxNQUFFd0UsSUFBRixFQUFRckMsT0FBUixDQUFnQixVQUFoQixFQUE0QmYsUUFBNUIsQ0FBcUMsUUFBckM7QUFDQSxXQUFPLEtBQVA7QUFDQTtBQUNELEdBUEQ7QUFRQTs7QUFFRDtBQUNBO0FBQ0E7O0FBRUF2QixRQUFPK0UsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjVFLFFBQU1DLFFBQU4sQ0FBZSxJQUFmLEVBQ0U0RSxFQURGLENBQ0ssWUFETCxFQUNtQnBFLHFCQURuQixFQUVFb0UsRUFGRixDQUVLLFlBRkwsRUFFbUJ6RCxxQkFGbkI7O0FBSUFFLDBCQUF3QmhCLG1CQUF4QjtBQUNBeUQ7O0FBRUFhO0FBQ0EsRUFURDs7QUFXQSxRQUFPaEYsTUFBUDtBQUNBLENBOVNEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9tZW51L21lbnUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG1lbnUuanMgMjAxNi0wNC0yMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogTWFpbiBNZW51IENvbnRyb2xsZXJcbiAqXG4gKiBXaGVuZXZlciB0aGUgdXNlciBjbGlja3Mgb24gYSBtZW51IGl0ZW0gdGhlIGJyb3dzZXIgbXVzdCBiZSByZWRpcmVjdGVkIHRvIHRoZSByZXNwZWN0aXZlIGxvY2F0aW9uLlxuICpcbiAqIE1pZGRsZSBidXR0b24gY2xpY2tzIGFyZSBhbHNvIHN1cHBvcnRlZC5cbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKCdtZW51JywgWyd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSddLCBmdW5jdGlvbihkYXRhKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gVkFSSUFCTEVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxuXHQgKlxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHQgKi9cblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuXG5cdC8qKlxuXHQgKiBNb2R1bGUgSW5zdGFuY2Vcblx0ICpcblx0ICogQHR5cGUge09iamVjdH1cblx0ICovXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xuXG5cdC8qKlxuXHQgKiBNZW51IExpc3QgU2VsZWN0b3Jcblx0ICpcblx0ICogQHR5cGUge09iamVjdH1cblx0ICovXG5cdGNvbnN0ICRsaXN0ID0gJHRoaXMuY2hpbGRyZW4oJ3VsJyk7XG5cblx0LyoqXG5cdCAqIFN1Yi1saXN0IFNlbGVjdG9yXG5cdCAqXG5cdCAqIFRoaXMgb2JqZWN0IGlzIHVzZWQgZm9yIGNvcnJlY3RseSBkaXNwbGF5aW5nIHRoZSBzdWItbGlzdCA8dWw+IGVsZW1lbnQgd2hlbmV2ZXIgaXQgZ29lcyBvdXRcblx0ICogb2Ygdmlld3BvcnQuXG5cdCAqXG5cdCAqIEB0eXBlIHtqUXVlcnl9XG5cdCAqL1xuXHRsZXQgJHN1Ymxpc3QgPSBudWxsO1xuXG5cdC8qKlxuXHQgKiBGYXZvcml0ZXMgQm94XG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCAkZmF2b3JpdGVzTWVudSA9ICR0aGlzLmZpbmQoJ3VsIGxpJykuZmlyc3QoKS5maW5kKCd1bCcpO1xuXG5cdC8qKlxuXHQgKiBEcmFnZ2FibGUgTWVudSBJdGVtc1xuXHQgKlxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgJGRyYWdnYWJsZU1lbnVJdGVtcyA9ICR0aGlzLmZpbmQoJy5mYXYtZHJhZy1pdGVtJyk7XG5cblx0LyoqXG5cdCAqIERyb3B6b25lIEJveFxuXHQgKlxuXHQgKiBUaGUgZHJhZ2dhYmxlIGVsZW1lbnRzIHdpbGwgYmUgcGxhY2VkIGhlcmUuXG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBmYXZEcm9wem9uZUJveCA9ICcjZmF2LWRyb3B6b25lLWJveCc7XG5cblx0LyoqXG5cdCAqIERyYWcgYW5kIGRyb3AgZmxhZyB0byBwcmV2ZW50IHRoZSBkZWZhdWx0IGFjdGlvbiBvZiBhIG1lbnUgaXRlbSB3aGVuIGl0IGlzIGRyYWdnZWQuXG5cdCAqXG5cdCAqIEB0eXBlIHtCb29sZWFufSBUcnVlIHdoaWxlIGFtIGl0ZW0gaXMgZHJhZ2dlZC5cblx0ICovXG5cdGxldCBvbkRyYWdBbmREcm9wID0gZmFsc2U7XG5cblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIEZVTkNUSU9OU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHQvKipcblx0ICogUmVwb3NpdGlvbnMgdGhlIHN1Yi1saXN0IHdoZW4gaXQgZ29lcyBvZmYgdGhlIHZpZXdwb3J0LlxuXHQgKi9cblx0ZnVuY3Rpb24gX29uTWVudUl0ZW1Nb3VzZUVudGVyKCkge1xuXHRcdGlmICgkbGlzdC5oYXNDbGFzcygnZXhwYW5kLWFsbCcpKSB7XG5cdFx0XHRyZXR1cm47IC8vIERvIG5vdCBjaGVjayBmb3Igdmlld3BvcnQgaW4gXCJleHBhbmQtYWxsXCIgc3RhdGUuXG5cdFx0fVxuXG5cdFx0JHN1Ymxpc3QgPSAkKHRoaXMpLmNoaWxkcmVuKCd1bCcpO1xuXHRcdFxuXHRcdGlmICgkc3VibGlzdC5sZW5ndGggPT09IDApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0JHN1Ymxpc3Qub2Zmc2V0ID0gJHN1Ymxpc3Qub2Zmc2V0KCkudG9wICsgJHN1Ymxpc3QuaGVpZ2h0KCk7XG5cblx0XHRpZiAoJHN1Ymxpc3Qub2Zmc2V0ID4gd2luZG93LmlubmVySGVpZ2h0KSB7XG5cdFx0XHQkc3VibGlzdFxuXHRcdFx0XHQuY3NzKCdtYXJnaW4tdG9wJywgLTEgKiAoJHN1Ymxpc3Qub2Zmc2V0IC0gd2luZG93LmlubmVySGVpZ2h0ICsgJCgnI21haW4taGVhZGVyJykub3V0ZXJIZWlnaHQoKVxuXHRcdFx0XHRcdCsgJCgnI21haW4tZm9vdGVyIC5pbmZvJykub3V0ZXJIZWlnaHQoKSkgKyAncHgnKVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ3N0YXktd2l0aGluLXZpZXdwb3J0Jyk7XG5cdFx0fVxuXHR9XG5cblx0LyoqXG5cdCAqIFJlbW92ZSB0aGUgaGVscGVyIGNsYXNzIFwic3RheS13aXRoaW4tdmlld3BvcnRcIiBhbmQgcmVzZXQgdGhlIFwibWFyZ2luLXRvcFwiIHZhbHVlLlxuXHQgKi9cblx0ZnVuY3Rpb24gX29uTWVudUl0ZW1Nb3VzZUxlYXZlKCkge1xuXHRcdGlmICgkbGlzdC5oYXNDbGFzcygnZXhwYW5kLWFsbCcpKSB7XG5cdFx0XHRyZXR1cm47IC8vIERvIG5vdCBjaGVjayBmb3Igdmlld3BvcnQgaW4gXCJleHBhbmQtYWxsXCIgc3RhdGUuXG5cdFx0fVxuXG5cdFx0JHN1Ymxpc3Rcblx0XHRcdC5jc3MoJ21hcmdpbi10b3AnLCAnJylcblx0XHRcdC5yZW1vdmVDbGFzcygnc3RheS13aXRoaW4tdmlld3BvcnQnKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBNYWtlcyBhbGwgbWVudSBpdGVtcyBkcmFnZ2FibGUuXG5cdCAqXG5cdCAqIFRoaXMgZnVuY3Rpb24gc2hvdWxkIGJlIGV4ZWN1dGVkIGluIHRoZSBtb2R1bGUuaW5pdCgpIG1ldGhvZC5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9ICRkcmFnZ2FibGVNZW51SXRlbXMgTWVudSBpdGVtIGpRdWVyeSBzZWxlY3Rvci5cblx0ICovXG5cdGZ1bmN0aW9uIF9tYWtlTWVudUl0ZW1zRHJhZ2dhYmxlKCRkcmFnZ2FibGVNZW51SXRlbXMpIHtcblx0XHQkZHJhZ2dhYmxlTWVudUl0ZW1zLmRyYWdnYWJsZSh7XG5cdFx0XHRoZWxwZXI6ICdjbG9uZScsIC8vIENsb25lIHRoZSBlbGVtZW50LCBkb24ndCBtb3ZlIHRoZSBlbGVtZW50IGl0c2VsZi5cblx0XHRcdHN0YXJ0OiBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHRcdFx0b25EcmFnQW5kRHJvcCA9IHRydWU7XG5cdFx0XHRcdHVpLmhlbHBlci5hZGRDbGFzcygnY3VycmVudGx5RHJhZ2dlZCcpO1xuXHRcdFx0XHRfY3JlYXRlRmF2b3JpdGVzRHJvcHpvbmUodGhpcyk7XG5cdFx0XHR9LFxuXHRcdFx0c3RvcDogZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0b25EcmFnQW5kRHJvcCA9IGZhbHNlO1xuXHRcdFx0XHQkKGZhdkRyb3B6b25lQm94KS5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHR9KTtcblx0fVxuXG5cdC8qKlxuXHQgKiBDcmVhdGVzIHRoZSBmYXZvcml0ZXMgYm94LCB3aGVyZSB0aGUgZHJhZ2dhYmxlIGl0ZW1zIGNhbiBiZSBkcm9wcGVkIG9uLlxuXHQgKlxuXHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBkcmFnZ2VkRWxlbWVudCBEcmFnZ2VkIG1lbnUgaXRlbS5cblx0ICovXG5cdGZ1bmN0aW9uIF9jcmVhdGVGYXZvcml0ZXNEcm9wem9uZShkcmFnZ2VkRWxlbWVudCkge1xuXHRcdGxldCBkcm9wem9uZUJveCA9ICcnO1xuXHRcdGxldCBhY3Rpb24gPSAnJztcblxuXHRcdGlmICgkKGRyYWdnZWRFbGVtZW50KS5wYXJlbnRzKCdsaScpLmZpbmQoJy5mYS1oZWFydCcpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0ZHJvcHpvbmVCb3ggPSBgXG5cdFx0XHRcdDxkaXYgaWQ9XCJmYXYtZHJvcHpvbmUtYm94XCIgY2xhc3M9XCJmYXYtYWRkXCI+XG5cdFx0XHRcdFx0PGkgY2xhc3M9XCJmYSBmYS1oZWFydFwiPjwvaT5cblx0XHRcdFx0PC9kaXY+XG5cdFx0XHRgO1xuXHRcdFx0YWN0aW9uID0gJ3NhdmUnO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRkcm9wem9uZUJveCA9IGBcblx0XHRcdFx0PGRpdiBpZD1cImZhdi1kcm9wem9uZS1ib3hcIiBjbGFzcz1cImZhdi1kZWxldGVcIj5cblx0XHRcdFx0XHQ8aSBjbGFzcz1cImZhIGZhLXRyYXNoXCI+PC9pPlxuXHRcdFx0XHQ8L2Rpdj5cblx0XHRcdGA7XG5cdFx0XHRhY3Rpb24gPSAnZGVsZXRlJztcblx0XHR9XG5cblx0XHRfcG9zaXRpb25Ecm9wem9uZUJveChkcm9wem9uZUJveCwgZHJhZ2dlZEVsZW1lbnQpO1xuXG5cdFx0JChmYXZEcm9wem9uZUJveCkuZHJvcHBhYmxlKF9nZXRPYmplY3RGcm9tQWN0aW9uKGFjdGlvbiwgZHJhZ2dlZEVsZW1lbnQpKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBTdG9yZXMgdGhlIG1lbnUgaXRlbSBhcyBhIGZhdm9yaXRlIGluIHRoZSBkYXRhYmFzZS5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IGxpbmtLZXkgVW5pcXVlIGxpbmsga2V5IGZyb20gdGhlIG1lbnUgaXRlbS5cblx0ICogQHBhcmFtIHtPYmplY3R9IGRyYWdnZWRFbGVtZW50IERyYWdnZWQgbWVudSBpdGVtLlxuXHQgKi9cblx0ZnVuY3Rpb24gX3NhdmVUb0Zhdm9yaXRlcyhsaW5rS2V5LCBkcmFnZ2VkRWxlbWVudCkge1xuXHRcdCQuYWpheCh7XG5cdFx0XHR1cmw6ICdhZG1pbi5waHA/ZG89QWRtaW5GYXZvcml0ZXNBamF4L0FkZE1lbnVJdGVtJmxpbmtfa2V5PScgKyBsaW5rS2V5LFxuXHRcdFx0ZXJyb3I6IGZ1bmN0aW9uKGVycm9yKSB7XG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoJ0NvdWxkIG5vdCBzYXZlIHRoZSBtZW51IGl0ZW0gd2l0aCB0aGUgbGluayBrZXk6ICcgKyBsaW5rS2V5KTtcblx0XHRcdH0sXG5cdFx0XHRzdWNjZXNzOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCFfaXNMaW5rS2V5SW5GYXZvcml0ZXMobGlua0tleSkpIHtcblx0XHRcdFx0XHRjb25zdCAkbmV3TGluayA9ICQoZHJhZ2dlZEVsZW1lbnQpLmNsb25lKCkuYWRkQ2xhc3MoJ2Zhdi1kcmFnLWl0ZW0nKTtcblx0XHRcdFx0XHRjb25zdCAkbmV3TGlzdEl0ZW0gPSAkKCc8bGkvPicpLmFwcGVuZCgkbmV3TGluayk7XG5cdFx0XHRcdFx0JGZhdm9yaXRlc01lbnUuYXBwZW5kKCRuZXdMaXN0SXRlbSk7XG5cdFx0XHRcdFx0X21ha2VNZW51SXRlbXNEcmFnZ2FibGUoJG5ld0xpc3RJdGVtLmZpbmQoJy5mYXYtZHJhZy1pdGVtJykpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH1cblxuXHQvKipcblx0ICogRGVsZXRlcyB0aGUgbWVudSBpdGVtIGFzIGEgZmF2b3JpdGUgZnJvbSB0aGUgZGF0YWJhc2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBsaW5rS2V5IFVuaXF1ZSBsaW5rIGtleSBmcm9tIHRoZSBtZW51IGl0ZW0uXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBkcmFnZ2VkRWxlbWVudCBEcmFnZ2VkIG1lbnUgaXRlbS5cblx0ICovXG5cdGZ1bmN0aW9uIF9kZWxldGVGcm9tRmF2b3JpdGVzKGxpbmtLZXksIGRyYWdnZWRFbGVtZW50KSB7XG5cdFx0JC5hamF4KHtcblx0XHRcdHVybDogJ2FkbWluLnBocD9kbz1BZG1pbkZhdm9yaXRlc0FqYXgvUmVtb3ZlTWVudUl0ZW0mbGlua19rZXk9JyArIGxpbmtLZXksXG5cdFx0XHRlcnJvcjogZnVuY3Rpb24oZXJyb3IpIHtcblx0XHRcdFx0Y29uc29sZS5lcnJvcignQ291bGQgbm90IHJlbW92ZSB0aGUgbWVudSBpdGVtIHdpdGggdGhlIGxpbmsga2V5OiAnICsgbGlua0tleSk7XG5cdFx0XHR9LFxuXHRcdFx0c3VjY2VzczogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQoZHJhZ2dlZEVsZW1lbnQpLnBhcmVudCgnbGknKS5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHR9KTtcblx0fVxuXG5cdC8qKlxuXHQgKiBDaGVja3MgaWYgYSBtZW51IGl0ZW0gaXMgYWxyZWFkeSBzdG9yZWQgaW4gdGhlIGZhdm9yaXRlcyBtZW51LlxuXHQgKlxuXHQgKiBAcGFyYW0ge1N0cmluZ30gbGlua0tleSBVbmlxdWUgbGluayBrZXkgb2YgYSBtZW51IGl0ZW0uXG5cdCAqXG5cdCAqIEByZXR1cm4ge0Jvb2xlYW59IFRydWUgaWYgbWVudSBpdGVtIGlzIGFscmVhZHkgc3RvcmVkLCBlbHNlIGZhbHNlIHdpbGwgYmUgcmV0dXJuZWQuXG5cdCAqL1xuXHRmdW5jdGlvbiBfaXNMaW5rS2V5SW5GYXZvcml0ZXMobGlua0tleSkge1xuXHRcdHJldHVybiAoJGZhdm9yaXRlc01lbnUuZmluZCgnIycgKyBsaW5rS2V5KS5sZW5ndGggIT09IDApO1xuXHR9XG5cblx0LyoqXG5cdCAqIEdldCBqUXVlcnlVSSBkcm9wcGFibGUgb3B0aW9ucyBvYmplY3Rcblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IGFjdGlvbiBBY3Rpb24gdG8gZXhlY3V0ZSB2YWx1ZT1zYXZlfGRlbGV0ZS5cblx0ICogQHBhcmFtIHtPYmplY3R9IGRyYWdnZWRFbGVtZW50IERyYWdnZWQgbWV1IGl0ZW0uXG5cdCAqXG5cdCAqIEByZXR1cm4ge09iamVjdH0galF1ZXJ5VUkgZHJvcHBhYmxlIG9wdGlvbnMuXG5cdCAqL1xuXHRmdW5jdGlvbiBfZ2V0T2JqZWN0RnJvbUFjdGlvbihhY3Rpb24sIGRyYWdnZWRFbGVtZW50KSB7XG5cdFx0Y29uc3QgIGRyb3BwYWJsZU9wdGlvbnMgPSB7XG5cdFx0XHRhY2NlcHQ6ICcuZmF2LWRyYWctaXRlbScsXG5cdFx0XHR0b2xlcmFuY2U6ICdwb2ludGVyJyxcblx0XHRcdC8vIEZ1bmN0aW9uIHdoZW4gaG92ZXJpbmcgb3ZlciB0aGUgZmF2b3JpdGVzIGJveC5cblx0XHRcdG92ZXI6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkKGZhdkRyb3B6b25lQm94KS5jc3MoJ29wYWNpdHknLCAnMS4wJyk7XG5cdFx0XHR9LFxuXHRcdFx0Ly8gRnVuY3Rpb24gd2hlbiBob3ZlcmluZyBvdXQgZnJvbSB0aGUgZmF2b3JpdGVzIGJveC5cblx0XHRcdG91dDogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQoZmF2RHJvcHpvbmVCb3gpLmNzcygnb3BhY2l0eScsICcwLjknKTtcblx0XHRcdH0sXG5cdFx0XHQvLyBGdW5jdGlvbiB3aGVuIGRyb3BwaW5nIGFuIGVsZW1lbnQgb24gdGhlIGZhdm9yaXRlcyBib3guXG5cdFx0XHRkcm9wOiBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHRcdFx0bGV0IGxpbmtLZXkgPSAkKHVpLmRyYWdnYWJsZSkuYXR0cignaWQnKTtcblxuXHRcdFx0XHRpZiAoYWN0aW9uID09PSAnc2F2ZScpIHtcblx0XHRcdFx0XHRfc2F2ZVRvRmF2b3JpdGVzKGxpbmtLZXksIGRyYWdnZWRFbGVtZW50KTtcblx0XHRcdFx0fSBlbHNlIGlmIChhY3Rpb24gPT09ICdkZWxldGUnKSB7XG5cdFx0XHRcdFx0X2RlbGV0ZUZyb21GYXZvcml0ZXMobGlua0tleSwgZHJhZ2dlZEVsZW1lbnQpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdHJldHVybiBkcm9wcGFibGVPcHRpb25zO1xuXHR9XG5cblx0LyoqXG5cdCAqIFBvc2l0aW9ucyB0aGUgRHJvcHpvbmVCb3ggYXQgdGhlIGNvcnJlY3QgcGxhY2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBkcm9wem9uZUJveCBEcm9wem9uZUJveCBIVE1MLlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZHJhZ2dlZEVsZW1lbnQgRHJhZ2dlZCBtZW51IGl0ZW0uXG5cdCAqL1xuXHRmdW5jdGlvbiBfcG9zaXRpb25Ecm9wem9uZUJveChkcm9wem9uZUJveCwgZHJhZ2dlZEVsZW1lbnQpIHtcblx0XHRjb25zdCAkZHJvcHpvbmVCb3ggPSAkKGRyb3B6b25lQm94KTtcblxuXHRcdCQoZHJhZ2dlZEVsZW1lbnQpLnBhcmVudCgnbGknKS5wcmVwZW5kKCRkcm9wem9uZUJveCk7XG5cblx0XHRjb25zdCBkcm9wem9uZUJveEhlaWdodCA9ICRkcm9wem9uZUJveC5vdXRlckhlaWdodCgpO1xuXG5cdFx0JGRyb3B6b25lQm94LmNzcyh7XG5cdFx0XHR0b3A6ICQoZHJhZ2dlZEVsZW1lbnQpLnBvc2l0aW9uKCkudG9wIC0gKGRyb3B6b25lQm94SGVpZ2h0IC8gMilcblx0XHR9KTtcblx0fVxuXG5cdC8qKlxuXHQgKiBPcGVuIHRoZSBhY3RpdmUgbWVudSBncm91cC5cblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBmaW5kIHRoZSBtZW51IGl0ZW0gdGhhdCBjb250YWlucyB0aGUgc2FtZSBcImRvXCIgR0VUIHBhcmFtZXRlciBhbmQgc2V0IHRoZSBcImFjdGl2ZVwiXG5cdCAqIGNsYXNzIHRvIGl0cyBwYXJlbnQuXG5cdCAqL1xuXHRmdW5jdGlvbiBfdG9nZ2xlQWN0aXZlTWVudUdyb3VwKCkge1xuXHRcdGNvbnN0IGN1cnJlbnRVcmxQYXJhbWV0ZXJzID0gJC5kZXBhcmFtKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2guc2xpY2UoMSkpO1xuXG5cdFx0JGxpc3QuZmluZCgnbGk6Z3QoMSkgYScpLmVhY2goKGluZGV4LCBsaW5rKSA9PiB7XG5cdFx0XHRjb25zdCBsaW5rVXJsUGFyYW1ldGVycyA9ICQuZGVwYXJhbSgkKGxpbmspLmF0dHIoJ2hyZWYnKS5yZXBsYWNlKC8uKihcXD8pLywgJyQxJykuc2xpY2UoMSkpO1xuXG5cdFx0XHRpZiAobGlua1VybFBhcmFtZXRlcnMuZG8gPT09IGN1cnJlbnRVcmxQYXJhbWV0ZXJzLmRvKSB7XG5cdFx0XHRcdCQobGluaykucGFyZW50cygnbGk6bHQoMiknKS5hZGRDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHR9KTtcblx0fVxuXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBJTklUSUFMSVpBVElPTlxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHQkbGlzdC5jaGlsZHJlbignbGknKVxuXHRcdFx0Lm9uKCdtb3VzZWVudGVyJywgX29uTWVudUl0ZW1Nb3VzZUVudGVyKVxuXHRcdFx0Lm9uKCdtb3VzZWxlYXZlJywgX29uTWVudUl0ZW1Nb3VzZUxlYXZlKTtcblx0XHRcblx0XHRfbWFrZU1lbnVJdGVtc0RyYWdnYWJsZSgkZHJhZ2dhYmxlTWVudUl0ZW1zKTtcblx0XHRfdG9nZ2xlQWN0aXZlTWVudUdyb3VwKCk7XG5cdFx0XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXHRcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
