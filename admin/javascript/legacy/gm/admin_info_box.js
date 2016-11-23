/* --------------------------------------------------------------
 admin_info_box.js 2016-07-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Initialize Admin Info Box (Compatibility)
 *
 * This legacy module will initialize the functionality of the compatibility info box.
 *
 * Note: The old admin_info_box.js.php was rewritten because there was no need to be parsed with
 * PHP (performance penalty).
 */
$(function() {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES 
	// ------------------------------------------------------------------------
	
	var $wrapper;
	var closeTimeout;
	var autoClose = false;
	
	// ------------------------------------------------------------------------
	// INITIALIZE INFO BOX 
	// ------------------------------------------------------------------------
	
	function _initialize() {
		$wrapper.hide();
		
		$(document).click(function() {
			var $element = $(this);
			
			if ($wrapper.is(':visible')) {
				if ($element !== $wrapper && $element.parents('#admin_info_wrapper').length === 0
					&& !$element.hasClass('admin_info_box_button')) {
					$wrapper.removeClass('active');
				}
			}
		});
		
		$('.admin_info_box').each(function() {
			if ($(this).not('.hidden').length > 0) {
				$('.admin_info_box_button').addClass('active');
			}
			
			if (!$(this).hasClass('read') && !$(this).hasClass('hidden')) {
				autoClose = true;
				_open();
				return false;
			}
		});
		
		// Set info box item count.
		$('.notification-count').text($('.admin_info_box').length);
		
		if ($('.admin_info_box').length > 0) {
			$('.notification-count').removeClass('hidden');
		}
		
		$(window).on('scroll', _updatePosition);
		
		$('.admin_info_box_button').on('click', function() {
			clearTimeout(closeTimeout);
			
			autoClose = false;
			
			if ($wrapper.hasClass('active')) {
				_close();
			} else {
				_open();
			}
			
			return false;
		});
		
		$wrapper
			.on('mousemove mouseenter', function() {
				clearTimeout(closeTimeout);
			})
			.on('mouseleave', function() {
				if (autoClose === true) {
					closeTimeout = setTimeout(function() {
						_close();
					}, 1000);
				}
			})
			.on('click', 'a', function() {
				if ($(this).hasClass('ajax')) {
					autoClose = false;
					
					var $adminInfoBox = $(this).closest('.admin_info_box');
					var rel = $(this).attr('rel');
					
					if (rel !== 'hide_info_box') {
						$adminInfoBox.addClass('progress');
					}
					
					$.get($(this).attr('href'))
						.done(function(response) {
							switch (rel) {
								case 'clear_cache':
									$adminInfoBox.removeClass('info warning progress');
									
									if (response.length < 500) {
										$adminInfoBox.find('.info_text').html(response);
										$adminInfoBox.find('a[rel="clear_cache"]').hide();
										$adminInfoBox.addClass('success');
										
										setTimeout(function() {
											if ($('.admin_info_box').not('.hidden').length === 0) {
												$('.admin_info_box_button').removeClass('active');
											}
											
											_close();
											
											setTimeout(function() {
												$adminInfoBox
													.addClass('hidden')
													.remove();
											}, 500);
										}, 2000);
										
										$('.notification-count').text(parseInt($('.notification-count').text()) - 1);
										
										if ($('.notification-count').text() === '0') {
											$('.notification-count').addClass('hidden');
										}
									} else {
										$adminInfoBox.find('.info_text')
											.html(jse.core.lang.translate('ERROR_SESSION_EXPIRED', 'admin_info_boxes'));
										$adminInfoBox.addClass('error');
									}
									
									break;
								
								case 'hide_info_box':
									$('.show_all_info_boxes').prop('checked', false);
									
									$adminInfoBox.slideUp(500, function() {
										$(this).addClass('hidden');
										_checkVisible();
										
										if ($('.admin_info_box').not('.hidden').length === 0) {
											$('.admin_info_box_button').removeClass('active');
										}
									});
									
									if ($('.admin_info_box:visible').length === 1) {
										_close();
									}
									
									break;
								
								case 'remove_info_box':
									$adminInfoBox.slideUp(500, function() {
										$(this).addClass('hidden');
										
										if ($('.admin_info_box').not('.hidden').length === 0) {
											$('.admin_info_box_button').removeClass('active');
										}
										
										$adminInfoBox.remove();
									});
									
									if ($('.admin_info_box:visible').length === 1) {
										_close();
									}
									
									break;
							}
						})
						.fail(function(response) {
							$adminInfoBox
								.removeClass('info warning progress')
								.addClass('error');
							$adminInfoBox.find('.info_text').html(response);
						});
					
					return false;
					
				} else if ($(this).hasClass('target_blank')) {
					var myWindow;
					myWindow = window.open($(this).attr('href'));
					myWindow.focus();
					return false;
				}
				
				return true;
			});
		
		$('.show_all_info_boxes').on('click', function() {
			if ($('.show_all_info_boxes').prop('checked')) {
				$('.no_messages').hide();
				$('.admin_info_box.hidden').show();
			} else {
				if ($('.admin_info_box').not('.hidden').length === 0) {
					$('.no_messages').show();
				}
				
				$('.admin_info_box.hidden').hide();
			}
		});
	}
	
	function _open() {
		_updatePosition();
		_checkVisible();
		
		$wrapper
			.show()
			.addClass('active');
		
		$('.admin_info_box:visible').each(function() {
			if (!$(this).hasClass('read') && !$(this).hasClass('hidden')) {
				$.ajax({
					url: 'request_port.php',
					data: {
						module: 'AdminInfobox',
						action: 'set_status_read',
						id: $(this).find('.admin_info_box_id').text(),
						XTCsid: session_id
					}
				});
			}
		});
		
		if (autoClose === true) {
			closeTimeout = setTimeout(function() {
				_close();
			}, 5000);
		}
		
	}
	
	function _close() {
		$wrapper.removeClass('active');
	}
	
	function _updatePosition() {
		$wrapper.css('top', $wrapper.height() * -1);
	}
	
	function _checkVisible() {
		var showAll = false;
		
		if ($('.admin_info_box.hidden').length > 0) {
			$('.show_all').css('display', 'block');
			showAll = true;
		} else {
			$('.show_all').css('display', 'none');
		}
		
		if ($('.admin_info_box').not('.hidden').length === 0) {
			$wrapper.find('.no_messages').show();
		}
	}
	
	
	// ------------------------------------------------------------------------
	// LOAD INFO BOX HTML
	// ------------------------------------------------------------------------
	
	$.ajax({
		url: 'request_port.php',
		data: {
			module: 'LoadAdminInfoBoxes',
			XTCsid: session_id
		}
	}).done(function(response) {
		$('body').append(response);
		
		$wrapper = $('#admin_info_wrapper');
		
		if ($wrapper.length === 1) {
			_initialize();
		}
	});
}); 