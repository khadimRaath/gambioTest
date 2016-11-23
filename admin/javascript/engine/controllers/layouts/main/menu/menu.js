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
gx.controllers.module('menu', ['user_configuration_service'], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);

	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};

	/**
	 * Menu List Selector
	 *
	 * @type {Object}
	 */
	const $list = $this.children('ul');

	/**
	 * Sub-list Selector
	 *
	 * This object is used for correctly displaying the sub-list <ul> element whenever it goes out
	 * of viewport.
	 *
	 * @type {jQuery}
	 */
	let $sublist = null;

	/**
	 * Favorites Box
	 *
	 * @type {Object}
	 */
	const $favoritesMenu = $this.find('ul li').first().find('ul');

	/**
	 * Draggable Menu Items
	 *
	 * @type {Object}
	 */
	const $draggableMenuItems = $this.find('.fav-drag-item');

	/**
	 * Dropzone Box
	 *
	 * The draggable elements will be placed here.
	 *
	 * @type {Object}
	 */
	const favDropzoneBox = '#fav-dropzone-box';

	/**
	 * Drag and drop flag to prevent the default action of a menu item when it is dragged.
	 *
	 * @type {Boolean} True while am item is dragged.
	 */
	let onDragAndDrop = false;

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
			$sublist
				.css('margin-top', -1 * ($sublist.offset - window.innerHeight + $('#main-header').outerHeight()
					+ $('#main-footer .info').outerHeight()) + 'px')
				.addClass('stay-within-viewport');
		}
	}

	/**
	 * Remove the helper class "stay-within-viewport" and reset the "margin-top" value.
	 */
	function _onMenuItemMouseLeave() {
		if ($list.hasClass('expand-all')) {
			return; // Do not check for viewport in "expand-all" state.
		}

		$sublist
			.css('margin-top', '')
			.removeClass('stay-within-viewport');
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
			start: function(event, ui) {
				onDragAndDrop = true;
				ui.helper.addClass('currentlyDragged');
				_createFavoritesDropzone(this);
			},
			stop: function(event) {
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
		let dropzoneBox = '';
		let action = '';

		if ($(draggedElement).parents('li').find('.fa-heart').length === 0) {
			dropzoneBox = `
				<div id="fav-dropzone-box" class="fav-add">
					<i class="fa fa-heart"></i>
				</div>
			`;
			action = 'save';
		} else {
			dropzoneBox = `
				<div id="fav-dropzone-box" class="fav-delete">
					<i class="fa fa-trash"></i>
				</div>
			`;
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
			error: function(error) {
				console.error('Could not save the menu item with the link key: ' + linkKey);
			},
			success: function() {
				if (!_isLinkKeyInFavorites(linkKey)) {
					const $newLink = $(draggedElement).clone().addClass('fav-drag-item');
					const $newListItem = $('<li/>').append($newLink);
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
			error: function(error) {
				console.error('Could not remove the menu item with the link key: ' + linkKey);
			},
			success: function() {
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
		return ($favoritesMenu.find('#' + linkKey).length !== 0);
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
		const  droppableOptions = {
			accept: '.fav-drag-item',
			tolerance: 'pointer',
			// Function when hovering over the favorites box.
			over: function() {
				$(favDropzoneBox).css('opacity', '1.0');
			},
			// Function when hovering out from the favorites box.
			out: function() {
				$(favDropzoneBox).css('opacity', '0.9');
			},
			// Function when dropping an element on the favorites box.
			drop: function(event, ui) {
				let linkKey = $(ui.draggable).attr('id');

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
		const $dropzoneBox = $(dropzoneBox);

		$(draggedElement).parent('li').prepend($dropzoneBox);

		const dropzoneBoxHeight = $dropzoneBox.outerHeight();

		$dropzoneBox.css({
			top: $(draggedElement).position().top - (dropzoneBoxHeight / 2)
		});
	}

	/**
	 * Open the active menu group.
	 *
	 * This method will find the menu item that contains the same "do" GET parameter and set the "active"
	 * class to its parent.
	 */
	function _toggleActiveMenuGroup() {
		const currentUrlParameters = $.deparam(window.location.search.slice(1));

		$list.find('li:gt(1) a').each((index, link) => {
			const linkUrlParameters = $.deparam($(link).attr('href').replace(/.*(\?)/, '$1').slice(1));

			if (linkUrlParameters.do === currentUrlParameters.do) {
				$(link).parents('li:lt(2)').addClass('active');
				return false;
			}
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function(done) {
		$list.children('li')
			.on('mouseenter', _onMenuItemMouseEnter)
			.on('mouseleave', _onMenuItemMouseLeave);
		
		_makeMenuItemsDraggable($draggableMenuItems);
		_toggleActiveMenuGroup();
		
		done();
	};
	
	return module;
});