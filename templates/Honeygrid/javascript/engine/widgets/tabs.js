/* --------------------------------------------------------------
 tabs.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the tabs / accordion
 */
gambio.widgets.module('tabs', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		$tabs = null,
		$content = null,
		$tabList = null,
		$contentList = null,
		transition = {
			classOpen: 'active',
			open: false,
			calcHeight: true
		},
		defaults = {},
		options = $.extend(true, {}, defaults, data),
		module = {};


// ########## HELPER FUNCTIONS ##########

	/**
	 * Function that sets the active classes to the
	 * tabs and the content headers and shows / hides
	 * the content
	 * @param       {integer}       index       The index of the clicked element
	 * @private
	 */
	var _setClasses = function(index) {
		// Set the active tab
		$tabList
			.removeClass('active')
			.eq(index)
			.addClass('active');

		transition.open = false;
		var $hide = $contentList
			.filter('.active')
			.removeClass('active')
			.children('.tab-body'),
			$show = $contentList.eq(index);

		$show
			.addClass('active')
			.find('.tab-body')
			.addClass('active');
	};


// ########## EVENT HANDLER ##########

	/**
	 * Click handler for the tabs. It hides
	 * all other tab content except it's own
	 * @param       {object}    e       jQuery event object
	 * @private
	 */
	var _clickHandlerTabs = function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
			index = $self.index();

		if (!$self.hasClass('active')) {
			_setClasses(index);
		}
	};

	/**
	 * Click handler for the accordion. It hides
	 * all other tab content except it's own
	 * @param       {object}    e       jQuery event object
	 * @private
	 */
	var _clickHandlerAccordion = function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
			$container = $self.closest('.tab-pane'),
			index = $container.index();

		if (!$container.hasClass('active')) {
			_setClasses(index);
		}
	};


// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		$tabs = $this.children('.nav-tabs');
		$tabList = $tabs.children('li');
		$content = $this.children('.tab-content');
		$contentList = $content.children('.tab-pane');

		$this
			.on('click', '.nav-tabs li', _clickHandlerTabs)
			.on('click', '.tab-content .tab-heading', _clickHandlerAccordion);

		done();
	};

	// Return data to widget engine
	return module;
});