/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
        { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'insert' },
        { name: 'others' },
        { name: 'links' },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'forms' },
        { name: 'about' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'colors' },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'styles' },
        { name: 'tools' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'div;p;h1;h2;h3;pre';

    config.allowedContent = true;

	// Make dialogs simpler.
	// config.removeDialogTabs = 'image:advanced;link:advanced';

    config.toolbar_Basic = [
        ['PasteFromWord','Link', 'Unlink', '-', 'Image','Flash','SpecialChar','HorizontalRule'],
        '/',
        ['Font','FontSize'],
        '/',
        ['TextColor','BGColor'],['Source'],
        '/',
        ['Bold','Italic','Underline','Strike', '-', 'SelectAll','RemoveFormat'],
        '/',
        ['NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Maximize']
    ];

    config.toolbar_ImageMapper = [
        ['Link', 'Unlink'],
        ['Image','Table','SpecialChar'],
        ['Font','TextColor','BGColor'],
        ['FontSize','Source','PasteFromWord'],
        ['Bold','Italic','Underline','Strike','-','SelectAll','RemoveFormat'],
        ['NumberedList','BulletedList','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Maximize']
    ];

    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_P;
	config.smiley_admin_path = '../images/icons/smileys/';
	config.smiley_path = '../images/icons/smileys/';
	config.protectedSource.push(/\{[\s\S]*?\}/g);
	config.extraPlugins = 'showprotected';
};

CKEDITOR.dtd.$removeEmpty['i'] = false;