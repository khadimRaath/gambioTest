'use strict';

/* --------------------------------------------------------------
 core_workarounds.js 2015-08-05 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Core Workarounds Module
 *
 * This file contains workarounds that do not belong in other JS modules.
 */
gambio.widgets.module('core_workarounds', [], function () {

	'use strict';

	var module = {};

	var _initMobileMenu = function _initMobileMenu() {
		var $profile = $('#topbar-container nav > ul> li').clone(),
		    $login = $profile.find('.login-off-item'),
		    $loginClone = $login.clone();

		$loginClone.addClass('dropdown navbar-topbar-item');
		$login.remove();
		$profile = $profile.add($loginClone);

		$('#categories nav > ul').append($profile);
		$('#categories nav > ul').attr('data-gambio-widget', 'link_crypter'); //reinitialize widgets
		gambio.widgets.init($('#categories nav > ul'));

		var $verticalMenu = $('.navbar-categories-left');
		if ($verticalMenu.length > 0) {
			$verticalMenu.find('ul.level-1').append($profile.clone());

			$verticalMenu.find('ul.level-1').attr('data-gambio-widget', 'link_crypter');
			gambio.widgets.init($verticalMenu.find('ul.level-1'));

			// hide the new elements
			$verticalMenu.find('.navbar-topbar-item').hide();
		}
	};

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		_initMobileMenu();

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY29yZV93b3JrYXJvdW5kcy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiX2luaXRNb2JpbGVNZW51IiwiJHByb2ZpbGUiLCIkIiwiY2xvbmUiLCIkbG9naW4iLCJmaW5kIiwiJGxvZ2luQ2xvbmUiLCJhZGRDbGFzcyIsInJlbW92ZSIsImFkZCIsImFwcGVuZCIsImF0dHIiLCJpbml0IiwiJHZlcnRpY2FsTWVudSIsImxlbmd0aCIsImhpZGUiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixrQkFBdEIsRUFBMEMsRUFBMUMsRUFBOEMsWUFBVzs7QUFFeEQ7O0FBRUEsS0FBSUEsU0FBUyxFQUFiOztBQUVBLEtBQUlDLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNoQyxNQUFJQyxXQUFXQyxFQUFFLGdDQUFGLEVBQW9DQyxLQUFwQyxFQUFmO0FBQUEsTUFDQ0MsU0FBU0gsU0FBU0ksSUFBVCxDQUFjLGlCQUFkLENBRFY7QUFBQSxNQUVDQyxjQUFjRixPQUFPRCxLQUFQLEVBRmY7O0FBSUFHLGNBQVlDLFFBQVosQ0FBcUIsNkJBQXJCO0FBQ0FILFNBQU9JLE1BQVA7QUFDQVAsYUFBV0EsU0FBU1EsR0FBVCxDQUFhSCxXQUFiLENBQVg7O0FBRUFKLElBQUUsc0JBQUYsRUFBMEJRLE1BQTFCLENBQWlDVCxRQUFqQztBQUNBQyxJQUFFLHNCQUFGLEVBQTBCUyxJQUExQixDQUErQixvQkFBL0IsRUFBcUQsY0FBckQsRUFWZ0MsQ0FVdUM7QUFDdkVkLFNBQU9DLE9BQVAsQ0FBZWMsSUFBZixDQUFvQlYsRUFBRSxzQkFBRixDQUFwQjs7QUFFQSxNQUFJVyxnQkFBZ0JYLEVBQUUseUJBQUYsQ0FBcEI7QUFDQSxNQUFJVyxjQUFjQyxNQUFkLEdBQXVCLENBQTNCLEVBQThCO0FBQzdCRCxpQkFBY1IsSUFBZCxDQUFtQixZQUFuQixFQUFpQ0ssTUFBakMsQ0FBd0NULFNBQVNFLEtBQVQsRUFBeEM7O0FBRUFVLGlCQUFjUixJQUFkLENBQW1CLFlBQW5CLEVBQWlDTSxJQUFqQyxDQUFzQyxvQkFBdEMsRUFBNEQsY0FBNUQ7QUFDQWQsVUFBT0MsT0FBUCxDQUFlYyxJQUFmLENBQW9CQyxjQUFjUixJQUFkLENBQW1CLFlBQW5CLENBQXBCOztBQUVBO0FBQ0FRLGlCQUFjUixJQUFkLENBQW1CLHFCQUFuQixFQUEwQ1UsSUFBMUM7QUFDQTtBQUNELEVBdkJEOztBQTBCQTs7OztBQUlBaEIsUUFBT2EsSUFBUCxHQUFjLFVBQVNJLElBQVQsRUFBZTtBQUM1QmhCOztBQUVBZ0I7QUFDQSxFQUpEOztBQU1BLFFBQU9qQixNQUFQO0FBQ0EsQ0EzQ0QiLCJmaWxlIjoid2lkZ2V0cy9jb3JlX3dvcmthcm91bmRzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjb3JlX3dvcmthcm91bmRzLmpzIDIwMTUtMDgtMDUgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIENvcmUgV29ya2Fyb3VuZHMgTW9kdWxlXG4gKlxuICogVGhpcyBmaWxlIGNvbnRhaW5zIHdvcmthcm91bmRzIHRoYXQgZG8gbm90IGJlbG9uZyBpbiBvdGhlciBKUyBtb2R1bGVzLlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2NvcmVfd29ya2Fyb3VuZHMnLCBbXSwgZnVuY3Rpb24oKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdHZhciBtb2R1bGUgPSB7fTtcblxuXHR2YXIgX2luaXRNb2JpbGVNZW51ID0gZnVuY3Rpb24oKSB7XG5cdFx0dmFyICRwcm9maWxlID0gJCgnI3RvcGJhci1jb250YWluZXIgbmF2ID4gdWw+IGxpJykuY2xvbmUoKSxcblx0XHRcdCRsb2dpbiA9ICRwcm9maWxlLmZpbmQoJy5sb2dpbi1vZmYtaXRlbScpLFxuXHRcdFx0JGxvZ2luQ2xvbmUgPSAkbG9naW4uY2xvbmUoKTtcblxuXHRcdCRsb2dpbkNsb25lLmFkZENsYXNzKCdkcm9wZG93biBuYXZiYXItdG9wYmFyLWl0ZW0nKTtcblx0XHQkbG9naW4ucmVtb3ZlKCk7XG5cdFx0JHByb2ZpbGUgPSAkcHJvZmlsZS5hZGQoJGxvZ2luQ2xvbmUpO1xuXG5cdFx0JCgnI2NhdGVnb3JpZXMgbmF2ID4gdWwnKS5hcHBlbmQoJHByb2ZpbGUpO1xuXHRcdCQoJyNjYXRlZ29yaWVzIG5hdiA+IHVsJykuYXR0cignZGF0YS1nYW1iaW8td2lkZ2V0JywgJ2xpbmtfY3J5cHRlcicpOyAgLy9yZWluaXRpYWxpemUgd2lkZ2V0c1xuXHRcdGdhbWJpby53aWRnZXRzLmluaXQoJCgnI2NhdGVnb3JpZXMgbmF2ID4gdWwnKSk7XG5cblx0XHR2YXIgJHZlcnRpY2FsTWVudSA9ICQoJy5uYXZiYXItY2F0ZWdvcmllcy1sZWZ0Jyk7XG5cdFx0aWYgKCR2ZXJ0aWNhbE1lbnUubGVuZ3RoID4gMCkge1xuXHRcdFx0JHZlcnRpY2FsTWVudS5maW5kKCd1bC5sZXZlbC0xJykuYXBwZW5kKCRwcm9maWxlLmNsb25lKCkpO1xuXG5cdFx0XHQkdmVydGljYWxNZW51LmZpbmQoJ3VsLmxldmVsLTEnKS5hdHRyKCdkYXRhLWdhbWJpby13aWRnZXQnLCAnbGlua19jcnlwdGVyJyk7XG5cdFx0XHRnYW1iaW8ud2lkZ2V0cy5pbml0KCR2ZXJ0aWNhbE1lbnUuZmluZCgndWwubGV2ZWwtMScpKTtcblxuXHRcdFx0Ly8gaGlkZSB0aGUgbmV3IGVsZW1lbnRzXG5cdFx0XHQkdmVydGljYWxNZW51LmZpbmQoJy5uYXZiYXItdG9wYmFyLWl0ZW0nKS5oaWRlKCk7XG5cdFx0fVxuXHR9O1xuXG5cblx0LyoqXG5cdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHQgKiBAY29uc3RydWN0b3Jcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdF9pbml0TW9iaWxlTWVudSgpO1xuXG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==
