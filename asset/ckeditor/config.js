/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example: 
	config.uiColor = '#AADC6E';
    config.toolbarLocation='top';
    config.toolbarCanCollapse=true;
    config.toolbarStartupExpanded = true;
    config.tabSpaces = 4;
    config.startupShowBorders = true;
    config.startupOutlineBlocks = false;
     
    config.smiley_path = CKEDITOR.basePath + 'plugins/smiley/images/';
    config.smiley_images = [
        'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tongue_smile.gif',
        'embarrassed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif',
        'devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif',
        'broken_heart.gif','kiss.gif','envelope.gif'
    ];
    config.smiley_descriptions = [
        'smiley', 'sad', 'wink', 'laugh', 'frown', 'cheeky', 'blush', 'surprise',
        'indecision', 'angry', 'angel', 'cool', 'devil', 'crying', 'enlightened', 'no',
        'yes', 'heart', 'broken heart', 'kiss', 'mail'
    ];
    
    config.skin = 'moono'; 
    config.shiftEnterMode = CKEDITOR.ENTER_BR; 
    config.resize_enabled = true; 
    config.readOnly = false;
    config.height = '500px';
    
    config.font_style = {
        element:        'span',
        styles:         { 'font-family': '#(family)' },
        overrides:      [ { element: 'font', attributes: { 'face': null } } ]
    };
    config.autoGrow_onStartup = true;
    config.autoGrow_minHeight = 600;
    
    
    config.toolbarGroups = [
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'forms' },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'links' },
        { name: 'insert' },
        '/',
        { name: 'styles' },
        { name: 'colors' },
        { name: 'tools' },
        { name: 'others' },
        { name: 'about' }
    ];
};
