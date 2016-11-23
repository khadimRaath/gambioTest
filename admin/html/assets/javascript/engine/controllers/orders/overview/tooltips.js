'use strict';

/* --------------------------------------------------------------
 tooltip.js 2016-09-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Orders Table Tooltip
 *
 * This controller displays tooltips for the orders overview table. The tooltips are loaded after the
 * table data request is ready for optimization purposes.
 */
gx.controllers.module('tooltips', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @var {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options
  *
  * @type {Object}
  */
	var defaults = {
		sourceUrl: jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/Tooltips',
		selectors: {
			mouseenter: {
				orderItems: '.tooltip-order-items',
				customerMemos: '.tooltip-customer-memos',
				customerAddresses: '.tooltip-customer-addresses',
				orderSumBlock: '.tooltip-order-sum-block',
				orderStatusHistory: '.tooltip-order-status-history',
				orderComment: '.tooltip-order-comment'
			},
			click: {
				trackingLinks: '.tooltip-tracking-links'
			}
		}
	};

	/**
  * Final Options
  *
  * @var {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Object
  *
  * @type {Object}
  */
	var module = {};

	/**
  * Tooltip Contents
  *
  * Contains the rendered HTML of the tooltips. The HTML is rendered with each table draw.
  *
  * e.g. tooltips.400210.orderItems >> HTML for order items tooltip of order #400210.
  *
  * @type {Object}
  */
	var tooltips = [];

	/**
  * DataTables XHR Parameters
  *
  * The same parameters used for fetching the table data need to be used for fetching the tooltips.
  *
  * @type {Object}
  */
	var datatablesXhrParameters = void 0;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Get Target Position
  *
  * @param {jQuery} $target
  *
  * @return {String}
  */
	function _getTargetPosition($target) {
		var horizontal = $target.offset().left - $(window).scrollLeft() > $(window).width() / 2 ? 'left' : 'right';
		var vertical = $target.offset().top - $(window).scrollTop() > $(window).height() / 2 ? 'top' : 'bottom';

		return horizontal + ' ' + vertical;
	}

	/**
  * Get Tooltip Position
  *
  * @param {jQuery} $target
  *
  * @return {String}
  */
	function _getTooltipPosition($target) {
		var horizontal = $target.offset().left - $(window).scrollLeft() > $(window).width() / 2 ? 'right' : 'left';
		var vertical = $target.offset().top - $(window).scrollTop() > $(window).height() / 2 ? 'bottom' : 'top';

		return horizontal + ' ' + vertical;
	}

	/**
  * If there is only one link then open it in a new tab. 
  */
	function _onTrackingLinksClick() {
		var trackingLinks = $(this).parents('tr').data('trackingLinks');

		if (trackingLinks.length === 1) {
			window.open(trackingLinks[0], '_blank');
		}
	}

	/**
  * Initialize tooltip for static table data.
  *
  * Replaces the browsers default tooltip with a qTip instance for every element on the table which has
  * a title attribute.
  */
	function _initTooltipsForStaticContent() {
		$this.find('tbody [title]').qtip({
			style: { classes: 'gx-qtip info' }
		});
	}

	/**
  * Show Tooltip
  *
  * Display the Qtip instance of the target. The tooltip contents are fetched after the table request
  * is finished for performance reasons. This method will not show anything until the tooltip contents
  * are fetched.
  *
  * @param {jQuery.Event} event
  */
	function _showTooltip(event) {
		event.stopPropagation();

		var orderId = $(this).parents('tr').data('id');

		if (!tooltips[orderId]) {
			return; // The requested tooltip is not loaded, do not continue.
		}

		var tooltipPosition = _getTooltipPosition($(this));
		var targetPosition = _getTargetPosition($(this));

		$(this).qtip({
			content: tooltips[orderId][event.data.name],
			style: {
				classes: 'gx-qtip info'
			},
			position: {
				my: tooltipPosition,
				at: targetPosition,
				effect: false,
				viewport: $(window),
				adjust: {
					method: 'none shift'
				}
			},
			hide: {
				fixed: true,
				delay: 300
			},
			show: {
				ready: true,
				delay: 100
			},
			events: {
				hidden: function hidden(event, api) {
					api.destroy(true);
				}
			}
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('draw.dt', _initTooltipsForStaticContent).on('preXhr.dt', function (event, settings, json) {
			return datatablesXhrParameters = json;
		}).on('xhr.dt', function () {
			return $.post(options.sourceUrl, datatablesXhrParameters, function (response) {
				return tooltips = response;
			}, 'json');
		}).on('click', '.tooltip-tracking-links', _onTrackingLinksClick);

		for (var event in options.selectors) {
			for (var name in options.selectors[event]) {
				$this.on(event, options.selectors[event][name], { name: name }, _showTooltip);
			}
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy90b29sdGlwcy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwic291cmNlVXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInNlbGVjdG9ycyIsIm1vdXNlZW50ZXIiLCJvcmRlckl0ZW1zIiwiY3VzdG9tZXJNZW1vcyIsImN1c3RvbWVyQWRkcmVzc2VzIiwib3JkZXJTdW1CbG9jayIsIm9yZGVyU3RhdHVzSGlzdG9yeSIsIm9yZGVyQ29tbWVudCIsImNsaWNrIiwidHJhY2tpbmdMaW5rcyIsIm9wdGlvbnMiLCJleHRlbmQiLCJ0b29sdGlwcyIsImRhdGF0YWJsZXNYaHJQYXJhbWV0ZXJzIiwiX2dldFRhcmdldFBvc2l0aW9uIiwiJHRhcmdldCIsImhvcml6b250YWwiLCJvZmZzZXQiLCJsZWZ0Iiwid2luZG93Iiwic2Nyb2xsTGVmdCIsIndpZHRoIiwidmVydGljYWwiLCJ0b3AiLCJzY3JvbGxUb3AiLCJoZWlnaHQiLCJfZ2V0VG9vbHRpcFBvc2l0aW9uIiwiX29uVHJhY2tpbmdMaW5rc0NsaWNrIiwicGFyZW50cyIsImxlbmd0aCIsIm9wZW4iLCJfaW5pdFRvb2x0aXBzRm9yU3RhdGljQ29udGVudCIsImZpbmQiLCJxdGlwIiwic3R5bGUiLCJjbGFzc2VzIiwiX3Nob3dUb29sdGlwIiwiZXZlbnQiLCJzdG9wUHJvcGFnYXRpb24iLCJvcmRlcklkIiwidG9vbHRpcFBvc2l0aW9uIiwidGFyZ2V0UG9zaXRpb24iLCJjb250ZW50IiwibmFtZSIsInBvc2l0aW9uIiwibXkiLCJhdCIsImVmZmVjdCIsInZpZXdwb3J0IiwiYWRqdXN0IiwibWV0aG9kIiwiaGlkZSIsImZpeGVkIiwiZGVsYXkiLCJzaG93IiwicmVhZHkiLCJldmVudHMiLCJoaWRkZW4iLCJhcGkiLCJkZXN0cm95IiwiaW5pdCIsImRvbmUiLCJvbiIsInNldHRpbmdzIiwianNv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QUFRMEIsUUFBUixFQUFrQkMsSUFBbEI7QUFBQSxVQUEyQm5ELDBCQUEwQm1ELElBQXJEO0FBQUEsR0FGbEIsRUFHRUYsRUFIRixDQUdLLFFBSEwsRUFHZTtBQUFBLFVBQU1yRSxFQUFFd0UsSUFBRixDQUFPdkQsUUFBUWYsU0FBZixFQUEwQmtCLHVCQUExQixFQUNuQjtBQUFBLFdBQVlELFdBQVdzRCxRQUF2QjtBQUFBLElBRG1CLEVBQ2MsTUFEZCxDQUFOO0FBQUEsR0FIZixFQUtFSixFQUxGLENBS0ssT0FMTCxFQUtjLHlCQUxkLEVBS3lDbkMscUJBTHpDOztBQU9BLE9BQUssSUFBSVUsS0FBVCxJQUFrQjNCLFFBQVFWLFNBQTFCLEVBQXFDO0FBQ3BDLFFBQUssSUFBSTJDLElBQVQsSUFBaUJqQyxRQUFRVixTQUFSLENBQWtCcUMsS0FBbEIsQ0FBakIsRUFBMkM7QUFDMUM3QyxVQUFNc0UsRUFBTixDQUFTekIsS0FBVCxFQUFnQjNCLFFBQVFWLFNBQVIsQ0FBa0JxQyxLQUFsQixFQUF5Qk0sSUFBekIsQ0FBaEIsRUFBZ0QsRUFBQ0EsVUFBRCxFQUFoRCxFQUF3RFAsWUFBeEQ7QUFDQTtBQUNEOztBQUVEeUI7QUFDQSxFQWZEOztBQWlCQSxRQUFPdkUsTUFBUDtBQUNBLENBcE5GIiwiZmlsZSI6Im9yZGVycy9vdmVydmlldy90b29sdGlwcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gdG9vbHRpcC5qcyAyMDE2LTA5LTIxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBPcmRlcnMgVGFibGUgVG9vbHRpcFxuICpcbiAqIFRoaXMgY29udHJvbGxlciBkaXNwbGF5cyB0b29sdGlwcyBmb3IgdGhlIG9yZGVycyBvdmVydmlldyB0YWJsZS4gVGhlIHRvb2x0aXBzIGFyZSBsb2FkZWQgYWZ0ZXIgdGhlXG4gKiB0YWJsZSBkYXRhIHJlcXVlc3QgaXMgcmVhZHkgZm9yIG9wdGltaXphdGlvbiBwdXJwb3Nlcy5cbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQndG9vbHRpcHMnLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdCAqXG5cdFx0ICogQHZhciB7alF1ZXJ5fVxuXHRcdCAqL1xuXHRcdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XG5cdFx0ICovXG5cdFx0Y29uc3QgZGVmYXVsdHMgPSB7XG5cdFx0XHRzb3VyY2VVcmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89T3JkZXJzT3ZlcnZpZXdBamF4L1Rvb2x0aXBzJyxcblx0XHRcdHNlbGVjdG9yczoge1xuXHRcdFx0XHRtb3VzZWVudGVyOiB7XG5cdFx0XHRcdFx0b3JkZXJJdGVtczogJy50b29sdGlwLW9yZGVyLWl0ZW1zJyxcblx0XHRcdFx0XHRjdXN0b21lck1lbW9zOiAnLnRvb2x0aXAtY3VzdG9tZXItbWVtb3MnLFxuXHRcdFx0XHRcdGN1c3RvbWVyQWRkcmVzc2VzOiAnLnRvb2x0aXAtY3VzdG9tZXItYWRkcmVzc2VzJyxcblx0XHRcdFx0XHRvcmRlclN1bUJsb2NrOiAnLnRvb2x0aXAtb3JkZXItc3VtLWJsb2NrJyxcblx0XHRcdFx0XHRvcmRlclN0YXR1c0hpc3Rvcnk6ICcudG9vbHRpcC1vcmRlci1zdGF0dXMtaGlzdG9yeScsXG5cdFx0XHRcdFx0b3JkZXJDb21tZW50OiAnLnRvb2x0aXAtb3JkZXItY29tbWVudCcsXG5cdFx0XHRcdH0sXG5cdFx0XHRcdGNsaWNrOiB7XG5cdFx0XHRcdFx0dHJhY2tpbmdMaW5rczogJy50b29sdGlwLXRyYWNraW5nLWxpbmtzJ1x0XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHQgKlxuXHRcdCAqIEB2YXIge09iamVjdH1cblx0XHQgKi9cblx0XHRjb25zdCBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7T2JqZWN0fVxuXHRcdCAqL1xuXHRcdGNvbnN0IG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFRvb2x0aXAgQ29udGVudHNcblx0XHQgKlxuXHRcdCAqIENvbnRhaW5zIHRoZSByZW5kZXJlZCBIVE1MIG9mIHRoZSB0b29sdGlwcy4gVGhlIEhUTUwgaXMgcmVuZGVyZWQgd2l0aCBlYWNoIHRhYmxlIGRyYXcuXG5cdFx0ICpcblx0XHQgKiBlLmcuIHRvb2x0aXBzLjQwMDIxMC5vcmRlckl0ZW1zID4+IEhUTUwgZm9yIG9yZGVyIGl0ZW1zIHRvb2x0aXAgb2Ygb3JkZXIgIzQwMDIxMC5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XG5cdFx0ICovXG5cdFx0bGV0IHRvb2x0aXBzID0gW107XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGF0YVRhYmxlcyBYSFIgUGFyYW1ldGVyc1xuXHRcdCAqXG5cdFx0ICogVGhlIHNhbWUgcGFyYW1ldGVycyB1c2VkIGZvciBmZXRjaGluZyB0aGUgdGFibGUgZGF0YSBuZWVkIHRvIGJlIHVzZWQgZm9yIGZldGNoaW5nIHRoZSB0b29sdGlwcy5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XG5cdFx0ICovXG5cdFx0bGV0IGRhdGF0YWJsZXNYaHJQYXJhbWV0ZXJzO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEZVTkNUSU9OU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEdldCBUYXJnZXQgUG9zaXRpb25cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5fSAkdGFyZ2V0XG5cdFx0ICpcblx0XHQgKiBAcmV0dXJuIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0ZnVuY3Rpb24gX2dldFRhcmdldFBvc2l0aW9uKCR0YXJnZXQpIHtcblx0XHRcdGNvbnN0IGhvcml6b250YWwgPSAkdGFyZ2V0Lm9mZnNldCgpLmxlZnQgLSAkKHdpbmRvdykuc2Nyb2xsTGVmdCgpID4gJCh3aW5kb3cpLndpZHRoKCkgLyAyXG5cdFx0XHRcdFx0PyAnbGVmdCdcblx0XHRcdFx0XHQ6ICdyaWdodCc7XG5cdFx0XHRjb25zdCB2ZXJ0aWNhbCA9ICR0YXJnZXQub2Zmc2V0KCkudG9wIC0gJCh3aW5kb3cpLnNjcm9sbFRvcCgpID4gJCh3aW5kb3cpLmhlaWdodCgpIC8gMlxuXHRcdFx0XHRcdD8gJ3RvcCdcblx0XHRcdFx0XHQ6ICdib3R0b20nO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gaG9yaXpvbnRhbCArICcgJyArIHZlcnRpY2FsO1xuXHRcdH1cblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgVG9vbHRpcCBQb3NpdGlvblxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXRcblx0XHQgKlxuXHRcdCAqIEByZXR1cm4ge1N0cmluZ31cblx0XHQgKi9cblx0XHRmdW5jdGlvbiBfZ2V0VG9vbHRpcFBvc2l0aW9uKCR0YXJnZXQpIHtcblx0XHRcdGNvbnN0IGhvcml6b250YWwgPSAkdGFyZ2V0Lm9mZnNldCgpLmxlZnQgLSAkKHdpbmRvdykuc2Nyb2xsTGVmdCgpID4gJCh3aW5kb3cpLndpZHRoKCkgLyAyXG5cdFx0XHRcdFx0PyAncmlnaHQnXG5cdFx0XHRcdFx0OiAnbGVmdCc7XG5cdFx0XHRjb25zdCB2ZXJ0aWNhbCA9ICR0YXJnZXQub2Zmc2V0KCkudG9wIC0gJCh3aW5kb3cpLnNjcm9sbFRvcCgpID4gJCh3aW5kb3cpLmhlaWdodCgpIC8gMlxuXHRcdFx0XHRcdD8gJ2JvdHRvbSdcblx0XHRcdFx0XHQ6ICd0b3AnO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gaG9yaXpvbnRhbCArICcgJyArIHZlcnRpY2FsO1xuXHRcdH1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJZiB0aGVyZSBpcyBvbmx5IG9uZSBsaW5rIHRoZW4gb3BlbiBpdCBpbiBhIG5ldyB0YWIuIFxuXHRcdCAqL1xuXHRcdGZ1bmN0aW9uIF9vblRyYWNraW5nTGlua3NDbGljaygpIHtcblx0XHRcdGNvbnN0IHRyYWNraW5nTGlua3MgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgndHJhY2tpbmdMaW5rcycpOyBcblx0XHRcdFxuXHRcdFx0aWYgKHRyYWNraW5nTGlua3MubGVuZ3RoID09PSAxKSB7XG5cdFx0XHRcdHdpbmRvdy5vcGVuKHRyYWNraW5nTGlua3NbMF0sICdfYmxhbmsnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSB0b29sdGlwIGZvciBzdGF0aWMgdGFibGUgZGF0YS5cblx0XHQgKlxuXHRcdCAqIFJlcGxhY2VzIHRoZSBicm93c2VycyBkZWZhdWx0IHRvb2x0aXAgd2l0aCBhIHFUaXAgaW5zdGFuY2UgZm9yIGV2ZXJ5IGVsZW1lbnQgb24gdGhlIHRhYmxlIHdoaWNoIGhhc1xuXHRcdCAqIGEgdGl0bGUgYXR0cmlidXRlLlxuXHRcdCAqL1xuXHRcdGZ1bmN0aW9uIF9pbml0VG9vbHRpcHNGb3JTdGF0aWNDb250ZW50KCkge1xuXHRcdFx0JHRoaXMuZmluZCgndGJvZHkgW3RpdGxlXScpLnF0aXAoe1xuXHRcdFx0XHRzdHlsZToge2NsYXNzZXM6ICdneC1xdGlwIGluZm8nfVxuXHRcdFx0fSk7XG5cdFx0fVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNob3cgVG9vbHRpcFxuXHRcdCAqXG5cdFx0ICogRGlzcGxheSB0aGUgUXRpcCBpbnN0YW5jZSBvZiB0aGUgdGFyZ2V0LiBUaGUgdG9vbHRpcCBjb250ZW50cyBhcmUgZmV0Y2hlZCBhZnRlciB0aGUgdGFibGUgcmVxdWVzdFxuXHRcdCAqIGlzIGZpbmlzaGVkIGZvciBwZXJmb3JtYW5jZSByZWFzb25zLiBUaGlzIG1ldGhvZCB3aWxsIG5vdCBzaG93IGFueXRoaW5nIHVudGlsIHRoZSB0b29sdGlwIGNvbnRlbnRzXG5cdFx0ICogYXJlIGZldGNoZWQuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcblx0XHQgKi9cblx0XHRmdW5jdGlvbiBfc2hvd1Rvb2x0aXAoZXZlbnQpIHtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XG5cdFx0XHRjb25zdCBvcmRlcklkID0gJCh0aGlzKS5wYXJlbnRzKCd0cicpLmRhdGEoJ2lkJyk7XG5cdFx0XHRcblx0XHRcdGlmICghdG9vbHRpcHNbb3JkZXJJZF0pIHtcblx0XHRcdFx0cmV0dXJuOyAvLyBUaGUgcmVxdWVzdGVkIHRvb2x0aXAgaXMgbm90IGxvYWRlZCwgZG8gbm90IGNvbnRpbnVlLlxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRjb25zdCB0b29sdGlwUG9zaXRpb24gPSBfZ2V0VG9vbHRpcFBvc2l0aW9uKCQodGhpcykpO1xuXHRcdFx0Y29uc3QgdGFyZ2V0UG9zaXRpb24gPSBfZ2V0VGFyZ2V0UG9zaXRpb24oJCh0aGlzKSk7XG5cdFx0XHRcblx0XHRcdCQodGhpcykucXRpcCh7XG5cdFx0XHRcdGNvbnRlbnQ6IHRvb2x0aXBzW29yZGVySWRdW2V2ZW50LmRhdGEubmFtZV0sXG5cdFx0XHRcdHN0eWxlOiB7XG5cdFx0XHRcdFx0Y2xhc3NlczogJ2d4LXF0aXAgaW5mbydcblx0XHRcdFx0fSxcblx0XHRcdFx0cG9zaXRpb246IHtcblx0XHRcdFx0XHRteTogdG9vbHRpcFBvc2l0aW9uLFxuXHRcdFx0XHRcdGF0OiB0YXJnZXRQb3NpdGlvbixcblx0XHRcdFx0XHRlZmZlY3Q6IGZhbHNlLFxuXHRcdFx0XHRcdHZpZXdwb3J0OiAkKHdpbmRvdyksXG5cdFx0XHRcdFx0YWRqdXN0OiB7XG5cdFx0XHRcdFx0XHRtZXRob2Q6ICdub25lIHNoaWZ0J1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSxcblx0XHRcdFx0aGlkZToge1xuXHRcdFx0XHRcdGZpeGVkOiB0cnVlLFxuXHRcdFx0XHRcdGRlbGF5OiAzMDBcblx0XHRcdFx0fSxcblx0XHRcdFx0c2hvdzoge1xuXHRcdFx0XHRcdHJlYWR5OiB0cnVlLFxuXHRcdFx0XHRcdGRlbGF5OiAxMDBcblx0XHRcdFx0fSxcblx0XHRcdFx0ZXZlbnRzOiB7XG5cdFx0XHRcdFx0aGlkZGVuOiAoZXZlbnQsIGFwaSkgPT4ge1xuXHRcdFx0XHRcdFx0YXBpLmRlc3Ryb3kodHJ1ZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignZHJhdy5kdCcsIF9pbml0VG9vbHRpcHNGb3JTdGF0aWNDb250ZW50KVxuXHRcdFx0XHQub24oJ3ByZVhoci5kdCcsIChldmVudCwgc2V0dGluZ3MsIGpzb24pID0+IGRhdGF0YWJsZXNYaHJQYXJhbWV0ZXJzID0ganNvbilcblx0XHRcdFx0Lm9uKCd4aHIuZHQnLCAoKSA9PiAkLnBvc3Qob3B0aW9ucy5zb3VyY2VVcmwsIGRhdGF0YWJsZXNYaHJQYXJhbWV0ZXJzLFxuXHRcdFx0XHRcdHJlc3BvbnNlID0+IHRvb2x0aXBzID0gcmVzcG9uc2UsICdqc29uJykpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLnRvb2x0aXAtdHJhY2tpbmctbGlua3MnLCBfb25UcmFja2luZ0xpbmtzQ2xpY2spOyBcblx0XHRcdFxuXHRcdFx0Zm9yIChsZXQgZXZlbnQgaW4gb3B0aW9ucy5zZWxlY3RvcnMpIHtcblx0XHRcdFx0Zm9yIChsZXQgbmFtZSBpbiBvcHRpb25zLnNlbGVjdG9yc1tldmVudF0pIHtcblx0XHRcdFx0XHQkdGhpcy5vbihldmVudCwgb3B0aW9ucy5zZWxlY3RvcnNbZXZlbnRdW25hbWVdLCB7bmFtZX0sIF9zaG93VG9vbHRpcCk7XG5cdFx0XHRcdH1cdFxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9XG4pOyJdfQ==
