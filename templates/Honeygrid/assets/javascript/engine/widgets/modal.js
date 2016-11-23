'use strict';

/* --------------------------------------------------------------
 modal.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that opens a modal layer with the URL given by
 * an a-tag that has the class "js-open-modal". For backwards
 * compatibility the class "lightbox_iframe" is possible, also.
 */
gambio.widgets.module('modal', [gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		add: '&lightbox_mode=1' },
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler to open the modal
  * window with the link data
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _openModal = function _openModal(e) {
		e.preventDefault();

		var $self = $(this),
		    url = $self.attr('href'),
		    dataset = $self.parseModuleData('modal'),
		    type = dataset.type || e.data.type,
		    settings = $.extend({}, dataset.settings || {});

		url += url[0] === '#' || url[0] === '.' ? '' : options.add;
		settings.template = url;

		jse.libs.template.modal[type](settings);
		if (dataset.finishEvent) {
			$('body').trigger(dataset.finishEvent);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on('click', '.js-open-modal', _openModal).on('click', '.lightbox_iframe', { type: 'iframe' }, _openModal);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9kYWwuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImFkZCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfb3Blbk1vZGFsIiwiZSIsInByZXZlbnREZWZhdWx0IiwiJHNlbGYiLCJ1cmwiLCJhdHRyIiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsInR5cGUiLCJzZXR0aW5ncyIsInRlbXBsYXRlIiwianNlIiwibGlicyIsIm1vZGFsIiwiZmluaXNoRXZlbnQiLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxPQURELEVBR0MsQ0FDQ0YsT0FBT0csTUFBUCxHQUFnQiwwQkFEakIsRUFFQ0gsT0FBT0csTUFBUCxHQUFnQixhQUZqQixDQUhELEVBUUMsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVGOztBQUVFLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVztBQUNWQyxPQUFLLGtCQURLLEVBRFo7QUFBQSxLQUlDQyxVQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQUpYO0FBQUEsS0FLQ0YsU0FBUyxFQUxWOztBQU9GOztBQUVFOzs7Ozs7QUFNQSxLQUFJUyxhQUFhLFNBQWJBLFVBQWEsQ0FBU0MsQ0FBVCxFQUFZO0FBQzVCQSxJQUFFQyxjQUFGOztBQUVBLE1BQUlDLFFBQVFSLEVBQUUsSUFBRixDQUFaO0FBQUEsTUFDQ1MsTUFBTUQsTUFBTUUsSUFBTixDQUFXLE1BQVgsQ0FEUDtBQUFBLE1BRUNDLFVBQVVILE1BQU1JLGVBQU4sQ0FBc0IsT0FBdEIsQ0FGWDtBQUFBLE1BR0NDLE9BQU9GLFFBQVFFLElBQVIsSUFBZ0JQLEVBQUVSLElBQUYsQ0FBT2UsSUFIL0I7QUFBQSxNQUlDQyxXQUFXZCxFQUFFSSxNQUFGLENBQVMsRUFBVCxFQUFhTyxRQUFRRyxRQUFSLElBQW9CLEVBQWpDLENBSlo7O0FBTUFMLFNBQVFBLElBQUksQ0FBSixNQUFXLEdBQVgsSUFBa0JBLElBQUksQ0FBSixNQUFXLEdBQTlCLEdBQXFDLEVBQXJDLEdBQTBDTixRQUFRRCxHQUF6RDtBQUNBWSxXQUFTQyxRQUFULEdBQW9CTixHQUFwQjs7QUFFQU8sTUFBSUMsSUFBSixDQUFTRixRQUFULENBQWtCRyxLQUFsQixDQUF3QkwsSUFBeEIsRUFBOEJDLFFBQTlCO0FBQ0EsTUFBSUgsUUFBUVEsV0FBWixFQUF5QjtBQUN4Qm5CLEtBQUUsTUFBRixFQUFVb0IsT0FBVixDQUFrQlQsUUFBUVEsV0FBMUI7QUFDQTtBQUNELEVBaEJEOztBQWtCRjs7QUFFRTs7OztBQUlBdkIsUUFBT3lCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCdkIsUUFDRXdCLEVBREYsQ0FDSyxPQURMLEVBQ2MsZ0JBRGQsRUFDZ0NsQixVQURoQyxFQUVFa0IsRUFGRixDQUVLLE9BRkwsRUFFYyxrQkFGZCxFQUVrQyxFQUFDVixNQUFNLFFBQVAsRUFGbEMsRUFFb0RSLFVBRnBEOztBQUlBaUI7QUFDQSxFQVBEOztBQVNBO0FBQ0EsUUFBTzFCLE1BQVA7QUFDQSxDQWhFRiIsImZpbGUiOiJ3aWRnZXRzL21vZGFsLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2RhbC5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgdGhhdCBvcGVucyBhIG1vZGFsIGxheWVyIHdpdGggdGhlIFVSTCBnaXZlbiBieVxuICogYW4gYS10YWcgdGhhdCBoYXMgdGhlIGNsYXNzIFwianMtb3Blbi1tb2RhbFwiLiBGb3IgYmFja3dhcmRzXG4gKiBjb21wYXRpYmlsaXR5IHRoZSBjbGFzcyBcImxpZ2h0Ym94X2lmcmFtZVwiIGlzIHBvc3NpYmxlLCBhbHNvLlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdtb2RhbCcsXG5cblx0W1xuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGFkZDogJyZsaWdodGJveF9tb2RlPTEnLCAgIC8vIEFkZCB0aGlzIHBhcmFtZXRlciB0byBlYWNoIFVSTFxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIHRvIG9wZW4gdGhlIG1vZGFsXG5cdFx0ICogd2luZG93IHdpdGggdGhlIGxpbmsgZGF0YVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3Blbk1vZGFsID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHR1cmwgPSAkc2VsZi5hdHRyKCdocmVmJyksXG5cdFx0XHRcdGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ21vZGFsJyksXG5cdFx0XHRcdHR5cGUgPSBkYXRhc2V0LnR5cGUgfHwgZS5kYXRhLnR5cGUsXG5cdFx0XHRcdHNldHRpbmdzID0gJC5leHRlbmQoe30sIGRhdGFzZXQuc2V0dGluZ3MgfHwge30pO1xuXG5cdFx0XHR1cmwgKz0gKHVybFswXSA9PT0gJyMnIHx8IHVybFswXSA9PT0gJy4nKSA/ICcnIDogb3B0aW9ucy5hZGQ7XG5cdFx0XHRzZXR0aW5ncy50ZW1wbGF0ZSA9IHVybDtcblxuXHRcdFx0anNlLmxpYnMudGVtcGxhdGUubW9kYWxbdHlwZV0oc2V0dGluZ3MpO1xuXHRcdFx0aWYgKGRhdGFzZXQuZmluaXNoRXZlbnQpIHtcblx0XHRcdFx0JCgnYm9keScpLnRyaWdnZXIoZGF0YXNldC5maW5pc2hFdmVudCk7XG5cdFx0XHR9XG5cdFx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdjbGljaycsICcuanMtb3Blbi1tb2RhbCcsIF9vcGVuTW9kYWwpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLmxpZ2h0Ym94X2lmcmFtZScsIHt0eXBlOiAnaWZyYW1lJ30sIF9vcGVuTW9kYWwpO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
