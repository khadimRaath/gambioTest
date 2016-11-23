'use strict';

/* --------------------------------------------------------------
 social_share.js 2016-07-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the social sharing support
 * 
 * (e.g.: Facebook, Twitter, Google+)
 * 
 * {@link https://github.com/heiseonline/shariff}
 */
gambio.widgets.module('social_share', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ########## 

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  */
	module.init = function (done) {
		$this.addClass('shariff');

		var config = {
			url: window.location.href,
			theme: 'standard',
			lang: jse.core.config.get('languageCode'),
			services: []
		};

		if (options.facebook !== undefined) {
			config.services.push('facebook');
		}

		if (options.twitter !== undefined) {
			config.services.push('twitter');
		}

		if (options.googleplus !== undefined) {
			config.services.push('googleplus');
		}

		if (options.pinterest !== undefined) {
			config.services.push('pinterest');
		}

		new Shariff($this, config);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc29jaWFsX3NoYXJlLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJhZGRDbGFzcyIsImNvbmZpZyIsInVybCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsInRoZW1lIiwibGFuZyIsImpzZSIsImNvcmUiLCJnZXQiLCJzZXJ2aWNlcyIsImZhY2Vib29rIiwidW5kZWZpbmVkIiwicHVzaCIsInR3aXR0ZXIiLCJnb29nbGVwbHVzIiwicGludGVyZXN0IiwiU2hhcmlmZiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGNBREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVGOztBQUVFLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVyxFQURaO0FBQUEsS0FFQ0MsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGWDtBQUFBLEtBR0NELFNBQVMsRUFIVjs7QUFNRjs7QUFFRTs7O0FBR0FBLFFBQU9PLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJOLFFBQU1PLFFBQU4sQ0FBZSxTQUFmOztBQUVBLE1BQUlDLFNBQVM7QUFDWkMsUUFBS0MsT0FBT0MsUUFBUCxDQUFnQkMsSUFEVDtBQUVaQyxVQUFPLFVBRks7QUFHWkMsU0FBTUMsSUFBSUMsSUFBSixDQUFTUixNQUFULENBQWdCUyxHQUFoQixDQUFvQixjQUFwQixDQUhNO0FBSVpDLGFBQVU7QUFKRSxHQUFiOztBQU9BLE1BQUlmLFFBQVFnQixRQUFSLEtBQXFCQyxTQUF6QixFQUFvQztBQUNuQ1osVUFBT1UsUUFBUCxDQUFnQkcsSUFBaEIsQ0FBcUIsVUFBckI7QUFDQTs7QUFFRCxNQUFJbEIsUUFBUW1CLE9BQVIsS0FBb0JGLFNBQXhCLEVBQW1DO0FBQ2xDWixVQUFPVSxRQUFQLENBQWdCRyxJQUFoQixDQUFxQixTQUFyQjtBQUNBOztBQUVELE1BQUlsQixRQUFRb0IsVUFBUixLQUF1QkgsU0FBM0IsRUFBc0M7QUFDckNaLFVBQU9VLFFBQVAsQ0FBZ0JHLElBQWhCLENBQXFCLFlBQXJCO0FBQ0E7O0FBRUQsTUFBSWxCLFFBQVFxQixTQUFSLEtBQXNCSixTQUExQixFQUFxQztBQUNwQ1osVUFBT1UsUUFBUCxDQUFnQkcsSUFBaEIsQ0FBcUIsV0FBckI7QUFDQTs7QUFFRCxNQUFJSSxPQUFKLENBQVl6QixLQUFaLEVBQW1CUSxNQUFuQjs7QUFFQUY7QUFDQSxFQTdCRDs7QUErQkE7QUFDQSxRQUFPUixNQUFQO0FBQ0EsQ0F2REYiLCJmaWxlIjoid2lkZ2V0cy9zb2NpYWxfc2hhcmUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHNvY2lhbF9zaGFyZS5qcyAyMDE2LTA3LTEyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBlbmFibGVzIHRoZSBzb2NpYWwgc2hhcmluZyBzdXBwb3J0XG4gKiBcbiAqIChlLmcuOiBGYWNlYm9vaywgVHdpdHRlciwgR29vZ2xlKylcbiAqIFxuICoge0BsaW5rIGh0dHBzOi8vZ2l0aHViLmNvbS9oZWlzZW9ubGluZS9zaGFyaWZmfVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdzb2NpYWxfc2hhcmUnLFxuXG5cdFtdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjIFxuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpcy5hZGRDbGFzcygnc2hhcmlmZicpOyBcblx0XHRcdFxuXHRcdFx0dmFyIGNvbmZpZyA9IHtcblx0XHRcdFx0dXJsOiB3aW5kb3cubG9jYXRpb24uaHJlZixcblx0XHRcdFx0dGhlbWU6ICdzdGFuZGFyZCcsXG5cdFx0XHRcdGxhbmc6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpLFxuXHRcdFx0XHRzZXJ2aWNlczogW11cblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmZhY2Vib29rICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0Y29uZmlnLnNlcnZpY2VzLnB1c2goJ2ZhY2Vib29rJyk7IFxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAob3B0aW9ucy50d2l0dGVyICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0Y29uZmlnLnNlcnZpY2VzLnB1c2goJ3R3aXR0ZXInKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0aWYgKG9wdGlvbnMuZ29vZ2xlcGx1cyAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdGNvbmZpZy5zZXJ2aWNlcy5wdXNoKCdnb29nbGVwbHVzJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLnBpbnRlcmVzdCAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdGNvbmZpZy5zZXJ2aWNlcy5wdXNoKCdwaW50ZXJlc3QnKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0bmV3IFNoYXJpZmYoJHRoaXMsIGNvbmZpZyk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pOyJdfQ==
