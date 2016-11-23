/* --------------------------------------------------------------
 events.js 2016-02-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.events = jse.libs.template.events || {};

/**
 * ## Honeygrid Event Constants Library
 * 
 * Contains all triggered custom event names. Use the methods to get the event names.
 *
 * @module Honeygrid/Libs/events
 * @exports jse.libs.template.events
 */
(function(exports) {

	'use strict';
	
	/**
	 * OPEN_FLYOUT Constant
	 * 
	 * @return {string}
	 */
	exports.OPEN_FLYOUT = function() {
		return 'OPEN_FLYOUT';
	};
	
	/**
	 * TRANSITION Constant
	 *
	 * @return {string}
	 */
	exports.TRANSITION = function() {
		return 'TRANSITION';
	};
	
	/**
	 * TRANSITION_FINISHED Constant
	 *
	 * @return {string}
	 */
	exports.TRANSITION_FINISHED = function() {
		return 'TRANSITION_FINISHED';
	};
	
	/**
	 * TRANSITION_STOP Constant
	 *
	 * @return {string}
	 */
	exports.TRANSITION_STOP = function() {
		return 'TRANSITION_STOP';
	};
	
	/**
	 * BREAKPOINT Constant
	 *
	 * @return {string}
	 */
	exports.BREAKPOINT = function() {
		return 'BREAKPOINT';
	};
	
	/**
	 * CART_OPEN Constant
	 *
	 * @return {string}
	 */
	exports.CART_OPEN = function() {
		return 'CART_DROPDOWN_OPEN';
	};
	
	/**
	 * CART_CLOSE Constant
	 *
	 * @return {string}
	 */
	exports.CART_CLOSE = function() {
		return 'CART_DROPDOWN_CLOSE';
	};
	
	/**
	 * CART_UPDATE Constant
	 *
	 * @return {string}
	 */
	exports.CART_UPDATE = function() {
		return 'CART_DROPDOWN_UPDATE';
	};
	
	/**
	 * SWIPER_GOTO Constant
	 *
	 * @return {string}
	 */
	exports.SWIPER_GOTO = function() {
		return 'SWIPER_GOTO';
	};
	
	/**
	 * SLIDES_UPDATE Constant
	 *
	 * @return {string}
	 */
	exports.SLIDES_UPDATE = function() {
		return 'SLIDES_UPDATE';
	};
	
	/**
	 * CHECK_CART Constant
	 *
	 * @return {string}
	 */
	exports.CHECK_CART = function() {
		return 'CHECK_CART';
	};
	
	/**
	 * CART_UPDATED Constant
	 *
	 * @return {string}
	 */
	exports.CART_UPDATED = function() {
		return 'CART_UPDATED';
	};
	
	/**
	 * REPOSITIONS_STICKYBOX Constant
	 *
	 * @return {string}
	 */
	exports.REPOSITIONS_STICKYBOX = function() {
		return 'REPOSITIONS_STICKYBOX';
	};
	
	/**
	 * ADD_CUSTOMIZER_WISHLIST Constant
	 *
	 * @return {string}
	 */
	exports.ADD_CUSTOMIZER_WISHLIST = function() {
		return 'ADD_CUSTOMIZER_WISHLIST';
	};
	
	/**
	 * ADD_CUSTOMIZER_CART Constant
	 *
	 * @return {string}
	 */
	exports.ADD_CUSTOMIZER_CART = function() {
		return 'ADD_CUSTOMIZER_CART';
	};
	
	/**
	 * WISHLIST_TO_CART Constant
	 *
	 * @return {string}
	 */
	exports.WISHLIST_TO_CART = function() {
		return 'WISHLIST_TO_CART';
	};
	
	/**
	 * WISHLIST_CART_DELETE Constant
	 *
	 * @return {string}
	 */
	exports.WISHLIST_CART_DELETE = function() {
		return 'WISHLIST_CART_DELETE';
	};
	
	/**
	 * MENU_REPOSITIONED Constant
	 *
	 * @return {string}
	 */
	exports.MENU_REPOSITIONED = function() {
		return 'MENU_REPOSITIONED';
	};

	/**
	 * STICKYBOX_CONTENT_CHANGE Constant
	 * 
	 * @returns {string}
     */
	exports.STICKYBOX_CONTENT_CHANGE = function() {
		return 'STICKYBOX_CONTENT_CHANGE';
	};

	/**
	 * SHARE_CART_MODAL_READY Constant
	 *
	 * @returns {string}
	 */
	exports.SHARE_CART_MODAL_READY = function() {
		return 'SHARE_CART_MODAL_READY';
	};

}(jse.libs.template.events));