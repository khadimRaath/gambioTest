'use strict';

/* --------------------------------------------------------------
	tsexcellence.js 2016-09-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('tsexcellence', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	module.init = function (done) {
		$('button#remove_tsbp').on('click', function (e) {
			e.preventDefault();
			$.ajax({
				"data": {
					"remove_tsbp": "true"
				},
				"url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
				"type": "POST"
			}).done(function (data) {
				window.location = window.location;
			});
		});
		$('button#add_tsbp').on('click', function (e) {
			e.preventDefault();
			$.ajax({
				"data": {
					"add_tsbp": "true",
					"amount": $("input[name=tsbp_amount]").val()
				},
				"url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
				"type": "POST"
			}).done(function (data) {
				window.location = window.location;
			});
		});
		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdHNleGNlbGxlbmNlLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImFqYXgiLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0Iiwid2luZG93IiwibG9jYXRpb24iLCJ2YWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGNBQXRCLEVBQXNDLEVBQXRDLEVBQTBDLFVBQVNDLElBQVQsRUFBZTs7QUFFeEQ7O0FBRUQ7O0FBRUMsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxXQUFXLEVBRFo7QUFBQSxLQUdDQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUhYO0FBQUEsS0FJQ0QsU0FBUyxFQUpWOztBQU1BQSxRQUFPTyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCTCxJQUFFLG9CQUFGLEVBQXdCTSxFQUF4QixDQUEyQixPQUEzQixFQUFvQyxVQUFTQyxDQUFULEVBQVk7QUFDL0NBLEtBQUVDLGNBQUY7QUFDQVIsS0FBRVMsSUFBRixDQUFPO0FBQ04sWUFBUTtBQUNQLG9CQUFlO0FBRFIsS0FERjtBQUlOLFdBQU9DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsaURBSmpDO0FBS04sWUFBUTtBQUxGLElBQVAsRUFNR1IsSUFOSCxDQU1RLFVBQVNQLElBQVQsRUFBZTtBQUN0QmdCLFdBQU9DLFFBQVAsR0FBa0JELE9BQU9DLFFBQXpCO0FBQ0EsSUFSRDtBQVNBLEdBWEQ7QUFZQWYsSUFBRSxpQkFBRixFQUFxQk0sRUFBckIsQ0FBd0IsT0FBeEIsRUFBaUMsVUFBU0MsQ0FBVCxFQUFZO0FBQzVDQSxLQUFFQyxjQUFGO0FBQ0FSLEtBQUVTLElBQUYsQ0FBTztBQUNOLFlBQVE7QUFDUCxpQkFBWSxNQURMO0FBRVAsZUFBVVQsRUFBRSx5QkFBRixFQUE2QmdCLEdBQTdCO0FBRkgsS0FERjtBQUtOLFdBQU9OLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsaURBTGpDO0FBTU4sWUFBUTtBQU5GLElBQVAsRUFPR1IsSUFQSCxDQU9RLFVBQVNQLElBQVQsRUFBZTtBQUN0QmdCLFdBQU9DLFFBQVAsR0FBa0JELE9BQU9DLFFBQXpCO0FBQ0EsSUFURDtBQVVBLEdBWkQ7QUFhQVY7QUFDQSxFQTNCRDs7QUE2QkE7QUFDQSxRQUFPUixNQUFQO0FBQ0EsQ0EzQ0QiLCJmaWxlIjoid2lkZ2V0cy90c2V4Y2VsbGVuY2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHR0c2V4Y2VsbGVuY2UuanMgMjAxNi0wOS0yNlxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKCd0c2V4Y2VsbGVuY2UnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRkZWZhdWx0cyA9IHtcblx0XHR9LFxuXHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdG1vZHVsZSA9IHt9O1xuXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdCQoJ2J1dHRvbiNyZW1vdmVfdHNicCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdFwiZGF0YVwiOiB7XG5cdFx0XHRcdFx0XCJyZW1vdmVfdHNicFwiOiBcInRydWVcIixcblx0XHRcdFx0fSxcblx0XHRcdFx0XCJ1cmxcIjoganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPVRydXN0ZWRTaG9wc0V4Y2VsbGVuY2UnLFxuXHRcdFx0XHRcInR5cGVcIjogXCJQT1NUXCJcblx0XHRcdH0pLmRvbmUoZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0XHR3aW5kb3cubG9jYXRpb24gPSB3aW5kb3cubG9jYXRpb247XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHQkKCdidXR0b24jYWRkX3RzYnAnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcImRhdGFcIjoge1xuXHRcdFx0XHRcdFwiYWRkX3RzYnBcIjogXCJ0cnVlXCIsXG5cdFx0XHRcdFx0XCJhbW91bnRcIjogJChcImlucHV0W25hbWU9dHNicF9hbW91bnRdXCIpLnZhbCgpXG5cdFx0XHRcdH0sXG5cdFx0XHRcdFwidXJsXCI6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9yZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1UcnVzdGVkU2hvcHNFeGNlbGxlbmNlJyxcblx0XHRcdFx0XCJ0eXBlXCI6IFwiUE9TVFwiXG5cdFx0XHR9KS5kb25lKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0d2luZG93LmxvY2F0aW9uID0gd2luZG93LmxvY2F0aW9uO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0cmV0dXJuIG1vZHVsZTtcbn0pO1xuIl19
